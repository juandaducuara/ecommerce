<?php

namespace App\Infrastructure\Persistence\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;

    protected $allowedFields = [
        'group',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $useTimestamps = true;
    protected $createdField  = false;
    protected $updatedField  = 'updated_at';

    // Cache de configuraciones
    protected static array $cache = [];

    // Obtener valor
    public function getValue(string $key, ?string $group = null, $default = null)
    {
        $cacheKey = $group ? "{$group}.{$key}" : $key;

        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $builder = $this->where('key', $key);

        if ($group) {
            $builder->where('group', $group);
        }

        $setting = $builder->first();

        if (!$setting) {
            return $default;
        }

        $value = $this->castValue($setting->value, $setting->type);
        self::$cache[$cacheKey] = $value;

        return $value;
    }

    // Establecer valor
    public function setValue(string $key, $value, ?string $group = 'general'): bool
    {
        $existing = $this->where('key', $key)
            ->where('group', $group)
            ->first();

        $data = [
            'group' => $group,
            'key'   => $key,
            'value' => is_array($value) ? json_encode($value) : $value,
        ];

        // Limpiar cache
        $cacheKey = "{$group}.{$key}";
        unset(self::$cache[$cacheKey]);

        if ($existing) {
            return $this->update($existing->id, $data);
        }

        return (bool)$this->insert($data);
    }

    // Obtener grupo completo
    public function getGroup(string $group): array
    {
        $settings = $this->where('group', $group)->findAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $this->castValue($setting->value, $setting->type);
        }

        return $result;
    }

    // Obtener configuraciones públicas
    public function getPublic(): array
    {
        $settings = $this->where('is_public', 1)->findAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->group][$setting->key] = $this->castValue($setting->value, $setting->type);
        }

        return $result;
    }

    // Obtener todas las configuraciones agrupadas
    public function getAllGrouped(): array
    {
        $settings = $this->findAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->group][$setting->key] = [
                'value'       => $this->castValue($setting->value, $setting->type),
                'type'        => $setting->type,
                'description' => $setting->description,
            ];
        }

        return $result;
    }

    // Castear valor según tipo
    protected function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int)$value,
            'boolean' => (bool)$value,
            'json', 'array' => json_decode($value, true) ?? [],
            'float'   => (float)$value,
            default   => $value,
        };
    }

    // Limpiar cache
    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
