<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Transaksi Tunai - {{ $transaction->transaction_code }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header { width: 100%; margin-bottom: 4px; }
        .header td { border: none; vertical-align: middle; }
        .title-text { text-align: center; line-height: 1.1; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 10px; }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
            line-height: 1.2;
        }
        .section-title { font-weight: bold; font-size: 10px; background-color: #f2f2f2; }
        .check-box { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; text-align: center; line-height: 9px; font-size: 9px; vertical-align: top; margin-top: 1px; }
        .nested-table { width: 100%; border-collapse: collapse; margin: 0; padding: 0; }
        .nested-table td { border: none; padding: 0 0 1px 0; }
        .checklist-table { width: 100%; border-collapse: collapse; border: none; margin-top: 2px; }
        .checklist-table td { border: none; padding: 0 0 2px 0; vertical-align: top; }
        .checkbox-col { width: 14px; }
    </style>
</head>
<body>

    <table class="header" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td width="20%" style="text-align: left; vertical-align: top;">
                <img src="{{ public_path('assets/images/logo_kop.png') }}" style="max-width: 200px; max-height: 100px; display: block; margin-top: -5px;">
            </td>
            <td width="60%" style="text-align: center; vertical-align: top;">
                <h2 style="margin: 0; font-size: 15px;">Authorized Money Changer</h2>
                <div style="font-size: 11px; margin-bottom: 0;">(Pedagang Valuta Asing)</div>
                <div style="font-size: 10px;">
                    {{ $office->address }}<br>
                    Phone / WA : {{ $office->phone }}<br>
                    E-mail : monicasejahtera24@gmail.com
                </div>
            </td>
            <td width="20%"></td>
        </tr>
    </table>
    <div style="text-align: center; margin-top: 4px; font-size: 13px; font-weight: bold; text-decoration: underline; margin-bottom: 5px;">FORMULIR TRANSAKSI TUNAI</div>

    <table width="100%" style="margin-bottom: 2px; font-size: 10px;">
        <tr>
            <td width="50%">Cabang : {{ $cdd->cabang ?? '' }}</td>
            <td width="50%" style="text-align: right;">Tanggal : {{ $transaction->created_at->format('d-m-Y') }}</td>
        </tr>
    </table>

    <table class="table-data">
        <tr>
            <td colspan="5" class="section-title">DATA PROFIL PELAKU / PEMILIK DANA TRANSAKSI TUNAI</td>
        </tr>
        <tr>
            <td width="20%">Jenis Nasabah</td>
            <td width="40%" colspan="2">
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_nasabah ?? '') == 'Perorangan WNI' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Perorangan WNI</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_nasabah ?? '') == 'Perorangan WNA' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Perorangan WNA</td>
                    </tr>
                </table>
            </td>
            <td width="40%" colspan="2">
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_nasabah ?? '') == 'Korporasi-Resident' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Korporasi-Resident</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_nasabah ?? '') == 'Korporasi-Non Resident' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Korporasi-Non Resident</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="20%">Nama Lengkap</td>
            <td width="60%" colspan="3">{{ $cdd->nama_lengkap ?? '' }}</td>
            <td width="20%">NPWP: {{ $cdd->npwp ?? '' }}</td>
        </tr>
        <tr>
            <td rowspan="2" width="20%">Alamat Domisili<br><br><br>Jika Beda<br>Dengan ID</td>
            <td width="60%" colspan="3">Nama Jalan: {{ $cdd->nama_jalan ?? '' }}</td>
            <td width="20%">RT/RW: {{ $cdd->rt_rw ?? '' }}</td>
        </tr>
        <tr>
            <td width="20%">Kecamatan:<br>{{ $cdd->kecamatan ?? '' }}</td>
            <td width="20%">Kabupaten:<br>{{ $cdd->kabupaten ?? '' }}</td>
            <td width="20%">Provinsi:<br>{{ $cdd->provinsi ?? '' }}</td>
            <td width="20%">Kode Pos:<br>{{ $cdd->kode_pos ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="5">
                Alamat Domisili di Negara Asal (khusus Perorangan WNA & Korporasi Non Resident)<br>
                <table width="100%" style="border:none; margin-top: 2px;">
                    <tr>
                        <td style="border:none; width: 40%; padding: 0;"></td>
                        <td style="border:none; width: 30%; padding: 0;">Negara : {{ $cdd->negara ?? '' }}</td>
                        <td style="border:none; width: 30%; padding: 0;">Kode Pos : {{ $cdd->kode_pos ?? '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="20%" style="vertical-align: middle;">Rata-rata Penghasilan/thn<br>(Jutaan Rupiah)</td>
            <td colspan="4">
                {{ $cdd->penghasilan_tahun ? 'Rp ' . number_format((float) $cdd->penghasilan_tahun, 0, ',', '.') : '' }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold;">Data Pekerjaan untuk Pelaku Transaksi Perorangan</td>
            <td colspan="2" style="text-align: center; font-weight: bold;">Untuk Korporasi</td>
        </tr>
        <tr>
            <td colspan="2" rowspan="2" width="40%">
                Jenis Pekerjaan :
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Pegawai Negeri' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Pegawai Negeri/DPR/MPR/DPD</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'ABRI' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>ABRI/Polisi <i>(termasuk Pensiunan)</i></td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Pegawai Swasta' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Pegawai Swasta <i>(termasuk Pensiunan)</i></td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Wiraswasta' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Wiraswasta / Pemilik Usaha</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Ibu Rumah Tangga' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Ibu Rumah Tangga</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Pelajar' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Pelajar / Mahasiswa</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Pedagang' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Pedagang</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->jenis_pekerjaan ?? '') == 'Lainnya' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Lainnya, sebutkan {{ $cdd->jenis_pekerjaan_lainnya ?? '_________________' }}</td>
                    </tr>
                </table>
            </td>
            <td width="20%">
                Nama Perusahaan Tempat Bekerja :<br><br>{{ $cdd->nama_perusahaan ?? '' }}
            </td>
            <td colspan="2" rowspan="2" width="40%">
                Bentuk Hukum Badan Usaha :
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->bentuk_hukum ?? '') == 'CV' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>CV</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->bentuk_hukum ?? '') == 'PT' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>PT</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->bentuk_hukum ?? '') == 'Yayasan' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Yayasan</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->bentuk_hukum ?? '') == 'Firma' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Firma</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->bentuk_hukum ?? '') == 'Lainnya' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Lainnya, Sebutkan {{ $cdd->bentuk_hukum_lainnya ?? '_________________' }}</td>
                    </tr>
                </table>
                <div style="margin-top: 4px;">
                    Bidang Usaha Korporasi:<br><br>{{ $cdd->bidang_usaha ?? '' }}
                </div>
            </td>
        </tr>
        <tr>
            <td>
                Jabatan:<br><br>{{ $cdd->jabatan ?? '' }}
            </td>
        </tr>
    </table>

    <table class="table-data">
        <tr>
            <td colspan="3" class="section-title">DATA TRANSAKSI TUNAI (WAJIB DIISI OLEH NASABAH)</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total Jumlah Dana Tunai :</strong> {{ $cdd->total_dana_tunai ? 'Rp ' . number_format((float) $cdd->total_dana_tunai, 0, ',', '.') : '' }}</td>
        </tr>
        <tr>
            <td width="33%">
                Tujuan Transaksi :<br>
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->tujuan_transaksi ?? '') == 'Tabungan' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Tabungan / Investasi</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->tujuan_transaksi ?? '') == 'Pajak' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Pembayaran Pajak</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Pembayaran Tagihan Perjalanan /<br>Travelling</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->tujuan_transaksi ?? '') == 'Bisnis' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Bisnis</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Pembayaran Asuransi / Pinjaman</td>
                    </tr>
                </table>
            </td>
            <td width="33%">
                Hubungan Pemilik Dana dengan<br>Penerima Dana :<br>
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->hubungan_pemilik_dana ?? '') == 'Sendiri' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Rekening Sendiri</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->hubungan_pemilik_dana ?? '') == 'Keluarga' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Keluarga Dekat</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Rekan Bisnis</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Klien</td>
                    </tr>
                </table>
            </td>
            <td width="34%">
                Sumber Dana :<br>
                <table class="checklist-table">
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->sumber_dana ?? '') == 'Gaji' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Gaji / Penghasilan</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Penjualan Properti</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Tabungan / Hasil Investasi</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">{!! ($cdd->sumber_dana ?? '') == 'Usaha' ? 'X' : '&nbsp;' !!}</div></td>
                        <td>Hasil Usaha</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Warisan</td>
                    </tr>
                    <tr>
                        <td class="checkbox-col"><div class="check-box">&nbsp;</div></td>
                        <td>Hibah</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="font-size: 10px; line-height: 1.2; margin-bottom: 4px;">
        Dengan menandatangani aplikasi ini, saya menyatakan bahwa seluruh data dalam formulir ini adalah merupakan data yang benar dan terkini dan transaksi bukan dalam rangka pencucian uang atau pendanaan terorisme.
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
        <tr>
            <td style="border: 1px solid #000; width: 50%; height: 65px; vertical-align: bottom; text-align: center; padding: 4px;">
                (Nama & Tanda Tangan Pelaku Transaksi)
            </td>
            <td style="border: 1px solid #000; width: 50%; height: 65px; vertical-align: bottom; text-align: center; padding: 4px;">
                <div style="margin-bottom: 12px;"><strong>{{ $cdd->no_telp ?? '_________________________' }}</strong></div>
                <div style="font-size: 10px;">No Telp Pelaku Transaksi Yang dapat dihubungi</div>
            </td>
        </tr>
    </table>
    <div style="font-weight: bold; font-size: 10px; margin-top: 2px;">
        Data Wajib Diisi
    </div>

</body>
</html>
