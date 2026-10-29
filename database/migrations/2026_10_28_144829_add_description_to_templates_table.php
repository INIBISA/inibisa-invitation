<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->text('description')->nullable()->after('category');
        });

        foreach ([
            'eternal-ivory' => 'Nuansa ivory yang tenang dengan serif anggun dan ornamen floral klasik.',
            'modern-minimalist' => 'Desain bersih dengan ruang lapang, fokus pada tipografi dan informasi acara.',
            'rustic-forest' => 'Nuansa kayu dan dedaunan hangat untuk pernikahan outdoor yang intim.',
            'sweet-blossom' => 'Nuansa blush romantis dengan bunga sakura yang hangat dan manis.',
            'midnight-nusantara' => 'Nuansa biru malam mewah dengan aksen emas dan geometri Nusantara.',
            'voxel-voyage' => 'Dunia voxel penuh warna dengan panel permainan, peta perjalanan, dan nuansa petualangan ceria.',
        ] as $key => $description) {
            DB::table('templates')->where('key', $key)->whereNull('description')->update(['description' => $description]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
