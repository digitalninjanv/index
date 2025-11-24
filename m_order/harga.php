<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if (isset($_SESSION['email']) == 0) {
    header('Location: ../../index.php');
    exit;
}

if (!in_array($_SESSION['level_id'], ["1", "2"])) {
    echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
    exit;
}

$codx = $_GET['codx'] ?? '';
if (!$codx) {
    echo "<script>alert('Data order tidak ditemukan'); document.location.href='index.php'</script>";
    exit;
}

function sanitize_currency($value)
{
    return (int) preg_replace('/[^0-9]/', '', $value ?? '0');
}

function format_rupiah($value)
{
    return number_format((int) $value, 0, ',', '.');
}

$message = '';
$error = '';

// Ambil data order
$stmtOrder = $conn->prepare("SELECT * FROM m_order WHERE codx = :codx");
$stmtOrder->bindParam(':codx', $codx, PDO::PARAM_STR);
$stmtOrder->execute();
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<script>alert('Order tidak ditemukan'); document.location.href='index.php'</script>";
    exit;
}

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $harga_seal = sanitize_currency($_POST['harga_seal']);
    $harga_truck_muat = sanitize_currency($_POST['harga_truck_muat']);
    $harga_thc_muat = sanitize_currency($_POST['harga_thc_muat']);
    $harga_of = sanitize_currency($_POST['harga_of']);
    $harga_lss = sanitize_currency($_POST['harga_lss']);
    $harga_thc_bongkar = sanitize_currency($_POST['harga_thc_bongkar']);
    $harga_strip = sanitize_currency($_POST['harga_strip']);
    $harga_truck_bongkar = sanitize_currency($_POST['harga_truck_bongkar']);
    $harga_buruh = sanitize_currency($_POST['harga_buruh']);

    $update = $conn->prepare("
        UPDATE m_order SET
            harga_seal = :harga_seal,
            harga_truck_muat = :harga_truck_muat,
            harga_thc_muat = :harga_thc_muat,
            harga_of = :harga_of,
            harga_lss = :harga_lss,
            harga_thc_bongkar = :harga_thc_bongkar,
            harga_strip = :harga_strip,
            harga_truck_bongkar = :harga_truck_bongkar,
            harga_buruh = :harga_buruh
        WHERE codx = :codx
    ");

    $update->bindParam(':harga_seal', $harga_seal, PDO::PARAM_INT);
    $update->bindParam(':harga_truck_muat', $harga_truck_muat, PDO::PARAM_INT);
    $update->bindParam(':harga_thc_muat', $harga_thc_muat, PDO::PARAM_INT);
    $update->bindParam(':harga_of', $harga_of, PDO::PARAM_INT);
    $update->bindParam(':harga_lss', $harga_lss, PDO::PARAM_INT);
    $update->bindParam(':harga_thc_bongkar', $harga_thc_bongkar, PDO::PARAM_INT);
    $update->bindParam(':harga_strip', $harga_strip, PDO::PARAM_INT);
    $update->bindParam(':harga_truck_bongkar', $harga_truck_bongkar, PDO::PARAM_INT);
    $update->bindParam(':harga_buruh', $harga_buruh, PDO::PARAM_INT);
    $update->bindParam(':codx', $codx, PDO::PARAM_STR);

    if ($update->execute()) {
        $message = 'Harga berhasil diperbarui.';
        // Refresh data order
        $stmtOrder->execute();
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = 'Gagal memperbarui harga. Silakan coba lagi.';
    }
}

$master = "Harga";
$judulOrder = htmlspecialchars($order['nama'] ?? '-');
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
      Master Data <?php echo $master; ?>
    </h1>
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
        <a type="button" href="index.php" class="btn btn-primary" >
            Kembali
        </a>

        <div class="box" style="margin-top: 15px;">
          <div class="box-header">
            <h3 class="box-title">Edit Harga - <?php echo $judulOrder; ?></h3>
            <p>Kode Order: <strong><?php echo htmlspecialchars($codx); ?></strong></p>
          </div>

          <div class="box-body">
            <?php if ($message): ?>
              <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
              <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
              <div class="form-group">
                <label>SEAL</label>
                <input type="text" class="form-control" name="harga_seal" value="<?php echo format_rupiah($order['harga_seal']); ?>" required>
              </div>

              <div class="form-group">
                <label>Truck Muat</label>
                <input type="text" class="form-control" name="harga_truck_muat" value="<?php echo format_rupiah($order['harga_truck_muat']); ?>" required>
              </div>

              <div class="form-group">
                <label>THC Muat</label>
                <input type="text" class="form-control" name="harga_thc_muat" value="<?php echo format_rupiah($order['harga_thc_muat']); ?>" required>
              </div>

              <div class="form-group">
                <label>O/F</label>
                <input type="text" class="form-control" name="harga_of" value="<?php echo format_rupiah($order['harga_of']); ?>" required>
              </div>

              <div class="form-group">
                <label>LSS</label>
                <input type="text" class="form-control" name="harga_lss" value="<?php echo format_rupiah($order['harga_lss']); ?>" required>
              </div>

              <div class="form-group">
                <label>THC Bongkar</label>
                <input type="text" class="form-control" name="harga_thc_bongkar" value="<?php echo format_rupiah($order['harga_thc_bongkar']); ?>" required>
              </div>

              <div class="form-group">
                <label>Strip, Lo-lo, dll</label>
                <input type="text" class="form-control" name="harga_strip" value="<?php echo format_rupiah($order['harga_strip']); ?>" required>
              </div>

              <div class="form-group">
                <label>Truck Bongkar</label>
                <input type="text" class="form-control" name="harga_truck_bongkar" value="<?php echo format_rupiah($order['harga_truck_bongkar']); ?>" required>
              </div>

              <div class="form-group">
                <label>Buruh</label>
                <input type="text" class="form-control" name="harga_buruh" value="<?php echo format_rupiah($order['harga_buruh']); ?>" required>
              </div>

              <button type="submit" class="btn btn-success">Simpan Harga</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include '../footer.php'; ?>
