<!-- app/Views/admin/tambah_divisi.php -->

<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>
<!-- tambah_divisi.php -->

<!-- Add this code to display the notification message -->
<?php if (session()->getFlashdata('pesan')) : ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('pesan') ?>
    </div>
<?php endif; ?>

<!-- Your form for adding a new division goes here -->

<h2>Tambah Data Divisi</h2>

<form method="post" action="<?= base_url('topsis/simpanDivisi') ?>">
    <div class="form-group">
        <label for="nama_divisi">Nama Divisi:</label>
        <input type="text" name="nama_divisi" class="form-control" required>
    </div>

    <!-- Tambahkan field lain jika diperlukan -->

    <div class="form-group">
        <button type="submit" class="btn btn-succes">Simpan</button>
    </div>
</form>

<?= $this->endSection() ?>