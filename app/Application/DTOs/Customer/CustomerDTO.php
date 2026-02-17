<?php

namespace App\Application\DTOs\Customer;

class CustomerDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $phone,
        public readonly ?string $documentType,
        public readonly ?string $documentNumber,
        public readonly string $status,
        public readonly ?string $emailVerifiedAt,
        public readonly string $createdAt,
        public readonly int $ordersCount,
        public readonly float $totalSpent,
    ) {}

    public static function fromModel(object $user, int $ordersCount = 0, float $totalSpent = 0): self
    {
        return new self(
            id: $user->id,
            email: $user->email,
            firstName: $user->first_name,
            lastName: $user->last_name,
            phone: $user->phone,
            documentType: $user->document_type,
            documentNumber: $user->document_number,
            status: $user->status,
            emailVerifiedAt: $user->email_verified_at,
            createdAt: $user->created_at,
            ordersCount: $ordersCount,
            totalSpent: $totalSpent,
        );
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'email'             => $this->email,
            'first_name'        => $this->firstName,
            'last_name'         => $this->lastName,
            'full_name'         => $this->getFullName(),
            'phone'             => $this->phone,
            'document_type'     => $this->documentType,
            'document_number'   => $this->documentNumber,
            'status'            => $this->status,
            'email_verified_at' => $this->emailVerifiedAt,
            'created_at'        => $this->createdAt,
            'orders_count'      => $this->ordersCount,
            'total_spent'       => $this->totalSpent,
        ];
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function isVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
