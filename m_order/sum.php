<?php
include '../../config.php';
error_reporting(0);

/* --- CEK SESI --- */
if(isset($_SESSION['email']) == 0) {
	header('Location: ../../index.php');
}

include '../../auth.php';

/* --- AMBIL DATA UTAMA --- */
$codx = $_GET['codx'];

// 1. Ambil Data Order
$sqlOrder = "SELECT * FROM m_order WHERE codx = :codx";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bindParam(':codx', $codx, PDO::PARAM_STR);
$stmtOrder->execute();
$rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC);

// 2. Ambil Data Setting
$sqlSetting = "SELECT * FROM setting ORDER BY id DESC";
$stmtSetting = $conn->prepare($sqlSetting);
$stmtSetting->execute();
$rowSetting = $stmtSetting->fetch();

// --- LOGIKA BARU PENGAMBILAN DATA TANDA TANGAN & CAP ---
// Ambil ID TTD dan Cap dari variabel $rowOrder
$ttd_id = $rowOrder['ttd_id']; 
$cap_id = $rowOrder['cap_id']; 

// Query Data Tanda Tangan (Untuk Gambar TTD, Nama, dan Jabatan)
$stmt_ttd = $conn->prepare("SELECT * FROM m_tanda_tangan WHERE id = :id");
$stmt_ttd->execute(['id' => $ttd_id]);
$ttd = $stmt_ttd->fetch(PDO::FETCH_ASSOC);

// Query Data Cap (Untuk Gambar Cap)
$stmt_cap = $conn->prepare("SELECT * FROM m_cap WHERE id = :id");
$stmt_cap->execute(['id' => $cap_id]);
$cap = $stmt_cap->fetch(PDO::FETCH_ASSOC);


// 3. Ambil data kas untuk Invoice 2 (Rincian)
$sqlKas = $conn->prepare("SELECT * FROM `m_kas` WHERE order_codx = :order_codx AND stat = 4 ORDER BY id DESC");
$sqlKas->bindParam(':order_codx', $codx, PDO::PARAM_STR);
$sqlKas->execute();
$kasItems = $sqlKas->fetchAll(PDO::FETCH_ASSOC);

$grandTotalRincian = 0;
foreach ($kasItems as $kasItem) {
    $grandTotalRincian += $kasItem['nilai'];
}

// 4. Hitung total Invoice 1
$handlingFee = $rowOrder['total_amount'] * 0.011;
$totalInvoiceOne = $rowOrder['total_amount'] + $handlingFee;

// 5. Fungsi Terbilang
function terbilang($angka) {
    $angka = abs($angka);
    $baca  = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $hasil = "";
    if ($angka < 12) { $hasil = $baca[$angka]; } 
    elseif ($angka < 20) { $hasil = terbilang($angka - 10) . " belas"; } 
    elseif ($angka < 100) { $hasil = terbilang(floor($angka / 10)) . " puluh " . terbilang($angka % 10); } 
    elseif ($angka < 200) { $hasil = "seratus " . terbilang($angka - 100); } 
    elseif ($angka < 1000) { $hasil = terbilang(floor($angka / 100)) . " ratus " . terbilang($angka % 100); } 
    elseif ($angka < 2000) { $hasil = "seribu " . terbilang($angka - 1000); } 
    elseif ($angka < 1000000) { $hasil = terbilang(floor($angka / 1000)) . " ribu " . terbilang($angka % 1000); } 
    elseif ($angka < 1000000000) { $hasil = terbilang(floor($angka / 1000000)) . " juta " . terbilang($angka % 1000000); } 
    else { $hasil = "Angka terlalu besar"; }
    return trim(preg_replace('/\s+/', ' ', $hasil));
}

// --- TEMPLATE BAGIAN ATAS (KOP SURAT) AGAR BISA DIPAKAI BERULANG --- //
ob_start();
?>
<div class="watermark">
     <img src="../../images/logo.png?p=1" width="100%">
