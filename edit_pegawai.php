<?php
$page = "Data Pegawai";
require_once("./header.php");
if (isset($_GET["pegawai_id"])) {
    $id = $_GET["pegawai_id"];
    $sql = "SELECT * FROM `pegawai` WHERE `pegawai_id` = '$id'";
    $result = $koneksi->query($sql);
    if ($result->num_rows < 1) {
        header('location:./data_pegawai.php');
        exit();
    }
} else {
    header("location:./data_pegawai.php");
    exit();
}

$sql = "SELECT * FROM `pegawai` WHERE `pegawai`.`pegawai_id` = '$id'";

$result = $koneksi->query($sql);
while ($row = $result->fetch_assoc()) {
    $pegawai_id = $row['pegawai_id'];
    $pegawai_foto = $row['pegawai_foto'];
    $pegawai_rfid = $row['pegawai_rfid'];
    $pegawai_nama = $row['pegawai_nama'];
    $pegawai_nip = $row['pegawai_nip'];
    $pegawai_jeniskelamin = $row['pegawai_jeniskelamin'];
    $pegawai_lahir = $row['pegawai_lahir'];
    $pegawai_nomorhp = $row['pegawai_nomorhp'];
    $pegawai_alamat = $row['pegawai_alamat'];
    $pegawai_jabatan = $row['jabatan_id'];
}
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Edit Data Pegawai</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="./data_pegawai.php">Data Pegawai</a></li>
                <li class="breadcrumb-item active">Edit Data Pegawai</li>
            </ol>
            <!-- START MESSAGE -->
            <div id="response">
                <?php
                if (isset($_GET['msg']) && isset($_SERVER['HTTP_REFERER'])) {
                    if ($_GET['msg'] == 1 && $_SERVER['HTTP_REFERER']) {
                ?>
                <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                    <strong>Berhasil update data!</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php
                    }
                }
                ?>
            </div>
            <!-- END MESSAGE -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit mr-1"></i>
                    Edit Data Pegawai
                </div>
                <div class="card-body">
                    <form class="mb-5" action="./edit_pegawai_post.php" method="POST" id="appsform"
                        enctype="multipart/form-data">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="PegawaiRfid">RFID UID</label>
                            <div class="col-sm-8">
                                <input type="hidden" class="form-control" id="pegawaiID" name="pegawaiID"
                                    value="<?php echo $pegawai_id; ?>" autocomplete="off" required>
                                <input type="text" class="form-control" id="pegawaiRfid" name="pegawaiRfid"
                                    placeholder="Masukkan RFID UID" value="<?php echo $pegawai_rfid; ?>"
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiNIP">NIP</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="pegawaiNip" name="pegawaiNip"
                                    placeholder="Masukkan NIP" value="<?php echo $pegawai_nip; ?>" autocomplete="off"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiNama">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pegawaiNama" name="pegawaiNama"
                                    placeholder="Masukkan Nama Lengkap" value="<?php echo $pegawai_nama; ?>"
                                    autocomplete="off" minlength="2" maxlength="50" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiJabatan">Jabatan</label>
                            <div class="col-sm-8">
                                <select class="custom-select" id="pegawaiJabatan" name="pegawaiJabatan"
                                    autocomplete="off" required>
                                    <?php
                                        $sql = "SELECT * FROM `jabatan` ORDER BY `jabatan_id` ASC";
                                        $result = $koneksi->query($sql);

                                        if ($result->num_rows > 0) {
                                            echo '<option value="">- Pilih Jabatan -</option>';
                                            while ($row = $result->fetch_assoc()) {
                                                $jabatanId = $row['jabatan_id'];
                                                $jabatanNama = $row['jabatan_nama'];
                                                if ($pegawai_jabatan == $jabatanId) {
                                                    echo '<option value="' . $jabatanId . '" selected>' . $jabatanNama . '</option>';
                                                } else {
                                                    echo '<option value="' . $jabatanId . '">' . $jabatanNama . '</option>';
                                                }
                                            }
                                        } else {
                                            echo '<option value="">- Jabatan Tidak Ditemukan -</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiJK">Jenis Kelamin</label>
                            <div class="col-sm-8">
                                <select class="custom-select" id="pegawaiJK" name="pegawaiJK" autocomplete="off"
                                    required>
                                    <option value="">- Pilih Jenis Kelamin -</option>
                                    <option value="M" <?php echo ($pegawai_jeniskelamin == 'M') ? "selected" : "" ?>>
                                        Pria</option>
                                    <option value="F" <?php echo ($pegawai_jeniskelamin == 'F') ? "selected" : "" ?>>
                                        Wanita</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiTgl">Tanggal Lahir</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="pegawaiTgl" name="pegawaiTgl"
                                    value="<?php echo $pegawai_lahir; ?>" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiNohp">Nomor Telepon <small
                                    style="color:red">Contoh : 62822xxxx4496</small></label>
                            <div class="col-sm-8">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">+</div>
                                    </div>
                                    <input type="number" class="form-control" id="pegawaiNohp" name="pegawaiNohp"
                                        placeholder="Masukkan Nomor Telepon" value="<?php echo $pegawai_nomorhp; ?>"
                                        autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiAlamat">Alamat Lengkap</label>
                            <div class="col-sm-8">
                                <textarea type="text" class="form-control" id="pegawaiAlamat" name="pegawaiAlamat"
                                    placeholder="Masukkan Alamat Lengkap" minlength="4" maxlength="500"
                                    autocomplete="off" required><?php echo $pegawai_alamat; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiFoto">Foto pegawai</label>
                            <div class="col-sm-7">
                                <img src="./image/<?php echo $pegawai_foto; ?>"
                                    style="width: 120px;float: left;margin-bottom: 10px;">
                                <input type="file" class="form-control-file" id="pegawaiFoto" name="pegawaiFoto"
                                    autocomplete="off" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-8 ml-auto">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php
    require_once("./footer.php");
    ?>
    <script>
    $(document).ready(function() {
        $('#pegawaiRfid').focus();
        $("form#appsform").submit(function() {
            // Karena ada file tidak bisa pakai serialize
            // var postdata = $(this).serialize();
            var postdata = new FormData(this);
            var postaction = $(this).attr('action');
            $.ajax({
                type: "POST",
                url: postaction,
                timeout: false,
                data: postdata,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function(response) {
                    if (response.status) {
                        window.location =
                            "./edit_pegawai.php?pegawai_id=<?php echo $pegawai_id; ?>&msg=1";
                    } else {
                        $("#response").html(
                            '<div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            response.message + '</div>');
                    }
                },
                error: function(_, _, errorMessage) {
                    $("#response").html(
                        '<div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        errorMessage + '</div>');
                },
                beforeSend: function() {
                    $("#response").html(
                        '<div class="alert alert-warning alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Loading...</div>'
                    );
                }
            });
            return false;
        });
    });
    </script>