<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use App\Models\UnlistedStock;
use App\Models\UnlistedPriceData;
use App\Models\UnlistedFinancials;
use App\Models\UnlistedThesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnlistedStocksController extends Controller
{
    public function index()
    {
        $isAdmin = !empty(Privilege::get('admin')) || !empty(Privilege::get('user_master'));
        if (!$isAdmin && empty(Privilege::get('unlisted.stockx'))) abort(403);

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

    public function storeStock(Request $request)
    {
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
        $stock     = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();
        $newStatus = $stock->UL_STOCKS_STATUS === '1' ? '0' : '1';
        $stock->update(['UL_STOCKS_STATUS' => $newStatus]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    public function getPriceModal(string $fincode)
    {
        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        return view('admin.unlisted.price-modal', compact('stock'));
    }

    public function storePriceData(Request $request, string $fincode)
    {
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
        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $updated = UnlistedPriceData::where('UL_PD_FINCODE', $fincode)
                    ->where('UL_PD_DATE', $date)
                    ->update(['UL_PD_INVALID_FLAG' => 1]);

        abort_if(!$updated, 404, 'Record not found.');

        return response()->json(['success' => true, 'message' => 'Marked as invalid.']);
    }

    public function getFinancialsModal(string $fincode)
    {
        $stock = UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)
                    ->select('UL_STOCKS_FINCODE', 'UL_STOCKS_COMPNAME')
                    ->firstOrFail();

        return view('admin.unlisted.financials-modal', compact('stock'));
    }

    public function storeFinancialsData(Request $request, string $fincode)
    {
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
        $request->validate(['logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048'], [
            'logo.mimes' => 'Only PNG, JPG, JPEG, SVG, WEBP files are allowed.',
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
            $ext  = strtolower($request->file('logo')->getClientOriginalExtension());
            $ext  = ['jfif' => 'jpg', 'jpeg' => 'jpg'][$ext] ?? $ext;
            $slug = $stock->UL_STOCKS_SLUG;
            $filename = $slug . '.' . $ext;
            // On Hostinger, public_html/ sits next to the app dir (unlisted-gain/../public_html)
            // public_path() returns unlisted-gain/public/ which is NOT web-accessible there
            $parentPublicHtml = dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html';
            $webRoot  = is_dir($parentPublicHtml) ? $parentPublicHtml : public_path();
            $destDir  = $webRoot . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'company-logo';

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
        UnlistedStock::where('UL_STOCKS_FINCODE', $fincode)->firstOrFail();

        $thesis = UnlistedThesis::where('UL_THESIS_FINCODE', $fincode)
                    ->orderByDesc('UL_THESIS_ID')
                    ->first();

        $data = [
            'UL_THESIS_CONTENT'     => $request->input('UL_THESIS_CONTENT'),
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
        $request->validate(['file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120']);

        $folder = public_path('images/unlisted-thesis-images');
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $file     = $request->file('file');
        $filename = 'thesis_' . $fincode . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($folder, $filename);

        return response()->json(['location' => asset('images/unlisted-thesis-images/' . $filename)]);
    }
}
