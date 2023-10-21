<?php

namespace App\Controllers;

use App\Libraries\Notification;
use App\Models\M_karyawan;
use App\Models\M_hasil;
use App\Models\M_divisi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use Carbon\Carbon;
use TCPDF;


class topsis extends BaseController
{
    protected $helpers = ['form', 'date'];
    protected $session = null;
    protected $request = null;
    protected $notif;
    public function __construct()
    {
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->notif = new Notification();
        $this->db = \Config\Database::connect();
        $this->moduser = model('App\Models\M_akun');
    }


    public function index(RequestInterface $request = null)
    {
        //filter login
        if (!isset($_SESSION['admin_logged_in'])) {
            $dataIndex['check'] = "Belum login";
            $this->session->removeTempdata('message');
            return view('admin/login', $dataIndex);
        } else {
            $dataIndex['check'] = "Sedang login";
        }
        // Inisialisasi model M_divisi
        $divisiModel = new M_divisi();
        $data['divisiList'] = $divisiModel->findAll();

        // Mengecek apakah ada pesan
        if ($this->session->has('message')) {
            $data['message'] = $this->session->get('message');
        } else {
            $this->session->removeTempdata('message');
            $data['message'] = "";
        }

        // ===================================
        $filter_divisi = $this->request->getPost('filter_divisi');
        // var_dump($filter_divisi);
        // Ambil data karyawan dari database berdasarkan filter divisi (jika ada)
        $karyawan = new M_karyawan();

        // Check if $filter_divisi is not empty
        if ($filter_divisi) {
            // Use the updated method getKaryawanWithDivisi() with filter
            $data['datapos'] = $karyawan->getKaryawanWithDivisi($filter_divisi);
        } else {
            // Use the updated method getKaryawanWithDivisi() without filter
            $data['datapos'] = $karyawan->getKaryawanWithDivisi();
        }
        // ===================================

        // =================================
        if ($this->request->isAJAX()) {
            $view = view("topsis/index", $data);
            return $this->response->setJSON([
                'html' => $view,
                'data' => $data
            ]);
        } else {
            $this->session->removeTempdata('message');
            return view("topsis/index", $data);
        }
    }

