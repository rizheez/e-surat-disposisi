<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $suratKeluar->nomor_surat }}</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 2.5cm;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 13pt;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .info-table .label {
            width: 120px;
            font-weight: normal;
        }
        .info-table .separator {
            width: 15px;
            text-align: center;
        }
        .perihal-row td {
            padding-top: 5px;
        }
        .content {
            text-align: justify;
            margin: 15px 0 30px 0;
        }
        .content p {
            text-indent: 40px;
            margin: 8px 0;
        }
        .signature {
            margin-top: 40px;
            float: right;
            text-align: center;
            width: 250px;
        }
        .signature .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
        .signature .title {
            font-size: 11pt;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $unitKerja?->nama ?? config('app.name') }}</h2>
        @if($unitKerja?->parent)
            <h3>{{ $unitKerja->parent->nama }}</h3>
        @endif
        <p>Sistem Persuratan Elektronik</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor</td>
            <td class="separator">:</td>
            <td>{{ $suratKeluar->nomor_surat }}</td>
            <td style="text-align: right; width: 200px;">
                {{ $suratKeluar->tanggal ? $suratKeluar->tanggal->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="separator">:</td>
            <td>{{ $suratKeluar->lampiran ?? '-' }}</td>
        </tr>
        <tr class="perihal-row">
            <td class="label">Perihal</td>
            <td class="separator">:</td>
            <td><strong>{{ $suratKeluar->perihal }}</strong></td>
        </tr>
    </table>

    <p>Kepada Yth.<br>
    <strong>{{ $suratKeluar->tujuan }}</strong><br>
    di Tempat</p>

    <div class="content">
        @if($suratKeluar->isi_surat)
            {!! $suratKeluar->isi_surat !!}
        @else
            <p>Dengan hormat,</p>
            <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
        @endif
    </div>

    <div style="clear: both;"></div>

    <div class="signature">
        <p>Hormat kami,</p>
        <p class="name">{{ $suratKeluar->createdBy?->name ?? 'Penanda Tangan' }}</p>
        <p class="title">{{ $suratKeluar->createdBy?->jabatan ?? '' }}</p>
    </div>

    <div class="footer">
        <p>Dokumen ini dihasilkan oleh {{ config('app.name') }} — {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>
</html>
