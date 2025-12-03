<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if (isset($_SESSION['email']) == 0) {
    header('Location: ../../index.php');
    exit;
}

// if (!in_array($_SESSION['level_id'], ["1", "2"])) {
//     echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
//     exit;
// }

include '../../auth.php';

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

// ... (Kode update database yang sudah ada biarkan saja) ...



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
    
    // TAMBAHAN BARU:
    $harga_inv = sanitize_currency($_POST['harga_inv']); 

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
            harga_buruh = :harga_buruh,
            harga_inv = :harga_inv  
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
    
    // TAMBAHAN BIND BARU:
    $update->bindParam(':harga_inv', $harga_inv, PDO::PARAM_INT);
    
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

// HITUNG TOTAL BIAYA AWAL (PHP)
$total_biaya_awal = 
    $order['harga_seal'] + 
    $order['harga_truck_muat'] + 
    $order['harga_thc_muat'] + 
    $order['harga_of'] + 
    $order['harga_lss'] + 
    $order['harga_thc_bongkar'] + 
    $order['harga_strip'] + 
    $order['harga_truck_bongkar'] + 
    $order['harga_buruh'];

$harga_inv_awal = $order['harga_inv'];
$profit_awal = $harga_inv_awal - $total_biaya_awal;

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

    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-4">
            <div class="callout callout-warning" style="background-color: #f39c12 !important; color: white;">
                <h4>Total Biaya</h4>
                <h3 id="info_total_biaya" style="margin-top: 0; font-weight: bold;">
                    Rp. <?php echo format_rupiah($total_biaya_awal); ?>
                </h3>
                <p>Total Pengeluaran Operasional</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="callout callout-info" style="background-color: #00c0ef !important; color: white;">
                <h4>Harga (Invoice)</h4>
                <h3 id="info_harga_inv" style="margin-top: 0; font-weight: bold;">
                    Rp. <?php echo format_rupiah($harga_inv_awal); ?>
                </h3>
                <p>Nilai Tagihan ke Customer</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="callout callout-success" style="background-color: #00a65a !important; color: white;">
                <h4>Profit</h4>
                <h3 id="info_profit" style="margin-top: 0; font-weight: bold;">
                    Rp. <?php echo format_rupiah($profit_awal); ?>
                </h3>
                <p>Keuntungan Bersih</p>
            </div>
        </div>
    </div>
    <hr>

    <form method="POST">
        <div class="row">
            <div class="col-md-6">
                 <div class="form-group">
                    <label>SEAL : <span id="view_seal" class="text-green">Rp. <?php echo format_rupiah($order['harga_seal']); ?></span></label>
                    <input type="number" id="val_seal" class="form-control hitung-biaya" name="harga_seal" value="<?php echo $order['harga_seal']; ?>" onkeyup="updateUI(this.value, 'view_seal')" required>
                </div>
                
                <div class="form-group">
                    <label>Truck Muat : <span id="view_truck_muat" class="text-green">Rp. <?php echo format_rupiah($order['harga_truck_muat']); ?></span></label>
                    <input type="number" id="val_truck_muat" class="form-control hitung-biaya" name="harga_truck_muat" value="<?php echo $order['harga_truck_muat']; ?>" onkeyup="updateUI(this.value, 'view_truck_muat')" required>
                </div>

                <div class="form-group">
                    <label>THC Muat : <span id="view_thc_muat" class="text-green">Rp. <?php echo format_rupiah($order['harga_thc_muat']); ?></span></label>
                    <input type="number" id="val_thc_muat" class="form-control hitung-biaya" name="harga_thc_muat" value="<?php echo $order['harga_thc_muat']; ?>" onkeyup="updateUI(this.value, 'view_thc_muat')" required>
                </div>

                <div class="form-group">
                    <label>O/F : <span id="view_of" class="text-green">Rp. <?php echo format_rupiah($order['harga_of']); ?></span></label>
                    <input type="number" id="val_of" class="form-control hitung-biaya" name="harga_of" value="<?php echo $order['harga_of']; ?>" onkeyup="updateUI(this.value, 'view_of')" required>
                </div>

                <div class="form-group">
                    <label>LSS : <span id="view_lss" class="text-green">Rp. <?php echo format_rupiah($order['harga_lss']); ?></span></label>
                    <input type="number" id="val_lss" class="form-control hitung-biaya" name="harga_lss" value="<?php echo $order['harga_lss']; ?>" onkeyup="updateUI(this.value, 'view_lss')" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>THC Bongkar : <span id="view_thc_bongkar" class="text-green">Rp. <?php echo format_rupiah($order['harga_thc_bongkar']); ?></span></label>
                    <input type="number" id="val_thc_bongkar" class="form-control hitung-biaya" name="harga_thc_bongkar" value="<?php echo $order['harga_thc_bongkar']; ?>" onkeyup="updateUI(this.value, 'view_thc_bongkar')" required>
                </div>

                <div class="form-group">
                    <label>Strip, Lo-lo, dll : <span id="view_strip" class="text-green">Rp. <?php echo format_rupiah($order['harga_strip']); ?></span></label>
                    <input type="number" id="val_strip" class="form-control hitung-biaya" name="harga_strip" value="<?php echo $order['harga_strip']; ?>" onkeyup="updateUI(this.value, 'view_strip')" required>
                </div>

                <div class="form-group">
                    <label>Truck Bongkar : <span id="view_truck_bongkar" class="text-green">Rp. <?php echo format_rupiah($order['harga_truck_bongkar']); ?></span></label>
                    <input type="number" id="val_truck_bongkar" class="form-control hitung-biaya" name="harga_truck_bongkar" value="<?php echo $order['harga_truck_bongkar']; ?>" onkeyup="updateUI(this.value, 'view_truck_bongkar')" required>
                </div>

                <div class="form-group">
                    <label>Buruh : <span id="view_buruh" class="text-green">Rp. <?php echo format_rupiah($order['harga_buruh']); ?></span></label>
                    <input type="number" id="val_buruh" class="form-control hitung-biaya" name="harga_buruh" value="<?php echo $order['harga_buruh']; ?>" onkeyup="updateUI(this.value, 'view_buruh')" required>
                </div>
            </div>
        </div>

        <hr style="border-top: 2px dashed #ccc;">

        <div class="form-group" style="background-color: #e8f0fe; padding: 15px; border-radius: 5px; border-left: 5px solid #3c8dbc;">
            <label style="font-size: 16px; color: #3c8dbc;">HARGA JUAL (INVOICE) : <span id="view_inv" style="color: #3c8dbc; font-weight: bold;">Rp. <?php echo format_rupiah($order['harga_inv']); ?></span></label>
            <input type="number" id="val_inv" class="form-control" name="harga_inv" value="<?php echo $order['harga_inv']; ?>" 
                   style="height: 50px; font-size: 20px; font-weight: bold; border: 1px solid #3c8dbc;"
                   onkeyup="updateUI(this.value, 'view_inv')" required>
            <small class="text-muted">Masukkan harga tagihan ke customer di sini. Profit akan otomatis terhitung.</small>
        </div>

        <button type="submit" class="btn btn-success btn-lg btn-block" style="margin-top: 20px;">Simpan Perubahan Harga</button>
    </form>