    public function proses()
    {
        if (!isset($_SESSION['admin_logged_in'])) {
            $dataIndex['check'] = "Belum login";
            $this->session->removeTempdata('message');
            return view('admin/login', $dataIndex);
        } else {
            $dataIndex['check'] = "Sedang login";
        }

        // Mengambil nilai bobot kriteria dari inputan form
        $bobot_psikotes = $this->request->getPost('bobot_psikotes');
        $bobot_wawancara = $this->request->getPost('bobot_wawancara');
        $bobot_kesehatan = $this->request->getPost('bobot_kesehatan');
        $bobot_pemberkasan = $this->request->getPost('bobot_pemberkasan');

        // Mengambil nilai filter divisi (jika ada)
        $filter_divisi = $this->request->getPost('filter_divisi');
        //*
        // echo "<pre>";
        // print_r($filter_divisi);
        // echo "</pre>";
        //*/
        // Ambil data karyawan dari database beserta informasi divisi (jika ada filter)
        $karyawan = new M_karyawan();

        if ($filter_divisi) {
            // Gunakan where dan filter_divisi untuk mengambil data karyawan sesuai filter
            $data_karyawan = $karyawan->where('id_divisi', $filter_divisi)->findAll();
        } else {
            $data_karyawan = $karyawan->getKaryawanWithDivisi();
        }

        // Cek apakah data dengan ID karyawan sudah ada di tabel 'hasil'
        $hasilModel = new M_hasil();
        $existingData = $hasilModel->findAll();
        $existingKaryawanIds = array_column($existingData, 'id_karyawan');

        // Lakukan pengecekan data ID karyawan sebelum melakukan perhitungan TOPSIS
        $filteredDataKaryawan = [];
        foreach ($data_karyawan as $karyawan) {
            $id = $karyawan['id'];
            if (!in_array($id, $existingKaryawanIds)) {
                // Data dengan ID karyawan belum ada di tabel 'hasil', tambahkan ke data yang akan diproses
                $filteredDataKaryawan[] = $karyawan;
            }
        }
        // Lanjutkan dengan pemrosesan data karyawan...


        /*
        echo "<pre>";
        print_r($karyawan);
        echo "</pre>";
        //*/


        // Konversi data karyawan menjadi matriks keputusan
        $matriks = array();
        foreach ($data_karyawan as $karyawan) {
            $matriks[$karyawan['id']] = array(
                $karyawan['pemberkasan'],
                $karyawan['wawancara'],
                $karyawan['kesehatan'],
                $karyawan['psikotes']
            );
        }
        // echo "Matriks keputusan";
        // echo "<pre>";
        // print_r($matriks);
        // echo "</pre>";

        // Bobot kriteria
        $bobot = array(
            $bobot_psikotes,
            $bobot_wawancara,
            $bobot_kesehatan,
            $bobot_pemberkasan
        );
        // echo "Nilai bobot";
        // echo "<pre>";
        // print_r($bobot);
        // echo "</pre>";
        $pembagi = array();


        //mengelompokkan nilai setiap kriteria
        $indeks = 0;
        $nilai_kriteria = array();
        foreach ($matriks as $data) {
            for ($i = 0; $i < count($data); $i++) {
                $nilai_kriteria[$i][$indeks] = $data[$i];
            }
            $indeks++;;
        }
        $pembagi = array(); // Inisialisasi $pembagi sebagai array kosong

        //mencari pembagi
        foreach ($nilai_kriteria as $nilai) {
            $jumlah = 0;
            for ($i = 0; $i < count($nilai); $i++) {
                $jumlah += pow($nilai[$i], 2);
            }
            array_push($pembagi, round(sqrt($jumlah), 3));
        }

        /*
        echo "mencari pembagi";
        echo "<br>";
        echo "<pre>";
        print_r($pembagi);
        echo "</pre>";
        //*/

        //normalisasi matriks
        $nilai_normalisasi = array();
        $indeks_data = 0;
        foreach ($matriks as $id => $karyawan) {
            for ($i = 0; $i < count($karyawan); $i++) {
                $nilai_normalisasi[$id][$i] = round(($karyawan[$i] / $pembagi[$i]), 3);
            }
            $indeks_data++;
        }

        /*
        echo "normalisai matrix";
        echo "<br>";
        echo "<pre>";
        print_r($nilai_normalisasi);
        echo "</pre>";
        //*/

        // Menghitung matriks terbobot
        $matriks_terbobot = array();
        foreach ($nilai_normalisasi as $data) {
            $nilai_terbobot = array();
            for ($j = 0; $j < count($data); $j++) {
                $nilai_terbobot[] = round(($data[$j] * $bobot[$j]), 3);
            }
            $matriks_terbobot[] = $nilai_terbobot;
        }

        /*
        echo "matriks terbobot";
        echo "<br>";
        echo "<pre>";
        print_r($matriks_terbobot);
        echo "</pre>";
        //*/

        // Menghitung solusi ideal positif (A+) dan solusi ideal negatif (A-)
        $aplus = array();
        $aminus = array();
        for ($i = 0; $i < count($matriks_terbobot[0]); $i++) {
            $column = array_column($matriks_terbobot, $i);
            $aplus[$i] = max($column);
            $aminus[$i] = min($column);
        }
        /*
        echo "solusi ideal positif A+";
        echo "<br>";
        echo "<pre>";
        print_r($aplus);
        echo "</pre>";
        //*/


        // echo "<br>";
        // echo "solusi ideal negatif A-";
        // echo "<br>";
        // echo "<pre>";
        // print_r($aminus);
        // echo "</pre>";

        // Menghitung jarak antara setiap objek karyawan dengan solusi ideal positif (D+) dan solusi ideal negatif (D-)
        $dplus = array();
        $dminus = array();
        for ($i = 0; $i < count($matriks_terbobot); $i++) {
            $dplus[$i] = 0;
            $dminus[$i] = 0;
            for ($j = 0; $j < count($matriks_terbobot[$i]); $j++) {
                $dplus[$i] += pow($matriks_terbobot[$i][$j] - $aplus[$j], 2);
                $dminus[$i] += pow($matriks_terbobot[$i][$j] - $aminus[$j], 2);
            }
            $dplus[$i] = round(sqrt($dplus[$i]), 3); //
            $dminus[$i] = round(sqrt($dminus[$i]), 3); //
        }

        /*
        echo "jarak antara setiap objek karyawan dengan solusi ideal positif D+";
        echo "<br>";
        echo "<pre>";
        print_r($dplus);
        echo "</pre>";
        //*/

        /*
        echo "<br>";
        echo "jarak antara setiap objek karyawan dengan solusi ideal negatif D-";
        echo "<br>";
        echo "<pre>";
        print_r($dminus);
        echo "</pre>";
        //*/
        // Menghitung skor preferensi (V*)
        $v = array();
        $existingKaryawanIds = array_column($existingData, 'id_karyawan'); // Ambil ID karyawan dari data yang sudah ada di tabel 'hasil'

        // Array untuk melacak notifikasi yang sudah ditambahkan
        $existingNotif = array();

        foreach (array_keys($dplus) as $index) {
            $id = $data_karyawan[$index]['id'];

            if (!in_array($id, $existingKaryawanIds)) {
                $v[$index] = round(($dminus[$index] / ($dplus[$index] + $dminus[$index])), 3);
                $this->session->setFlashdata('success', 'Perhitungan TOPSIS berhasil dilakukan.');
            } else {
                // Pengecekan apakah notifikasi sudah pernah ditambahkan
                if (!isset($existingNotif[$id])) {
                    $existingNotif[$id] = true;

                    // Membuat pesan
                    $message = 'Data karyawan sudah dihitung dan sudah tersedia di database. Klik tautan di bawah ini untuk melihat semua data id yang sudah pernah di hitung.<a href="javascript:void(0);" onclick="showPopup(\'' . implode(',', $existingKaryawanIds) . '\')"><span style="color: green;">Lihat semua data</span></a> ';
                    $this->session->setFlashdata('message', $message);
                }
            }
        }
        // echo "matriks terbobot";
        // echo "<br>";
        // echo "<pre>";
        // print_r($v);
        // echo "</pre>";


        arsort($v);

        $hasil_topsis = array();
        foreach (array_keys($v) as $index) {
            $id = $data_karyawan[$index]['id'];
            $alternatif = $data_karyawan[$index]['nama'];
            $skor_preferensi = $v[$index];
            $id_divisi = $data_karyawan[$index]['id_divisi'];
            $tgl = date('Y-m-d H:i:s');

            // Mendapatkan nama divisi berdasarkan ID divisi
            $model_divisi = new M_divisi();
            $nama_divisi = $model_divisi->find($id_divisi)['nama'];

            // Simpan data hasil perhitungan ke tabel hasil
            $data = array(
                'id_karyawan' => $id,
                'alternatif' => $alternatif,
                'skor_preferensi' => $skor_preferensi,
                'id_divisi' => $id_divisi,
                'tgl' => $tgl, // Tambahkan kolom tgl ke data array
            );
            $this->db->table('hasil')->insert($data);

            // Tambahkan hasil perhitungan ke array $hasil_topsis
            $hasil_topsis[] = array(
                'id_karyawan' => $id,
                'alternatif' => $alternatif,
                'skor_preferensi' => $skor_preferensi,
                'nama_divisi' => $nama_divisi,
                'tgl' => $tgl, // Gunakan variabel $tgl sebagai tgl
            );
        }

        $data['hasil_topsis'] = $hasil_topsis;
        $data['v'] = $v;

        return view('topsis/result', $data);
    }


