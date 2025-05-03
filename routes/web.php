<?php

use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController; // <-- Añadir importación

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::middleware(['can:manage categories'])->group(function () {
        Route::resource('categories', CategoryController::class)->names('categories');
    });

    Route::middleware(['can:manage clients'])->group(function () {
        Route::resource('clients', ClientController::class)->names('clients');
    });

    Route::middleware(['can:manage products'])->group(function () {
        Route::resource('products', ProductController::class)->names('products');
        Route::get('change_status/products/{product}', [ProductController::class, 'change_status'])->name('products.change_status');
    });

    Route::middleware(['can:manage providers'])->group(function () {
        Route::resource('providers', ProviderController::class)->names('providers');
    });

    Route::middleware(['can:view purchases'])->group(function () {
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('purchases/pdf/{purchase}', [PurchaseController::class, 'generatePDF'])->name('purchases.pdf');
    });
    Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store')->middleware('can:create purchases');
    Route::get('purchase/upload/{purchase}', [PurchaseController::class,'upload'])->name('upload.purchase')->middleware('can:create purchases');
    Route::get('change_status/purchases/{purchase}', [PurchaseController::class,'change_status'])->name('purchases.change_status')->middleware('can:cancel purchases');

     Route::middleware(['can:view sales'])->group(function () {
        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });

    Route::get('/dashboard', function () {return view('dashboard');})->middleware(['auth', 'verified'])->name('dashboard');

    Route::post('sales', [SaleController::class, 'store'])->name('sales.store')->middleware('can:create sales');
    Route::get('change_status/sales/{sale}', [SaleController::class,'change_status'])->name('sales.change_status')->middleware('can:cancel sales');

    Route::middleware(['can:view reports'])->group(function () {
        Route::get('sales/reports_day', [ReportController::class,'reports_day'])->name('reports.day');
        Route::get('sales/reports_date', [ReportController::class,'reports_date'])->name('reports.date');
        Route::post('sales/report_results', [ReportController::class,'report_results'])->name('report.results');
    });

   

    // --- Grupo para Administración (Usuarios y Roles) ---
    Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {

        // Rutas de Usuarios (ya existentes)
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');

        
        Route::get('business', [BusinessController::class, 'index'])->name('business.index');
        Route::put('business/{business}', [BusinessController::class, 'update'])->name('business.update');


        Route::get('printer', [PrinterController::class, 'index'])->name('printer.index');
        Route::put('printer/{printer}', [PrinterController::class, 'update'])->name('printer.update'); 
    });


    Route::get('/prueba', function () {
        return view('prueba');
    })->name('prueba');

});
