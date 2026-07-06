<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DocumentNumber
{
    /**
     * Untuk dokumen transaksi
     * Contoh:
     * PO-20260703-0001
     * SO-20260703-0001
     */
    public static function generate(
        string $table,
        string $field,
        string $prefix
    ): string {

        $today = date('Ymd');

        $like = $prefix . '-' . $today . '-%';

        $last = DB::table($table)
            ->where($field, 'like', $like)
            ->orderByDesc($field)
            ->value($field);

        if (!$last) {

            $number = 1;

        } else {

            $number = (int) substr($last, -4) + 1;

        }

        return sprintf(
            '%s-%s-%04d',
            $prefix,
            $today,
            $number
        );
    }

    /**
     * Untuk master data
     * Contoh:
     * CUST0001
     * SUP0001
     * PRD0001
     */
    public static function generateMaster(
        string $table,
        string $field,
        string $prefix,
        int $digit = 4
    ): string {

        $last = DB::table($table)
            ->where($field, 'like', $prefix . '%')
            ->orderByDesc($field)
            ->value($field);

        if (!$last) {

            $number = 1;

        } else {

            $number = (int) preg_replace('/\D/', '', $last) + 1;

        }

        return $prefix .
            str_pad(
                $number,
                $digit,
                '0',
                STR_PAD_LEFT
            );
    }
}