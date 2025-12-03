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

// 1. Ambil Data Order (Digunakan di kedua halaman)
$sqlOrder = "SELECT * FROM m_order WHERE codx = :codx";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bindParam(':codx', $codx, PDO::PARAM_STR);
$stmtOrder->execute();
$rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC);

// 2. Ambil Data Setting (Digunakan di halaman 2)
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


// 3. Fungsi Terbilang (Digunakan di halaman 1)
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Full Invoice Print</title>
<script>
  window.onload = function() {
    window.print();
  };
</script>
<style>
    /* --- GLOBAL STYLES --- */
    body {
        font-family: Arial, sans-serif; /* Font disamakan */
        margin: 0;
        padding: 20px;
        background: #fff; 
        color: #000;
        font-size: 14px;
    }

    /* --- KOP SURAT (Dipakai di Halaman 1 & 2) --- */
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
    
    /* --- WATERMARK --- */
    .watermark {
         z-index: -1;
         position: absolute;
         opacity: 0.05;
         width: 95%;
         top: 50px; /* Posisi watermark */
    }

    /* --- LAYOUT UMUM --- */
    .page-container {
        position: relative;
        width: 100%;
    }

    table {
        font-size: 14px;
        width: 100%;
    }

    /* --- TABEL RINCIAN (HALAMAN 2) --- */
    .detail-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .detail-table th {
      border: 1px solid #000;
      padding: 8px;
      background-color: #f2f2f2; /* Abu tipis formal */
      font-weight: bold;
      text-align: left;
    }
    .detail-table td {
      border: 1px solid #000;
      padding: 8px;
    }
    
    .total-section {
      text-align: right;
      padding-top: 20px;
    }
    .total-section h2 {
      margin: 0;
      font-size: 18px;
      font-weight: bold;
    }

    /* --- PRINT CONTROL --- */
    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }
        body {
            background: none;
            padding: 0;
            margin: 0;
        }
        /* Pastikan watermark muncul saat print (browser setting dependent) */
        .watermark {
            display: block;
        }
    }
</style>
</head>
                
<body>

<div class="page-container page-one">
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
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
      <tr>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%; padding: 10px; text-align: center; vertical-align: middle; border: 1px solid red; color: blue; font-weight: bold; text-transform: uppercase;">
          Commercial Invoice
        </td>
      </tr>
    </table>

    <table>
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

    <table>
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
        $shipperID = $rowOrder['shipper_id'];
        $stmtShipper = $conn->prepare("SELECT * FROM m_user WHERE id = :shipper");
        $stmtShipper->bindParam(':shipper', $shipperID, PDO::PARAM_INT);
        $stmtShipper->execute();
        $rowShipper = $stmtShipper->fetch(PDO::FETCH_ASSOC);
        
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
    <table class="tbl-detail">
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
            <?php
            // Menghitung 1.1% dari total amount
            $persen = $rowOrder['total_amount'] * 0.011;
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
            // Menjumlahkan total amount + persen
            $totalFinal = $rowOrder['total_amount'] + $persen;
            echo number_format($totalFinal, 0, ',', '.');
            ?>
        </td>
        <td></td>
    </tr>
</table>
    <div style="margin-top:8px; font-style:italic;">
      IN WORD : <?php echo "Rp " . number_format($totalFinal, 0, ',', '.') . " (" . terbilang($totalFinal) . " rupiah)"; ?>
    </div>

    <br><br>

    <?php
    if ($rowOrder) {
        if ($rowOrder['muncul_rek'] == 1) {
            $stmtRek = $conn->prepare("SELECT nama, des FROM m_rek WHERE id = :rekid");
            $stmtRek->execute(['rekid' => $rowOrder['rek_id']]);
            $rekData = $stmtRek->fetch(PDO::FETCH_ASSOC);
            ?>
            <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">
              <tr>
                <td style="vertical-align:top; width:50%;">
                  <div style="border:1px solid #000; padding:8px;">
                    <div><?php echo htmlspecialchars($rekData['nama']); ?></div>
                    <div><?php echo htmlspecialchars($rekData['des']); ?></div>
                    <div>Nomor Rekening</div>
                  </div>
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
            <?php
        } else {
            ?>
            <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">
              <tr>
                <td style="width:50%;">&nbsp;</td> 
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
            <?php
        }
    }
    ?>
</div>

<div class="page-break"></div>

<div class="page-container page-two">
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

    <div style="text-align:center; margin-bottom:20px;">
        <h2 style="text-decoration: underline; margin:0;">RINCIAN BIAYA / DETAIL INVOICE</h2>
        <h4 style="margin:5px 0;">No. Invoice: <?php echo $rowOrder['invoice_no']; ?></h4>
        <div style="font-size:12px;">
             <?php
                date_default_timezone_set('Asia/Makassar'); 
                echo "Dicetak pada: " . date('l, j F Y - H:i') . ' WITA';
             ?>
        </div>
    </div>

    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="width:150px; font-weight:bold;">Tagihan Kepada</td>
            <td>: <?php echo $rowOrder['customer']; ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Alamat</td>
            <td>: <?php echo $rowOrder['address']; ?></td>
        </tr>
    </table>

    <table class="detail-table">
      <thead>
          <tr>
            <th style="width:40px; text-align:center;">No</th>
            <th>Deskripsi</th>
            <th style="width:60px; text-align:center;">Qty</th>
            <th style="width:150px; text-align:right;">Harga Satuan</th>
            <th style="width:150px; text-align:right;">Total</th>
          </tr>
      </thead>
      <tbody>
      <?php
       $count = 1;
       $grandTotalRincian = 0; 
        
       $sqlKas = $conn->prepare("SELECT * FROM `m_kas` WHERE order_codx = :order_codx AND stat = 4 ORDER BY id DESC");
       $sqlKas->bindParam(':order_codx', $codx, PDO::PARAM_STR);
       $sqlKas->execute();
       
       while($dataKas = $sqlKas->fetch()) {
           $nilai = $dataKas['nilai'];
           $grandTotalRincian += $nilai; 
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
        ?>
        <?php if($count < 5) { ?>
            <tr><td colspan="5" style="height:30px;"></td></tr>
        <?php } ?>
      </tbody>
      <tfoot>
          <tr>
              <td colspan="4" style="text-align:right; font-weight:bold; background-color:#f2f2f2;">TOTAL</td>
              <td style="text-align:right; font-weight:bold; background-color:#f2f2f2;">
                  <?php echo "Rp " . number_format($grandTotalRincian, 0, ',', '.'); ?>
              </td>
          </tr>
      </tfoot>
    </table>

</div>

</body>
</html>