<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellTransactionCdd extends Model
{
    protected $fillable = [
        'sell_transaction_id',
        'jenis_nasabah',
        'nama_lengkap',
        'npwp',
        'nama_jalan',
        'rt_rw',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'cabang',
        'tujuan_transaksi',
        'hubungan_pemilik_dana',
        'sumber_dana',
        'total_dana_tunai',
        'no_telp',
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
    ];

    public function sellTransaction()
    {
        return $this->belongsTo(SellTransaction::class);
    }
}
