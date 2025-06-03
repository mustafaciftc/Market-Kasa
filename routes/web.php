<?php

use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BankAccountController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Middleware\VerifyCsrfToken;



/*Route::get('/artisan', function () {
    Artisan::call('optimize:clear');
    return Artisan::output(); // Komut çıktısını döndürür
});*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Routes 
Route::prefix('dashboard')->middleware(['auth', 'App\Http\Middleware\AdminAccess'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Satış İşlemleri
    Route::get('/satisyap', [SaleController::class, 'index'])->name('satisyap');
    Route::post('/satisyap', [SaleController::class, 'store'])->name('satisyap.store');
    Route::get('/satisyap/{id}/edit', [SaleController::class, 'edit'])->name('satisyap.edit');
    Route::put('/satisyap/{id}', [SaleController::class, 'update'])->name('satisyap.update');
    Route::delete('/satisyap/{id}', [SaleController::class, 'destroy'])->name('satisyap.destroy');
    Route::get('/satisyonetim', [SaleController::class, 'satisyonetim'])->name('satisyonetim');
    Route::get('/satisyonetim/filter', [SaleController::class, 'filter'])->name('satisyonetim.filter');
    Route::get('/satisyonetim/{id}/details', [SaleController::class, 'details'])->name('satisyonetim.details');
    Route::get('/get-sale-details/{id}', [SaleController::class, 'getSaleDetails']);
    Route::post('/get-sale-products', [SaleController::class, 'getSaleProducts'])->name('get-sale-products');
    Route::post('/check-customer-debt', [SaleController::class, 'checkCustomerDebt'])->name('check-customer-debt');    
    Route::post('/return', [SaleController::class, 'returnProduct'])->name('return');
    Route::get('/urun-iade', [SaleController::class, 'listReturns'])->name('urun-iade');
    Route::delete('/urun-iade/{id}', [SaleController::class, 'destroyReturn'])->name('urun-iade.destroy');
    Route::post('/urun/get-by-id', [ProductController::class, 'getById'])->name('urun.getById');
    Route::post('/urun/get-by-barcode', [ProductController::class, 'getByBarcode'])->name('urun.getByBarcode');
    Route::post('/urun/get-by-category', [ProductController::class, 'getByCategory'])->name('urun.get-by-category');

    // Veresiye Yönetimi
    Route::get('/veresiyeyonetimi', [DebtController::class, 'index'])->name('veresiyeyonetimi');
    Route::get('/veresiyeekle', [DebtController::class, 'create'])->name('veresiyeekle');
    Route::post('/veresiyeekle', [DebtController::class, 'store'])->name('veresiyeekle.store');
    Route::get('/veresiyeyonetimi/{id}', [DebtController::class, 'show'])->name('veresiyeyonetimi.show');
    Route::get('/veresiyeyonetimi/{id}/edit', [DebtController::class, 'edit'])->name('veresiyeyonetimi.edit');
    Route::put('/veresiyeyonetimi/{id}', [DebtController::class, 'update'])->name('veresiyeyonetimi.update');
    Route::delete('/veresiyeyonetimi/{id}', [DebtController::class, 'destroy'])->name('veresiyeyonetimi.destroy');
    Route::post('/veresiyeyonetimi/remind', [DebtController::class, 'remind'])->name('veresiyeyonetimi.remind');
    Route::get('/veresiyeyonetimi/send-reminders', [DebtController::class, 'sendAutoReminders']);

    // Personel Yönetimi
    Route::get('/personelyonetimi', [StaffController::class, 'index'])->name('personelyonetimi');
    Route::post('/personelyonetimi', [StaffController::class, 'store'])->name('personelyonetimi.store');
    Route::get('/personelyonetimi/{id}/edit', [StaffController::class, 'edit'])->name('personelyonetimi.edit');
    Route::put('/personelyonetimi/{id}', [StaffController::class, 'update'])->name('personelyonetimi.update');
    Route::delete('/personelyonetimi/{id}', [StaffController::class, 'destroy'])->name('personelyonetimi.destroy');

    // Ürün Yönetimi
    Route::get('/urunyonetimi', [ProductController::class, 'index'])->name('urunyonetimi');
    Route::get('/urun', [ProductController::class, 'index'])->name('urun.index');
    Route::post('/urun', [ProductController::class, 'store'])->name('urun.store');
    Route::get('/urun/{id}/edit', [ProductController::class, 'edit'])->name('urun.edit');
    Route::put('/urun/{id}', [ProductController::class, 'update'])->name('urun.update');
    Route::delete('/urun/{id}', [ProductController::class, 'destroy'])->name('urun.destroy');
    Route::post('/urun/search', [SaleController::class, 'searchProducts']);
    Route::get('/remote-returns', [SaleController::class, 'manageRemoteReturns'])->name('remote_returns')->middleware('admin');

    // Kategori Yönetimi
    Route::get('/kategoriyonetimi', [CategoryController::class, 'index'])->name('kategoriyonetimi');
    Route::post('/kategoriyonetimi', [CategoryController::class, 'store'])->name('kategoriyonetimi.store');
    Route::get('/kategoriyonetimi/{id}/edit', [CategoryController::class, 'edit'])->name('kategoriyonetimi.edit');
    Route::put('/kategoriyonetimi/{id}', [CategoryController::class, 'update'])->name('kategoriyonetimi.update');
    Route::delete('/kategoriyonetimi/{id}', [CategoryController::class, 'destroy'])->name('kategoriyonetimi.destroy');

    // Profil ve Kullanıcı Yönetimi
    Route::get('/profil', [AdminController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profil/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/kullaniciyonetimi', [UserController::class, 'index'])->name('kullaniciyonetimi');
    Route::post('/kullaniciekle', [UserController::class, 'store'])->name('kullaniciekle.store');
    Route::get('/kullanici/edit/{id}', [UserController::class, 'edit'])->name('kullanici.edit');
    Route::put('/kullanici/update/{id}', [UserController::class, 'update'])->name('kullanici.update');
    Route::delete('/kullanici/delete/{id}', [UserController::class, 'destroy'])->name('kullanici.delete');
	Route::post('/bankaccount', [BankAccountController::class, 'store'])->name('bankaccount.store');
    Route::get('/bankaccount/edit/{id}', [BankAccountController::class, 'edit'])->name('bankaccount.edit');
    Route::post('/bankaccount/update/{id}', [BankAccountController::class, 'update'])->name('bankaccount.update');
	Route::put('/bankaccount/update/{id}', [BankAccountController::class, 'update'])->name('bankaccount.update');
    Route::delete('/bankaccount/delete/{id}', [BankAccountController::class, 'destroy'])->name('bankaccount.delete');

    // Müşteri Yönetimi
    Route::get('/musteriyonetimi', [CustomerController::class, 'index'])->name('musteriyonetimi');
    Route::get('/musteriyonetimi/ekle', [CustomerController::class, 'create'])->name('musteriekle');
    Route::post('/musteriyonetimi/ekle', [CustomerController::class, 'store'])->name('musteriekle.store');
    Route::get('/musteri/{id}/edit', [CustomerController::class, 'edit'])->name('musteri.edit');
    Route::put('/musteri/update/{id}', [CustomerController::class, 'update'])->name('musteri.update');
    Route::delete('/musteri/delete/{id}', [CustomerController::class, 'destroy'])->name('musteri.delete');

    // Diğer Satış ve İade Rotaları
    Route::get('/other-sales', [SaleController::class, 'otherSales'])->name('other-sales');
    Route::put('/other-sales/{id}', [SaleController::class, 'updateOtherSale'])->name('other-sales.update');
    Route::get('/other-returns', [SaleController::class, 'otherReturns'])->name('other-returns');
});

// Müşteri alışveriş rotaları
Route::prefix('customer')->middleware('auth')->group(function () {
    Route::get('/shopping', [SaleController::class, 'customerShopping'])->name('customer.shopping');
    Route::get('/urun/get-by-category', [ProductController::class, 'getByCategory']);
    Route::get('/cart', [SaleController::class, 'getCustomerCart'])->name('customer.cart');
    Route::post('/add-to-cart', [SaleController::class, 'addToCart'])->name('customer.add-to-cart');
    Route::post('/remove-from-cart', [SaleController::class, 'removeFromCart'])->name('customer.remove-from-cart');
    Route::post('/update-cart', [SaleController::class, 'updateCart'])->name('customer.update-cart');
    Route::post('/clear-cart', [SaleController::class, 'clearCart'])->name('customer.clear-cart');
    Route::get('/checkout', [SaleController::class, 'checkout'])->name('customer.checkout');
	Route::post('/order/complete', [SaleController::class, 'completeOrder'])->name('customer.order.complete');    
	Route::get('/orders', [SaleController::class, 'customerOrders'])->name('customer.orders');
	Route::get('/status/{sale_id}', [SaleController::class, 'paymentStatus'])->name('customer.status');
    Route::get('/success/{sale_id}', [SaleController::class, 'paymentSuccess'])->name('customer.success');
    Route::get('/failure/{sale_id}', [SaleController::class, 'paymentFailure'])->name('customer.failure');
	Route::get('/failure', [SaleController::class, 'paymentFailureGeneric'])->name('customer.failure.generic');									Route::get('/pending/{sale_id}', [SaleController::class, 'paymentPending'])->name('customer.pending');
	Route::post('/checkout/iyzico-callback', [SaleController::class, 'paymentCallback'])
		->name('checkout.customer.callback')
		->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/return/request', [SaleController::class, 'requestRemoteReturn'])->name('customer.return.request');
    Route::get('/returns', [SaleController::class, 'customerReturns'])->name('customer.returns');
    Route::post('/sales/products', [SaleController::class, 'getSaleProducts']);
    Route::get('/sales/details/{id}', [SaleController::class, 'details']);
    Route::get('/sales/remote-details/{id}', [SaleController::class, 'remoteProductDetails'])->name('customer.sales.remote-details');
    Route::post('/sales/remote-products', [SaleController::class, 'getRemoteSaleProducts'])->name('customer.sales.remote-products');
	Route::get('/other-returns', [SaleController::class, 'manageRemoteReturns'])->name('other-returns');    
	Route::post('/return/{id}/process', [SaleController::class, 'processRemoteReturn'])->name('customer.return.process');
	Route::post('/refund-callback', [SaleController::class, 'refundCallback'])->name('customer.refund.callback');
});


