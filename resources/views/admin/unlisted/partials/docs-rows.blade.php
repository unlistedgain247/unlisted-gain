@php $offset = ($documents->currentPage() - 1) * $documents->perPage(); @endphp

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Company</th>
                <th>Type</th>
                <th>Period</th>
                <th>Date</th>
                <th>Description</th>
                <th>File</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            @php
                $period = trim(($doc->UL_DOC_PERIOD_MM ?? '') . ' ' . ($doc->UL_DOC_PERIOD_YY ?? ''));
                $docJson = json_encode($doc, JSON_HEX_APOS | JSON_HEX_TAG);
            @endphp
            <tr>
                <td>{{ $doc->UL_STOCKS_COMPNAME }}</td>
                <td>{{ $doc->UL_DOC_TYPE }}</td>
                <td>{{ $period ?: '—' }}</td>
                <td>{{ $doc->UL_DOC_DATE ? \Carbon\Carbon::parse($doc->UL_DOC_DATE)->format('d M Y') : '—' }}</td>
                <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $doc->UL_DOC_DESCRIPTION }}">
                    {{ $doc->UL_DOC_DESCRIPTION ?: '—' }}
                </td>
                <td>
                    @if($doc->UL_DOC_FILE_PATH)
                    <a href="{{ asset($doc->UL_DOC_FILE_PATH) }}" target="_blank" rel="noopener" title="Download file">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    @elseif($doc->UL_DOC_FILELINK)
                    <a href="{{ $doc->UL_DOC_FILELINK }}" target="_blank" rel="noopener" title="Open link">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    @else
                    <span style="color:#cbd5e1">—</span>
                    @endif
                </td>
                <td>
                    <span class="admin-badge {{ $doc->UL_DOC_STATUS === '1' ? 'badge-admin' : 'badge-locked' }}">
                        {{ $doc->UL_DOC_STATUS === '1' ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <i class="fa-solid fa-pen doc-edit-btn" data-doc='{{ $docJson }}'
                        style="cursor:pointer;color:#2196f3;font-size:12px;margin-right:10px" title="Edit"></i>
                    <label class="tgl-switch" title="Toggle status" style="vertical-align:middle;">
                        <input type="checkbox" class="doc-toggle" data-id="{{ $doc->UL_DOC_ID }}"
                            {{ $doc->UL_DOC_STATUS === '1' ? 'checked' : '' }}>
                        <span class="tgl-slider"></span>
                    </label>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#aaa;padding:32px">
                    <i class="fa-regular fa-folder-open" style="font-size:24px;display:block;margin-bottom:8px"></i>
                    No documents found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.paginator', [
    'total'       => $documents->total(),
    'perPage'     => $documents->perPage(),
    'currentPage' => $documents->currentPage(),
    'callback'    => 'loadDocsPage',
])
