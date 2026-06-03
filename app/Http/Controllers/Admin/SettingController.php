<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $restaurant = Restaurant::find($this->getRestaurantId());

        return view('admin.settings.index', compact('restaurant'));
    }

    public function updateRestaurant(Request $request)
    {
        $restaurant = Restaurant::find($this->getRestaurantId());

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
        $request->validate([
            'opening_hours' => 'required|array',
            'opening_hours.*.open' => 'nullable|date_format:H:i',
            'opening_hours.*.close' => 'nullable|date_format:H:i',
        ]);

        $restaurant = Restaurant::find($this->getRestaurantId());
        $restaurant->update(['opening_hours' => $request->opening_hours]);

        return back()->with('success', 'Horaires mis à jour.');
    }

    private function getRestaurantId(): int
    {
        $user = auth()->user();
        
        if ($user->isAdmin() && request()->filled('restaurant_id')) {
            return request()->restaurant_id;
        }

        return $user->restaurants()->first()?->id ?? 1;
    }
}