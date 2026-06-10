@extends('layouts.admin')
@section('title', 'Form CDD - ' . $buyTransaction->transaction_code)
@section('heading', 'Formulir CDD: ' . $buyTransaction->transaction_code)
@section('content')
<div class="max-w-3xl">
    @php $cdd = $buyTransaction->cdd; @endphp
    <form method="POST" action="{{ route('admin.buy-transactions.cdd-store', $buyTransaction) }}" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-5">
        @csrf
        <h3 class="text-sm font-bold text-gray-800 pb-2 border-b">DATA PROFIL PELAKU / PEMILIK DANA</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Nasabah</label>
                <select name="jenis_nasabah" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    @foreach(['Perorangan WNI', 'Perorangan WNA', 'Korporasi-Resident', 'Korporasi-Non Resident'] as $v)
                        <option value="{{ $v }}" {{ old('jenis_nasabah', $cdd->jenis_nasabah ?? '') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $cdd->nama_lengkap ?? $buyTransaction->customer_name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">NPWP</label>
                <input type="text" name="npwp" value="{{ old('npwp', $cdd->npwp ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">No. Telp</label>
                <input type="text" name="no_telp" value="{{ old('no_telp', $cdd->no_telp ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Nama Jalan</label>
                <input type="text" name="nama_jalan" value="{{ old('nama_jalan', $cdd->nama_jalan ?? $buyTransaction->customer_address) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">RT/RW</label><input type="text" name="rt_rw" value="{{ old('rt_rw', $cdd->rt_rw ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label><input type="text" name="kecamatan" value="{{ old('kecamatan', $cdd->kecamatan ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Kabupaten</label><input type="text" name="kabupaten" value="{{ old('kabupaten', $cdd->kabupaten ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Provinsi</label><input type="text" name="provinsi" value="{{ old('provinsi', $cdd->provinsi ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Kode Pos</label><input type="text" name="kode_pos" value="{{ old('kode_pos', $cdd->kode_pos ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Negara</label><input type="text" name="negara" value="{{ old('negara', $cdd->negara ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label><input type="text" name="cabang" value="{{ old('cabang', $cdd->cabang ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Penghasilan/Tahun (Rp Juta)</label><input type="number" name="penghasilan_tahun" value="{{ old('penghasilan_tahun', $cdd->penghasilan_tahun ?? '') }}" step="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
        </div>

        <h3 class="text-sm font-bold text-gray-800 pb-2 border-b pt-2">DATA PEKERJAAN & KORPORASI</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Pekerjaan</label>
                <select name="jenis_pekerjaan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    @foreach(['Pegawai Negeri', 'ABRI', 'Pegawai Swasta', 'Wiraswasta', 'Ibu Rumah Tangga', 'Pelajar', 'Pedagang', 'Lainnya'] as $v)
                        <option value="{{ $v }}" {{ old('jenis_pekerjaan', $cdd->jenis_pekerjaan ?? '') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Jenis Pekerjaan Lainnya</label><input type="text" name="jenis_pekerjaan_lainnya" value="{{ old('jenis_pekerjaan_lainnya', $cdd->jenis_pekerjaan_lainnya ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Nama Perusahaan</label><input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $cdd->nama_perusahaan ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Jabatan</label><input type="text" name="jabatan" value="{{ old('jabatan', $cdd->jabatan ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bentuk Hukum</label>
                <select name="bentuk_hukum" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    @foreach(['CV', 'PT', 'Yayasan', 'Firma', 'Lainnya'] as $v)
                        <option value="{{ $v }}" {{ old('bentuk_hukum', $cdd->bentuk_hukum ?? '') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Bentuk Hukum Lainnya</label><input type="text" name="bentuk_hukum_lainnya" value="{{ old('bentuk_hukum_lainnya', $cdd->bentuk_hukum_lainnya ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Bidang Usaha</label><input type="text" name="bidang_usaha" value="{{ old('bidang_usaha', $cdd->bidang_usaha ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
        </div>

        <h3 class="text-sm font-bold text-gray-800 pb-2 border-b pt-2">DATA TRANSAKSI TUNAI</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Total Dana Tunai</label><input type="text" name="total_dana_tunai" value="{{ old('total_dana_tunai', $cdd->total_dana_tunai ?? 'Rp ' . number_format($buyTransaction->grand_total, 0, ',', '.')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tujuan Transaksi</label>
                <select name="tujuan_transaksi" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach(['Tabungan', 'Pajak', 'Bisnis'] as $v)
                        <option value="{{ $v }}" {{ old('tujuan_transaksi', $cdd->tujuan_transaksi ?? '') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Hubungan Pemilik Dana</label>
                <select name="hubungan_pemilik_dana" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach(['Sendiri', 'Keluarga'] as $v)
                        <option value="{{ $v }}" {{ old('hubungan_pemilik_dana', $cdd->hubungan_pemilik_dana ?? '') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sumber Dana</label>
                <select name="sumber_dana" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach(['Gaji', 'Usaha'] as $v)
                        <option value="{{ $v }}" {{ old('sumber_dana', $cdd->sumber_dana ?? '') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Simpan CDD</button>
            <a href="{{ route('admin.buy-transactions.show', $buyTransaction) }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Kembali</a>
        </div>
    </form>
</div>
@endsection
