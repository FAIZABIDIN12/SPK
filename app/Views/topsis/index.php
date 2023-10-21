<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <form method="post" action="<?= base_url('topsis/proses') ?>">
        <div class="text-right mb-3">
          <button type="submit" class="btn btn-success">Proses</button>
        </div>
        <div class="form-row">
          <div class="col-md-3 mb-3">
            <label for="bobot_psikotes">Bobot Psikotes:</label>
            <input type="number" name="bobot_psikotes" step="0.01" value="0.25" class="form-control" required>
          </div>

          <div class="col-md-3 mb-3">
            <label for="bobot_wawancara">Bobot Wawancara:</label>
            <input type="number" name="bobot_wawancara" step="0.01" value="0.25" class="form-control" required>
          </div>

          <div class="col-md-3 mb-3">
            <label for="bobot_kesehatan">Bobot Kesehatan:</label>
            <input type="number" name="bobot_kesehatan" step="0.01" value="0.25" class="form-control" required>
          </div>

          <div class="col-md-3 mb-3">
            <label for="bobot_pemberkasan">Bobot Pemberkasan:</label>
            <input type="number" name="bobot_pemberkasan" step="0.01" value="0.25" class="form-control" required>
          </div>
        </div>



        <div class="form-group col-md-3">
          <label for="filter_divisi">Filter Divisi:</label>
          <select id="filter_divisi" name="filter_divisi" class="form-control">
            <option value="">Semua Divisi</option>
            <?php foreach ($divisiList as $divisi) : ?>
              <option value="<?= $divisi['id_divisi'] ?>" <?= ($divisi['id_divisi'] == $filter_divisi) ? 'selected' : '' ?>>
                <?= $divisi['nama'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-12 col-md-12">
          <a href="<?= base_url('topsis/tambah') ?>">
            <button type="button" class="btn btn-dark pull-right">
              <i class="material-icons">add</i>Tambah Divisi
            </button>
          </a>
        </div>
      </form>

      <table class="table table-hover border">
        <thead class="text-grey border">
          <tr>
            <th class="btn-dark">NO</th>
            <th class="btn-info">Nama</th>
            <th class="btn-info">Pemberkasan</th>
            <th class="btn-info">Psikotes</th>
            <th class="btn-info">Kesehatan</th>
            <th class="btn-info">Wawancara</th>
            <th class="btn-info">Divisi</th>
            <th class="btn-info">Tanggal | Waktu</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          if ($datapos && is_iterable($datapos)) {
            foreach ($datapos as $data) :
              // $divisiModel = new \App\Models\M_divisi(); // Ubah M_divisi menjadi \App\Models\M_divisi
              // $divisi = $divisiModel->find($data['id_divisi']);
          ?>
              <tr>
                <td><?= $no ?></td>
                <td><?= $data['nama'] ?></td>
                <td><?= $data['pemberkasan'] ?></td>
                <td><?= $data['psikotes'] ?></td>
                <td><?= $data['kesehatan'] ?></td>
                <td><?= $data['wawancara'] ?></td>
                <td><?= $data['nama_divisi'] ?></td>
                <td><?= $data['tgl'] ?></td>
              </tr>
          <?php
              $no++;
            endforeach;
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#filter_divisi').change(function() {
      var selectedDivisi = $(this).val();

      $.ajax({
        url: '<?= base_url("topsis/index") ?>', // Memanggil metode index di controller topsis
        method: 'POST',
        data: {
          filter_divisi: selectedDivisi
        },
        dataType: 'json',
        success: function(response) {
          var data = response.data;
          // Tampilkan data filter di sini
          console.log(data);

          var html = '';
          var no = 1;
          if (data && Array.isArray(data.datapos)) {
            data.datapos.forEach(function(row) {
              html += '<tr>' +
                '<td>' + no + '</td>' +
                '<td>' + row.nama + '</td>' +
                '<td>' + row.pemberkasan + '</td>' +
                '<td>' + row.psikotes + '</td>' +
                '<td>' + row.wawancara + '</td>' +
                '<td>' + row.kesehatan + '</td>' +
                '<td>' + row.nama_divisi + '</td>' +
                '<td>' + row.tgl + '</td>' +
                '</tr>';
              no++;
            });
          }

          $('tbody').html(html);
          console.log("success");
        },
        error: function(xhr, status, error) {
          console.log(xhr.responseText);
          console.log("error");
        }

      });
    });
  });
</script>
<?= $this->endSection() ?>