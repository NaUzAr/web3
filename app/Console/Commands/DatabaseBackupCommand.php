<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--retention=7 : Hari penyimpanan file backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat cadangan database harian (dump) dan membersihkan file backup lama';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        $date = now()->format('Y-m-d_H-i-s');
        $filename = "backup-{$connection}-{$date}.sql";
        $filepath = "{$backupDir}/{$filename}";

        $this->info("Memulai proses backup database '{$connection}'...");

        $success = false;

        try {
            if ($connection === 'pgsql') {
                $host = $dbConfig['host'] ?? '127.0.0.1';
                $port = $dbConfig['port'] ?? 5432;
                $database = $dbConfig['database'] ?? '';
                $username = $dbConfig['username'] ?? '';
                $password = $dbConfig['password'] ?? '';

                putenv("PGPASSWORD={$password}");
                $command = sprintf(
                    'pg_dump -h %s -p %s -U %s -F p -b -v -f "%s" %s',
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($username),
                    $filepath,
                    escapeshellarg($database)
                );
                exec($command, $output, $returnVar);
                $success = ($returnVar === 0 && File::exists($filepath));
            } elseif ($connection === 'mysql') {
                $host = $dbConfig['host'] ?? '127.0.0.1';
                $port = $dbConfig['port'] ?? 3306;
                $database = $dbConfig['database'] ?? '';
                $username = $dbConfig['username'] ?? '';
                $password = $dbConfig['password'] ?? '';

                $pwdArg = $password ? "--password=" . escapeshellarg($password) : "";
                $command = sprintf(
                    'mysqldump -h %s -P %s -u %s %s %s > "%s"',
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($username),
                    $pwdArg,
                    escapeshellarg($database),
                    $filepath
                );
                exec($command, $output, $returnVar);
                $success = ($returnVar === 0 && File::exists($filepath));
            } elseif ($connection === 'sqlite') {
                $dbPath = $dbConfig['database'];
                if (File::exists($dbPath)) {
                    File::copy($dbPath, $filepath);
                    $success = true;
                }
            } else {
                $this->warn("Koneksi '{$connection}' belum memiliki driver dump otomatis khusus.");
            }

            if ($success) {
                $filesize = round(File::size($filepath) / 1024, 2);
                $this->info("Backup berhasil dibuat: {$filename} ({$filesize} KB)");
                Log::info("Database backup created successfully: {$filename}");
            } else {
                $this->warn("Peringatan: Utilitas dump belum tersedia atau database tidak aktif. Periksa instalasi pg_dump / mysqldump.");
            }

            // Pembersihan backup lama (retensi)
            $retentionDays = (int) $this->option('retention');
            $cutoffTime = now()->subDays($retentionDays)->timestamp;
            $files = File::files($backupDir);
            $deletedOldFiles = 0;

            foreach ($files as $file) {
                if ($file->getMTime() < $cutoffTime) {
                    File::delete($file->getPathname());
                    $deletedOldFiles++;
                }
            }

            if ($deletedOldFiles > 0) {
                $this->line("Dibersihkan {$deletedOldFiles} file backup lama (> {$retentionDays} hari).");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error saat backup database: " . $e->getMessage());
            Log::error("Database backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
