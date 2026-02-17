<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'order_number',
        'user_id',
        'billing_address_id',
        'shipping_address_id',
        'coupon_id',
        'status',
        'payment_status',
        'subtotal',
        'discount',
        'shipping_cost',
        'tax',
        'total',
        'currency',
        'items_count',
        'coupon_code',
        'customer_email',
        'customer_phone',
        'customer_notes',
        'admin_notes',
        'shipping_data',
        'billing_data',
        'ip_address',
        'user_agent',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Estados de orden
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REFUNDED   = 'refunded';

    // Estados de pago
    const PAYMENT_PENDING            = 'pending';
    const PAYMENT_PAID               = 'paid';
    const PAYMENT_FAILED             = 'failed';
    const PAYMENT_REFUNDED           = 'refunded';
    const PAYMENT_PARTIALLY_REFUNDED = 'partially_refunded';

    // Generar número de orden
    public function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(3)));

        return "{$prefix}-{$timestamp}-{$random}";
    }

    // Buscar por número de orden
    public function findByOrderNumber(string $orderNumber)
    {
        return $this->where('order_number', $orderNumber)->first();
    }

    // Obtener órdenes de un usuario
    public function getByUser(int $userId, int $limit = 20, int $offset = 0)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    // Obtener orden con items
    public function getWithItems(int $orderId)
    {
        $order = $this->find($orderId);

        if ($order) {
            $order->items = (new OrderItemModel())
                ->where('order_id', $orderId)
                ->findAll();
        }

        return $order;
    }

    // Obtener orden con todo
    public function getFullOrder(int $orderId)
    {
        $order = $this->getWithItems($orderId);

        if ($order) {
            $order->payments = (new PaymentModel())
                ->where('order_id', $orderId)
                ->findAll();

            $order->shipments = (new ShipmentModel())
                ->where('order_id', $orderId)
                ->findAll();
        }

        return $order;
    }

    // Actualizar estado
    public function updateStatus(int $orderId, string $status): bool
    {
        $data = ['status' => $status];

        // Actualizar timestamps según el estado
        switch ($status) {
            case self::STATUS_SHIPPED:
                $data['shipped_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_DELIVERED:
                $data['delivered_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_CANCELLED:
                $data['cancelled_at'] = date('Y-m-d H:i:s');
                break;
        }

        return $this->update($orderId, $data);
    }

    // Actualizar estado de pago
    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        $data = ['payment_status' => $paymentStatus];

        if ($paymentStatus === self::PAYMENT_PAID) {
            $data['paid_at'] = date('Y-m-d H:i:s');
            $data['status'] = self::STATUS_CONFIRMED;
        }

        return $this->update($orderId, $data);
    }

    // Obtener por estado
    public function getByStatus(string $status, int $limit = 50)
    {
        return $this->where('status', $status)
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }

    // Obtener órdenes pendientes de pago
    public function getPendingPayment(int $limit = 50)
    {
        return $this->where('payment_status', self::PAYMENT_PENDING)
            ->where('status !=', self::STATUS_CANCELLED)
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }

    // Estadísticas
    public function getStats(string $period = 'month'): array
    {
        $startDate = match ($period) {
            'day'   => date('Y-m-d 00:00:00'),
            'week'  => date('Y-m-d 00:00:00', strtotime('-7 days')),
            'month' => date('Y-m-01 00:00:00'),
            'year'  => date('Y-01-01 00:00:00'),
            default => date('Y-m-01 00:00:00'),
        };

        $totalOrders = $this->where('created_at >=', $startDate)
            ->countAllResults();

        $totalRevenue = $this->selectSum('total')
            ->where('created_at >=', $startDate)
            ->where('payment_status', self::PAYMENT_PAID)
            ->first();

        $pendingOrders = $this->where('status', self::STATUS_PENDING)
            ->where('created_at >=', $startDate)
            ->countAllResults();

        return [
            'total_orders'   => $totalOrders,
            'total_revenue'  => $totalRevenue->total ?? 0,
            'pending_orders' => $pendingOrders,
            'period'         => $period,
        ];
    }

    // Filtrar órdenes (para admin)
    public function filter(array $filters, int $page = 1, int $perPage = 20): array
    {
        $builder = $this;

        if (!empty($filters['status'])) {
            $builder = $builder->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $builder = $builder->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['date_from'])) {
            $builder = $builder->where('created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder = $builder->where('created_at <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder = $builder->groupStart()
                ->like('order_number', $filters['search'])
                ->orLike('customer_email', $filters['search'])
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $orders = $builder->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);

        return [
            'orders' => $orders,
            'total'  => $total,
            'pager'  => $builder->pager,
        ];
    }
}
