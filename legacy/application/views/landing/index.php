

<!-- <div id="carouselInterval" class="carousel slide mt-5 pt-3" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active" data-interval="3000">
      <img src="<?= base_url('assets/img/img_properties/img_carousel/carousel-1.jpg'); ?>" class="d-block h-500 w-100">
    </div>
    <div class="carousel-item" data-interval="3000">
      <img src="<?= base_url('assets/img/img_properties/img_carousel/carousel-2.jpg'); ?>" class="d-block h-500 w-100">
    </div>
    <div class="carousel-item">
      <img src="<?= base_url('assets/img/img_properties/img_carousel/carousel-3.jpg'); ?>" class="d-block h-500 w-100">
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselInterval" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselInterval" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div> -->

<!-- <div class="jumbotron jumbotron-fluid text-center mt-5" style="background-image: url('<?= base_url('assets/img/img_properties/BINUS-KIJANG-3.jpg'); ?>'); background-size: cover; height: 600px;">
  <div class="container">
    <h1 class="display-4">Fluid jumbotron</h1>
    <p class="lead">This is a modified jumbotron that occupies the entire horizontal space of its parent.</p>
  </div>
</div> -->

<div class="jumbotron jumbotron-fluid text-center mt-5" style="background-image: url('<?= base_url('assets/img/img_properties/merahputih.jpg'); ?>'); background-size: cover; background-position: center;">
  <h1 class="display-4 font-weight-bold">Laporkan app</h1>
  <!-- <p class="lead font-weight-bold">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p> -->
  <hr class="my-4">
  <p class="font-weight-bold">Silahkan memilih untuk Login jika anda sudah mempunyai akun atau daftar jika anda masih belum mempunyai akun.</p>
  <a class="btn btn-primary btn-lg font-weight-bold" href="http://localhost/laporkan-app/landing/masuk"><i class="fas fa-fw fa-sign-in-alt"></i> MASUK</a>
  <a class="btn btn-success btn-lg font-weight-bold" href="http://localhost/laporkan-app/landing/daftar"><i class="fas fa-fw fa-file-signature"></i> DAFTAR</a>
</div>




<header>
	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
	  <div class="container">
	    <a class="navbar-brand" href="<?= base_url(); ?>">
	      <img src="<?= base_url('assets/img/img_properties/favicon-text.png'); ?>" class="d-inline-block align-top img-fluid img-w-100">
	    </a>
	    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
	      <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
	      <div class="navbar-nav">
		  <a class="nav-link page-scroll" href=""><h5 class="my-auto">Beranda</h5></a>
	        <a class="nav-link page-scroll" href="#tentang"><h5 class="my-auto">Tentang</h5></a>
	        <a class="nav-link page-scroll" href="#daftar_laporan"><h5 class="my-auto">Daftar Laporan</h5></a>
	        <a class="nav-link page-scroll" href="#saran"><h5 class="my-auto">Saran</h5></a>
	      </div>
	      <div class="navbar-nav ml-auto">
	        <a class="nav-link btn btn-sm font-weight-bold m-1 btn-login" href="<?= base_url('landing/masuk'); ?>"><i class="fas fa-fw fa-sign-in-alt"></i> MASUK</a>
	        <a class="nav-link btn btn-sm font-weight-bold m-1 btn-danger text-white" href="<?= base_url('landing/daftar'); ?>"><i class="fas fa-fw fa-file-signature"></i> DAFTAR</a>
	      </div>
	    </div>
	  </div>
	</nav>
</header>

