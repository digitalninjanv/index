<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
        header('Location: ../../index.php');
}

include '../../auth.php';

$master = "Deadline Order";

$deadlineTable = 'm_order_deadline';
$deadlineTableExists = false;

$tableCheck = $conn->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
$tableCheck->execute([$deadlineTable]);
if ($tableCheck->fetchColumn() > 0) {
  $deadlineTableExists = true;
}


$orderQuerySql = "SELECT mo.*, md.due_date FROM m_order mo LEFT JOIN {$deadlineTable} md ON md.order_id = mo.id ORDER BY mo.id DESC";

if (!$deadlineTableExists) {
  $orderQuerySql = "SELECT mo.* FROM m_order mo ORDER BY mo.id DESC";
}

$orderQuery = $conn->prepare($orderQuerySql);
$orderQuery->execute();

function ambilTanggalJatuhTempo($data, $deadlineTableExists)
{
  if ($deadlineTableExists && !empty($data['due_date'])) {
    return $data['due_date'];
  }

  // Fallback jika tabel belum ada atau belum diisi: gunakan discharging_date atau invoice_date apa adanya
  $fallbackDate = !empty($data['discharging_date']) ? $data['discharging_date'] : $data['invoice_date'];

  if (!$fallbackDate) {
    return null;
  }

  $fallbackTimestamp = strtotime($fallbackDate);

  if (!$fallbackTimestamp) {
    return null;
  }

  return date('Y-m-d', $fallbackTimestamp);
}

function hitungSisaHari($dueDate)
{
  if (!$dueDate) {
    return ['label' => '-', 'badge' => 'label-default'];
  }

  $dueTimestamp = strtotime($dueDate);

  if (!$dueTimestamp) {
    return ['label' => '-', 'badge' => 'label-default'];
  }

  $todayTimestamp = strtotime(date('Y-m-d'));
  $selisihHari = floor(($dueTimestamp - $todayTimestamp) / 86400);

  if ($selisihHari < 0) {
    $selisihHari = abs($selisihHari);
    return [
      'label' => "Terlambat {$selisihHari} hari",
      'badge' => 'label-danger'
    ];
  }

  return [
    'label' => "Sisa {$selisihHari} hari lagi",
    'badge' => $selisihHari <= 3 ? 'label-warning' : 'label-success'
  ];
}
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

        Data <?php echo $master; ?>
      </h1>
      <i><p>Menu ini digunakan untuk memantau jatuh tempo pembayaran per order.</p></i>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables <?php echo $master; ?></a></li>
        <li class="active">Master Data <?php echo $master; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <!-- /.box -->

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Deadline Pembayaran per Order</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php if (!$deadlineTableExists): ?>
                <div class="alert alert-info">
                  Struktur tabel <strong><?php echo $deadlineTable; ?></strong> belum tersedia. Halaman ini tetap menampilkan data order dan siap dihubungkan ketika tabel baru sudah dikirim oleh client.
                </div>
              <?php endif; ?>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>ID Order</th>
                  <th>Kode Order</th>
                  <th>Invoice Date (varchar)</th>
                  <th>Discharging Date (varchar)</th>
                  <th>Jatuh Tempo</th>
                  <th>Sisa Hari</th>
                </tr>
                </thead>
                <tbody>

                <?php
                   $count = 1;
                   while($data=$orderQuery->fetch()) {
                     $dueDateValue = ambilTanggalJatuhTempo($data, $deadlineTableExists);
                     $dueDateLabel = $dueDateValue ? date('d-m-Y', strtotime($dueDateValue)) : '-';
                     $badgeInfo = hitungSisaHari($dueDateValue);
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo $data['id']; ?></td>
                  <td><?php echo $data['codx'] ? $data['codx'] : '-'; ?></td>
                  <td><?php echo $data['invoice_date'] ? $data['invoice_date'] : '-'; ?></td>
                  <td><?php echo $data['discharging_date'] ? $data['discharging_date'] : '-'; ?></td>
                  <td><?php echo $dueDateLabel; ?></td>
                  <td><span class="label <?php echo $badgeInfo['badge']; ?>"><?php echo $badgeInfo['label']; ?></span></td>
                </tr>

                <?php
                $count=$count+1;
                }
                ?>

                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>ID Order</th>
                  <th>Kode Order</th>
                  <th>Invoice Date (varchar)</th>
                  <th>Discharging Date (varchar)</th>
                  <th>Jatuh Tempo</th>
                  <th>Sisa Hari</th>
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

  <?php
  include '../footer.php';
  ?>

  <script>
  $(function () {
    $('#example1').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'scrollX'     : true,
      'autoWidth'   : false
    })
  })
</script>

</body>
</html>
