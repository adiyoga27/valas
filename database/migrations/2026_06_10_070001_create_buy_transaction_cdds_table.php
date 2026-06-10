<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buy_transaction_cdds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buy_transaction_id')->constrained('buy_transactions')->cascadeOnDelete();
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
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buy_transaction_cdds');
    }
};
