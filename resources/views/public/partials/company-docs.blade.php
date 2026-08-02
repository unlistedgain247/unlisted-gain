@php
    $bucketDefs = [
        'results'           => ['label' => 'Results', 'types' => ['Annual Report', 'Quarterly Report']],
        'corporate-actions' => ['label' => 'Corporate Actions', 'types' => ['Announcement']],
        'ipo-related'       => ['label' => 'IPO Related Info', 'types' => ['Announcement IPO', 'DRHP', 'RHP']],
        'research'          => ['label' => 'Research &amp; Presentations', 'types' => ['Research Report', 'Investor Presentation', 'Anchor Investor', 'Valuation Report']],
    ];

    $buckets = collect($bucketDefs)->map(function ($b, $key) use ($documents) {
        $docs = collect($b['types'])->flatMap(fn ($t) => $documents->get($t, collect()));
        return ['key' => $key, 'label' => $b['label'], 'docs' => $docs];
    })->filter(fn ($b) => $b['docs']->isNotEmpty())->values();
@endphp

<div class="doc-bucket-wrap">
@if($buckets->isEmpty())
    <div class="doc-empty">
        <i class="fa-regular fa-folder-open"></i>
        <p>No documents available yet.</p>
    </div>
@else
    <div class="doc-bucket-tabs">
        @foreach($buckets as $b)
        <button type="button" class="doc-bucket-tab {{ $loop->first ? 'active' : '' }}" data-bucket="{{ $b['key'] }}">{{ $b['label'] }}</button>
        @endforeach
    </div>

    <div class="doc-bucket-content">
        @foreach($buckets as $b)
        <div class="doc-bucket-pane {{ $loop->first ? 'active' : '' }}" data-bucket-pane="{{ $b['key'] }}">
            <div class="fin-table-wrap"><table class="fin-table doc-table">
                <thead><tr><th class="fin-th-label">Type</th><th>Period / Date</th><th>Document</th></tr></thead>
                <tbody>
                    @foreach($b['docs'] as $doc)
                    @php
                        $period       = trim(($doc->UL_DOC_PERIOD_MM ?? '') . '/' . ($doc->UL_DOC_PERIOD_YY ?? ''), '/');
                        $periodOrDate = $period ?: ($doc->UL_DOC_DATE ? \Carbon\Carbon::parse($doc->UL_DOC_DATE)->format('d M Y') : '—');
                        $url          = $doc->UL_DOC_FILE_PATH ? asset($doc->UL_DOC_FILE_PATH) : $doc->UL_DOC_FILELINK;
                    @endphp
                    <tr>
                        <td class="fin-td-label">{{ $doc->UL_DOC_TYPE }}</td>
                        <td>{{ $periodOrDate }}</td>
                        <td>
                            @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="doc-dl-btn"><i class="fa-solid fa-download"></i> Download</a>
                            @else
                            <span class="doc-dl-na">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div>
        </div>
        @endforeach
    </div>
@endif
</div>
