<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\OperatingHour;
use App\Models\SpecialDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // public function index()
    // {
    //     $restaurant = Restaurant::find($this->getRestaurantId());

    //     return view('admin.settings.index', compact('restaurant'));
    // }

    public function index()
    {
        $restaurant = $this->getRestaurant();
        
        if (!$restaurant) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Aucun restaurant trouvé.');
        }

        $restaurant->load(['operatingHours', 'specialDays']);
        $days = OperatingHour::DAYS;

        return view('admin.settings.index', compact('restaurant', 'days'));
    }

    public function updateRestaurant(Request $request)
    {
        $restaurant = $this->getRestaurant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:2048',
            'minimum_order' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'estimated_delivery_time' => 'required|integer|min:1',
            'accepts_delivery' => 'boolean',
            'accepts_takeaway' => 'boolean',
            'accepts_dine_in' => 'boolean',
        ]);

        $validated['accepts_delivery'] = $request->boolean('accepts_delivery');
        $validated['accepts_takeaway'] = $request->boolean('accepts_takeaway');
        $validated['accepts_dine_in'] = $request->boolean('accepts_dine_in');

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('restaurants', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($restaurant->cover_image) {
                Storage::disk('public')->delete($restaurant->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        return back()->with('success', 'Paramètres mis à jour.');
    }

    public function updateHours(Request $request)
    {
        // $request->validate([
        //     'opening_hours' => 'required|array',
        //     'opening_hours.*.open' => 'nullable|date_format:H:i',
        //     'opening_hours.*.close' => 'nullable|date_format:H:i',
        // ]);

        // $restaurant = Restaurant::find($this->getRestaurantId());
        // $restaurant->update(['opening_hours' => $request->opening_hours]);

        // return back()->with('success', 'Horaires mis à jour.');
        $restaurant = $this->getRestaurant();

        $request->validate([
            'hours' => 'required|array',
            'hours.*.day' => 'required|in:' . implode(',', array_keys(OperatingHour::DAYS)),
            'hours.*.open_time' => 'nullable|date_format:H:i',
            'hours.*.close_time' => 'nullable|date_format:H:i',
            'hours.*.is_closed' => 'boolean',
        ]);

        foreach ($request->hours as $hourData) {
            OperatingHour::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'day' => $hourData['day'],
                ],
                [
                    'open_time' => $hourData['open_time'] ?? '09:00',
                    'close_time' => $hourData['close_time'] ?? '22:00',
                    'is_closed' => $hourData['is_closed'] ?? false,
                ]
            );
        }

        return back()->with('success', 'Horaires mis à jour avec succès.');
    }

    public function addSpecialDay(Request $request)
    {
        $restaurant = $this->getRestaurant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'is_closed' => 'boolean',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        $restaurant->specialDays()->create($validated);

        return back()->with('success', 'Jour spécial ajouté.');
    }

    public function removeSpecialDay(SpecialDay $specialDay)
    {
        $specialDay->delete();
        return back()->with('success', 'Jour spécial supprimé.');
    }

    private function getRestaurant(): ?Restaurant
    {
        $user = auth()->user();
        if ($user->isAdmin() && request()->filled('restaurant_id')) {
            return Restaurant::find(request()->restaurant_id);
        }
        return $user->restaurants()->first();
    }
}