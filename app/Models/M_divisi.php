<?php

namespace App\Models;

use CodeIgniter\Model;

class M_divisi extends Model
{
    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';
    protected $allowedFields = ['nama'];

    // Tambahkan fungsi atau metode lain yang Anda butuhkan di sini
    public function getIDByNama($namaDivisi)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id_divisi');
        $builder->where('nama', $namaDivisi);
        $query = $builder->get()->getRow();

        if ($query) {
            return $query->id_divisi;
        }

        return null;
    }
}
