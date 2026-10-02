<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jenis = $arguments[0] ?? 'warga';
        $auth  = service('auth');

        $akun = $jenis === 'petugas' ? $auth->petugas() : $auth->warga();

        if ($akun !== null) {
            return null;
        }

        if ($request instanceof IncomingRequest && strtoupper($request->getMethod()) === 'GET') {
            $query = $request->getUri()->getQuery();
            session()->set('kembali_ke', current_url() . ($query !== '' ? '?' . $query : ''));
        }

        return redirect()
            ->to($jenis === 'petugas' ? url_to('petugas.masuk') : url_to('masuk'))
            ->with('info', 'Silakan masuk terlebih dahulu.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
