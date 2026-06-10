<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Transaksi Tunai - {{ $transaction->transaction_code }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { border: none; vertical-align: middle; }
        .title-text { text-align: center; line-height: 1.3; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .section-title { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td width="20%">
                @if ($office->logo)
                    <img src="{{ storage_path('app/public/' . $office->logo) }}" style="max-width: 120px; max-height: 60px;">
                @else
                    <h2>LOGO</h2>
                @endif
            </td>
            <td width="80%" class="title-text">
                <h2>Authorized Money Changer</h2>
                <strong>(Pedagang Valuta Asing)</strong><br>
                {{ $office->address }}<br>
                Phone / WA : {{ $office->phone }}<br>
                E-mail : monicasejahtera24@gmail.com
            </td>
        </tr>
    </table>

    <h3 style="text-align: center; text-decoration: underline;">FORMULIR TRANSAKSI TUNAI</h3>

    <table width="100%" style="margin-bottom: 10px;">
        <tr>
            <td width="50%">Cabang : {{ $cdd->cabang ?? '' }}</td>
            <td width="50%" style="text-align: right;">Tanggal : {{ $transaction->created_at->format('d-m-Y') }}</td>
        </tr>
    </table>

    <table class="table-data">
        <tr>
            <td colspan="4" class="section-title">DATA PROFIL PELAKU / PEMILIK DANA TRANSAKSI TUNAI</td>
        </tr>
        <tr>
            <td width="20%">Jenis Nasabah</td>
            <td width="30%">
                [ {{ ($cdd->jenis_nasabah ?? '') == 'Perorangan WNI' ? 'X' : ' ' }} ] Perorangan WNI<br>
                [ {{ ($cdd->jenis_nasabah ?? '') == 'Perorangan WNA' ? 'X' : ' ' }} ] Perorangan WNA
            </td>
            <td colspan="2">
                [ {{ ($cdd->jenis_nasabah ?? '') == 'Korporasi-Resident' ? 'X' : ' ' }} ] Korporasi-Resident<br>
                [ {{ ($cdd->jenis_nasabah ?? '') == 'Korporasi-Non Resident' ? 'X' : ' ' }} ] Korporasi-Non Resident
            </td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td colspan="2">{{ $cdd->nama_lengkap ?? '' }}</td>
            <td>NPWP: {{ $cdd->npwp ?? '' }}</td>
        </tr>
        <tr>
            <td rowspan="2">Alamat Domisili</td>
            <td colspan="2">Nama Jalan: {{ $cdd->nama_jalan ?? '' }}</td>
            <td>RT/RW: {{ $cdd->rt_rw ?? '' }}</td>
        </tr>
        <tr>
            <td>Kecamatan: {{ $cdd->kecamatan ?? '' }}</td>
            <td>Kabupaten: {{ $cdd->kabupaten ?? '' }}</td>
            <td>Provinsi: {{ $cdd->provinsi ?? '' }}</td>
        </tr>
    </table>

    <table class="table-data">
        <tr>
            <td colspan="3" class="section-title">DATA TRANSAKSI TUNAI (WAJIB DIISI OLEH NASABAH)</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total Jumlah Dana Tunai :</strong> {{ $cdd->total_dana_tunai ?? '' }}</td>
        </tr>
        <tr>
            <td width="33%">
                <strong>Tujuan Transaksi :</strong><br>
                [ {{ ($cdd->tujuan_transaksi ?? '') == 'Tabungan' ? 'X' : ' ' }} ] Tabungan / Investasi<br>
                [ {{ ($cdd->tujuan_transaksi ?? '') == 'Pajak' ? 'X' : ' ' }} ] Pembayaran Pajak<br>
                [ {{ ($cdd->tujuan_transaksi ?? '') == 'Bisnis' ? 'X' : ' ' }} ] Bisnis
            </td>
            <td width="33%">
                <strong>Hubungan Pemilik Dana :</strong><br>
                [ {{ ($cdd->hubungan_pemilik_dana ?? '') == 'Sendiri' ? 'X' : ' ' }} ] Rekening Sendiri<br>
                [ {{ ($cdd->hubungan_pemilik_dana ?? '') == 'Keluarga' ? 'X' : ' ' }} ] Keluarga Dekat
            </td>
            <td width="33%">
                <strong>Sumber Dana :</strong><br>
                [ {{ ($cdd->sumber_dana ?? '') == 'Gaji' ? 'X' : ' ' }} ] Gaji / Penghasilan<br>
                [ {{ ($cdd->sumber_dana ?? '') == 'Usaha' ? 'X' : ' ' }} ] Hasil Usaha
            </td>
        </tr>
    </table>

    <p style="font-size: 10px;">Dengan menandatangani aplikasi ini, saya menyatakan bahwa seluruh data dalam formulir ini adalah merupakan data yang benar dan terkini...</p>

    <table class="table-data" style="margin-top: 20px; text-align: center; height: 100px;">
        <tr>
            <td width="50%" style="vertical-align: bottom; height: 80px;">(Nama & Tanda Tangan Pelaku Transaksi)</td>
            <td width="50%" style="vertical-align: bottom;">No Telp Pelaku Transaksi Yang dapat dihubungi<br><strong>{{ $cdd->no_telp ?? '' }}</strong></td>
        </tr>
    </table>

</body>
</html>
