<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class SysDiagram extends Model
{
    use CompanyDbConnection;

    // Table associated with the model

    // Disable timestamps as the table does not have created_at and updated_at columns
    protected $table = 'sysdiagrams';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'name',
        'principal_id',
        'diagram_id',
        'version',
        'definition',
    ];

    // Cast the definition column as binary to handle varbinary type correctly
    protected $casts = [
        'definition' => 'binary',
    ];

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'diagram_id';
}
