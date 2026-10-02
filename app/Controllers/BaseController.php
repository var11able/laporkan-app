<?php

namespace App\Controllers;

use App\Services\AuthService;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text', 'tampilan'];
    protected AuthService $auth;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->auth = service('auth');
    }

    protected function tampil(string $view, array $data = []): string
    {
        return view($view, $data + [
            'warga'   => $this->auth->warga(),
            'petugas' => $this->auth->petugas(),
        ]);
    }

    protected function validasi(array $aturan): bool
    {
        return $this->validateData($this->request->getPost() ?? [], $aturan);
    }

    protected function tidakDitemukan(string $pesan = 'Halaman yang Anda cari tidak ditemukan.'): PageNotFoundException
    {
        return PageNotFoundException::forPageNotFound($pesan);
    }

    protected function kembaliDenganError(?string $pesan = null): RedirectResponse
    {
        $redirect = redirect()->back()->withInput();

        return $pesan !== null ? $redirect->with('error', $pesan) : $redirect;
    }

    protected function file(string $field): ?UploadedFile
    {
        $file = $this->request->getFile($field);

        return $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE ? $file : null;
    }

    protected function files(string $field): array
    {
        $files = $this->request->getFileMultiple($field) ?? [];

        return array_values(array_filter($files, static fn ($f) => $f instanceof UploadedFile && $f->getError() !== UPLOAD_ERR_NO_FILE));
    }
}
