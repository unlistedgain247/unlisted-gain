<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>UnlistedGain — Unlisted Share Price List</title>
<style>
    @page {
        margin: 118px 32px 60px 32px;
    }

    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #2a2f28;
        font-size: 11px;
    }

    header {
        position: fixed;
        top: -98px;
        left: 0;
        right: 0;
        height: 88px;
    }

    footer {
        position: fixed;
        bottom: -45px;
        left: 0;
        right: 0;
        height: 35px;
        border-top: 1px solid #dfe6d8;
        padding-top: 7px;
        font-size: 8.5px;
        color: #8a938c;
        text-align: center;
    }

    .letterhead {
        width: 100%;
        border-collapse: collapse;
        border-bottom: 2px solid #87b942;
    }

    .letterhead td { vertical-align: middle; padding-bottom: 10px; }

    /* Source file is a wide logo+wordmark lockup (1089x296) — constrain by
       height only and let width scale proportionally, or it distorts. */
    .brand-logo { height: 44px; width: auto; }

    .doc-meta { text-align: right; font-size: 9.5px; color: #666; line-height: 1.5; }
    .doc-title { font-size: 14px; font-weight: bold; color: #1a1a1a; margin-bottom: 2px; }

    .disclaimer-strip {
        background: #f6faf0;
        border: 1px solid #e2edd2;
        padding: 7px 10px;
        font-size: 9px;
        color: #4a5344;
        margin-bottom: 10px;
    }

    /* Fixed layout is required — dompdf's automatic table layout algorithm
       miscalculates row heights when a <thead> repeats across page breaks,
       which produced a full-page solid header cell in testing. */
    table.price-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table.price-table thead { display: table-header-group; }
    table.price-table tbody { display: table-row-group; }

    /* Row splitting mid-company across a page break (name on one page,
       numbers on the next) is the other dompdf failure mode seen in testing —
       this is the documented fix. */
    table.price-table tbody tr {
        page-break-inside: avoid;
    }

    table.price-table thead th {
        background: #0c3105;
        color: #fff;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 7px 5px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
    }

    table.price-table thead th.num { text-align: right; }

    table.price-table tbody td {
        padding: 8px 5px;
        border-bottom: 1px solid #eef2ec;
        font-size: 10px;
        vertical-align: top;
        overflow: hidden;
    }

    table.price-table tbody td.num { text-align: right; font-family: 'Courier New', monospace; }

    table.price-table tbody tr.alt td { background: #f9fbf6; }

    .company-name { font-weight: bold; font-size: 11.5px; color: #1a1a1a; }

    /* Fixed box + max-width/max-height (not object-fit, which dompdf doesn't
       reliably support) so logos of any source aspect ratio stay contained
       without distortion. */
    table.price-table tbody td.logo-cell { text-align: center; padding: 6px 4px; }

    .logo-box {
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        border: 1px solid #e3e8dd;
        border-radius: 5px;
        background: #fff;
        overflow: hidden;
    }

    .logo-box img { max-width: 20px; max-height: 20px; vertical-align: middle; }

    .logo-box.logo-fallback {
        background: #eef5e4;
        color: #4a7c20;
        font-size: 9px;
        font-weight: bold;
    }

    .pe-good { color: #158a45; font-weight: bold; }
    .pe-mid  { color: #b4740e; font-weight: bold; }
    .pe-high { color: #c1281f; font-weight: bold; }
    .pe-na   { color: #9aa3a0; }
</style>
</head>
<body>

    <header>
        <table class="letterhead" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:170px;">
                    <img src="{{ $logoUri }}" class="brand-logo">
                </td>
                <td class="doc-meta">
                    <div class="doc-title">Unlisted Share Price List</div>
                    Generated on {{ $generatedAt }}<br>
                    unlistedshareswala.com
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Unlistedgain Advantage Solutions Pvt. Ltd. &middot; 113/2, Meenakshi Garden, Tilak Nagar, New Delhi &middot; support@unlistedgain.com &middot; +91 98918 81886
    </footer>

    <div class="disclaimer-strip">
        Pricing, market cap and P/E figures are indicative and subject to change at the time of execution. Please connect with our team for the latest price updates and availability.
    </div>

    <table class="price-table">
        <colgroup>
            <col width="30">
            <col width="231">
            <col width="80">
            <col width="65">
            <col width="65">
            <col width="80">
            <col width="50">
        </colgroup>
        <thead>
            <tr>
                <th></th>
                <th>Company</th>
                <th class="num">Price (Rs.)</th>
                <th class="num">Face Val.</th>
                <th class="num">Book Val.</th>
                <th class="num">Mkt Cap (Cr.)</th>
                <th class="num">P/E</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $i => $s)
            @php
                $pe = $s->pe_ratio;
                $peClass = 'pe-na';
                if (is_numeric($pe)) {
                    $peClass = $pe < 0 ? 'pe-high' : ($pe <= 25 ? 'pe-good' : ($pe <= 45 ? 'pe-mid' : 'pe-high'));
                }
                $initial = strtoupper(substr($s->UL_STOCKS_COMPNAME, 0, 1));
            @endphp
            <tr class="{{ $i % 2 === 1 ? 'alt' : '' }}">
                <td class="logo-cell">
                    @if($s->logo_data_uri)
                    <span class="logo-box"><img src="{{ $s->logo_data_uri }}"></span>
                    @else
                    <span class="logo-box logo-fallback">{{ $initial }}</span>
                    @endif
                </td>
                <td class="company-name">{{ $s->UL_STOCKS_COMPNAME }}</td>
                <td class="num">{{ $s->current_price !== null ? number_format($s->current_price, 2) : '-' }}</td>
                <td class="num">{{ $s->face_value !== null ? number_format($s->face_value, 2) : '-' }}</td>
                <td class="num">{{ $s->book_value !== null ? number_format($s->book_value, 2) : '-' }}</td>
                <td class="num">{{ $s->market_cap !== null ? number_format($s->market_cap, 1) : '-' }}</td>
                <td class="num {{ $peClass }}">{{ is_numeric($pe) ? number_format($pe, 1) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
