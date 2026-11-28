<?php
declare(strict_types=1);

namespace CareerPulse\Controllers\Admin;

use CareerPulse\Controllers\OfferQueries;
use PDOException;

/** CRUD administrateur des types d'offre. */
final class OfferTypeController extends AdminController
{
    use OfferQueries;

    private const STATUSES = ['active', 'inactive'];

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->postIsValid()) {
            $action = (string) ($_POST['action'] ?? '');
            $id = (int) ($_POST['type_id'] ?? 0);

            if ($action === 'delete') {
                if ($id <= 0 || $this->findOfferType($id) === null) {
                    flash('error', 'Type d’offre introuvable.');
                } elseif ($this->countOffersForType($id) > 0) {
                    flash('error', 'Ce type ne peut pas être supprimé tant que des offres lui sont rattachées. Modifiez ou supprimez d’abord ces offres.');
                } else {
                    $stmt = $this->db->prepare('DELETE FROM offer_types WHERE id = :id');
                    $stmt->execute(['id' => $id]);
                    flash('success', 'Le type d’offre a été supprimé.');
                }
                redirect_to('admin/offer-types');
            }

            $name = trim((string) ($_POST['name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $status = (string) ($_POST['status'] ?? 'active');
            $errors = [];

            if ($name === '') {
                $errors[] = 'Le nom du type est obligatoire.';
            } elseif (mb_strlen($name) > 80) {
                $errors[] = 'Le nom du type est limité à 80 caractères.';
            }
            if (mb_strlen($description) > 255) {
                $errors[] = 'La description est limitée à 255 caractères.';
            }
            if (!in_array($status, self::STATUSES, true)) {
                $errors[] = 'Le statut choisi est invalide.';
            }
            if (!in_array($action, ['create', 'update'], true)) {
                $errors[] = 'Action invalide.';
            }
            if ($action === 'update' && ($id <= 0 || $this->findOfferType($id) === null)) {
                $errors[] = 'Le type à modifier est introuvable.';
            }
            if ($action === 'update' && $status === 'inactive' && $this->countPublishedOffersForType($id) > 0) {
                $errors[] = 'Ce type possède encore des offres publiées. Clôturez-les avant de désactiver le type.';
            }

            if ($errors !== []) {
                flash('error', implode(' ', $errors));
                remember_input(['name' => $name, 'description' => $description, 'status' => $status]);
                redirect_to('admin/offer-types', $action === 'update' && $id > 0 ? ['edit' => $id] : []);
            }

            try {
                if ($action === 'update') {
                    $stmt = $this->db->prepare('UPDATE offer_types SET name = :name, description = :description, status = :status WHERE id = :id');
                    $stmt->execute(['name' => $name, 'description' => $description, 'status' => $status, 'id' => $id]);
                    flash('success', 'Le type d’offre a été mis à jour.');
                } else {
                    $stmt = $this->db->prepare('INSERT INTO offer_types (name, description, status) VALUES (:name, :description, :status)');
                    $stmt->execute(['name' => $name, 'description' => $description, 'status' => $status]);
                    flash('success', 'Le type d’offre a été créé.');
                }
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                flash('error', 'Un type d’offre porte déjà ce nom.');
                remember_input(['name' => $name, 'description' => $description, 'status' => $status]);
                redirect_to('admin/offer-types', $action === 'update' && $id > 0 ? ['edit' => $id] : []);
            }

            redirect_to('admin/offer-types');
        }

        $editId = (int) ($_GET['edit'] ?? 0);
        $this->render('admin', 'offer-types', 'Gestion des types d’offres', 'admin/offer-types', [
            'offerTypes' => $this->fetchOfferTypes(),
            'editingType' => $editId > 0 ? $this->findOfferType($editId) : null,
        ]);
    }
}
