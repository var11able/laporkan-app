<?php

namespace App\Services;

use App\Entities\Masyarakat;
use App\Entities\User;
use App\Models\MasyarakatModel;
use App\Models\UserModel;
use CodeIgniter\Session\Session;

class AuthService
{
    public const KEY_WARGA   = 'warga_id';
    public const KEY_PETUGAS = 'petugas_id';

    private const HASH_PALSU = '$2y$10$vg9Z9sxFpdU6atqs8BFUAeJxWoWLnLzqyxaMfWPA17l5taicznwEO';

    private ?Masyarakat $warga = null;
    private ?User $petugas     = null;

    public function __construct(
        private readonly Session $session,
        private readonly MasyarakatModel $masyarakatModel = new MasyarakatModel(),
        private readonly UserModel $userModel = new UserModel(),
    ) {
    }

    public function loginWarga(string $username, string $password): bool
    {
        $warga = $this->masyarakatModel->cariUsername(trim($username));

        if (! $this->cocok($warga?->password, $password)) {
            return false;
        }

        $this->rehashJikaPerlu($this->masyarakatModel, $warga->id_masyarakat, $warga->password, $password);
        $this->mulaiSesi(self::KEY_WARGA, $warga->id_masyarakat);
        $this->warga = $warga;

        return true;
    }

    public function loginPetugas(string $username, string $password): bool
    {
        $petugas = $this->userModel->cariUsername(trim($username));

        if (! $this->cocok($petugas?->password, $password)) {
            return false;
        }

        $this->rehashJikaPerlu($this->userModel, $petugas->id_user, $petugas->password, $password);
        $this->mulaiSesi(self::KEY_PETUGAS, $petugas->id_user);
        $this->petugas = $petugas;

        return true;
    }

    public function logout(): void
    {
        $this->session->remove([self::KEY_WARGA, self::KEY_PETUGAS]);
        $this->session->regenerate(true);
        $this->warga   = null;
        $this->petugas = null;
    }

    public function warga(): ?Masyarakat
    {
        $id = $this->session->get(self::KEY_WARGA);

        if ($id === null) {
            return null;
        }

        if ($this->warga?->id_masyarakat !== (int) $id) {
            $this->warga = $this->masyarakatModel->find((int) $id);
        }

        return $this->warga;
    }

    public function petugas(): ?User
    {
        $id = $this->session->get(self::KEY_PETUGAS);

        if ($id === null) {
            return null;
        }

        if ($this->petugas?->id_user !== (int) $id) {
            $this->petugas = $this->userModel->find((int) $id);
        }

        return $this->petugas;
    }

    public function cekPassword(Masyarakat|User $akun, string $password): bool
    {
        return password_verify($password, $akun->password);
    }

    public function segarkan(): void
    {
        $this->warga   = null;
        $this->petugas = null;
    }

    private function cocok(?string $hash, string $password): bool
    {
        $valid = password_verify($password, $hash ?? self::HASH_PALSU);

        return $hash !== null && $valid;
    }

    private function mulaiSesi(string $key, int $id): void
    {
        $this->session->regenerate(true);
        $this->session->remove([self::KEY_WARGA, self::KEY_PETUGAS]);
        $this->session->set($key, $id);
    }

    private function rehashJikaPerlu(MasyarakatModel|UserModel $model, int $id, string $hash, string $password): void
    {
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $model->skipValidation()->update($id, ['password' => password_hash($password, PASSWORD_DEFAULT)]);
            $model->skipValidation(false);
        }
    }
}
