<?php

namespace App\Controllers;

use App\Libraries\Notification;
use App\Models\M_karyawan;
use App\Models\M_divisi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Admin extends BaseController
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

	public function index()
	{
		if ($this->session->has('message')) {
			$dataIndex['message'] = $this->session->get('message');
		} else {
			$dataIndex['message'] = "";
		};

		if (!isset($_SESSION['admin_logged_in'])) {
			$dataIndex['check'] = "Belum login";
			$this->session->removeTempdata('message');
			return view('admin/login', $dataIndex);
		} else {
			$dataIndex['check'] = "Sedang login";
		}
		$karyawan = new M_karyawan();
		$data['datapos'] = $karyawan->findAll();
		$this->session->removeTempdata('message');
		echo view("admin/dashboard", $data);
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

	// App\Controllers\Admin.php

	public function dashboard()
	{
		// Mengecek apakah ada pesan
		$message = session()->getFlashdata('message') ?? '';

		// Buat object model $karyawan
		$karyawanModel = new M_karyawan();
		$datapos = $karyawanModel->getKaryawanWithDivisi();

		// Buat object model $devisi
		$devisiModel = new M_divisi();
		$divisi = $devisiModel->findAll();

		return view('admin/dashboard', [
			'message' => $message,
			'datapos' => $datapos,
			'divisiModel' => $divisi,
		]);
	}

	public function tambah()
	{
		$divisiModel = new M_divisi();
		$data['divisi'] = $divisiModel->findAll();

		if ($this->request->getMethod() === 'post') {
			// Validasi input data jika diperlukan
			$validation = \Config\Services::validation();
			$validation->setRules([
				'nama' => 'required',
				'pemberkasan' => 'required|integer',
				'psikotes' => 'required|integer',
				'wawancara' => 'required|integer',
				'kesehatan' => 'required|integer',
				'id_divisi' => 'required|integer'
			]);

			if (!$validation->withRequest($this->request)->run()) {
				$errors = $validation->getErrors();
				// Set flash data untuk notifikasi gagal tambah data
				session()->setFlashdata('message', 'Gagal menambahkan data');
				session()->setFlashdata('message_type', 'error');

				// Redirect kembali ke halaman tambah dengan flash data
				return redirect()->back()->withInput()->with('errors', $errors);
			}

			// Ambil data dari form input
			$nama = $this->request->getPost('nama');
			$pemberkasan = $this->request->getPost('pemberkasan');
			$psikotes = $this->request->getPost('psikotes');
			$wawancara = $this->request->getPost('wawancara');
			$kesehatan = $this->request->getPost('kesehatan');
			$idDivisi = $this->request->getPost('id_divisi');

			// Proses simpan data ke tabel karyawan
			$karyawanModel = new M_karyawan();
			$karyawanData = [
				'nama' => $nama,
				'pemberkasan' => $pemberkasan,
				'psikotes' => $psikotes,
				'wawancara' => $wawancara,
				'kesehatan' => $kesehatan,
				'id_divisi' => $idDivisi
			];
			$result = $karyawanModel->insert($karyawanData);

			if ($result) {
				// Set flash data untuk notifikasi berhasil tambah data
				session()->setFlashdata('message', 'Data berhasil ditambahkan');
				session()->setFlashdata('message_type', 'success');
			} else {
				// Set flash data untuk notifikasi gagal tambah data
				session()->setFlashdata('message', 'Gagal menambahkan data');
				session()->setFlashdata('message_type', 'error');
			}

			// Redirect ke halaman dashboard dengan flash data
			return redirect()->to('/admin/dashboard');
		} else {
			// Tampilkan halaman form tambah data
			return view('admin/tambah', $data);
		}
	}


	public function edit($id)
	{
		$karyawanModel = new M_karyawan();
		$divisiModel = new M_divisi();

		// Ambil data karyawan berdasarkan ID
		$karyawan = $karyawanModel->find($id);

		// Jika data karyawan tidak ditemukan, redirect ke halaman dashboard atau tampilkan pesan error
		if (!$karyawan) {
			return redirect()->to('/admin/dashboard')->with('error', 'Data karyawan tidak ditemukan');
		}

		// Ambil semua data divisi
		$data['divisi'] = $divisiModel->findAll();

		if ($this->request->getMethod() === 'post') {
			// Validasi input data jika diperlukan
			$validation = \Config\Services::validation();
			$validation->setRules([
				'nama' => 'required',
				'pemberkasan' => 'required|integer',
				'psikotes' => 'required|integer',
				'wawancara' => 'required|integer',
				'kesehatan' => 'required|integer',
				'id_divisi' => 'required|integer'
			]);

			if (!$validation->withRequest($this->request)->run()) {
				$errors = $validation->getErrors();
				// Set flash data untuk notifikasi gagal edit
				session()->setFlashdata('message', 'Gagal mengupdate data');
				session()->setFlashdata('message_type', 'error');

				// Redirect kembali ke halaman edit dengan flash data
				return redirect()->back()->withInput()->with('errors', $errors);
			}

			// Ambil data dari form input
			$nama = $this->request->getPost('nama');
			$pemberkasan = $this->request->getPost('pemberkasan');
			$psikotes = $this->request->getPost('psikotes');
			$wawancara = $this->request->getPost('wawancara');
			$kesehatan = $this->request->getPost('kesehatan');
			$idDivisi = $this->request->getPost('id_divisi');

			// Perbarui data karyawan
			$karyawanData = [
				'nama' => $nama,
				'pemberkasan' => $pemberkasan,
				'psikotes' => $psikotes,
				'wawancara' => $wawancara,
				'kesehatan' => $kesehatan,
				'id_divisi' => $idDivisi
			];
			$result = $karyawanModel->update($id, $karyawanData);

			if ($result) {
				// Set flash data untuk notifikasi berhasil edit
				session()->setFlashdata('message', 'Data berhasil diperbarui');
				session()->setFlashdata('message_type', 'success');
			} else {
				// Set flash data untuk notifikasi gagal edit
				session()->setFlashdata('message', 'Gagal mengupdate data');
				session()->setFlashdata('message_type', 'error');
			}

			// Redirect ke halaman dashboard dengan flash data
			return redirect()->to('/admin/dashboard');
		} else {
			// Tampilkan halaman form edit data
			$data['karyawan'] = $karyawan;
			return view('admin/edit', $data);
		}
	}


	public function delete($id)
	{
		$karyawan = new M_karyawan();

		$data = $karyawan->find($id);

		if ($data) {
			if ($karyawan->delete($id)) {
				session()->setFlashdata('message', 'Data berhasil dihapus'); // Set flash data
			} else {
				session()->setFlashdata('message', 'Gagal menghapus data'); // Set flash data
			}
		} else {
			session()->setFlashdata('message', 'Data tidak ditemukan'); // Set flash data
		}


		return redirect()->to('/admin/dashboard');
	}


	public function import()
	{
		if ($this->request->getMethod() === 'post') {
			// Ambil file yang diunggah
			$file = $this->request->getFile('file');

			// Validasi file
			if ($file->isValid() && !$file->hasMoved()) {
				// Load spreadsheet
				$spreadsheet = IOFactory::load($file->getPathname());

				// Ambil data dari sheet pertama
				$sheet = $spreadsheet->getActiveSheet();
				$data = $sheet->toArray();

				// Proses impor data
				$karyawan = new M_karyawan();
				$divisi = new M_divisi();
				$successCount = 0; // Untuk menghitung berapa banyak data berhasil diimpor

				foreach ($data as $index => $row) {
					// Skip baris pertama (indeks 0) karena ini adalah header kolom
					if ($index === 0) {
						continue;
					}

					// Pastikan format data sesuai dengan struktur kolom pada file excel
					$nama = $row[0];
					$pemberkasan = $row[2];
					$psikotes = $row[1];
					$wawancara = $row[4];
					$kesehatan = $row[3];
					$namaDivisi = $row[5];
					$tgl = $row[6];

					// Ambil ID divisi berdasarkan nama divisi
					$idDivisi = $divisi->getIDByNama($namaDivisi);

					// Jika ID divisi tidak ditemukan, tambahkan divisi baru ke tabel "divisi"
					if (!$idDivisi) {
						$idDivisi = $divisi->insert(['nama' => $namaDivisi]);
					}

					// Simpan data ke tabel "karyawan" dengan ID divisi yang sesuai
					$result = $karyawan->insert([
						'nama' => $nama,
						'pemberkasan' => $pemberkasan,
						'psikotes' => $psikotes,
						'wawancara' => $wawancara,
						'kesehatan' => $kesehatan,
						'tgl' => $tgl,
						'id_divisi' => $idDivisi,
					]);

					if ($result) {
						$successCount++;
					}
				}

				// Set flash data sesuai dengan hasil impor
				if ($successCount > 0) {
					session()->setFlashdata('message', 'Berhasil mengimpor ' . $successCount . ' data');
					session()->setFlashdata('message_type', 'success'); // Gunakan 'success' untuk warna hijau
				} else {
					session()->setFlashdata('message', 'Gagal mengimpor data, periksa struktur file anda');
					session()->setFlashdata('message_type', 'danger'); // Gunakan 'danger' untuk warna merah
				}
				return redirect()->to('/admin/dashboard');
			} else {
				session()->setFlashdata('message', 'Gagal mengunggah file');
				session()->setFlashdata('message_type', 'danger');
				return redirect()->back();
			}
		}

		// Tampilkan halaman form import
		return view('admin/dashboard');
	}

	// App\Controllers\Admin.php

	public function delete_by_divisi_tgl()
	{
		$divisi = $this->request->getPost('delete_divisi');
		$tgl = $this->request->getPost('delete_tgl');

		// Ambil ID divisi berdasarkan nama divisi
		$divisiModel = new M_divisi();
		$idDivisi = $divisiModel->getIDByNama($divisi);

		if (!$idDivisi) {
			session()->setFlashdata('message', 'Divisi tidak ditemukan');
			session()->setFlashdata('message_type', 'danger');
		} else {
			$karyawanModel = new M_karyawan();

			// Lakukan penghapusan berdasarkan divisi dan tanggal
			$result = $karyawanModel->deleteByDivisiTgl($idDivisi, $tgl);

			if ($result) {
				session()->setFlashdata('message', 'Berhasil menghapus data');
				session()->setFlashdata('message_type', 'success');
			} else {
				session()->setFlashdata('message', 'Gagal menghapus data');
				session()->setFlashdata('message_type', 'danger');
			}
		}

		return redirect()->to('/admin/dashboard');
	}
}
