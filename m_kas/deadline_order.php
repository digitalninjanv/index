<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
        header('Location: ../../index.php');
}

include '../../auth.php';

$master = "Deadline Order";
$deadlineToday = strtotime(date('Y-m-d'));

$orderQuery = $conn->prepare("
    SELECT mo.id, mo.codx, mo.nama, mo.invoice_date, mo.discharging_date,
           od.deadline_date, od.notes AS deadline_note
    FROM m_order mo
    LEFT JOIN m_order_deadline od ON od.order_id = mo.id
    ORDER BY mo.id DESC
");
$orderQuery->execute();

function resolveDeadline(array $order): array
{
    $deadline = null;
    $source = '';

    if (!empty($order['deadline_date'])) {
        $deadline = $order['deadline_date'];
        $source = 'deadline_date';
    } elseif (!empty($order['discharging_date'])) {
        $dischargingTimestamp = strtotime($order['discharging_date']);
        if ($dischargingTimestamp !== false) {
            $deadline = date('Y-m-d', $dischargingTimestamp);
            $source = 'discharging_date';
        }
    } elseif (!empty($order['invoice_date'])) {
        $invoiceTimestamp = strtotime($order['invoice_date']);
        if ($invoiceTimestamp !== false) {
            $deadline = date('Y-m-d', $invoiceTimestamp);
            $source = 'invoice_date';
        }
    }

    return [$deadline, $source];
}

function formatDeadlineStatus(?string $deadline, int $todayTimestamp): array
{
    if (!$deadline) {
        return ['Tanggal jatuh tempo belum tersedia', 'label label-default'];
    }

    $deadlineTimestamp = strtotime($deadline);
    if ($deadlineTimestamp === false) {
        return ['Tanggal jatuh tempo belum tersedia', 'label label-default'];
    }

    $daysRemaining = (int) floor(($deadlineTimestamp - $todayTimestamp) / 86400);

    if ($daysRemaining > 0) {
        return ["Sisa {$daysRemaining} hari lagi", 'label label-success'];
    }

    if ($daysRemaining === 0) {
        return ['Jatuh tempo hari ini', 'label label-warning'];
    }

    $lateDays = abs($daysRemaining);
    return ["Terlambat {$lateDays} hari", 'label label-danger'];
}
?>

  <?php
  include '../header.php';
  include '../sidebar.php';
  ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Menu <?php echo $master; ?>
      </h1>
      <p>Menu khusus untuk memantau jatuh tempo per order tanpa mengubah transaksi yang sudah ada.</p>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?php echo $master; ?></li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Ringkasan Jatuh Tempo Order</h3>
            </div>
            <div class="box-body">
              <p class="text-muted">
                Kolom <strong>invoice_date</strong> dan <strong>discharging_date</strong> tetap ditampilkan apa adanya (tipe varchar).
                Perhitungan jatuh tempo menggunakan kolom tanggal baru (DATE) saat tersedia, dengan fallback ke tanggal yang sudah ada.
              </p>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Kode Order</th>
                    <th>Invoice Date (varchar)</th>
                    <th>Discharging Date (varchar)</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Sisa Hari</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $count = 1;
                    while($order = $orderQuery->fetch()) {
                      list($deadline, $deadlineSource) = resolveDeadline($order);
                      list($statusLabel, $statusClass) = formatDeadlineStatus($deadline, $deadlineToday);
                  ?>
                  <tr>
                    <td><?php echo $count; ?></td>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['codx'] ? $order['codx'] : '-'; ?></td>
                    <td><?php echo $order['invoice_date'] ? $order['invoice_date'] : '-'; ?></td>
                    <td><?php echo $order['discharging_date'] ? $order['discharging_date'] : '-'; ?></td>
                    <td>
                      <?php echo $deadline ? date('d-m-Y', strtotime($deadline)) : '-'; ?>
                      <?php if ($deadlineSource) { ?>
                        <br><small class="text-muted">Sumber: <?php echo $deadlineSource; ?></small>
                      <?php } ?>
                    </td>
                    <td><span class="<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                  </tr>
                  <?php
                      $count++;
                    }
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Kode Order</th>
                    <th>Invoice Date (varchar)</th>
                    <th>Discharging Date (varchar)</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Sisa Hari</th>
                  </tr>
                </tfoot>
              </table>
              <p class="text-muted" style="margin-top: 10px;">
                Struktur ini siap disambungkan dengan tabel baru berisi aturan jatuh tempo tanpa mengubah kolom invoice_date dan discharging_date.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

<?php include '../footer.php'; ?>

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
