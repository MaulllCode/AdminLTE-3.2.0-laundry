<div>
  <?php
  $tgl = date('Y-m-d h:i:s');
  $seminggu = mktime(0, 0, 0, date("n"), date("j") + 7, date("Y"));
  $batas_waktu = date("Y-m-d h:i:s", $seminggu);

  $kode   = 'PESANAN' . Date('Ymdsi');
  $id_outlet = $_SESSION['id_outlet'];
  $id_user   = $_SESSION['id_user'];
  $id_member = $_GET['id_member'];
  $_SESSION['id_member'] = $id_member;

  $outletq = mysqli_query($kon, "SELECT nama from tb_outlet WHERE id_outlet = " . $id_outlet);
  $outlet = mysqli_fetch_assoc($outletq);

  $memberq = mysqli_query($kon, "SELECT nama from tb_member WHERE id_member = " . $id_member);
  $member = mysqli_fetch_assoc($memberq);

  $paket = mysqli_query($kon, "SELECT * FROM tb_paket WHERE id_outlet = " . $id_outlet);

  // <!-- proses -->
  if (isset($_POST['tambah'])) {
    $kode_invoice = $_POST['kode_invoice'];
    $biaya_tambahan = $_POST['biaya_tambahan'];
    $diskon = $_POST['diskon'];
    $pajak = $_POST['pajak'];

    $sql = "INSERT INTO tb_transaksi VALUES (NULL, '$id_outlet', '$kode_invoice', '$id_member', '$tgl',  '$batas_waktu', NULL, '$biaya_tambahan', '$diskon', '$pajak', 'Baru', 'Belum_dibayar', '$id_user')";

    $result1 = mysqli_query($kon, $sql);

    if ($result1 == 1) {

      $id_paket = $_POST['id_paket'];
      $qty = $_POST['qty'];

      $hargapaketq = mysqli_query($kon, "SELECT * from tb_paket WHERE id_paket = " . $id_paket);
      $hargapaket =  mysqli_fetch_array($hargapaketq);

      $harga = ($hargapaket['harga'] * $qty + $biaya_tambahan) - ($diskon / 100) + ($pajak / 100);
      $kode_invoice;

      $transaksiq = mysqli_query($kon, "SELECT * FROM tb_transaksi WHERE kode_invoice = '" . $kode_invoice . "'");
      $transaksi = mysqli_fetch_assoc($transaksiq);

      $id_transaksi = $transaksi['id_transaksi'];

      $sql1 = "INSERT INTO tb_detail_transaksi VALUES (NULL, '$id_transaksi', '$id_paket', '$qty', '$harga', NULL, NULL)";

      // for ($i = 0; $i < count($id_transaksi); $i++) {
      //   $sql1 = "INSERT INTO tb_detail_transaksi VALUES (NULL, '$id_transaksi[$i]', '$id_paket[$i]', '$qty[$i]', '$harga[$i]', NULL, NULL)";
      // }
      $result = mysqli_query($kon, $sql1);

      if ($result == 1) {
        echo '<script>alert("Transaksi Berhasil ditambahkan"); window.location.href="index.php?page=transaksi_sukses&id_transaksi=' . $id_transaksi, '"</script>';
        // header('location: index.php?page=transaksi_sukses.php&id=' . $id_transaksi);
      } else {
        echo "gagal";
        die("Connection failed: " . mysqli_connect_error());
      }
    }
  }
  // <!-- session -->
  if ($_SESSION["role"] == "Owner") {
    echo '<script>alert("Hanya Admin yang dapat mengakses halaman ini !!!"); window.location.href="index.php"</script>';
  }
  ?>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0">TAMBAH TRANSAKSI</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php?page=data_transaksi">Home</a></li>
            <li class="breadcrumb-item active">Tambah Transaksi</li>
          </ol>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>

  <section class="content">
    <div class="card">
      <div class="row">
        <!-- left column -->
        <div class="card-header col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label>ID OUTLET</label>
                  <input type="text" name="id_outlet" class="form-control" placeholder="ID OUTLET" required value="<?= $outlet['nama']; ?>" readonly>
                </div>
                <div class="form-group">
                  <label>KODE INVOICE</label>
                  <input type="text" name="kode_invoice" value="<?= $kode; ?>" readonly class="form-control" placeholder="KODE INVOICE" required>
                </div>
                <div class="form-group">
                  <label>NAMA MEMBER</label>
                  <input type="text" name="id_member" readonly class="form-control" value="<?= $member['nama']; ?>" placeholder="ID MEMBER" required>
                </div>
                <div class="form-group">
                  <label>PILIHAN PAKET</label>
                  <div class="row">
                    <?php
                    while ($key = mysqli_fetch_array($paket)) {
                    ?>
                      <div class="card col-sm-2 mx-3">
                        <h3><?= $key['jenis'];  ?></h3>
                        <h4>Rp. <?php echo number_format($key['harga']); ?> </h4>
                        <p><a href="index.php?page=keranjang&id_member=<?= $id_member; ?>&jenis=<?php echo $key['jenis']; ?>&aksi=tambah_produk&qty=1" class="btn btn-primary" role="button">Masukan Keranjang</a></p>
                      </div>
                    <?php
                    }
                    ?>
                  </div>
                </div>
                <div class="form-group">
                  <label>BIAYA TAMBAHAN</label>
                  <input type="number" name="biaya_tambahan" value="0" readonly class="form-control" placeholder="BIAYA TAMBAHAN" required>
                </div>
                <div class="form-group">
                  <label>DISKON (%)</label>
                  <input type="number" name="diskon" value="0" readonly class="form-control" placeholder="DISKON" required>
                </div>
                <div class="form-group">
                  <label>PAJAK (%)</label>
                  <input type="number" value="0" name="pajak" readonly class="form-control" placeholder="PAJAK" required>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-primary" name="tambah" title="Simpan Data"><i class="fas fa-save"></i> Simpan</button>
                <button type="reset" class="btn btn-success" title="Reset Data"><i class="fas fa-retweet"></i> Reset</button>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
  </section>
  <!-- /.content-header -->
</div>
