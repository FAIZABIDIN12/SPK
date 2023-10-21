<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header card-header-info">
                <h4 class="card-title">Edit Data Karyawan</h4>
                <p class="card-category">Ubah informasi karyawan</p>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama" class="bmd-label-floating">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= $karyawan['nama'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="pemberkasan" class="bmd-label-floating">Pemberkasan</label>
                        <input type="number" name="pemberkasan" class="form-control" value="<?= $karyawan['pemberkasan'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="psikotes" class="bmd-label-floating">Psikotes</label>
                        <input type="number" name="psikotes" class="form-control" value="<?= $karyawan['psikotes'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="wawancara" class="bmd-label-floating">Wawancara</label>
                        <input type="number" name="wawancara" class="form-control" value="<?= $karyawan['wawancara'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="kesehatan" class="bmd-label-floating">Kesehatan</label>
                        <input type="number" name="kesehatan" class="form-control" value="<?= $karyawan['kesehatan'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="id_divisi">Divisi</label>
                        <select class="form-control" id="id_divisi" name="id_divisi" required>
                            <option value="">Pilih Divisi</option>
                            <?php foreach ($divisi as $row) : ?>
                                <option value="<?= $row['id_divisi'] ?>" <?= ($karyawan['id_divisi'] == $row['id_divisi']) ? 'selected' : '' ?>><?= $row['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>