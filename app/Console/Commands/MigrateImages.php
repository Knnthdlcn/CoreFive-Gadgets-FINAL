<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateImages extends Command
{
    protected $signature = 'images:migrate {--dry-run}';
    protected $description = 'Backup and migrate product image paths and copy files into public/images';

    public function handle()
    {
        $this->info('Starting product images migration...');

        $dry = $this->option('dry-run');

        // Collect rows that likely need migration
        $products = DB::table('products')
            ->where(function ($q) {
                $q->whereNull('image_path')
                  ->orWhere('image_path', 'like', 'products/%')
                  ->orWhere('image_path', 'like', 'storage/%')
                  ->orWhere('image_path', 'like', '%Product_IMG%')
                  ->orWhere('image_path', 'not like', 'images/%');
            })
            ->select('product_id', 'product_name', 'image_path')
            ->get();

        $report = [];
        $ts = date('Ymd_His');
        $backupFile = storage_path("app/backups/product_images_backup_{$ts}.json");

        // Ensure backup dir exists
        if (!File::exists(dirname($backupFile))) {
            File::makeDirectory(dirname($backupFile), 0755, true);
        }

        $this->info('Found ' . $products->count() . ' products to examine.');

        foreach ($products as $p) {
            $row = [
                'product_id' => $p->product_id,
                'product_name' => $p->product_name,
                'original_image_path' => $p->image_path,
                'filename' => null,
                'storage_exists' => false,
                'public_exists' => false,
                'action' => 'skipped',
                'note' => null,
            ];

            $imgPath = $p->image_path ?? '';
            $filename = '';
            if ($imgPath !== '') {
                $filename = basename(str_replace('\\', '/', $imgPath));
                $row['filename'] = $filename;
            }

            // Paths to check
            $storagePath = storage_path('app/public/products/' . $filename);
            $publicPath = public_path('images/' . $filename);

            $row['storage_exists'] = $filename !== '' && File::exists($storagePath);
            $row['public_exists'] = $filename !== '' && File::exists($publicPath);

            if ($filename === '') {
                $row['note'] = 'no image_path';
                $row['action'] = 'none';
                $report[] = $row;
                $this->line("#{$p->product_id}: no image_path, skipping");
                continue;
            }

            if (!$row['storage_exists'] && !$row['public_exists']) {
                $row['note'] = 'file missing in both storage and public';
                $row['action'] = 'none';
                $report[] = $row;
                $this->warn("#{$p->product_id}: file {$filename} not found on disk");
                continue;
            }

            // Ensure public images dir exists
            if (!File::exists(public_path('images'))) {
                if (!$dry) File::makeDirectory(public_path('images'), 0755, true);
            }

            // If storage exists and public does not, copy
            if ($row['storage_exists'] && !$row['public_exists']) {
                if (!$dry) {
                    try {
                        File::copy($storagePath, $publicPath);
                        $this->info("Copied {$filename} -> public/images");
                    } catch (\Throwable $e) {
                        $row['note'] = 'copy_failed: ' . $e->getMessage();
                        $row['action'] = 'copy_failed';
                        $report[] = $row;
                        $this->error("Failed to copy {$filename}: {$e->getMessage()}");
                        continue;
                    }
                } else {
                    $this->info("(dry) would copy {$filename} -> public/images");
                }
            }

            // Update DB to images/<filename>
            $newPath = 'images/' . $filename;
            if (!$dry) {
                DB::table('products')->where('product_id', $p->product_id)->update(['image_path' => $newPath]);
                $row['action'] = 'updated_db';
                $row['note'] = 'updated to ' . $newPath;
                $this->info("#{$p->product_id}: image_path updated to {$newPath}");
            } else {
                $row['action'] = 'would_update_db';
                $row['note'] = 'dry-run';
                $this->info("(dry) #{$p->product_id}: would update image_path to {$newPath}");
            }

            $report[] = $row;
        }

        // Write backup report
        file_put_contents($backupFile, json_encode($report, JSON_PRETTY_PRINT));
        $this->info('Backup/report written to: ' . $backupFile);

        $this->info('Migration complete.');
        return 0;
    }
}
