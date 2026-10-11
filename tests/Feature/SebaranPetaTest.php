<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use App\Services\SebaranPeta;
use Config\Services;
use Tests\Support\FeatureTestCase;

/**
 * @internal
 */
final class SebaranPetaTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Services::routes()->loadRoutes();
    }

    public function testSemuaProvinsiMunculMeskipunBelumAdaLaporan(): void
    {
        $peta = (new SebaranPeta())->publik();

        $this->assertCount(38, $peta['provinsi']);
        $this->assertSame(0, $peta['terjangkau']);
        $this->assertSame([0], array_values(array_unique(array_column($peta['provinsi'], 'kelas'))));
    }

    public function testHanyaLaporanPublikYangDihitung(): void
    {
        $petugas = $this->buatPetugas();
        $warga   = $this->buatWarga();
        $this->buatLaporan($warga);
        $tanpaRingkasan = $this->buatLaporan($warga);
        Services::tanggapan(false)->tambah($tanpaRingkasan, $petugas, StatusLaporan::Diverifikasi, 'Diverifikasi.');
        Services::tanggapan(false)->tambah($this->muatUlang($tanpaRingkasan), $petugas, StatusLaporan::Valid, 'Valid.');
        $this->jadikanPublik($this->buatLaporan($warga), $petugas);

        $jatim = $this->provinsi((new SebaranPeta())->publik(), '35');

        $this->assertNull($jatim['jumlah']);
        $this->assertSame('<5', $jatim['label']);
        $this->assertSame(1, $jatim['kelas']);
    }

    public function testJumlahDiBawahAmbangDisamarkanTermasukPenandaBaru(): void
    {
        $petugas = $this->buatPetugas();
        $warga   = $this->buatWarga();

        for ($i = 0; $i < 4; $i++) {
            $this->jadikanPublik($this->buatLaporan($warga), $petugas);
        }

        $jatim = $this->provinsi((new SebaranPeta())->publik(), '35');
        $this->assertNull($jatim['jumlah']);
        $this->assertFalse($jatim['baru']);

        $this->jadikanPublik($this->buatLaporan($warga), $petugas);

        $peta  = (new SebaranPeta())->publik();
        $jatim = $this->provinsi($peta, '35');
        $this->assertSame(5, $jatim['jumlah']);
        $this->assertSame('5', $jatim['label']);
        $this->assertSame(2, $jatim['kelas']);
        $this->assertTrue($jatim['baru']);
        $this->assertSame('35', $peta['provinsi'][0]['kode']);
        $this->assertStringContainsString('Jawa Timur (5)', $peta['ringkasan']);
    }

    public function testPenandaBaruMemakaiTanggalLaporanMenjadiPublik(): void
    {
        $petugas = $this->buatPetugas();
        $warga   = $this->buatWarga();

        for ($i = 0; $i < 5; $i++) {
            $this->jadikanPublik($this->buatLaporan($warga), $petugas);
        }

        $lama = date('Y-m-d H:i:s', strtotime('-90 days'));
        $this->db->table('laporan')->update(['tgl_laporan' => $lama]);
        cache()->delete(config('PetaSebaran')->kunciCache);
        $this->assertTrue($this->provinsi((new SebaranPeta())->publik(), '35')['baru']);

        $this->db->table('tanggapan')->update(['tgl_tanggapan' => $lama]);
        cache()->delete(config('PetaSebaran')->kunciCache);
        $this->assertFalse($this->provinsi((new SebaranPeta())->publik(), '35')['baru']);
    }

    public function testPetugasMelihatJumlahAsliSemuaStatus(): void
    {
        $warga = $this->buatWarga();
        $this->buatLaporan($warga);
        $this->buatLaporan($warga, 'Laporan pungli di Jakarta.', ['id_kabupaten_kota' => $this->idKabupaten('31.71')]);

        $peta = (new SebaranPeta())->petugas();
        $this->assertSame(1, $this->provinsi($peta, '35')['jumlah']);
        $this->assertSame('1', $this->provinsi($peta, '31')['label']);
        $this->assertFalse($peta['samar']);

        $this->assertSame(0, $this->provinsi((new SebaranPeta())->petugas(StatusLaporan::Selesai), '35')['jumlah']);
    }

    public function testBerandaMenampilkanDaftarProvinsiTanpaMembocorkanJumlahKecil(): void
    {
        $this->jadikanPublik($this->buatLaporan($this->buatWarga()), $this->buatPetugas());

        $html = $this->buka('/')->getBody();

        $this->assertStringContainsString('data-peta="bola"', $html);
        $this->assertStringContainsString('Laporan dari seluruh Indonesia', $html);
        $this->assertStringContainsString('Jawa Timur', $html);
        $this->assertStringNotContainsString('&quot;jumlah&quot;:1', $html);
    }

    public function testDasborMenampilkanPetaDenganSaringanStatus(): void
    {
        $this->buatLaporan($this->buatWarga());

        $html = $this->sebagaiPetugas($this->buatPetugas())->buka('petugas?peta=selesai')->getBody();

        $this->assertStringContainsString('data-peta="datar"', $html);
        $this->assertStringContainsString('Jumlah laporan', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*\?peta=selesai" aria-current="page"/', $html);
    }

    public function testBerkasPetaMemuatSemuaKodeProvinsi(): void
    {
        $topo  = json_decode((string) file_get_contents(FCPATH . 'assets/geo/indonesia-provinsi.json'), true);
        $kode  = array_map(static fn (array $g): string => $g['properties']['kode'], $topo['objects']['provinsi']['geometries']);
        $basis = array_column($this->db->table('provinsi')->select('kode')->get()->getResultArray(), 'kode');

        sort($kode);
        sort($basis);
        $this->assertSame($basis, $kode);
    }

    private function provinsi(array $peta, string $kode): array
    {
        foreach ($peta['provinsi'] as $p) {
            if ($p['kode'] === $kode) {
                return $p;
            }
        }

        $this->fail('Provinsi ' . $kode . ' tidak ada');
    }
}
