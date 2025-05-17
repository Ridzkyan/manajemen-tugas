<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class PengaturanController extends Controller
{
    private function getMysqlPaths()
    {
        return [
            'mysqldump' => 'C:\xampp\mysql\bin\mysqldump.exe',
            'mysql'     => 'C:\xampp\mysql\bin\mysql.exe',
        ];
    }

    public function data()
    {
        return view('admin.admin_pengaturan.data');
    }

    public function backup()
    {
        $paths = $this->getMysqlPaths();
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $filename = 'backup-' . date('Ymd-His') . '.sql';
        $path = storage_path("app/backups/$filename");

        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        $command = "\"{$paths['mysqldump']}\" -u {$user} " . ($pass ? "-p{$pass}" : '') . " {$db} > \"$path\"";
        system($command, $result);

        return $result === 0
            ? response()->download($path)
            : back()->with('error', 'Backup gagal.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql',
        ]);

        $paths = $this->getMysqlPaths();
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $file = $request->file('sql_file')->getPathname();

        $command = "\"{$paths['mysql']}\" -u {$user} " . ($pass ? "-p{$pass}" : '') . " {$db} < \"$file\"";
        system($command, $result);

        return $result === 0
            ? back()->with('success', 'Restore berhasil!')
            : back()->with('error', 'Restore gagal.');
    }

    public function backupFiles()
    {
        $storagePath = storage_path('app/public');
        $zipFileName = 'storage-backup-' . date('Ymd-His') . '.zip';
        $zipPath = storage_path("app/backups/{$zipFileName}");

        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = File::allFiles($storagePath);
            foreach ($files as $file) {
                $relativePath = str_replace($storagePath . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                $zip->addFile($file->getRealPath(), $relativePath);
            }
            $zip->close();
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Backup file gagal.');
    }

    public function backupZip()
    {
        $paths = $this->getMysqlPaths();
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        $timestamp = now()->format('Ymd-His');
        $backupDir = storage_path('app/backups');
        $sqlFile = "{$backupDir}/backup-{$timestamp}.sql";
        $zipFile = "{$backupDir}/backup-{$timestamp}.zip";

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $command = "\"{$paths['mysqldump']}\" -u {$user} " . ($pass ? "-p{$pass}" : '') . " {$db} > \"$sqlFile\"";
        system($command, $result);

        if ($result !== 0) {
            return back()->with('error', 'Gagal melakukan backup database.');
        }

        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($sqlFile, basename($sqlFile));
            $publicPath = storage_path('app/public');
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($publicPath));
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'storage/' . substr($filePath, strlen($publicPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        unlink($sqlFile);
        return response()->download($zipFile);
    }
}
