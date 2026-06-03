<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()
            ->with(['items.product', 'restaurant'])
            ->latest()
            ->take(10)
            ->get();

        $addresses = $user->addresses;
        $favorites = $user->favorites()->with('product.restaurant')->get();
        $loyaltyPoints = $user->getLoyaltyBalance();

        return view('profile.index', compact('user', 'orders', 'addresses', 'favorites', 'loyaltyPoints'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // 'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'preferences' => 'nullable|array',
        ]);

        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    public function addAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'street_address' => 'required|string|max:500',
            'apartment' => 'nullable|string|max:100',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:2',
            'instructions' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($validated);

        return back()->with('success', 'Adresse ajoutée avec succès.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'street_address' => 'required|string|max:500',
            'apartment' => 'nullable|string|max:100',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:2',
            'instructions' => 'nullable|string|max:500',
            'is_default' => 'boolean',  
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Adresse mise à jour.');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorize('delete', $address);

        $wasDefault = $address->is_default;
        $address->delete();

        // Si c'était l'adresse par défaut, définir une nouvelle adresse par défaut
        if ($wasDefault) {
            $newDefault = auth()->user()->addresses()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Adresse supprimée.');
    }

    public function orders()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'restaurant', 'review'])
            ->latest()
            ->paginate(10);

        return view('profile.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'items.product',
            'items.options',
            'statusHistory',
            'deliveryTracking',
            'review',
            'restaurant',
        ]);

        return view('profile.order-detail', compact('order'));
    }

    public function favorites()
    {
        $favorites = auth()->user()->favorites()
            ->with('product.restaurant')
            ->paginate(12);

        return view('profile.favorites', compact('favorites'));
    }

    public function toggleFavorite(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $favorite = auth()->user()->favorites()
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            auth()->user()->favorites()->create([
                'product_id' => $validated['product_id'],
            ]);
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Ajouté aux favoris.' : 'Retiré des favoris.',
        ]);
    }

    public function loyalty()
    {
        $user = auth()->user();
        $points = $user->getLoyaltyBalance();
        $history = $user->loyaltyPoints()->latest()->paginate(20);
        $redemptions = $user->loyaltyRedemptions()->with('reward')->latest()->get();

        return view('profile.loyalty', compact('points', 'history', 'redemptions'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
