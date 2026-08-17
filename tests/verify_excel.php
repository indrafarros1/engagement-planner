<?php
require '/opt/data/projects/engagement-planner/app/vendor/autoload.php';
$app = require '/opt/data/projects/engagement-planner/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['/tmp/anggaran-2026-08-17.xlsx', '/tmp/pembayaran-2026-08-17.xlsx'] as $f) {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($f);
    $reader->setReadDataOnly(true);
    $s = $reader->load($f)->getActiveSheet();
    $rows = $s->toArray(null, true, true, true);
    echo basename($f) . " — sheet=" . $s->getTitle() . " baris=" . count($rows) . "\n";
    foreach (array_slice($rows, 0, 3) as $r) {
        echo "   " . implode(' | ', array_map(fn($v) => (string)$v, $r)) . "\n";
    }
    echo "\n";
}
