<?= $this->section('content') ?>
<div class="col-lg-12 col-md-12">
    <?= $message ?>
</div>
<div class="col-lg-4 col-md-4">
    <div class="card">
        <div class="card-header card-header-info">
            <h4 class="card-title">TOPSIS</h4>
            <p class="card-category">Input Nilai Karyawan</p>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">

                <thead class="strong">
                    <th class="">No</th>
                    <th>Nama</th>
                    <th>Telpon</th>
                    <th>Alamat</th>
                    <th>Tanggal</th>
                    <th>Laporan</th>
                    <th>Aksi</th>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($datapos as $data) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $data['nama'] ?></td>
                            <td><?= $data['pemberkasan'] ?></td>
                            <td><?= $data['psikotes'] ?></td>
                            <td><?= $data['wawancara'] ?></td>
                            <td><?= $data['kesehatan'] ?></td>
                            <td class="td-actions">
                                <!-- <td><?= $data['jenis_lap'] ?></td> -->


                                <!-- <a href="<?= base_url('topsis/detail/' . $data['no']) ?>">
                                    <button type="button" rel="tooltip" title="detail" class="btn btn-dark btn-link btn-sm">
                                        <i class="material-icons">article</i>
                                    </button>
                                </a>
                                <a href="<?= base_url('topsis/tambah/' . $data['no']) ?>">
                                    <button type="button" rel="tooltip" title="detail" class="btn btn-dark btn-link btn-sm">
                                        <i class="material-icons">add</i>
                                    </button>
                                </a>
                                <a href="#" data-href="<?= base_url('topsis/delete/' . $data['no']) ?>" onclick="confirmToDelete(this)">
                                    <button type="button" rel="tooltip" title="Hapus" class="btn btn-danger btn-link btn-sm">
                                        <i class="material-icons">close</i>
                                    </button>
                                </a> -->
                            </td>
                        </tr>

                    <?php
                        $no++;
                    endforeach
                    ?>
                </tbody>
            </table>

            <!-- Modal -->
            <div id="editModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header" style="display: inline;">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Detail lapor</h4>
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
                            <h2 class="h2">Apakah Anda yakin?</h2>
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
                    //$("#editModal").modal('show');
                    var url_detail = el.dataset.href;
                    //alert(url_detail);
                    $.ajax({
                        type: 'GET',
                        url: url_detail,
                        success: function(data) {
                            //alert('successful');
                            //menampilkan data ke dalam modal
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
<?= $this->endSection() ?> -->