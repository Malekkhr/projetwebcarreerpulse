<?php
declare(strict_types=1);

namespace CareerPulse\Models;

/** Type d'offre (table offer_types), relié aux offres par type_id. */
final class OfferTypeModel
{
    private int $id = 0;
    private string $name = '';
    private string $description = '';
    private string $status = 'active';
    private int $offerCount = 0;
    private string $createdAt = '';

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getStatus(): string { return $this->status; }
    public function getOfferCount(): int { return $this->offerCount; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setOfferCount(int $offerCount): void { $this->offerCount = $offerCount; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    /** Convertit une ligne PDO en objet métier. */
    public static function fromArray(array $row): self
    {
        $model = new self();
        $model->setId((int) ($row['id'] ?? 0));
        $model->setName((string) ($row['name'] ?? ''));
        $model->setDescription((string) ($row['description'] ?? ''));
        $model->setStatus((string) ($row['status'] ?? 'active'));
        $model->setOfferCount((int) ($row['offer_count'] ?? 0));
        $model->setCreatedAt((string) ($row['created_at'] ?? ''));

        return $model;
    }
}
