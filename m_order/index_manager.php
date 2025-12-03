<?php
//include '../../config.php'; // dimatikan sesuai file asli

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}

include '../../auth.php';

$master = "Order";
$dba = "order";
$ket = "| Nomor Hp - Alamat";
$ketnama = "Silahkan mengisi nama";

?>
 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"> 
 
<?php
include '../header.php';
include '../sidebar.php';
?>

<div class="content-wrapper">
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

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
            
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">
            Tambah
            </button>
              
            <a class="btn btn-success" href="tambah.php">Tambah Banyak</a>

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Master Data <?php echo $master; ?></h3>
              <i><p>Diurutkan berdasarkan order open pertama yang perlu di tindak lanjuti</p></i>
            </div>
            
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Order</th>
                  <th>Shipper</th>
                  <th>Consignee</th>
                  <th>Belum Bayar <small>❗</small></th>
                  <th>Pembayaran <small>❗</small></th>
                  <th>Sisa <small>❗</small></th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php
					$totalnilais = 0;   
					$totalbayars = 0;   
					$count = 1;
				   
                   $sql = $conn->prepare("SELECT * FROM `m_order` ORDER BY id DESC");
                   $sql->execute();
                   while($data=$sql->fetch()) {
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  
                  <?php
                    $statusList = [
                        0 => 'open', 1 => 'Termuat Container', 2 => 'Stack Depo Asal',
                        3 => 'Onboard', 4 => 'Ongoing', 5 => 'Arrive',
                        6 => 'Stack Depo Tujuan', 7 => 'Dooring'
                    ];
                    $status = isset($statusList[$data['status']]) ? $statusList[$data['status']] : 'tidak diketahui';
                    ?>
                    
                  <td>
                      <small style="background-color:yellow;">
                       <?php
                            $stmt = $conn->prepare("SELECT * FROM m_order_kat WHERE id = :order_kat_id");
                            $stmt->execute([':order_kat_id' => $data['order_kat_id']]);
                            $row = $stmt->fetch();
                            echo $row['nama'];
                        ?>
                      - 
                       <?php
                            $stmt = $conn->prepare("SELECT * FROM m_order_katsub WHERE id = :order_katsub_id");
                            $stmt->execute([':order_katsub_id' => $data['order_katsub_id']]);
                            $row = $stmt->fetch();
                            echo $row['nama'];
                        ?>
                        </small>
                      
                      <small><?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></small> - <?php echo $data['nama'];?> 
                  
                  <small
                  data-id="<?= $data['id'] ?>" 
                      data-no_bastb="<?= $data['no_bastb'] ?>"
                        data-container_seal="<?= $data['container_seal'] ?>"
                        data-container_size="<?= $data['container_size'] ?>"
                        data-loading_des="<?= $data['loading_des'] ?>"
                        data-commodity="<?= $data['commodity'] ?>"
                        data-vessel="<?= $data['vessel'] ?>"
                        data-voyage="<?= $data['voyage'] ?>"
                        data-conditi="<?= $data['conditi'] ?>"
                        data-discharging_date="<?= $data['discharging_date'] ?>"
                      data-status="<?= $data['status'] ?>" 
                      type="button" data-toggle="modal"
                  class="label pull-right bg-green btn_update" style="cursor: pointer;">Form</small>
                  
                  <small
                      data-idi="<?= $data['id'] ?>"
                      data-shipper_id="<?= $data['shipper_id'] ?>"
                      data-invoice_no="<?= $data['invoice_no'] ?>"
                        data-invoice_date="<?= $data['invoice_date'] ?>"
                        data-credit_terms="<?= $data['credit_terms'] ?>"
                        data-customer="<?= $data['customer'] ?>"
                        data-attn="<?= $data['attn'] ?>"
                        data-address="<?= $data['address'] ?>"
                        data-total_amount="<?= $data['total_amount'] ?>"
                        data-muncul_rek="<?= $data['muncul_rek'] ?>"
                        data-reke="<?= $data['rek_id'] ?>"
                        data-ttd_id="<?= $data['ttd_id'] ?>" 
                        data-cap_id="<?= $data['cap_id'] ?>"
                      type="button" data-toggle="modal"
                  class="label pull-right bg-yellow btn_invoice" style="cursor: pointer;">Invoice</small>

                  <b><small style="cursor: pointer;">Status : <?= $status ?></small></b>
                  </td>
                  
                  <td>
                        <?php
                        if ($data['shipper_id'] == '0') {
                            echo "<small>-- Belum --</small>";
                        } else {
                            $stmt = $conn->prepare("SELECT nama FROM m_user WHERE id = :id");
                            $stmt->execute([':id' => $data['shipper_id']]);
                            $row = $stmt->fetch();
                            echo htmlspecialchars($row['nama']);
                        }
                        ?>
                        <a style="cursor: pointer;" data-toggle="modal" data-target="#shipper" data-id="<?= $data['id'] ?>">Ubah</a>
                    </td>
                    
                    <td>
                        <?php
                        if ($data['consignee_id'] == '0') {
                            echo "<small>-- Belum --</small>";
                        } else {
                            $stmt = $conn->prepare("SELECT nama FROM m_user WHERE id = :id");
                            $stmt->execute([':id' => $data['consignee_id']]);
                            $row = $stmt->fetch();
                            echo htmlspecialchars($row['nama']);
                        }
                        ?>
                        <a style="cursor: pointer;" data-toggle="modal" data-target="#consignee" data-id="<?= $data['id'] ?>">Ubah</a>
                    </td>
                  
                  <?php
                  $stmt = $conn->prepare("SELECT SUM(nilai) AS total_nilai FROM m_kas WHERE stat = 4 AND order_id = :order_id");
                        $stmt->bindParam(':order_id', $data['id']);
                        $stmt->execute();
                        $row = $stmt->fetch();
                  ?>
                  
                  <td><small><?php echo "Rp " . number_format($row['total_nilai'], 0, ',', '.');?></small></td>
                  <?php $totalnilai = $row['total_nilai']; ?>
                  
                  <?php
                  $stmt = $conn->prepare("SELECT SUM(nilai) AS total_bayar FROM m_kas WHERE stat = 1 AND order_id = :order_id");
                        $stmt->bindParam(':order_id', $data['id']);
                        $stmt->execute();
                        $row = $stmt->fetch();
                  ?>
                  <td><small><?php echo "Rp " . number_format($row['total_bayar'], 0, ',', '.');?></small></td>
                  <?php $totalbayar = $row['total_bayar']; ?>
                  <?php $sisa = $totalnilai - $totalbayar?>
                  <td><small><?php echo "Rp " . number_format($sisa, 0, ',', '.');?></small></td>
                  
                  <td>
                      <small>
                      <a href="invoice.php?codx=<?php echo $data['codx']; ?>">Invoice</a> |
                      <a href="full.php?codx=<?php echo $data['codx']; ?>">Full</a> |
                      <a href="sum.php?codx=<?php echo $data['codx']; ?>">Sum</a> |
                      <a href="harga.php?codx=<?php echo $data['codx']; ?>">Harga</a> |
                      <a href="cetak.php?codx=<?php echo $data['codx']; ?>">Cetak</a> |
                      </small>
                      
                      <a href="list.php?codx=<?php echo $data['codx'];?>">List️</a>
                      
                        <?php if (!empty($data['notes'])): ?>
                            <a href="<?php echo $data['notes']; ?>">📝</a>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['notes_sortir'])): ?>
                            <a href="<?php echo $data['notes_sortir']; ?>">📓</a>
                        <?php endif; ?>
                      
                      <?php if (!empty($data['shipper_id'])): ?>
                      <button  
                      data-order_id="<?= $data['id'] ?>"
                      data-order_kat_id="<?= $data['order_kat_id'] ?>"
                      data-order_katsub_id="<?= $data['order_katsub_id'] ?>"
                      data-order_codx="<?= $data['codx'] ?>"
                      data-shipper_id="<?= $data['shipper_id'] ?>" 
                      data-consignee_id="<?= $data['consignee_id'] ?>" 
                      type="button" class="btn btn-light btn_tambahb" data-toggle="modal">➕</button>
                      <?php endif; ?>
                      
                    <a class="btn btn-light" onclick="return confirm('are you want deleting data')" href="../../controller/<?php echo $dba;?>_controller.php?op=hapus&id=<?php echo $data['id']; ?>">❌</a>
                  </td>
                </tr>

                <?php
                $totalnilais += $totalnilai;
                $totalbayars += $totalbayar;
                $sisa = $totalnilais - $totalbayars;
                $count=$count+1;
                } 
                ?>
                
                <b>Belum Bayar = <?= "Rp. ".number_format($totalnilais,0).",-" ?> </b><br>
                Sudah Bayar = <?= "Rp. ".number_format($totalbayars,0).",-" ?><br>
                Sisa = <?= "Rp. ".number_format($sisa,0).",-" ?><br>
                
                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Order</th>
                  <th>Shipper</th>
                  <th>Consignee</th>
                  <th>Total Biaya</th>
                  <th>Bayar</th>
                  <th>Sisa</th>
                  <th>Aksi</th>
                </tr>
                </tfoot>
              </table>
            </div>
            </div>
          </div>
        </div>
      </section>
    </div>

