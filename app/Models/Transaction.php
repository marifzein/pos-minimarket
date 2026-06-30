<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionDetail;

class Transaction extends Model
{
    protected $guarded = [];

        public function details()
    {
        return $this->hasMany(
            TransactionDetail::class
        );
    }

    public static function generateNoNota()
    {
        $today = now()->format('Ymd');

        // $last = self::whereDate('created_at', today())
        //     ->orderByDesc('id')
        //     ->first();

        $last = self::where('no_nota', 'like', 'INV-' . $today . '-%')
            ->orderByDesc('id')
            ->first();  


        if (!$last) {
            $number = 1;
        } else {

            $lastNumber = (int) substr(
                $last->no_nota,
                -5
            );

            $number = $lastNumber + 1;
        }

        return sprintf(
            'INV-%s-%05d',
            $today,
            $number
        );
    }
}