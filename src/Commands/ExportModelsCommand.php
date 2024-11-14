<?php

namespace IdQueue\IdQueuePackage\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportModelsCommand extends Command
{
    protected $signature = 'Models:export';

    protected $description = 'Export all package Models to the Laravel Models folder';

    public function handle()
    {
        $packageModelsPath = __DIR__.'/../Models';
        $appModelsPath = app_path('Models');

        if (! File::exists($packageModelsPath)) {
            $this->error("No Models found in the package's Models directory.");

            return;
        }

        File::ensureDirectoryExists($appModelsPath);

        $files = File::allFiles($packageModelsPath);

        foreach ($files as $file) {
            $destination = $appModelsPath.'/'.$file->getFilename();
            File::copy($file->getPathname(), $destination);
            $this->info("Exported: {$file->getFilename()}");
        }

        $this->info('All Models have been exported to the Laravel Models folder.');
    }
}
