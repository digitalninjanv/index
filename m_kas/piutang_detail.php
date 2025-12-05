<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
        header('Location: ../../index.php');
}

// if( $_SESSION['level_id'] == "1" ){
// }else{
//   echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
// }

include '../../auth.php';

$master = "Piutang";
$dba = "piutang";
$ket = "";
$ketnama = "Silahkan mengisi nama";

$orderId = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
?>

  <?php
  include '../header.php';
  include '../sidebar.php';
  ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Detail Piutang per Order
      </h1>
      <p>piutang adalah pendapatan dari luar yang masih belum di kembalikan atau orang pribadi yang berhutang dan belum di kembalikan</p>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables <?php echo $master; ?></a></li>
        <li class="active">Detail Piutang per Order</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <a href="piutang.php" class="btn btn-default" style="margin-bottom: 10px;">Kembali ke Summary</a>
          <!-- /.box -->

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Order ID: <?php echo $orderId; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php if($orderId <= 0) { ?>
                <div class="alert alert-warning">Order tidak ditemukan. Silakan kembali ke halaman summary.</div>
              <?php } else { ?>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Created</th>
                  <th>Nama</th>
                  <th>Deskripsi <?php echo $ket; ?></th>
                  <th>Nilai</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php
                   $count = 1;
                   $piutang = 0;

                   $sql = $conn->prepare("SELECT * FROM `m_kas` WHERE stat = 4 AND order_id = :order_id");
                   $sql->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                   $sql->execute();
                   while($data=$sql->fetch()) {
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></td>
                  <td><?php echo $data['nama'];?></td>
                  <td><?php echo $data['des'];?></td>
                  <td><?php echo "Rp. ".number_format($data['nilai'], 0). ",-"; ?></td>

                  <td>
                  <button
                      data-id="<?= $data['id'] ?>"
                      data-created_at="<?= $data['created_at'] ?>"
                      data-nama="<?= $data['nama'] ?>"
                      data-des="<?= $data['des']?>"
                      data-nilai="<?= $data['nilai']?>"
                      type="button" class="btn btn-light btn_update" data-toggle="modal">✎</button>

                    <a class="btn btn-light" onclick="return confirm('are you want deleting data')" href="../../controller/<?php echo $dba;?>_controller.php?op=hapus&id=<?php echo $data['id']; ?>">❌</a>

                  <a class="btn btn-light" onclick="return confirm('yakin akan melunaskan?')" href="../../controller/<?php echo $dba;?>_controller.php?op=selesai&id=<?php echo $data['id']; ?>">
                     ✅

                     </a>
                  </td>
                </tr>

                <?php
                $piutang += $data['nilai'];
                $count=$count+1;
                }
                ?>
                <b>Total Piutang = <?= "Rp. ".number_format($piutang,0).",-" ?></b> <br>
                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Created</th>
                  <th>Nama</th>
                  <th>Deskripsi <?php echo $ket; ?></th>
                  <th>Nilai</th>
                  <th>Aksi</th>
                </tr>
                </tfoot>
              </table>
              <?php } ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
