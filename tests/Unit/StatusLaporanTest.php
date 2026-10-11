<?php

namespace Tests\Unit;

use App\Enums\StatusLaporan;
use App\Services\LaporanService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class StatusLaporanTest extends CIUnitTestCase
{
    public function testAlurNormalBergerakSatuLangkah(): void
    {
        $this->assertTrue(StatusLaporan::Baru->bisaBerubahKe(StatusLaporan::Diverifikasi));
        $this->assertTrue(StatusLaporan::Diverifikasi->bisaBerubahKe(StatusLaporan::Valid));
        $this->assertTrue(StatusLaporan::Valid->bisaBerubahKe(StatusLaporan::Diteruskan));
        $this->assertTrue(StatusLaporan::Diteruskan->bisaBerubahKe(StatusLaporan::Selesai));
    }

    public function testTidakValidHanyaDariBaruAtauDiverifikasi(): void
    {
        $this->assertTrue(StatusLaporan::Baru->bisaBerubahKe(StatusLaporan::TidakValid));
        $this->assertTrue(StatusLaporan::Diverifikasi->bisaBerubahKe(StatusLaporan::TidakValid));
        $this->assertFalse(StatusLaporan::Valid->bisaBerubahKe(StatusLaporan::TidakValid));
        $this->assertFalse(StatusLaporan::Diteruskan->bisaBerubahKe(StatusLaporan::TidakValid));
    }

    public function testTidakBisaMelompatiTahap(): void
    {
        $this->assertFalse(StatusLaporan::Baru->bisaBerubahKe(StatusLaporan::Selesai));
        $this->assertFalse(StatusLaporan::Baru->bisaBerubahKe(StatusLaporan::Diteruskan));
        $this->assertFalse(StatusLaporan::Diverifikasi->bisaBerubahKe(StatusLaporan::Selesai));
    }

    public function testStatusAkhirTidakPunyaTransisi(): void
    {
        $this->assertSame([], StatusLaporan::Selesai->transisiBerikutnya());
        $this->assertSame([], StatusLaporan::TidakValid->transisiBerikutnya());
        $this->assertFalse(StatusLaporan::Selesai->bisaBerubahKe(StatusLaporan::Baru));
    }

    public function testHanyaStatusBaruBisaDiubahWarga(): void
    {
        foreach (StatusLaporan::cases() as $status) {
            $this->assertSame($status === StatusLaporan::Baru, $status->bisaDiubahWarga(), $status->value);
        }
    }

    public function testNilaiSesuaiEnumDatabaseLama(): void
    {
        $this->assertSame(
            ['belum_ditanggapi', 'proses', 'valid', 'pengerjaan', 'selesai', 'tidak_valid'],
            array_map(static fn (StatusLaporan $s) => $s->value, StatusLaporan::cases()),
        );
    }

    public function testLabelUntukWargaMemakaiBahasaSederhana(): void
    {
        $this->assertSame('Laporan diterima', StatusLaporan::Baru->labelWarga());
        $this->assertSame('Tidak dapat ditindaklanjuti', StatusLaporan::TidakValid->labelWarga());
        $this->assertSame('tidak-valid', StatusLaporan::TidakValid->slug());
    }

    public function testLabelMengikutiAlurPenerusanLaporan(): void
    {
        $this->assertSame('Diteruskan ke instansi berwenang', StatusLaporan::Diteruskan->labelWarga());
        $this->assertSame('Ditindaklanjuti', StatusLaporan::Selesai->labelWarga());
        $this->assertSame(
            ['Diterima', 'Diverifikasi', 'Terverifikasi', 'Diteruskan', 'Ditindaklanjuti'],
            array_map(static fn (StatusLaporan $s) => $s->labelTahap(), StatusLaporan::alur()),
        );
    }

    public function testHanyaLaporanTerverifikasiYangBisaTampilPublik(): void
    {
        $this->assertFalse(StatusLaporan::Baru->bisaTampilPublik());
        $this->assertFalse(StatusLaporan::Diverifikasi->bisaTampilPublik());
        $this->assertFalse(StatusLaporan::TidakValid->bisaTampilPublik());
        $this->assertTrue(StatusLaporan::Valid->bisaTampilPublik());
        $this->assertTrue(StatusLaporan::Diteruskan->bisaTampilPublik());
        $this->assertTrue(StatusLaporan::Selesai->bisaTampilPublik());
    }

    public function testFormatNomorLaporan(): void
    {
        $this->assertSame('LP-2026-000123', LaporanService::nomorLaporan(123, 2026));
        $this->assertSame('LP-2024-000008', LaporanService::nomorLaporan(8, 2024));
    }
}
