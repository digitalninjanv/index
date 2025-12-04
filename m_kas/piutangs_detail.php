<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
        header('Location: ../../index.php');
}

include '../../auth.php';

$master = "Detail Piutang Shipper";
$dba = "piutangs";
$ket = "";
$ketnama = "Silahkan mengisi nama";
$piutang = 0;

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderId <= 0) {
    header('Location: piutangs.php');
    exit;
}

$orderStmt = $conn->prepare("SELECT mo.*, u.nama AS shipper_nama FROM m_order mo LEFT JOIN m_user u ON u.id = mo.shipper_id WHERE mo.id = ?");
$orderStmt->execute([$orderId]);
$orderData = $orderStmt->fetch();

$totalStmt = $conn->prepare("SELECT SUM(nilai) AS total_piutang FROM m_kas WHERE order_id = ? AND shipper_id >= 1 AND stat = 4");
$totalStmt->execute([$orderId]);
$totalRow = $totalStmt->fetch();
$totalPiutang = (int)($totalRow['total_piutang'] ?? 0);

$detailStmt = $conn->prepare("SELECT * FROM m_kas WHERE order_id = ? AND shipper_id >= 1 AND stat = 4 ORDER BY created_at DESC");
$detailStmt->execute([$orderId]);

$statMap = [
    1 => 'Kas Masuk',
    2 => 'Kas Keluar',
    3 => 'Deposit',
    4 => 'Piutang',
    5 => 'Piutang Lunas'
];
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

        <?php echo $master; ?>
      </h1>
      <p>Detail piutang shipper per order.</p>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="piutangs.php">Summary <?php echo $master; ?></a></li>
        <li class="active">Detail Order <?php echo $orderId; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <a class="btn btn-default" href="piutangs.php">&laquo; Kembali ke Summary</a>
          <!-- /.box -->

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Informasi Order</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
                  <p><strong>Kode Order:</strong> <?php echo $orderData['codx'] ? $orderData['codx'] : '-'; ?></p>
                  <p><strong>Nama Order:</strong> <?php echo $orderData['nama'] ? $orderData['nama'] : '-'; ?></p>
                </div>
                <div class="col-md-6">
                  <p><strong>Shipper:</strong> <?php echo $orderData['shipper_nama'] ? $orderData['shipper_nama'] : '-'; ?></p>
                  <p><strong>Total Piutang:</strong> <?php echo "Rp. ".number_format($totalPiutang, 0).",-"; ?></p>
                </div>
              </div>
            </div>
          </div>

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Detail Piutang</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Keterangan</th>
                  <th>Deskripsi</th>
                  <th>Nominal</th>
                  <th>Jenis Transaksi</th>
                </tr>
                </thead>
                <tbody>

                <?php
                   $count = 1;
                   while($data=$detailStmt->fetch()) {
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></td>
                  <td><?php echo $data['nama']; ?></td>
                  <td><?php echo $data['des']; ?></td>
                  <td><?php echo "Rp. ".number_format($data['nilai'], 0). ",-"; ?></td>
                  <td><?php echo $statMap[$data['stat']] ?? '-'; ?></td>
                </tr>

                <?php
                $piutang += $data['nilai'];
                $count=$count+1;
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                  <th colspan="4">Total</th>
                  <th colspan="2"><?php echo "Rp. ".number_format($piutang,0).",-" ?></th>
                </tr>
                </tfoot>
              </table>
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

<?php include '../footer.php'; ?>
