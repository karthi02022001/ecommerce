<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Address;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page
     */
    public function index()
    {
        $user = auth()->user();

        // Get user's addresses
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return view('profile.index', compact('user', 'addresses'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user->update($validated);

            return redirect()->back()
                ->with('success', __('Profile updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating profile: ') . $e->getMessage());
        }
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => __('The current password is incorrect.')
                ]);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return redirect()->back()
                ->with('success', __('Password updated successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error updating password: ') . $e->getMessage());
        }
    }

    /**
     * Store a new address
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'address_type' => 'required|in:shipping,billing',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        try {
            $validated['customer_id'] = auth()->id();

            $address = Address::create($validated);

            if ($request->has('is_default') && $request->is_default) {
                $address->setAsDefault();
            }

            return redirect()->back()
                ->with('success', __('Address added successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error adding address: ') . $e->getMessage());
        }
    }

    /**
     * Update an existing address
     */
    public function updateAddress(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        // Verify ownership
        if ($address->customer_id !== auth()->id()) {
            abort(403, __('Unauthorized action.'));
        }

        $validated = $request->validate([
            'address_type' => 'required|in:shipping,billing',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        try {
            $address->update($validated);

            if ($request->has('is_default') && $request->is_default) {
                $address->setAsDefault();
            }

            return redirect()->back()
                ->with('success', __('Address updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating address: ') . $e->getMessage());
        }
    }

    /**
     * Delete an address
     */
    public function deleteAddress($id)
    {
        $address = Address::findOrFail($id);

        // Verify ownership
        if ($address->customer_id !== auth()->id()) {
            abort(403, __('Unauthorized action.'));
        }

        try {
            $address->delete();

            return redirect()->back()
                ->with('success', __('Address deleted successfully!'));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('Error deleting address: ') . $e->getMessage());
        }
    }
}
