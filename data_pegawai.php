<?php
$page = "Data Pegawai";
require_once("./header.php");
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Data Pegawai</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Pegawai</li>
            </ol>
            <!-- START MESSAGE -->
            <?php
            if (isset($_GET['msg']) && isset($_SERVER['HTTP_REFERER'])) {
                if ($_GET['msg'] == 1 && $_SERVER['HTTP_REFERER']) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                <strong>Berhasil Menghapus Data Pegawai!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
                } else if ($_GET['msg'] == 2 && $_SERVER['HTTP_REFERER']) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                <strong>Gagal Menghapus Data Pegawai!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
                }
            }
            ?>
            <!-- END MESSAGE -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <i class="fas fa-table mr-1"></i>
                        Data Pegawai
                    </div>
                    <div>
                        <a href="./tambah_pegawai.php">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i> Tambah Data Pegawai
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>RFID</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jabatan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $no = 1;
                            $sql = "SELECT `pegawai`.`pegawai_id`, `pegawai`.`pegawai_nama`, `pegawai`.`pegawai_nip`, `pegawai`.`pegawai_jeniskelamin`, `pegawai`.`pegawai_lahir`, `pegawai`.`pegawai_foto`, `jabatan`.`jabatan_nama`, `pegawai`.`pegawai_rfid` 
                                    FROM `pegawai`
                                    INNER JOIN `jabatan` ON `pegawai`.`jabatan_id` = `jabatan`.`jabatan_id`";
                            $result = $koneksi->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $pegawai_id = $row['pegawai_id'];
                                $pegawai_foto = $row['pegawai_foto'];
                                $pegawai_rfid = $row['pegawai_rfid'];
                                $pegawai_nama = $row['pegawai_nama'];
                                $pegawai_nip = $row['pegawai_nip'];
                                $pegawai_jeniskelamin = $row['pegawai_jeniskelamin'];
                                $pegawai_lahir = $row['pegawai_lahir'];
                                $jabatan_nama = $row['jabatan_nama'];
                            ?>
                                <tr class="text-center">
                                    <td><?php echo $no++; ?></td>
                                    <td><img src="./image/<?php echo $pegawai_foto ?>" class="rounded-circle"
                                            alt="Foto <?php echo $pegawai_nama ?>" width="80" height="80"></td>
                                    <td><?php echo $pegawai_nama; ?></td>
                                    <td><?php echo $pegawai_nip; ?></td>
                                    <td><?php echo $pegawai_rfid; ?></td>
                                    <td><?php echo jenis_kelamin($pegawai_jeniskelamin); ?></td>
                                    <td><?php echo format_hari_tanggal($pegawai_lahir, true); ?></td>
                                    <td><?php echo $jabatan_nama; ?></td>
                                    <td>
                                        <a href="edit_pegawai.php?pegawai_id=<?php echo $pegawai_id; ?>"
                                            class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="hapus_pegawai.php?pegawai_id=<?php echo $pegawai_id; ?>"
                                            class="btn btn-danger" onclick="return confirm('Apakah anda yakin?')"><i
                                                class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                                <?php
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