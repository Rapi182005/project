<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\TrackingController; 
use App\Http\Controllers\CheckoutController; 
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\GoogleAuthController;

Route::post('/admin/orders/{id}/check', [CheckoutController::class, 'checkPaymentStatus'])->name('admin.orders.check-payment');

Route::get('/gmail/login', [GoogleAuthController::class, 'redirectToGoogle'])->name('gmail.login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');

Route::get('/logout', function () {
    session()->forget(['admin_logged_in', 'user_logged_in', 'user_id', 'user_name', 'cart', 'user_role']);
    return redirect()->route('login');
})->name('logout');

Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');

Route::get('/user-dashboard', function () {
    if (!session('user_logged_in')) return redirect()->route('login');
    $user = DB::table('users')->where('id', session('user_id'))->first();
    $products = Product::all(); 
    return view('user_dashboard', compact('products', 'user'));
})->name('user.dashboard');

Route::get('/my-orders', function () {
    if (!session('user_logged_in')) return redirect()->route('login');

    // Sync only orders that are currently Unpaid
    $unpaidOrders = DB::table('orders')
        ->where('user_id', session('user_id'))
        ->where('payment_status', 'Unpaid')
        ->get();

    $controller = new \App\Http\Controllers\CheckoutController();
    foreach ($unpaidOrders as $order) {
        $controller->checkPaymentStatus($order->id);
    }

    $myOrders = DB::table('orders')
        ->where('user_id', session('user_id'))
        ->orderBy('created_at', 'desc')
        ->get();

    return view('user_orders', compact('myOrders'));
})->name('user.orders');

Route::get('/orders/edit/{id}', [TrackingController::class, 'edit'])->name('user.orders.edit');
Route::post('/orders/update/{id}', [TrackingController::class, 'update'])->name('user.orders.update');
Route::delete('/orders/delete/{id}', [TrackingController::class, 'delete'])->name('user.orders.delete');

/*
|--------------------------------------------------------------------------
| Shopping Cart Logic
|--------------------------------------------------------------------------
*/

Route::post('/cart/add/{id}', function ($id) {
    $product = Product::findOrFail($id);
    $cart = session()->get('cart', []);
    if(isset($cart[$id])) { $cart[$id]['quantity']++; } 
    else { $cart[$id] = ["name" => $product->name, "quantity" => 1, "price" => $product->price, "image" => $product->image]; }
    session()->put('cart', $cart);
    return back()->with('success', 'Product added to cart!');
})->name('cart.add');

