<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}

include '../../auth.php';

$codx = $_GET['codx'];

// Ambil data Order berdasarkan codx
$sql = "SELECT * FROM m_order WHERE codx = :codx";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':codx', $codx, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice - <?php echo $row['invoice_no']; ?></title>
<script>
  window.onload = function() {
    window.print();
  };
</script>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px; /* Standar ukuran font */
    }
    .kop-surat {
        display: flex;
        align-items: flex-start;
        border-bottom: 2px solid black;
        padding-bottom: 10px;
    }
    .kop-logo img {
        width: 100px; /* Sesuaikan ukuran logo */
        height: auto;
    }
    .kop-text {
        margin-left: 15px;
    }
    .kop-text h1 {
        margin: 0;
        font-size: 30px;
        color: red;
        font-weight: bold;
    }
    .kop-text h2 {
        margin: 2px 0;
        font-size: 12px;
        color: blue;
        font-weight: bold;
    }
    .kop-text p {
        margin: 2px 0;
        font-size: 12px;
    }
    
    /* kotak COMMERCIAL INVOICE */
    .commercial-invoice {
      border: 2px solid red;      /* border kotak warna merah */
      padding: 10px 18px;
      color: blue;                /* teks berwarna biru */
      font-weight: bold;          /* huruf tebal */
      text-transform: uppercase;
      text-align: center;
    }

    .watermark {
         z-index: -1;
         position: absolute;
         opacity: 0.05;
         width: 100%;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    /* Styling tabel detail */
    .tbl-detail td {
        padding: 3px;
        vertical-align: top;
    }
</style>
</head>
                
<body>
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

<br>
<table style="width: 100%; table-layout: fixed;">
  <tr>
    <td style="width: 33.33%;"></td>
    <td style="width: 33.33%;"></td>
    <td style="width: 33.33%; text-align: center;">
      <div class="commercial-invoice">Commercial Invoice</div>
    </td>
  </tr>
</table>

<table class="tbl-detail">
  <tr>
    <td width="25%">Invoice No</td>
    <td width="2%">:</td>
    <td><?php echo $row['invoice_no']; ?></td>
  </tr>
  <tr>
    <td>Invoice Date</td>
    <td>:</td>
    <td><?php echo $row['invoice_date']; ?></td>
  </tr>
  <tr>
    <td>Credit Terms</td>
    <td>:</td>
    <td><?php echo $row['credit_terms']; ?></td>
  </tr>
  <tr>
    <td>Customer</td>
    <td>:</td>
    <td><?php echo $row['customer']; ?></td>
  </tr>
  <tr>
    <td>Attn</td>
    <td>:</td>
    <td><?php echo $row['attn']; ?></td>
  </tr>
  <tr>
    <td>Address</td>
    <td>:</td>
    <td><?php echo $row['address']; ?></td>
  </tr>
</table>

<br>
<br>

<table class="tbl-detail">
  <tr>
    <td width="25%">NO.BASTB</td>
    <td width="2%">:</td>
    <td><?php echo $row['no_bastb']; // Perbaikan: di DB biasanya no_bastb bukan no_bast ?></td>
  </tr>
  <tr>
    <td>CONTAINER/SEAL</td>
    <td>:</td>
    <td><?php echo $row['container_seal']; ?></td>
  </tr>
  <tr>
    <td>CONTAINER SIZE</td>
    <td>:</td>
    <td><?php echo $row['container_size']; ?></td>
  </tr>
  <tr>
    <td>LOADING/DESTINATION</td>
    <td>:</td>
    <td><?php echo $row['loading_des']; ?></td>
  </tr>
  
    <?php   
    // Ambil Data Shipper
    $shipper = $row['shipper_id'];
    $sqla = "SELECT * FROM m_user WHERE id = :shipper";
    $stmta = $conn->prepare($sqla);
    $stmta->bindParam(':shipper', $shipper, PDO::PARAM_INT);
    $stmta->execute();
    $rowa = $stmta->fetch(PDO::FETCH_ASSOC);
    
    // Ambil Data Consignee
    $consignee = $row['consignee_id'];
    $sqlb = "SELECT * FROM m_user WHERE id = :consignee";
    $stmtb = $conn->prepare($sqlb);
    $stmtb->bindParam(':consignee', $consignee, PDO::PARAM_INT);
    $stmtb->execute();
    $rowb = $stmtb->fetch(PDO::FETCH_ASSOC);
    ?>
  
  <tr>
    <td>SHIPPER</td>
    <td>:</td>
    <td><?php echo $rowa['nama']; ?></td>
  </tr>
  <tr>
    <td>CONSIGNEE</td>
    <td>:</td>
    <td><?php echo $rowb['nama']; ?></td>
  </tr>
  <tr>
    <td>COMMODITY</td>
    <td>:</td>
    <td><?php echo $row['commodity']; ?></td>
  </tr>
  <tr>
    <td>VESSEL</td>
    <td>:</td>
    <td><?php echo $row['vessel']; ?></td>
  </tr>
  <tr>
    <td>VOYAGE</td>
    <td>:</td>
    <td><?php echo $row['voyage']; ?></td>
  </tr>
  <tr>
    <td>CONDITION</td>
    <td>:</td>
    <?php
    $conditiId = $row['conditi'];
    $stmt2 = $conn->prepare("SELECT nama FROM m_condition WHERE id = :id");
    $stmt2->execute(['id' => $conditiId]);
    $cond = $stmt2->fetch(PDO::FETCH_ASSOC);
    ?>
    <td><?php echo $cond['nama'] ?? ''; ?></td>
  </tr>
  <tr>
    <td>DISCHARGING DATE</td>
    <td>:</td>
    <td><?php echo $row['discharging_date']; ?></td>
  </tr>
</table>

<br>

<table class="tbl-detail">
  <tr>
    <td width="25%">TOTAL AMOUNT</td>
    <td style="width:12px; text-align:center;">:</td>
    <td style="width:20px; text-align:right;">Rp</td>
    <td style="text-align:right; width:120px; font-variant-numeric:tabular-nums;">
        <?php echo number_format($row['total_amount'], 0, ',', '.'); ?>
    </td>
    <td></td> </tr>

<tr>
    <td>Handling Container (VAT 1,1%)</td>
    <td style="width:12px; text-align:center;">:</td>
    <td style="width:20px; text-align:right;">Rp</td>
    <td style="text-align:right; width:120px; font-variant-numeric:tabular-nums;">
        <?php
        $persen = $row['total_amount'] * 0.011;
        echo number_format($persen, 0, ',', '.');
        ?>
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
        <?php
        $totalFinal = $row['total_amount'] + $persen;  
        echo number_format($totalFinal, 0, ',', '.');
        ?>
    </td>
    <td></td>
</tr>
</table>

<?php
// Fungsi Terbilang
function terbilang($angka) {
    $angka = abs($angka);
    $baca  = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $hasil = "";

    if ($angka < 12) {
        $hasil = $baca[$angka];
    } elseif ($angka < 20) {
        $hasil = terbilang($angka - 10) . " belas";
    } elseif ($angka < 100) {
        $hasil = terbilang(floor($angka / 10)) . " puluh " . terbilang($angka % 10);
    } elseif ($angka < 200) {
        $hasil = "seratus " . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $hasil = terbilang(floor($angka / 100)) . " ratus " . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        $hasil = "seribu " . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $hasil = terbilang(floor($angka / 1000)) . " ribu " . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        $hasil = terbilang(floor($angka / 1000000)) . " juta " . terbilang($angka % 1000000);
    } else {
        $hasil = "Angka terlalu besar";
    }

    return trim(preg_replace('/\s+/', ' ', $hasil)); 
}
?>

<div style="margin-top:8px; font-style:italic;">
  IN WORD : <?php echo "Rp " . number_format($totalFinal, 0, ',', '.') . " (" . terbilang($totalFinal) . " rupiah)"; ?>
</div>

<br>
<br>

<?php
// --- LOGIKA BARU PENGAMBILAN DATA TANDA TANGAN & CAP ---

// 1. Ambil ID TTD dan Cap dari variabel $row (data m_order)
$ttd_id = $row['ttd_id']; 
$cap_id = $row['cap_id']; 
$rek_id = $row['rek_id'];

// 2. Query Data Tanda Tangan (Untuk Gambar TTD, Nama, dan Jabatan)
$stmt_ttd = $conn->prepare("SELECT * FROM m_tanda_tangan WHERE id = :id");
$stmt_ttd->execute(['id' => $ttd_id]);
$ttd = $stmt_ttd->fetch(PDO::FETCH_ASSOC);

// 3. Query Data Cap (Untuk Gambar Cap)
$stmt_cap = $conn->prepare("SELECT * FROM m_cap WHERE id = :id");
$stmt_cap->execute(['id' => $cap_id]);
$cap = $stmt_cap->fetch(PDO::FETCH_ASSOC);

// 4. Query Data Rekening (Jika ditampilkan)
$rek = null;
if ($row['muncul_rek'] == 1) {
    $stmt_rek = $conn->prepare("SELECT nama, des FROM m_rek WHERE id = :rekid");
    $stmt_rek->execute(['rekid' => $rek_id]);
    $rek = $stmt_rek->fetch(PDO::FETCH_ASSOC);
}
?>

<table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">
  <tr>
    
    <td style="vertical-align:top; width:60%;">
      <?php if ($row['muncul_rek'] == 1 && $rek): ?>
          <div style="border:1px solid #000; padding:8px; width: 80%;">
            <div style="font-weight:bold;"><?php echo htmlspecialchars($rek['nama']); ?></div>
            <div style="white-space: pre-line;"><?php echo htmlspecialchars($rek['des']); ?></div>
            <div style="margin-top:5px; font-style:italic; font-size:12px;">Nomor Rekening</div>
          </div>
      <?php else: ?>
          &nbsp; <?php endif; ?>
    </td>

    <td style="vertical-align:top; text-align:center; width:40%;">
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

</body>
</html>