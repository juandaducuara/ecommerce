<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class ShippingRateModel extends Model
{
    protected $table            = 'shipping_rates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'zone_id',
        'name',
        'min_weight',
        'max_weight',
        'min_order_amount',
        'max_order_amount',
        'price',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Obtener tarifas por zona
    public function getByZone(int $zoneId)
    {
        return $this->where('zone_id', $zoneId)
            ->where('is_active', 1)
            ->orderBy('price', 'ASC')
            ->findAll();
    }

    // Obtener tarifas aplicables
    public function getApplicable(int $zoneId, float $orderAmount, ?float $weight = null)
    {
        $builder = $this->where('zone_id', $zoneId)
            ->where('is_active', 1)
            ->groupStart()
            ->where('min_order_amount <=', $orderAmount)
            ->groupEnd()
            ->groupStart()
            ->where('max_order_amount IS NULL')
            ->orWhere('max_order_amount >=', $orderAmount)
            ->groupEnd();

        if ($weight !== null) {
            $builder->groupStart()
                ->where('min_weight <=', $weight)
                ->groupEnd()
                ->groupStart()
                ->where('max_weight IS NULL')
                ->orWhere('max_weight >=', $weight)
                ->groupEnd();
        }

        return $builder->orderBy('price', 'ASC')->findAll();
    }

    // Calcular costo de envío
    public function calculateCost(string $region, float $orderAmount, ?float $weight = null): array
    {
        $zone = (new ShippingZoneModel())->findByRegion($region);

        if (!$zone) {
            return [
                'available' => false,
                'message'   => 'No hay envíos disponibles para esta región',
            ];
        }

        $rates = $this->getApplicable($zone->id, $orderAmount, $weight);

        if (empty($rates)) {
            return [
                'available' => false,
                'message'   => 'No hay tarifas de envío disponibles para este pedido',
            ];
        }

        return [
            'available' => true,
            'zone'      => $zone,
            'rates'     => $rates,
        ];
    }

    // Formatear tiempo estimado
    public function getEstimatedTime(object $rate): string
    {
        if ($rate->estimated_days_min === $rate->estimated_days_max) {
            return "{$rate->estimated_days_min} días";
        }

        return "{$rate->estimated_days_min}-{$rate->estimated_days_max} días";
    }
}