</div>
<div class="kop-surat">
    <div class="kop-logo">
        <img src="../../images/logo.png?p=1" alt="Logo Perusahaan">
    </div>
    <div class="kop-text">
        <h1>PT. HARUKA JASA SAMUDRA</h1>
        <h2>FREIGH FORWARDING SERVICES, EXPORT-IMPORT, LAND TRANSPORT, WAREHOUSE</h2>
        <p><b>HEAD OFFICE</b> : Jl.Bengawan Solo No.40 Singkil. Kota Manado - SULUT 95234</p>
        <p><b>WAREHOUSE</b> : Jl.Ir.Soekarno Kelurahan Airmadidi Atas Kecamatan Airmadidi, Minahasa Utara – SULUT 95371</p>
        <p><b>SURABAYA BRANCH</b> : Jl.Laksda M.Nasir, Ruko TJ.Priok No.11. Perak Barat Kec. Krembangan Surabaya – JATIM 60177</p>
        <p>Telp: 0852-0011-2552 | Email: haruka.samudra@gmail.com</p>
    </div>
</div>
<?php
$kopSuratHTML = ob_get_clean();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Invoice Gabungan</title>
<script>
  window.onload = function() {
    window.print();
  };
</script>
<style>
    /* --- GLOBAL STYLES --- */
    body {
        font-family: Arial, sans-serif; /* Font seragam untuk semua halaman */
        margin: 0;
        padding: 20px;
        background: #fff;
        font-size: 12px; /* Ukuran font standar dokumen */
        color: #000;
    }

    /* Struktur KOP SURAT */
    .kop-surat {
        display: flex;
        align-items: flex-start;
        border-bottom: 2px solid black;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .kop-logo img {
        width: 100px;
        height: auto;
    }
    .kop-text {
        margin-left: 15px;
    }
    .kop-text h1 {
        margin: 0;
        font-size: 24px; /* Disesuaikan agar muat */
        color: red;
        font-weight: bold;
    }
    .kop-text h2 {
        margin: 2px 0;
        font-size: 11px;
        color: blue;
        font-weight: bold;
    }
    .kop-text p {
        margin: 1px 0;
        font-size: 10px;
    }

    /* WATERMARK */
    .watermark {
         z-index: -1;
         position: absolute;
         opacity: 0.05;
         width: 95%;
         top: 50px;
         left: 0;
    }

    /* TITLE BOX (Kotak Judul Merah/Biru) */
    .title-box-container {
        width: 100%; 
        border-collapse: collapse; 
        table-layout: fixed;
        margin-bottom: 15px;
    }
    .title-box {
        padding: 10px; 
        text-align: center; 
        vertical-align: middle; 
        border: 1px solid red; 
        color: blue; 
        font-weight: bold; 
        text-transform: uppercase;
        font-size: 14px;
    }

    /* TABEL STANDAR (Untuk Halaman 2 & 3) */
    .std-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 12px;
    }
    .std-table th {
        border: 1px solid #000;
        padding: 8px;
        background-color: #f0f0f0;
        text-align: center;
        font-weight: bold;
    }
    .std-table td {
        border: 1px solid #000;
        padding: 6px;
    }

    /* TABEL INFORMASI (Tanpa Garis, untuk Halaman 1 & Info Header) */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .info-table td {
        padding: 2px;
        vertical-align: top;
    }

    /* PRINT CONTROL */
    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }
        body {
            padding: 0;
            background: none;
        }
    }
</style>
</head>
                
<body>

