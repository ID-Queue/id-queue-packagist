<?php

namespace IdQueue\IdQueuePackagist\Models\Admin;

use IdQueue\IdQueuePackagist\Traits\AdminDbConnection;
use Illuminate\Database\Eloquent\Model;

class SysDiagram extends Model
{
    use AdminDbConnection;

    protected $table = 'sysdiagrams';

    protected $primaryKey = 'diagram_id';

    public $timestamps = false;  // Disable timestamps if they are not used

    protected $fillable = [
        'name',
        'principal_id',
        'version',
        'definition',
    ];
}
