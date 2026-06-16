<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            'email'        => 'nullable|email|unique:users,email',
            'password'     => 'nullable|string|min:8',
        ]);

        $userId = null;
        if (!empty($data['email']) && !empty($data['password'])) {
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
            'alamat'       => $data['alamat'] ?? null,
            'deskripsi'    => $data['deskripsi'] ?? null,
        ]);

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
                'nullable', 'email',
                Rule::unique('users', 'email')->ignore($vendor->user_id),
            ],
            'password'     => 'nullable|string|min:8',
        ]);

        $vendor->update([
            'nama_vendor'  => $data['nama_vendor'],
            'jenis_vendor' => $data['jenis_vendor'] ?? null,
            'alamat'       => $data['alamat'] ?? null,
            'deskripsi'    => $data['deskripsi'] ?? null,
        ]);

        // Jika belum punya akun dan email+password diisi, buatkan akun
        if (!$vendor->user_id && !empty($data['email']) && !empty($data['password'])) {
            $user = User::create([
                'name'     => $data['nama_vendor'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'vendor',
            ]);
            $vendor->update(['user_id' => $user->id]);
        } elseif ($vendor->user_id && !empty($data['password'])) {
            // update password akun yang sudah ada
            $vendor->user->update(['password' => Hash::make($data['password'])]);
        }

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil dihapus.');
    }
}