<?php
$page = "Data Pegawai";
require_once("./header.php");
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Tambah Data Pegawai</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="./data_pegawai.php">Data Pegawai</a></li>
                <li class="breadcrumb-item active">Tambah Data Pegawai</li>
            </ol>
            
            <div id="response"></div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-plus mr-1"></i>
                    Formulir Tambah Data Pegawai
                </div>
                <div class="card-body">
                    <form class="mb-5" action="./tambah_pegawai_post.php" method="POST" id="appsform" enctype="multipart/form-data">
                        
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiRfid">RFID UID</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pegawaiRfid" name="pegawaiRfid" placeholder="Tempelkan kartu baru pada alat..." autocomplete="off" required readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="rfid-status">
                                            <i class="fas fa-sync-alt fa-spin"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiNip">NIP</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="pegawaiNip" name="pegawaiNip"
                                    placeholder="Masukkan NIP" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiNama">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pegawaiNama" name="pegawaiNama"
                                    placeholder="Masukkan Nama Lengkap" autocomplete="off" minlength="2" maxlength="50"
                                    required>
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
                                                echo '<option value="' . $jabatanId . '">' . htmlspecialchars($jabatanNama) . '</option>';
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
                                    <option value="M">Pria</option>
                                    <option value="F">Wanita</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiTgl">Tanggal Lahir</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="pegawaiTgl" name="pegawaiTgl"
                                    autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="PegawaiNohp">Nomor Telepon <small
                                    style="color:red">Contoh : 62822xxxx4496</small></label>
                            <div class="col-sm-8">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">+</div>
                                    </div>
                                    <input type="number" class="form-control" id="pegawaiNohp" name="pegawaiNohp"
                                        placeholder="Masukkan Nomor Telepon" autocomplete="off" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiAlamat">Alamat Lengkap</label>
                            <div class="col-sm-8">
                                <textarea type="text" class="form-control" id="pegawaiAlamat" name="pegawaiAlamat"
                                    placeholder="Masukkan Alamat Lengkap" minlength="4" maxlength="500"
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="pegawaiFoto">Foto Pegawai</label>
                            <div class="col-sm-8">
                                <input type="file" class="form-control-file" id="pegawaiFoto" name="pegawaiFoto"
                                    autocomplete="off" accept="image/*" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-8 ml-auto">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" id="reset" class="btn btn-danger">Reset</button>
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
        let rfidInterval;

        // Fungsi untuk mendapatkan UID RFID dari server
        function getRfidUid() {
            $.ajax({
                url: 'action.php?do=get_rfid_code',
                type: 'GET',
                success: function(data) {
                    if (data && data.trim().length > 0) {
                        $('#pegawaiRfid').val(data.trim());
                        $('#rfid-status').html('<i class="fas fa-check-circle text-success"></i>');
                        clearInterval(rfidInterval);
                        $('#pegawaiNip').focus();
                    }
                }
            });
        }

        // Fungsi untuk memulai pengecekan RFID
        function startRfidCheck() {
            if (rfidInterval) clearInterval(rfidInterval);
            rfidInterval = setInterval(getRfidUid, 2000);
            $('#rfid-status').html('<i class="fas fa-sync-alt fa-spin"></i>');
        }

        startRfidCheck();

        // Fungsi untuk tombol reset
        $("#reset").click(function() {
            startRfidCheck();
        });

        // Logika untuk submit form AJAX
        $("form#appsform").submit(function(e) {
            e.preventDefault(); // Mencegah form refresh halaman
            
            var postdata = new FormData(this);
            var postaction = $(this).attr('action');
            
            $.ajax({
                type: "POST",
                url: postaction,
                data: postdata,
                dataType: 'json',   // Biarkan jQuery mem-parsing JSON secara otomatis
                contentType: false, // Wajib false untuk FormData
                processData: false, // Wajib false untuk FormData
                timeout: false,

                beforeSend: function() {
                    $("#response").html('<div class="alert alert-warning text-center h4" role="alert">Loading...</div>');
                },

                success: function(response) {
                    // Karena 'dataType: json', variabel 'response' sudah berupa objek JavaScript
                    if (response.status) {
                        // Jika server merespon status: true
                        $("#response").html(
                            '<div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">' +
                            response.message + // Ambil pesan langsung dari objek
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                        );
                        $("#appsform")[0].reset(); // Reset form
                        startRfidCheck(); // Mulai lagi pemindaian RFID
                    } else {
                        // Jika server merespon status: false
                        $("#response").html(
                            '<div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert">' +
                            response.message + // Ambil pesan error langsung dari objek
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                        );
                    }
                },

                error: function(jqXHR, textStatus, errorThrown) {
                    // Fungsi error untuk menangani kegagalan koneksi atau server error 500
                    let errorMessage = 'Terjadi kegagalan koneksi.';
                    if (textStatus === 'parsererror') {
                        errorMessage = 'Gagal memproses data balasan dari server.';
                    } else if (errorThrown) {
                        errorMessage = 'Error: ' + errorThrown;
                    }
                    $("#response").html(
                        '<div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert">' +
                        errorMessage +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                    );
                }
            });
        });
    });
    </script>
</div>