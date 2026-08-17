<?php
require '/opt/data/projects/engagement-planner/app/vendor/autoload.php';
$app = require '/opt/data/projects/engagement-planner/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$path = '/opt/data/projects/engagement-planner/screenshots/Buku-test.xlsx';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
var_dump(get_class($reader));
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($path);
$sheet = $spreadsheet->getActiveSheet();
echo "Sheet: " . $sheet->getTitle() . "\n";
$rows = $sheet->toArray(null, true, true, true);
echo "Total rows: " . count($rows) . "\n";
foreach (array_slice($rows, 0, 4) as $r) {
    echo json_encode($r) . "\n";
}
