<?php
declare(strict_types=1);

namespace CareerPulse\Models;

/** Offre publiée par une entreprise (table offers), rattachée à un type d'offre. */
final class OfferModel
{
    private int $id = 0;
    private int $typeId = 0;
    private string $typeName = '';
    private string $title = '';
    private string $company = '';
    private string $location = '';
    private string $workMode = 'Sur site';
    private string $description = '';
    private string $requirements = '';
    private string $deadline = '';
    private string $status = 'Brouillon';
    private string $createdAt = '';

    public function getId(): int { return $this->id; }
    public function getTypeId(): int { return $this->typeId; }
    public function getTypeName(): string { return $this->typeName; }
    public function getTitle(): string { return $this->title; }
    public function getCompany(): string { return $this->company; }
    public function getLocation(): string { return $this->location; }
    public function getWorkMode(): string { return $this->workMode; }
    public function getDescription(): string { return $this->description; }
    public function getRequirements(): string { return $this->requirements; }
    public function getDeadline(): string { return $this->deadline; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setTypeId(int $typeId): void { $this->typeId = $typeId; }
    public function setTypeName(string $typeName): void { $this->typeName = $typeName; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setCompany(string $company): void { $this->company = $company; }
    public function setLocation(string $location): void { $this->location = $location; }
    public function setWorkMode(string $workMode): void { $this->workMode = $workMode; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setRequirements(string $requirements): void { $this->requirements = $requirements; }
    public function setDeadline(string $deadline): void { $this->deadline = $deadline; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    /** Convertit les résultats de la jointure offers / offer_types en objet. */
    public static function fromArray(array $row): self
    {
        $model = new self();
        $model->setId((int) ($row['id'] ?? 0));
        $model->setTypeId((int) ($row['type_id'] ?? 0));
        $model->setTypeName((string) ($row['type_name'] ?? ''));
        $model->setTitle((string) ($row['title'] ?? ''));
        $model->setCompany((string) ($row['company'] ?? ''));
        $model->setLocation((string) ($row['location'] ?? ''));
        $model->setWorkMode((string) ($row['work_mode'] ?? 'Sur site'));
        $model->setDescription((string) ($row['description'] ?? ''));
        $model->setRequirements((string) ($row['requirements'] ?? ''));
        $model->setDeadline((string) ($row['deadline'] ?? ''));
        $model->setStatus((string) ($row['status'] ?? 'Brouillon'));
        $model->setCreatedAt((string) ($row['created_at'] ?? ''));

        return $model;
    }
}
