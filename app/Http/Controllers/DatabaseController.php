<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    // Backup the database
    public function backup()
    {
        $database = env('DB_DATABASE', 'it12');
        $user = env('DB_USERNAME', 'root');
        $pass = env('DB_PASSWORD', '');
        $host = env('DB_HOST', '127.0.0.1');
        $backupFile = storage_path("app/backup_".date('Y-m-d_H-i-s').".sql");

        $passwordOption = $pass === '' ? '' : "--password={$pass}";

        $command = '"C:\xampp\mysql\bin\mysqldump.exe" -h '.$host.' -u '.$user.' '.$passwordOption.' --add-drop-table '.$database;
        $command .= ' > "'.$backupFile.'"';

        exec($command, $output, $returnVar);

        if($returnVar !== 0) {
            return back()->with('error', 'Backup failed. Make sure mysqldump is in your system PATH. Error code: '.$returnVar);
        }

        return response()->download($backupFile)->deleteFileAfterSend(true);
    }

    // Restore the database
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|mimes:sql,txt',
        ]);

        $file = $request->file('backup_file');
        $tempPath = storage_path('app/temp_restore.sql');
        
        // Move uploaded file to a known location
        $file->move(storage_path('app'), 'temp_restore.sql');

        $database = env('DB_DATABASE', 'it12');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $host = env('DB_HOST', '127.0.0.1');

        try {
            // Build the command
            $passwordOption = $password === '' ? '' : "--password={$password}";
            
            $command = '"C:\xampp\mysql\bin\mysql.exe" -h '.$host.' -u '.$username.' '.$passwordOption.' '.$database.' < "'.$tempPath.'"';
            
            // Execute command and capture output
            exec($command . ' 2>&1', $output, $returnVar);
            
            // Clean up temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            if($returnVar !== 0) {
                $error = implode("\n", $output);
                return back()->with('error', 'Restore failed. Error: ' . $error);
            }
            
            return back()->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            // Clean up temp file on error
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}