<div class="modal fade" id="tambah" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah <?php echo $master;?></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=tambah" method="post" enctype="multipart/form-data">
              
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <div class="form-group">
                  <label class="control-label">Tanggal:</label>
                  <input type="text" id="tanggal" class="form-control" name="created_at" value="<?php echo date('Y-m-d H:i:s'); ?>" />
                  <small style="color:red;">tahun-bulan-tgl jam</small>
                </div>
                <script>
                  flatpickr("#tanggal", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i:S",
                    time_24hr: true,
                    defaultDate: new Date()
                  });
                </script>

          <div class="form-group">
            <label class="col-form-label">Jenis Order :</label>
            <select style="width: 100%;" class="form-control" name="order_kat_id" id="order_kat_id">
                <option value="0">-- Pilih Jenis Order --</option>
                <?php
                $sql = $conn->prepare("SELECT * FROM m_order_kat");
                $sql->execute();
                while($data = $sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id']; ?>"><?php echo $data['nama']; ?></option>
                <?php } ?> 
            </select>
           </div>

            <div class="form-group">
                <label class="col-form-label">Sub Jenis Order :</label>
                <select style="width: 100%;" class="form-control" name="order_katsub_id" id="order_katsub_id">
                    <option value="0">-- Pilih Sub Jenis Order --</option>
                </select>
            </div>
            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            $('#order_kat_id').change(function() {
                var orderKatId = $(this).val();
                if(orderKatId) {
                    $.ajax({
                        type: 'POST',
                        url: 'get_suborder.php',
                        data: {order_kat_id: orderKatId},
                        success: function(response) {
                            $('#order_katsub_id').html(response);
                        }
                    });
                } else {
                    $('#order_katsub_id').html('<option value="">-- Pilih Sub Jenis Order --</option>');
                }
            });
            </script>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="upload" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="shipper" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=shipper" method="post" enctype="multipart/form-data">
          <input type="hidden" id="id_coba" name="id" />
          <div class="form-group">
            <label class="col-form-label">Shipper :</label>
            <select style="width: 100%;" id="idshipper" class="form-control" name="shipper_id" >
                <option value="0">-- Pilih Shipper --</option>
                <?php
                $sql = $conn->prepare("SELECT * FROM m_user WHERE level_id = 4 ORDER BY id DESC");
                $sql->execute();
                while($data=$sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                <?php } ?> 
            </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="consignee" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=consignee" method="post" enctype="multipart/form-data">
          <input type="hidden" id="id_coba" name="id" />
          <div class="form-group">
            <label class="col-form-label">Consignee :</label>
            <select style="width: 100%;" id="idconsignee" class="form-control" name="consignee_id" >
                <option value="0">-- Pilih Consignee --</option>
                <?php
                $sql = $conn->prepare("SELECT * FROM m_user WHERE level_id = 3 ORDER BY id DESC");
                $sql->execute();
                while($data=$sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                <?php } ?> 
            </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <form id="form-edit-transaksi-masuk">
              <div class="modal-body">
                  <div class="form-group">
                    <input type="hidden" id="id_edit" name="id" />
                  
                    <div class="form-group">
                      <label class="control-label">Nomor BASTB :</label>
                      <input type="text" class="form-control" name="no_bastb" id="no_bastb_edit" placeholder="Silahkan isi Nomor BASTB" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Container / Seal :</label>
                      <input type="text" class="form-control" name="container_seal" id="container_seal_edit" placeholder="Silahkan isi Container / Seal" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Container Size :</label>
                      <input type="text" class="form-control" name="container_size" id="container_size_edit" placeholder="Silahkan isi Container Size" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Loading / Destination :</label>
                      <input type="text" class="form-control" name="loading_des" id="loading_des_edit" placeholder="Silahkan isi Loading / Destination" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Commodity :</label>
                      <input type="text" class="form-control" name="commodity" id="commodity_edit" placeholder="Silahkan isi Commodity" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Vessel :</label>
                      <input type="text" class="form-control" name="vessel" id="vessel_edit" placeholder="Silahkan isi Vessel" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Voyage :</label>
                      <input type="text" class="form-control" name="voyage" id="voyage_edit" placeholder="Silahkan isi Voyage" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Condition :</label>
                      <input type="text" class="form-control" name="conditi" id="conditi_edit" placeholder="Silahkan isi Condition" />
                      <small style="color:red">* isi sesuai nomor yang ada dibawah </small>
                      
                      <table style="border: 1px solid black; border-collapse: collapse; width: 100%; color: black;" class="table table-bordered table-striped">
                        <thead><tr><th>No</th><th>Nama</th><th>Des</th></tr></thead>
                        <tbody>
                        <?php
                           $count = 1;
                           $sql = $conn->prepare("SELECT * FROM `m_condition`");
                           $sql->execute();
                           while($data=$sql->fetch()) {
                        ?>
                          <tr>
                          <td><?php echo $count; ?></td>
                          <td><?php echo $data['nama'];?></td>
                          <td><?php echo $data['des'];?></td>
                          </tr>
                        <?php $count++; } ?>
                         </tbody>
                      </table>
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Discharging date :</label>
                      <input type="text" class="form-control" name="discharging_date" id="discharging_date_edit" placeholder="Silahkan isi Discharging date" />
                    </div>
                    
    			  <div class="form-group">
                    <label class="control-label">Status : </label>        
    					<input type="text" class="form-control" id="status_edit" name="status" />
    					<small>0. Open, 1. Termuat Container, 2. Stack Depo Asal, 3. Onboard, 4. Ongoing, 5. Arrive, 6. Stack Depo Tujuan, 7. Dooring</small>
                  </div>
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save-update">Save changes</button>
              </div>
          </form>
        </div>
      </div>
