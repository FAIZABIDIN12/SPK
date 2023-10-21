<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Bagian HTML lainnya di halaman hasil_topsis.php -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('message')) : ?>
    <div class="alert alert-info">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header card-header-info">
        <h4 class="card-title">Hasil Perhitungan TOPSIS</h4>
        <p class="card-category">Perangkingan</p>
    </div>
    <div class="container">
        <?php if ($message) : ?>
            <div class="alert alert-info">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php
        // Mengurutkan array hasil_topsis berdasarkan skor preferensi secara descending
        usort($hasil_topsis, function ($a, $b) {
            return $b['skor_preferensi'] <=> $a['skor_preferensi'];
        });
        ?>

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">ID Karyawan</th>
                    <th scope="col">Alternatif</th>
                    <th scope="col">Skor Preferensi</th>
                    <th scope="col">Divisi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($hasil_topsis as $hasil) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $hasil['id_karyawan'] ?></td>
                        <td><?= $hasil['alternatif'] ?></td>
                        <td><?= number_format($hasil['skor_preferensi'], 3) ?></td>
                        <td><?= $hasil['nama_divisi'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-right mt-3">
            <a href="<?= base_url('topsis/hasil_topsis') ?>" class="btn btn-dark">
                <i class="material-icons">book</i> Lihat Semua Data
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Pastikan Anda mengganti versi perpustakaan jika diperlukan -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Pastikan Anda mengganti versi perpustakaan jika diperlukan -->

<script>
    function showPopup(ids) {
        var idArray = ids.split(',');
        var columns = 10; // Jumlah kolom per baris
        var popupContent = '<div><strong>ID Karyawan yang Sudah Pernah Dilakukan Perhitungan:</strong><br><br>';
        popupContent += '<table><tr>';

        for (var i = 0; i < idArray.length; i++) {
            if (i > 0 && i % columns === 0) {
                popupContent += '</tr><tr>';
            }
            popupContent += '<td>ID: ' + idArray[i] + '</td>';
        }

        popupContent += '</tr></table></div>';

        // Tampilkan popup dengan SweetAlert2
        Swal.fire({
            title: 'Data dalam Database',
            html: popupContent,
            icon: 'info',
            confirmButtonText: 'Tutup',
            showCloseButton: true, // Tampilkan tombol close
        });
    }
</script>







<?= $this->endSection() ?>