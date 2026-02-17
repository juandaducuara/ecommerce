<?php

namespace App\Application\DTOs\Sales;

use App\Application\DTOs\Customer\AddressDTO;

class CheckoutDTO
{
    public function __construct(
        public readonly int $cartId,
        public readonly ?int $userId,
        public readonly string $customerEmail,
        public readonly ?string $customerPhone,
        public readonly AddressDTO $shippingAddress,
        public readonly ?AddressDTO $billingAddress,
        public readonly int $shippingRateId,
        public readonly string $paymentGateway,
        public readonly ?string $couponCode,
        public readonly ?string $customerNotes,
        public readonly string $ipAddress,
        public readonly string $userAgent,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            cartId: (int)$data['cart_id'],
            userId: $data['user_id'] ?? null,
            customerEmail: $data['customer_email'],
            customerPhone: $data['customer_phone'] ?? null,
            shippingAddress: AddressDTO::fromArray($data['shipping_address']),
            billingAddress: isset($data['billing_address'])
                ? AddressDTO::fromArray($data['billing_address'])
                : null,
            shippingRateId: (int)$data['shipping_rate_id'],
            paymentGateway: $data['payment_gateway'],
            couponCode: $data['coupon_code'] ?? null,
            customerNotes: $data['customer_notes'] ?? null,
            ipAddress: $data['ip_address'] ?? '',
            userAgent: $data['user_agent'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'cart_id'          => $this->cartId,
            'user_id'          => $this->userId,
            'customer_email'   => $this->customerEmail,
            'customer_phone'   => $this->customerPhone,
            'shipping_address' => $this->shippingAddress->toArray(),
            'billing_address'  => $this->billingAddress?->toArray(),
            'shipping_rate_id' => $this->shippingRateId,
            'payment_gateway'  => $this->paymentGateway,
            'coupon_code'      => $this->couponCode,
            'customer_notes'   => $this->customerNotes,
        ];
    }

    public function useSameAddressForBilling(): bool
    {
        return $this->billingAddress === null;
    }
}
