<?php
// --- 1. KONFIGURASI & SESSION ---
include '../../config.php'; // Pastikan path ini benar
// error_reporting(0); // Aktifkan baris ini jika ingin menyembunyikan semua warning di masa depan

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}
// --- 2. LOGIKA FILTER TANGGAL ---
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Format untuk query SQL
$start_date = $tgl_awal . " 00:00:00";
$end_date   = $tgl_akhir . " 23:59:59";

// --- 3. HELPER FUNCTION ---
function tanggal_indo($tanggal) {
    if(empty($tanggal)) return '-';
    $bulan = array (1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des');
    $pecahkan = explode('-', $tanggal);
    // Cek format tanggal valid
    if(count($pecahkan) == 3) {
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
    return $tanggal;
}

function getStatusOrder($status_id) {
    switch ($status_id) {
        case 0: return 'Open'; break;
        case 1: return 'Kirim'; break;
        case 2: return 'Transit'; break;
        case 3: return 'Sampai'; break;
        case 4: return 'Selesai'; break;
        default: return '-'; break;
    }
}

// --- 4. QUERY DATA ---
try {
    // A. Query List Order
    $sql = "SELECT * FROM m_order 
            WHERE created_at BETWEEN :s AND :e 
            ORDER BY created_at ASC"; 
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':s', $start_date);
    $stmt->bindParam(':e', $end_date);
    $stmt->execute();
    $data_order = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // B. Hitung Summary
    // 1. Total Container
    $q_total = $conn->prepare("SELECT COUNT(*) as total FROM m_order WHERE created_at BETWEEN :s AND :e");
    $q_total->bindParam(':s', $start_date);
    $q_total->bindParam(':e', $end_date);
    $q_total->execute();
    $total_container = $q_total->fetch()['total'];

    // 2. 20 Feet
    $q_20 = $conn->prepare("SELECT COUNT(*) as total FROM m_order WHERE container_size LIKE '%20%' AND created_at BETWEEN :s AND :e");
    $q_20->bindParam(':s', $start_date);
    $q_20->bindParam(':e', $end_date);
    $q_20->execute();
    $feet_20 = $q_20->fetch()['total'];

    // 3. 40 Feet
    $q_40 = $conn->prepare("SELECT COUNT(*) as total FROM m_order WHERE container_size LIKE '%40%' AND created_at BETWEEN :s AND :e");
    $q_40->bindParam(':s', $start_date);
    $q_40->bindParam(':e', $end_date);
    $q_40->execute();
    $feet_40 = $q_40->fetch()['total'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan List Order</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            background-color: #fff;
            margin: 20px;
        }

        /* --- STYLE KHUSUS PRINT & NAVIGASI --- */
        .no-print {
            background: #f4f4f4;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .filter-form { display: flex; align-items: center; gap: 10px; }
        .form-input { padding: 5px; border: 1px solid #ccc; border-radius: 3px; }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-blue { background-color: #007bff; }
        .btn-gray { background-color: #6c757d; }
        .btn-green { background-color: #28a745; }

        /* --- STYLE HEADER LAPORAN --- */
        .report-header { margin-bottom: 15px; }
        .report-title {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 12px;
            margin-top: 5px;
            font-weight: bold;
        }
        .header-line {
            border-bottom: 2px solid #000;
            margin-top: 8px;
            width: 100%;
        }

        /* --- STYLE TABEL --- */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table th {
            border: 1px solid #000;
            padding: 6px;
            background-color: #f2f2f2; 
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }

        table.data-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }

        .summary-table {
            width: 300px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .summary-table td { padding: 2px; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* SETTING PRINT PAGE */
        @media print {
            @page { 
                size: A4 landscape;
                margin: 10mm; 
            }
            body { margin: 0; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div class="filter-form">
            <a href="../admin/" class="btn btn-gray">&laquo; Kembali ke Dashboard</a>
            <form method="GET" action="" style="display:flex; gap:10px; align-items:center; margin-left: 20px;">
                <label>Periode:</label>
                <input type="date" name="tgl_awal" class="form-input" value="<?= $tgl_awal ?>">
                <span>s/d</span>
                <input type="date" name="tgl_akhir" class="form-input" value="<?= $tgl_akhir ?>">
                <button type="submit" class="btn btn-blue">Filter</button>
            </form>
        </div>
        <button onclick="window.print()" class="btn btn-green"> Cetak Laporan</button>
    </div>

    <div class="report-header">
        <div class="report-title">DATA MUATAN / LIST ORDER</div>
        <div class="report-subtitle">
            Periode: <?= tanggal_indo($tgl_awal) ?> - <?= tanggal_indo($tgl_akhir) ?>
        </div>
        <div class="header-line"></div>
    </div>

    <table class="summary-table">
        <tr>
            <td>Total Container</td>
            <td>: <?= $total_container ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Rincian</td>
            <td>
                : <?= $feet_20 ?> (20 Feet)<br>
                &nbsp;&nbsp;<?= $feet_40 ?> (40 Feet)
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="8%">TANGGAL</th>
                <th>CUSTOMER</th>
                <th>KAPAL / VOYAGE</th>
                <th>COMMODITY</th>
                <th>CONTAINER / SEAL</th>
                <th width="8%">STATUS</th>
                <th width="10%">INVOICE</th>
                <th width="12%">TOTAL AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(count($data_order) > 0) {
                $no = 1;
                $grand_total = 0;
                foreach($data_order as $row): 
                    $grand_total += $row['total_amount'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <span class="text-bold"><?= htmlspecialchars($row['customer'] ?? '') ?></span><br>
                    <small>Attn: <?= htmlspecialchars($row['attn'] ?? '') ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($row['vessel'] ?? '') ?><br>
                    Voy: <?= htmlspecialchars($row['voyage'] ?? '') ?>
                </td>
                <td><?= htmlspecialchars($row['commodity'] ?? '') ?></td>
                <td>
                    <?= htmlspecialchars($row['container_seal'] ?? '') ?><br>
                    <small>Size: <?= htmlspecialchars($row['container_size'] ?? '') ?></small>
                </td>
                <td class="text-center">
                    <?= getStatusOrder($row['status']) ?>
                </td>
                <td><?= htmlspecialchars($row['invoice_no'] ?? '') ?></td>
                <td class="text-right">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
            </tr>
            <?php 
                endforeach; 
            ?>
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="8" class="text-right">TOTAL PENDAPATAN PERIODE INI</td>
                    <td class="text-right">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                </tr>
            <?php
            } else {
                echo "<tr><td colspan='9' class='text-center' style='padding:20px;'>Tidak ada data order pada periode ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p style="font-size: 10px;"><i>Dicetak pada: <?= date("d-m-Y H:i") ?></i></p>
    </div>

</body>
</html>