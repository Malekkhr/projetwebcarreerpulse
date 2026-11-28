<?php
declare(strict_types=1);

namespace CareerPulse\Controllers;

use CareerPulse\Models\OfferModel;
use CareerPulse\Models\OfferTypeModel;

/** Requêtes PDO préparées et jointures partagées par les vues front et back. */
trait OfferQueries
{
    /** @return OfferTypeModel[] */
    private function fetchOfferTypes(bool $activeOnly = false): array
    {
        $sql = 'SELECT ot.id, ot.name, ot.description, ot.status, ot.created_at,
                       COUNT(o.id) AS offer_count
                FROM offer_types ot
                LEFT JOIN offers o ON o.type_id = ot.id'
            . ($activeOnly ? " WHERE ot.status = 'active'" : '')
            . ' GROUP BY ot.id, ot.name, ot.description, ot.status, ot.created_at
                ORDER BY ot.name ASC';

        return array_map(static fn (array $row): OfferTypeModel => OfferTypeModel::fromArray($row), $this->db->query($sql)->fetchAll());
    }

    private function findOfferType(int $id, bool $activeOnly = false): ?OfferTypeModel
    {
        $sql = 'SELECT id, name, description, status, created_at FROM offer_types WHERE id = :id'
            . ($activeOnly ? " AND status = 'active'" : '');
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? OfferTypeModel::fromArray($row) : null;
    }

    /** @return OfferModel[] */
    private function fetchOffers(array $filters = [], bool $publicOnly = false): array
    {
        $where = [];
        $params = [];

        if ($publicOnly) {
            $where[] = "o.status = 'Publiée'";
            $where[] = "ot.status = 'active'";
            $where[] = '(o.deadline IS NULL OR o.deadline >= CURDATE())';
        }
        if (!empty($filters['type_id'])) {
            $where[] = 'o.type_id = :type_id';
            $params['type_id'] = (int) $filters['type_id'];
        }
        if (!empty($filters['work_mode'])) {
            $where[] = 'o.work_mode = :work_mode';
            $params['work_mode'] = (string) $filters['work_mode'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(o.title LIKE :title_term OR o.company LIKE :company_term OR o.location LIKE :location_term)';
            $term = '%' . (string) $filters['q'] . '%';
            $params['title_term'] = $term;
            $params['company_term'] = $term;
            $params['location_term'] = $term;
        }

        $sql = 'SELECT o.id, o.type_id, ot.name AS type_name, o.title, o.company, o.location,
                       o.work_mode, o.description, o.requirements, o.deadline, o.status, o.created_at
                FROM offers o
                INNER JOIN offer_types ot ON ot.id = o.type_id'
            . ($where !== [] ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY o.created_at DESC, o.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(static fn (array $row): OfferModel => OfferModel::fromArray($row), $stmt->fetchAll());
    }

    private function findOffer(int $id, bool $publicOnly = false): ?OfferModel
    {
        $sql = 'SELECT o.id, o.type_id, ot.name AS type_name, o.title, o.company, o.location,
                       o.work_mode, o.description, o.requirements, o.deadline, o.status, o.created_at
                FROM offers o
                INNER JOIN offer_types ot ON ot.id = o.type_id
                WHERE o.id = :id'
            . ($publicOnly ? " AND o.status = 'Publiée' AND ot.status = 'active' AND (o.deadline IS NULL OR o.deadline >= CURDATE())" : '');
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? OfferModel::fromArray($row) : null;
    }

    private function countOffersForType(int $typeId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM offers WHERE type_id = :id');
        $stmt->execute(['id' => $typeId]);

        return (int) $stmt->fetchColumn();
    }

    private function countPublishedOffersForType(int $typeId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM offers WHERE type_id = :id AND status = 'Publiée' AND (deadline IS NULL OR deadline >= CURDATE())");
        $stmt->execute(['id' => $typeId]);

        return (int) $stmt->fetchColumn();
    }
}
