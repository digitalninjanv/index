<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}

// if( $_SESSION['level_id'] == "1" ){
// }else{
//   echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
// }

include '../../auth.php';

$master = "Kas Keluar";
$dba = "kas";
$ket = "";
$ketnama = "Silahkan mengisi nama";

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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">
            Tambah
              </button>
          <!-- /.box -->

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Master Data <?php echo $master; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Created</th>
                  <th>Nama</th>
                  <th>Deskripsi <?php echo $ket; ?></th>
                  <th>Nilai</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php
                   $count = 1;
				   
                   $sql = $conn->prepare("SELECT * FROM `m_kas` WHERE shipper_id = 0 AND stat = 2 AND `created_at` BETWEEN '".date("Y-m-01 00:00:01")."' AND '".date("Y-m-t 23:59:59")."' ORDER BY id DESC");
                   $sql->execute();
                   while($data=$sql->fetch()) {
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></td>
                  <td><?php echo $data['nama'];?></td>
                  <td><?php echo $data['des'];?></td>
                  <td><?php echo "Rp. ".number_format($data['nilai'], 0). ",-"; ?></td>
                  <?php 
                  $nilai = $data['nilai'];
                  
                  if (date("Y-m-d", strtotime($data['created_at'])) == date("Y-m-d")) {
                    $pengeluaranHariIni += $data['nilai'];
                  }
                  
                  ?>
                  
                  <td>
                  <button 
                      data-id="<?= $data['id'] ?>" 
                      data-created_at="<?= $data['created_at'] ?>" 
                      data-nama="<?= $data['nama'] ?>" 
                      data-des="<?= $data['des']?>"
                      data-nilai="<?= $data['nilai']?>"
                      type="button" class="btn btn-light btn_update" data-toggle="modal">✎</button>
                    <a class="btn btn-light" onclick="return confirm('are you want deleting data')" href="../../controller/<?php echo $dba;?>_controller.php?op=hapusk&id=<?php echo $data['id']; ?>">❌</a>
                  </td>
                </tr>

                <?php
                $totals += $nilai;
                
                
                
                
                $count=$count+1;
                } 
                ?>
                Keluar Harian = <?= "Rp. ".number_format($pengeluaranHariIni,0).",-" ?> <br>
                <b>Keluar Bulanan = <?= "Rp. ".number_format($totals,0).",-" ?></b> <br>

                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Created</th>
                  <th>Nama</th>
                  <th>Deskripsi <?php echo $ket; ?></th>
                  <th>Nilai</th>
                  <th>Aksi</th>
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

   <!-- Modal Tambah -->
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah <?php echo $master;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="../../controller/<?php echo $dba;?>_controller.php?op=keluar" method="post"  enctype="multipart/form-data">
              
          <div class="form-group">
            <label class="control-label" >Tanggal : </label>       
				<input type="text" class="form-control" name="created_at" value="<?php echo date("Y-m-d H:i:s");?>" />
				<small style="color:red;">tahun-bulan-tgl jam</small>
          </div>

          
          <div class="form-group">
            <label class="col-form-label">Nama :</label>
            <input type="text" class="form-control" id="myInput" name="nama" placeholder="<?php echo $ketnama. " ".$master. " ..."; ?>" />
          </div>
          
          <div class="form-group">
            <label class="control-label" >Deskripsi : </label>       
				<input type="text" class="form-control" name="des" placeholder="Silahkan Mengisi <?php echo $ket. " ..."; ?>"/>
          </div>
          
          <div class="form-group">
                    <label class="col-form-label">Kategori Kas :</label>
                    <select style="width: 100%;" class="form-control" name="kat_kas_id" >
                        <?php
                        $sql = $conn->prepare("SELECT * FROM m_kategori_kas ORDER BY id DESC");
                        $sql->execute();
                        while($data=$sql->fetch()) {
                        ?>  
                        <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                        <?php } ?> 
                    </select>
                  </div>

          <div class="form-group">
            <label class="control-label" >Rupiah :  <label id="formattedLabel" style="margin-top: 5px; display: block; color: black;"></label> </label>       
				<input type="number" id="nilaiInput" class="form-control"  name="nilai" />
          </div>
          
          <div class="form-group">
                    <label class="control-label" >Stat : </label>         
    					<input type="text" class="form-control" name="stat" value="2" />
    					<small style="color:red;">1.Masuk 2. Keluar 3. Deposit </small>
                  </div>
          
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button  type="submit" name="upload" type="button" class="btn btn-primary" >Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>

    
<script>
    const input = document.getElementById("nilaiInput");
    const label = document.getElementById("formattedLabel");

    input.addEventListener("input", function () {
        // Ambil hanya angka
        let rawValue = this.value.replace(/\D/g, '');

        if (rawValue === "") {
            label.textContent = "";
            return;
        }

        // Ubah ke format Indonesia tanpa desimal
        let formatted = parseInt(rawValue).toLocaleString("id-ID");

        label.textContent = formatted;
    });
