<div class="fl-overlay" onclick="if(event.target===this)closeFinancialsListModal()">
    <div class="fl-modal">

        <div class="fl-header">
            <h3>Financials List &mdash; {{ $stock->UL_STOCKS_COMPNAME }}</h3>
            <button class="fl-close" onclick="closeFinancialsListModal()" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="fl-info">
            {{ $financials->total() }} results found: Showing page {{ $financials->currentPage() }} of {{ $financials->lastPage() }}
        </div>

        <div class="fl-table-wrap">
            <table class="fl-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Period End</th>
                        <th>Type</th>
                        <th>No. Months</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($financials as $row)
                    @php
                    $pe = (int) $row->UL_FIN_Period_end;
                    $peLabel = intdiv($pe, 100) . '-' . str_pad($pe % 100, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <tr @class(['fl-row-alt'=> $loop->even])>
                        <td>{{ $stock->UL_STOCKS_COMPNAME }}</td>
                        <td>{{ $peLabel }}</td>
                        <td>{{ $row->UL_FIN_Type === 'C' ? 'Consolidated' : 'Standalone' }}</td>
                        <td>{{ $row->UL_FIN_No_months }}</td>
                        <td>{{ $row->UL_FIN_Unit ?? '—' }}</td>
                        <td>
                            <span class="fl-status {{ $row->UL_FIN_STATUS == '1' ? 'fl-badge-active' : 'fl-badge-inactive' }}">
                                {{ $row->UL_FIN_STATUS == '1' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <i class="fa-solid fa-pen fl-edit-btn"
                                data-period-end="{{ $row->UL_FIN_Period_end }}"
                                data-type="{{ $row->UL_FIN_Type }}"
                                data-no-months="{{ $row->UL_FIN_No_months }}"
                                style="color:#2196f3;cursor:pointer;margin-right:10px"
                                title="Edit"></i>
                            <i class="fa-solid fa-trash fl-delete-btn"
                                data-period-end="{{ $row->UL_FIN_Period_end }}"
                                data-type="{{ $row->UL_FIN_Type }}"
                                data-no-months="{{ $row->UL_FIN_No_months }}"
                                style="color:#e53935;cursor:pointer"
                                title="Deactivate"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:32px;color:#aaa">
                            <i class="fa-regular fa-folder-open" style="font-size:22px;display:block;margin-bottom:8px"></i>
                            No financial data found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginator', [
        'total' => $financials->total(),
        'perPage' => $financials->perPage(),
        'currentPage' => $financials->currentPage(),
        'callback' => 'loadFinancialsListPage',
        ])

    </div>
</div>

<script>
    (function() {
        var STOCKS_BASE = window.STOCKS_BASE;
        var CSRF = $('meta[name="csrf-token"]').attr('content');
        var $tbody = $('.fl-table tbody');

        $tbody.on('click', '.fl-edit-btn', function() {
            var periodEnd = $(this).data('period-end');
            var type = $(this).data('type');
            var noMonths = $(this).data('no-months');
            $('#finEditModalWrap').html(loadingSpinner());
            $.get(STOCKS_BASE + '/' + window.flFincode + '/financials/' + periodEnd + '/' + type + '/' + noMonths + '/edit')
                .done(function(html) {
                    $('#finEditModalWrap').html(html);
                })
                .fail(function() {
                    $('#finEditModalWrap').empty();
                    alert('Failed to load.');
                });
        });

        $tbody.on('click', '.fl-delete-btn', function() {
            if (!confirm('Mark this record as inactive?')) return;
            var $row = $(this).closest('tr');
            var periodEnd = $(this).data('period-end');
            var type = $(this).data('type');
            var noMonths = $(this).data('no-months');
            $.ajax({
                    url: STOCKS_BASE + '/' + window.flFincode + '/financials/' + periodEnd + '/' + type + '/' + noMonths,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    },
                })
                .done(function(res) {
                    if (res.success) $row.find('.fl-status').text('Inactive').css('color', '#e53935');
                })
                .fail(function() {
                    alert('Operation failed.');
                });
        });
    }());
</script>