<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use App\Helpers\SafeUpload;
use App\Models\UnlistedStock;
use App\Models\UnlistedPriceData;
use App\Models\UnlistedFinancials;
use App\Models\UnlistedThesis;
use App\Models\UnlistedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnlistedStocksController extends Controller
{
    private function canAccess(): bool
    {
        return !empty(Privilege::get('admin'))
            || !empty(Privilege::get('unlisted.stocks'));
    }

    public function index()
    {
        if (!$this->canAccess()) abort(403);

        $stocks   = UnlistedStock::orderByDesc('UL_STOCKS_FINCODE')->paginate(20);
        $fincodes = $stocks->pluck('UL_STOCKS_FINCODE');

        $latestPrices = DB::table('unlisted_price_data as pd')
            ->joinSub(
                DB::table('unlisted_price_data')
                    ->selectRaw('UL_PD_FINCODE, MAX(UL_PD_DATE) as max_date')
                    ->where('UL_PD_INVALID_FLAG', '!=', '1')
                    ->whereIn('UL_PD_FINCODE', $fincodes)
                    ->groupBy('UL_PD_FINCODE'),
                'latest',
                fn($j) => $j->on('pd.UL_PD_FINCODE', '=', 'latest.UL_PD_FINCODE')
                             ->on('pd.UL_PD_DATE', '=', 'latest.max_date')
            )
            ->select('pd.UL_PD_FINCODE', 'pd.UL_PD_DATE', 'pd.UL_PD_BID_PRICE')
            ->get()
            ->keyBy('UL_PD_FINCODE');

        return view('admin.unlisted.index', compact('stocks', 'latestPrices'));
    }

    public function docs()
    {
        if (!$this->canAccess()) abort(403);

        $stocks = UnlistedStock::orderBy('UL_STOCKS_COMPNAME')
            ->get(['UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME']);

        return view('admin.unlisted.docs', compact('stocks'));
    }

    public function docsData(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $query = DB::table('unlisted_documents as d')
            ->join('unlisted_stocks as s', 's.UL_STOCKS_FINCODE', '=', 'd.UL_DOC_FINCODE')
            ->select('d.*', 's.UL_STOCKS_COMPNAME')
            ->orderByDesc('d.UL_DOC_ID');

        if ($fincode = $request->input('fincode', '')) {
            $query->where('d.UL_DOC_FINCODE', $fincode);
        }

        if ($type = $request->input('type', '')) {
            $query->where('d.UL_DOC_TYPE', $type);
        }

        if ($status = $request->input('status', '')) {
            $query->where('d.UL_DOC_STATUS', $status);
        }

        if ($s = trim($request->input('q', ''))) {
            $query->where(function ($qq) use ($s) {
                $qq->where('s.UL_STOCKS_COMPNAME', 'like', "%{$s}%")
                   ->orWhere('d.UL_DOC_DESCRIPTION', 'like', "%{$s}%");
            });
        }

        $documents = $query->paginate(25);

        return view('admin.unlisted.partials.docs-rows', compact('documents'));
    }

    private function docsDestDir(): string
    {
        return SafeUpload::webRoot() . DIRECTORY_SEPARATOR . 'company-docs';
    }

    private function storeDocumentFile($file, int $fincode, string $type): string
    {
        $ext = SafeUpload::documentExtension($file);

        // Should be unreachable — the `mimes:` validation rule already rejected
        // anything outside the allowed types — but never fall back to a
        // client-supplied extension if this map ever misses a case.
        abort_if($ext === null, 422, 'Unsupported file type.');

        $typeSlug = Str::slug($type);
        $filename = $fincode . '_' . $typeSlug . '_' . time() . '_' . uniqid() . '.' . $ext;

        $destDir = $this->docsDestDir();
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $file->move($destDir, $filename);

        return 'company-docs/' . $filename;
    }

    private function docValidationRules(): array
    {
        return [
            'fincode'        => 'required|integer|exists:unlisted_stocks,UL_STOCKS_FINCODE',
            'type'           => 'required|in:' . implode(',', UnlistedDocument::DOC_TYPES),
            'research_house' => 'required_if:type,Research Report|nullable|string|max:100',
            'date'           => 'nullable|date',
            'period_mm'      => 'nullable|in:03,06,09,12',
            'period_yy'      => 'nullable|digits:4',
            'description'    => 'nullable|string|max:500',
            'upload_type'    => 'required|in:file,link',
            'status'         => 'required|in:1,0',
        ];
    }

    public function storeDocument(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $rules         = $this->docValidationRules();
        $rules['file'] = 'required_if:upload_type,file|nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:10240';
        $rules['link'] = 'required_if:upload_type,link|nullable|url|max:500';

        $validated = $request->validate($rules);

        $data = [
            'UL_DOC_FINCODE'        => $validated['fincode'],
            'UL_DOC_TYPE'           => $validated['type'],
            'UL_DOC_RESEARCH_HOUSE' => $validated['research_house'] ?? null,
            'UL_DOC_DATE'           => $validated['date'] ?? null,
            'UL_DOC_PERIOD_MM'      => $validated['period_mm'] ?? null,
            'UL_DOC_PERIOD_YY'      => $validated['period_yy'] ?? null,
            'UL_DOC_DESCRIPTION'    => $validated['description'] ?? null,
            'UL_DOC_STATUS'         => $validated['status'],
            'UL_DOC_INSERT_BY'      => session('uid'),
            'UL_DOC_INSERT_TIME'    => now(),
            'UL_DOC_UPDATE_TIME'    => now(),
        ];

        if ($validated['upload_type'] === 'file') {
            $data['UL_DOC_FILE_PATH'] = $this->storeDocumentFile($request->file('file'), $validated['fincode'], $validated['type']);
        } else {
            $data['UL_DOC_FILELINK'] = $validated['link'];
        }

        UnlistedDocument::create($data);

        return response()->json(['success' => true, 'message' => 'Document added successfully.']);
    }

    public function getDocument(int $id)
    {
        if (!$this->canAccess()) abort(403);

        $document = UnlistedDocument::findOrFail($id);

        return response()->json(['success' => true, 'document' => $document]);
    }

    public function updateDocument(Request $request, int $id)
    {
        if (!$this->canAccess()) abort(403);

        $document = UnlistedDocument::findOrFail($id);

        $rules         = $this->docValidationRules();
        $rules['file'] = 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:10240';
        $rules['link'] = 'nullable|url|max:500';

        $validated = $request->validate($rules);

        $data = [
            'UL_DOC_FINCODE'        => $validated['fincode'],
            'UL_DOC_TYPE'           => $validated['type'],
            'UL_DOC_RESEARCH_HOUSE' => $validated['research_house'] ?? null,
            'UL_DOC_DATE'           => $validated['date'] ?? null,
            'UL_DOC_PERIOD_MM'      => $validated['period_mm'] ?? null,
            'UL_DOC_PERIOD_YY'      => $validated['period_yy'] ?? null,
            'UL_DOC_DESCRIPTION'    => $validated['description'] ?? null,
            'UL_DOC_STATUS'         => $validated['status'],
            'UL_DOC_UPDATE_TIME'    => now(),
        ];

        if ($validated['upload_type'] === 'file') {
            if ($request->hasFile('file')) {
                $data['UL_DOC_FILE_PATH'] = $this->storeDocumentFile($request->file('file'), $validated['fincode'], $validated['type']);
                $data['UL_DOC_FILELINK']  = null;
            }
        } else {
            $data['UL_DOC_FILELINK']  = $validated['link'];
            $data['UL_DOC_FILE_PATH'] = null;
        }

        $document->update($data);

        return response()->json(['success' => true, 'message' => 'Document updated successfully.']);
    }

    public function toggleDocumentStatus(int $id)
    {
        if (!$this->canAccess()) abort(403);

        $document  = UnlistedDocument::findOrFail($id);
        $newStatus = $document->UL_DOC_STATUS === '1' ? '0' : '1';
        $document->update(['UL_DOC_STATUS' => $newStatus, 'UL_DOC_UPDATE_TIME' => now()]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    public function storeStock(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $request->validate(['name' => 'required|string|max:255'], [
            'name.required' => 'Company name is required.',
        ]);

        $name     = trim($request->input('name'));
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;
        while (UnlistedStock::where('UL_STOCKS_SLUG', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $stock = UnlistedStock::create([
            'UL_STOCKS_COMPNAME'      => $name,
            'UL_STOCKS_SLUG'          => $slug,
            'UL_STOCKS_COMPNAME_TYPE' => 'unlisted',
            'UL_STOCKS_STATUS'        => '1',
            'UL_STOCKS_INSERT_BY'     => session('uid'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock added successfully.',
            'stock'   => [
                'fincode' => $stock->UL_STOCKS_FINCODE,
                'name'    => $stock->UL_STOCKS_COMPNAME,
                'slug'    => $stock->UL_STOCKS_SLUG,
            ],
        ]);
    }

    public function storeIndustry(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $request->validate(['name' => 'required|string|max:100'], [
            'name.required' => 'Industry name is required.',
        ]);

        $name     = trim($request->input('name'));
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;
        while (DB::table('industry_master')->where('IM_SLUG', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $newCode = (DB::table('industry_master')->max('IM_IND_CODE') ?? 0) + 1;

        DB::table('industry_master')->insert([
            'IM_IND_CODE'    => $newCode,
            'IM_INDUSTRY'    => $name,
            'IM_FLAG'        => 'A',
            'IM_INSERT_TIME' => now(),
            'IM_UPDATE_TIME' => now(),
            'IM_SLUG'        => $slug,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Industry added successfully.',
            'industry' => ['code' => $newCode, 'name' => $name, 'slug' => $slug],
        ]);
    }

    public function toggleStockStatus(string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock     = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();
        $newStatus = $stock->UL_STOCKS_STATUS === '1' ? '0' : '1';
        $stock->update(['UL_STOCKS_STATUS' => $newStatus]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    public function getPriceModal(string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        return view('admin.unlisted.price-modal', compact('stock'));
    }

    public function storePriceData(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $request->validate([
            'UL_PD_DATE'      => 'required|date',
            'UL_PD_BID_PRICE' => 'required|numeric|min:0',
        ], [
            'UL_PD_DATE.required'      => 'Date is required.',
            'UL_PD_DATE.date'          => 'Enter a valid date.',
            'UL_PD_BID_PRICE.required' => 'Bid price is required.',
            'UL_PD_BID_PRICE.numeric'  => 'Bid price must be a number.',
        ]);

        UnlistedPriceData::updateOrInsert(
            ['UL_PD_FINCODE' => $fincode, 'UL_PD_DATE' => $request->input('UL_PD_DATE')],
            [
                'UL_PD_BID_PRICE'    => $request->input('UL_PD_BID_PRICE'),
                'UL_PD_INVALID_FLAG' => 0,
                'UL_PD_UPDTIME'      => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Price saved successfully.']);
    }

    public function getPriceList(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        $prices = UnlistedPriceData::where('UL_PD_FINCODE', $fincode)
                    ->orderByDesc('UL_PD_DATE')
                    ->select('UL_PD_DATE', 'UL_PD_BID_PRICE', 'UL_PD_INVALID_FLAG')
                    ->paginate(10, ['*'], 'page', $request->input('page', 1));

        return view('admin.unlisted.price-list-modal', compact('stock', 'prices'));
    }

    public function updatePriceEntry(Request $request, string $fincode, string $date)
    {
        if (!$this->canAccess()) abort(403);

        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $request->validate([
            'UL_PD_BID_PRICE' => 'required|numeric|min:0',
        ]);

        $updated = UnlistedPriceData::where('UL_PD_FINCODE', $fincode)
                    ->where('UL_PD_DATE', $date)
                    ->update([
                        'UL_PD_BID_PRICE' => $request->input('UL_PD_BID_PRICE'),
                        'UL_PD_UPDTIME'   => now(),
                    ]);

        abort_if(!$updated, 404, 'Record not found.');

        return response()->json(['success' => true, 'message' => 'Updated successfully.']);
    }

    public function deletePriceEntry(string $fincode, string $date)
    {
        if (!$this->canAccess()) abort(403);

        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $updated = UnlistedPriceData::where('UL_PD_FINCODE', $fincode)
                    ->where('UL_PD_DATE', $date)
                    ->update(['UL_PD_INVALID_FLAG' => 1]);

        abort_if(!$updated, 404, 'Record not found.');

        return response()->json(['success' => true, 'message' => 'Marked as invalid.']);
    }

    public function getFinancialsModal(string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        return view('admin.unlisted.financials-modal', compact('stock'));
    }

    public function storeFinancialsData(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $request->validate([
            'UL_FIN_Period_end' => 'required|integer',
            'UL_FIN_Type'       => 'required|in:C,S',
            'UL_FIN_No_months'  => 'required|in:3,6,12',
        ], [
            'UL_FIN_Period_end.required' => 'Period End is required.',
            'UL_FIN_Type.required'       => 'Type is required.',
            'UL_FIN_Type.in'             => 'Type must be Consolidated or Standalone.',
            'UL_FIN_No_months.required'  => 'No. Months is required.',
            'UL_FIN_No_months.in'        => 'No. Months must be 3, 6, or 12.',
        ]);

        $numericFields = [
            'UL_FIN_Unit', 'UL_FIN_FV', 'UL_FIN_NUM_SHARES',
            'UL_FIN_NET_SALES', 'UL_FIN_OTHER_INCOME', 'UL_FIN_TOTAL_INCOME',
            'UL_FIN_TOTAL_EXPENDITURE', 'UL_FIN_OPERATING_PROFIT', 'UL_FIN_INTEREST',
            'UL_FIN_DEPRECIATION', 'UL_FIN_EXCEPTIONAL_INCOME', 'UL_FIN_PBT',
            'UL_FIN_TAX', 'UL_FIN_PAT', 'UL_FIN_ADJUSTMENTS',
            'UL_FIN_PROFIT_AFTER_ADJUSTMENTS', 'UL_FIN_ADJUSTED_EPS', 'UL_FIN_DPS',
            'UL_FIN_SHAREHOLDER_FUNDS', 'UL_FIN_MINORITY_INTEREST', 'UL_FIN_BORROWINGS',
            'UL_FIN_OTHER_NONCURRENT_LIABILITIES', 'UL_FIN_TOTAL_CURRENT_LIABILITIES',
            'UL_FIN_TOTAL_LIABILITIES', 'UL_FIN_FIXED_ASSETS', 'UL_FIN_OTHER_NONCURRENT_ASSETS',
            'UL_FIN_TOTAL_CURRENT_ASSETS', 'UL_FIN_TOTAL_ASSETS', 'UL_FIN_TOTAL_DEBT',
            'UL_FIN_OPENING_CASH', 'UL_FIN_CFO', 'UL_FIN_CFI', 'UL_FIN_CFF',
            'UL_FIN_NET_CASH_FLOW', 'UL_FIN_CLOSING_CASH',
            'UL_FIN_CURRENT_LIABILITIES', 'UL_FIN_NON_CURRENT_LIABILITIES',
            'UL_FIN_CURRENT_ASSETS', 'UL_FIN_NON_CURRENT_ASSETS',
            'UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES',
            'UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES',
            'UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES',
            'UL_FIN_FREE_CASH_FLOW',
        ];

        $data = [];
        foreach ($numericFields as $field) {
            $val          = $request->input($field);
            $data[$field] = ($val !== null && $val !== '') ? $val : null;
        }

        $totalAssets = $request->input('UL_FIN_TOTAL_ASSETS');
        $totalLiab   = $request->input('UL_FIN_TOTAL_LIABILITIES');
        if ($totalAssets !== null && $totalAssets !== '' && $totalLiab !== null && $totalLiab !== '') {
            if ((float) $totalAssets !== (float) $totalLiab) {
                return response()->json(['success' => false, 'message' => 'Total Assets must equal Total Liabilities.'], 422);
            }
        }

        $exists = UnlistedFinancials::where('UL_FIN_FINCODE',   $fincode)
                    ->where('UL_FIN_Period_end', $request->input('UL_FIN_Period_end'))
                    ->where('UL_FIN_Type',       $request->input('UL_FIN_Type'))
                    ->where('UL_FIN_No_months',  $request->input('UL_FIN_No_months'))
                    ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Record already exists for this Period End, Type, and No. Months combination.'], 422);
        }

        UnlistedFinancials::insert(array_merge($data, [
            'UL_FIN_FINCODE'     => $fincode,
            'UL_FIN_Period_end'  => $request->input('UL_FIN_Period_end'),
            'UL_FIN_Type'        => $request->input('UL_FIN_Type'),
            'UL_FIN_No_months'   => $request->input('UL_FIN_No_months'),
            'UL_FIN_STATUS'      => '1',
            'UL_FIN_INSERT_BY'   => session('uid'),
            'UL_FIN_INSERT_TIME' => now(),
            'UL_FIN_UPDATE_TIME' => now(),
        ]));

        return response()->json(['success' => true, 'message' => 'Financials saved successfully.']);
    }

    public function getFinancialsListModal(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        $financials = UnlistedFinancials::where('UL_FIN_FINCODE', $fincode)
                        ->orderByDesc('UL_FIN_Period_end')
                        ->paginate(20, ['UL_FIN_FINCODE', 'UL_FIN_Period_end', 'UL_FIN_Type', 'UL_FIN_No_months', 'UL_FIN_Unit', 'UL_FIN_STATUS'], 'page', $request->input('page', 1));

        return view('admin.unlisted.financials-list-modal', compact('stock', 'financials'));
    }

    public function getFinancialsEditModal(string $fincode, string $periodEnd, string $type, string $noMonths)
    {
        if (!$this->canAccess()) abort(403);

        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        $financial = UnlistedFinancials::where('UL_FIN_FINCODE',   $fincode)
                        ->where('UL_FIN_Period_end', $periodEnd)
                        ->where('UL_FIN_Type',       $type)
                        ->where('UL_FIN_No_months',  $noMonths)
                        ->firstOrFail();

        return view('admin.unlisted.financials-modal', compact('stock', 'financial'));
    }

    public function updateFinancialsData(Request $request, string $fincode, string $periodEnd, string $type, string $noMonths)
    {
        if (!$this->canAccess()) abort(403);

        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $request->validate([
            'UL_FIN_Period_end' => 'required|integer',
            'UL_FIN_Type'       => 'required|in:C,S',
            'UL_FIN_No_months'  => 'required|in:3,6,12',
        ]);

        $newPeriod  = (string) $request->input('UL_FIN_Period_end');
        $newType    = $request->input('UL_FIN_Type');
        $newMonths  = (string) $request->input('UL_FIN_No_months');
        $keyChanged = ($newPeriod !== $periodEnd || $newType !== $type || $newMonths !== $noMonths);

        if ($keyChanged) {
            $exists = UnlistedFinancials::where('UL_FIN_FINCODE',   $fincode)
                        ->where('UL_FIN_Period_end', $newPeriod)
                        ->where('UL_FIN_Type',       $newType)
                        ->where('UL_FIN_No_months',  $newMonths)
                        ->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'A record already exists for this Period End / Type / No. Months combination.'], 422);
            }
        }

        $numericFields = [
            'UL_FIN_Unit', 'UL_FIN_FV', 'UL_FIN_NUM_SHARES',
            'UL_FIN_NET_SALES', 'UL_FIN_OTHER_INCOME', 'UL_FIN_TOTAL_INCOME',
            'UL_FIN_TOTAL_EXPENDITURE', 'UL_FIN_OPERATING_PROFIT', 'UL_FIN_INTEREST',
            'UL_FIN_DEPRECIATION', 'UL_FIN_EXCEPTIONAL_INCOME', 'UL_FIN_PBT',
            'UL_FIN_TAX', 'UL_FIN_PAT',
            'UL_FIN_SHAREHOLDER_FUNDS', 'UL_FIN_TOTAL_LIABILITIES', 'UL_FIN_TOTAL_ASSETS',
            'UL_FIN_TOTAL_DEBT', 'UL_FIN_CURRENT_LIABILITIES', 'UL_FIN_NON_CURRENT_LIABILITIES',
            'UL_FIN_CURRENT_ASSETS', 'UL_FIN_NON_CURRENT_ASSETS',
            'UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES',
            'UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES',
            'UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES',
            'UL_FIN_FREE_CASH_FLOW',
        ];

        $totalAssets = $request->input('UL_FIN_TOTAL_ASSETS');
        $totalLiab   = $request->input('UL_FIN_TOTAL_LIABILITIES');
        if ($totalAssets !== null && $totalAssets !== '' && $totalLiab !== null && $totalLiab !== '') {
            if ((float) $totalAssets !== (float) $totalLiab) {
                return response()->json(['success' => false, 'message' => 'Total Assets must equal Total Liabilities.'], 422);
            }
        }

        $data = [];
        foreach ($numericFields as $field) {
            $val          = $request->input($field);
            $data[$field] = ($val !== null && $val !== '') ? $val : null;
        }

        $data['UL_FIN_Period_end']  = $newPeriod;
        $data['UL_FIN_Type']        = $newType;
        $data['UL_FIN_No_months']   = $newMonths;
        $data['UL_FIN_UPDATE_TIME'] = now();

        UnlistedFinancials::where('UL_FIN_FINCODE',   $fincode)
            ->where('UL_FIN_Period_end', $periodEnd)
            ->where('UL_FIN_Type',       $type)
            ->where('UL_FIN_No_months',  $noMonths)
            ->update($data);

        return response()->json(['success' => true, 'message' => 'Updated successfully.']);
    }

    public function softDeleteFinancial(string $fincode, string $periodEnd, string $type, string $noMonths)
    {
        if (!$this->canAccess()) abort(403);

        $updated = UnlistedFinancials::where('UL_FIN_FINCODE',   $fincode)
                    ->where('UL_FIN_Period_end', $periodEnd)
                    ->where('UL_FIN_Type',       $type)
                    ->where('UL_FIN_No_months',  $noMonths)
                    ->update(['UL_FIN_STATUS' => '0', 'UL_FIN_UPDATE_TIME' => now()]);

        abort_if(!$updated, 404, 'Record not found.');

        return response()->json(['success' => true, 'message' => 'Marked as inactive.']);
    }

    public function getOverviewModal(string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $industries = DB::table('industry_master')
            ->where('IM_FLAG', 'A')
            ->where('IM_IND_CODE', '>', 0)
            ->orderBy('IM_INDUSTRY')
            ->get(['IM_IND_CODE', 'IM_INDUSTRY']);

        return view('admin.unlisted.overview-modal', compact('stock', 'industries'));
    }

    public function updateOverview(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        // SVG is deliberately excluded — it can carry an embedded <script> that
        // executes if the file's raw URL is opened directly (stored XSS).
        $request->validate(['logo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048'], [
            'logo.mimes' => 'Only PNG, JPG, JPEG, WEBP files are allowed.',
            'logo.max'   => 'Logo must not exceed 2 MB.',
        ]);

        $stock   = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();
        $indCode = $request->input('UL_STOCKS_IND_CODE');
        $indName = $indCode
            ? DB::table('industry_master')->where('IM_IND_CODE', $indCode)->value('IM_INDUSTRY')
            : null;

        $data = [
            'UL_STOCKS_COMPNAME'          => $request->input('UL_STOCKS_COMPNAME'),
            'UL_STOCKS_IND_CODE'          => $indCode ?: null,
            'UL_STOCKS_INDUSTRY'          => $indName,
            'UL_STOCKS_ISIN'              => $request->input('UL_STOCKS_ISIN'),
            'UL_STOCKS_S_NAME'            => $request->input('UL_STOCKS_S_NAME'),
            'UL_STOCKS_CATEGORY'          => $request->input('UL_STOCKS_CATEGORY'),
            'UL_STOCKS_INC_MONTH'         => $request->input('UL_STOCKS_INC_MONTH'),
            'UL_STOCKS_INC_YEAR'          => $request->input('UL_STOCKS_INC_YEAR'),
            'UL_STOCKS_WEBSITE'           => $request->input('UL_STOCKS_WEBSITE'),
            'UL_STOCKS_STATUS'            => $request->input('UL_STOCKS_STATUS'),
            'UL_STOCKS_COMP_RATING'       => $request->input('UL_STOCKS_COMP_RATING'),
            'UL_STOCKS_VALUATION_RATING'  => $request->input('UL_STOCKS_VALUATION_RATING'),
            'UL_STOCKS_BUY_SELL_FLAG'     => $request->input('UL_STOCKS_BUY_SELL_FLAG'),
            'UL_STOCKS_LOT_SIZE'          => $request->input('UL_STOCKS_LOT_SIZE'),
            'UL_STOCKS_ROFR_FLAG'         => $request->input('UL_STOCKS_ROFR_FLAG'),
            'UL_STOCKS_DEMAT_ACCOUNT_REQ' => $request->input('UL_STOCKS_DEMAT_ACCOUNT_REQ'),
            'UL_STOCKS_Qtr_Data_Publish'  => $request->input('UL_STOCKS_Qtr_Data_Publish'),
            'UL_STOCKS_ABOUT'             => $request->input('UL_STOCKS_ABOUT'),
        ];

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $ext = SafeUpload::imageExtension($request->file('logo'));
            abort_if($ext === null, 422, 'Unsupported image type.');
            $slug = $stock->UL_STOCKS_SLUG;
            $filename = $slug . '.' . $ext;
            $destDir  = SafeUpload::webRoot() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'company-logo';

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            foreach (['png','jpg','jpeg','svg','webp'] as $oldExt) {
                $old = $destDir . DIRECTORY_SEPARATOR . $slug . '.' . $oldExt;
                if (file_exists($old)) @unlink($old);
            }

            try {
                $request->file('logo')->move($destDir, $filename);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()]);
            }

            $data['UL_STOCKS_LOGO_LINK'] = 'images/company-logo/' . $filename;
        } elseif ($request->hasFile('logo')) {
            return response()->json(['success' => false, 'message' => 'Uploaded file is invalid.']);
        }

        $stock->update($data);

        return response()->json(['success' => true, 'message' => 'Overview updated successfully.']);
    }

    public function getThesisModal(string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $stock  = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();
        $thesis = UnlistedThesis::where('UL_THESIS_FINCODE', $fincode)
                    ->orderByDesc('UL_THESIS_ID')
                    ->first();

        return view('admin.unlisted.thesis-modal', compact('stock', 'thesis'));
    }

    public function saveThesis(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $thesis = UnlistedThesis::where('UL_THESIS_FINCODE', $fincode)
                    ->orderByDesc('UL_THESIS_ID')
                    ->first();

        $data = [
            // Rendered raw ({!! !!}) on the public company page — must be sanitized
            // the same way CmsArticleController purifies article content, since this
            // module's privilege is independently grantable to non-admin staff.
            'UL_THESIS_CONTENT'     => clean($request->input('UL_THESIS_CONTENT', '')),
            'UL_THESIS_ACTIVE'      => $request->input('UL_THESIS_ACTIVE', '1'),
            'UL_THESIS_UPDATE_TIME' => now(),
        ];

        if ($thesis) {
            UnlistedThesis::where('UL_THESIS_ID', $thesis->UL_THESIS_ID)->update($data);
        } else {
            UnlistedThesis::insert(array_merge($data, [
                'UL_THESIS_FINCODE'     => $fincode,
                'UL_THESIS_INSERT_TIME' => now(),
            ]));
        }

        return response()->json(['success' => true, 'message' => 'Thesis saved successfully.']);
    }

    public function uploadThesisImage(Request $request, string $fincode)
    {
        if (!$this->canAccess()) abort(403);

        $request->validate(['file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120']);

        $folder = SafeUpload::webRoot() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'unlisted-thesis-images';
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $file = $request->file('file');
        $ext  = SafeUpload::imageExtension($file);
        abort_if($ext === null, 422, 'Unsupported image type.');
        $filename = 'thesis_' . $fincode . '_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($folder, $filename);

        return response()->json(['location' => asset('images/unlisted-thesis-images/' . $filename)]);
    }
}
