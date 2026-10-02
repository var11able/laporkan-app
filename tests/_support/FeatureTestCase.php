<?php

namespace Tests\Support;

use App\Entities\Masyarakat;
use App\Entities\User;
use App\Services\AuthService;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use Config\Security;
use Config\Services;

abstract class FeatureTestCase extends DatabaseTestCase
{
    use FeatureTestTrait;

    protected const TOKEN = 'token-uji-csrf';

    private array $sesi = [];

    protected function setUp(): void
    {
        parent::setUp();

        $security                 = config(Security::class);
        $security->tokenRandomize = false;
        $security->redirect       = false;

        cache()->clean();
        $this->sesi = [];
    }

    protected function sebagaiWarga(Masyarakat $warga): static
    {
        $this->sesi = [AuthService::KEY_WARGA => $warga->getId()];

        return $this;
    }

    protected function sebagaiPetugas(User $petugas): static
    {
        $this->sesi = [AuthService::KEY_PETUGAS => $petugas->getId()];

        return $this;
    }

    protected function lanjutkanSesi(): static
    {
        $this->sesi = $_SESSION ?? [];

        return $this;
    }

    protected function buka(string $path): TestResponse
    {
        Services::resetSingle('auth');
        Services::resetSingle('security');
        Services::resetSingle('routes');
        Services::resetSingle('router');

        return $this->withSession(array_merge($this->sesi, [config(Security::class)->tokenName => self::TOKEN]))->get($path);
    }

    protected function kirim(string $path, array $data = [], string $method = 'POST', bool $denganToken = true): TestResponse
    {
        Services::resetSingle('auth');
        Services::resetSingle('security');
        Services::resetSingle('routes');
        Services::resetSingle('router');
        $tokenName = config(Security::class)->tokenName;

        if ($denganToken) {
            $data[$tokenName] = self::TOKEN;
        }

        if ($method !== 'POST') {
            $data['_method'] = $method;
        }

        return $this->withSession(array_merge($this->sesi, [$tokenName => self::TOKEN]))->post($path, $data);
    }
}