<main class="flex-shrink-0">
	<div class="container pb-5">
		<div class="row py-4" id="tentang">
			<div class="col-lg">
				<h4>Tentang</h4>
				<p>Dalam era digital ini, kebutuhan akan pengiriman laporan dan pengaduan secara efisien dan aman semakin meningkat. "Laporkan" hadir sebagai solusi yang memudahkan masyarakat dalam menyampaikan laporan dan pengaduan mereka. Kami menyediakan platform yang tidak hanya sederhana dan user-friendly, tetapi juga memastikan bahwa setiap laporan dapat diakses secara publik dan dipertanggungjawabkan oleh pelapor. Dengan "Laporkan", kami berkomitmen untuk memberikan layanan terbaik dalam pengelolaan pengaduan publik. Setiap laporan yang masuk akan ditangani dengan transparansi dan profesionalisme, sehingga masyarakat dapat merasa yakin bahwa suara mereka didengar dan ditindaklanjuti dengan tepat.</p>
			</div>
		</div>
		<div class="row py-4" id="daftar_laporan">
  <div class="col-lg">
    <h4>Daftar Laporan</h4>
    <p class="text-justify">Berikut adalah daftar pengaduan-pengaduan masyarakat berdasarkan terbaru:</p>
	<div class="table-responsive">
  <table class="table table-bordered" id="table_id">
    <thead class="thead-dark">
      <tr>
        <th class="align-middle">No.</th>
        <th class="align-middle">Tanggal Pengaduan</th>
        <th class="align-middle">Isi Laporan</th>
        <th class="align-middle">Foto</th>
        <th class="align-middle">Status</th>
        <th class="align-middle">Tanggapan</th>
        <th class="align-middle">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; ?>
      <?php foreach ($pengaduan as $dp): ?>
        <?php 
          $getTanggapan = $this->db->order_by('tanggapan.id_tanggapan', 'desc')->get_where('tanggapan', ['id_pengaduan' => $dp['id_pengaduan']])->row_array();
          $status = explode('_', $dp['status_pengaduan']);
          $status = implode(' ', $status);
          $status = ucwords($status);
        ?>
        <tr>
          <td class="align-middle"><?= $i++; ?></td>
          <td class="align-middle"><?= $dp['tgl_pengaduan']; ?></td>
          <td class="align-middle"><?= $dp['isi_laporan']; ?></td>
          <td class="align-middle text-center">
            <a href="<?= base_url('assets/img/img_pengaduan/') . $dp['foto']; ?>" class="enlarge">
              <img src="<?= base_url('assets/img/img_pengaduan/') . $dp['foto']; ?>" class="img-fluid img-w-75-hm-100" alt="<?= $dp['foto']; ?>">
            </a>
          </td>
          <td class="align-middle">
            <?php if ($dp['status_pengaduan'] == 'proses'): ?>
              <button type="button" class="btn btn-sm text-center btn-danger"><i class="fas fa-fw fa-sync"></i> <?= $status; ?></button>
            <?php elseif ($dp['status_pengaduan'] == 'valid'): ?>
              <button type="button" class="btn btn-sm text-center btn-success"><i class="fas fa-fw fa-check"></i> <?= $status; ?></button>
            <?php elseif ($dp['status_pengaduan'] == 'pengerjaan'): ?>
              <button type="button" class="btn btn-sm text-center btn-warning"><i class="fas fa-fw fa-hammer"></i> <?= $status; ?></button>
            <?php elseif ($dp['status_pengaduan'] == 'selesai'): ?>
              <button type="button" class="btn btn-sm text-center btn-primary"><i class="fas fa-fw fa-check-double"></i> <?= $status; ?></button>
            <?php elseif ($dp['status_pengaduan'] == 'tidak_valid'): ?>
              <button type="button" class="btn btn-sm text-center btn-secondary"><i class="fas fa-fw fa-times"></i> <?= $status; ?></button>
            <?php else: ?>
              <button type="button" class="btn btn-sm text-center btn-secondary"><i class="fas fa-fw fa-times"></i> <?= $status; ?></button>
            <?php endif ?>
          </td>
          <td class="align-middle">
            <?php if ($getTanggapan): ?>
              <?php if ($getTanggapan['status_tanggapan'] == 'proses'): ?>
                <p><?= $getTanggapan['isi_tanggapan']; ?></p>
              <?php elseif ($getTanggapan['status_tanggapan'] == 'valid'): ?>
                <p><?= $getTanggapan['isi_tanggapan']; ?></p>
              <?php elseif ($getTanggapan['status_tanggapan'] == 'pengerjaan'): ?>
                <p><?= $getTanggapan['isi_tanggapan']; ?></p>
              <?php elseif ($getTanggapan['status_tanggapan'] == 'selesai'): ?>
                <p><?= $getTanggapan['isi_tanggapan']; ?></p>
              <?php elseif ($getTanggapan['status_tanggapan'] == 'tidak_valid'): ?>
                <p><?= $getTanggapan['isi_tanggapan']; ?></p>
              <?php endif ?>
            <?php else: ?>
              <p>-</p>
            <?php endif ?>
          </td>
          <td class="align-middle text-center">
            <a href="<?= base_url('landing/detailPengaduan/' . $dp['id_pengaduan']); ?>" class="btn btn-sm btn-info m-1"><i class="fas fa-fw fa-align-justify"></i> Detail</a>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

  </div>
