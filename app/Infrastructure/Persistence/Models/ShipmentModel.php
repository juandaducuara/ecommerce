<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class ShipmentModel extends Model
{
    protected $table            = 'shipments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'order_id',
        'carrier',
        'tracking_number',
        'tracking_url',
        'status',
        'weight',
        'cost',
        'label_url',
        'shipped_at',
        'estimated_delivery',
        'delivered_at',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Estados de envío
    const STATUS_PENDING          = 'pending';
    const STATUS_PICKED_UP        = 'picked_up';
    const STATUS_IN_TRANSIT       = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED        = 'delivered';
    const STATUS_FAILED           = 'failed';
    const STATUS_RETURNED         = 'returned';

    // Obtener envíos de una orden
    public function getByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    // Buscar por número de seguimiento
    public function findByTracking(string $trackingNumber)
    {
        return $this->where('tracking_number', $trackingNumber)->first();
    }

    // Crear envío
    public function createShipment(int $orderId, string $carrier, ?float $cost = null): int
    {
        return $this->insert([
            'order_id' => $orderId,
            'carrier'  => $carrier,
            'status'   => self::STATUS_PENDING,
            'cost'     => $cost,
        ]);
    }

    // Actualizar estado
    public function updateStatus(int $shipmentId, string $status): bool
    {
        $data = ['status' => $status];

        if ($status === self::STATUS_PICKED_UP || $status === self::STATUS_IN_TRANSIT) {
            $data['shipped_at'] = date('Y-m-d H:i:s');
        }

        if ($status === self::STATUS_DELIVERED) {
            $data['delivered_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($shipmentId, $data);
    }

    // Agregar número de seguimiento
    public function setTracking(int $shipmentId, string $trackingNumber, ?string $trackingUrl = null): bool
    {
        return $this->update($shipmentId, [
            'tracking_number' => $trackingNumber,
            'tracking_url'    => $trackingUrl,
        ]);
    }

    // Obtener envíos pendientes
    public function getPending(int $limit = 50)
    {
        return $this->where('status', self::STATUS_PENDING)
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }

    // Obtener envíos en tránsito
    public function getInTransit(int $limit = 50)
    {
        return $this->whereIn('status', [
                self::STATUS_PICKED_UP,
                self::STATUS_IN_TRANSIT,
                self::STATUS_OUT_FOR_DELIVERY,
            ])
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }
}
