<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-8 col-md-4">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">TOPSIS</h4>
        <p class="card-category">Input Nilai</p>
      </div>
      <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="bmd-label-floating">Nama</label>
                <input type="text" name="nama" class="form-control">
              </div>
              <div class="form-group">
                <label class="bmd-label-floating">Pemberkasan</label>
                <input type="number" name="pemberkasan" class="form-control">
              </div>
              <div class="form-group">
                <label class="bmd-label-floating">Psikotes</label>
                <input type="number" name="psikotes" class="form-control">
              </div>
              <div class="form-group">
                <label class="bmd-label-floating">Wawancara</label>
                <input type="number" name="wawancara" class="form-control">
              </div>
              <div class="form-group">
                <label class="bmd-label-floating">Kesehatan</label>
                <input type="number" name="kesehatan" class="form-control">
              </div>
              <div class="form-group">
                <label for="id_divisi">Divisi</label>
                <select class="form-control" id="id_divisi" name="id_divisi" required>
                  <option value="">Pilih Divisi</option>
                  <?php foreach ($divisi as $row) : ?>
                    <option value="<?= $row['id_divisi'] ?>"><?= $row['nama'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <br>
          <button type="submit" class="btn btn-info">Tambah</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>