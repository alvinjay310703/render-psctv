<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TechnicianAssigned;
use App\Notifications\ServiceCompleted;
use App\Events\ServiceRequestStatusUpdated;

class ServiceRequestController extends Controller
{
    /** 🧭 List all service requests */
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['technician', 'creator', 'customer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhere('service_type', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        $serviceRequests = $query->latest()->paginate(10);

        // Get technicians for filter dropdown
        $technicians = Technician::with('user')->active()->get();

        return view('service_requests.index', compact('serviceRequests', 'technicians'));
    }

    /** 🧩 Create form - UPDATED */
    public function create()
    {
        // ✅ PRELOAD RECENT ACTIVE CUSTOMERS FOR QUICK SELECTION
        $recentCustomers = Customer::with('user')
            ->where('status', 'active')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->user->name,
                    'email' => $customer->user->email,
                    'phone' => $customer->phone,
                    'address' => $customer->full_address
                ];
            });
        
        return view('service_requests.create', compact('recentCustomers'));
    }

    /** 🧾 Store new service request */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'address'       => 'required|string|max:255',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'service_type'  => 'required|string|max:100',
            'notes'         => 'nullable|string',
            'urgency'       => 'nullable|in:low,medium,high,emergency',
            'customer_id'   => 'nullable|exists:customers,id',
        ]);

        // ✅ ATTACH CUSTOMER INFO IF CUSTOMER ID IS PROVIDED
        if (!empty($validated['customer_id'])) {
            $customer = Customer::with('user')->find($validated['customer_id']);
            if ($customer) {
                $validated['customer_id'] = $customer->id;
                $validated['customer_name'] = $customer->user->name;
                $validated['phone'] = $customer->phone ?? $validated['phone'];
                $validated['email'] = $customer->user->email ?? $validated['email'];
                $validated['address'] = $customer->full_address ?? $validated['address'];
                
                // Use customer coordinates if available
                if ($customer->latitude && $customer->longitude) {
                    $validated['latitude'] = $customer->latitude;
                    $validated['longitude'] = $customer->longitude;
                }
            }
        }

        // Attach registered customer info if logged in
        if (Auth::check() && empty($validated['customer_id'])) {
            $customer = Auth::user()->customer ?? null;
            if ($customer) {
                $validated['customer_id']   = $customer->id;
                $validated['customer_name'] = $customer->user->name;
                $validated['phone']         = $customer->phone ?? $validated['phone'];
                $validated['email']         = $customer->user->email ?? $validated['email'];
            }
        }

        $validated['status']     = 'pending';
        $validated['created_by'] = Auth::id();

        $serviceRequest = ServiceRequest::create($validated);

        // Auto-geocode if missing
        if ((empty($validated['latitude']) || empty($validated['longitude'])) && !empty($validated['address'])) {
            try {
                $serviceRequest->geocodeAddress();
            } catch (\Throwable $e) {
                Log::warning("Geocoding skipped for ServiceRequest #{$serviceRequest->id}: " . $e->getMessage());
            }
        }

        // Notify admins about new service request
        $this->notifyNewServiceRequest($serviceRequest);

        return redirect()
            ->route('service_requests.index')
            ->with('success', 'Service request created successfully.');
    }

    // 🔍 Show single request
    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['technician.user', 'creator', 'photos', 'customer.user']);
        
        // Get similar requests for sidebar
        $similarRequests = ServiceRequest::where('service_type', $serviceRequest->service_type)
            ->where('id', '!=', $serviceRequest->id)
            ->where('status', 'completed')
            ->with('technician')
            ->latest()
            ->limit(5)
            ->get();

        return view('service_requests.show', compact('serviceRequest', 'similarRequests'));
    }

    /** ✏️ Edit request form */
    public function edit(ServiceRequest $serviceRequest)
    {
        return view('service_requests.edit', compact('serviceRequest'));
    }

    /** 🧰 Update request */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'service_type'  => 'required|string|max:100',
            'notes'         => 'nullable|string',
            'address'       => 'nullable|string|max:255',
            'urgency'       => 'nullable|in:low,medium,high,emergency',
        ]);

        $serviceRequest->update($validated);

        // Re-geocode if address changed
        if ($serviceRequest->wasChanged('address') && !empty($validated['address'])) {
            try {
                $serviceRequest->geocodeAddress();
            } catch (\Throwable $e) {
                Log::warning("Geocoding failed during update for ServiceRequest #{$serviceRequest->id}: " . $e->getMessage());
            }
        }

        return redirect()
            ->route('service_requests.show', $serviceRequest->id)
            ->with('success', 'Service request updated successfully.');
    }

    /** ❌ Delete request */
    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequestId = $serviceRequest->id;
        $serviceRequest->delete();

        Log::info("Service request #{$serviceRequestId} deleted by user #" . Auth::id());

        return redirect()
            ->route('service_requests.index')
            ->with('success', 'Service request deleted successfully.');
    }

    /** 👨‍🔧 Show technician assignment page - UPDATED */
    public function assignPage(ServiceRequest $serviceRequest)
    {
        // Load technicians with coordinates and ensure we have valid data
        $technicians = Technician::available()
            ->with('user')
            ->get()
            ->map(function ($tech) {
                // Ensure coordinates are set, use defaults if not
                if (!$tech->latitude || !$tech->longitude) {
                    $tech->latitude = 12.8797;
                    $tech->longitude = 121.7740;
                    
                    // Try to geocode if address exists
                    if (!empty($tech->address)) {
                        Log::info("Attempting to geocode technician {$tech->id} with address: {$tech->address}");
                        $tech->geocodeAddress();
                        // Reload the technician to get updated coordinates
                        $tech->refresh();
                    }
                }
                return $tech;
            });

        return view('service_requests.assign', compact('serviceRequest', 'technicians'));
    }

    /** ⚙️ Assign technician to service request - FIXED VERSION */
    public function assignTechnician(Request $request, ServiceRequest $serviceRequest)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'notes'         => 'nullable|string|max:1000',
            'scheduled_date' => 'nullable|date|after:today',
        ]);

        // ✅ Find technician
        $technician = Technician::findOrFail($validated['technician_id']);

        if ($technician->status !== 'active') {
            return back()->withErrors(['technician_id' => "Technician {$technician->full_name} is not active."]);
        }

        // ✅ Check active jobs count using a subquery instead of scope to avoid PostgreSQL issue
        $maxActive = config('service.tech_max_active', 5);
        $activeJobsCount = ServiceRequest::where('technician_id', $technician->id)
            ->whereIn('status', ['pending', 'assigned', 'in-progress'])
            ->count();

        if ($activeJobsCount >= $maxActive) {
            return back()->withErrors(['technician_id' => "Technician {$technician->full_name} already has maximum active jobs."])->withInput();
        }

        // ✅ Assign technician using model method
        $serviceRequest->assignTo($technician->id);

        // ✅ Update notes if provided
        if (!empty($validated['notes'])) {
            $serviceRequest->update(['notes' => $validated['notes']]);
        }

        // ✅ Geocode if missing
        if (!$serviceRequest->latitude || !$serviceRequest->longitude) {
            try { 
                $serviceRequest->geocodeAddress(); 
            } catch (\Throwable $e) {
                Log::warning("Geocoding failed for ServiceRequest #{$serviceRequest->id}: {$e->getMessage()}");
            }
        }

        // ✅ Load related users
        $serviceRequest->load(['technician.user', 'customer.user']);

        // ✅ Create notification instance
        $notification = new TechnicianAssigned($serviceRequest, Auth::user());

        // 🔔 Notify technician
        if ($serviceRequest->technician?->user) {
            try {
                $serviceRequest->technician->user->notify($notification);
                Log::info("Notified technician #{$serviceRequest->technician->user->id} about assignment to request #{$serviceRequest->id}");
            } catch (\Throwable $e) {
                Log::error("Failed to notify technician (User ID: {$serviceRequest->technician->user->id}): {$e->getMessage()}");
            }
        }

        // 🔔 Notify customer
        if ($serviceRequest->customer?->user) {
            try {
                $serviceRequest->customer->user->notify($notification);
                Log::info("Notified customer #{$serviceRequest->customer->user->id} about technician assignment for request #{$serviceRequest->id}");
            } catch (\Throwable $e) {
                Log::error("Failed to notify customer (User ID: {$serviceRequest->customer->user->id}): {$e->getMessage()}");
            }
        }

        // 🔔 Notify admins about assignment
        $admins = User::whereIn('role', ['admin', 'staff'])->get();
        if ($admins->count() > 0) {
            try {
                Notification::send($admins, $notification);
                Log::info("Notified {$admins->count()} admins about technician assignment for request #{$serviceRequest->id}");
            } catch (\Throwable $e) {
                Log::error("Failed to notify admins about assignment for request #{$serviceRequest->id}: {$e->getMessage()}");
            }
        }

        // 🔔 Fire real-time broadcast event
        try {
            event(new ServiceRequestStatusUpdated($serviceRequest));
            Log::info("Broadcasted status update for ServiceRequest #{$serviceRequest->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to broadcast ServiceRequestStatusUpdated for ServiceRequest #{$serviceRequest->id}: {$e->getMessage()}");
        }

        return redirect()
            ->route('service_requests.show', $serviceRequest->id)
            ->with('success', "Technician {$technician->full_name} assigned successfully and notifications sent!");
    }

    /** 🔁 Update request status - USING MODEL METHOD */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'status'   => 'required|string|in:pending,assigned,in-progress,completed,cancelled',
            'report'   => 'nullable|string',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $oldStatus = $serviceRequest->status;
        $serviceRequest->status = $request->status;

        // Handle status-specific logic
        switch ($request->status) {
            case 'in-progress':
                if (!$serviceRequest->started_at) {
                    $serviceRequest->started_at = now();
                    Log::info("Service request #{$serviceRequest->id} marked as in-progress");
                }
                break;

            case 'completed':
                $serviceRequest->completed_at = now();
                $serviceRequest->report = $request->report ?? $serviceRequest->report;

                // 📸 Save completion photos
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $path = $photo->store('service_photos', 'public');
                        $serviceRequest->photos()->create([
                            'path'  => $path,
                            'mime'  => $photo->getMimeType(),
                            'label' => 'Completion Proof',
                        ]);
                    }
                    Log::info("Uploaded {$request->file('photos')->count()} completion photos for service request #{$serviceRequest->id}");
                }

                // 🔔 CRITICAL: Trigger completion notifications via model method
                $adminsNotified = $serviceRequest->notifyCompletion();
                
                // Store notification info in session
                session()->flash('notification_info', 
                    "Service marked as completed. Notified {$adminsNotified} admin/staff members."
                );
                
                Log::info("Service request #{$serviceRequest->id} completed. Model method notified {$adminsNotified} admin/staff users.");
                break;

            case 'cancelled':
                $serviceRequest->completed_at = null;
                $serviceRequest->started_at = null;
                Log::info("Service request #{$serviceRequest->id} cancelled by user #" . Auth::id());
                break;
        }

        $serviceRequest->save();

        // Log status change
        Log::info("Service request #{$serviceRequest->id} status changed from '{$oldStatus}' to '{$request->status}' by user #" . Auth::id());

        /** ✅ Broadcast realtime status update */
        try {
            broadcast(new ServiceRequestStatusUpdated($serviceRequest))->toOthers();
            Log::info("Broadcasted status update for service request #{$serviceRequest->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to broadcast status update for request #{$serviceRequest->id}: {$e->getMessage()}");
        }

        return redirect()
            ->route('service_requests.show', $serviceRequest->id)
            ->with('success', "Service request status updated to " . ucfirst(str_replace('-', ' ', $request->status)) . "!");
    }

    /** 📊 Service requests statistics */
    public function statistics()
    {
        $stats = [
            'total' => ServiceRequest::count(),
            'pending' => ServiceRequest::pending()->count(),
            'assigned' => ServiceRequest::where('status', 'assigned')->count(),
            'in_progress' => ServiceRequest::where('status', 'in-progress')->count(),
            'completed' => ServiceRequest::completed()->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
        ];

        // Recent completed requests
        $recentCompleted = ServiceRequest::completed()
            ->with(['technician', 'customer.user'])
            ->latest('completed_at')
            ->limit(10)
            ->get();

        // Top technicians by completed jobs
        $topTechnicians = Technician::withCount(['serviceRequests as completed_requests_count' => function($query) {
            $query->where('status', 'completed');
        }])
        ->orderBy('completed_requests_count', 'desc')
        ->limit(5)
        ->get();

        return view('service_requests.statistics', compact('stats', 'recentCompleted', 'topTechnicians'));
    }

    /** 🔔 Test completion notifications (for debugging) */
    public function testCompletionNotification(ServiceRequest $serviceRequest)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('service_requests.show', $serviceRequest->id)
                ->with('error', 'Only administrators can test notifications.');
        }

        $results = $serviceRequest->triggerCompletionNotifications();

        return redirect()
            ->route('service_requests.show', $serviceRequest->id)
            ->with('success', "Test notifications sent! Results: " . 
                "Customer: " . ($results['customer_notified'] ? 'Yes' : 'No') . ", " .
                "Technician: " . ($results['technician_notified'] ? 'Yes' : 'No') . ", " .
                "Admins: {$results['admins_notified']}, " .
                "Total: {$results['total_notifications']}"
            );
    }

    /** 📋 Bulk status update */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:service_requests,id',
            'status' => 'required|in:pending,assigned,in-progress,completed,cancelled',
        ]);

        $updatedCount = 0;
        $completedRequests = [];

        foreach ($request->request_ids as $requestId) {
            $serviceRequest = ServiceRequest::find($requestId);
            
            if ($serviceRequest && $serviceRequest->status !== $request->status) {
                $serviceRequest->update(['status' => $request->status]);
                
                // Handle completion notifications
                if ($request->status === 'completed') {
                    $serviceRequest->completed_at = now();
                    $serviceRequest->save();
                    $completedRequests[] = $serviceRequest;
                }
                
                $updatedCount++;
            }
        }

        // Notify for completed requests using model method
        foreach ($completedRequests as $completedRequest) {
            $completedRequest->notifyCompletion();
        }

        Log::info("Bulk updated {$updatedCount} service requests to '{$request->status}' by user #" . Auth::id());

        return redirect()
            ->route('service_requests.index')
            ->with('success', "Updated status for {$updatedCount} service requests.");
    }

    /** 🔔 Private helper method to notify admins about new service requests */
    private function notifyNewServiceRequest(ServiceRequest $serviceRequest)
    {
        try {
            $admins = User::whereIn('role', ['admin', 'staff'])->get();
            
            if ($admins->count() > 0) {
                Notification::send($admins, new \App\Notifications\ServiceRequestCreated($serviceRequest, $serviceRequest->display_customer_name));
                Log::info("Notified {$admins->count()} admins about new service request #{$serviceRequest->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to notify admins about new service request #{$serviceRequest->id}: {$e->getMessage()}");
        }
    }
}