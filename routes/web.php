<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Announcement;
use App\Notifications\NewAnnouncementNotification;

// Admin Controllers
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceLogController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceRequestController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SystemStatusController;
use App\Http\Controllers\Admin\TechnicianController;
use App\Http\Controllers\Admin\UserController;

// Staff Controllers
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffTechnicianController;
use App\Http\Controllers\Staff\StaffServiceRequestController;
use App\Http\Controllers\Staff\StaffSubscriptionController;
use App\Http\Controllers\Staff\StaffBillingController;
use App\Http\Controllers\Staff\StaffCustomerController;
use App\Http\Controllers\Staff\StaffProfileController;
use App\Http\Controllers\Staff\StaffNotificationController;
use App\Http\Controllers\Staff\AnnouncementController as StaffAnnouncementController;

// API Controllers
use App\Http\Controllers\Api\TechnicianAuthController;
use App\Http\Controllers\Api\TechnicianServiceRequestController;
use App\Http\Controllers\Api\TechnicianProfileController;

// Auth Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleController;

use App\Http\Controllers\ContactController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('admin.landing');
})->name('landing');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| Technician API Routes
|--------------------------------------------------------------------------
*/

// Technician API Login/Logout
Route::post('/technician/login', [TechnicianAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/technician/logout', [TechnicianAuthController::class, 'logout']);

// Technician API Protected Routes
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/technician/profile', [TechnicianProfileController::class, 'profile']);
    Route::put('/technician/profile', [TechnicianProfileController::class, 'updateProfile']);
    Route::post('/technician/profile/avatar', [TechnicianProfileController::class, 'uploadAvatar']);
    Route::post('technician/update', [TechnicianProfileController::class, 'update']);
});

// Technician Service Requests API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/technician/requests', [TechnicianServiceRequestController::class, 'index']);
    Route::get('/technician/requests/{id}', [TechnicianServiceRequestController::class, 'show']);
    Route::put('/technician/requests/{id}/status', [TechnicianServiceRequestController::class, 'updateStatus']);
});

