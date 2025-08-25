<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductApprovalController;

/**
 * Minimal auth (replace with your own if you already have login)
 * If you already have a login, you can skip these 3 routes.
 */
Route::view('/login', 'auth.login')->name('login')->middleware('guest');
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $creds = $request->validate(['email' => 'required|email', 'password' => 'required']);
    if (! auth()->attempt($creds, $request->boolean('remember'))) {
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
    $request->session()->regenerate();
    return redirect()->intended(route('dashboard', absolute: false));
})->name('login.store')->middleware('guest');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

/** Dashboard */
Route::get('/dashboard', function () {
    $u = auth()->user();
    if (! $u) return redirect()->route('login');
    return $u->role === 'admin'
        ? redirect()->route('admin.products.pending')
        : redirect()->route('products.index');
})->name('dashboard');

/** Protected routes */
Route::middleware('auth')->group(function () {

    Route::middleware('role:vendor,admin')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        // Open a notification and go to the related product
        Route::get('/notifications/{notification}/open', function (string $notificationId) {
            $user = auth()->user();

            $n = $user->notifications()->findOrFail($notificationId);
            $n->markAsRead();

            $productId = $n->data['product_id'] ?? null;

            // Vendor goes to edit screen of their product (policy will enforce ownership)
            if ($productId) {
                return redirect()->route('products.edit', $productId);
            }

            return redirect()->route('notifications.index');
        })->name('notifications.open');
    });


    // Vendor-only
    Route::middleware('role:vendor')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Admin-only
    Route::prefix('admin')->as('admin.')->middleware('role:admin')->group(function () {
        Route::get('/products/pending', [ProductApprovalController::class, 'pending'])->name('products.pending');
        Route::put('/products/{product}/approve', [ProductApprovalController::class, 'approve'])->name('products.approve');
        Route::put('/products/{product}/reject', [ProductApprovalController::class, 'reject'])->name('products.reject');
    });

    Route::get('/notifications', function () {
        $user = auth()->user();
        return view('notifications.index', [
            'unread' => $user->unreadNotifications,
            'all'    => $user->notifications()->latest()->limit(50)->get(),
        ]);
    })->name('notifications.index');

    Route::post('/notifications/mark-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('status', 'All notifications marked as read.');
    })->name('notifications.markRead');

    // Open a notification and go to the related product
    Route::get('/notifications/{notification}/open', function (string $notificationId) {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notificationId);

        $n->markAsRead();

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.products.pending');
        }

        if ($user->isVendor()) {
            return redirect()->route('products.index');
        }

        // Fallback
        return redirect()->route('notifications.index');
    })->name('notifications.open');
});
