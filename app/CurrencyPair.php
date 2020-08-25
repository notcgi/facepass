<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyPair extends Model
{
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
}
