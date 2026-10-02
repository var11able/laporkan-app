<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Publik\Beranda::index', ['as' => 'beranda']);
$routes->get('laporan', 'Publik\Laporan::index', ['as' => 'laporan.publik']);
$routes->get('laporan/(:segment)', 'Publik\Laporan::show/$1', ['as' => 'laporan.detail']);
$routes->get('lacak', 'Publik\Lacak::index', ['as' => 'lacak']);
$routes->get('saran', 'Publik\Saran::index', ['as' => 'saran']);
$routes->post('saran', 'Publik\Saran::kirim');
$routes->get('kebijakan-privasi', 'Publik\Halaman::privasi', ['as' => 'privasi']);
$routes->get('syarat-ketentuan', 'Publik\Halaman::syarat', ['as' => 'syarat']);
$routes->get('tentang-korupsi', 'Publik\Halaman::tentangKorupsi', ['as' => 'tentang_korupsi']);

$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('masuk', 'Auth\WargaAuth::masuk', ['as' => 'masuk']);
    $routes->post('masuk', 'Auth\WargaAuth::prosesMasuk');
    $routes->get('daftar', 'Auth\WargaAuth::daftar', ['as' => 'daftar']);
    $routes->post('daftar', 'Auth\WargaAuth::prosesDaftar');
    $routes->get('petugas/masuk', 'Auth\PetugasAuth::masuk', ['as' => 'petugas.masuk']);
    $routes->post('petugas/masuk', 'Auth\PetugasAuth::prosesMasuk');
});
$routes->post('keluar', 'Auth\Keluar::index', ['as' => 'keluar']);

$routes->get('api/wilayah/(:num)/kabupaten-kota', 'Api\Wilayah::kabupatenKota/$1', ['as' => 'api.kabupaten_kota']);

$routes->group('warga', ['filter' => 'auth:warga'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Warga\Laporan::index', ['as' => 'warga.beranda']);
    $routes->get('laporan/baru', 'Warga\BuatLaporan::mulai', ['as' => 'warga.laporan.baru']);
    $routes->get('laporan/baru/(:segment)', 'Warga\BuatLaporan::langkah/$1', ['as' => 'warga.laporan.langkah']);
    $routes->post('laporan/baru/(:segment)', 'Warga\BuatLaporan::simpanLangkah/$1');
    $routes->get('laporan/baru/bukti/(:segment)', 'Warga\BuatLaporan::lihatBukti/$1', ['as' => 'warga.laporan.bukti_draf']);
    $routes->get('laporan/terkirim/(:num)', 'Warga\BuatLaporan::terkirim/$1', ['as' => 'warga.laporan.terkirim']);
    $routes->get('laporan/(:num)', 'Warga\Laporan::show/$1', ['as' => 'warga.laporan.detail']);
    $routes->get('laporan/(:num)/ubah', 'Warga\Laporan::edit/$1', ['as' => 'warga.laporan.ubah']);
    $routes->put('laporan/(:num)', 'Warga\Laporan::update/$1');
    $routes->delete('laporan/(:num)', 'Warga\Laporan::delete/$1');
    $routes->get('laporan/(:num)/bukti/(:num)', 'Warga\Laporan::bukti/$1/$2', ['as' => 'warga.laporan.bukti']);
    $routes->get('rekap', 'Warga\Rekap::index', ['as' => 'warga.rekap']);
    $routes->get('rekap/cetak', 'Warga\Rekap::cetak', ['as' => 'warga.rekap.cetak']);
    $routes->get('profil', 'Warga\Profil::index', ['as' => 'warga.profil']);
    $routes->put('profil', 'Warga\Profil::update');
    $routes->get('profil/password', 'Warga\Profil::password', ['as' => 'warga.password']);
    $routes->put('profil/password', 'Warga\Profil::gantiPassword');
});

