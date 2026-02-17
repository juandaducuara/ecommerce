<?php

namespace App\Application\DTOs\Sales;

class OrderDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $orderNumber,
        public readonly ?int $userId,
        public readonly string $status,
        public readonly string $paymentStatus,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $shippingCost,
        public readonly float $tax,
        public readonly float $total,
        public readonly string $currency,
        public readonly int $itemsCount,
        public readonly string $customerEmail,
        public readonly ?string $customerPhone,
        public readonly array $items,
        public readonly ?array $shippingData,
        public readonly ?array $billingData,
        public readonly ?string $paidAt,
        public readonly ?string $shippedAt,
        public readonly ?string $deliveredAt,
        public readonly string $createdAt,
        public readonly ?array $paymentData,
    ) {}

    public static function fromModel(object $order, array $items = [], ?array $paymentData = null): self
    {
        return new self(
            id: $order->id,
            orderNumber: $order->order_number,
            userId: $order->user_id,
            status: $order->status,
            paymentStatus: $order->payment_status,
            subtotal: (float)$order->subtotal,
            discount: (float)$order->discount,
            shippingCost: (float)$order->shipping_cost,
            tax: (float)$order->tax,
            total: (float)$order->total,
            currency: $order->currency,
            itemsCount: (int)$order->items_count,
            customerEmail: $order->customer_email,
            customerPhone: $order->customer_phone,
            items: $items,
            shippingData: $order->shipping_data
                ? json_decode($order->shipping_data, true)
                : null,
            billingData: $order->billing_data
                ? json_decode($order->billing_data, true)
                : null,
            paidAt: $order->paid_at,
            shippedAt: $order->shipped_at,
            deliveredAt: $order->delivered_at,
            createdAt: $order->created_at,
            paymentData: $paymentData,
        );
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'order_number'   => $this->orderNumber,
            'user_id'        => $this->userId,
            'status'         => $this->status,
            'payment_status' => $this->paymentStatus,
            'subtotal'       => $this->subtotal,
            'discount'       => $this->discount,
            'shipping_cost'  => $this->shippingCost,
            'tax'            => $this->tax,
            'total'          => $this->total,
            'currency'       => $this->currency,
            'items_count'    => $this->itemsCount,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'items'          => $this->items,
            'shipping_data'  => $this->shippingData,
            'billing_data'   => $this->billingData,
            'paid_at'        => $this->paidAt,
            'shipped_at'     => $this->shippedAt,
            'delivered_at'   => $this->deliveredAt,
            'created_at'     => $this->createdAt,
            'payment_data'   => $this->paymentData,
        ];
    }

    public function isPaid(): bool
    {
        return $this->paymentStatus === 'paid';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending'    => 'Pendiente',
            'processing' => 'Procesando',
            'confirmed'  => 'Confirmado',
            'shipped'    => 'Enviado',
            'delivered'  => 'Entregado',
            'cancelled'  => 'Cancelado',
            'refunded'   => 'Reembolsado',
            default      => $this->status,
        };
    }

    public function getPaymentStatusLabel(): string
    {
        return match ($this->paymentStatus) {
            'pending'            => 'Pendiente',
            'paid'               => 'Pagado',
            'failed'             => 'Fallido',
            'refunded'           => 'Reembolsado',
            'partially_refunded' => 'Reembolso parcial',
            default              => $this->paymentStatus,
        };
    }
}
