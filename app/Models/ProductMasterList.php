<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMasterList extends Model
{
    use HasFactory;

    const
        CODE_STATUS_SOLD   = 'Sold',
        CODE_STATUS_BUY    = 'Buy'
    ;

    protected $fillable = [
        'types',
        'brand',
        'model',
        'capacity',
        'quantity',
    ];
}