    public function hasil_topsis()
    {

        if (!isset($_SESSION['admin_logged_in'])) {
            $dataIndex['check'] = "Topsis Belum login";
            $this->session->removeTempdata('message');
            return view('admin/login', $dataIndex);
        } else {
            $dataIndex['check'] = "Sedang login";
        }


        // Inisialisasi model M_hasil
        $model_hasil = new M_hasil();
        //*/
        $divisiModel = new M_divisi();
        $data['divisiList'] = $divisiModel->findAll();
        // Periksa apakah $filter_divisi tidak kosong
        $filter_divisi = $this->request->getPost('filter_divisi');
        // print_r($filter_divisi);
        if ($filter_divisi) {
            //*

            // Filter data berdasarkan divisi yang dipilih (jika ada)
            $id_divisi = (int) $filter_divisi;
            $data['hasil_topsis'] = $model_hasil->getHasilByDivisi($id_divisi);

            /*
        echo "<pre>";
        print_r($filter_divisi);
        echo "</pre>";
        //*/
        } else {
            // Ambil semua data hasil jika tidak ada filter
            $data['hasil_topsis'] = $model_hasil->getHasilWithDivisi();
        }



        // Periksa apakah request merupakan AJAX
        if ($this->request->isAJAX()) {
            $view = view("topsis/hasil_topsis", $data); // Buat view baru untuk menampilkan tabel hasil_topsis
            return $this->response->setJSON([
                'html' => $view,
                'data' => [
                    'hasil_topsis' => $data['hasil_topsis'],
                    'divisiList' => $data['divisiList'], // Kirim juga daftar divisiList
                ],
            ]);
        } else {
            $this->session->removeTempdata('message');
            return view("topsis/hasil_topsis", $data);
        }
    }



