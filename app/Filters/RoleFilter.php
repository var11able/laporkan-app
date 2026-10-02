<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $petugas = service('auth')->petugas();

        if ($petugas === null) {
            return redirect()->to(url_to('petugas.masuk'));
        }

        if (in_array($petugas->jabatan, $arguments ?? [], true)) {
            return null;
        }

        service('auditLog')->catat(
            'Ditolak: akses ' . strtoupper($request->getMethod()) . ' /' . ltrim($request->getUri()->getPath(), '/') . ' sebagai ' . $petugas->jabatan()->label(),
            $petugas->getId(),
        );

        return redirect()->to(url_to('petugas.dasbor'))
            ->with('error', 'Akses ditolak. Fitur ini hanya untuk administrator. Hubungi administrator jika Anda memerlukannya.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
