<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Team;
use App\Services\CmsService;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function __construct(
        private CmsService $cmsService
    ) {}

    // ── Services ────────────────────────────────────────────

    public function services()
    {
        $data = $this->cmsService->getServicesData();

        return view("admin.cms.services", $data);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            "nama_layanan" => "required|string|max:255",
            "icon"         => "required|string|max:100",
            "deskripsi"    => "required|string",
            "urutan"       => "nullable|integer|min:0",
            "is_active"    => "required|in:0,1",
        ]);

        $this->cmsService->storeService($data);

        return redirect()->route("admin.cms.index")->with("success", "Layanan berhasil ditambahkan.");
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $request->validate([
            "nama_layanan" => "required|string|max:255",
            "icon"         => "required|string|max:100",
            "deskripsi"    => "required|string",
            "urutan"       => "nullable|integer|min:0",
            "is_active"    => "required|in:0,1",
        ]);

        $this->cmsService->updateService($service, $data);

        return redirect()->route("admin.cms.index")->with("success", "Layanan berhasil diperbarui.");
    }

    public function destroyService(Service $service)
    {
        $this->cmsService->deleteService($service);

        return redirect()->route("admin.cms.index")->with("success", "Layanan berhasil dihapus.");
    }

    // ── Portfolio ───────────────────────────────────────────

    public function portfolio()
    {
        $data = $this->cmsService->getPortfolioData();

        return view("admin.cms.portfolio", $data);
    }

    public function storePortfolio(Request $request)
    {
        $data = $request->validate([
            "judul"     => "required|string|max:255",
            "kategori"  => "required|string|max:100",
            "gambar"    => "required|image|mimes:jpg,jpeg,png|max:2048",
            "tips_file" => "nullable|file|max:5120",
            "is_active" => "nullable|boolean",
        ], [
            "gambar.mimes" => "Format gambar harus JPG, JPEG, atau PNG.",
        ]);

        $this->cmsService->storePortfolio(
            $data,
            $request->file("gambar"),
            $request->file("tips_file")
        );

        return redirect()->route("admin.cms.portfolio")->with("success", "Portfolio berhasil ditambahkan.");
    }

    public function updatePortfolio(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            "judul"     => "required|string|max:255",
            "kategori"  => "required|string|max:100",
            "gambar"    => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            "tips_file" => "nullable|file|max:5120",
            "is_active" => "nullable|boolean",
        ]);

        $this->cmsService->updatePortfolio(
            $portfolio,
            $data,
            $request->file("gambar"),
            $request->file("tips_file")
        );

        return redirect()->route("admin.cms.portfolio")->with("success", "Portfolio berhasil diperbarui.");
    }

    public function destroyPortfolio(Portfolio $portfolio)
    {
        $this->cmsService->deletePortfolio($portfolio);

        return redirect()->route("admin.cms.portfolio")->with("success", "Portfolio berhasil dihapus.");
    }

    // ── Team ────────────────────────────────────────────────

    public function team()
    {
        $data = $this->cmsService->getTeamData();

        return view("admin.cms.team", $data);
    }

    public function storeTeam(Request $request)
    {
        $data = $request->validate([
            "nama"      => "required|string|max:255",
            "jabatan"   => "required|string|max:255",
            "foto"      => "required|image|mimes:jpg,jpeg,png|max:2048",
            "deskripsi" => "nullable|string",
            "urutan"    => "nullable|integer|min:0",
            "is_active" => "required|in:0,1",
        ], [
            "foto.mimes" => "Format gambar harus JPG, JPEG, atau PNG.",
        ]);

        $this->cmsService->storeTeam($data, $request->file("foto"));

        return redirect()->route("admin.cms.team")->with("success", "Anggota tim berhasil ditambahkan.");
    }

    public function updateTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            "nama"      => "required|string|max:255",
            "jabatan"   => "required|string|max:255",
            "foto"      => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            "urutan"    => "nullable|integer|min:0",
            "is_active" => "required|in:0,1",
        ]);

        $this->cmsService->updateTeam($team, $data, $request->file("foto"));

        return redirect()->route("admin.cms.team")->with("success", "Anggota tim berhasil diperbarui.");
    }

    public function destroyTeam(Team $team)
    {
        $this->cmsService->deleteTeam($team);

        return redirect()->route("admin.cms.team")->with("success", "Anggota tim berhasil dihapus.");
    }

    // ── Clients ─────────────────────────────────────────────

    public function clients()
    {
        $data = $this->cmsService->getClientsData();

        return view("admin.cms.clients", $data);
    }

    public function storeClient(Request $request)
    {
        $data = $request->validate([
            "nama_client" => "required|string|max:255",
            "logo"        => "required|image|mimes:jpg,jpeg,png|max:1024",
            "website"     => "nullable|url|max:255",
            "is_active"   => "required|in:0,1",
        ], [
            "logo.mimes" => "Logo harus berformat JPG, JPEG, atau PNG.",
        ]);

        $this->cmsService->storeClient($data, $request->file("logo"));

        return redirect()->route("admin.cms.clients")->with("success", "Logo klien berhasil ditambahkan.");
    }

    public function updateClient(Request $request, Client $client)
    {
        $data = $request->validate([
            "nama_client" => "required|string|max:255",
            "logo"        => "nullable|image|mimes:jpg,jpeg,png|max:1024",
            "website"     => "nullable|url|max:255",
            "is_active"   => "required|in:0,1",
        ]);

        $this->cmsService->updateClient($client, $data, $request->file("logo"));

        return redirect()->route("admin.cms.clients")->with("success", "Logo klien berhasil diperbarui.");
    }

    public function destroyClient(Client $client)
    {
        $this->cmsService->deleteClient($client);

        return redirect()->route("admin.cms.clients")->with("success", "Logo klien berhasil dihapus.");
    }
}