Route::post('/cart/update', function (Request $request) {
    if($request->id && $request->quantity) {
        $cart = session()->get('cart');
        if(isset($cart[$request->id])) {
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Quantity updated!');
    }
})->name('cart.update');

Route::get('/cart/remove/{id}', function ($id) {
    $cart = session()->get('cart');
    if(isset($cart[$id])) { unset($cart[$id]); session()->put('cart', $cart); }
    return back()->with('success', 'Product removed!');
})->name('cart.remove');

Route::post('/cart/clear', function () {
    session()->forget('cart');
    return back()->with('success', 'Cart cleared!');
})->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('admin.dashboard');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/delete/{id}', [ProductController::class, 'destroy'])->name('admin.products.delete');
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    
    Route::get('/users', function() {
        if (!session('admin_logged_in')) return redirect()->route('login');
        $users = DB::table('users')->get();
        return view('admin.users', compact('users'));
    })->name('admin.users');

    // User Management Actions
    Route::post('/users/promote/{id}', function ($id) {
        $user = DB::table('users')->where('id', $id)->first();
        $newRole = ($user->role === 'Top Customer') ? 'Standard User' : 'Top Customer';
        DB::table('users')->where('id', $id)->update(['role' => $newRole]);
        return back()->with('success', "User role updated to $newRole!");
    })->name('admin.users.promote');

    Route::delete('/users/delete/{id}', function ($id) {
        DB::table('users')->where('id', $id)->delete();
        return back()->with('success', 'User account deleted successfully.');
    })->name('admin.users.delete');

    Route::get('/orders', function() {
        if (!session('admin_logged_in')) return redirect()->route('login');

        $pendingOrders = DB::table('orders')
            ->where('status', 'Pending')
            ->whereNotNull('external_id')
            ->get();

        foreach ($pendingOrders as $order) {
            app(CheckoutController::class)->checkPaymentStatus($order->id);
        }

        $orders = DB::table('orders')->orderBy('created_at', 'desc')->get();
        return view('admin.orders', compact('orders'));
    })->name('admin.orders');

   Route::post('/orders/update-status/{id}', function (Request $request, $id) {
    $order = DB::table('orders')->where('id', $id)->first();
    
    // 1. Update the database record
    DB::table('orders')->where('id', $id)->update([
        'status' => $request->status,
        'courier' => $request->courier,
        'tracking_number' => $request->tracking_number,
        'updated_at' => now()
    ]);

    // AUTO-PROMOTE LOGIC: Triggers on 'Delivered'
    if ($request->status === 'Delivered') {
        $maxPurchases = DB::table('orders')
            ->where('status', 'Delivered')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->first();

        if ($maxPurchases) {
            DB::table('users')->where('role', '!=', 'Admin')->update(['role' => 'Standard User']);
            $topUserIds = DB::table('orders')
                ->where('status', 'Delivered')
                ->select('user_id')
                ->groupBy('user_id')
                ->having(DB::raw('count(*)'), '=', $maxPurchases->total)
                ->pluck('user_id');
            DB::table('users')->whereIn('id', $topUserIds)->update(['role' => 'Top Customer']);
        }
    }

    // 2. Prepare and Send Gmail Notification
    $user = DB::table('users')->where('id', $order->user_id)->first();

    if ($user && !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        try {
            $gmail = new \App\Services\GmailService();
            $subject = "";
            $body = "";

            if ($request->status === 'Shipped') {
                $subject = "Your Order is on the Way! - #{$order->external_id}";
                $body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                        <h2 style='color: #4f46e5;'>Order Shipped!</h2>
                        <p>Hi <strong>{$user->name}</strong>,</p>
                        <p>Your order #{$order->external_id} has been handed over to <strong>{$request->courier}</strong>.</p>
                        <p><strong>Tracking Number:</strong> {$request->tracking_number}</p>
                        <hr>
                        <p>You can track your package on the courier's website. Thank you for waiting!</p>
                    </div>";
            } 
            elseif ($request->status === 'Delivered') {
                $subject = "Order Delivered Successfully! - #{$order->external_id}";
                $body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                        <h2 style='color: #16a34a;'>Package Delivered</h2>
                        <p>Hi <strong>{$user->name}</strong>,</p>
                        <p>Good news! Your order #{$order->external_id} (<strong>{$order->product_name}</strong>) has been successfully delivered.</p>
                        <hr>
                        <p>We hope you enjoy your purchase! If you have any questions, feel free to reply to this email.</p>
                        <p>Best regards,<br><strong>Brian Tanda, Due Baba</strong></p>
                    </div>";
            }

            if ($subject !== "") {
                $gmail->sendOrderEmail($user->email, $subject, $body);
            }

        } catch (\Exception $e) {
            \Log::error("Status Update Gmail Error: " . $e->getMessage());
        }
    }

    return back()->with('success', 'Order status updated. Top Customer roles recalculated!');
})->name('admin.orders.update');

    Route::get('/revenue', function() {
        if (!session('admin_logged_in')) return redirect()->route('login');
        
        $orders = DB::table('orders')->where('status', 'Delivered')->orderBy('created_at', 'desc')->get();
        
        // Leaderboard logic for the Revenue View
        $leaderboard = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'Delivered')
            ->select('users.name', 'users.email', 'users.role', DB::raw('count(orders.id) as total_purchases'))
            ->groupBy('users.id', 'users.name', 'users.email', 'users.role')
            ->orderBy('total_purchases', 'desc')
            ->get();

        return view('admin.revenue', compact('orders', 'leaderboard'));
    })->name('admin.revenue');
});