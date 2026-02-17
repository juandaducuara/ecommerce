<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Application\Services\Auth\AuthService;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    // ─── Login ───

    public function loginForm()
    {
        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->authService->attempt(
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $role = $result['role']->slug;
        if (in_array($role, ['super-admin', 'admin'], true)) {
            return redirect()->to('/admin')->with('success', 'Bienvenido de vuelta.');
        }

        return redirect()->to('/account')->with('success', 'Bienvenido de vuelta.');
    }

    // ─── Register ───

    public function registerForm()
    {
        return view('auth/register');
    }

    public function register()
    {
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Este email ya está registrado.',
            ],
            'password_confirm' => [
                'matches' => 'Las contraseñas no coinciden.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->authService->register([
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
        ]);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to('/login')->with('success', 'Cuenta creada exitosamente. Ahora puedes iniciar sesión.');
    }

    // ─── Logout ───

    public function logout()
    {
        $this->authService->logout();
        return redirect()->to('/login')->with('success', 'Has cerrado sesión.');
    }

    // ─── Forgot Password ───

    public function forgotPasswordForm()
    {
        return view('auth/forgot_password');
    }

    public function forgotPassword()
    {
        $rules = ['email' => 'required|valid_email'];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->authService->generateResetToken($this->request->getPost('email'));

        // En desarrollo, mostrar el link de reset
        if (ENVIRONMENT === 'development' && isset($result['token'])) {
            $resetUrl = site_url('reset-password/' . $result['token']);
            return redirect()->back()->with('success', $result['message'])
                ->with('dev_reset_url', $resetUrl);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    // ─── Reset Password ───

    public function resetPasswordForm(string $token)
    {
        return view('auth/reset_password', ['token' => $token]);
    }

    public function resetPassword()
    {
        $rules = [
            'token'            => 'required',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'password_confirm' => [
                'matches' => 'Las contraseñas no coinciden.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $token = $this->request->getPost('token');
            return redirect()->to('/reset-password/' . $token)->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->authService->resetPassword(
            $this->request->getPost('token'),
            $this->request->getPost('password')
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to('/login')->with('success', $result['message']);
    }
}
