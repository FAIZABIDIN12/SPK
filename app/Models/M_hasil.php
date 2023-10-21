<?php

namespace App\Models;

use CodeIgniter\Model;

class M_hasil extends Model
{
    protected $table      = 'hasil';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $allowedFields = ['alternatif', 'skor_preferensi', 'id_karyawan', 'id_divisi', 'tgl'];

    //Method untuk mendapatkan data hasil dengan filter berdasarkan divisi
    public function getHasilByDivisi($id_divisi)
    {
        $model_divisi = new M_divisi();
        $hasil_topsis = $this->where('id_divisi', $id_divisi)->findAll();

        foreach ($hasil_topsis as &$hasil) {
            $nama_divisi = $model_divisi->find($id_divisi)['nama'];
            $hasil['nama_divisi'] = $nama_divisi;
        }

        return $hasil_topsis;
    }


    // Method untuk mendapatkan data hasil dengan nama divisi
    public function getHasilWithDivisi()
    {
        $model_divisi = new M_divisi();

        $hasil_topsis = $this->findAll();

        foreach ($hasil_topsis as &$hasil) {
            $id_divisi = $hasil['id_divisi'];
            $nama_divisi = $model_divisi->find($id_divisi)['nama'];
            $hasil['nama_divisi'] = $nama_divisi;
        }

        return $hasil_topsis;
    }
}
