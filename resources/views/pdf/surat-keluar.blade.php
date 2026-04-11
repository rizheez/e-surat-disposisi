<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $suratKeluar->nomor_surat }}</title>
    <style>
        @font-face {
            font-family: 'Candara';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/Candara.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Candaraz';
            font-style: italic;
            font-weight: normal;
            src: url("{{ public_path('fonts/Candaraz.ttf') }}") format('truetype');
        }

        @page {
            margin: 0;
        }

        .candaraz {
            font-family: 'Candaraz', sans-serif;
            font-size: 11pt;
            /* make italic */
            font-style: italic;
            line-height: 0.5;
        }

        body {
            font-family: 'Candara', sans-serif;
            font-size: 11pt;
            line-height: 1;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-wrapper {
            position: relative;
            width: 100%;
            min-height: 100%;
        }

        /* ── Header Kop ── */
        .header-kop {
            width: 100%;
            text-align: center;
            padding-bottom: 5px;
        }

        .header-kop img {
            width: 100%;
            max-height: 110px;
        }

        /* ── Content area ── */
        .content-area {
            padding: 10px 60px 80px 70px;
        }

        /* ── Tanggal ── */
        .tanggal {
            text-align: right;
            margin-bottom: 10px;
        }

        /* ── Info table (Nomor, Lampiran, Perihal) ── */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            vertical-align: top;
            padding: 1px 0;
        }

        .info-table .label {
            width: 85px;
            font-weight: normal;
        }

        .info-table .separator {
            width: 15px;
            text-align: center;
        }

        /* ── Isi Surat (dynamic) ── */
        .isi-surat {
            text-align: justify;
            margin: 10px 0 20px 0;
        }

        .isi-surat p {
            margin: 5px 0;
        }

        .isi-surat ol,
        .isi-surat ul {
            margin: 5px 0 5px 20px;
            padding-left: 15px;
        }

        /* ── TTD ── */
        .signature-wrapper {
            width: 100%;
            margin-top: 20px;
        }

        .signature-wrapper td.signature-cell {
            text-align: left;
            width: 250px;
            vertical-align: top;
        }

        .signature-wrapper .qr-ttd {
            margin: 5px 0;
        }

        .signature-wrapper .qr-ttd img {
            width: 80px;
            height: 80px;
        }

        .signature-wrapper .name {
            margin-top: 5px;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-wrapper .name.no-qr {
            margin-top: 80px;
        }

        .signature-wrapper .title {
            font-size: 11pt;
            margin: 0;
        }

        /* ── Tembusan ── */
        .tembusan {
            clear: both;
            margin-top: 30px;
        }

        .tembusan .tembusan-label {
            font-weight: normal;
        }

        .tembusan ol {
            margin: 2px 0 0 0;
            padding-left: 20px;
        }

        .tembusan ol li {
            margin: 1px 0;
        }

        /* ── Footer Kop ── */
        .footer-kop {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
        }

        .footer-kop table {
            width: 100%;
        }

        .footer-kop td {
            text-align: center;
        }

        .footer-kop img {
            width: 75%;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        {{-- ═══ Header Kop ═══ --}}
        <div class="header-kop">
            <img src="{{ public_path('images/header kop.png') }}" alt="Kop Surat">
        </div>

        <div class="content-area">
            {{-- ═══ Tanggal ═══ --}}
            <div class="tanggal">
                {{ $suratKeluar->tanggal_surat?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
            </div>

            {{-- ═══ Info Surat (Fixed) ═══ --}}
            <table class="info-table">
                <tr>
                    <td class="label">Nomor</td>
                    <td class="separator">:</td>
                    <td>{{ $suratKeluar->nomor_surat }}</td>
                </tr>
                <tr>
                    <td class="label">Lampiran</td>
                    <td class="separator">:</td>
                    <td>{{ $suratKeluar->lampiran ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Perihal</td>
                    <td class="separator">:</td>
                    <td>{{ $suratKeluar->perihal }}</td>
                </tr>
            </table>

            {{-- ═══ Kepada & Salam Pembuka (Fixed Template) ═══ --}}
            <div style="margin-bottom: 10px;">
                <p>Kepada, Yth.</p>
                <p><strong>{{ $suratKeluar->tujuan }}</strong></p>
                <p>Di &ndash; Tempat</p>
                {{-- <p style="margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p> --}}
            </div>

            {{-- ═══ Isi Surat (Dynamic — hanya isi/body surat) ═══ --}}
            <div class="isi-surat">
                <p class="candaraz"><strong>Assalamu&rsquo;alaikum Wr. Wb.</strong></p>

                @if ($suratKeluar->isi_surat)
                    {!! $suratKeluar->isi_surat !!}
                @else
                    <p><em>(Isi surat dari file upload)</em></p>
                @endif
            </div>

            {{-- ═══ Salam Penutup (Fixed Template) ═══ --}}
            <div style="margin-bottom: 5px;">
                <p class="candaraz"><strong>Wallahul Muwaffieq Ilaa Aqwamith Tharieq</strong></p>
                <p class="candaraz"><strong>Wassalammu&rsquo;alaikum Wp. Wb.</strong></p>
            </div>

            {{-- ═══ TTD (Fixed) ═══ --}}
            <div style="clear: both;"></div>

            @php
                $hasQr = isset($qrDataUri) && $qrDataUri && $suratKeluar->qr_token;
            @endphp
            <table class="signature-wrapper">
                <tr>
                    <td>&nbsp;</td>
                    <td class="signature-cell">
                        @if ($suratKeluar->penandatangan)
                            <p>{{ $suratKeluar->penandatangan->jabatan ?? 'Rektor,' }}</p>
                            @if ($hasQr)
                                <div class="qr-ttd"><img src="{{ $qrDataUri }}"></div>
                            @endif
                            <p class="name {{ !$hasQr ? 'no-qr' : '' }}">{{ $suratKeluar->penandatangan->name }}</p>
                            <p class="title">{{ $suratKeluar->penandatangan->nip ?? '' }}</p>
                        @else
                            <p>Rektor,</p>
                            @if ($hasQr)
                                <div class="qr-ttd"><img src="{{ $qrDataUri }}"></div>
                            @endif
                            <p class="name {{ !$hasQr ? 'no-qr' : '' }}">
                                {{ $suratKeluar->pembuat?->name ?? 'Penanda Tangan' }}</p>
                            <p class="title">{{ $suratKeluar->pembuat?->jabatan ?? '' }}</p>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- ═══ Tembusan (Fixed) ═══ --}}
            @if ($suratKeluar->tembusan && count($suratKeluar->tembusan) > 0)
                <div class="tembusan">
                    <span class="tembusan-label">Tembusan :</span>
                    <ol>
                        @foreach ($suratKeluar->tembusan as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>

        {{-- ═══ Footer Kop ═══ --}}
        <div class="footer-kop">
            <table>
                <tr>
                    <td>
                        <img src="{{ public_path('images/footer kop.png') }}" alt="Footer Kop">
                    </td>
                </tr>
            </table>
        </div>


    </div>
</body>

</html>
