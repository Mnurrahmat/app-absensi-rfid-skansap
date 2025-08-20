<?php
require_once __DIR__ . '/vendor/autoload.php'; 
require_once("./config/db.php");
require_once("./config/function.php");

$pdf = new FPDF();

$sql = "SELECT `pegawai`.`pegawai_nama`, `pegawai`.`pegawai_nip`, `jabatan`.`jabatan_nama`, `rekap`.`rekap_tanggal`, `rekap`.`rekap_masuk`, `rekap`.`rekap_keluar`
        FROM `rekap`
        INNER JOIN `pegawai` ON `rekap`.`pegawai_id` = `pegawai`.`pegawai_id`
        INNER JOIN `jabatan` ON `pegawai`.`jabatan_id` = `jabatan`.`jabatan_id`
        ORDER BY `rekap`.`rekap_tanggal` DESC, `pegawai`.`pegawai_nama` ASC";

$result = $koneksi->query($sql);

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, 'LAPORAN ABSENSI PEGAWAI', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'SMK NEGERI 1 PANGKEP', 0, 1, 'C');
        $this->Line(10, $this->GetY() + 5, 200, $this->GetY() + 5);
        $this->Ln(10);

        // Header tabel
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(10, 10, 'No', 1, 0, 'C', true);
        $this->Cell(45, 10, 'Nama', 1, 0, 'C', true);
        $this->Cell(35, 10, 'NIP', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Jabatan', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Tanggal', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Masuk', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Pulang', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Laporan Absensi | Halaman ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$nomor = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10, 8, $nomor++, 1, 0, 'C');
        $pdf->Cell(45, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $row['pegawai_nama']), 1, 0, 'L');
        $pdf->Cell(35, 8, $row['pegawai_nip'], 1, 0, 'L');
        $pdf->Cell(35, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $row['jabatan_nama']), 1, 0, 'L');
        $pdf->Cell(25, 8, date('d-m-Y', strtotime($row['rekap_tanggal'])), 1, 0, 'C');
        $pdf->Cell(20, 8, $row['rekap_masuk'], 1, 0, 'C');
        $pdf->Cell(20, 8, $row['rekap_keluar'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(190, 10, 'Tidak ada data untuk ditampilkan', 1, 1, 'C');
}

// Nama file PDF
$tanggal_sekarang = format_hari_tanggal(date('Y-m-d'), true);
$nama_file = "Laporan_Absensi_SMKN1_Pangkep_" . str_replace([' ', ':'], '_', $tanggal_sekarang) . ".pdf";

$pdf->Output('I', $nama_file);