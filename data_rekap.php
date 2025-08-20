<?php
$page = "Data Rekap";
require_once("./header.php");
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Data Rekap</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Rekap Kehadiran Pegawai</li>
            </ol>

            <?php
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] == 1) {
            ?>
                    <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                        <strong>Berhasil Menghapus Data Rekap!</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php
                } else if ($_GET['msg'] == 2) {
                ?>
                    <div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert">
                        <strong>Gagal Menghapus Data Rekap!</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
            <?php
                }
            }
            ?>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <i class="fas fa-table mr-1"></i>
                        Data Rekap
                    </div>
                    <form action="./rekap.php" method="get">
                        <div class="row">
                            <div class="col-md-auto">
                                <select class="custom-select" id="jabatan_id" name="jabatan_id"
                                    autocomplete="off">
                                    <?php
                                        $sql = "SELECT * FROM `jabatan` ORDER BY `jabatan_id` ASC";
                                        $result = $koneksi->query($sql);

                                        if ($result->num_rows > 0) {
                                            echo '<option value="">- Pilih Jabatan -</option>';
                                            while ($row = $result->fetch_assoc()) {
                                                $jabatanId = $row['jabatan_id'];
                                                $jabatanNama = $row['jabatan_nama'];

                                                echo '<option value="' . $jabatanId . '">' . $jabatanNama . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">- Jabatan Tidak Ditemukan -</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <input type="date" class="form-control" name="tanggal" id="tanggal" autocomplete="off">
                            </div>
                            <div>
                                <a href="./cetak_exel.php" target="_blank" class="btn btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Cetak Excel
                                </a>
                                <a href="./cetak_pdf.php" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Foto Pegawai</th>
                                    <th>Nama Pegawai</th>
                                    <th>NIP Pegawai</th>
                                    <th>Jabatan Pegawai</th>
                                    <th>Rekap Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Foto Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Foto Pulang</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no=1;
                                $sql = "SELECT `rekap`.`rekap_id`, `rekap`.`rekap_tanggal`, `rekap`.`rekap_masuk`, `rekap`.`rekap_keluar`, `rekap`.`rekap_photomasuk`, `rekap`.`rekap_photokeluar`, `rekap`.`rekap_keterangan`, `pegawai`.`pegawai_foto`, `pegawai`.`pegawai_nama`, `pegawai`.`pegawai_nip`, `jabatan`.`jabatan_nama`
                                        FROM `rekap`
                                        INNER JOIN `pegawai` ON `rekap`.`pegawai_id` = `pegawai`.`pegawai_id`
                                        INNER JOIN `jabatan` ON `pegawai`.`jabatan_id` = `jabatan`.`jabatan_id`
                                        ORDER BY `rekap`.`rekap_tanggal` DESC, `rekap`.`rekap_masuk` DESC";
                                $result = $koneksi->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                        <tr class="text-center">
                                            <td><?php echo $no++; ?></td>
                                            <td class="text-center"><img src="./image/<?php echo $row['pegawai_foto']; ?>" class="rounded-circle" alt="Foto" width="80" height="80"></td>
                                            <td><?php echo $row['pegawai_nama']; ?></td>
                                            <td><?php echo $row['pegawai_nip']; ?></td>
                                            <td><?php echo $row['jabatan_nama']; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['rekap_tanggal'])); ?></td>
                                            <td><?php echo $row['rekap_masuk']; ?></td>
                                            <td class="text-center"><img src="./image/<?php echo $row['rekap_photomasuk']; ?>" class="rounded" alt="Foto Masuk" width="80" height="80"></td>
                                            <td><?php echo $row['rekap_keluar']; ?></td>
                                            <td class="text-center"><img src="./image/<?php echo $row['rekap_photokeluar']; ?>" class="rounded" alt="Foto Keluar" width="80" height="80"></td>
                                            <td><?php echo $row['rekap_keterangan']; ?></td>
                                            <td class="text-center">
                                                <a href="edit_rekap.php?rekap_id=<?php echo $row['rekap_id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Lihat/Edit
                                                </a>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="11" class="text-center">Tidak ada data rekap ditemukan.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
require_once("./footer.php");
?>
</div>