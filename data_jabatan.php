<?php
$page = "Data Jabatan";
require_once("./header.php");
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Data Jabatan</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Data Jabatan</li>
            </ol>
            <?php
            if (isset($_GET['msg']) && isset($_SERVER['HTTP_REFERER'])) {
                if ($_GET['msg'] == 1 && $_SERVER['HTTP_REFERER']) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                <strong>Berhasil Menghapus Data Jabatan!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>R
            <?php
                } else if ($_GET['msg'] == 2 && $_SERVER['HTTP_REFERER']) {
            ?>
            <div class="alert alert-danger alert-dismissible fade show text-center h4" role="alert">
                <strong>Gagal Menghapus Data Jabatan!</strong>
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
                        Data Jabatan
                    </div>
                    <div>
                        <a href="./tambah_jabatan.php">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i> Tambah Data Jabatan
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jabatan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                
                                $sql = "SELECT * FROM `jabatan` ORDER BY `jabatan_nama` ASC";
                                $result = $koneksi->query($sql);
                                
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr class="text-center">
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo htmlspecialchars($row['jabatan_nama']); ?></td>
                                    <td>
                                        <a href="edit_jabatan.php?jabatan_id=<?php echo $row['jabatan_id']; ?>"
                                            class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="hapus_jabatan.php?jabatan_id=<?php echo $row['jabatan_id']; ?>"
                                            class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus jabatan ini?')"><i
                                                class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                                <?php
                                    $no++;
                                    }
                                } else {
                                    echo '<tr><td colspan="3" class="text-center">Tidak ada data jabatan.</td></tr>';
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