</div>
    
<div class="modal fade" id="modalinvoice" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Invoice</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <form id="form-invoice">

              <div class="modal-body">
                  <div class="form-group">
                    <input type="hidden" id="idi_edit" name="id" />
                    <input type="hidden" id="shipper_id_edit" name="shipper_id" />
                    
                    <div class="form-group">
                      <label class="control-label">Invoice No :</label>
                      <input type="text" class="form-control" name="invoice_no" id="invoice_no_edit" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Invoice Date :</label>
                      <input type="text" class="form-control" name="invoice_date" id="invoice_date_edit" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Credit Terms :</label>
                      <input type="text" class="form-control" name="credit_terms" id="credit_terms_edit" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Customer :</label>
                      <input type="text" class="form-control" name="customer" id="customer_edit" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Attn :</label>
                      <input type="text" class="form-control" name="attn" id="attn_edit" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Address :</label>
                      <input type="text" class="form-control" name="address" id="address_edit" />
                    </div>
                    
                    <div class="form-group">
                    <label class="control-label" >Total Amount : <label id="totalm" style="margin-top: 5px; display: block; color: black;"></label> </label>         
    					<input type="text" class="form-control" name="total_amount" id="total_amount_edit" value="0" />
                     </div>
                     
                     <script>
                        const input = document.getElementById("total_amount_edit");
                        const label = document.getElementById("totalm");
                        input.addEventListener("input", function () {
                            let rawValue = this.value.replace(/\D/g, '');
                            if (rawValue === "") { label.textContent = ""; return; }
                            let formatted = parseInt(rawValue).toLocaleString("id-ID");
                            label.textContent = formatted;
                        });
                    </script>
                    
                    <div class="form-group">
                      <label class="control-label">Munculkan Rek :</label>
                      <input type="text" class="form-control" name="muncul_rek" id="muncul_rek_edit" />
                      <small style="color:red;">0. Tidak 1. Aktif</small>
                    </div>
                    
                    <?php
                    // Fetch Data untuk Dropdown
                    $stmt = $conn->prepare("SELECT * FROM m_rek");
                    $stmt->execute();
                    $reks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $stmt_ttd = $conn->prepare("SELECT * FROM m_tanda_tangan");
                    $stmt_ttd->execute();
                    $ttds = $stmt_ttd->fetchAll(PDO::FETCH_ASSOC);

                    $stmt_cap = $conn->prepare("SELECT * FROM m_cap");
                    $stmt_cap->execute();
                    $caps = $stmt_cap->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <div class="form-group">
                        <label class="control-label">Pilih Rekening :</label>
                        <select id="rek_select" class="form-control" style="width: 100%;">
                            <option value="">-- Pilih Rekening --</option>
                            <?php foreach ($reks as $rek): ?>
                                <option value="<?php echo ($rek['id']); ?>"><?php echo htmlspecialchars($rek['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Pilih Tanda Tangan :</label>
                        <select id="ttd_select" class="form-control" style="width: 100%;">
                            <option value="">-- Pilih Tanda Tangan --</option>
                            <?php foreach ($ttds as $ttd): ?>
                                <option value="<?php echo ($ttd['id']); ?>"><?php echo htmlspecialchars($ttd['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Pilih Cap :</label>
                        <select id="cap_select" class="form-control" style="width: 100%;">
                            <option value="">-- Pilih Cap --</option>
                            <?php foreach ($caps as $cap): ?>
                                <option value="<?php echo ($cap['id']); ?>"><?php echo htmlspecialchars($cap['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="hidden" class="form-control" name="rek_id" id="reke_edit" value="" />
                        <input type="hidden" class="form-control" name="ttd_id" id="ttd_id_edit" value="" />
                        <input type="hidden" class="form-control" name="cap_id" id="cap_id_edit" value="" />
                    </div>
                    
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-invoice">Save changes</button>
              </div>
          </form>
        </div>
      </div>
</div>
    
<div class="modal fade" id="modalshipper" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ganti Shipper</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=shipper" method="post" enctype="multipart/form-data">
          <input type="text" id="ids_edit" name="id" style="display:none;" />
          <div class="form-group">
            <label class="col-form-label">Shipper :</label>
            <select style="width: 100%;" class="form-control" name="shipper_id" >
                <?php
                $sql = $conn->prepare("SELECT * FROM m_user WHERE level_id = 4 ORDER BY id DESC");
                $sql->execute();
                while($data=$sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                <?php } ?> 
            </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
  </div>
</div>    
    
<div class="modal fade" id="modaltambahb" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pembayaran </h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <form action="tambahb.php" method="post" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="form-group">
                   <input type="hidden" class="form-control" id="order_id_edit" name="order_id" />
                   <input type="hidden" class="form-control" id="order_kat_id_edit" name="order_kat_id" />
                   <input type="hidden" class="form-control" id="order_katsub_id_edit" name="order_katsub_id" />
                   <input type="hidden" class="form-control" id="order_codx_edit" name="order_codx" />
                   <input type="hidden" class="form-control" id="shippert_id_edit" name="shipper_id" />
                   <input type="hidden" class="form-control" id="consignee_id_edit" name="consignee_id" />
                  	
                  <div class="form-group">
                    <label class="control-label" >Deskripsi Pembayaran : </label>         
    					<input type="text" class="form-control" name="nama" />
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Tanggal : </label>         
    					<input type="text" class="form-control" name="created_at" value="<?php echo date("Y-m-d H:i:s");?>" />
                  </div>
                  
                  <div class="form-group">
                    <label class="col-form-label">Kategori Kas :</label>
                    <select style="width: 100%;" class="form-control" name="kat_kas_id" >
                        <option value="0">-- Pilih --</option>
                        <?php
                        $sql = $conn->prepare("SELECT * FROM m_kategori_kas ORDER BY id DESC");
                        $sql->execute();
                        while($data=$sql->fetch()) {
                        ?>  
                        <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                        <?php } ?> 
                    </select>
                    <small style="color:green;">bisa di pilih bisa di kosongkan</small>
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Rupiah : <label id="nilailabel" style="margin-top: 5px; display: block; color: black;"></label> </label>         
    					<input type="text" id="nilai_edit" class="form-control" name="nilai" value="0" />
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Stat : </label>         
    					<select class="form-control" name="stat">
                          <option value="1" selected>Masuk</option>
                          <option value="4">Piutang</option>
                        </select>
    					<small style="color:red;">1.Masuk 2. Keluar 3. Deposit 4. Piutang 5. Piutang Lunas </small><br>
    					<small style="color:green;">informasi bahwa hanya akan gunakan status 1 atau 4</small>
                  </div>
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
          </form>
        </div>
      </div>
</div>
    
<script>
    function updateFormattedLabel() {
    const input = document.getElementById("nilai_edit");
    const label = document.getElementById("nilailabel");
    let rawValue = input.value.replace(/[^0-9,]/g, '').replace(',', '.');
    if (!rawValue || isNaN(rawValue)) { label.textContent = ""; return; }
    const number = parseFloat(rawValue);
    const formatted = number.toLocaleString('id-ID', { maximumFractionDigits: 20 });
    label.textContent = formatted;
    }
    
    document.getElementById("nilai_edit").addEventListener("input", updateFormattedLabel);
    
    $('#modaltambahb').on('shown.bs.modal', function () {
        updateFormattedLabel();
    });
</script>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
     $(document).ready(function(){
         
        // Inisialisasi Select2 Umum
        $('#idshipper').select2({ dropdownParent: $('#shipper') });
        $('#idconsignee').select2({ dropdownParent: $('#consignee') });
        
        // --- PERBAIKAN INISIALISASI SELECT2 UNTUK INVOICE (FITUR SEARCH) ---
        // Menggunakan dropdownParent agar search box bisa diklik di dalam modal
        $('#rek_select').select2({
            dropdownParent: $('#modalinvoice')
        });
        $('#ttd_select').select2({
            dropdownParent: $('#modalinvoice')
        });
        $('#cap_select').select2({
            dropdownParent: $('#modalinvoice')
        });

        // --- LISTENER PERUBAHAN NILAI SELECT KE HIDDEN INPUT ---
        $('#rek_select').on('change', function() {
            $('#reke_edit').val($(this).val());
        });
        $('#ttd_select').on('change', function() {
            $('#ttd_id_edit').val($(this).val());
        });
        $('#cap_select').on('change', function() {
            $('#cap_id_edit').val($(this).val());
        });
        // -----------------------------------------------------------------

        $('#btn-save-update').click(function(){
           $.ajax({
               url: "edit.php", type : 'post', data : $('#form-edit-transaksi-masuk').serialize(),
               success: function(data){
                   var res = JSON.parse(data);
                   if (res.code == 200){ alert('Success Update'); location.reload(); }
               }
           }) 
        });
        
        $('#btn-invoice').click(function(){
           $.ajax({
               url: "editi.php", type : 'post', data : $('#form-invoice').serialize(),
               success: function(data){
                   try {
                       var res = JSON.parse(data);
                       if (res.code == 200){ 
                           alert('Data Invoice Berhasil Diupdate'); 
                           location.reload(); 
                       } else {
                           alert('Gagal Update: ' + (res.message || 'Unknown error'));
                       }
                   } catch (e) {
                       console.error("JSON Parse Error:", e);
                       console.log("Raw Response:", data);
                       alert('Error parsing server response. Check console for details.');
                   }
               },
               error: function(xhr, status, error) {
                   console.error("AJAX Error:", status, error);
                   alert('Terjadi kesalahan saat menghubungi server.');
               }
           }) 
        });
        
        $('#btn-shipper').click(function(){
          $.ajax({
              url: "edit_shipper.php", type : 'post', data : $('#form-shipper').serialize(),
              success: function(data){
                  var res = JSON.parse(data);
                  if (res.code == 200){ alert('Success Update'); location.reload(); }
              }
          }) 
        });
        
        $('#btn-save-tambahb').click(function(){
          $.ajax({
              url: "tambahb.php", type : 'post', data : $('#form-tambahb').serialize(),
              success: function(data){
                  var res = JSON.parse(data);
                  if (res.code == 200){ alert('Success Update'); location.reload(); }
              }
          }) 
        });
        
        $(document).on('click','.btn_update',function(){
            $("#id_edit").val($(this).attr('data-id'));
            $("#no_bastb_edit").val($(this).attr('data-no_bastb'));
            $("#container_seal_edit").val($(this).attr('data-container_seal'));
            $("#container_size_edit").val($(this).attr('data-container_size'));
            $("#loading_des_edit").val($(this).attr('data-loading_des'));
            $("#commodity_edit").val($(this).attr('data-commodity'));
            $("#vessel_edit").val($(this).attr('data-vessel'));
            $("#voyage_edit").val($(this).attr('data-voyage'));
            $("#conditi_edit").val($(this).attr('data-conditi'));
            $("#discharging_date_edit").val($(this).attr('data-discharging_date'));
            $("#status_edit").val($(this).attr('data-status'));
            $('#modalEdit').modal('show');
        });
        
        // LOGIKA SAAT TOMBOL INVOICE DIKLIK (Populate Data)
        $(document).on('click','.btn_invoice',function(){
            console.log("Membuka Modal Invoice");
            $("#idi_edit").val($(this).attr('data-idi'));
            $("#shipper_id_edit").val($(this).attr('data-shipper_id'));
            $("#invoice_no_edit").val($(this).attr('data-invoice_no'));
            $("#invoice_date_edit").val($(this).attr('data-invoice_date'));
            $("#credit_terms_edit").val($(this).attr('data-credit_terms'));
            $("#customer_edit").val($(this).attr('data-customer'));
            $("#attn_edit").val($(this).attr('data-attn'));
            $("#address_edit").val($(this).attr('data-address'));
            $("#total_amount_edit").val($(this).attr('data-total_amount'));
            $("#muncul_rek_edit").val($(this).attr('data-muncul_rek'));
            
            // Ambil ID dari tombol
            var rekId = $(this).attr('data-reke');
            var ttdId = $(this).attr('data-ttd_id');
            var capId = $(this).attr('data-cap_id');

            // Set ke Hidden Input
            $("#reke_edit").val(rekId);
            $("#ttd_id_edit").val(ttdId);
            $("#cap_id_edit").val(capId);
            
            // Set Value ke Select2 dan trigger change agar tampilan update
            $('#rek_select').val(rekId).trigger('change');
            $('#ttd_select').val(ttdId).trigger('change');
            $('#cap_select').val(capId).trigger('change');
            
            $('#modalinvoice').modal('show');
        });
        
        $(document).on('click','.btn_shipper',function(){
            $("#ids_edit").val($(this).attr('data-ids'));
            $('#modalshipper').modal('show');
        });
        
        $(document).on('click','.btn_tambahb',function(){
            $("#id_edit").val($(this).attr('data-id'));
            $("#order_id_edit").val($(this).attr('data-order_id'));
            $("#order_kat_id_edit").val($(this).attr('data-order_kat_id'));
            $("#order_katsub_id_edit").val($(this).attr('data-order_katsub_id'));
            $("#order_codx_edit").val($(this).attr('data-order_codx'));
            $("#shippert_id_edit").val($(this).attr('data-shipper_id'));
            $("#consignee_id_edit").val($(this).attr('data-consignee_id'));
            $('#modaltambahb').modal('show');
        });

        // Initialize DataTable
        $('#example1').DataTable({
          'paging': true, 'lengthChange': true, 'searching': true, 'ordering': true,
          'info': true, 'scrollX': true, 'autoWidth': false
        })
    });

    // Helper untuk modal
    $('#shipper').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); var id = button.data('id'); var modal = $(this); modal.find('#id_coba').val(id);
    });
    $('#consignee').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); var id = button.data('id'); var modal = $(this); modal.find('#id_coba').val(id);
    });
</script>

</body>
</html>