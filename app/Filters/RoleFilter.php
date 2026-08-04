<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('user_id')) {
            if ($request->isAJAX()) {
                return response()->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'
                ]);
            }
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $userRole = session()->get('role');
        
        // If arguments are provided (e.g. role:reviewer,admin)
        if ($arguments && !in_array($userRole, $arguments)) {
            if ($request->isAJAX()) {
                return response()->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk tindakan ini.'
                ]);
            }
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
