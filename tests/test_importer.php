<?php
require '/opt/data/projects/engagement-planner/app/vendor/autoload.php';
$app = require '/opt/data/projects/engagement-planner/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$imp = new \App\Support\BudgetSpreadsheetImporter();
$result = $imp->import('/opt/data/projects/engagement-planner/screenshots/Buku-test.xlsx');
echo "created: " . $result['created']->count() . "\n";
echo "errors: " . json_encode($result['errors']) . "\n";
foreach ($result['created'] as $item) {
    echo " - {$item->name} | cat={$item->category->value} | satuan={$item->unit_price} | qty={$item->quantity} | kontrak=" . ($item->contract_value ?? '-') . " | payer={$item->payer->value}\n";
}
