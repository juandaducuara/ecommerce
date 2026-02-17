<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'order_id',
        'transaction_id',
        'gateway',
        'method',
        'status',
        'amount',
        'currency',
        'gateway_response',
        'error_code',
        'error_message',
        'payer_email',
        'payer_document',
        'card_last_four',
        'card_brand',
        'ip_address',
        'processed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Estados de pago
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED   = 'approved';
    const STATUS_DECLINED   = 'declined';
    const STATUS_ERROR      = 'error';
    const STATUS_REFUNDED   = 'refunded';
    const STATUS_CANCELLED  = 'cancelled';

    // Gateways
    const GATEWAY_PAYU        = 'payu';
    const GATEWAY_MERCADOPAGO = 'mercadopago';

    // Obtener pagos de una orden
    public function getByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Buscar por transaction_id
    public function findByTransactionId(string $transactionId)
    {
        return $this->where('transaction_id', $transactionId)->first();
    }

    // Obtener último pago de una orden
    public function getLastPayment(int $orderId)
    {
        return $this->where('order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    // Crear pago pendiente
    public function createPending(
        int $orderId,
        float $amount,
        string $gateway,
        ?string $payerEmail = null
    ): int {
        return $this->insert([
            'order_id'    => $orderId,
            'gateway'     => $gateway,
            'status'      => self::STATUS_PENDING,
            'amount'      => $amount,
            'currency'    => 'COP',
            'payer_email' => $payerEmail,
            'ip_address'  => service('request')->getIPAddress(),
        ]);
    }

    // Aprobar pago
    public function approve(int $paymentId, string $transactionId, array $gatewayResponse = []): bool
    {
        return $this->update($paymentId, [
            'transaction_id'   => $transactionId,
            'status'           => self::STATUS_APPROVED,
            'gateway_response' => json_encode($gatewayResponse),
            'processed_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    // Rechazar pago
    public function decline(int $paymentId, string $errorCode, string $errorMessage, array $gatewayResponse = []): bool
    {
        return $this->update($paymentId, [
            'status'           => self::STATUS_DECLINED,
            'error_code'       => $errorCode,
            'error_message'    => $errorMessage,
            'gateway_response' => json_encode($gatewayResponse),
            'processed_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    // Estadísticas de pagos
    public function getStats(string $period = 'month'): array
    {
        $startDate = match ($period) {
            'day'   => date('Y-m-d 00:00:00'),
            'week'  => date('Y-m-d 00:00:00', strtotime('-7 days')),
            'month' => date('Y-m-01 00:00:00'),
            'year'  => date('Y-01-01 00:00:00'),
            default => date('Y-m-01 00:00:00'),
        };

        $approved = $this->selectSum('amount')
            ->where('status', self::STATUS_APPROVED)
            ->where('created_at >=', $startDate)
            ->first();

        $declined = $this->where('status', self::STATUS_DECLINED)
            ->where('created_at >=', $startDate)
            ->countAllResults();

        return [
            'total_approved' => $approved->amount ?? 0,
            'declined_count' => $declined,
        ];
    }
}
