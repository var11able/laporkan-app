<?php

namespace Tests\Feature\Services;

use App\Enums\StatusPengaduan;
use App\Exceptions\AturanBisnisException;
use App\Models\TanggapanModel;
use Config\Services;
use Tests\Support\DatabaseTestCase;

final class TanggapanServiceTest extends DatabaseTestCase
{
    public function testTanggapanMengubahStatusLaporan(): void
    {
        $laporan  = $this->buatLaporan($this->buatWarga());
        $operator = $this->buatPetugas();

        Services::tanggapan(false)->tambah($laporan, $operator, StatusPengaduan::Diverifikasi, 'Sedang kami tinjau.');

        $this->assertSame(StatusPengaduan::Diverifikasi, $this->muatUlang($laporan)->status());
        $this->seeInDatabase('log', ['id_user' => $operator->getId()]);
    }

    public function testTransisiIlegalDitolak(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->expectException(AturanBisnisException::class);
        Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusPengaduan::Selesai, 'Langsung selesai.');
    }

    public function testTransisiIlegalTidakMenyimpanApaPun(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        try {
            Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusPengaduan::Selesai, 'x');
        } catch (AturanBisnisException) {
        }

        $this->dontSeeInDatabase('tanggapan', ['id_pengaduan' => $laporan->id_pengaduan]);
        $this->assertSame(StatusPengaduan::Baru, $this->muatUlang($laporan)->status());
    }

    public function testTidakValidWajibMenyertakanAlasan(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());

        $this->expectExceptionMessage('alasan');
        Services::tanggapan(false)->tambah($laporan, $this->buatPetugas(), StatusPengaduan::TidakValid, '   ');
    }

    public function testHapusTanggapanTerakhirMengembalikanStatusSebelumnya(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());
        $admin   = $this->buatPetugas('administrator', 'admin');
        $service = Services::tanggapan(false);

        $service->tambah($laporan, $admin, StatusPengaduan::Diverifikasi, 'Diverifikasi.');
        $terakhir = $service->tambah($this->muatUlang($laporan), $admin, StatusPengaduan::TidakValid, 'Kurang jelas.');

        $status = $service->hapusTerakhir($terakhir, $admin);

        $this->assertSame(StatusPengaduan::Diverifikasi, $status);
        $this->assertSame(StatusPengaduan::Diverifikasi, $this->muatUlang($laporan)->status());
    }

    public function testHapusSatuSatunyaTanggapanKembaliKeBaru(): void
    {
        $laporan  = $this->buatLaporan($this->buatWarga());
        $admin    = $this->buatPetugas('administrator', 'admin');
        $service  = Services::tanggapan(false);
        $terakhir = $service->tambah($laporan, $admin, StatusPengaduan::TidakValid, 'Bukan wewenang kami.');

        $service->hapusTerakhir($terakhir, $admin);

        $this->assertSame(StatusPengaduan::Baru, $this->muatUlang($laporan)->status());
    }

    public function testHanyaTanggapanTerakhirYangBisaDihapus(): void
    {
        $laporan = $this->buatLaporan($this->buatWarga());
        $admin   = $this->buatPetugas('administrator', 'admin');
        $service = Services::tanggapan(false);
        $pertama = $service->tambah($laporan, $admin, StatusPengaduan::Diverifikasi, 'Diverifikasi.');
        $service->tambah($this->muatUlang($laporan), $admin, StatusPengaduan::Valid, 'Valid.');

        $this->expectExceptionMessage('Hanya tanggapan terakhir');
        $service->hapusTerakhir($pertama, $admin);
    }

    public function testOperatorTidakBisaMenghapusTanggapanDanTercatat(): void
    {
        $laporan   = $this->buatLaporan($this->buatWarga());
        $operator  = $this->buatPetugas();
        $tanggapan = Services::tanggapan(false)->tambah($laporan, $operator, StatusPengaduan::Diverifikasi, 'Diverifikasi.');

        try {
            Services::tanggapan(false)->hapusTerakhir($tanggapan, $operator);
            $this->fail('Operator seharusnya ditolak.');
        } catch (AturanBisnisException) {
        }

        $this->seeInDatabase('tanggapan', ['id_tanggapan' => $tanggapan->id_tanggapan]);
        $this->assertSame(1, $this->db->table('log')->like('isi_log', 'Ditolak')->countAllResults());
    }

    public function testOperatorHanyaBisaMengubahTanggapanSendiri(): void
    {
        $laporan   = $this->buatLaporan($this->buatWarga());
        $penulis   = $this->buatPetugas('operator', 'op1');
        $lain      = $this->buatPetugas('operator', 'op2');
        $tanggapan = Services::tanggapan(false)->tambah($laporan, $penulis, StatusPengaduan::Diverifikasi, 'Asli.');

        $this->expectException(AturanBisnisException::class);
        Services::tanggapan(false)->ubah($tanggapan, $lain, 'Diubah orang lain.');
    }

    public function testUbahStatusMassalMelewatiLaporanYangTidakSesuai(): void
    {
        $warga    = $this->buatWarga();
        $operator = $this->buatPetugas();
        $a        = $this->buatLaporan($warga, 'Laporan pertama yang masih baru.');
        $b        = $this->buatLaporan($warga, 'Laporan kedua yang sudah diverifikasi.');
        Services::tanggapan(false)->tambah($b, $operator, StatusPengaduan::Diverifikasi, 'Diverifikasi.');

        $hasil = Services::tanggapan(false)->ubahStatusMassal(
            [$a->id_pengaduan, $b->id_pengaduan],
            $operator,
            StatusPengaduan::Diverifikasi,
            'Sedang kami tinjau.',
        );

        $this->assertSame(1, $hasil['berhasil']);
        $this->assertSame([$b->nomor_laporan], $hasil['dilewati']);
        $this->assertCount(1, (new TanggapanModel())->untukPengaduan($a->id_pengaduan));
    }
}
