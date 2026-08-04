<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MockupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StorefrontController;
use App\Http\Livewire\DeliveryInformation;
use App\Http\Livewire\Manage\ManageOrder;
use App\Http\Livewire\Manage\ManageProduct;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::redirect('register', '/')->name('register');
// billplz controller
Route::get('billplz-callback', [PaymentController::class, 'callback'])->name('billplz-callback');
Route::get('order/index', ManageOrder::class)->name('order.index');
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [Controller::class, 'redirectUser'])->name('dashboard');
    Route::get('mockup/standard-tee', [MockupController::class, 'redirectToStudio'])->defaults('type', 'shirt')->name('mockup.shirt');
    Route::get('mockup/sweatshirt', [MockupController::class, 'redirectToStudio'])->defaults('type', 'sweat')->name('mockup.sweat');
    Route::get('mockup/hoodie', [MockupController::class, 'redirectToStudio'])->defaults('type', 'hoodie')->name('mockup.hoodie');
    Route::get('product/manage', ManageProduct::class)->name('product.manage');
    Route::get('product/create', [ProductsController::class, 'create'])->name('product.create');
    Route::post('product', [ProductsController::class, 'store'])->name('product.store');
    Route::get('product/{product}/edit', [ProductsController::class, 'edit'])->name('product.edit');
    Route::put('product/{product}', [ProductsController::class, 'update'])->name('product.update');
    Route::delete('product/{product}', [ProductsController::class, 'destroy'])->name('product.destroy');
});
Route::get('product/{product}', [ProductsController::class, 'show'])->name('product.show');
// cart controller
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart', [CartController::class, 'store'])->name('cart.store');
Route::middleware('auth')->group(function () {
    Route::get('checkout', DeliveryInformation::class)->name('guest.checkout');
    Route::get('billplz', [PaymentController::class, 'createBill'])->name('billplz-create');
    Route::post('billplz', [PaymentController::class, 'storeBill'])->name('billplz-store');
    Route::get('billplz-redirect', [PaymentController::class, 'redirect'])->name('billplz-redirect');
});
// catalog controller
Route::redirect('explore', '/');
Route::get('shop', [StorefrontController::class, 'search'])->name('search');
Route::get('discover', [StorefrontController::class, 'discover'])->name('discover');
Route::get('{shopname}', [StorefrontController::class, 'people'])->name('people');
