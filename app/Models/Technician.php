<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Technician extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'address',
        'service_area',
        'date_hire',
        'specialization',
        'emergency_name',
        'emergency_phone',
        'status',
        'profile_picture',
        'latitude',
        'longitude',
        'technician_id',
    ];

    protected $casts = [
        'date_hire' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // Add this to make active_jobs_count accessible
    protected $appends = ['active_jobs_count'];

    /** Relationship: technician has many service requests */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'technician_id');
    }

    /** Relationship: belongs to user */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Completed jobs */
    public function getJobsCompletedAttribute()
    {
        return $this->serviceRequests()->where('status', 'completed')->count();
    }

    /** Pending or in-progress jobs */
    public function getJobsPendingAttribute()
    {
        return $this->serviceRequests()->whereIn('status', ['pending', 'assigned', 'in-progress'])->count();
    }

    /** FIXED: Available technicians (less than max active jobs) - PostgreSQL compatible */
    public function scopeAvailable($query, $maxJobs = 5)
    {
        return $query->whereHas('serviceRequests', function ($q) use ($maxJobs) {
            $q->whereIn('status', ['pending', 'assigned', 'in-progress']);
        }, '<', $maxJobs)
        ->orWhereDoesntHave('serviceRequests', function ($q) {
            $q->whereIn('status', ['pending', 'assigned', 'in-progress']);
        })
        ->where('status', 'active');
    }

    /** Alternative method using subquery (PostgreSQL compatible) */
    public function scopeAvailableAlt($query, $maxJobs = 5)
    {
        return $query->where('status', 'active')
            ->whereRaw("(
                SELECT COUNT(*) 
                FROM service_requests 
                WHERE technicians.id = service_requests.technician_id 
                AND status IN ('pending', 'assigned', 'in-progress')
            ) < ?", [$maxJobs]);
    }

    /** Active technicians */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Active job counter - Fixed for PostgreSQL */
    public function getActiveJobsCountAttribute()
    {
        if (!array_key_exists('active_jobs_count', $this->relations)) {
            $this->relations['active_jobs_count'] = $this->serviceRequests()
                ->whereIn('status', ['pending', 'assigned', 'in-progress'])
                ->count();
        }
        return $this->relations['active_jobs_count'];
    }

    /** Helper: is technician active */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Check if technician is available for new jobs */
    public function isAvailable($maxJobs = 5): bool
    {
        return $this->isActive() && $this->active_jobs_count < $maxJobs;
    }

    /** Get coordinates from address using multiple fallback methods */
    public static function getCoordinatesFromAddress($address)
    {
        if (!$address) {
            Log::warning('Geocoding: No address provided');
            return [null, null];
        }

        Log::info("Attempting to geocode address: {$address}");

        // Method 1: Try OpenStreetMap with proper headers
        $coordinates = self::tryOpenStreetMap($address);
        if ($coordinates[0] && $coordinates[1]) {
            Log::info("OpenStreetMap success: {$coordinates[0]}, {$coordinates[1]}");
            return $coordinates;
        }

        // Method 2: Try with simplified address (city/region only)
        $simplifiedAddress = self::simplifyAddress($address);
        if ($simplifiedAddress !== $address) {
            Log::info("Trying simplified address: {$simplifiedAddress}");
            $coordinates = self::tryOpenStreetMap($simplifiedAddress);
            if ($coordinates[0] && $coordinates[1]) {
                Log::info("OpenStreetMap success with simplified address: {$coordinates[0]}, {$coordinates[1]}");
                return $coordinates;
            }
        }

        Log::warning("All geocoding methods failed for address: {$address}");
        return [null, null];
    }

    /** Simplify address for better geocoding results */
    protected static function simplifyAddress($fullAddress)
    {
        // Remove specific street addresses, keep city and region
        $patterns = [
            '/(\d+\s+)?([A-Za-z]+\s+)+(Street|St|Avenue|Ave|Road|Rd|Boulevard|Blvd|Drive|Dr|Lane|Ln)\s*,?/i',
            '/\b(?:Upper|Lower|North|South|East|West|Central)\s+/i',
            '/\b(?:Barangay|Brgy|Sitio|Purok)\s+\w+\s*,?/i'
        ];

        $simplified = $fullAddress;
        foreach ($patterns as $pattern) {
            $simplified = preg_replace($pattern, '', $simplified);
        }

        // Clean up extra commas and spaces
        $simplified = preg_replace('/\s*,\s*,/', ',', $simplified);
        $simplified = preg_replace('/^,\s*/', '', $simplified);
        $simplified = trim($simplified, ' ,');

        // If we have nothing left, return the original
        if (empty($simplified)) {
            return $fullAddress;
        }

        return $simplified;
    }

    /** Try OpenStreetMap Nominatim */
    protected static function tryOpenStreetMap($address)
    {
        try {
            $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address) . "&limit=1&countrycodes=ph";

            $response = Http::withHeaders([
                'User-Agent' => 'PCTVS-Service-System/1.0 (admin@pctvs.local)',
                'Accept' => 'application/json',
                'Referer' => config('app.url', 'http://localhost')
            ])
            ->timeout(15)
            ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    return [
                        floatval($data[0]['lat']),
                        floatval($data[0]['lon'])
                    ];
                }
            }

            Log::warning("OpenStreetMap no results for: {$address}");
            return [null, null];

        } catch (\Exception $e) {
            Log::error("OpenStreetMap geocoding failed: " . $e->getMessage());
            return [null, null];
        }
    }

    /** Manual geocoding method for existing records */
    public function geocodeAddress()
    {
        if (empty($this->address)) {
            Log::warning("No address to geocode for technician ID: {$this->id}");
            return false;
        }

        Log::info("Manual geocoding for technician {$this->id}: {$this->address}");

        [$lat, $lng] = self::getCoordinatesFromAddress($this->address);
        
        if ($lat && $lng) {
            $this->update([
                'latitude' => $lat,
                'longitude' => $lng
            ]);
            Log::info("Successfully geocoded technician {$this->id}: {$lat}, {$lng}");
            return true;
        }

        Log::warning("Failed to geocode technician {$this->id}: {$this->address}");
        return false;
    }

    /** Boot method to auto-generate technician ID and fetch coordinates */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($technician) {
            // Generate unique technician ID if not already set
            if (empty($technician->technician_id)) {
                $technician->technician_id = 'TECH-' . strtoupper(Str::random(6));
            }

            // Fetch latitude & longitude from address
            if (!empty($technician->address)) {
                Log::info("Geocoding new technician address: {$technician->address}");
                [$lat, $lng] = self::getCoordinatesFromAddress($technician->address);
                $technician->latitude = $lat;
                $technician->longitude = $lng;
                
                if ($lat && $lng) {
                    Log::info("Coordinates found for new technician: {$lat}, {$lng}");
                } else {
                    Log::warning("No coordinates found for new technician address: {$technician->address}");
                    // Set default coordinates for Philippines if geocoding fails
                    $technician->latitude = 12.8797;
                    $technician->longitude = 121.7740;
                }
            } else {
                // Set default coordinates if no address
                $technician->latitude = 12.8797;
                $technician->longitude = 121.7740;
            }
        });

        static::updating(function ($technician) {
            // Update latitude & longitude if address changed
            if ($technician->isDirty('address')) {
                if (!empty($technician->address)) {
                    Log::info("Geocoding updated address: {$technician->address}");
                    [$lat, $lng] = self::getCoordinatesFromAddress($technician->address);
                    $technician->latitude = $lat;
                    $technician->longitude = $lng;
                    
                    if ($lat && $lng) {
                        Log::info("Coordinates updated: {$lat}, {$lng}");
                    } else {
                        Log::warning("No coordinates found for updated address: {$technician->address}");
                        // Keep existing coordinates if geocoding fails
                        if (!$technician->latitude || !$technician->longitude) {
                            $technician->latitude = 12.8797;
                            $technician->longitude = 121.7740;
                        }
                    }
                } else {
                    // Set default coordinates if address is cleared
                    $technician->latitude = 12.8797;
                    $technician->longitude = 121.7740;
                }
            }
        });
    }
}