</div>

		<div class="row py-4 justify-content-center" id="saran">
			<div class="col-lg-6 my-2">
				<div class="border p-3 rounded">
					<h4>Saran</h4>
					<p>Berikan saran terbaik Anda agar aplikasi Lapor Pak bekerja lebih baik lagi.</p>
					<form action="<?= base_url('landing/saran'); ?>" method="post">
						<div class="form-group">
							<label for="nama"><i class="fas fa-fw fa-id-card"></i> Nama Lengkap</label>
							<input type="text" id="nama" class="form-control <?= (form_error('nama')) ? 'is-invalid' : ''; ?>" name="nama" required value="<?= set_value('nama'); ?>">
							<div class="invalid-feedback">
			          <?= form_error('nama'); ?>
			        </div>
						</div>
						<div class="form-group">
							<label for="no_telepon"><i class="fas fa-fw fa-phone"></i> No. Telepon</label>
							<input type="number" id="no_telepon" class="form-control <?= (form_error('no_telepon')) ? 'is-invalid' : ''; ?>" name="no_telepon" required placeholder="contoh: 08123456789" value="<?= set_value('no_telepon'); ?>">
							<div class="invalid-feedback">
	              <?= form_error('no_telepon'); ?>
	            </div>
						</div>
						<div class="form-group">
							<label for="alamat"><i class="fas fa-fw fa-map-marker-alt"></i> Alamat</label>
							<textarea id="alamat" class="form-control <?= (form_error('alamat')) ? 'is-invalid' : ''; ?>" name="alamat" required><?= set_value('alamat'); ?></textarea>
							<div class="invalid-feedback">
            		<?= form_error('alamat'); ?>
          		</div>
						</div>
						<div class="form-group">
							<label for="saran"><i class="fas fa-fw fa-lightbulb"></i> Saran</label>
							<textarea id="saran" class="form-control <?= (form_error('saran')) ? 'is-invalid' : ''; ?>" name="saran" required><?= set_value('saran'); ?></textarea>
							<div class="invalid-feedback">
            		<?= form_error('saran'); ?>
        			</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" class="btn btn-danger"><i class="fas fa-fw fa-paper-plane"></i> Kirim</button>
						</div>
					</form>
				</div>
			</div>
			<!-- <div class="col-lg-6 my-2">
				<div class="border p-3 rounded">
					<h4>Peta Kabupaten Bondowoso</h4>
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2282.8707509948963!2d113.82174085889423!3d-7.91426281243188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6dcdcd914c45b%3A0x2b876ee550fd7d07!2sKantor%20Bupati%20Bondowoso!5e0!3m2!1sid!2sid!4v1685868331143!5m2!1sid!2sid" style="border:0; width: 100%; height: 33.125rem;" allowfullscreen="" loading="lazy"></iframe>
				</div>
			</div> -->
		</div>
	</div>
</main>