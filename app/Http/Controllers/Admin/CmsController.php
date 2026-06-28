<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Team;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;

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
            'events' => Event::orderBy('nama_event')->get(),
        ]);
    }

   public function storePortfolio(Request $request)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'tips_file' => 'nullable|file|max:5120',
            'is_active' => 'nullable|boolean',
        ],
        [
            'gambar.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
        ]);

        // Upload gambar ke public/images/landing/portofolio
        if ($request->hasFile('gambar')) {

        $file = $request->file('gambar');

        $namaFile = time() . '.' . $file->getClientOriginalExtension();

        $file->move(
            public_path('images/landing/portofolio'),
            $namaFile
        );

        $data['gambar'] = $namaFile;
        }

        // Upload tips file
        if ($request->hasFile('tips_file')) {
            $data['tips_file'] = $request->file('tips_file')->store('portfolio/tips', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        Portfolio::create($data);

        return redirect()->route('admin.cms.portfolio')
            ->with('success', 'Portfolio berhasil ditambahkan.');
    }

    public function updatePortfolio(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tips_file' => 'nullable|file|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('gambar')) {

            if ($portfolio->gambar) {

                $old = public_path('images/landing/portofolio/'.$portfolio->gambar);

                if(file_exists($old)){
                    unlink($old);
                }
            }

            $file = $request->file('gambar');

            $namaFile = time().'.'.$file->getClientOriginalExtension();

            $file->move(
                public_path('images/landing/portofolio'),
                $namaFile
            );

            $data['gambar'] = $namaFile;
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
        if($portfolio->gambar){

        $old = public_path('images/landing/portofolio/'.$portfolio->gambar);

            if(file_exists($old)){
                unlink($old);
            }

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
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi' => 'nullable|string',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
        ],
        [
            'foto.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
        ]);

        if ($request->hasFile('foto')) {

            $file = $request->file('foto');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('images/landing/team'), $filename);

            $data['foto'] = $filename;
        }

        Team::create($data);

        return redirect()->route('admin.cms.team')->with('success', 'Anggota tim berhasil ditambahkan.');
    }

    public function updateTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        if ($request->hasFile('foto')) {
            if (
                $team->foto &&
                file_exists(public_path('images/landing/team/'.$team->foto))
            ) {

                unlink(public_path('images/landing/team/'.$team->foto));

            }

            $file = $request->file('foto');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('images/landing/team'), $filename);

            $data['foto'] = $filename;
        }

        $team->update($data);

        return redirect()->route('admin.cms.team')->with('success', 'Anggota tim berhasil diperbarui.');
    }

    public function destroyTeam(Team $team)
    {
        if (
            $team->foto &&
            file_exists(public_path('images/landing/team/'.$team->foto))
        ) {
            unlink(public_path('images/landing/team/'.$team->foto));
        }
        $team->delete();

        return redirect()->route('admin.cms.team')->with('success', 'Anggota tim berhasil dihapus.');
    }

    // ─────────────── LOGO KLIEN ───────────────
    public function clients()
    {
        return view('admin.cms.clients', [
            'clients' => Client::latest()->get(),
        ]);
    }

    public function storeClient(Request $request)
    {
        $data = $request->validate([
            'nama_client' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:1024',
            'website'     => 'nullable|url|max:255',
            'is_active'   => 'required|in:0,1',
        ],
        [
            'logo.mimes' => 'Logo harus berformat JPG, JPEG, atau PNG.',
        ]);

        $file = $request->file('logo');

        $filename = time().'_'.$file->getClientOriginalName();

        $file->move(
            public_path('images/landing/clients'),
            $filename
        );

        $data['logo'] = $filename;

        Client::create($data);

        return redirect()->route('admin.cms.clients')->with('success', 'Logo klien berhasil ditambahkan.');
    }

    public function updateClient(Request $request, Client $client)
    {
        $data = $request->validate([
            'nama_client' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'website'     => 'nullable|url|max:255',
            'is_active'   => 'required|in:0,1',
        ]);

        if ($request->hasFile('logo')) {
            if (
                $client->logo &&
                file_exists(public_path('images/landing/clients/'.$client->logo))
            ) {
                unlink(public_path('images/landing/clients/'.$client->logo));
            }

            $file = $request->file('logo');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(
                public_path('images/landing/clients'),
                $filename
            );

            $data['logo'] = $filename;
        }

        $client->update($data);

        return redirect()->route('admin.cms.clients')->with('success', 'Logo klien berhasil diperbarui.');
    }

    public function destroyClient(ClientLogo $client)
    {
        if (
            $client->logo &&
            file_exists(public_path('images/landing/clients/'.$client->logo))
        ) {

            unlink(public_path('images/landing/clients/'.$client->logo));

        }
        $client->delete();

        return redirect()->route('admin.cms.clients')->with('success', 'Logo klien berhasil dihapus.');
    }
}