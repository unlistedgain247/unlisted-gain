@extends('layout.app')

@section('title', $stock->UL_STOCKS_COMPNAME . ' Unlisted Share Price, Financials & Analysis | UnlistedGain')
@section('meta_description', 'Buy ' . $stock->UL_STOCKS_COMPNAME . ' unlisted shares at the best price. View current share price, market cap, P/E ratio, financials and investment thesis on UnlistedGain.')
@section('meta_keywords', strtolower($stock->UL_STOCKS_COMPNAME) . ' unlisted shares, ' . strtolower($stock->UL_STOCKS_COMPNAME) . ' share price, ' . strtolower($stock->UL_STOCKS_COMPNAME) . ' pre-ipo, buy ' . strtolower($stock->UL_STOCKS_COMPNAME) . ' shares')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/company.css') }}?v={{ filemtime(public_path('assets/css/pagecss/company.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [
    ['label' => 'Unlisted Shares', 'url' => url('/unlisted-shares-price-list-india')],
    ['label' => $stock->UL_STOCKS_COMPNAME],
]])
@endsection

@section('content')
<div class="ug-company-page">

    {{-- ── HERO ── --}}
    <div class="cp-hero">
        <div class="cp-container">
            <div class="cp-hero-grid">

                {{-- Left: identity --}}
                <div class="cp-identity">
                    <div class="cp-logo-wrap">
                        @if($stock->UL_STOCKS_LOGO_LINK)
                        <img src="{{ url($stock->UL_STOCKS_LOGO_LINK) }}"
                             alt="{{ $stock->UL_STOCKS_COMPNAME }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        @endif
                        <div class="cp-logo-fallback" style="{{ $stock->UL_STOCKS_LOGO_LINK ? 'display:none' : 'display:flex' }}">
                            {{ strtoupper(substr($stock->UL_STOCKS_COMPNAME, 0, 2)) }}
                        </div>
                    </div>

                    <div class="cp-identity-body">
                        <h1 class="cp-company-name">{{ $stock->UL_STOCKS_COMPNAME }}</h1>
                        <div class="cp-badges">
                            @if($stock->UL_STOCKS_INDUSTRY)
                            <span class="cp-badge cp-badge--industry">
                                <i class="fas fa-industry"></i> {{ $stock->UL_STOCKS_INDUSTRY }}
                            </span>
                            @endif
                            @if($stock->UL_STOCKS_COMPNAME_TYPE)
                            <span class="cp-badge cp-badge--type">{{ ucfirst($stock->UL_STOCKS_COMPNAME_TYPE) }}</span>
                            @endif
                            <span class="cp-badge cp-badge--live"><i class="fas fa-circle"></i> Active</span>
                        </div>
                        <div class="cp-meta-row">
                            @if($stock->UL_STOCKS_ISIN)
                            <span class="cp-meta-chip"><i class="fas fa-fingerprint"></i> {{ $stock->UL_STOCKS_ISIN }}</span>
                            @endif
                            @if($stock->UL_STOCKS_INC_YEAR)
                            <span class="cp-meta-chip">
                                <i class="fas fa-calendar-alt"></i>
                                Est. {{ $stock->UL_STOCKS_INC_MONTH ? date('M', mktime(0,0,0,(int)$stock->UL_STOCKS_INC_MONTH)) . ' ' : '' }}{{ $stock->UL_STOCKS_INC_YEAR }}
                            </span>
                            @endif
                            @if($stock->UL_STOCKS_WEBSITE)
                            <a href="{{ $stock->UL_STOCKS_WEBSITE }}" target="_blank" rel="noopener" class="cp-meta-link">
                                <i class="fas fa-external-link-alt"></i> Website
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: price card --}}
                <div class="cp-price-card">
                    @if($currentPrice)
                    <div class="cp-price-header">
                        <span class="cp-price-label">Current Price</span>
                        @if($priceData && $priceData->UL_PD_DATE)
                        <span class="cp-price-updated">
                            <i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($priceData->UL_PD_DATE)->format('d M Y') }}
                        </span>
                        @endif
                    </div>
                    <div class="cp-price-main">
                        <div class="cp-bid-price">&#8377;{{ number_format((float)$currentPrice, 2) }}</div>
                        @if($askPrice)
                        <div class="cp-ask-row">
                            <span class="cp-price-type">Bid</span>
                            <span class="cp-price-divider">|</span>
                            <span class="cp-price-type">Ask</span>
                            <span class="cp-ask-val">&#8377;{{ number_format((float)$askPrice, 2) }}</span>
                        </div>
                        @endif
                    </div>
                    @if($stock->UL_STOCKS_BUY_SELL_FLAG)
                    <div class="cp-cta-row">
                        <button class="cp-btn-buy invest-trigger"
                            data-type="buy"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $currentPrice }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }}">
                            <i class="fas fa-arrow-trend-up"></i> Buy Shares
                        </button>
                        <button class="cp-btn-sell invest-trigger"
                            data-type="sell"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $currentPrice }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }}">
                            <i class="fas fa-arrow-trend-down"></i> Sell
                        </button>
                    </div>
                    @if($stock->UL_STOCKS_LOT_SIZE)
                    <p class="cp-lot-note"><i class="fas fa-info-circle"></i> Min lot: {{ number_format($stock->UL_STOCKS_LOT_SIZE) }} shares</p>
                    @endif
                    @endif
                    @else
                    <div class="cp-price-header"><span class="cp-price-label">Price</span></div>
                    <div class="cp-price-main">
                        <div class="cp-bid-price cp-price-na">&mdash;</div>
                        <p class="cp-price-na-note">Contact us for the latest price</p>
                    </div>
                    <a href="/connect" class="cp-btn-quote"><i class="fas fa-phone-alt"></i> Get a Quote</a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ── STATS STRIP ── --}}
    @if($currentPrice || $latestFin)
    <div class="cp-stats-strip">
        <div class="cp-container">
            <div class="cp-stats-row">
                @if($marketCap !== null)
                <div class="cp-stat">
                    <span class="cp-stat-label">Market Cap</span>
                    <span class="cp-stat-value">&#8377;{{ number_format($marketCap, 0) }} Cr</span>
                </div>
                @endif
                @if($peRatio !== null)
                <div class="cp-stat">
                    <span class="cp-stat-label">P/E Ratio</span>
                    <span class="cp-stat-value">{{ number_format($peRatio, 1) }}x</span>
                </div>
                @endif
                <div class="cp-stat">
                    <span class="cp-stat-label">EPS</span>
                    <span class="cp-stat-value @if($eps !== null && $eps < 0) cp-stat-neg @endif">
                        @if($eps !== null)&#8377;{{ number_format($eps, 2) }}@else<span style="color:#bbb">—</span>@endif
                    </span>
                </div>
                @if($latestFin && $latestFin->UL_FIN_FV !== null)
                <div class="cp-stat">
                    <span class="cp-stat-label">Face Value</span>
                    <span class="cp-stat-value">&#8377;{{ number_format((float)$latestFin->UL_FIN_FV, 2) }}</span>
                </div>
                @endif
                @if($bookValue !== null)
                <div class="cp-stat">
                    <span class="cp-stat-label">Book Value</span>
                    <span class="cp-stat-value">&#8377;{{ number_format($bookValue, 2) }}</span>
                </div>
                @endif
                @if($currentPrice && $bookValue && $bookValue > 0)
                <div class="cp-stat">
                    <span class="cp-stat-label">P/B Ratio</span>
                    <span class="cp-stat-value">{{ number_format((float)$currentPrice / $bookValue, 2) }}x</span>
                </div>
                @endif
                @if($latestFin && $latestFin->UL_FIN_PROMOTERS_HOLDING !== null)
                <div class="cp-stat">
                    <span class="cp-stat-label">Promoter Hold.</span>
                    <span class="cp-stat-value">{{ number_format((float)$latestFin->UL_FIN_PROMOTERS_HOLDING, 2) }}%</span>
                </div>
                @endif
                @if($stock->UL_STOCKS_LOT_SIZE)
                <div class="cp-stat">
                    <span class="cp-stat-label">Min Lot</span>
                    <span class="cp-stat-value">{{ number_format($stock->UL_STOCKS_LOT_SIZE) }} sh.</span>
                </div>
                @endif
                @if($latestFin && $latestFin->UL_FIN_TOTAL_DEBT !== null)
                @php $debtCr = round((float)$latestFin->UL_FIN_TOTAL_DEBT * (float)($latestFin->UL_FIN_Unit ?? 1) / 10000000, 1); @endphp
                <div class="cp-stat">
                    <span class="cp-stat-label">Total Debt</span>
                    <span class="cp-stat-value">&#8377;{{ number_format($debtCr, 0) }} Cr</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── SECTION NAV ── --}}
    <div class="cp-section-nav" id="cpSectionNav">
        <div class="cp-container">
            <nav class="cp-snav-links">
                @if($stock->UL_STOCKS_ABOUT)
                <a href="#about" class="cp-snav-link active">Overview</a>
                @endif
                @if($financials->isNotEmpty())
                <a href="#financials" class="cp-snav-link">Financials</a>
                @endif
                @if($thesisHtml)
                <a href="#thesis" class="cp-snav-link">Investment Thesis</a>
                @endif
                <a href="#details" class="cp-snav-link">Company Info</a>
            </nav>
        </div>
    </div>

    {{-- ── PAGE BODY ── --}}
    <div class="cp-body">
        <div class="cp-container">

            {{-- About --}}
            @if($stock->UL_STOCKS_ABOUT)
            <section class="cp-section" id="about">
                <div class="cp-section-head">
                    <h2>About <span>{{ $stock->UL_STOCKS_COMPNAME }}</span></h2>
                </div>
                <div class="cp-about-text">
                    {!! nl2br(e($stock->UL_STOCKS_ABOUT)) !!}
                </div>
            </section>
            @endif

            {{-- Financials --}}
            @if($financials->isNotEmpty() || $quarterlyFin->isNotEmpty())
            <section class="cp-section cp-section--fin" id="financials">
                <div class="fin-header">
                    <h2 class="fin-heading">Financial <span>Highlights</span></h2>
                    <div class="fin-period-row" id="finPeriodRow">
                        <button class="fin-period-btn active" data-period="y">Yearly</button>
                        {{-- quarterly btn added by JS if data exists --}}
                    </div>
                </div>

                @php
                $toCr = fn($v,$u) => ($v===null||$v==='') ? null : round((float)$v*(float)$u/10000000, 2);
                $fmtN = function($v) {
                    if ($v===null) return '<span class="fd">—</span>';
                    $a=abs($v);
                    if ($a>=1000) return number_format($v,0);
                    if ($a>=100)  return number_format($v,1);
                    if ($a>=10)   return number_format($v,1);
                    return number_format($v,2);
                };
                $fmtCr  = fn($v) => $v===null ? '<span class="fd">—</span>' : $fmtN($v);
                $fmtEps = fn($v) => ($v===null||$v==='') ? '<span class="fd">—</span>' : '&#8377;'.number_format((float)$v,2);
                $fmtPct = fn($v) => ($v===null||$v==='') ? '<span class="fd">—</span>' : number_format((float)$v,1).'%';
                // Yearly: always FY{YY} — take first 4 chars as year regardless of YYYYMM vs YYYY
                $yearLabel = function($p) {
                    return 'FY' . substr(substr((string)$p, 0, 4), -2);
                };
                // Quarterly: Mon-YY format (e.g. Mar-24)
                $periodLabel = function($p) use ($yearLabel) {
                    $s=(string)$p;
                    if (strlen($s)<=4) return $yearLabel($s);
                    if (strlen($s)===6) {
                        $yr=substr($s,0,4); $mo=(int)substr($s,4,2);
                        $mn=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        return ($mn[$mo-1]??$mo).'-'.substr($yr,-2);
                    }
                    return $s;
                };
                $calcYoy = fn($cur,$prev) => ($prev&&abs((float)$prev)>0.001) ? round(((float)$cur-(float)$prev)/abs((float)$prev)*100,1) : null;
                $calcCagr = function($l,$o,$n) {
                    if (!$o||!$l||abs((float)$o)<0.001||$n<=0) return null;
                    $sign=(float)$l>0?1:-1;
                    return round((pow(abs((float)$l/abs((float)$o)),1/$n)-1)*$sign*100,1);
                };
                $fmtChg = function($v) {
                    if ($v===null) return '<span class="fd">—</span>';
                    $cls=$v>=0?'fin-pos':'fin-neg';
                    return '<span class="'.$cls.'">'.($v>=0?'+':'').number_format($v,1).'%</span>';
                };

                // Yearly: DESC from DB → reverse for display oldest→latest
                $yFin    = $financials->reverse()->values();
                $yCount  = $yFin->count();
                $yLatest = $yFin->last();
                $yPrev   = $yCount >= 2 ? $yFin[$yCount-2] : null;
                $yOldest = $yFin->first();
                $yPer    = max(1, $yCount-1);

                $yoyF  = fn($key) => $calcYoy(
                    $yLatest  ? $toCr($yLatest->$key??null,  (float)($yLatest->UL_FIN_Unit??1))  : null,
                    $yPrev    ? $toCr($yPrev->$key??null,    (float)($yPrev->UL_FIN_Unit??1))    : null
                );
                $cagrF = fn($key) => $calcCagr(
                    $yLatest  ? $toCr($yLatest->$key??null,  (float)($yLatest->UL_FIN_Unit??1))  : null,
                    $yOldest  ? $toCr($yOldest->$key??null,  (float)($yOldest->UL_FIN_Unit??1))  : null,
                    $yPer
                );

                // Quarterly
                $qFin     = $quarterlyFin->reverse()->values();
                $qCount   = $qFin->count();
                $qLatest  = $qFin->last();
                $qPrev    = $qCount >= 2 ? $qFin[$qCount-2] : null;
                $qSameQly = $qCount >= 5 ? $qFin[$qCount-5] : null; // same quarter, last year
                $qoyF     = fn($key) => $calcYoy(
                    $qLatest  ? $toCr($qLatest->$key??null,  (float)($qLatest->UL_FIN_Unit??1))  : null,
                    $qPrev    ? $toCr($qPrev->$key??null,    (float)($qPrev->UL_FIN_Unit??1))    : null
                );
                $qYoyF    = fn($key) => $calcYoy(
                    $qLatest  ? $toCr($qLatest->$key??null,  (float)($qLatest->UL_FIN_Unit??1))  : null,
                    $qSameQly ? $toCr($qSameQly->$key??null, (float)($qSameQly->UL_FIN_Unit??1)) : null
                );
                @endphp

                @if($qFin->isNotEmpty())
                <script>
                (function(){
                    var row=document.getElementById('finPeriodRow');
                    if(row){var b=document.createElement('button');b.className='fin-period-btn';b.dataset.period='q';b.textContent='Quarterly';row.appendChild(b);}
                })();
                </script>
                @endif

                {{-- ── MAIN TABS: Financial Data | Charts ── --}}
                <div class="fin-main-tabs">
                    <button class="fin-main-tab active" data-main="data">Financial Data</button>
                    <button class="fin-main-tab" data-main="charts">Charts</button>
                </div>

                {{-- ════════ FINANCIAL DATA PANE ════════ --}}
                <div class="fin-main-pane" id="fm-data">
                    <div class="fin-type-tabs">
                        <button class="fin-tab active" data-tab="pl">Profit &amp; Loss</button>
                        <button class="fin-tab" data-tab="bs">Balance Sheet</button>
                        <button class="fin-tab" data-tab="cf">Cash Flow</button>
                        <button class="fin-tab" data-tab="ratio">Ratio</button>
                    </div>
                    <p class="fin-note-top"><i class="fas fa-info-circle"></i> All figures are in Crores (&#8377;)</p>

                    {{-- Yearly P&L --}}
                    <div class="fin-pane" id="fp-y-pl">
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr>
                                <th class="fin-th-label"></th>
                                @foreach($yFin as $f)<th>{{ $yearLabel($f->UL_FIN_Period_end) }}</th>@endforeach
                                <th class="fin-th-chg">YOY %</th><th class="fin-th-chg">CAGR %</th>
                            </tr></thead>
                            <tbody>
                            @foreach([['Net Sales','UL_FIN_NET_SALES'],['Other Income','UL_FIN_OTHER_INCOME'],['Total Income','UL_FIN_TOTAL_INCOME'],['Operating Profit','UL_FIN_OPERATING_PROFIT'],['Interest','UL_FIN_INTEREST'],['Depreciation','UL_FIN_DEPRECIATION'],['Total Expenditure','UL_FIN_TOTAL_EXPENDITURE'],['Exceptional Income','UL_FIN_EXCEPTIONAL_INCOME'],['PBT','UL_FIN_PBT'],['TAX','UL_FIN_TAX'],['PAT','UL_FIN_PAT']] as [$lbl,$key])
                            <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($yFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($yoyF($key)) !!}</td><td>{!! $fmtChg($cagrF($key)) !!}</td></tr>
                            @endforeach
                            @php $eL=(float)($yLatest?->UL_FIN_ADJUSTED_EPS??0);$eP=(float)($yPrev?->UL_FIN_ADJUSTED_EPS??0);$eO=(float)($yOldest?->UL_FIN_ADJUSTED_EPS??0); @endphp
                            <tr class="fin-tr-eps"><td class="fin-td-label">EPS (&#8377;)</td>@foreach($yFin as $f)<td>{!! $fmtEps($f->UL_FIN_ADJUSTED_EPS??null) !!}</td>@endforeach<td>{!! $fmtChg($calcYoy($eL,$eP)) !!}</td><td>{!! $fmtChg($calcCagr($eL,$eO,$yPer)) !!}</td></tr>
                            </tbody>
                        </table></div>
                    </div>

                    {{-- Yearly Balance Sheet --}}
                    <div class="fin-pane fin-hidden" id="fp-y-bs">
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr>
                                <th class="fin-th-label"></th>
                                @foreach($yFin as $f)<th>{{ $yearLabel($f->UL_FIN_Period_end) }}</th>@endforeach
                                <th class="fin-th-chg">YOY %</th><th class="fin-th-chg">CAGR %</th>
                            </tr></thead>
                            <tbody>
                            @foreach([['Shareholder Funds','UL_FIN_SHAREHOLDER_FUNDS'],['Total Debt','UL_FIN_TOTAL_DEBT'],['Current Liabilities','UL_FIN_CURRENT_LIABILITIES'],['Non Current Liabilities','UL_FIN_NON_CURRENT_LIABILITIES'],['Total Liabilities','UL_FIN_TOTAL_LIABILITIES'],['Current Assets','UL_FIN_CURRENT_ASSETS'],['Non Current Assets','UL_FIN_NON_CURRENT_ASSETS'],['Total Assets','UL_FIN_TOTAL_ASSETS']] as [$lbl,$key])
                            <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($yFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($yoyF($key)) !!}</td><td>{!! $fmtChg($cagrF($key)) !!}</td></tr>
                            @endforeach
                            </tbody>
                        </table></div>
                    </div>

                    {{-- Yearly Cash Flow --}}
                    <div class="fin-pane fin-hidden" id="fp-y-cf">
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr>
                                <th class="fin-th-label"></th>
                                @foreach($yFin as $f)<th>{{ $yearLabel($f->UL_FIN_Period_end) }}</th>@endforeach
                                <th class="fin-th-chg">YOY %</th><th class="fin-th-chg">CAGR %</th>
                            </tr></thead>
                            <tbody>
                            @foreach([['Cash Flow From Operating Activities','UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES'],['Cash Flow From Investing Activities','UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES'],['Cash Flow From Financing Activities','UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES'],['Free Cash Flow','UL_FIN_FREE_CASH_FLOW']] as [$lbl,$key])
                            <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($yFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($yoyF($key)) !!}</td><td>{!! $fmtChg($cagrF($key)) !!}</td></tr>
                            @endforeach
                            </tbody>
                        </table></div>
                    </div>

                    {{-- Yearly Ratio --}}
                    <div class="fin-pane fin-hidden" id="fp-y-ratio">
                        @php
                        $yRatio=$yFin->map(function($f){$pat=(float)($f->UL_FIN_PAT??0);$sf=(float)($f->UL_FIN_SHAREHOLDER_FUNDS??0);$td=(float)($f->UL_FIN_TOTAL_DEBT??0);$ta=(float)($f->UL_FIN_TOTAL_ASSETS??0);$op=(float)($f->UL_FIN_OPERATING_PROFIT??0);$ca=(float)($f->UL_FIN_CURRENT_ASSETS??0);$cl=(float)($f->UL_FIN_CURRENT_LIABILITIES??0);return['p'=>$f->UL_FIN_Period_end,'roe'=>$sf>0?round($pat/$sf*100,1):null,'roce'=>$ta>0?round($op/$ta*100,1):null,'roa'=>$ta>0?round($pat/$ta*100,1):null,'cr'=>$cl>0?round($ca/$cl,1):null,'de'=>$sf>0?round($td/$sf,2):null];});
                        $yrL=$yRatio->last();$yrP=$yRatio->count()>=2?$yRatio[$yRatio->count()-2]:null;
                        @endphp
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr><th class="fin-th-label"></th>@foreach($yRatio as $r)<th>{{ $yearLabel($r['p']) }}</th>@endforeach<th class="fin-th-chg">YOY %</th></tr></thead>
                            <tbody>
                            @foreach([['ROE(%)','roe'],['ROCE(%)','roce'],['ROA(%)','roa'],['Current Ratio(x)','cr'],['Debt / Equity(x)','de']] as [$rl,$rk])
                            <tr><td class="fin-td-label">{{ $rl }}</td>@foreach($yRatio as $r)<td>{{ $r[$rk]!==null?number_format($r[$rk],2):'—' }}</td>@endforeach@php $chg=($yrL[$rk]!==null&&$yrP&&$yrP[$rk]!==null)?round($yrL[$rk]-$yrP[$rk],2):null; @endphp<td>{!! $fmtChg($chg) !!}</td></tr>
                            @endforeach
                            </tbody>
                        </table></div>
                    </div>

                    @if($qFin->isNotEmpty())
                    {{-- Quarterly P&L --}}
                    <div class="fin-pane fin-hidden" id="fp-q-pl">
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr><th class="fin-th-label"></th>@foreach($qFin as $f)<th>{{ $periodLabel($f->UL_FIN_Period_end) }}</th>@endforeach<th class="fin-th-chg">QoQ %</th><th class="fin-th-chg">YOY %</th></tr></thead>
                            <tbody>
                            @foreach([['Net Sales','UL_FIN_NET_SALES'],['Other Income','UL_FIN_OTHER_INCOME'],['Total Income','UL_FIN_TOTAL_INCOME'],['Operating Profit','UL_FIN_OPERATING_PROFIT'],['Interest','UL_FIN_INTEREST'],['Depreciation','UL_FIN_DEPRECIATION'],['PBT','UL_FIN_PBT'],['TAX','UL_FIN_TAX'],['PAT','UL_FIN_PAT']] as [$lbl,$key])
                            <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($qFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($qoyF($key)) !!}</td><td>{!! $fmtChg($qYoyF($key)) !!}</td></tr>
                            @endforeach
                            @php $qeL=(float)($qLatest?->UL_FIN_ADJUSTED_EPS??0);$qeP=(float)($qPrev?->UL_FIN_ADJUSTED_EPS??0);$qeSq=(float)($qSameQly?->UL_FIN_ADJUSTED_EPS??0); @endphp
                            <tr class="fin-tr-eps"><td class="fin-td-label">EPS (&#8377;)</td>@foreach($qFin as $f)<td>{!! $fmtEps($f->UL_FIN_ADJUSTED_EPS??null) !!}</td>@endforeach<td>{!! $fmtChg($calcYoy($qeL,$qeP)) !!}</td><td>{!! $fmtChg($calcYoy($qeL,$qeSq)) !!}</td></tr>
                            </tbody>
                        </table></div>
                    </div>

                    {{-- Quarterly Balance Sheet --}}
                    <div class="fin-pane fin-hidden" id="fp-q-bs">
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr><th class="fin-th-label"></th>@foreach($qFin as $f)<th>{{ $periodLabel($f->UL_FIN_Period_end) }}</th>@endforeach<th class="fin-th-chg">QoQ %</th><th class="fin-th-chg">YOY %</th></tr></thead>
                            <tbody>
                            @foreach([['Shareholder Funds','UL_FIN_SHAREHOLDER_FUNDS'],['Total Debt','UL_FIN_TOTAL_DEBT'],['Current Liabilities','UL_FIN_CURRENT_LIABILITIES'],['Non Current Liabilities','UL_FIN_NON_CURRENT_LIABILITIES'],['Total Liabilities','UL_FIN_TOTAL_LIABILITIES'],['Current Assets','UL_FIN_CURRENT_ASSETS'],['Non Current Assets','UL_FIN_NON_CURRENT_ASSETS'],['Total Assets','UL_FIN_TOTAL_ASSETS']] as [$lbl,$key])
                            <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($qFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($qoyF($key)) !!}</td><td>{!! $fmtChg($qYoyF($key)) !!}</td></tr>
                            @endforeach
                            </tbody>
                        </table></div>
                    </div>

                    {{-- Quarterly Cash Flow --}}
                    <div class="fin-pane fin-hidden" id="fp-q-cf">
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr><th class="fin-th-label"></th>@foreach($qFin as $f)<th>{{ $periodLabel($f->UL_FIN_Period_end) }}</th>@endforeach<th class="fin-th-chg">QoQ %</th><th class="fin-th-chg">YOY %</th></tr></thead>
                            <tbody>
                            @foreach([['Cash Flow From Operating Activities','UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES'],['Cash Flow From Investing Activities','UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES'],['Cash Flow From Financing Activities','UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES'],['Free Cash Flow','UL_FIN_FREE_CASH_FLOW']] as [$lbl,$key])
                            <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($qFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($qoyF($key)) !!}</td><td>{!! $fmtChg($qYoyF($key)) !!}</td></tr>
                            @endforeach
                            </tbody>
                        </table></div>
                    </div>

                    {{-- Quarterly Ratio --}}
                    <div class="fin-pane fin-hidden" id="fp-q-ratio">
                        @php
                        $qRatio=$qFin->map(function($f){$pat=(float)($f->UL_FIN_PAT??0);$sf=(float)($f->UL_FIN_SHAREHOLDER_FUNDS??0);$td=(float)($f->UL_FIN_TOTAL_DEBT??0);$ta=(float)($f->UL_FIN_TOTAL_ASSETS??0);$op=(float)($f->UL_FIN_OPERATING_PROFIT??0);$ca=(float)($f->UL_FIN_CURRENT_ASSETS??0);$cl=(float)($f->UL_FIN_CURRENT_LIABILITIES??0);return['p'=>$f->UL_FIN_Period_end,'roe'=>$sf>0?round($pat/$sf*100,1):null,'roce'=>$ta>0?round($op/$ta*100,1):null,'roa'=>$ta>0?round($pat/$ta*100,1):null,'cr'=>$cl>0?round($ca/$cl,1):null,'de'=>$sf>0?round($td/$sf,2):null];});
                        $qrL=$qRatio->last();$qrP=$qRatio->count()>=2?$qRatio[$qRatio->count()-2]:null;
                        @endphp
                        @php
                        $qSameQlyR = $qCount >= 5 ? $qRatio->get($qCount-5) : null;
                        @endphp
                        <div class="fin-table-wrap"><table class="fin-table">
                            <thead><tr><th class="fin-th-label"></th>@foreach($qRatio as $r)<th>{{ $periodLabel($r['p']) }}</th>@endforeach<th class="fin-th-chg">QoQ %</th><th class="fin-th-chg">YOY %</th></tr></thead>
                            <tbody>
                            @foreach([['ROE(%)','roe'],['ROCE(%)','roce'],['ROA(%)','roa'],['Current Ratio(x)','cr'],['Debt / Equity(x)','de']] as [$rl,$rk])
                            @php
                            $qoq=($qrL[$rk]!==null&&$qrP&&$qrP[$rk]!==null)?round($qrL[$rk]-$qrP[$rk],2):null;
                            $yoy=($qrL[$rk]!==null&&$qSameQlyR&&$qSameQlyR[$rk]!==null)?round($qrL[$rk]-$qSameQlyR[$rk],2):null;
                            @endphp
                            <tr><td class="fin-td-label">{{ $rl }}</td>@foreach($qRatio as $r)<td>{{ $r[$rk]!==null?number_format($r[$rk],2):'—' }}</td>@endforeach<td>{!! $fmtChg($qoq) !!}</td><td>{!! $fmtChg($yoy) !!}</td></tr>
                            @endforeach
                            </tbody>
                        </table></div>
                    </div>
                    @endif

                </div>{{-- /fm-data --}}

                {{-- ════════ CHARTS PANE ════════ --}}
                <div class="fin-main-pane fin-hidden" id="fm-charts">

                    {{-- Chart sub-tabs --}}
                    <div class="fin-type-tabs fin-chart-type-tabs">
                        <button class="fin-tab fin-ctab active" data-ctab="pl">Profit &amp; Loss</button>
                        <button class="fin-tab fin-ctab" data-ctab="bs">Balance Sheet</button>
                        <button class="fin-tab fin-ctab" data-ctab="cf">Cash Flow</button>
                    </div>

                    {{-- Yearly charts --}}
                    <div class="fin-cp" id="fcp-y">
                        <div class="fin-csec" data-ctab="pl">
                            <div class="fin-charts-grid">
                                <div class="fin-chart-box"><p class="fin-chart-title">Net Sales <span>(in Cr.)</span></p><canvas id="ch-y-sales"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Total Income <span>(in Cr.)</span></p><canvas id="ch-y-income"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Net Profit <span>(in Cr.)</span></p><canvas id="ch-y-pat"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Operating Profit <span>(in Cr.)</span></p><canvas id="ch-y-op"></canvas></div>
                            </div>
                        </div>
                        <div class="fin-csec fin-hidden" data-ctab="bs">
                            <div class="fin-charts-grid fin-charts-2">
                                <div class="fin-chart-box"><p class="fin-chart-title">Shareholder Funds <span>(in Cr.)</span></p><canvas id="ch-y-sf"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Total Assets <span>(in Cr.)</span></p><canvas id="ch-y-ta"></canvas></div>
                            </div>
                        </div>
                        <div class="fin-csec fin-hidden" data-ctab="cf">
                            <div class="fin-charts-grid">
                                <div class="fin-chart-box"><p class="fin-chart-title">Operating Cash Flow <span>(in Cr.)</span></p><canvas id="ch-y-cfo"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Investing Cash Flow <span>(in Cr.)</span></p><canvas id="ch-y-cfi"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Financing Cash Flow <span>(in Cr.)</span></p><canvas id="ch-y-cff"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Free Cash Flow <span>(in Cr.)</span></p><canvas id="ch-y-fcf"></canvas></div>
                            </div>
                        </div>
                    </div>

                    @if($qFin->isNotEmpty())
                    {{-- Quarterly charts --}}
                    <div class="fin-cp fin-hidden" id="fcp-q">
                        <div class="fin-csec" data-ctab="pl">
                            <div class="fin-charts-grid">
                                <div class="fin-chart-box"><p class="fin-chart-title">Net Sales <span>(in Cr.)</span></p><canvas id="ch-q-sales"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Total Income <span>(in Cr.)</span></p><canvas id="ch-q-income"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Net Profit <span>(in Cr.)</span></p><canvas id="ch-q-pat"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Operating Profit <span>(in Cr.)</span></p><canvas id="ch-q-op"></canvas></div>
                            </div>
                        </div>
                        <div class="fin-csec fin-hidden" data-ctab="bs">
                            <div class="fin-charts-grid fin-charts-2">
                                <div class="fin-chart-box"><p class="fin-chart-title">Shareholder Funds <span>(in Cr.)</span></p><canvas id="ch-q-sf"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Total Assets <span>(in Cr.)</span></p><canvas id="ch-q-ta"></canvas></div>
                            </div>
                        </div>
                        <div class="fin-csec fin-hidden" data-ctab="cf">
                            <div class="fin-charts-grid">
                                <div class="fin-chart-box"><p class="fin-chart-title">Operating Cash Flow <span>(in Cr.)</span></p><canvas id="ch-q-cfo"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Investing Cash Flow <span>(in Cr.)</span></p><canvas id="ch-q-cfi"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Financing Cash Flow <span>(in Cr.)</span></p><canvas id="ch-q-cff"></canvas></div>
                                <div class="fin-chart-box"><p class="fin-chart-title">Free Cash Flow <span>(in Cr.)</span></p><canvas id="ch-q-fcf"></canvas></div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>{{-- /fm-charts --}}

                {{-- Chart data for JS --}}
                @php
                $mkSeries = function($col, $fin) use ($toCr) {
                    return $fin->map(function($f) use ($col, $toCr) {
                        return $toCr($f->$col ?? null, $f->UL_FIN_Unit ?? 1);
                    })->values()->toArray();
                };
                $chartData = [
                    'y' => [
                        'labels' => $yFin->map(function($f) use ($yearLabel) { return $yearLabel($f->UL_FIN_Period_end); })->values()->toArray(),
                        'sales'  => $mkSeries('UL_FIN_NET_SALES', $yFin),
                        'income' => $mkSeries('UL_FIN_TOTAL_INCOME', $yFin),
                        'pat'    => $mkSeries('UL_FIN_PAT', $yFin),
                        'op'     => $mkSeries('UL_FIN_OPERATING_PROFIT', $yFin),
                        'sf'     => $mkSeries('UL_FIN_SHAREHOLDER_FUNDS', $yFin),
                        'ta'     => $mkSeries('UL_FIN_TOTAL_ASSETS', $yFin),
                        'cfo'    => $mkSeries('UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES', $yFin),
                        'cfi'    => $mkSeries('UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES', $yFin),
                        'cff'    => $mkSeries('UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES', $yFin),
                        'fcf'    => $mkSeries('UL_FIN_FREE_CASH_FLOW', $yFin),
                    ],
                    'q' => $qFin->isNotEmpty() ? [
                        'labels' => $qFin->map(function($f) use ($periodLabel) { return $periodLabel($f->UL_FIN_Period_end); })->values()->toArray(),
                        'sales'  => $mkSeries('UL_FIN_NET_SALES', $qFin),
                        'income' => $mkSeries('UL_FIN_TOTAL_INCOME', $qFin),
                        'pat'    => $mkSeries('UL_FIN_PAT', $qFin),
                        'op'     => $mkSeries('UL_FIN_OPERATING_PROFIT', $qFin),
                        'sf'     => $mkSeries('UL_FIN_SHAREHOLDER_FUNDS', $qFin),
                        'ta'     => $mkSeries('UL_FIN_TOTAL_ASSETS', $qFin),
                        'cfo'    => $mkSeries('UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES', $qFin),
                        'cfi'    => $mkSeries('UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES', $qFin),
                        'cff'    => $mkSeries('UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES', $qFin),
                        'fcf'    => $mkSeries('UL_FIN_FREE_CASH_FLOW', $qFin),
                    ] : null,
                ];
                @endphp
                <script>
                window._finData = {!! json_encode($chartData) !!};
                </script>

            </section>
            @endif

            {{-- Investment Thesis --}}
            @if($thesisHtml)
            <section class="cp-section" id="thesis">
                <div class="cp-section-head">
                    <h2>Investment <span>Thesis</span></h2>
                    <span class="cp-section-badge"><i class="fas fa-lightbulb"></i> Research</span>
                </div>
                <div class="cp-thesis-body">
                    {!! $thesisHtml !!}
                </div>
            </section>
            @endif

            {{-- Company Details --}}
            <section class="cp-section" id="details">
                <div class="cp-section-head">
                    <h2>Company <span>Information</span></h2>
                </div>
                <div class="cp-details-grid">
                    @if($stock->UL_STOCKS_ISIN)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-fingerprint"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">ISIN</span>
                            <span class="cp-detail-value">{{ $stock->UL_STOCKS_ISIN }}</span>
                        </div>
                    </div>
                    @endif
                    @if($stock->UL_STOCKS_INDUSTRY)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-industry"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">Industry</span>
                            <span class="cp-detail-value">{{ $stock->UL_STOCKS_INDUSTRY }}</span>
                        </div>
                    </div>
                    @endif
                    @if($stock->UL_STOCKS_CATEGORY)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-tag"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">Category</span>
                            <span class="cp-detail-value">{{ $stock->UL_STOCKS_CATEGORY }}</span>
                        </div>
                    </div>
                    @endif
                    @if($stock->UL_STOCKS_INC_YEAR)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">Incorporated</span>
                            <span class="cp-detail-value">
                                {{ $stock->UL_STOCKS_INC_MONTH ? date('F', mktime(0,0,0,(int)$stock->UL_STOCKS_INC_MONTH)) . ' ' : '' }}{{ $stock->UL_STOCKS_INC_YEAR }}
                            </span>
                        </div>
                    </div>
                    @endif
                    @if($stock->UL_STOCKS_LOT_SIZE)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-layer-group"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">Min Lot Size</span>
                            <span class="cp-detail-value">{{ number_format($stock->UL_STOCKS_LOT_SIZE) }} shares</span>
                        </div>
                    </div>
                    @endif
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-university"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">Demat Required</span>
                            <span class="cp-detail-value">{{ $stock->UL_STOCKS_DEMAT_ACCOUNT_REQ ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    @if($stock->UL_STOCKS_ROFR_FLAG !== null)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">ROFR Applicable</span>
                            <span class="cp-detail-value">{{ $stock->UL_STOCKS_ROFR_FLAG ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    @endif
                    @if($stock->UL_STOCKS_WEBSITE)
                    <div class="cp-detail-card">
                        <div class="cp-detail-icon"><i class="fas fa-globe"></i></div>
                        <div class="cp-detail-body">
                            <span class="cp-detail-label">Website</span>
                            <a href="{{ $stock->UL_STOCKS_WEBSITE }}" target="_blank" rel="noopener" class="cp-detail-link">
                                {{ $stock->UL_STOCKS_WEBSITE }} <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </section>

        </div>{{-- /cp-container --}}
    </div>{{-- /cp-body --}}

</div>{{-- /ug-company-page --}}

{{-- ── FAQ ── --}}
<section class="ug-faq-section">
    <h2 class="faq-title">Frequently Asked <span>Questions</span></h2>
    <div class="faq-container" id="faqContainer">
        <div class="faq-item active">
            <div class="faq-question">
                How to buy {{ $stock->UL_STOCKS_COMPNAME }} unlisted shares?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>You can buy {{ $stock->UL_STOCKS_COMPNAME }} unlisted shares through UnlistedGain by clicking the "Buy Shares" button on this page. Our team will guide you through KYC, payment, and direct share transfer to your demat account.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                What is the minimum lot size for {{ $stock->UL_STOCKS_COMPNAME }}?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>The minimum lot size for {{ $stock->UL_STOCKS_COMPNAME }} is {{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }} shares. Transactions are processed in multiples of this quantity.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                Is it safe to invest in {{ $stock->UL_STOCKS_COMPNAME }}?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>Investing in unlisted shares carries risks including illiquidity and limited public disclosures. Through UnlistedGain, shares are transferred directly to your demat account ensuring ownership security. Always consult a SEBI-registered advisor before investing.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                What documents are needed to buy {{ $stock->UL_STOCKS_COMPNAME }} shares?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>You will need a valid Demat account, PAN card, and KYC documents. Our team will guide you through the complete verification process after you submit your inquiry.</p>
            </div>
        </div>
        <div class="faq-extra-items" style="display:none">
            <div class="faq-item">
                <div class="faq-question">
                    How is the unlisted share price of {{ $stock->UL_STOCKS_COMPNAME }} determined?
                    <span class="faq-icon"></span>
                </div>
                <div class="faq-answer">
                    <p>Unlisted share prices are driven by supply-demand dynamics, comparable listed peer valuations, company financials, and recent off-market transaction data. UnlistedGain updates prices regularly based on market activity.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="faq-footer">
        <button id="viewAllFaq" class="view-all-btn">View All</button>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    // ── Sticky section nav ──
    var nav = document.getElementById('cpSectionNav');
    if (nav) {
        var links = nav.querySelectorAll('.cp-snav-link');
        var sections = [];
        links.forEach(function (l) {
            var id = l.getAttribute('href').replace('#', '');
            var el = document.getElementById(id);
            if (el) sections.push({ el: el, link: l });
        });
        function onScroll() {
            var scrollY = window.scrollY + 120;
            var navTop  = nav.getBoundingClientRect().top + window.scrollY;
            nav.classList.toggle('cp-snav--sticky', window.scrollY > navTop - 56);
            var current = sections[0];
            sections.forEach(function (s) { if (scrollY >= s.el.offsetTop) current = s; });
            links.forEach(function (l) { l.classList.remove('active'); });
            if (current) current.link.classList.add('active');
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        links.forEach(function (l) {
            l.addEventListener('click', function (e) {
                e.preventDefault();
                var el = document.getElementById(l.getAttribute('href').replace('#', ''));
                if (el) window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 70, behavior: 'smooth' });
            });
        });
    }

    // ── Financial tabs ──
    var activePeriod = 'y', activeTab = 'pl', activeMain = 'data', activeChartTab = 'pl';
    var _charts = {};
    var GREEN = '#87b942', RED = '#e05c5c';

    // Main tab (Financial Data / Charts)
    document.querySelectorAll('.fin-main-tab').forEach(function(btn){
        btn.addEventListener('click', function(){
            activeMain = this.dataset.main;
            document.querySelectorAll('.fin-main-tab').forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.fin-main-pane').forEach(function(p){ p.classList.add('fin-hidden'); });
            var pane = document.getElementById('fm-' + activeMain);
            if (pane) pane.classList.remove('fin-hidden');
            if (activeMain === 'charts') { showChartsPeriod(); renderChartTab(); }
        });
    });

    // Period toggle
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.fin-period-btn');
        if (!btn) return;
        activePeriod = btn.dataset.period;
        document.querySelectorAll('.fin-period-btn').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
        if (activeMain === 'data') showPane();
        else { showChartsPeriod(); renderChartTab(); }
    });

    // Data sub-tab (P&L / BS / CF / Ratio) — only on non-chart tabs
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.fin-tab');
        if (!btn) return;
        if (btn.classList.contains('fin-ctab')) {
            // Chart sub-tab
            activeChartTab = btn.dataset.ctab;
            document.querySelectorAll('.fin-ctab').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            showChartSection();
            renderChartTab();
        } else {
            // Data sub-tab
            activeTab = btn.dataset.tab;
            document.querySelectorAll('.fin-tab:not(.fin-ctab)').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            showPane();
        }
    });

    function showPane() {
        document.querySelectorAll('.fin-pane').forEach(function(p){ p.classList.add('fin-hidden'); });
        var el = document.getElementById('fp-' + activePeriod + '-' + activeTab);
        if (el) el.classList.remove('fin-hidden');
    }

    function showChartsPeriod() {
        document.querySelectorAll('.fin-cp').forEach(function(p){ p.classList.add('fin-hidden'); });
        var el = document.getElementById('fcp-' + activePeriod);
        if (el) el.classList.remove('fin-hidden');
        showChartSection();
    }

    function showChartSection() {
        var cp = document.getElementById('fcp-' + activePeriod);
        if (!cp) return;
        cp.querySelectorAll('.fin-csec').forEach(function(s){ s.classList.add('fin-hidden'); });
        var sec = cp.querySelector('.fin-csec[data-ctab="' + activeChartTab + '"]');
        if (sec) sec.classList.remove('fin-hidden');
    }

    function makeBar(id, labels, data) {
        var canvas = document.getElementById(id);
        if (!canvas) return;
        if (_charts[id]) { _charts[id].destroy(); delete _charts[id]; }
        var colors = (data||[]).map(function(v){ return v !== null && v >= 0 ? GREEN : RED; });
        _charts[id] = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: { labels: labels||[], datasets: [{ data: data||[], backgroundColor: colors, borderRadius: 5, borderSkipped: false }] },
            options: {
                responsive: true, maintainAspectRatio: true, aspectRatio: 1.7,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(ctx){ return ' ₹' + (ctx.raw!==null?ctx.raw.toFixed(1):'—') + ' Cr'; } } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#666' } },
                    y: { grid: { color: '#f2f4f7' }, ticks: { font: { size: 11 }, color: '#666',
                        callback: function(v){ return v>=1000?(v/1000).toFixed(0)+'K':v; }
                    }}
                }
            },
            plugins: [{
                id: 'barLabels',
                afterDatasetsDraw: function(chart) {
                    var ctx2 = chart.ctx;
                    chart.data.datasets.forEach(function(ds, i) {
                        chart.getDatasetMeta(i).data.forEach(function(bar, idx) {
                            var val = ds.data[idx];
                            if (val === null || val === undefined) return;
                            ctx2.save();
                            ctx2.fillStyle = '#444';
                            ctx2.font = 'italic 10px sans-serif';
                            ctx2.textAlign = 'center';
                            ctx2.fillText(val>=1000?(val/1000).toFixed(1)+'K':val.toFixed(1), bar.x, bar.y-5);
                            ctx2.restore();
                        });
                    });
                }
            }]
        });
    }

    function renderChartTab() {
        var d = window._finData;
        if (!d) return;
        var src = activePeriod === 'y' ? d.y : (d.q || null);
        if (!src) return;
        var p = activePeriod, t = activeChartTab;
        if (t === 'pl') {
            makeBar('ch-'+p+'-sales',  src.labels, src.sales);
            makeBar('ch-'+p+'-income', src.labels, src.income);
            makeBar('ch-'+p+'-pat',    src.labels, src.pat);
            makeBar('ch-'+p+'-op',     src.labels, src.op);
        } else if (t === 'bs') {
            makeBar('ch-'+p+'-sf', src.labels, src.sf);
            makeBar('ch-'+p+'-ta', src.labels, src.ta);
        } else if (t === 'cf') {
            makeBar('ch-'+p+'-cfo', src.labels, src.cfo);
            makeBar('ch-'+p+'-cfi', src.labels, src.cfi);
            makeBar('ch-'+p+'-cff', src.labels, src.cff);
            makeBar('ch-'+p+'-fcf', src.labels, src.fcf);
        }
    }

    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showPane);
    } else {
        showPane();
    }
})();
</script>
@endpush
@endsection
