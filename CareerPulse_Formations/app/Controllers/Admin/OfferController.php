<?php
declare(strict_types=1);

namespace CareerPulse\Controllers\Admin;

use CareerPulse\Controllers\OfferQueries;

final class OfferController extends AdminController
{
    use OfferQueries;

    private const WORK_MODES = ['Sur site', 'Hybride', 'Télétravail'];
    private const STATUSES = ['Brouillon', 'Publiée', 'Clôturée'];

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->postIsValid()) {
            $action = (string) ($_POST['action'] ?? '');
            $id = (int) ($_POST['offer_id'] ?? 0);

            if ($action === 'delete') {
                if ($id > 0) {
                    $stmt = $this->db->prepare('DELETE FROM offers WHERE id = :id');
                    $stmt->execute(['id' => $id]);
                    flash($stmt->rowCount() > 0 ? 'success' : 'error', $stmt->rowCount() > 0 ? 'L’offre a été supprimée.' : 'Offre introuvable.');
                } else {
                    flash('error', 'Offre introuvable.');
                }
                redirect_to('admin/offers');
            }

            $payload = [
                'type_id' => (int) ($_POST['type_id'] ?? 0),
                'title' => trim((string) ($_POST['title'] ?? '')),
                'company' => trim((string) ($_POST['company'] ?? '')),
                'location' => trim((string) ($_POST['location'] ?? '')),
                'work_mode' => (string) ($_POST['work_mode'] ?? ''),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'requirements' => trim((string) ($_POST['requirements'] ?? '')),
                'deadline' => trim((string) ($_POST['deadline'] ?? '')),
                'status' => (string) ($_POST['status'] ?? 'Brouillon'),
            ];

            $errors = $this->validateOffer($payload);
            if (!in_array($action, ['create', 'update'], true)) {
                $errors[] = 'Action invalide.';
            }
            if ($action === 'update' && ($id <= 0 || $this->findOffer($id) === null)) {
                $errors[] = 'L’offre à modifier est introuvable.';
            }

            if (in_array($action, ['create', 'update'], true) && $this->offerAlreadyExists($payload['title'], $payload['company'], $action === 'update' ? $id : 0)) {
                $errors[] = 'Cette entreprise possède déjà une offre portant ce titre.';
            }

            if ($errors !== []) {
                flash('error', implode(' ', $errors));
                remember_input($payload);
                redirect_to('admin/offers', $action === 'update' && $id > 0 ? ['edit' => $id] : []);
            }

            if ($action === 'update') {
                $stmt = $this->db->prepare(
                    'UPDATE offers SET type_id = :type_id, title = :title, company = :company,
                     location = :location, work_mode = :work_mode, description = :description,
                     requirements = :requirements, deadline = :deadline, status = :status WHERE id = :id'
                );
                $stmt->execute($payload + ['id' => $id]);
                flash('success', 'L’offre a été mise à jour.');
            } else {
                $stmt = $this->db->prepare(
                    'INSERT INTO offers (type_id, title, company, location, work_mode, description, requirements, deadline, status)
                     VALUES (:type_id, :title, :company, :location, :work_mode, :description, :requirements, :deadline, :status)'
                );
                $stmt->execute($payload);
                flash('success', 'L’offre a été ajoutée.');
            }
            redirect_to('admin/offers');
        }

        $editId = (int) ($_GET['edit'] ?? 0);
        $this->render('admin', 'offers', 'Gestion des offres', 'admin/offers', [
            'offers' => $this->fetchOffers(),
            'offerTypes' => $this->fetchOfferTypes(),
            'editingOffer' => $editId > 0 ? $this->findOffer($editId) : null,
        ]);
    }

    /** Validation côté serveur des valeurs saisies, avant toute écriture PDO. */
    private function validateOffer(array $offer): array
    {
        $errors = [];
        if ($offer['title'] === '') {
            $errors[] = 'Le titre de l’offre est obligatoire.';
        } elseif (mb_strlen($offer['title']) > 160) {
            $errors[] = 'Le titre est limité à 160 caractères.';
        }
        if ($offer['company'] === '') {
            $errors[] = 'Le nom de l’entreprise est obligatoire.';
        } elseif (mb_strlen($offer['company']) > 120) {
            $errors[] = 'Le nom de l’entreprise est limité à 120 caractères.';
        }
        if ($offer['location'] === '') {
            $errors[] = 'Le lieu est obligatoire.';
        } elseif (mb_strlen($offer['location']) > 120) {
            $errors[] = 'Le lieu est limité à 120 caractères.';
        }
        if (!in_array($offer['work_mode'], self::WORK_MODES, true)) {
            $errors[] = 'Le mode de travail choisi est invalide.';
        }
        if ($offer['description'] === '') {
            $errors[] = 'La description est obligatoire.';
        } elseif (mb_strlen($offer['description']) > 3000) {
            $errors[] = 'La description est limitée à 3 000 caractères.';
        }
        if (mb_strlen($offer['requirements']) > 2000) {
            $errors[] = 'Les compétences requises sont limitées à 2 000 caractères.';
        }
        if (!in_array($offer['status'], self::STATUSES, true)) {
            $errors[] = 'Le statut choisi est invalide.';
        }
        if ($offer['deadline'] !== '') {
            $date = $offer['deadline'];
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
                || !checkdate((int) substr($date, 5, 2), (int) substr($date, 8, 2), (int) substr($date, 0, 4))) {
                $errors[] = 'La date limite n’est pas valide.';
            } elseif ($offer['status'] === 'Publiée' && $date < date('Y-m-d')) {
                $errors[] = 'Une offre publiée ne peut pas avoir une date limite déjà passée.';
            }
        }
        $type = $offer['type_id'] > 0 ? $this->findOfferType($offer['type_id'], true) : null;
        if ($type === null) {
            $errors[] = 'Choisissez un type d’offre actif.';
        }

        return $errors;
    }
    private function offerAlreadyExists(string $title, string $company, int $exceptId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM offers WHERE title = :title AND company = :company AND id <> :except_id LIMIT 1');
        $stmt->execute(['title' => $title, 'company' => $company, 'except_id' => $exceptId]);

        return $stmt->fetchColumn() !== false;
    }
}
