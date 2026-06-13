<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Team;
use App\Models\ClientLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    // ─────────────── LAYANAN ───────────────
    public function services()
    {
        return view('admin.cms.services', [
            'services' => Service::orderBy('urutan')->get(),
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'icon'         => 'required|string|max:100',
            'deskripsi'    => 'required|string',
            'urutan'       => 'nullable|integer|min:0',
            'is_active'    => 'required|in:0,1',
        ]);

        Service::create($data);

        return redirect()->route('admin.cms.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'icon'         => 'required|string|max:100',
            'deskripsi'    => 'required|string',
            'urutan'       => 'nullable|integer|min:0',
            'is_active'    => 'required|in:0,1',
        ]);

        $service->update($data);

        return redirect()->route('admin.cms.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroyService(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.cms.index')->with('success', 'Layanan berhasil dihapus.');
    }

    // ─────────────── PORTFOLIO ───────────────
    public function portfolio()
    {
        return view('admin.cms.portfolio', [
            'portfolios' => Portfolio::latest()->get(),
        ]);
    }

    public function storePortfolio(Request $request)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'gambar'    => 'required|image|max:2048',
            'tips_file' => 'nullable|file|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $data['gambar'] = $request->file('gambar')->store('portfolio', 'public');
        if ($request->hasFile('tips_file')) {
            $data['tips_file'] = $request->file('tips_file')->store('portfolio/tips', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');

        Portfolio::create($data);

        return redirect()->route('admin.cms.portfolio')->with('success', 'Portfolio berhasil ditambahkan.');
    }

    public function updatePortfolio(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'gambar'    => 'nullable|image|max:2048',
            'tips_file' => 'nullable|file|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('gambar')) {
            if ($portfolio->gambar) {
                Storage::disk('public')->delete($portfolio->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('portfolio', 'public');
        }

        if ($request->hasFile('tips_file')) {
            if ($portfolio->tips_file) {
                Storage::disk('public')->delete($portfolio->tips_file);
            }
            $data['tips_file'] = $request->file('tips_file')->store('portfolio/tips', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $portfolio->update($data);

        return redirect()->route('admin.cms.portfolio')->with('success', 'Portfolio berhasil diperbarui.');
    }

    public function destroyPortfolio(Portfolio $portfolio)
    {
        if ($portfolio->gambar) {
            Storage::disk('public')->delete($portfolio->gambar);
        }
        if ($portfolio->tips_file) {
            Storage::disk('public')->delete($portfolio->tips_file);
        }
        $portfolio->delete();

        return redirect()->route('admin.cms.portfolio')->with('success', 'Portfolio berhasil dihapus.');
    }

    // ─────────────── TIM ───────────────
    public function team()
    {
        return view('admin.cms.team', [
            'teams' => Team::orderBy('urutan')->get(),
        ]);
    }

    public function storeTeam(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'foto'      => 'required|image|max:2048',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        $data['foto'] = $request->file('foto')->store('team', 'public');

        Team::create($data);

        return redirect()->route('admin.cms.team')->with('success', 'Anggota tim berhasil ditambahkan.');
    }

    public function updateTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'foto'      => 'nullable|image|max:2048',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        if ($request->hasFile('foto')) {
            if ($team->foto) {
                Storage::disk('public')->delete($team->foto);
            }
            $data['foto'] = $request->file('foto')->store('team', 'public');
        }

        $team->update($data);

        return redirect()->route('admin.cms.team')->with('success', 'Anggota tim berhasil diperbarui.');
    }

    public function destroyTeam(Team $team)
    {
        if ($team->foto) {
            Storage::disk('public')->delete($team->foto);
        }
        $team->delete();

        return redirect()->route('admin.cms.team')->with('success', 'Anggota tim berhasil dihapus.');
    }

    // ─────────────── LOGO KLIEN ───────────────
    public function clients()
    {
        return view('admin.cms.clients', [
            'clients' => ClientLogo::latest()->get(),
        ]);
    }

    public function storeClient(Request $request)
    {
        $data = $request->validate([
            'nama_client' => 'required|string|max:255',
            'logo'        => 'required|image|max:1024',
            'website'     => 'nullable|url|max:255',
            'status'      => 'required|in:partner,klien',
            'is_active'   => 'required|in:0,1',
        ]);

        $data['logo'] = $request->file('logo')->store('client-logos', 'public');

        ClientLogo::create($data);

        return redirect()->route('admin.cms.clients')->with('success', 'Logo klien berhasil ditambahkan.');
    }

    public function updateClient(Request $request, ClientLogo $client)
    {
        $data = $request->validate([
            'nama_client' => 'required|string|max:255',
            'logo'        => 'nullable|image|max:1024',
            'website'     => 'nullable|url|max:255',
            'status'      => 'required|in:partner,klien',
            'is_active'   => 'required|in:0,1',
        ]);

        if ($request->hasFile('logo')) {
            if ($client->logo) {
                Storage::disk('public')->delete($client->logo);
            }
            $data['logo'] = $request->file('logo')->store('client-logos', 'public');
        }

        $client->update($data);

        return redirect()->route('admin.cms.clients')->with('success', 'Logo klien berhasil diperbarui.');
    }

    public function destroyClient(ClientLogo $client)
    {
        if ($client->logo) {
            Storage::disk('public')->delete($client->logo);
        }
        $client->delete();

        return redirect()->route('admin.cms.clients')->with('success', 'Logo klien berhasil dihapus.');
    }
}