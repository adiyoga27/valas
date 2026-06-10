<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buy_transaction_cdds', function (Blueprint $table) {
            $table->string('penghasilan_tahun')->nullable()->after('total_dana_tunai');
            $table->string('negara')->nullable()->after('penghasilan_tahun');
            $table->string('kode_pos')->nullable()->after('negara');
            $table->string('jenis_pekerjaan')->nullable()->after('kode_pos');
            $table->string('jenis_pekerjaan_lainnya')->nullable()->after('jenis_pekerjaan');
            $table->string('nama_perusahaan')->nullable()->after('jenis_pekerjaan_lainnya');
            $table->string('jabatan')->nullable()->after('nama_perusahaan');
            $table->string('bentuk_hukum')->nullable()->after('jabatan');
            $table->string('bentuk_hukum_lainnya')->nullable()->after('bentuk_hukum');
            $table->string('bidang_usaha')->nullable()->after('bentuk_hukum_lainnya');
        });
    }

    public function down(): void
    {
        Schema::table('buy_transaction_cdds', function (Blueprint $table) {
            $table->dropColumn([
                'penghasilan_tahun',
                'negara',
                'kode_pos',
                'jenis_pekerjaan',
                'jenis_pekerjaan_lainnya',
                'nama_perusahaan',
                'jabatan',
                'bentuk_hukum',
                'bentuk_hukum_lainnya',
                'bidang_usaha',
            ]);
        });
    }
};
