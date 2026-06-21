<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('user')
        ->withCount([
            'eventVendors as active_jobs_count' => function ($q) {
                $q->whereIn('status_vendor', [
                    'ditugaskan',
                    'dikerjakan'
                ]);
            }
        ])
        ->latest();

        if ($request->search) {
            $query->where('nama_vendor', 'like', '%' . $request->search . '%');
        }

        $vendors = $query->paginate(10)->withQueryString();

        return view('admin.vendor.index', [
            'vendors'       => $vendors,
            'totalVendors'  => Vendor::count(),
            'activeVendors' => Vendor::whereNotNull('user_id')->count(),
            'busyVendors'   => Vendor::whereHas('eventVendors', function ($q) {
                $q->whereIn('status_vendor', [
                    'ditugaskan',
                    'dikerjakan'
                ]);
            })->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_vendor'  => 'required|string|max:255',
            'jenis_vendor' => 'nullable|string|max:100',
            'alamat'       => 'nullable|string',
            'deskripsi'    => 'nullable|string',
            'email'        => 'nullable|email|max:255|unique:vendors,email|required_with:password',
            'password'     => 'nullable|string|min:8',
        ]);

        if (!empty($data['password']) && User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email tersebut sudah digunakan oleh akun lain.',
            ]);
        }

        DB::transaction(function () use ($data) {
            $userId = null;

            if (!empty($data['password'])) {
                $user = User::create([
                    'name'     => $data['nama_vendor'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role'     => 'vendor',
                ]);
                $userId = $user->id;
            }

            Vendor::create([
                'user_id'      => $userId,
                'nama_vendor'  => $data['nama_vendor'],
                'jenis_vendor' => $data['jenis_vendor'] ?? null,
                'email'        => $data['email'] ?? null,
                'alamat'       => $data['alamat'] ?? null,
                'deskripsi'    => $data['deskripsi'] ?? null,
            ]);
        });

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'nama_vendor'  => 'required|string|max:255',
            'jenis_vendor' => 'nullable|string|max:100',
            'alamat'       => 'nullable|string',
            'deskripsi'    => 'nullable|string',
            'email' => [
                'nullable', 'email', 'max:255', 'required_with:password',
                Rule::unique('vendors', 'email')->ignore($vendor->id),
            ],
            'password'     => 'nullable|string|min:8',
        ]);

        $vendor->update([
            'nama_vendor'  => $data['nama_vendor'],
            'jenis_vendor' => $data['jenis_vendor'] ?? null,
            'email'        => $data['email'] ?? null,
            'alamat'       => $data['alamat'] ?? null,
            'deskripsi'    => $data['deskripsi'] ?? null,
        ]);

        // Jika belum punya akun dan email+password diisi, buatkan akun
        if (!$vendor->user_id && !empty($data['password'])) {
            if (User::where('email', $data['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Email tersebut sudah digunakan oleh akun lain.',
                ]);
            }

            $user = User::create([
                'name'     => $data['nama_vendor'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'vendor',
            ]);
            $vendor->update(['user_id' => $user->id]);
        } elseif ($vendor->user_id) {
            $account = $vendor->user;

            if (!empty($data['email']) && $data['email'] !== $account->email) {
                if (User::where('email', $data['email'])->where('id', '!=', $account->id)->exists()) {
                    throw ValidationException::withMessages([
                        'email' => 'Email tersebut sudah digunakan oleh akun lain.',
                    ]);
                }

                $account->email = $data['email'];
            }

            $account->name = $data['nama_vendor'];
            if (!empty($data['password'])) {
                $account->password = Hash::make($data['password']);
            }
            $account->save();
        }

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
