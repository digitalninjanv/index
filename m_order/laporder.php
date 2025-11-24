<?php
// --- 1. KONFIGURASI & SESSION ---
include '../../config.php'; 

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email']) == 0) {
    header('Location: ../../index.php');
    exit;
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
    if(count($pecahkan) == 3) {
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
    return $tanggal;
}

// Helper format uang
function format_uang($angka) {
    if($angka == 0 || empty($angka)) return '-';
    return number_format($angka, 0, ',', '.');
}

// --- 4. QUERY DATA DENGAN JOIN ---
try {
    $sql = "SELECT 
                o.*,
                k.nama AS nama_kategori_status,
                s.nama AS nama_shipper,
                c.nama AS nama_consignee
            FROM m_order o
            LEFT JOIN m_order_kat k ON o.order_kat_id = k.id
            LEFT JOIN m_user s ON o.shipper_id = s.id
            LEFT JOIN m_user c ON o.consignee_id = c.id
            WHERE o.created_at BETWEEN :awal AND :akhir
            ORDER BY o.created_at ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':awal', $start_date);
    $stmt->bindParam(':akhir', $end_date);
    $stmt->execute();
    $data_order = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error Query: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Order Detail</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px; 
            color: #000;
            background-color: #fff;
            margin: 20px;
        }

        /* --- STYLE NAVIGASI (HILANG SAAT PRINT) --- */
        .no-print {
            background: #f4f4f4;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .filter-form { display: flex; align-items: center; gap: 10px; }
        .form-input { padding: 5px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px;}
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

        /* --- CONTAINER SCROLL --- */
        .table-scroll {
            width: 100%;
            overflow-x: auto; /* Membuat scroll horizontal */
            border: 1px solid #000; /* Opsional: batas luar */
            margin-bottom: 10px;
        }

        /* --- STYLE TABEL --- */
        table.data-table {
            min-width: 2000px; /* Memaksa tabel melebar agar scroll muncul */
            border-collapse: collapse;
            white-space: nowrap; /* Mencegah teks turun ke bawah (wrap) */
        }
        
        table.data-table th {
            border: 1px solid #000;
            padding: 6px;
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        table.data-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }

        /* Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .bg-yellow { background-color: #fffacd; } /* Warna background untuk profit/penting */

        /* SETTING PRINT PAGE */
        @media print {
            @page { 
                size: A4 landscape; 
                margin: 5mm; 
            }
            body { margin: 0; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            
            /* Saat print, scroll hilang dan tabel mungkin terpotong jika kertas tidak cukup.
               Biasanya browser akan mengecilkan skala (scale to fit). */
            .table-scroll {
                overflow-x: visible;
                border: none;
            }
            table.data-table th {
                background-color: #e0e0e0 !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div class="filter-form">
            <a href="../admin/" class="btn btn-gray">&laquo; Kembali</a>
            <form method="GET" action="" style="display:flex; gap:10px; align-items:center; margin-left: 15px;">
                <label>Periode:</label>
                <input type="date" name="tgl_awal" class="form-input" value="<?= $tgl_awal ?>">
                <span>s/d</span>
                <input type="date" name="tgl_akhir" class="form-input" value="<?= $tgl_akhir ?>">
                <button type="submit" class="btn btn-blue">Tampilkan</button>
            </form>
        </div>
        <button onclick="window.print()" class="btn btn-green"> Cetak / PDF</button>
    </div>

    <div class="report-header">
        <div class="report-title">LAPORAN PROFITABILITAS ORDER</div>
        <div class="report-subtitle">
            Periode: <?= tanggal_indo($tgl_awal) ?> - <?= tanggal_indo($tgl_akhir) ?>
        </div>
        <div class="header-line"></div>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="30">NO</th>
                    <th>TANGGAL</th>
                    <th>KAPAL</th>
                    <th>VOYAGE</th>
                    <th>STATUS</th>
                    <th>NO CONTAINER</th>
                    <th>SEAL</th>
                    <th>SIZE</th>
                    <th>COMMODITY</th>
                    <th>SHIPPER</th>
                    <th>CONSIGNEE</th>
                    
                    <th>SEAL (Biaya)</th>
                    <th>TRUCK MUAT</th>
                    <th>THC MUAT</th>
                    <th>O/F</th>
                    <th>LSS</th>
                    <th>THC BONGKAR</th>
                    <th>STRIP/LO-LO</th>
                    <th>TRUCK BONGKAR</th>
                    <th>BURUH</th>
                    
                    <th style="background-color: #ddd;">TOTAL BIAYA</th>
                    <th style="background-color: #ddd;">HARGA (JUAL)</th>
                    <th style="background-color: #4CAF50; color: white;">PROFIT</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(count($data_order) > 0) {
                    $no = 1;
                    $grand_total_profit = 0;

                    foreach($data_order as $row): 
                        $tgl_buat = date('d-m-Y', strtotime($row['created_at']));

                        // --- PERHITUNGAN ---
                        // Mengambil nilai, jika null/kosong dianggap 0
                        $biaya_seal = $row['harga_seal'] ?? 0;
                        $biaya_tm   = $row['harga_truck_muat'] ?? 0;
                        $biaya_thcm = $row['harga_thc_muat'] ?? 0;
                        $biaya_of   = $row['harga_of'] ?? 0;
                        $biaya_lss  = $row['harga_lss'] ?? 0;
                        $biaya_thcb = $row['harga_thc_bongkar'] ?? 0;
                        $biaya_str  = $row['harga_strip'] ?? 0;
                        $biaya_tb   = $row['harga_truck_bongkar'] ?? 0;
                        $biaya_brh  = $row['harga_buruh'] ?? 0;

                        // Hitung Total Biaya
                        $total_expenses = $biaya_seal + $biaya_tm + $biaya_thcm + $biaya_of + $biaya_lss + $biaya_thcb + $biaya_str + $biaya_tb + $biaya_brh;

                        // Harga Jual (Total Amount)
                        $harga_jual = $row['total_amount'] ?? 0;

                        // Hitung Profit
                        $profit = $harga_jual - $total_expenses;
                        
                        $grand_total_profit += $profit;
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= $tgl_buat ?></td>
                    <td><?= htmlspecialchars($row['vessel'] ?? '-') ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['voyage'] ?? '-') ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['nama_kategori_status'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['no_bastb'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['container_seal'] ?? '-') ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['container_size'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['commodity'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_shipper'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_consignee'] ?? '-') ?></td>
                    
                    <td class="text-right"><?= format_uang($biaya_seal) ?></td>
                    <td class="text-right"><?= format_uang($biaya_tm) ?></td>
                    <td class="text-right"><?= format_uang($biaya_thcm) ?></td>
                    <td class="text-right"><?= format_uang($biaya_of) ?></td>
                    <td class="text-right"><?= format_uang($biaya_lss) ?></td>
                    <td class="text-right"><?= format_uang($biaya_thcb) ?></td>
                    <td class="text-right"><?= format_uang($biaya_str) ?></td>
                    <td class="text-right"><?= format_uang($biaya_tb) ?></td>
                    <td class="text-right"><?= format_uang($biaya_brh) ?></td>

                    <td class="text-right bg-yellow text-bold"><?= format_uang($total_expenses) ?></td>
                    <td class="text-right bg-yellow text-bold"><?= format_uang($harga_jual) ?></td>
                    <td class="text-right text-bold" style="color: <?= $profit < 0 ? 'red' : 'green' ?>;">
                        <?= format_uang($profit) ?>
                    </td>
                </tr>
                <?php 
                    endforeach; 
                ?>
                <tr>
                    <td colspan="21" class="text-right text-bold" style="padding: 10px;">TOTAL PROFIT PERIODE INI:</td>
                    <td class="text-right text-bold" style="padding: 10px; font-size: 12px; background-color: #e0e0e0;">Rp. <?= format_uang($grand_total_profit) ?></td>
                </tr>
                <?php
                } else {
                    echo "<tr><td colspan='22' class='text-center' style='padding:20px;'>Tidak ada data pada periode ini.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div style="font-size: 10px; color: #666; margin-top: 5px;">* Geser tabel ke kanan untuk melihat detail biaya dan profit.</div>

    <div style="margin-top: 20px; text-align: right;">
        <p style="font-size: 9px;"><i>Dicetak pada: <?= date("d-m-Y H:i") ?></i></p>
    </div>

</body>
</html>