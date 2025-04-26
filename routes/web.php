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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas web para tu aplicación. Estas
| rutas son cargadas por RouteServiceProvider y todas ellas serán
| asignadas al grupo de middleware "web". ¡Haz algo grandioso!
|
*/

// --- Rutas Públicas ---
Route::get('/', function () {
    return view('welcome');
});

// --- Rutas de Autenticación ---
// Estas rutas (login, register, etc.) deben estar accesibles para usuarios no autenticados
Auth::routes();

// --- Rutas Protegidas (Requieren Autenticación) ---
Route::middleware(['auth'])->group(function () {

    // Dashboard principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // --- Gestión de Categorías ---
    Route::middleware(['can:manage categories'])->group(function () {
        Route::resource('categories', CategoryController::class)->names('categories');
    });

    // --- Gestión de Clientes ---
    Route::middleware(['can:manage clients'])->group(function () {
        Route::resource('clients', ClientController::class)->names('clients');
    });

    // --- Gestión de Productos ---
    Route::middleware(['can:manage products'])->group(function () {
        Route::resource('products', ProductController::class)->names('products');
        Route::get('change_status/products/{product}', [ProductController::class, 'change_status'])->name('products.change_status');
    });

    // --- Gestión de Proveedores ---
    Route::middleware(['can:manage providers'])->group(function () {
        Route::resource('providers', ProviderController::class)->names('providers');
    });

    // --- Gestión de Compras ---
    // Ver compras
    Route::middleware(['can:view purchases'])->group(function () {
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('purchases/pdf/{purchase}', [PurchaseController::class, 'generatePDF'])->name('purchases.pdf');
    });
    // Crear compras
    Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store')->middleware('can:create purchases');
    // Subir archivo (asociado a crear/gestionar compras)
    Route::get('purchase/upload/{purchase}', [PurchaseController::class,'upload'])->name('upload.purchase')->middleware('can:create purchases');
    // Cancelar compras
    Route::get('change_status/purchases/{purchase}', [PurchaseController::class,'change_status'])->name('purchases.change_status')->middleware('can:cancel purchases');

    // --- Gestión de Ventas ---
     // Ver ventas
     Route::middleware(['can:view sales'])->group(function () {
        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });
    // Crear ventas
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store')->middleware('can:create sales');
    // Cancelar ventas
    Route::get('change_status/sales/{sale}', [SaleController::class,'change_status'])->name('sales.change_status')->middleware('can:cancel sales');

    // --- Reportes ---
    Route::middleware(['can:view reports'])->group(function () {
        Route::get('sales/reports_day', [ReportController::class,'reports_day'])->name('reports.day');
        Route::get('sales/reports_date', [ReportController::class,'reports_date'])->name('reports.date');
        Route::post('sales/report_results', [ReportController::class,'report_results'])->name('report.results');
    });

    // --- Configuración (Solo Admin) ---
    // Puedes usar un permiso 'manage settings' o directamente el rol 'Admin'
    Route::middleware(['role:Admin'])->group(function () { // O middleware(['permission:manage settings'])
        Route::resource('business', BusinessController::class)->names('business')->only(['index','update']);
        Route::resource('printers', PrinterController::class)->names('printers')->only(['index','update']);
        // Aquí podrías añadir rutas para gestionar usuarios si lo implementas
        // Route::resource('users', UserController::class)->names('users');
    });

    Route::middleware(['auth', 'can:manage users'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update'); // O Patch
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        // Route::get('users/{user}', [UserController::class, 'show'])->name('users.show'); // Si necesitas vista de detalle
    });


    
    // --- Ruta de Prueba (Accesible por cualquier usuario autenticado) ---
    Route::get('/prueba', function () {
        return view('prueba');
    })->name('prueba'); // Añadir nombre es buena práctica

}); // Fin del grupo middleware 'auth'

