<?php
// Generate a static fallback JSON for PH regions (writes to public/js/ph-address-fallback.json)
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

try {
    $regions = DB::table('philippine_regions')->select(['region_code as code', 'name'])->orderBy('name')->get();
    $arr = [];
    foreach ($regions as $r) {
        $code = (string) ($r->code ?? $r->region_code ?? '');
        // normalize code: some DB entries use long codes; keep last 2 digits or as-is
        // many controllers expect the code to be short like '01','13','15' — but frontend only displays name
        $arr[] = ['code' => $code, 'name' => $r->name];
    }
    $out = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $path = __DIR__ . '/../public/js/ph-address-fallback.json';
    file_put_contents($path, $out);
    echo "Wrote fallback to: $path\n";
    echo "Regions: " . count($arr) . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
