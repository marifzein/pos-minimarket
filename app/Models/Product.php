<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StockMovement;

class Product extends Model
{
    protected $table = 'products';

    protected $guarded = [];

    public function stockMovements()
    {
        return $this->hasMany(
            StockMovement::class
        );
    }

    public function stockOpnameDetails()
    {
        return $this->hasMany(
            StockOpnameDetail::class
        );
    }
}