<div class="page-one">
    
    <?php echo $kopSuratHTML; ?>

    <br>
    <table class="title-box-container">
      <tr>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%;"></td>
        <td class="title-box" style="width: 33.33%;">
          Commercial Invoice
        </td>
      </tr>
    </table>

    <table class="info-table">
      <tr>
        <td width="150">Invoice No</td><td width="10">:</td><td><?php echo $rowOrder['invoice_no']; ?></td>
      </tr>
      <tr>
        <td>Invoice Date</td><td>:</td><td><?php echo $rowOrder['invoice_date']; ?></td>
      </tr>
      <tr>
        <td>Credit Terms</td><td>:</td><td><?php echo $rowOrder['credit_terms']; ?></td>
      </tr>
      <tr>
        <td>Customer</td><td>:</td><td><?php echo $rowOrder['customer']; ?></td>
      </tr>
      <tr>
        <td>Attn</td><td>:</td><td><?php echo $rowOrder['attn']; ?></td>
      </tr>
      <tr>
        <td>Address</td><td>:</td><td><?php echo $rowOrder['address']; ?></td>
      </tr>
    </table>

    <br><br>

    <table class="info-table">
      <tr>
        <td width="150">NO.BASTB</td><td width="10">:</td><td><?php echo $rowOrder['no_bast']; ?></td>
      </tr>
      <tr>
        <td>CONTAINER/SEAL</td><td>:</td><td><?php echo $rowOrder['container_seal']; ?></td>
      </tr>
      <tr>
        <td>CONTAINER SIZE</td><td>:</td><td><?php echo $rowOrder['container_size']; ?></td>
      </tr>
      <tr>
        <td>LOADING/DESTINATION</td><td>:</td><td><?php echo $rowOrder['loading_des']; ?></td>
      </tr>
      
      <?php   
        // Ambil Data Shipper
        $shipperID = $rowOrder['shipper_id'];
        $stmtShipper = $conn->prepare("SELECT * FROM m_user WHERE id = :shipper");
        $stmtShipper->bindParam(':shipper', $shipperID, PDO::PARAM_INT);
        $stmtShipper->execute();
        $rowShipper = $stmtShipper->fetch(PDO::FETCH_ASSOC);
        
        // Ambil Data Consignee
        $consigneeID = $rowOrder['consignee_id'];
        $stmtConsignee = $conn->prepare("SELECT * FROM m_user WHERE id = :consignee");
        $stmtConsignee->bindParam(':consignee', $consigneeID, PDO::PARAM_INT);
        $stmtConsignee->execute();
        $rowConsignee = $stmtConsignee->fetch(PDO::FETCH_ASSOC);
      ?>
      
      <tr>
        <td>SHIPPER</td><td>:</td><td><?php echo $rowShipper['nama']; ?></td>
      </tr>
      <tr>
        <td>CONSIGNEE</td><td>:</td><td><?php echo $rowConsignee['nama']; ?></td>
      </tr>
      <tr>
        <td>COMMODITY</td><td>:</td><td><?php echo $rowOrder['commodity']; ?></td>
      </tr>
      <tr>
        <td>VESSEL</td><td>:</td><td><?php echo $rowOrder['vessel']; ?></td>
      </tr>
      <tr>
        <td>VOYAGE</td><td>:</td><td><?php echo $rowOrder['voyage']; ?></td>
      </tr>
      <tr>
        <td>CONDITION</td><td>:</td>
        <?php
        $conditiId = $rowOrder['conditi'];
        $stmtCond = $conn->prepare("SELECT nama FROM m_condition WHERE id = :id");
        $stmtCond->execute(['id' => $conditiId]);
        $condData = $stmtCond->fetch(PDO::FETCH_ASSOC);
        ?>
        <td><?php echo $condData['nama'] ?? ''; ?></td>
      </tr>
      <tr>
        <td>DISCHARGING DATE</td><td>:</td><td><?php echo $rowOrder['discharging_date']; ?></td>
      </tr>
    </table>

    <br>
    <table style="width:100%; border-collapse:collapse; font-size:14px;">
    <tr>
        <td width="25%">TOTAL AMOUNT</td>
        <td style="width:12px; text-align:center;">:</td>
        <td style="width:20px; text-align:right;">Rp</td>
        <td style="text-align:right; width:120px; font-variant-numeric:tabular-nums;">
            <?php echo number_format($rowOrder['total_amount'], 0, ',', '.'); ?>
        </td>
        <td></td>
    </tr>

    <tr>
        <td>Handling Container (VAT 1,1%)</td>
        <td style="width:12px; text-align:center;">:</td>
        <td style="width:20px; text-align:right;">Rp</td>
        <td style="text-align:right; width:120px; font-variant-numeric:tabular-nums;">
            <?php echo number_format($handlingFee, 0, ',', '.'); ?>
        </td>
        <td></td>
    </tr>

    <tr>
        <td style="font-weight:bold; border-top:1px solid #000; padding-top:8px;">
            TOTAL INVOICE
        </td>
        <td style="width:12px; text-align:center; font-weight:bold; border-top:1px solid #000; padding-top:8px;">:</td>
        <td style="width:20px; text-align:right; font-weight:bold; border-top:1px solid #000; padding-top:8px;">
            Rp
        </td>
        <td style="text-align:right; width:120px; font-weight:bold; border-top:1px solid #000; padding-top:8px; font-variant-numeric:tabular-nums;">
            <?php echo number_format($totalInvoiceOne, 0, ',', '.'); ?>
        </td>
        <td></td>
    </tr>
