<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return view('test.index');
});

Route::get('/', [HomeController::class, 'home']);

Route::get('/dashboard', [HomeController::class, 'loginHome'])
    ->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // NEW: User Profile Routes
    Route::get('/user/profile', [HomeController::class, 'userProfile'])->name('user.profile');
    Route::patch('/user/profile', [HomeController::class, 'updateUserProfile'])->name('user.updateProfile');
});

require __DIR__ . '/auth.php';


//admin routes
Route::get('admin/profile', [AdminController::class, 'profile'])
    ->middleware(['auth', 'admin'])->name('adminProfile');

// THÊM ROUTE NÀY ĐỂ XỬ LÝ CẬP NHẬT PROFILE
Route::patch('admin/profile', [AdminController::class, 'updateProfile'])
    ->middleware(['auth', 'admin'])->name('admin.updateProfile');

Route::get('admin/dashboard', [AdminController::class, 'index'])
    ->middleware(['auth', 'admin']);
Route::get('adminSearchProducts', [AdminController::class, 'adminSearchProducts'])
    ->middleware(['auth', 'admin']);
Route::get('addProduct', [AdminController::class, 'addProduct'])
    ->middleware(['auth', 'admin']);
Route::post('createProduct', [AdminController::class, 'createProduct'])->name('admin.createProduct')
    ->middleware(['auth', 'admin']);
Route::get('viewProduct', [AdminController::class, 'viewProduct'])->name('admin.viewProduct')
    ->middleware(['auth', 'admin']);
Route::get('deleteProduct/{id}', [AdminController::class, 'deleteProduct'])->name('admin.deleteProduct');
Route::get('editProduct/{id}', [AdminController::class, 'editProduct'])
    ->middleware(['auth', 'admin']);
Route::post('updateProduct/{id}', [AdminController::class, 'updateProduct'])
    ->middleware(['auth', 'admin']);

// Order Routes
Route::get('admin/orders', [AdminController::class, 'viewOrder'])->name('admin.viewOrders')
    ->middleware(['auth', 'admin']);
Route::get('admin/orders/{id}', [AdminController::class, 'showOrderDetail'])->name('admin.viewOrderDetail')
    ->middleware(['auth', 'admin']);
Route::get('cancelOrder/{id}', [AdminController::class, 'cancelOrder'])->name('admin.cancelOrder')
    ->middleware(['auth', 'admin']);
Route::get('confirmOrder/{id}', [AdminController::class, 'confirmOrder'])->name('admin.confirmOrder')
    ->middleware(['auth', 'admin']);
// THÊM ROUTE MỚI NÀY
Route::get('admin/orders/confirm-refund/{id}', [AdminController::class, 'confirmRefund'])->name('admin.confirmRefund')
    ->middleware(['auth', 'admin']);
Route::get('admin/orders/confirm-payment/{id}', [AdminController::class, 'confirmPayment'])->name('admin.confirmPayment')
    ->middleware(['auth', 'admin']);

// Admin Warehouse Routes
Route::prefix('admin/warehouses')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'viewWarehouses'])->name('admin.viewWarehouses');
    Route::get('/add', [AdminController::class, 'addWarehouse'])->name('admin.addWarehouse');
    Route::post('/create', [AdminController::class, 'createWarehouse'])->name('admin.createWarehouse');
    Route::get('/edit/{id}', [AdminController::class, 'editWarehouse'])->name('admin.editWarehouse');
    Route::post('/update/{id}', [AdminController::class, 'updateWarehouse'])->name('admin.updateWarehouse');
    Route::get('/delete/{id}', [AdminController::class, 'deleteWarehouse'])->name('admin.deleteWarehouse');
    Route::get('/{warehouse_id}/stock', [AdminController::class, 'viewWarehouseStock'])->name('admin.viewWarehouseStock');
});

// NEW: Admin Stock Entry Management Routes (chỉnh sửa lô hàng)
Route::prefix('admin/stock-entries')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/edit/{stock_id}', [AdminController::class, 'editStockEntry'])->name('admin.editStockEntry');
    Route::post('/update/{stock_id}', [AdminController::class, 'updateStockEntry'])->name('admin.updateStockEntry');
    Route::post('/reduce/{stock_id}', [AdminController::class, 'reduceStockEntry'])->name('admin.reduceStockEntry');
});


// NEW: Admin Receive Product Routes
Route::prefix('admin/stock')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/receive', [AdminController::class, 'receiveProductForm'])->name('admin.receiveProductForm');
    Route::post('/receive', [AdminController::class, 'receiveProduct'])->name('admin.receiveProduct');
    Route::get('/search-products-ajax', [AdminController::class, 'searchProductsAjax'])->name('admin.searchProductsAjax');
});

