<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CcplantController;
use App\Http\Controllers\ElectricityController;
use App\Http\Controllers\FurnaceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockController;use App\Http\Controllers\SalesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerLedgerController;
use App\Http\Controllers\SupplierLedgerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeLedgerController;
use App\Http\Controllers\SettingsController;

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');

// Auth Routes
Route::get('login', [AuthController::class, 'create'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'store'])->middleware('guest');
Route::post('logout', [AuthController::class, 'destroy'])->name('logout')->middleware('auth');

// Protected Welcome Route
Route::get('welcome', function () {
    return view('welcome');
})->name('welcome')->middleware('auth');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // CC Plant
    Route::get('/ccplant', [CcplantController::class, 'index'])->name('ccplant.index');
    Route::post('/ccplant', [CcplantController::class, 'store'])->name('ccplant.add');
    Route::get('/ccplant/{heat}', [CcplantController::class, 'show'])->name('ccplant.show');
    Route::get('/ccplant/{heat}/edit', [CcplantController::class, 'edit'])->name('ccplant.edit');
    Route::put('/ccplant/{heat}', [CcplantController::class, 'update'])->name('ccplant.update');
    Route::delete('/ccplant/{heat}', [CcplantController::class, 'destroy'])->name('ccplant.delete');

    // Electricity
    Route::get('/electricity', [ElectricityController::class, 'index'])->name('electricity.index');
    Route::post('/electricity', [ElectricityController::class, 'add_log'])->name('electricity.add_log');
    Route::get('/electricity/log_table', [ElectricityController::class, 'log_table'])->name('electricity.log_table');
    Route::get('/electricity/{log}/edit', [ElectricityController::class, 'edit_log'])->name('electricity.edit_log')->where('log', '[0-9]+');
    Route::put('/electricity/{log}', [ElectricityController::class, 'update_log'])->name('electricity.update_log')->where('log', '[0-9]+');
    Route::delete('/electricity/{log}', [ElectricityController::class, 'delete_log'])->name('electricity.delete_log')->where('log', '[0-9]+');

    // Furnace
    Route::get('/furnace/issue_to_furnace', [FurnaceController::class, 'issue_to_furnace_form'])->name('furnace.issue_to_furnace_form');
    Route::post('/furnace/issue_to_furnace', [FurnaceController::class, 'issue_to_furnace'])->name('furnace.issue_to_furnace');
    Route::get('/furnace/raw_material_stock', [FurnaceController::class, 'raw_material_stock'])->name('furnace.raw_material_stock');
    Route::get('/furnace/stock/{stock}/edit', [FurnaceController::class, 'edit_stock'])->name('furnace.edit_stock');
    Route::put('/furnace/stock/{stock}', [FurnaceController::class, 'update_stock'])->name('furnace.update_stock');
    Route::delete('/furnace/stock/{stock}', [FurnaceController::class, 'delete_stock'])->name('furnace.delete_stock');

    // Purchase
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase/add-scrap', [PurchaseController::class, 'add_scrap'])->name('purchase.add_scrap');
    Route::get('/purchase/scrap_purchase_list', [PurchaseController::class, 'scrap_purchase_list'])->name('purchase.scrap_purchase_list');
    Route::get('/purchase/{purchase}/edit-scrap', [PurchaseController::class, 'edit_scrap'])->name('purchase.edit_scrap');
    Route::put('/purchase/{purchase}', [PurchaseController::class, 'update_scrap'])->name('purchase.update_scrap');
    Route::delete('/purchase/{purchase}', [PurchaseController::class, 'delete_scrap'])->name('purchase.delete_scrap');
    Route::get('/purchase/average-cost-dashboard', [PurchaseController::class, 'average_cost_dashboard'])->name('purchase.average_cost_dashboard');
    Route::post('/purchase/get-average-cost', [PurchaseController::class, 'get_average_cost'])->name('purchase.get_average_cost');
    Route::get('purchase/export/excel', [PurchaseController::class, 'exportExcel'])->name('purchase.export.excel');
    Route::get('purchase/export/pdf', [PurchaseController::class, 'exportPdf'])->name('purchase.export.pdf');

    // Supplier Ledger
    Route::get('supplier-ledger', [PurchaseController::class, 'showSupplierLedger'])->name('supplier.ledger');
    Route::post('supplier-ledger/payment', [PurchaseController::class, 'addSupplierPayment'])->name('supplier.ledger.payment');
    Route::get('purchase-report', [PurchaseController::class, 'purchaseReport'])->name('purchase.report');
    Route::post('supplier-ledger/opening-balance', [PurchaseController::class, 'addOpeningBalance'])->name('supplier.ledger.opening_balance');

    // Stock
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

    // Sales
     // Sales
    Route::get('sales/history', [SalesController::class, 'history'])->name('sales.history');
    Route::resource('sales', SalesController::class);
    Route::post('sales/{id}/restore', [SalesController::class, 'restore'])->name('sales.restore');
    Route::delete('sales/{id}/force-delete', [SalesController::class, 'forceDelete'])->name('sales.forceDelete');
    Route::get('sales/export/excel', [SalesController::class, 'exportExcel'])->name('sales.export.excel');
    Route::get('sales/export/pdf', [SalesController::class, 'exportPdf'])->name('sales.export.pdf');
    Route::get('sales/print', [SalesController::class, 'print'])->name('sales.print');
    Route::post('sales/clear-history', [SalesController::class, 'clearHistory'])->name('sales.clear_history');
    Route::get('sales/history', [SalesController::class, 'history'])->name('sales.history');
    Route::post('sales/clear-history', [SalesController::class, 'clearHistory'])->name('sales.clear_history');
    Route::get('sales-report', [SalesController::class, 'salesReport'])->name('sales.report');
    Route::get('profit-loss-report', [\App\Http\Controllers\ProfitLossController::class, 'generateReport'])->name('reports.profit_loss');

    // Customers
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');

    // Customer Ledger
    Route::get('customer-ledger', [CustomerLedgerController::class, 'show'])->name('customer.ledger');
    Route::post('customer-ledger/payment', [CustomerLedgerController::class, 'storePayment'])->name('customer.ledger.payment');
    Route::post('customer-ledger/opening-balance', [CustomerLedgerController::class, 'addOpeningBalance'])->name('customer.ledger.opening_balance');

    // Expenses
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);

    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::get('employee-ledger', [EmployeeLedgerController::class, 'show'])->name('employee.ledger');
    Route::post('employee-ledger/payment', [EmployeeLedgerController::class, 'storePayment'])->name('employee.ledger.payment');
    Route::post('employee-ledger/salary', [EmployeeLedgerController::class, 'storeSalary'])->name('employee.ledger.salary');

    // Settings
    Route::get('settings/profile', [SettingsController::class, 'profile'])->name('profile.settings');
    Route::post('settings/profile', [SettingsController::class, 'updateProfile'])->name('profile.settings.update');
    Route::get('settings/security', [SettingsController::class, 'security'])->name('security.settings');
    Route::post('settings/security', [SettingsController::class, 'updatePassword'])->name('password.update');
    Route::get('settings/application', [SettingsController::class, 'application'])->name('application.settings');
    Route::post('settings/application', [SettingsController::class, 'updateApplication'])->name('application.settings.update');
});

Route::get('/test-hash', function () {
    return Hash::make('admin');
});