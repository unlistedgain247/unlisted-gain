{{--
    Renders the P&L / Balance Sheet / Cash Flow / Ratio tables (yearly + quarterly)
    for ONE financials type (Consolidated or Standalone). Included once per type
    from company.blade.php, which appends $suffix to every element id so a
    Consolidated and a Standalone copy can coexist in the DOM without collisions.

    Expected variables: $financials, $quarterlyFin, $suffix,
    $toCr, $fmtCr, $fmtEps, $epsVal, $fmtChg, $yearLabel, $periodLabel, $calcYoy, $calcCagr
--}}
@php
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

$qFin     = $quarterlyFin->reverse()->values();
$qCount   = $qFin->count();
$qLatest  = $qFin->last();
$qPrev    = $qCount >= 2 ? $qFin[$qCount-2] : null;
$qSameQly = $qCount >= 5 ? $qFin[$qCount-5] : null;
$qoyF     = fn($key) => $calcYoy(
    $qLatest  ? $toCr($qLatest->$key??null,  (float)($qLatest->UL_FIN_Unit??1))  : null,
    $qPrev    ? $toCr($qPrev->$key??null,    (float)($qPrev->UL_FIN_Unit??1))    : null
);
$qYoyF    = fn($key) => $calcYoy(
    $qLatest  ? $toCr($qLatest->$key??null,  (float)($qLatest->UL_FIN_Unit??1))  : null,
    $qSameQly ? $toCr($qSameQly->$key??null, (float)($qSameQly->UL_FIN_Unit??1)) : null
);
@endphp

