<?php

namespace App\Application\DTOs\Customer;

class AddressDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $company,
        public readonly string $phone,
        public readonly string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly string $city,
        public readonly string $state,
        public readonly ?string $postalCode,
        public readonly string $country,
        public readonly ?string $additionalInfo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            company: $data['company'] ?? null,
            phone: $data['phone'],
            addressLine1: $data['address_line_1'],
            addressLine2: $data['address_line_2'] ?? null,
            city: $data['city'],
            state: $data['state'],
            postalCode: $data['postal_code'] ?? null,
            country: $data['country'] ?? 'CO',
            additionalInfo: $data['additional_info'] ?? null,
        );
    }

    public static function fromModel(object $address): self
    {
        return new self(
            id: $address->id,
            firstName: $address->first_name,
            lastName: $address->last_name,
            company: $address->company,
            phone: $address->phone,
            addressLine1: $address->address_line_1,
            addressLine2: $address->address_line_2,
            city: $address->city,
            state: $address->state,
            postalCode: $address->postal_code,
            country: $address->country,
            additionalInfo: $address->additional_info,
        );
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'first_name'      => $this->firstName,
            'last_name'       => $this->lastName,
            'company'         => $this->company,
            'phone'           => $this->phone,
            'address_line_1'  => $this->addressLine1,
            'address_line_2'  => $this->addressLine2,
            'city'            => $this->city,
            'state'           => $this->state,
            'postal_code'     => $this->postalCode,
            'country'         => $this->country,
            'additional_info' => $this->additionalInfo,
        ];
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getFormattedAddress(): string
    {
        $parts = [$this->addressLine1];

        if ($this->addressLine2) {
            $parts[] = $this->addressLine2;
        }

        $parts[] = $this->city . ', ' . $this->state;

        if ($this->postalCode) {
            $parts[] = $this->postalCode;
        }

        return implode(', ', $parts);
    }
}
