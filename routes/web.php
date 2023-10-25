<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    //Dashboard routes
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/getDashboarData', [App\Http\Controllers\HomeController::class, 'getDashboarData'])->name('getDashboarData');
    Route::get('getCustomersByGroup/{id}', [App\Http\Controllers\RegisterController::class, 'getCustomersByGroup'])->name('Customers.By.Group');
    

    //Register Routes
    Route::get('/showRegister/{type}', [App\Http\Controllers\RegisterController::class, 'showRegister'])->name('show.Register');
    Route::post('/showRegisterDetails', [App\Http\Controllers\RegisterController::class, 'showRegisterDetails'])->name('show.Register.Details');
    Route::get('/showRegisterPay/{acc_id}/{type}', [App\Http\Controllers\RegisterController::class, 'showRegisterPay'])->name('show.Register.Pay');
    Route::post('/Registerpayment', [App\Http\Controllers\RegisterController::class, 'RegisterPayment'])->name('register.Payment');
    
    //Accounts Routes
    Route::get('/balanceSheet', [App\Http\Controllers\RegisterController::class, 'balanceSheet'])->name('balanceSheet');
    Route::post('/showAccountReceivable', [App\Http\Controllers\RegisterController::class, 'showAccountReceivable'])->name('show.Account.Receivable');
    Route::post('/showAccountPayable', [App\Http\Controllers\RegisterController::class, 'showAccountPayable'])->name('show.Account.Payable');
    
    //Reports Routes
    Route::post('/showStockReport', [App\Http\Controllers\StockController::class, 'showStockReport'])->name('show.Stock.Report');
    Route::post('/showCategoryStockReport', [App\Http\Controllers\StockController::class, 'showCategoryStockReport'])->name('show.Category.Stock.Report');
    Route::get('getItemsByWarehouse/{id}', [App\Http\Controllers\StockController::class, 'getItemsByWarehouse'])->name('Items.By.Warehouse');
    Route::get('getItemsByCategory/{id}', [App\Http\Controllers\StockController::class, 'getItemsByCategory'])->name('Items.By.Category');
    Route::get('getItemsDetails/{id}', [App\Http\Controllers\StockController::class, 'getItemsDetails'])->name('Items.By.Details');
    Route::get('getUnitPrice/{item_id}/{unit_id}', [App\Http\Controllers\StockController::class, 'getUnitPrice'])->name('get.Unit.Price');

    //Inventory
    Route::get('showAddItemForm/', [App\Http\Controllers\InventoryController::class, 'showAddItemForm'])->name('show.Add.Item.Form');
    Route::post('/addNewItem', [App\Http\Controllers\InventoryController::class, 'addNewItem'])->name('add.New.Item');
    
    //Sale Invoice Route
    Route::get('newSaleInvoice/{invoice_id?}', [App\Http\Controllers\SaleInvoiceController::class, 'newSaleInvoice'])->name('new.Sale.Invoice');
    Route::post('createSaleInvoice', [App\Http\Controllers\SaleInvoiceController::class, 'createSaleInvoice'])->name('create.Sale.Invoice');
    Route::post('addItemToSaleInvoice/', [App\Http\Controllers\SaleInvoiceController::class, 'addItemToSaleInvoice'])->name('add.Item.To.Sale.Invoice');
    //Sale Reports Route
    Route::post('/showSaleReport', [App\Http\Controllers\SaleReportController::class, 'showSaleReport'])->name('show.Sale.Report');
    Route::post('/showSaleInvoicesReport', [App\Http\Controllers\SaleReportController::class, 'showSaleInvoicesReport'])->name('show.Sale.Invoices.Report');


    Route::post('/showPaymentCollectionRegion', [App\Http\Controllers\RegisterController::class, 'showPaymentCollectionRegion'])->name('show.Payment.Collection.Region');
    Route::post('/showPaymentCollectionRepresentative', [App\Http\Controllers\RegisterController::class, 'showPaymentCollectionRepresentative'])->name('show.Payment.Collection.Representative');
});
