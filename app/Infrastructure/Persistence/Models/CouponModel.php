<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table            = 'coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Tipos de cupón
    const TYPE_PERCENTAGE    = 'percentage';
    const TYPE_FIXED_AMOUNT  = 'fixed_amount';
    const TYPE_FREE_SHIPPING = 'free_shipping';

    // Buscar por código
    public function findByCode(string $code)
    {
        return $this->where('code', strtoupper($code))->first();
    }

    // Validar cupón
    public function isValid(string $code, float $orderAmount, ?int $userId = null): array
    {
        $coupon = $this->findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Cupón no encontrado'];
        }

        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => 'Este cupón no está activo'];
        }

        // Verificar fechas
        $now = date('Y-m-d H:i:s');

        if ($coupon->starts_at && $coupon->starts_at > $now) {
            return ['valid' => false, 'message' => 'Este cupón aún no está vigente'];
        }

        if ($coupon->expires_at && $coupon->expires_at < $now) {
            return ['valid' => false, 'message' => 'Este cupón ha expirado'];
        }

        // Verificar límite de uso global
        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => 'Este cupón ha alcanzado su límite de uso'];
        }

        // Verificar monto mínimo
        if ($coupon->min_order_amount && $orderAmount < $coupon->min_order_amount) {
            return [
                'valid'   => false,
                'message' => "El monto mínimo de compra es $" . number_format($coupon->min_order_amount, 0, ',', '.'),
            ];
        }

        // TODO: Verificar uso por usuario si $userId está presente

        return ['valid' => true, 'coupon' => $coupon];
    }

    // Calcular descuento
    public function calculateDiscount(object $coupon, float $orderAmount): float
    {
        $discount = match ($coupon->type) {
            self::TYPE_PERCENTAGE   => $orderAmount * ($coupon->value / 100),
            self::TYPE_FIXED_AMOUNT => $coupon->value,
            self::TYPE_FREE_SHIPPING => 0, // Se maneja aparte
            default                 => 0,
        };

        // Aplicar límite máximo de descuento
        if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
            $discount = $coupon->max_discount_amount;
        }

        return min($discount, $orderAmount);
    }

    // Incrementar uso
    public function incrementUsage(int $couponId): bool
    {
        return $this->set('times_used', 'times_used + 1', false)
            ->where('id', $couponId)
            ->update();
    }

    // Obtener cupones activos
    public function getActive()
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('is_active', 1)
            ->groupStart()
            ->where('starts_at IS NULL')
            ->orWhere('starts_at <=', $now)
            ->groupEnd()
            ->groupStart()
            ->where('expires_at IS NULL')
            ->orWhere('expires_at >=', $now)
            ->groupEnd()
            ->findAll();
    }
}
