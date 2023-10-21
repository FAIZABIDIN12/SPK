<?php namespace App\Models;

use CodeIgniter\Model;

class M_akun extends Model
{
    protected $table      = 'akun';
    protected $primaryKey = 'id_akun';

    protected $useAutoIncrement = true;
    protected $allowedFields = ['username','password','email','role'];
}