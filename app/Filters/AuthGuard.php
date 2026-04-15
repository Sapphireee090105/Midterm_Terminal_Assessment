<?php namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthGuard implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        if (!session()->get('isLoggedIn')) {
            return service('response')->setJSON(['error' => 'Unauthorized access. Please login first.'])->setStatusCode(401);
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}