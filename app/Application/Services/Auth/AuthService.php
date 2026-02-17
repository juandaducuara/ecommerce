<?php

namespace App\Application\Services\Auth;

use App\Infrastructure\Persistence\Models\UserModel;
use App\Infrastructure\Persistence\Models\RoleModel;

class AuthService
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Intentar autenticar al usuario con email y password.
     *
     * @return array{success: bool, message: string, user?: object, role?: object}
     */
    public function attempt(string $email, string $password): array
    {
        $user = $this->userModel->findActiveByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Credenciales incorrectas.'];
        }

        if (!$this->userModel->verifyPassword($password, $user->password)) {
            return ['success' => false, 'message' => 'Credenciales incorrectas.'];
        }

        $role = $this->roleModel->find($user->role_id);

        $session = session();
        $session->set([
            'user_id'     => $user->id,
            'user_email'  => $user->email,
            'user_name'   => trim($user->first_name . ' ' . $user->last_name),
            'user_role'   => $role->slug,
            'is_logged_in' => true,
        ]);

        $this->userModel->updateLastLogin($user->id);

        return ['success' => true, 'message' => 'Login exitoso.', 'user' => $user, 'role' => $role];
    }

    /**
     * Registrar un nuevo usuario con rol customer.
     *
     * @return array{success: bool, message: string, user_id?: int}
     */
    public function register(array $data): array
    {
        $customerRole = $this->roleModel->findBySlug('customer');

        if (!$customerRole) {
            return ['success' => false, 'message' => 'Error de configuración: rol customer no encontrado.'];
        }

        $userData = [
            'role_id'    => $customerRole->id,
            'email'      => $data['email'],
            'password'   => $data['password'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'status'     => 'active',
        ];

        $userId = $this->userModel->insert($userData);

        if (!$userId) {
            $errors = $this->userModel->errors();
            return ['success' => false, 'message' => implode(' ', $errors)];
        }

        return ['success' => true, 'message' => 'Cuenta creada exitosamente.', 'user_id' => $userId];
    }

    public function logout(): void
    {
        session()->destroy();
    }

    /**
     * Generar token de reset de password.
     *
     * @return array{success: bool, message: string, token?: string}
     */
    public function generateResetToken(string $email): array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            // No revelar si el email existe o no
            return ['success' => true, 'message' => 'Si el email existe, recibirás instrucciones para restablecer tu contraseña.'];
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->userModel->protect(false)
            ->update($user->id, [
                'reset_token'            => $token,
                'reset_token_expires_at' => $expiresAt,
            ]);
        $this->userModel->protect(true);

        return [
            'success' => true,
            'message' => 'Si el email existe, recibirás instrucciones para restablecer tu contraseña.',
            'token'   => $token,
        ];
    }

    /**
     * Restablecer password usando token.
     *
     * @return array{success: bool, message: string}
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return ['success' => false, 'message' => 'Token inválido o expirado.'];
        }

        if (strtotime($user->reset_token_expires_at) < time()) {
            return ['success' => false, 'message' => 'Token inválido o expirado.'];
        }

        $this->userModel->protect(false)
            ->update($user->id, [
                'password'               => $newPassword,
                'reset_token'            => null,
                'reset_token_expires_at' => null,
            ]);
        $this->userModel->protect(true);

        return ['success' => true, 'message' => 'Contraseña actualizada exitosamente.'];
    }
}
