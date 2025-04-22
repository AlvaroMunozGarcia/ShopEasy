<?php
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('categories', CategoryController::class)->names('categories');
Route::resource('clients', ClientController::class)->names('clients');
Route::resource('products', ProductController::class)->names('products');
Route::resource('providers', ProviderController::class)->names('providers');
Route::resource('purchases', PurchaseController::class)->names('purchases')->except('edit','update','destroy');
Route::get('purchases/pdf/{purchase}', [PurchaseController::class, 'generatePDF'])->name('purchases.pdf');
Route::resource('sales', SaleController::class)->names('sales')->except('edit','update','destroy');


Route::resource('business', BusinessController::class)->names('business')->only(['index','update']);
Route::resource('printers', PrinterController::class)->names('printers')->only(['index','update']);

Route::group();

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