$routes->group('petugas', ['filter' => 'auth:petugas'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Petugas\Dasbor::index', ['as' => 'petugas.dasbor']);

    $routes->get('laporan', 'Petugas\Laporan::index', ['as' => 'petugas.laporan']);
    $routes->get('laporan/baru', 'Petugas\Laporan::new', ['as' => 'petugas.laporan.baru']);
    $routes->post('laporan', 'Petugas\Laporan::create');
    $routes->post('laporan/status-massal', 'Petugas\Tanggapan::massal', ['as' => 'petugas.laporan.massal']);
    $routes->get('laporan/(:num)', 'Petugas\Laporan::show/$1', ['as' => 'petugas.laporan.detail']);
    $routes->get('laporan/(:num)/ubah', 'Petugas\Laporan::edit/$1', ['as' => 'petugas.laporan.ubah']);
    $routes->put('laporan/(:num)', 'Petugas\Laporan::update/$1');
    $routes->delete('laporan/(:num)', 'Petugas\Laporan::delete/$1', ['filter' => 'role:administrator']);
    $routes->get('laporan/(:num)/bukti/(:num)', 'Petugas\Laporan::bukti/$1/$2', ['as' => 'petugas.laporan.bukti']);
    $routes->put('laporan/(:num)/publik', 'Petugas\Laporan::publik/$1', ['as' => 'petugas.laporan.publik']);
    $routes->post('laporan/(:num)/identitas', 'Petugas\Laporan::identitas/$1', ['as' => 'petugas.laporan.identitas', 'filter' => 'role:administrator']);

    $routes->post('laporan/(:num)/tanggapan', 'Petugas\Tanggapan::create/$1', ['as' => 'petugas.tanggapan.tambah']);
    $routes->get('tanggapan/(:num)/ubah', 'Petugas\Tanggapan::edit/$1', ['as' => 'petugas.tanggapan.ubah']);
    $routes->put('tanggapan/(:num)', 'Petugas\Tanggapan::update/$1');
    $routes->delete('tanggapan/(:num)', 'Petugas\Tanggapan::delete/$1', ['filter' => 'role:administrator']);

    foreach (['provinsi' => 'Provinsi', 'kabupaten-kota' => 'KabupatenKota', 'warga' => 'Warga'] as $segmen => $controller) {
        $nama = str_replace('-', '_', $segmen);
        $routes->get($segmen, "Petugas\\{$controller}::index", ['as' => "petugas.{$nama}"]);
        $routes->get("{$segmen}/baru", "Petugas\\{$controller}::new", ['as' => "petugas.{$nama}.baru"]);
        $routes->post($segmen, "Petugas\\{$controller}::create");
        $routes->get("{$segmen}/(:num)/ubah", "Petugas\\{$controller}::edit/\$1", ['as' => "petugas.{$nama}.ubah"]);
        $routes->put("{$segmen}/(:num)", "Petugas\\{$controller}::update/\$1");
    }
    $routes->delete('provinsi/(:num)', 'Petugas\Provinsi::delete/$1');
    $routes->delete('kabupaten-kota/(:num)', 'Petugas\KabupatenKota::delete/$1');
    $routes->delete('warga/(:num)', 'Petugas\Warga::delete/$1', ['filter' => 'role:administrator']);

    $routes->group('pengguna', ['filter' => 'role:administrator'], static function (RouteCollection $routes): void {
        $routes->get('/', 'Petugas\Pengguna::index', ['as' => 'petugas.pengguna']);
        $routes->get('baru', 'Petugas\Pengguna::new', ['as' => 'petugas.pengguna.baru']);
        $routes->post('/', 'Petugas\Pengguna::create');
        $routes->get('(:num)/ubah', 'Petugas\Pengguna::edit/$1', ['as' => 'petugas.pengguna.ubah']);
        $routes->put('(:num)', 'Petugas\Pengguna::update/$1');
        $routes->delete('(:num)', 'Petugas\Pengguna::delete/$1');
    });

    $routes->get('saran', 'Petugas\Saran::index', ['as' => 'petugas.saran']);
    $routes->get('log', 'Petugas\Log::index', ['as' => 'petugas.log', 'filter' => 'role:administrator']);
    $routes->get('rekap', 'Petugas\Rekap::index', ['as' => 'petugas.rekap']);
    $routes->get('rekap/cetak', 'Petugas\Rekap::cetak', ['as' => 'petugas.rekap.cetak']);

    $routes->get('profil', 'Petugas\Profil::index', ['as' => 'petugas.profil']);
    $routes->put('profil', 'Petugas\Profil::update');
    $routes->get('profil/password', 'Petugas\Profil::password', ['as' => 'petugas.password']);
    $routes->put('profil/password', 'Petugas\Profil::gantiPassword');
});

$routes->get('landing/detailPengaduan/(:num)', 'Publik\Laporan::legacy/$1');
$routes->addRedirect('landing', 'beranda', 301);
$routes->addRedirect('landing/masuk', 'masuk', 301);
$routes->addRedirect('landing/daftar', 'daftar', 301);
$routes->addRedirect('landing/privacyPolicy', 'privasi', 301);
$routes->addRedirect('landing/termsAndConditions', 'syarat', 301);
$routes->addRedirect('auth', 'petugas.masuk', 301);
$routes->addRedirect('pelapor', 'warga.beranda', 301);
$routes->addRedirect('admin', 'petugas.dasbor', 301);
