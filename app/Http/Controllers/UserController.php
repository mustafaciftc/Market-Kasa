<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BankAccount;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::whereNull('deleted_at')->get();
		$bankAccounts = BankAccount::all();
        return view('kullaniciyonetimi', compact('users', 'bankAccounts'));
}

    /**
     * Store a newly created user in storage via AJAX.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:20|unique:users',
                'email' => 'required|email|max:65|unique:users',
                'phone' => 'nullable|string|max:255',
                'active' => 'required|boolean',
                'role' => 'required|in:' . implode(',', User::getValidRoles()),
            ]);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make('default123'),
                'phone' => $request->phone,
                'active' => $request->active,
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla eklendi.',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return user data for editing via AJAX.
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'active' => $user->active ? 1 : 0,
                    'role' => $user->role,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('User edit fetch failed', ['user_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bilgileri alınırken bir hata oluştu: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:20|unique:users,username,' . $id,
                'email' => 'required|email|max:65|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:255',
                'active' => 'required|boolean',
                'role' => 'required|in:' . implode(',', User::getValidRoles()),
            ]);

            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'active' => $request->active,
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla güncellendi.',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('User update failed', ['user_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete the specified user.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            Log::info('User deleted', ['user_id' => $id]);
            return redirect()->route('kullaniciyonetimi')->with('success', 'Kullanıcı başarıyla silindi.');
        } catch (\Exception $e) {
            Log::error('User deletion failed', ['user_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('kullaniciyonetimi')->with('error', 'Kullanıcı silinirken bir hata oluştu.');
        }
    }

    /**
     * Show and update profile.
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:20|unique:users,username,' . $user->id,
                'email' => 'required|email|max:65|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'phone' => 'nullable|string|max:255',
                'website' => 'nullable|string|max:60',
                'company' => 'nullable|string|max:60',
                'role' => 'sometimes|in:' . implode(',', User::getValidRoles()),
            ]);

            if ($request->filled('current_password') && $request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'Mevcut şifreniz hatalı.']);
                }
                $user->password = Hash::make($request->new_password);
            }

            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'phone' => $request->phone,
                'website' => $request->website,
                'company' => $request->company,
                'role' => $request->has('role') ? $request->role : $user->role,
            ]);

            return redirect()->route('profil')->with('success', 'Profil bilgileriniz başarıyla güncellendi.');
        } catch (\Exception $e) {
            Log::error('Profile update failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Profil güncellenirken bir hata oluştu.');
        }
    }
}