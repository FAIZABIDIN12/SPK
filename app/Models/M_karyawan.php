<?php

namespace App\Models;

use CodeIgniter\Model;

class M_karyawan extends Model
{
    protected $table      = 'karyawan';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'nama', 'pemberkasan', 'psikotes',
        'wawancara', 'kesehatan', 'id_divisi', 'tgl'
    ];
    // App\Models\M_karyawan.php

    public function getKaryawanWithDivisi($filter_divisi = null)
    {
        $builder = $this->db->table('karyawan');
        $builder->select('karyawan.*, divisi.nama as nama_divisi');
        $builder->join('divisi', 'divisi.id_divisi = karyawan.id_divisi');

        if ($filter_divisi) {
            $builder->where('divisi.id_divisi', $filter_divisi);
        }

        $query = $builder->get();

        if (!$query) {
            $error = $this->db->error(); // Get the database error
            throw new \RuntimeException('Database error: ' . $error['message']);
        }

        return $query->getResultArray();
    }

    public function deleteByDivisiTgl($idDivisi, $tgl)
    {
        // Lakukan penghapusan berdasarkan divisi dan tanggal
        // Di sini Anda perlu menyesuaikan nama kolom di tabel karyawan
        $this->where('id_divisi', $idDivisi)
            ->where('tgl', $tgl)
            ->delete();

        return $this->db->affectedRows() > 0;
    }
}
