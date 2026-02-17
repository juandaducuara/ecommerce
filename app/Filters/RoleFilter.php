<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * @param array|null $arguments Lista de roles permitidos (e.g., ['admin', 'super-admin'])
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        if ($arguments === null) {
            return;
        }

        $userRole = session()->get('user_role');

        if (!in_array($userRole, $arguments, true)) {
            return service('response')->setStatusCode(403, 'Acceso denegado')
                ->setBody(view('errors/html/error_403', [
                    'message' => 'No tienes permisos para acceder a esta página.',
                ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
