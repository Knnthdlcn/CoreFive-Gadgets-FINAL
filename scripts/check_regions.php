<?php
// Diagnostic: check philippine_regions table and counts using Laravel app bootstrap
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo 'APP_ENV=' . env('APP_ENV') . PHP_EOL;
echo 'DB default: ' . config('database.default') . PHP_EOL;
echo 'DB connection name: ' . DB::connection()->getName() . PHP_EOL;

try {
    $tables = DB::select("SHOW TABLES LIKE 'philippine_regions'");
    echo 'Regions table exists? ' . (count($tables) ? 'yes' : 'no') . PHP_EOL;
} catch (Exception $e) {
    echo 'SHOW TABLES error: ' . $e->getMessage() . PHP_EOL;
}

try {
    $count = DB::table('philippine_regions')->count();
    echo 'Regions count: ' . $count . PHP_EOL;
} catch (Exception $e) {
    echo 'Count error: ' . $e->getMessage() . PHP_EOL;
}

try {
    echo 'DB host: ' . config('database.connections.'.config('database.default').'.host') . PHP_EOL;
    echo 'DB database: ' . config('database.connections.'.config('database.default').'.database') . PHP_EOL;
    echo 'DB username: ' . config('database.connections.'.config('database.default').'.username') . PHP_EOL;
} catch (Exception $e) {
    // ignore
}

return 0;