// 🔥 ADD THIS CRITICAL ROUTE - Staff Dashboard (standalone route)
Route::middleware(['auth', 'role:staff'])->get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/export-payments', [DashboardController::class, 'exportPaymentsCSV'])->name('dashboard.export-payments');
    
    // Admin Search
    Route::get('/admin/search', [AdminSearchController::class, 'index'])->name('admin.search');
    
    // Packages
    Route::resource('packages', PackageController::class);
    
    // Subscriptions
    Route::resource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/{id}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    
    // Customers
    Route::get('/customers/list', [CustomerController::class, 'index'])->name('customers.list');
    Route::get('/customers/{customer}/subscriptions', [CustomerController::class, 'getSubscriptions'])->name('customers.subscriptions');
    Route::get('/customers/{customer}/billing', [CustomerController::class, 'getBilling'])->name('customers.billing');
    Route::get('/customers/{customer}/service-requests', [CustomerController::class, 'getServiceRequests'])->name('customers.service-requests');
    
    // ✅ ADD CUSTOMER SEARCH ROUTE
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    
    Route::resource('customers', CustomerController::class);
    
    // Users
    Route::resource('users', UserController::class);
    
    // Technicians
    Route::prefix('technicians')->group(function () {
        Route::get('/', [TechnicianController::class, 'index'])->name('technicians.index');
        Route::get('/add', [TechnicianController::class, 'create'])->name('technicians.create');
        Route::post('/', [TechnicianController::class, 'store'])->name('technicians.store');
        Route::get('/{technician}', [TechnicianController::class, 'show'])->name('technicians.show');
        Route::get('/{technician}/edit', [TechnicianController::class, 'edit'])->name('technicians.edit');
        Route::put('/{technician}', [TechnicianController::class, 'update'])->name('technicians.update');
        Route::delete('/{technician}', [TechnicianController::class, 'destroy'])->name('technicians.destroy');
        Route::get('/technician/jobs', [TechnicianController::class, 'myJobs'])->name('technician.jobs');
        Route::post('/technician/jobs/{id}/complete', [TechnicianController::class, 'completeJob'])->name('technician.complete');
        Route::middleware('auth:sanctum')->post('/technician/update-location', [TechnicianController::class, 'updateLocation']);
        Route::get('/{id}/location', [TechnicianController::class, 'getLocation']);
        
        // ✅ ADD GECODE ALL TECHNICIANS ROUTE
        Route::post('/geocode-all', [TechnicianController::class, 'geocodeAll'])->name('technicians.geocode-all');
    });
    
    // Service Requests
    Route::resource('service_requests', ServiceRequestController::class);
    Route::get('service_requests/{serviceRequest}/assign', [ServiceRequestController::class, 'assignPage'])->name('service_requests.assignPage');
    Route::post('service_requests/{serviceRequest}/assign', [ServiceRequestController::class, 'assignTechnician'])->name('service_requests.assign');
    Route::post('service_requests/{serviceRequest}/status', [ServiceRequestController::class, 'updateStatus'])->name('service_requests.updateStatus');
    
    // Billing
    Route::resource('billing', BillingController::class);
    Route::post('billing/{id}/mark-paid', [BillingController::class, 'markPaid'])->name('billing.markPaid');
    Route::get('billing/{id}/receipt', [BillingController::class, 'receipt'])->name('billing.receipt');
    Route::get('/billing/{id}/receipt-pdf', [BillingController::class, 'downloadReceipt'])->name('billing.receipt.pdf');
    
    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/api/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
    
    // Invoice Logs
    Route::get('/invoice-logs', [InvoiceLogController::class, 'index'])->name('invoice.logs');
    Route::get('/invoice-logs/{invoice}', [InvoiceLogController::class, 'showInvoice'])->name('invoices.show');
    
    // Locations
    Route::prefix('locations')->group(function() {
        Route::get('technician/{id}', [LocationController::class, 'technician'])->name('locations.technician');
        Route::get('customer/{id}', [LocationController::class, 'customer'])->name('locations.customer');
        Route::get('route/{id}', [LocationController::class, 'route'])->name('locations.route');
        Route::get('locations/service-request/{id}', [LocationController::class, 'serviceRequestMap'])->name('locations.service-request');
    });
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/notifications/{id}/read', function ($id) {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must log in first.');
        }
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? route('notifications.index'));
    })->name('notifications.read');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // System Status
    Route::get('/api/system/status', [SystemStatusController::class, 'status'])->name('system.status');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/api/settings/system-status', [SettingsController::class, 'systemStatus'])->name('settings.system-status');
    
    // Test Broadcast
    Route::get('/test-broadcast', function () {
        $user = Auth::user();
        $announcement = Announcement::latest()->first();
        if (!$announcement) {
            return '⚠️ No announcement found in DB.';
        }
        $user->notify(new NewAnnouncementNotification($announcement));
        return '✅ Test notification sent to Pusher channel for user #' . $user->id;
    })->name('test.broadcast');
});

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard (this should work now with the standalone route above)
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Service Requests - ADD THE DESTROY ROUTE HERE
    Route::get('/service-requests', [StaffServiceRequestController::class, 'index'])->name('service_requests.index');
    Route::get('/service-requests/create', [StaffServiceRequestController::class, 'create'])->name('service_requests.create');
    Route::post('/service-requests', [StaffServiceRequestController::class, 'store'])->name('service_requests.store');
    Route::get('/service-requests/{serviceRequest}', [StaffServiceRequestController::class, 'show'])->name('service_requests.show');
    Route::get('/service-requests/{serviceRequest}/assign', [StaffServiceRequestController::class, 'assignPage'])->name('service_requests.assignPage');
    Route::post('/service-requests/{serviceRequest}/assign', [StaffServiceRequestController::class, 'assignTechnician'])->name('service_requests.assign');
    Route::post('/service-requests/{serviceRequest}/update-status', [StaffServiceRequestController::class, 'updateStatus'])->name('service_requests.update_status');
    Route::post('/service-requests/{serviceRequest}/updateStatus', [StaffServiceRequestController::class, 'updateStatus'])->name('service_requests.updateStatus');
    
    // 🔥 ADD THIS MISSING DESTROY ROUTE
    Route::delete('/service-requests/{serviceRequest}', [StaffServiceRequestController::class, 'destroy'])->name('service_requests.destroy');
    
    // Customers
    Route::get('customers', [StaffCustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [StaffCustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{customer}/edit', [StaffCustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}', [StaffCustomerController::class, 'update'])->name('customers.update');
    
    // Technicians
    Route::get('technicians', [StaffTechnicianController::class, 'index'])->name('technicians.index');
    Route::get('technicians/{technician}', [StaffTechnicianController::class, 'show'])->name('technicians.show');
    Route::get('technicians/{technician}/edit', [StaffTechnicianController::class, 'edit'])->name('technicians.edit');
    Route::put('technicians/{technician}', [StaffTechnicianController::class, 'update'])->name('technicians.update');
    
    // Subscriptions
    Route::get('/subscriptions', [StaffSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{id}', [StaffSubscriptionController::class, 'show'])->name('subscriptions.show');
    
    // Billing
    Route::get('/billing', [StaffBillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/{billing}', [StaffBillingController::class, 'show'])->name('billing.show');
    Route::post('/billing/{billing}/mark-paid', [StaffBillingController::class, 'markPaid'])->name('billing.markPaid');
    Route::get('/billing/{billing}/receipt', [StaffBillingController::class, 'receipt'])->name('billing.receipt');
    Route::get('/billing/{billing}/receipt-pdf', [StaffBillingController::class, 'downloadReceipt'])->name('billing.receipt.pdf');
    
    // Announcements
    Route::resource('announcements', StaffAnnouncementController::class)->except(['destroy']);
    
    // Profile
    Route::get('/profile', [StaffProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    
    // Notifications
    Route::get('/notifications', [StaffNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [StaffNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/mark-read', [StaffNotificationController::class, 'markAsRead'])->name('notifications.markRead');

    
});