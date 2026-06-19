<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnlistedLeadsController;
use App\Http\Controllers\UnlistedStocksController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UnlistedOrdersController;
use App\Http\Controllers\PgController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest.only');

Route::get('/register', function (\Illuminate\Http\Request $request) {
    $referer = $request->headers->get('referer', '/');
    return view('auth.register', ['landingPage' => $referer]);
})->name('register')->middleware('guest.only');

Route::post('/login',    [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:register');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// Public routes
Route::get('/',               [PublicController::class, 'welcome'])->name('public.home');
Route::get('/about',          [PublicController::class, 'about'])->name('public.about');
Route::get('/connect',        [PublicController::class, 'connect'])->name('public.connect');
Route::get('/privacy-policy',      [PublicController::class, 'privacyPolicy'])->name('public.privacy-policy');
Route::get('/terms-of-use',        [PublicController::class, 'termsOfUse'])->name('public.terms-of-use');
Route::get('/off-market-annexure', [PublicController::class, 'offMarketAnnexure'])->name('public.off-market-annexure');
Route::get('/pan-unlisted-shares', [PublicController::class, 'panUnlistedShares'])->name('public.pan-unlisted-shares');
Route::get('/sebi-guidelines',     [PublicController::class, 'sebiGuidelines'])->name('public.sebi-guidelines');
Route::get('/knowledge-centre',    [PublicController::class, 'knowledgeCentre'])->name('public.knowledge-centre');
Route::get('/faq',                 [PublicController::class, 'faq'])->name('public.faq');

Route::post('/session/invest-intent', function (\Illuminate\Http\Request $request) {
    $path = $request->input('return_to', '/');
    if (!str_starts_with($path, '/') || str_starts_with($path, '//')) $path = '/';
    session([
        'invest_intent'    => [
            'type'     => $request->input('type'),
            'company'  => $request->input('company'),
            'price'    => $request->input('price'),
            'fincode'  => $request->input('fincode'),
            'lot_size' => (int) $request->input('lot_size', 50),
        ],
        'invest_return_to' => $path,
    ]);
    return response()->json(['success' => true]);
})->middleware('web');

Route::post('/session/clear-invest-intent', function () {
    session()->forget('invest_intent');
    return response()->json(['success' => true]);
})->middleware('web');

Route::post('/invest-inquiry', [UnlistedLeadsController::class, 'investInquiry'])->name('invest.inquiry');

// Profile
Route::get('/profile',            [ProfileController::class, 'show'])->name('profile');
Route::post('/profile/update',    [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');
Route::post('/profile/kyc/bank',  [ProfileController::class, 'uploadBank'])->name('profile.kyc.bank');
Route::post('/profile/kyc/demat', [ProfileController::class, 'uploadDemat'])->name('profile.kyc.demat');
Route::post('/profile/kyc/pan',   [ProfileController::class, 'uploadPan'])->name('profile.kyc.pan');

Route::prefix('unlisted')->group(function () {
    Route::get('/',       [StocksController::class, 'buy'])->name('public.buy');
    Route::get('/stocks', [StocksController::class, 'stocks'])->name('public.stocks');
});

Route::get('/pre-ipo-unlisted-shares',       [StocksController::class, 'preIpo'])->name('public.pre-ipo');
Route::get('/pre-ipo-unlisted-shares/data',  [StocksController::class, 'preIpoData'])->name('public.pre-ipo.data');

Route::get('/unlisted-shares-price-list-india',       [StocksController::class, 'priceList'])->name('public.price-list');
Route::get('/unlisted-shares-price-list-india/data',  [StocksController::class, 'priceListData'])->name('public.price-list.data');

Route::get('/companies/{slug}/', [StocksController::class, 'company'])->name('public.company');

// ── Admin entry ──────────────────────────────────────────────────────────────
Route::get('/admin', [AdminController::class, 'redirectToDashboard'])->name('admin.index');

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->middleware('privilege:admin')
        ->name('dashboard');

    // ── Users ────────────────────────────────────────────────────────────────
    Route::get('/users', [UsersController::class, 'index'])
        ->middleware('privilege:user_master')
        ->name('users');

    Route::get('/users/{uid}/kyc-docs', [UsersController::class, 'getKycDocsModal'])
        ->middleware('privilege:user_master')
        ->name('admin.users.kyc.docs');

    Route::post('/users/{uid}/kyc/{type}/verify', [UsersController::class, 'verifyKyc'])
        ->middleware('privilege:user_master')
        ->name('admin.users.kyc.verify');

    Route::get('/users/{uid}/kyc/{type}', [UsersController::class, 'serveKycFile'])
        ->middleware('privilege:user_master')
        ->name('admin.users.kyc');

    Route::post('/users/{uid}/reset-lockout', [UsersController::class, 'resetLockout'])
        ->middleware('privilege:user_master')
        ->name('users.reset-lockout');

    Route::get('/users/{uid}/privilege', [UsersController::class, 'getPrivilegeModal'])
        ->middleware('privilege:user_master')
        ->name('users.privilege.modal');

    Route::post('/users/{uid}/privilege', [UsersController::class, 'savePrivilege'])
        ->middleware('privilege:user_master')
        ->name('users.privilege');

    // ── Unlisted Stocks ──────────────────────────────────────────────────────
    Route::get('/unlisted', [UnlistedStocksController::class, 'index'])
        ->middleware('privilege:unlisted')
        ->name('unlisted');

    Route::post('/unlisted/stocks', [UnlistedStocksController::class, 'storeStock'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.store');

    Route::post('/unlisted/industries', [UnlistedStocksController::class, 'storeIndustry'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.industries.store');

    Route::post('/unlisted/stocks/{fincode}/toggle', [UnlistedStocksController::class, 'toggleStockStatus'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.toggle');

    Route::get('/unlisted/stocks/{fincode}/price', [UnlistedStocksController::class, 'getPriceModal'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.price');

    Route::post('/unlisted/stocks/{fincode}/price', [UnlistedStocksController::class, 'storePriceData'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.price.store');

    Route::post('/unlisted/stocks/{fincode}/price-list', [UnlistedStocksController::class, 'getPriceList'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.price.list');

    Route::patch('/unlisted/stocks/{fincode}/price/{date}', [UnlistedStocksController::class, 'updatePriceEntry'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.price.update');

    Route::delete('/unlisted/stocks/{fincode}/price/{date}', [UnlistedStocksController::class, 'deletePriceEntry'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.price.delete');

    Route::get('/unlisted/stocks/{fincode}/financials', [UnlistedStocksController::class, 'getFinancialsModal'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.financials');

    Route::post('/unlisted/stocks/{fincode}/financials', [UnlistedStocksController::class, 'storeFinancialsData'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.financials.store');

    Route::post('/unlisted/stocks/{fincode}/financials-list', [UnlistedStocksController::class, 'getFinancialsListModal'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.financials.list');

    Route::get('/unlisted/stocks/{fincode}/financials/{periodEnd}/{type}/{noMonths}/edit', [UnlistedStocksController::class, 'getFinancialsEditModal'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.financials.edit');

    Route::put('/unlisted/stocks/{fincode}/financials/{periodEnd}/{type}/{noMonths}', [UnlistedStocksController::class, 'updateFinancialsData'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.financials.update');

    Route::delete('/unlisted/stocks/{fincode}/financials/{periodEnd}/{type}/{noMonths}', [UnlistedStocksController::class, 'softDeleteFinancial'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.financials.delete');

    Route::get('/unlisted/stocks/{fincode}/thesis', [UnlistedStocksController::class, 'getThesisModal'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.thesis');

    Route::post('/unlisted/stocks/{fincode}/thesis', [UnlistedStocksController::class, 'saveThesis'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.thesis.save');

    Route::post('/unlisted/stocks/{fincode}/thesis/upload-image', [UnlistedStocksController::class, 'uploadThesisImage'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.thesis.upload');

    Route::get('/unlisted/stocks/{fincode}/overview', [UnlistedStocksController::class, 'getOverviewModal'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.overview');

    Route::post('/unlisted/stocks/{fincode}/overview', [UnlistedStocksController::class, 'updateOverview'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.stocks.overview.update');

    // ── Unlisted Leads ───────────────────────────────────────────────────────
    Route::get('/unlisted/leads', [UnlistedLeadsController::class, 'leads'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.leads');

    Route::get('/unlisted/leads/data', [UnlistedLeadsController::class, 'leadsData'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.leads.data');

    Route::post('/unlisted/leads/{leadId}/allocate', [UnlistedLeadsController::class, 'allocateLead'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.leads.allocate');

    Route::post('/unlisted/leads/{leadId}/disposition', [UnlistedLeadsController::class, 'saveDisposition'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.leads.disposition');

    Route::post('/unlisted/leads/{leadId}/clear-callback-request', [UnlistedLeadsController::class, 'clearCallbackRequest'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.leads.clearCallbackRequest');

    Route::get('/unlisted/leads/{leadId}/activity', [UnlistedLeadsController::class, 'leadActivity'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.leads.activity');

    // ── PG ───────────────────────────────────────────────────────────────────
    Route::get('/pg/margin', [PgController::class, 'margin'])
        ->middleware('privilege:pg')
        ->name('pg.margin');

    Route::get('/pg/margin/data', [PgController::class, 'marginData'])
        ->middleware('privilege:pg')
        ->name('pg.margin.data');

    Route::post('/pg/margin/modal', [PgController::class, 'marginModal'])
        ->middleware('privilege:pg')
        ->name('pg.margin.modal');

    Route::get('/pg/margin-error', [PgController::class, 'marginError'])
        ->middleware('privilege:pg')
        ->name('pg.margin-error');

    Route::get('/pg/margin-error/data', [PgController::class, 'marginErrorData'])
        ->middleware('privilege:pg')
        ->name('pg.margin-error.data');

    Route::post('/pg/margin-error/modal', [PgController::class, 'marginErrorModal'])
        ->middleware('privilege:pg')
        ->name('pg.margin-error.modal');

    // ── Unlisted Orders ──────────────────────────────────────────────────────
    Route::get('/unlisted/orders', [UnlistedOrdersController::class, 'orders'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.orders');

    Route::get('/unlisted/orders/data', [UnlistedOrdersController::class, 'ordersData'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.orders.data');

    Route::post('/unlisted/orders/{orderId}/update', [UnlistedOrdersController::class, 'updateOrder'])
        ->middleware('privilege:unlisted')
        ->name('unlisted.orders.update');
});
