<?php
$page = "Pengaturan Jam";
require_once("./header.php");

// Ambil data pengaturan saat ini dari database
$sql = "SELECT * FROM pengaturan_jam WHERE id = 1 LIMIT 1";
$result = $koneksi->query($sql);
$pengaturan = $result->fetch_assoc();
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Pengaturan Jam</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Pegaturan Jam Absensi</li>
            </ol>

            <div id="response"></div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-clock mr-1"></i>
                    Pengaturan Jam
                </div>
                <div class="card-body">
                    <form id="appsform" action="pengaturan_jam_post.php" method="POST">
                        <div class="form-group">
                            <label for="jam_masuk">Jam Masuk</label>
                            <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" value="<?= htmlspecialchars($pengaturan['jam_masuk']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="batas_terlambat">Batas Toleransi Keterlambatan</label>
                            <input type="time" class="form-control" id="batas_terlambat" name="batas_terlambat" value="<?= htmlspecialchars($pengaturan['batas_terlambat']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="jam_pulang">Jam Pulang</label>
                            <input type="time" class="form-control" id="jam_pulang" name="jam_pulang" value="<?= htmlspecialchars($pengaturan['jam_pulang']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php require_once("./footer.php"); ?>
</div>
    <script>
    $(document).ready(function() {
        // Event handler untuk form dengan ID "appsform"
        $("form#appsform").submit(function(e) {
            e.preventDefault(); // Mencegah submit standar

            // Menggunakan FormData agar konsisten, meskipun form ini tidak ada file
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
                
                beforeSend: function() {
                    $("#response").html(
                        '<div class="alert alert-warning alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Loading...</div>'
                    );
                },
                success: function(response) {
                    if (response.status) {
                        $("#response").html(
                            '<div class="alert alert-success alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            response.message + '</div>'
                        );
                        // Form pengaturan sebaiknya TIDAK di-reset agar nilai tersimpan tetap terlihat.
                        // Baris di bawah ini bisa diaktifkan jika Anda benar-benar ingin mengosongkan form.
                        // $("#appsform")[0].reset(); 
                    } else {
                        $("#response").html(
                            '<div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            response.message + '</div>'
                        );
                    }
                },
                error: function(_, _, errorMessage) {
                    $("#response").html(
                        '<div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        errorMessage + '</div>'
                    );
                }
            });
            return false;
        });
    });
    </script>
