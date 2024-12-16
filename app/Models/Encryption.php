<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encryption extends Model
{
    use HasFactory;

    protected $connection = 'encryption_db';

    protected $table = 'travel_key_details';

    protected $fillable = ['module', 'config_name', 'config_value', 'created_by', 'active'];
}