// Route để hiển thị form kiểm tra bảo hành
Route::get('/warranty/check', [AdminController::class, 'showCheckForm'])->name('warranty.checkForm');
// Route để xử lý yêu cầu kiểm tra bảo hành
Route::get('/warranty/check-status', [AdminController::class, 'check'])->name('warranty.check');

//user routes
Route::get('warranty', [HomeController::class, 'warranty'])->name('user.warranty');
Route::get('shipping', [HomeController::class, 'shipping'])->name('user.shipping');
Route::get('product/{id}', [HomeController::class, 'productDetails'])->name('productDetails');
Route::get('addToCart/{id}', [HomeController::class, 'addToCart'])
    ->middleware(['auth'])->name('addToCart');
Route::get('/cart/count', [HomeController::class, 'cartCount'])->name('cart.count')
    ->middleware('auth');
Route::get('/myCart', [HomeController::class, 'myCart'])->name('myCart')
    ->middleware('auth');
Route::post('/update-cart/{id}', [HomeController::class, 'updateCart'])
    ->middleware('auth');
Route::get('/delete-cart/{id}', [HomeController::class, 'deleteCart'])
    ->middleware('auth');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout')
    ->middleware('auth');
Route::post('/placeOrder', [HomeController::class, 'confirmOrder'])->name('placeOrder')
    ->middleware('auth');
Route::get('/my-orders', [OrderController::class, 'index'])->name('my.orders')
    ->middleware('auth');
Route::get('/my-orders/{id}', [OrderController::class, 'show'])->name('order.details')
    ->middleware('auth');
Route::get('/user/cancel-order/{id}', [HomeController::class, 'cancelOrderByUser'])->name('user.cancelOrder')
    ->middleware(['auth']);
Route::get('/user/confirm-received/{id}', [HomeController::class, 'confirmReceivedByUser'])->name('user.confirmReceived')
    ->middleware(['auth']);


// Thêm route này để xử lý callback từ Momo
Route::get('/momo-callback', [PaymentController::class, 'handleMomoCallback'])->name('momo.callback');

// thank you route
Route::get('/thank-you', function () {
    return view('home.thankYou'); // Create a view file named thankYou.blade.php
})->name('thankYou');

//search routes
Route::get('search', [SearchController::class, 'searchProducts']);
Route::get('products', [SearchController::class, 'showProducts']);
Route::get('products/console', [SearchController::class, 'showConsoleProducts']);
Route::get('products/controller', [SearchController::class, 'showControllerProducts']);
Route::get('products/disc', [SearchController::class, 'showDiscProducts']);

Route::get('products/sony', [SearchController::class, 'showSonyProducts']);
Route::get('products/console/sony', [SearchController::class, 'showConsoleSonyProducts']);
Route::get('products/controller/sony', [SearchController::class, 'showControllerSonyProducts']);
Route::get('products/disc/sony', [SearchController::class, 'showDiscSonyProducts']);
Route::get('products/sony/ps5', [SearchController::class, 'showPS5']);
Route::get('products/sony/ps4', [SearchController::class, 'showPS4']);
Route::get('products/sony/psvita', [SearchController::class, 'showPSV']);
Route::get('products/sony/psp', [SearchController::class, 'showPSP']);

Route::get('products/nintendo', [SearchController::class, 'showNintendoProducts']);
Route::get('products/console/nintendo', [SearchController::class, 'showConsoleNintendoNintendoProducts']);
Route::get('products/controller/nintendo', [SearchController::class, 'showControllerNintendoProducts']);
Route::get('products/disc/nintendo', [SearchController::class, 'showDiscNintendoProducts']);
Route::get('products/nintendo/switch', [SearchController::class, 'showSwitch']);
Route::get('products/nintendo/3ds', [SearchController::class, 'show3DS']);

Route::get('products/xbox', [SearchController::class, 'showXboxProducts']);
Route::get('products/console/xbox', [SearchController::class, 'showConsoleXboxProducts']);
Route::get('products/controller/xbox', [SearchController::class, 'showControllerXboxProducts']);
Route::get('products/disc/xbox', [SearchController::class, 'showDiscXboxProducts']);
Route::get('products/xbox/seriesx', [SearchController::class, 'showXboxSeries']);
Route::get('products/xbox/one', [SearchController::class, 'showXboxOne']);
Route::get('products/xbox/360', [SearchController::class, 'showXbox360']);

Route::get('products/accessory', [SearchController::class, 'showAccessory']);
Route::get('products/accessory/sony', [SearchController::class, 'showAccessorySony']);
Route::get('products/accessory/nintendo', [SearchController::class, 'showAccessoryNintendo']);
Route::get('products/accessory/xbox', [SearchController::class, 'showAccessoryXbox']);
