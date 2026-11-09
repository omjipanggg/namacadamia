<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Home\ProductController as Catalog;
use App\Http\Controllers\Panel\ProductController as PanelProduct;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductAttributeValueController;
use App\Http\Controllers\ProductSKUController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::prefix('/search')->group(function() {
    Route::get('/', [SearchController::class, 'index'])->name('search.index');
});

Route::apiResource('/articles', BlogController::class)->parameters([
    'articles' => 'blog',
]);

Route::get('/products', [Catalog::class, 'index'])->name('catalog.index');
Route::get('/products/category', [Catalog::class, 'categoryIndex'])->name('catalog.category.index');
Route::get('/products/category/{category}', [Catalog::class, 'categoryShow'])->name('catalog.category.show');
Route::get('/products/{product}', [Catalog::class, 'show'])->name('catalog.show');
Route::post('/product/{product}/like', [Catalog::class, 'like'])->name('catalog.like');

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('/dashboard')->group(function() {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
                
        Route::get('/product-attributes', [ProductAttributeController::class, 'index'])->name('products.attributes.index');
        Route::post('/product-attributes', [ProductAttributeController::class, 'store'])->name('products.attributes.store');
        Route::put('/product-attributes/{id}', [ProductAttributeController::class, 'update'])->name('products.attributes.update');
        Route::delete('/product-attributes/{id}', [ProductAttributeController::class, 'destroy'])->name('products.attributes.destroy');
        
        Route::get('/product-attribute-values', [ProductAttributeValueController::class, 'index'])->name('products.attributeValues.index');
        Route::post('/product-attribute-values', [ProductAttributeValueController::class, 'store'])->name('products.attributeValues.store');
        Route::put('/product-attribute-values/{id}', [ProductAttributeValueController::class, 'update'])->name('products.attributeValues.update');
        Route::delete('/product-attribute-values/{id}', [ProductAttributeValueController::class, 'destroy'])->name('products.attributeValues.destroy');
        
        Route::apiResource('/products/category', ProductCategoryController::class);

        Route::apiResource('/products', PanelProduct::class);
        Route::get('/products/{product}/variants', [ProductSKUController::class, 'show'])->name('variants.show');
        Route::post('/products/{product}/variants', [ProductSKUController::class, 'store'])->name('variants.store');
        Route::put('/product-variants/{variant}', [ProductSKUController::class, 'update'])->name('variants.update');
        Route::delete('/product-variants/{variant}', [ProductSKUController::class, 'destroy'])->name('variants.destroy');

        // LOOKUPS
        Route::get('/lookups', [DashboardController::class, 'lookups'])->name('dashboard.lookups');
    });
    
    Route::prefix('/tables')->group(function() {
        Route::get('/', [TableController::class, 'index'])->name('table.index');
        Route::get('/{table}', [TableController::class, 'show'])->name('table.show');
        Route::post('/{table}', [TableController::class, 'store'])->name('table.store');
        Route::delete('/{table}', [TableController::class, 'destroy'])->name('table.destroy');
    });
    
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::post('/stocks/adjust', [StockController::class, 'adjust'])->name('stocks.adjust');
    Route::post('/stocks/transfer', [StockController::class, 'transfer'])->name('stocks.transfer');
    Route::post('/stocks/set-quantity', [StockController::class, 'setQuantity'])->name('stocks.setQuantity');
    Route::get('/stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');

    Route::get('/stock-transfer', [StockTransferController::class, 'index'])->name('stockTransfer.index');
    Route::post('/stock-transfer', [StockTransferController::class, 'store'])->name('stockTransfer.store');
    Route::get('/stock-transfer/{id}', [StockTransferController::class, 'show'])->name('stockTransfer.show');
    Route::post('/stock-transfer/{id}/complete', [StockTransferController::class, 'complete'])->name('stockTransfer.complete');

    Route::get('/branches/{id}/stock', [BranchController::class, 'showStock'])->name('branches.stock');
    Route::apiResource('/branches', BranchController::class);
    Route::apiResource('/suppliers', SupplierController::class);

    Route::post('/sales/{sale}/pay', [SaleController::class, 'pay'])->name('sales.pay');
    Route::post('/sales/{sale}/complete', [SaleController::class, 'complete'])->name('sales.complete');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::apiResource('/sales', SaleController::class);

    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::delete('/purchases/bulk-delete', [PurchaseController::class, 'bulkDestroy'])->name('purchases.bulkDelete');
    Route::apiResource('/purchases', PurchaseController::class);
    
    Route::delete('/roles/bulk-delete', [RoleController::class, 'bulkDestroy'])->name('roles.bulkDelete');
    Route::apiResource('/roles', RoleController::class);

    Route::put('/users/bulk-reset', [UserController::class, 'bulkReset'])->name('users.bulkReset');
    Route::put('/users/bulk-toggle-active', [UserController::class, 'bulkToggleActive'])->name('users.bulkToggleActive');
    Route::apiResource('/users', UserController::class);

    Route::apiResource('/customers', CustomerController::class)->only(['store', 'update', 'destroy']);

    Route::prefix('/reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
    });

    Route::prefix('/notifications')->group(function() {
        Route::get('/', [NotificationController::class, 'index'])->name('notification.index');
        Route::post('/', [NotificationController::class, 'store'])->name('notification.store');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('notification.destroyAll');
        Route::get('/preview', [NotificationController::class, 'preview'])->name('notification.preview');
        Route::post('/test', [NotificationController::class, 'sendTest'])->name('notification.sendTest');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notification.markAsRead');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notification.markAllAsRead');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notification.destroy');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('notification.show');
    });
});

Route::prefix('/auth')->group(function() {
    Route::prefix('/{provider}')->group(function() {
        Route::get('/redirect', [SocialiteController::class, 'redirect'])->name('provider.redirect');
        Route::get('/callback', [SocialiteController::class, 'callback'])->name('provider.callback');
        Route::get('/revoke', [SocialiteController::class, 'revoke'])->name('provider.revoke');
        Route::get('/check', [SocialiteController::class, 'check'])->name('provider.check');
    });
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

Route::get('/check', [AuthController::class, 'check'])->name('check');