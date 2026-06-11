<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_transaction_cdds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_transaction_id')->constrained('sell_transactions')->cascadeOnDelete();
            $table->string('jenis_nasabah')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nama_jalan')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('cabang')->nullable();
            $table->string('tujuan_transaksi')->nullable();
            $table->string('hubungan_pemilik_dana')->nullable();
            $table->string('sumber_dana')->nullable();
            $table->string('total_dana_tunai')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('penghasilan_tahun')->nullable();
            $table->string('negara')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('jenis_pekerjaan')->nullable();
            $table->string('jenis_pekerjaan_lainnya')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bentuk_hukum')->nullable();
            $table->string('bentuk_hukum_lainnya')->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_transaction_cdds');
    }
};
