<?php

namespace Tests\Unit;

use App\Enums\StatusPengaduan;
use App\Services\PengaduanService;
use CodeIgniter\Test\CIUnitTestCase;

final class StatusPengaduanTest extends CIUnitTestCase
{
    public function testAlurNormalBergerakSatuLangkah(): void
    {
        $this->assertTrue(StatusPengaduan::Baru->bisaBerubahKe(StatusPengaduan::Diverifikasi));
        $this->assertTrue(StatusPengaduan::Diverifikasi->bisaBerubahKe(StatusPengaduan::Valid));
        $this->assertTrue(StatusPengaduan::Valid->bisaBerubahKe(StatusPengaduan::Diteruskan));
        $this->assertTrue(StatusPengaduan::Diteruskan->bisaBerubahKe(StatusPengaduan::Selesai));
    }

    public function testTidakValidHanyaDariBaruAtauDiverifikasi(): void
    {
        $this->assertTrue(StatusPengaduan::Baru->bisaBerubahKe(StatusPengaduan::TidakValid));
        $this->assertTrue(StatusPengaduan::Diverifikasi->bisaBerubahKe(StatusPengaduan::TidakValid));
        $this->assertFalse(StatusPengaduan::Valid->bisaBerubahKe(StatusPengaduan::TidakValid));
        $this->assertFalse(StatusPengaduan::Diteruskan->bisaBerubahKe(StatusPengaduan::TidakValid));
    }

    public function testTidakBisaMelompatiTahap(): void
    {
        $this->assertFalse(StatusPengaduan::Baru->bisaBerubahKe(StatusPengaduan::Selesai));
        $this->assertFalse(StatusPengaduan::Baru->bisaBerubahKe(StatusPengaduan::Diteruskan));
        $this->assertFalse(StatusPengaduan::Diverifikasi->bisaBerubahKe(StatusPengaduan::Selesai));
    }

    public function testStatusAkhirTidakPunyaTransisi(): void
    {
        $this->assertSame([], StatusPengaduan::Selesai->transisiBerikutnya());
        $this->assertSame([], StatusPengaduan::TidakValid->transisiBerikutnya());
        $this->assertFalse(StatusPengaduan::Selesai->bisaBerubahKe(StatusPengaduan::Baru));
    }

    public function testHanyaStatusBaruBisaDiubahWarga(): void
    {
        foreach (StatusPengaduan::cases() as $status) {
            $this->assertSame($status === StatusPengaduan::Baru, $status->bisaDiubahWarga(), $status->value);
        }
    }

    public function testNilaiSesuaiEnumDatabaseLama(): void
    {
        $this->assertSame(
            ['belum_ditanggapi', 'proses', 'valid', 'pengerjaan', 'selesai', 'tidak_valid'],
            array_map(static fn (StatusPengaduan $s) => $s->value, StatusPengaduan::cases()),
        );
    }

    public function testLabelUntukWargaMemakaiBahasaSederhana(): void
    {
        $this->assertSame('Laporan diterima', StatusPengaduan::Baru->labelWarga());
        $this->assertSame('Tidak dapat ditindaklanjuti', StatusPengaduan::TidakValid->labelWarga());
        $this->assertSame('tidak-valid', StatusPengaduan::TidakValid->slug());
    }

    public function testLabelMengikutiAlurPenerusanLaporan(): void
    {
        $this->assertSame('Diteruskan ke instansi berwenang', StatusPengaduan::Diteruskan->labelWarga());
        $this->assertSame('Ditindaklanjuti', StatusPengaduan::Selesai->labelWarga());
        $this->assertSame(
            ['Diterima', 'Diverifikasi', 'Terverifikasi', 'Diteruskan', 'Ditindaklanjuti'],
            array_map(static fn (StatusPengaduan $s) => $s->labelTahap(), StatusPengaduan::alur()),
        );
    }

    public function testHanyaLaporanTerverifikasiYangBisaTampilPublik(): void
    {
        $this->assertFalse(StatusPengaduan::Baru->bisaTampilPublik());
        $this->assertFalse(StatusPengaduan::Diverifikasi->bisaTampilPublik());
        $this->assertFalse(StatusPengaduan::TidakValid->bisaTampilPublik());
        $this->assertTrue(StatusPengaduan::Valid->bisaTampilPublik());
        $this->assertTrue(StatusPengaduan::Diteruskan->bisaTampilPublik());
        $this->assertTrue(StatusPengaduan::Selesai->bisaTampilPublik());
    }

    public function testFormatNomorLaporan(): void
    {
        $this->assertSame('LP-2026-000123', PengaduanService::nomorLaporan(123, 2026));
        $this->assertSame('LP-2024-000008', PengaduanService::nomorLaporan(8, 2024));
    }
}