</script>

<!-- Modal Edit -->
</div>
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="form-edit-transaksi-masuk">

              <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" id="id_edit" name="id" />
                    
                    <div class="form-group">
                    <label class="control-label" >Tanggal : </label>       
        				<input type="text" class="form-control" id="created_at_edit" name="created_at"/>
        				<small style="color:red;">tahun-bulan-tgl jam</small>
                  </div>
                    
    			        <div class="form-group">
                    <label class="control-label" >Nama : </label>        
    					      <input type="text" class="form-control" id="nama_edit" name="nama" />
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Deskripsi <?php echo $ket; ?> : </label>         
                    <input type="text" class="form-control" id="des_edit" name="des" />
                  </div>

                  <div class="form-group">
                    <label class="control-label" >Nilai : <label id="nilailabel" style="margin-top: 5px; display: block; color: black;"></label></label>         
                    <input type="text" class="form-control" id="nilai_edit" name="nilai" />
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
    
    <script>
    function updateFormattedLabel() {
    const input = document.getElementById("nilai_edit");
    const label = document.getElementById("nilailabel");

    let rawValue = input.value.replace(/[^0-9,]/g, '').replace(',', '.');

    if (!rawValue || isNaN(rawValue)) {
        label.textContent = "";
        return;
    }

   
    const number = parseFloat(rawValue);

   
    const formatted = number.toLocaleString('id-ID', {
        maximumFractionDigits: 20 
    });


    label.textContent = formatted;
    }
    
    document.getElementById("nilai_edit").addEventListener("input", updateFormattedLabel);
    
    $('#modalEdit').on('shown.bs.modal', function () {
        updateFormattedLabel();
    });


    </script>



   
  
  <?php
  include '../footer.php';
  ?>

<script type="text/javascript">
     $(document).ready(function(){
        
        $('#btn-save-update').click(function(){
           $.ajax({
               url: "edit.php",
               type : 'post',
               data : $('#form-edit-transaksi-masuk').serialize(),
               success: function(data){
                   var res = JSON.parse(data);
                   if (res.code == 200){
                       alert('Success Update');
                       location.reload();
                   }
               }
           }) 
        });
        
        $(document).on('click','.btn_update',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            $("#created_at_edit").val($(this).attr('data-created_at'));
            $("#nama_edit").val($(this).attr('data-nama'));
            $("#des_edit").val($(this).attr('data-des'));
            $("#nilai_edit").val($(this).attr('data-nilai'));
            
            updateFormattedLabel(); // panggil setelah isi nilai
            
            $('#edit').modal('show');
        });
    });

    $(document).ready(function() {
      $('#tambah').on('shown.bs.modal', function() {
        $('#myInput').trigger('focus');
      });

      $('#edit').on('shown.bs.modal', function() {
        $('#nama_edit').trigger('focus');
      });


    });
  </script> 
  
    

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
