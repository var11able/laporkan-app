<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategori extends Migration
{
    private const ATTRIBUTES = ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];
    public const KATEGORI    = [
        ['Suap-menyuap', 'Memberi atau menerima uang atau hadiah agar pejabat berbuat atau tidak berbuat sesuatu.'],
        ['Pungutan liar & pemerasan', 'Pejabat meminta biaya di luar ketentuan, misalnya untuk mengurus layanan atau izin.'],
        ['Gratifikasi', 'Hadiah untuk pejabat yang berhubungan dengan jabatannya dan tidak dilaporkan.'],
        ['Penggelapan dalam jabatan', 'Menggelapkan atau memalsukan uang, barang, atau dokumen yang dikelola karena jabatan.'],
        ['Kecurangan pengadaan barang/jasa', 'Mark-up harga, proyek fiktif, atau pengaturan pemenang tender.'],
        ['Penyalahgunaan wewenang', 'Memakai jabatan untuk menguntungkan diri sendiri atau orang lain sehingga merugikan keuangan negara.'],
        ['Benturan kepentingan', 'Pejabat ikut mengurus proyek atau kegiatan yang juga menguntungkan dirinya atau keluarganya.'],
        ['Lainnya', 'Dugaan korupsi yang tidak termasuk jenis di atas.'],
    ];

    public function up(): void
    {
        $this->forge->addField([
            'id_kategori' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'kategori'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'urutan'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addPrimaryKey('id_kategori');
        $this->forge->addUniqueKey('kategori', 'uq_kategori');
        $this->forge->createTable('kategori', true, self::ATTRIBUTES);

        foreach (self::KATEGORI as $i => [$kategori, $keterangan]) {
            if ($this->db->table('kategori')->where('kategori', $kategori)->countAllResults() === 0) {
                $this->db->table('kategori')->insert(['kategori' => $kategori, 'keterangan' => $keterangan, 'urutan' => $i + 1]);
            }
        }

        $this->forge->addColumn('pengaduan', [
            'id_kategori' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'id_masyarakat'],
        ]);
        $this->db->query('ALTER TABLE `pengaduan` ADD CONSTRAINT `fk_pengaduan_kategori`
            FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('pengaduan', 'fk_pengaduan_kategori');
        $this->forge->dropColumn('pengaduan', 'id_kategori');
        $this->forge->dropTable('kategori', true);
    }
}