    public function tambah()
    {
        // Tampilkan view untuk tambah divisi (buat file tambah_divisi.php)
        return view('topsis/tambah');
    }
    public function simpanDivisi()
    {
        $nama_divisi = $this->request->getPost('nama_divisi'); // Ambil data dari form

        if (empty($nama_divisi)) {
            return redirect()->back()->with('pesan', 'Nama divisi harus diisi.');
        }

        $data = array(
            'nama' => $nama_divisi
        );

        // Gunakan model M_divisi untuk menyimpan data
        $m_divisi = new \App\Models\M_divisi();
        $inserted = $m_divisi->insert($data);

        if ($inserted) {
            return redirect()->to('topsis/tambah')->with('pesan', 'Data divisi berhasil disimpan.');
        } else {
            return redirect()->back()->with('pesan', 'Gagal menyimpan data divisi.');
        }
    }

    // Contoh metode untuk menghapus data berdasarkan divisi dan tanggal
    public function deleteDataByTglAndDivisi()
    {
        $tgl = $this->request->getPost('tgl');
        $id_divisi = $this->request->getPost('id_divisi');

        // Membuat instance model M_hasil
        $model_hasil = new M_hasil();

        // Mulai transaksi
        $model_hasil->transStart();

        // Hapus data berdasarkan tanggal (tgl) dan id_divisi
        $rowsAffected = $model_hasil
            ->where('tgl', $tgl)
            ->where('id_divisi', $id_divisi)
            ->delete();

        // Selesaikan transaksi
        $model_hasil->transComplete();

        // Jika transaksi berhasil dilakukan dan data berhasil dihapus, tampilkan pesan sukses, jika gagal, tampilkan pesan error
        if ($model_hasil->transStatus() === FALSE || $rowsAffected === 0) {
            $model_hasil->transRollback();
            $message = "Gagal menghapus data.";
        } else {
            $model_hasil->transCommit();
            $message = "Data berhasil dihapus.";
        }

        // Set pesan ke flashdata dan kirim sebagai response JSON
        $this->session->setFlashdata('message', $message);
        return $this->response->setJSON(['message' => $message]);
    }
    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (isset($_SESSION['admin_logged_in'])) {
            return redirect()->to(base_url('admin'));
        }

        if (!isset($_SESSION['admin_logged_in']) && (empty($username) || empty($password))) {
            $this->notif->message('Data login tidak lengkap', 'danger');
            return redirect()->to(base_url('admin'));
        }

        if (!isset($_SESSION['admin_logged_in']) && isset($username) && isset($password)) {

            $datauser = ['username' => $username, 'password' => md5($password)];

            $user = $this->moduser->asObject()->where($datauser)->limit(1)->find();
            if (count($user) > 0) {
                if ($user[0]->role !== 'admin') {
                    $this->notif->message('Akun anda bukan Admin, dan tidak memiliki akses ke halaman ini', 'danger');
                    return redirect()->to(base_url('admin'));
                } else {
                    $data_session = array(
                        'admin_user_id' => $user[0]->id_akun,
                        'admin_username' => $user[0]->username,
                        'admin_role' => $user[0]->role,
                        'admin_logged_in' => TRUE
                    );
                    $this->session->set($data_session);
                    return redirect()->to(base_url('admin/dashboard'));
                }
            } else {
                $this->notif->message('Username atau password anda salah', 'danger');
                return redirect()->to(base_url('admin'));
            }
        }
    }

    public function logout()
    {
        $array_items = array('admin_user_id', 'admin_username', 'admin_role', 'admin_logged_in');
        $this->session->remove($array_items);
        return redirect()->to(base_url('admin',));
    }

    public function downloadPDF()
    {
        // Ambil data JSON dari data POST dan dekode menjadi objek
        $pdfData = json_decode($this->request->getPost('pdfData'));

        // Ambil HTML tabel lulus dan tidak lulus dari objek
        $lulusTableHTML = $pdfData->lulusTableHTML;
        $tidakLulusTableHTML = $pdfData->tidakLulusTableHTML;

        // Buat objek TCPDF
        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Tambahkan halaman baru
        $pdf->AddPage();

        // Tambahkan HTML tabel lulus ke PDF
        $pdf->writeHTML($lulusTableHTML);

        // Tambahkan pemisah antara tabel lulus dan tidak lulus jika diperlukan
        $pdf->AddPage();
        $pdf->writeHTML('<p>Data Karyawan | TIDAK LULUS</p>');

        // Tambahkan HTML tabel tidak lulus ke PDF
        $pdf->writeHTML($tidakLulusTableHTML);

        // Tentukan nama file PDF
        $filename = 'output.pdf';

        // Keluarkan hasil PDF ke browser dan set header agar diunduh sebagai file PDF
        $pdf->Output($filename, 'D'); // 'D' untuk mengunduh langsung file

        // Selesai
        exit();
    }
}
