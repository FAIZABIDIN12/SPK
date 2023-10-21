<!-- Bagian HTML lainnya di halaman hasil_topsis.php -->
<?= $this->extend('admin/admin_layout') ?>
<?= $this->section('content') ?>

<head>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables CSS dan JS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>



  <style>
    /* Define the styles for scores above and below the threshold */
    .above-threshold {
      color: green;
    }

    .below-threshold {
      color: red;
    }
  </style>
</head>

<!-- Bagian HTML lainnya di halaman hasil_topsis.php -->
<?php if ($message) : ?>
  <div class="alert alert-info">
    <?= $message ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header card-header-info">
    <h4 class="card-title">Hasil Perhitungan</h4>
    <p class="card-category">Perangkingan</p>
  </div>
  <div class="container">
    <!-- Add a div to display the result of filtering based on the threshold -->
    <div id="result">
      <!-- Display the result here -->
    </div>

    <div id="hasil_topsis">
      <table id="hasil_table" class="table table-bordered">
        <thead class="thead-dark">
          <tr>
            <th scope="col" data-column="id_karyawan">No</th>
            <th scope="col" data-column="id_karyawan">ID Karyawan</th>
            <th scope="col" data-column="alternatif">Alternatif</th>
            <th scope="col" data-column="skor_preferensi">Skor Preferensi</th>
            <th scope="col" data-column="nama_divisi">Divisi</th>
            <th scope="col" data-column="tgl">Tanggal | Waktu</th>
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
              <td><?= $hasil['tgl'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Add the dropdown filter for tgl and divisi -->
    <div class="container mt-3">
      <h8>Delete Data by Tgl and Divisi</h8>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="filter_tgl">Select Tgl:</label>
            <select class="form-control" id="filter_tgl">
              <option value="">All</option>
              <?php
              $previous_date = null;
              foreach ($hasil_topsis as $hasil) :
                $current_date = $hasil['tgl'];
                if ($current_date != $previous_date) :
              ?>
                  <option><?= $current_date; ?></option>
              <?php
                endif;
                $previous_date = $current_date;
              endforeach;
              ?>
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="filter_divisi">Select Divisi:</label>
            <select class="form-control" id="filter_divisi">
              <option value="">All</option>
              <?php foreach ($divisiList as $divisi) : ?>
                <option value="<?= $divisi['id_divisi']; ?>"><?= $divisi['nama']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <!-- Assuming this is inside a loop generating the table rows -->
            <button class="btn-delete btn-danger btn-sm">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card bg-dark text-white">
      <div class="card-body">
        <h5 class="card-title">Standar Ke Lulusan</h5>
        <label for="threshold_input" class="text-white font-weight-bold">Masukkan Standar Ke Lulusan Skor Preferensi:</label>
        <input type="number" id="threshold_input" step="0.1" min="0" max="1" value="0.6">
        <button id="apply_threshold_btn" class="btn btn-light btn-sm">Terapkan</button>
      </div>
    </div>
  </div>



  <!-- Tabel untuk karyawan yang lulus -->
  <div class="container" id="karyawan_lulus_table_container">
    <div class="text-right">
      <button id="print_pdf_btn" class="btn btn-success btn-sm">
        <i class="material-icons">print</i> Cetak PDF
      </button>
    </div>
    <h6>Data Karyawan | <span class="text-success">Lulus</span></h6>
    <table id="karyawan_lulus_table" class="table table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>No</th>
          <th>ID Karyawan</th>
          <th>Nama Karyawan</th>
          <th>Skor Preferensi</th>
          <th>Status</th>
          <th>Divisi</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data karyawan yang lulus akan ditampilkan di sini -->
        <!-- Anda dapat memasukkan data dinamis di sini menggunakan JavaScript -->
      </tbody>
    </table>
  </div>

  <!-- Tabel untuk karyawan yang tidak lulus -->
  <div class="container" id="karyawan_tidak_lulus_table_container">
    <h6>Data Karyawan | <span class="text-danger">Tidak Lulus</span></h6>
    <table id="karyawan_tidak_lulus_table" class="table table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>No</th>
          <th>ID Karyawan</th>
          <th>Nama Karyawan</th>
          <th>Skor Preferensi</th>
          <th>Deskripsi</th>
          <th>Divisi</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data karyawan yang tidak lulus akan ditampilkan di sini -->
        <!-- Anda dapat memasukkan data dinamis di sini menggunakan JavaScript -->
      </tbody>
    </table>
  </div>

</div>

<!-- jsPDF -->


<script>
  $(document).ready(function() {
    var passThreshold = parseFloat($('#threshold_input').val());

    // Function to update the table and re-render the Skor Preferensi column
    function updateTableWithThreshold() {
      $('#hasil_table').DataTable().columns(3).draw();
      updateCellColors();
    }

    // Function to update the cell colors based on the threshold
    function updateCellColors() {
      $('#hasil_table tbody td[data-column="skor_preferensi"]').each(function() {
        var skorPreferensi = parseFloat($(this).text());
        var aboveThreshold = skorPreferensi >= passThreshold;

        $(this).removeClass('pass fail'); // Clear any previous added classes

        if (aboveThreshold) {
          $(this).addClass('pass').text(skorPreferensi.toFixed(3) + ' | Pass');
        } else {
          $(this).addClass('fail').text(skorPreferensi.toFixed(3) + ' | Fail');
        }
      });
    }

    // Function to update the table with filtered data and split into lulus and tidak lulus
    var passThreshold = 0; // Definisikan variabel passThreshold

    function updateTableWithFilter(selectedDivisi) {
      // Dapatkan nilai passThreshold dari input threshold
      passThreshold = parseFloat($('#threshold_input').val());

      $.ajax({
        url: '<?= site_url("topsis/hasil_topsis") ?>',
        method: 'POST',
        data: {
          filter_divisi: selectedDivisi
        },
        dataType: 'json',
        success: function(response) {
          var data = response.data;
          var htmlLulus = '';
          var htmlTidakLulus = '';
          var noLulus = 1;
          var noTidakLulus = 1;

          if (data && Array.isArray(data.hasil_topsis)) {
            data.hasil_topsis.forEach(function(row) {
              var passColor = 'green';
              var failColor = 'red';
              var aboveThreshold = parseFloat(row.skor_preferensi) >= passThreshold;
              var scoreStyle = 'style="color: ' + (aboveThreshold ? passColor : failColor) + ';"';
              var deskripsi = aboveThreshold ?
                '<span style="color: green;">Karyawan ini dinyatakan lulus.</span>' :
                '<span style="color: red;">Karyawan ini dinyatakan tidak lulus.</span>';

              var rowData = '<tr>' +
                '<td>' + (aboveThreshold ? noLulus++ : noTidakLulus++) + '</td>' +
                '<td>' + row.id_karyawan + '</td>' +
                '<td>' + row.alternatif + '</td>' +
                '<td ' + scoreStyle + '>' + parseFloat(row.skor_preferensi).toFixed(3) + '</td>' +
                '<td>' + deskripsi + '</td>' + // Tambah kolom Deskripsi
                '<td>' + row.nama_divisi + '</td>' +
                '</tr>';

              if (aboveThreshold) {
                htmlLulus += rowData;
              } else {
                htmlTidakLulus += rowData;
              }
            });
          }

          // Update the lulus and tidak lulus tables
          $('#karyawan_lulus_table tbody').html(htmlLulus);
          $('#karyawan_tidak_lulus_table tbody').html(htmlTidakLulus);

          // Gabungkan data tabel yang diperbarui
          var updatedTableData = $('#karyawan_lulus_table').html() + $('#karyawan_tidak_lulus_table').html();

          // Simpan HTML tabel dalam input tersembunyi
          $('#updatedTableData').val(updatedTableData);
        },

        error: function(xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    }

    // Apply the threshold when the button is clicked
    $('#apply_threshold_btn').click(function() {
      var selectedDivisi = $('#filter_divisi').val(); // Ambil nilai filter_divisi
      updateTableWithFilter(selectedDivisi); // Panggil fungsi updateTableWithFilter dengan data filter_divisi
    });

    // Update the table when the input field value changes
    $('#threshold_input').change(function() {
      var selectedDivisi = $('#filter_divisi').val(); // Ambil nilai filter_divisi
      updateTableWithFilter(selectedDivisi); // Panggil fungsi updateTableWithFilter dengan data filter_divisi
    });


    // Apply the filter when the "Filter" button is clicked
    $('#filter_btn').click(function() {
      var selectedDivisi = $('#filter_divisi').val();
      updateTableWithFilter(selectedDivisi);
    });

    // Load the table with the initial threshold value and no filter
    updateTableWithThreshold();
    // updateTableWithFilter();
  });
  // Function to handle data deletion
  function deleteDataByTglAndDivisi(tgl, id_divisi) {
    $.ajax({
      url: '<?= site_url("Topsis/deleteDataByTglAndDivisi") ?>',
      method: 'POST',
      data: {
        tgl: tgl,
        id_divisi: id_divisi
      },
      dataType: 'json',
      success: function(response) {
        if (response.message === "Data berhasil dihapus.") {
          // Reload the table or update it as needed
          // For example, you can trigger a refresh of the DataTable
          $('#hasil_table').DataTable().ajax.reload();
        }
        alert(response.message); // Display a success or error message
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
        alert("Error occurred while deleting data.");
      }
    });
  }

  // Attach a click event handler to the "Delete" button
  $('.btn-delete').click(function() {
    var selectedTgl = $('#filter_tgl').val();
    var selectedDivisi = $('#filter_divisi').val();

    if (selectedTgl && selectedDivisi) {
      if (confirm("Are you sure you want to delete data for the selected date and division?")) {
        deleteDataByTglAndDivisi(selectedTgl, selectedDivisi);
      }
    } else {
      alert("Please select both date and division before deleting.");
    }
  });
</script>
<!-- Tambahkan pustaka pdfmake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>

<script>
  // Fungsi untuk mencetak kedua tabel karyawan (lulus dan tidak lulus) ke PDF menggunakan pdfmake
  function printTablesToPDF() {
    // Impor pdfmake dan font yang diperlukan
    var pdfMake = window.pdfMake;
    var vfsFonts = window.pdfMake.vfs;

    // Inisialisasi dokumen PDF
    var docDefinition = {
      content: [{
          text: 'Tabel Data Karyawan yang Lulus',
          style: 'header'
        }, // Judul tabel karyawan yang lulus
        {
          table: {
            headerRows: 1,
            body: [
              ['No', 'ID Karyawan', 'Nama Karyawan', 'Skor Preferensi', 'Deskripsi', 'Divisi']
            ]
          }
        },
        {
          text: '',
          pageBreak: 'before'
        }, // Pindah ke halaman baru sebelum mencetak tabel berikutnya
        {
          text: 'Tabel Data Karyawan yang Tidak Lulus',
          style: 'header'
        }, // Judul tabel karyawan yang tidak lulus
        {
          table: {
            headerRows: 1,
            body: [
              ['No', 'ID Karyawan', 'Nama Karyawan', 'Skor Preferensi', 'Deskripsi', 'Divisi']
            ]
          }
        }
      ],
      styles: {
        header: {
          fontSize: 16,
          bold: true
        },
        greenText: {
          color: 'green' // Warna teks hijau
        },
        redText: {
          color: 'red' // Warna teks merah
        }
      }
    };

    // Dapatkan data dari tabel karyawan yang lulus
    var lulusTable = document.getElementById("karyawan_lulus_table"); // Ganti dengan ID tabel karyawan yang lulus
    var lulusTableRows = lulusTable.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    // Masukkan data dari tabel karyawan yang lulus ke dalam dokumen PDF
    for (var i = 0; i < lulusTableRows.length; i++) {
      var rowData = [];
      var cells = lulusTableRows[i].getElementsByTagName("td");
      for (var j = 0; j < cells.length; j++) {
        // Cek apakah ini kolom "Deskripsi"
        if (j === 4) {
          var deskripsi = cells[j].textContent;
          rowData.push({
            text: deskripsi,
            style: ['greenText', 'bold']
          });
        } else {
          rowData.push(cells[j].textContent);
        }
      }
      docDefinition.content[1].table.body.push(rowData);
    }

    // Dapatkan data dari tabel karyawan yang tidak lulus
    var tidakLulusTable = document.getElementById("karyawan_tidak_lulus_table"); // Ganti dengan ID tabel karyawan yang tidak lulus
    var tidakLulusTableRows = tidakLulusTable.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    // Masukkan data dari tabel karyawan yang tidak lulus ke dalam dokumen PDF
    for (var i = 0; i < tidakLulusTableRows.length; i++) {
      var rowData = [];
      var cells = tidakLulusTableRows[i].getElementsByTagName("td");
      for (var j = 0; j < cells.length; j++) {
        // Cek apakah ini kolom "Deskripsi"
        if (j === 4) {
          var deskripsi = cells[j].textContent;
          rowData.push({
            text: deskripsi,
            style: ['redText', 'bold']
          });
        } else {
          rowData.push(cells[j].textContent);
        }
      }
      docDefinition.content[4].table.body.push(rowData);
    }

    // Buat dokumen PDF
    pdfMake.createPdf(docDefinition, null, vfsFonts).download("Data_Karyawan.pdf");
  }

  // Menambahkan event click ke tombol cetak PDF
  document.getElementById("print_pdf_btn").addEventListener("click", function() {
    printTablesToPDF();
  });
</script>
<?= $this->endSection() ?>