</table>

  <div style="margin-top:8px; font-style:italic;">
      IN WORD : <?php echo "Rp " . number_format($totalInvoiceOne, 0, ',', '.') . " (" . terbilang($totalInvoiceOne) . " rupiah)"; ?>
  </div>

  <br><br>

  <?php
    // Footer / Signature
    if ($rowOrder) {
        $rekData = null;
        if ($rowOrder['muncul_rek'] == 1) {
            $stmtRek = $conn->prepare("SELECT nama, des FROM m_rek WHERE id = :rekid");
            $stmtRek->execute(['rekid' => $rowOrder['rek_id']]);
            $rekData = $stmtRek->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:12px;">
          <tr>
            <td style="vertical-align:top; width:50%;">
              <?php if($rekData): ?>
              <div style="border:1px solid #000; padding:8px;">
                <div><?php echo htmlspecialchars($rekData['nama']); ?></div>
                <div><?php echo htmlspecialchars($rekData['des']); ?></div>
                <div>Nomor Rekening</div>
              </div>
              <?php endif; ?>
            </td>
            <td style="vertical-align:top; text-align:center; width:50%;">
              <div style="font-weight:bold; margin-bottom: 5px;">REGARDS</div>
              
              <div style="height:100px; position: relative; display: flex; justify-content: center; align-items: center;">
                  <?php if ($ttd && !empty($ttd['pic'])): ?>
                      <img src="../../public/uploads/ttd/<?php echo $ttd['pic']; ?>" 
                           style="height: 80px; position: absolute; z-index: 1;">
                  <?php endif; ?>
                  
                  <?php if ($cap && !empty($cap['pic'])): ?>
                      <img src="../../public/uploads/cap/<?php echo $cap['pic']; ?>" 
                           style="height: 80px; position: absolute; z-index: 2; opacity: 0.8;"> 
                  <?php endif; ?>
              </div>

              <div style="font-weight:bold; text-decoration: underline; margin-top:5px;">
                <?php echo htmlspecialchars($ttd['nama'] ?? ''); ?>
              </div>

              <div>
                <?php echo htmlspecialchars($ttd['des'] ?? ''); ?>
              </div>
            </td>
          </tr>
        </table>
    <?php } ?>
</div>


<div class="page-break"></div>


<div>
    <?php echo $kopSuratHTML; ?>

    <br>
    <table class="title-box-container">
      <tr>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%;"></td>
        <td class="title-box" style="width: 33.33%;">
          RINCIAN BIAYA
        </td>
      </tr>
    </table>

    <table class="info-table" style="margin-bottom: 20px;">
        <tr>
            <td width="150"><strong>Tagihan Kepada</strong></td>
            <td width="10">:</td>
            <td><?php echo $rowOrder['customer']; ?></td>
        </tr>
        <tr>
            <td><strong>Alamat</strong></td>
            <td>:</td>
            <td><?php echo $rowOrder['address']; ?></td>
        </tr>
        <tr>
             <td><strong>Tanggal Cetak</strong></td>
             <td>:</td>
             <td><?php date_default_timezone_set('Asia/Makassar'); echo date('d F Y H:i') . ' WITA'; ?></td>
        </tr>
    </table>

    <table class="std-table">
      <thead>
          <tr>
            <th style="width:30px">No</th>
            <th>Deskripsi</th>
            <th style="width:50px">Qty</th>
            <th style="width:150px">Harga Satuan</th>
            <th style="width:150px">Total</th>
          </tr>
      </thead>
      <tbody>
      <?php
       $count = 1;
       if(count($kasItems) > 0){
           foreach ($kasItems as $dataKas) {
           ?>
          <tr>
            <td style="text-align:center;"><?php echo $count; ?></td>
            <td><?php echo $dataKas['nama'];?></td>
            <td style="text-align:center;">1</td>
            <td style="text-align:right;"><?php echo "Rp " . number_format($dataKas['nilai'], 0, ',', '.');?></td>
            <td style="text-align:right;"><?php echo "Rp " . number_format($dataKas['nilai'], 0, ',', '.');?></td>
          </tr>
          <?php 
            $count++;
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data rincian tambahan.</td></tr>";
        }
        ?>
        <tr>
            <td colspan="4" style="text-align:right; font-weight:bold; background-color:#eee;">Total Rincian</td>
            <td style="text-align:right; font-weight:bold; background-color:#eee;">
                <?php echo "Rp " . number_format($grandTotalRincian, 0, ',', '.'); ?>
            </td>
        </tr>
      </tbody>
    </table>

    <br><br>
    <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:12px;">
      <tr>
        <td style="width:50%;"></td>
        <td style="vertical-align:top; text-align:center; width:50%;">
          <div style="font-weight:bold; margin-bottom: 5px;">REGARDS</div>
          
          <div style="height:100px; position: relative; display: flex; justify-content: center; align-items: center;">
              <?php if ($ttd && !empty($ttd['pic'])): ?>
                  <img src="../../public/uploads/ttd/<?php echo $ttd['pic']; ?>" 
                       style="height: 80px; position: absolute; z-index: 1;">
              <?php endif; ?>
              
              <?php if ($cap && !empty($cap['pic'])): ?>
                  <img src="../../public/uploads/cap/<?php echo $cap['pic']; ?>" 
                       style="height: 80px; position: absolute; z-index: 2; opacity: 0.8;"> 
              <?php endif; ?>
          </div>

          <div style="font-weight:bold; text-decoration: underline; margin-top:5px;">
            <?php echo htmlspecialchars($ttd['nama'] ?? ''); ?>
          </div>

          <div>
            <?php echo htmlspecialchars($ttd['des'] ?? ''); ?>
          </div>
        </td>
      </tr>
    </table>
</div>


<div class="page-break"></div>


<div>
    <?php echo $kopSuratHTML; ?>

    <br>
    <table class="title-box-container">
      <tr>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%;"></td>
        <td class="title-box" style="width: 33.33%;">
          REKAPITULASI
        </td>
      </tr>
    </table>

    <br>
    
    <table class="std-table" style="width: 60%; margin: 0 auto;"> <thead>
          <tr>
              <th>Keterangan</th>
              <th style="width: 200px;">Jumlah</th>
          </tr>
      </thead>
      <tbody>
        <tr>
          <td>Total Commercial Invoice (Hal. 1)</td>
          <td style="text-align:right;"><?php echo "Rp " . number_format($totalInvoiceOne, 0, ',', '.'); ?></td>
        </tr>
        <tr>
          <td>Total Rincian Biaya (Hal. 2)</td>
          <td style="text-align:right;"><?php echo "Rp " . number_format($grandTotalRincian, 0, ',', '.'); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold; background-color:#eee;">GRAND TOTAL</td>
          <td style="text-align:right; font-weight:bold; background-color:#eee; border-top: 2px solid #000;">
              <?php echo "Rp " . number_format($totalInvoiceOne + $grandTotalRincian, 0, ',', '.'); ?>
          </td>
        </tr>
      </tbody>
    </table>

    <br><br>
     <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:12px;">
      <tr>
        <td style="width:50%;"></td>
        <td style="vertical-align:top; text-align:center; width:50%;">
          <div style="font-weight:bold; margin-bottom: 5px;">REGARDS</div>
          
          <div style="height:100px; position: relative; display: flex; justify-content: center; align-items: center;">
              <?php if ($ttd && !empty($ttd['pic'])): ?>
                  <img src="../../public/uploads/ttd/<?php echo $ttd['pic']; ?>" 
                       style="height: 80px; position: absolute; z-index: 1;">
              <?php endif; ?>
              
              <?php if ($cap && !empty($cap['pic'])): ?>
                  <img src="../../public/uploads/cap/<?php echo $cap['pic']; ?>" 
                       style="height: 80px; position: absolute; z-index: 2; opacity: 0.8;"> 
              <?php endif; ?>
          </div>

          <div style="font-weight:bold; text-decoration: underline; margin-top:5px;">
            <?php echo htmlspecialchars($ttd['nama'] ?? ''); ?>
          </div>

          <div>
            <?php echo htmlspecialchars($ttd['des'] ?? ''); ?>
          </div>
        </td>
      </tr>
    </table>
</div>

</body>
</html>