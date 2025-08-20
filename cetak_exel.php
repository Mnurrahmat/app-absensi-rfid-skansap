<?php
require_once __DIR__ . '/vendor/autoload.php'; 
require_once("./config/db.php");
require_once("./config/function.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT `pegawai`.`pegawai_nama`, `pegawai`.`pegawai_nip`, `jabatan`.`jabatan_nama`, `rekap`.`rekap_tanggal`, `rekap`.`rekap_masuk`, `rekap`.`rekap_keluar`
        FROM `rekap`
        INNER JOIN `pegawai` ON `rekap`.`pegawai_id` = `pegawai`.`pegawai_id`
        INNER JOIN `jabatan` ON `pegawai`.`jabatan_id` = `jabatan`.`jabatan_id`
        ORDER BY `rekap`.`rekap_tanggal` DESC, `pegawai`.`pegawai_nama` ASC";
$result = $koneksi->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'LAPORAN REKAP ABSENSI PEGAWAI');
$sheet->mergeCells('A1:G1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('A2', 'SMK NEGERI 1 PANGKEP');
$sheet->mergeCells('A2:G2');
$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

$header = ['No', 'Nama', 'NIP', 'Jabatan', 'Tanggal', 'Masuk', 'Pulang'];
$col = 'A';
foreach ($header as $h) {
    $sheet->setCellValue($col . '4', $h);
    $sheet->getStyle($col . '4')->getFont()->setBold(true);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

$rowNum = 5;
$no = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNum, $no++);
        $sheet->setCellValue('B' . $rowNum, $row['pegawai_nama']);
        $sheet->setCellValue('C' . $rowNum, $row['pegawai_nip']);
        $sheet->setCellValue('D' . $rowNum, $row['jabatan_nama']);
        $sheet->setCellValue('E' . $rowNum, format_hari_tanggal($row['rekap_tanggal'], true));
        $sheet->setCellValue('F' . $rowNum, $row['rekap_masuk']);
        $sheet->setCellValue('G' . $rowNum, $row['rekap_keluar']);
        $rowNum++;
    }
} else {
    $sheet->setCellValue('A5', 'Tidak ada data');
    $sheet->mergeCells('A5:G5');
}

$namaFile = 'Laporan Absensi SMKN1 Pangkep (' . format_hari_tanggal(date('Y-m-d'), true) . ').xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$namaFile\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;