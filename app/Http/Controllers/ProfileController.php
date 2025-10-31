<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user profile page
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's addresses
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
        
        return view('profile.index', compact('user', 'addresses'));
    }
    
    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        $user->update($validated);
        
        return back()->with('success', __('Profile updated successfully.'));
    }
    
    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return back()->with('success', __('Password updated successfully.'));
    }
    
    /**
     * Store a new address
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'address_type' => ['required', 'in:billing,shipping'],
            'street_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'is_default' => ['boolean'],
        ]);
        
        $user = auth()->user();
        
        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }
        
        $user->addresses()->create($validated);
        
        return back()->with('success', __('Address added successfully.'));
    }
    
    /**
     * Update an existing address
     */
    public function updateAddress(Request $request, $id)
    {
        $user = auth()->user();
        $address = $user->addresses()->findOrFail($id);
        
        $validated = $request->validate([
            'address_type' => ['required', 'in:billing,shipping'],
            'street_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'is_default' => ['boolean'],
        ]);
        
        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }
        
        $address->update($validated);
        
        return back()->with('success', __('Address updated successfully.'));
    }
    
    /**
     * Delete an address
     */
    public function deleteAddress($id)
    {
        $user = auth()->user();
        $address = $user->addresses()->findOrFail($id);
        
        $address->delete();
        
        return back()->with('success', __('Address deleted successfully.'));
    }
}
