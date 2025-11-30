<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Staff;
use App\Models\Transaction;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Check if user is staff (driver, medical_staff, or manager)
        $staff = Staff::where('user_id', $user->id)->first();
        $earnings = null;
        
        if ($staff && in_array($staff->staff_type, ['driver', 'medical_staff', 'manager'])) {
            // Get earnings for this staff member
            $earnings = Transaction::where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->with(['incident.vehicle', 'incident.patient'])
                ->orderBy('date', 'desc')
                ->paginate(20);
        }
        
        return view('profile.edit', [
            'user' => $user,
            'staff' => $staff,
            'earnings' => $earnings,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
