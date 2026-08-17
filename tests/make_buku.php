<?php
require '/opt/data/projects/engagement-planner/app/vendor/autoload.php';
$app = require '/opt/data/projects/engagement-planner/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Buku');
$sheet->fromArray(['Item','Kategori','Harga Satuan','Jumlah','Penanggung','Kontrak','Catatan'], null, 'A1');
$rows = [
    ['Souvenir Bros', 'Lainnya', 15000, 60, 'Bersama', 900000, 'Souvenir tamu'],
    ['Baju adat CPP', 'Busana & Aksesori', 2500000, 1, 'CPP', 2500000, 'Fitting selesai'],
    ['Tenda & kursi', 'Tempat & Venue', 100000, 25, 'Bersama', 2500000, ''],
    ['Box kue', 'Katering', 20000, 50, 'CPW', 1000000, ''],
];
$sheet->fromArray($rows, null, 'A2');
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('/opt/data/projects/engagement-planner/screenshots/Buku-test.xlsx');
echo "OK: " . filesize('/opt/data/projects/engagement-planner/screenshots/Buku-test.xlsx') . " bytes\n";
