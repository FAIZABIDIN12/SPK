<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<head>
  <!-- ... Tag <link> untuk stylesheets lainnya ... -->
  <style>
    .notification {
      background-color: #f2dede;
      color: #a94442;
      border: 1px solid #ebccd1;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }

    .success-notification {
      background-color: #dff0d8;
      color: #3c763d;
      border-color: #d6e9c6;
    }

    .error-notification {
      background-color: #f2dede;
      color: #a94442;
      border: 1px solid #ebccd1;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }

    .bold-number {
      font-weight: bold;
    }
  </style>

  </style>
</head>
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4 col-md-4">
      <?php $message = session()->getFlashdata('message'); ?>
      <?php $message_type = session()->getFlashdata('message_type'); ?>

      <?php if ($message) : ?>
        <div class="alert <?= strpos($message_type, 'success') !== false ? 'alert-success' : 'alert-danger' ?>">
          <?= $message ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12 col-md-12">
      <div class="card">
        <h4 class="card-header card-header-info"><b>Import Data Karyawan</b></h4>
        <div class="card-body">
          <form method="post" action="<?= base_url('admin/import') ?>" enctype="multipart/form-data">
            <div class="form-group">
              <label for="file">Choose file</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.xls,.csv">
                <label class="custom-file-label" for="file">Choose file</label>
              </div>
            </div>
            <button type="submit" class="btn btn-success float-right btn-sm">Import</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Data Karyawan</h4>
          <p class="card-category">Nilai karyawan</p>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
          <div class="col-md-6">
            <form action="<?= base_url('admin/dashboard') ?>" method="get" class="form-inline">
              <label for="page_size" class="mr-2">Tampilkan:</label>
              <select name="page_size" id="page_size" class="form-control mr-2">
                <option value="10">10</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="1000">1000</option>
              </select>
              <button type="submit" class="btn btn-dark btn-sm">Terapkan</button>
            </form>
          </div>
          <a href="<?= base_url('admin/tambah') ?>" class="btn btn-dark">
            <i class="material-icons">add</i> Tambah Data
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-dark">
              <tr>
                <th class="border-right">No</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Psikotes</th>
                <th>Pemberkasan</th>
                <th>Kesehatan</th>
                <th>Wawancara</th>
                <th>Divisi</th>
                <th>Tanggal | Waktu</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($datapos && is_array($datapos)) {
                $page_size = isset($_GET['page_size']) ? intval($_GET['page_size']) : 10;

                foreach ($datapos as $index => $data) {
                  if ($index >= $page_size) break;

                  // Pastikan nama divisi tidak kosong.
                  $nama_divisi = isset($data['nama_divisi']) ? $data['nama_divisi'] : 'Belum Ada Divisi';
              ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $data['id'] ?></td>
                    <td><?= $data['nama'] ?></td>
                    <td><?= $data['psikotes'] ?></td>
                    <td><?= $data['pemberkasan'] ?></td>
                    <td><?= $data['kesehatan'] ?></td>
                    <td><?= $data['wawancara'] ?></td>
                    <td><?= $nama_divisi ?></td>
                    <td><?= $data['tgl'] ?></td>
                    <td class="td-actions">
                      <a href="<?= base_url('admin/edit/' . $data['id']) ?>" class="btn btn-dark btn-sm" title="Edit">
                        <i class="material-icons">edit</i>
                      </a>
                      <a href="#" data-href="<?= base_url('admin/delete/' . $data['id']) ?>" onclick="confirmToDelete(this)" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="material-icons">close</i>
                      </a>
                    </td>
                  </tr>
              <?php
                }
              }
              ?>
            </tbody>

          </table>
        </div>
        <!-- penghapusan berdasarkan divisi dan tanggal -->
        <form action="<?= base_url('admin/delete_by_divisi_tgl') ?>" method="post" class="form-inline">
          <div class="form-group">
            <label for="delete_divisi" class="mr-2">Divisi:</label>
            <select class="form-control" id="delete_divisi" name="delete_divisi">
              <option value="">Pilih Divisi</option>
              <?php
              $divisiList = array_column($datapos, 'nama_divisi');
              $divisiList = array_unique($divisiList);
              foreach ($divisiList as $divisi) : ?>
                <option value="<?= $divisi ?>"><?= $divisi ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group ml-3">
            <label for="delete_tgl" class="mr-2">Tanggal:</label>
            <select class="form-control" id="delete_tgl" name="delete_tgl">
              <option value="">Pilih Tanggal</option>
              <?php
              $tglList = array_column($datapos, 'tgl');
              $tglList = array_unique($tglList);
              foreach ($tglList as $tgl) : ?>
                <option value="<?= $tgl ?>"><?= $tgl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-danger ml-3">Hapus</button>
        </form>


      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div id="editModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="display: inline;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Hapus data</h4>
      </div>
      <div class="modal-body">
        <div class="fetched-data"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal -->

<div id="confirm-dialog" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h2 class="h2">Apakah anda yakin?</h2>
        <p>Data ini akan dihapus dan tidak dapat dikembalikan</p>
      </div>
      <div class="modal-footer">
        <a href="#" role="button" id="delete-button" class="btn btn-danger">Hapus</a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<script>
  function confirmToDelete(el) {
    $("#delete-button").attr("href", el.dataset.href);
    $("#confirm-dialog").modal('show');
  }

  function showDetail(el) {
    var url_detail = el.dataset.href;
    $.ajax({
      type: 'GET',
      url: url_detail,
      success: function(data) {
        $('.fetched-data').html(data);
        $("#editModal").modal('show');
      }
    });
  }
</script>

</div>
</div>
</div>
</div>
</div>
<?= $this->endSection() ?>