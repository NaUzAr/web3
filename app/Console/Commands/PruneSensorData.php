<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PruneSensorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:prune {--days=60 : Jumlah hari data sensor disimpan} {--force : Hapus tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pangkas data sensor lama yang melebihi batas waktu retensi untuk optimasi database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        if ($days < 1) {
            $this->error("Opsi --days harus bernilai minimal 1 hari.");
            return Command::FAILURE;
        }

        $cutoff = now()->subDays($days);
        $this->info("Memulai pembersihan data sensor yang lebih lama dari {$days} hari (Sebelum: {$cutoff->toDateTimeString()})...");

        $devices = Device::whereNotNull('table_name')->get();
        $totalDeleted = 0;

        foreach ($devices as $device) {
            $tableName = $device->table_name;

            if (!Schema::hasTable($tableName)) {
                continue;
            }

            try {
                $count = DB::table($tableName)
                    ->where('recorded_at', '<', $cutoff)
                    ->delete();

                if ($count > 0) {
                    $this->line(" - Tabel '{$tableName}' ({$device->name}): {$count} baris data dipangkas.");
                    $totalDeleted += $count;
                }
            } catch (\Throwable $e) {
                $this->error(" - Gagal memangkas tabel '{$tableName}': " . $e->getMessage());
                Log::error("Prune sensor error on table {$tableName}: " . $e->getMessage());
            }
        }

        $this->info("Pembersihan data sensor selesai. Total {$totalDeleted} baris data berhasil dibersihkan.");
        Log::info("Sensors data pruned", ['days' => $days, 'total_deleted' => $totalDeleted]);

        return Command::SUCCESS;
    }
}