</div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
    // Style text color helper
    var styleGreen = 'color: green; font-weight: bold;';
    var styleBlue  = 'color: #3c8dbc; font-weight: bold;';

    function updateUI(val, targetID) {
        // 1. Update Label Rupiah per item
        var formatted = formatRupiahJS(val);
        var el = document.getElementById(targetID);
        if(el) {
            el.innerHTML = 'Rp. ' + formatted;
            // Jika itu inv, pakai warna biru, selain itu hijau
            if(targetID == 'view_inv') el.setAttribute("style", styleBlue);
            else el.setAttribute("style", styleGreen);
        }

        // 2. Jalankan Kalkulasi Total
        kalkulasiTotal();
    }

    function kalkulasiTotal() {
        // Ambil nilai dari semua komponen biaya (default 0 jika kosong)
        var biaya = 0;
        biaya += Number(document.getElementById('val_seal').value) || 0;
        biaya += Number(document.getElementById('val_truck_muat').value) || 0;
        biaya += Number(document.getElementById('val_thc_muat').value) || 0;
        biaya += Number(document.getElementById('val_of').value) || 0;
        biaya += Number(document.getElementById('val_lss').value) || 0;
        biaya += Number(document.getElementById('val_thc_bongkar').value) || 0;
        biaya += Number(document.getElementById('val_strip').value) || 0;
        biaya += Number(document.getElementById('val_truck_bongkar').value) || 0;
        biaya += Number(document.getElementById('val_buruh').value) || 0;

        // Ambil nilai Harga Invoice
        var hargaInv = Number(document.getElementById('val_inv').value) || 0;

        // Hitung Profit
        var profit = hargaInv - biaya;

        // Update Dashboard Atas
        document.getElementById('info_total_biaya').innerText = 'Rp. ' + formatRupiahJS(biaya);
        document.getElementById('info_harga_inv').innerText = 'Rp. ' + formatRupiahJS(hargaInv);
        document.getElementById('info_profit').innerText = 'Rp. ' + formatRupiahJS(profit);
    }

    // Helper: Format Angka ke Ribuan (Tanpa Rp)
    function formatRupiahJS(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah || '0';
    }
</script>
<?php include '../footer.php'; ?>