{{-- Yearly P&L --}}
<div class="fin-pane{{ $suffix === '' ? '' : ' fin-hidden' }}" id="fp-y-pl{{ $suffix }}">
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
        @php $eL=$yLatest?$epsVal($yLatest):null;$eP=$yPrev?$epsVal($yPrev):null;$eO=$yOldest?$epsVal($yOldest):null; @endphp
        <tr class="fin-tr-eps"><td class="fin-td-label">EPS (&#8377;)</td>@foreach($yFin as $f)<td>{!! $fmtEps($epsVal($f)) !!}</td>@endforeach<td>{!! $fmtChg($calcYoy($eL,$eP)) !!}</td><td>{!! $fmtChg($calcCagr($eL,$eO,$yPer)) !!}</td></tr>
        </tbody>
    </table></div>
</div>

{{-- Yearly Balance Sheet --}}
<div class="fin-pane fin-hidden" id="fp-y-bs{{ $suffix }}">
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
<div class="fin-pane fin-hidden" id="fp-y-cf{{ $suffix }}">
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
<div class="fin-pane fin-hidden" id="fp-y-ratio{{ $suffix }}">
    @php
    $yRatio=$yFin->map(function($f){$pat=(float)($f->UL_FIN_PAT??0);$sf=(float)($f->UL_FIN_SHAREHOLDER_FUNDS??0);$td=(float)($f->UL_FIN_TOTAL_DEBT??0);$ta=(float)($f->UL_FIN_TOTAL_ASSETS??0);$op=(float)($f->UL_FIN_OPERATING_PROFIT??0);$ca=(float)($f->UL_FIN_CURRENT_ASSETS??0);$cl=(float)($f->UL_FIN_CURRENT_LIABILITIES??0);return['p'=>$f->UL_FIN_Period_end,'roe'=>$sf!=0?round($pat/$sf*100,1):null,'roce'=>$ta>0?round($op/$ta*100,1):null,'roa'=>$ta>0?round($pat/$ta*100,1):null,'cr'=>$cl>0?round($ca/$cl,1):null,'de'=>$sf!=0?round($td/$sf,2):null];});
    $yrL=$yRatio->last();$yrP=$yRatio->count()>=2?$yRatio[$yRatio->count()-2]:null;
    @endphp
    <div class="fin-table-wrap"><table class="fin-table">
        <thead><tr><th class="fin-th-label"></th>@foreach($yRatio as $r)<th>{{ $yearLabel($r['p']) }}</th>@endforeach<th class="fin-th-chg">YOY %</th></tr></thead>
        <tbody>
        @foreach([['ROE(%)','roe'],['ROCE(%)','roce'],['ROA(%)','roa'],['Current Ratio(x)','cr'],['Debt / Equity(x)','de']] as [$rl,$rk])
        <tr><td class="fin-td-label">{{ $rl }}</td>@foreach($yRatio as $r)<td>{{ $r[$rk]!==null?number_format($r[$rk],2):'—' }}</td>@endforeach@php $chg=$calcYoy($yrL[$rk]??null,$yrP[$rk]??null); @endphp<td>{!! $fmtChg($chg) !!}</td></tr>
        @endforeach
        </tbody>
    </table></div>
</div>

@if($qFin->isNotEmpty())
{{-- Quarterly P&L --}}
<div class="fin-pane fin-hidden" id="fp-q-pl{{ $suffix }}">
    <div class="fin-table-wrap"><table class="fin-table">
        <thead><tr><th class="fin-th-label"></th>@foreach($qFin as $f)<th>{{ $periodLabel($f->UL_FIN_Period_end) }}</th>@endforeach<th class="fin-th-chg">QoQ %</th><th class="fin-th-chg">YOY %</th></tr></thead>
        <tbody>
        @foreach([['Net Sales','UL_FIN_NET_SALES'],['Other Income','UL_FIN_OTHER_INCOME'],['Total Income','UL_FIN_TOTAL_INCOME'],['Operating Profit','UL_FIN_OPERATING_PROFIT'],['Interest','UL_FIN_INTEREST'],['Depreciation','UL_FIN_DEPRECIATION'],['PBT','UL_FIN_PBT'],['TAX','UL_FIN_TAX'],['PAT','UL_FIN_PAT']] as [$lbl,$key])
        <tr><td class="fin-td-label">{{ $lbl }}</td>@foreach($qFin as $f)<td>{!! $fmtCr($toCr($f->$key??null,$f->UL_FIN_Unit??1)) !!}</td>@endforeach<td>{!! $fmtChg($qoyF($key)) !!}</td><td>{!! $fmtChg($qYoyF($key)) !!}</td></tr>
        @endforeach
        @php $qeL=$qLatest?$epsVal($qLatest):null;$qeP=$qPrev?$epsVal($qPrev):null;$qeSq=$qSameQly?$epsVal($qSameQly):null; @endphp
        <tr class="fin-tr-eps"><td class="fin-td-label">EPS (&#8377;)</td>@foreach($qFin as $f)<td>{!! $fmtEps($epsVal($f)) !!}</td>@endforeach<td>{!! $fmtChg($calcYoy($qeL,$qeP)) !!}</td><td>{!! $fmtChg($calcYoy($qeL,$qeSq)) !!}</td></tr>
        </tbody>
    </table></div>
</div>

{{-- Quarterly Balance Sheet --}}
<div class="fin-pane fin-hidden" id="fp-q-bs{{ $suffix }}">
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
<div class="fin-pane fin-hidden" id="fp-q-cf{{ $suffix }}">
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
<div class="fin-pane fin-hidden" id="fp-q-ratio{{ $suffix }}">
    @php
    $qRatio=$qFin->map(function($f){$pat=(float)($f->UL_FIN_PAT??0);$sf=(float)($f->UL_FIN_SHAREHOLDER_FUNDS??0);$td=(float)($f->UL_FIN_TOTAL_DEBT??0);$ta=(float)($f->UL_FIN_TOTAL_ASSETS??0);$op=(float)($f->UL_FIN_OPERATING_PROFIT??0);$ca=(float)($f->UL_FIN_CURRENT_ASSETS??0);$cl=(float)($f->UL_FIN_CURRENT_LIABILITIES??0);return['p'=>$f->UL_FIN_Period_end,'roe'=>$sf!=0?round($pat/$sf*100,1):null,'roce'=>$ta>0?round($op/$ta*100,1):null,'roa'=>$ta>0?round($pat/$ta*100,1):null,'cr'=>$cl>0?round($ca/$cl,1):null,'de'=>$sf!=0?round($td/$sf,2):null];});
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
