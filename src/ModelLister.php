<?php

namespace IdQueue\IdQueuePackagist;

use Illuminate\Support\Facades\File;

class ModelLister
{
    protected string $modelsPath;

    public function __construct()
    {
        $this->modelsPath = app_path('Models');
    }

    /**
     * Get a list of all Laravel Models.
     */
    public function listModels(): array
    {
        $models = [];

        if (File::exists($this->modelsPath)) {
            $files = File::allFiles($this->modelsPath);
            foreach ($files as $file) {
                $namespace = 'App\\Models\\';
                $className = $namespace.pathinfo($file->getFilename(), PATHINFO_FILENAME);
                if (class_exists($className) && is_subclass_of($className, 'Illuminate\\Database\\Eloquent\\Model')) {
                    $models[] = $className;
                }
            }
        }

        return $models;
    }
}
