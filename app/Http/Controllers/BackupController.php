<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function index()
    {
        $path = storage_path('app/backups');

        if(!File::exists($path))
        {
            File::makeDirectory(
                $path,
                0755,
                true
            );
        }

        $files =
            collect(File::files($path))

            ->sortByDesc(
                fn($file)=>$file->getMTime()
            )

            ->map(function($file){

                return [

                    'name'=>$file->getFilename(),

                    'size'=>$file->getSize(),

                    'date'=>$file->getMTime(),

                ];

            });

        return view(
            'backup.index',
            compact('files')
        );
    }

    // hapus
    public function destroy($file)
    {
        $path = storage_path(
            'app/backups/'.$file
        );

        if(File::exists($path))
        {
            File::delete($path);
        }

        return back()->with(
            'success',
            'Backup berhasil dihapus.'
        );
    }

    // backup grok
    public function backup()
    {
        $mysqldump = config('database.mysqldump_path');
        if (!$mysqldump) {
            $mysqldump = env('MYSQLDUMP_PATH', 'mysqldump');
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $filename = 'backup_' . now()->format('Y-m-d_His') . ($isWindows ? '.sql' : '.sql.gz');
        $filepath = storage_path('app/backups/' . $filename);

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host') ?: '127.0.0.1';

        // Pastikan direktori backup ada
        $backupDir = dirname($filepath);
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        if ($isWindows) {
            // Windows - tanpa gzip (lebih stabil)
            $command = sprintf(
                '"%s" --host=%s --user=%s --password="%s" --routines --triggers --events %s > "%s"',
                $mysqldump,
                $host,
                $username,
                $password,
                $database,
                $filepath
            );
        } else {
            // Linux
            $command = sprintf(
                '"%s" --host=%s --user=%s --password="%s" --routines --triggers --events %s | gzip > "%s"',
                $mysqldump,
                $host,
                $username,
                $password,
                $database,
                $filepath
            );
        }

        exec($command, $output, $returnCode);
        

        // Jika $returnCode TIDAK SAMA DENGAN 0, artinya perintah GAGAL
        if ($returnCode !== 0) {
            // Gabungkan array output menjadi string untuk log error
            $error = implode("\n", $output) ?: "Unknown error dengan return code: " . $returnCode;
            
            \Log::error("Backup Gagal: " . $error);
            
            return back()->with(
                'error',
                'Backup gagal: ' . $error
            );
        }

        return back()->with(
            'success',
            'Backup berhasil : ' . $filename
        );
    }
    // backup gmn
    public function backup_gagal_maning()
    {
        $mysqldump = config('database.mysqldump_path');
        
        // Jaga-jaga jika config belum terbaca, ambil langsung dari env
        if (!$mysqldump) {
            $mysqldump = env('MYSQLDUMP_PATH', 'mysqldump');
        }

        // Cek OS (Windows atau Linux)
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        // Atur ekstensi file berdasarkan OS
        $filename = 'backup_' . now()->format('Y-m-d_His') . ($isWindows ? '.sql' : '.sql.gz');
        $filepath = storage_path('app/backups/' . $filename);

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');

        // Pisahkan perintah berdasarkan OS (Windows lokal tidak punya gzip secara default)
        if ($isWindows) {
            $command = sprintf(
                '"%s" -h %s -u %s -p"%s" %s > "%s"',
                $mysqldump,
                $host,
                $username,
                $password,
                $database,
                $filepath
            );
        } else {
            $command = sprintf(
                '"%s" -h %s -u %s -p"%s" %s | gzip > "%s"',
                $mysqldump,
                $host,
                $username,
                $password,
                $database,
                $filepath
            );
        }

        // $process = Process::fromShellCommandline($command);
        exec($command, $output, $returnCode);
        // dd($returnCode, $output);

        $process->run();

        if (!$process->isSuccessful()) {
            // Log error ke storage/logs/laravel.log untuk mempermudah debugging jika masih gagal
            \Log::error("Backup Gagal: " . $process->getErrorOutput());
            
            return back()->with(
                'error',
                'Backup gagal mengeksekusi mysqldump. Periksa setelan DB.'
            );
        }

        return back()->with(
            'success',
            'Backup berhasil : ' . $filename
        );
    }

    public function backup_gagal()
    {
        $filename =
            'backup_' .
            now()->format('Y-m-d_His')
            .
            '.sql.gz';

        $mysqldump =
            config('database.mysqldump_path');

        $filepath =
            storage_path(
                'app/backups/' .
                $filename
            );

        $database =
            config(
                'database.connections.mysql.database'
            );

        $username =
            config(
                'database.connections.mysql.username'
            );

        $password =
            config(
                'database.connections.mysql.password'
            );

        $host =
            config(
                'database.connections.mysql.host'
            );

        $process =
            Process::fromShellCommandline(

                sprintf(

                    '"%s" -h%s -u%s -p%s %s | gzip > "%s"',

                        $mysqldump,
                        $host,
                        $username,
                        $password,
                        $database,
                        $filepath

                    )

            );

        $process->run();

        if(!$process->isSuccessful())
        {
            return back()
                ->with(
                    'error',
                    'Backup gagal'
                );
        }

        return back()
            ->with(
                'success',
                'Backup berhasil : '
                .
                $filename
            );
    }

    public function download($file)
    {
        return response()->download(

            storage_path(
                'app/backups/' .
                $file
            )

        );
    }

    // public function destroy($file)
    // {
    //     File::delete(

    //         storage_path(
    //             'app/backups/' .
    //             $file
    //         )

    //     );

    //     return back()
    //         ->with(
    //             'success',
    //             'Backup dihapus'
    //         );
    // }
}