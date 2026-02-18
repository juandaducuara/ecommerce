<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\SettingModel;

class SettingsController extends BaseController
{
    protected SettingModel $settings;

    public function __construct()
    {
        $this->settings = new SettingModel();
    }

    public function index()
    {
        $this->ensureDefaults();

        $general = $this->settings->getGroup('general');
        $social  = $this->settings->getGroup('social');
        $payment = $this->settings->getGroup('payment');

        return view('admin/settings', [
            'title'   => 'Configuración de la tienda',
            'general' => $general,
            'social'  => $social,
            'payment' => $payment,
        ]);
    }

    public function update()
    {
        $generalFields = [
            'store_name', 'store_email', 'store_phone', 'store_address',
            'brand_primary_color', 'tagline', 'hero_title', 'hero_subtitle',
        ];
        foreach ($generalFields as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                $this->settings->setValue($key, trim($value), 'general');
            }
        }

        $socialFields = ['facebook_url', 'instagram_url', 'twitter_url', 'whatsapp_number'];
        foreach ($socialFields as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                $this->settings->setValue($key, trim($value), 'social');
            }
        }

        $paymentFields = [
            'payu_merchant_id', 'payu_api_key', 'payu_api_login', 'payu_account_id',
            'mercadopago_public_key', 'mercadopago_access_token',
        ];
        foreach ($paymentFields as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                $this->settings->setValue($key, trim($value), 'payment');
            }
        }

        // Checkboxes de sandbox (si no vienen en POST = false)
        $this->settings->setValue('payu_sandbox',        $this->request->getPost('payu_sandbox')        ? '1' : '0', 'payment');
        $this->settings->setValue('mercadopago_sandbox', $this->request->getPost('mercadopago_sandbox') ? '1' : '0', 'payment');

        SettingModel::clearCache();

        return redirect()->to('/admin/settings')->with('success', 'Configuración guardada correctamente.');
    }

    public function uploadLogo()
    {
        $file = $this->request->getFile('logo');

        if (!$file || !$file->isValid()) {
            return redirect()->to('/admin/settings')->with('error', 'No se recibió ningún archivo válido.');
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml', 'image/avif'];
        if (!in_array($file->getMimeType(), $allowed)) {
            return redirect()->to('/admin/settings')->with('error', 'El archivo debe ser una imagen (JPG, PNG, WEBP, GIF o SVG).');
        }

        $uploadDir = FCPATH . 'uploads/brand/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $oldLogo = $this->settings->getValue('store_logo', 'general', '');
        if ($oldLogo && file_exists(FCPATH . $oldLogo)) {
            @unlink(FCPATH . $oldLogo);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        $this->settings->setValue('store_logo', 'uploads/brand/' . $newName, 'general');
        SettingModel::clearCache();

        return redirect()->to('/admin/settings')->with('success', 'Logo actualizado correctamente.');
    }

    public function deleteLogo()
    {
        $logo = $this->settings->getValue('store_logo', 'general', '');
        if ($logo && file_exists(FCPATH . $logo)) {
            @unlink(FCPATH . $logo);
        }

        $this->settings->setValue('store_logo', '', 'general');
        SettingModel::clearCache();

        return redirect()->to('/admin/settings')->with('success', 'Logo eliminado.');
    }

    // Inserta valores por defecto si todavía no existen en la BD
    private function ensureDefaults(): void
    {
        $defaults = [
            ['setting_key' => 'brand_primary_color', 'value' => '#4f46e5', 'type' => 'string', 'description' => 'Color principal de marca', 'is_public' => 1],
            ['setting_key' => 'tagline',             'value' => 'Tu tienda de confianza',                                      'type' => 'string', 'description' => 'Eslogan de la tienda',  'is_public' => 1],
            ['setting_key' => 'hero_title',          'value' => 'Bienvenido a nuestra tienda',                                'type' => 'string', 'description' => 'Título del hero',        'is_public' => 1],
            ['setting_key' => 'hero_subtitle',       'value' => 'Encuentra los mejores productos con envío a todo Colombia.', 'type' => 'string', 'description' => 'Subtítulo del hero',   'is_public' => 1],
        ];

        foreach ($defaults as $d) {
            if (empty($d['setting_key'])) continue;
            $exists = $this->settings->where('setting_group', 'general')->where('setting_key', $d['setting_key'])->first();
            if (!$exists) {
                $this->settings->insert(array_merge(['setting_group' => 'general'], $d));
            }
        }
    }
}
