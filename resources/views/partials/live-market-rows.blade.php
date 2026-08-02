@forelse($rows as $row)
@php
    $tone = $row['pct'] > 0 ? 'lmw-pos' : ($row['pct'] < 0 ? 'lmw-neg' : 'lmw-neutral');
    $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $row['name']);
    $initials = collect(preg_split('/\s+/', trim($cleanName)))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp
<tr>
    <td class="lmw-name">
        <a href="{{ url('/companies/' . $row['slug'] . '/') }}">
            <span class="lmw-avatar">{{ strtoupper($initials) }}</span>
            <span class="lmw-name-text">{{ $row['name'] }}</span>
        </a>
    </td>
    <td class="lmw-price">{{ number_format($row['price'], 1) }}</td>
    <td>
        <span class="lmw-badge {{ $tone }}">
            {{ $row['chg'] > 0 ? '+' : '' }}{{ number_format($row['chg'], 1) }}
        </span>
    </td>
    <td>
        <span class="lmw-badge {{ $tone }}">
            {{ $row['pct'] > 0 ? '+' : '' }}{{ number_format($row['pct'], 1) }}%
        </span>
    </td>
</tr>
@empty
<tr><td colspan="4" class="lmw-empty">No data available for this range.</td></tr>
@endforelse
