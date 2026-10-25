<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'rustic-forest' => 'Rustic',
            'modern-minimalist' => 'Minimalist',
        ] as $key => $category) {
            DB::table('templates')
                ->where('key', $key)
                ->where('price', 0)
                ->update(['price' => 150000]);

            DB::table('templates')
                ->where('key', $key)
                ->where('category', 'Wedding')
                ->update(['category' => $category]);
        }
    }

    public function down(): void
    {
    }
};
