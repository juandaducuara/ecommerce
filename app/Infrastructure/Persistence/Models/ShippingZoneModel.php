<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class ShippingZoneModel extends Model
{
    protected $table            = 'shipping_zones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'name',
        'regions',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Obtener zonas activas
    public function getActive()
    {
        return $this->where('is_active', 1)->findAll();
    }

    // Obtener zona por región
    public function findByRegion(string $region)
    {
        $zones = $this->where('is_active', 1)->findAll();

        foreach ($zones as $zone) {
            $regions = json_decode($zone->regions, true) ?? [];
            if (in_array($region, $regions)) {
                return $zone;
            }
        }

        return null;
    }

    // Obtener zona con tarifas
    public function getWithRates(int $zoneId)
    {
        $zone = $this->find($zoneId);

        if ($zone) {
            $zone->rates = (new ShippingRateModel())
                ->where('zone_id', $zoneId)
                ->where('is_active', 1)
                ->orderBy('price', 'ASC')
                ->findAll();
        }

        return $zone;
    }

    // Obtener regiones de una zona
    public function getRegions(int $zoneId): array
    {
        $zone = $this->find($zoneId);

        if (!$zone) {
            return [];
        }

        return json_decode($zone->regions, true) ?? [];
    }
}
