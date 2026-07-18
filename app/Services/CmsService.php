<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Team;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CmsService
{
    // ── Services ────────────────────────────────────────────

    public function getServicesData(): array
    {
        return ["services" => Service::orderBy("urutan")->get()];
    }

    public function storeService(array $data): void
    {
        Service::create($data);
    }

    public function updateService(Service $service, array $data): void
    {
        $service->update($data);
    }

    public function deleteService(Service $service): void
    {
        $service->delete();
    }

    // ── Portfolio ───────────────────────────────────────────

    public function getPortfolioData(): array
    {
        return [
            "portfolios" => Portfolio::latest()->get(),
            "events"     => \App\Models\Event::orderBy("nama_event")->get(),
        ];
    }

    public function storePortfolio(array $data, ?UploadedFile $gambar, ?UploadedFile $tipsFile): void
    {
        if ($gambar) {
            $data["gambar"] = $this->uploadPublicImage($gambar, "images/landing/portofolio");
        }

        if ($tipsFile) {
            $data["tips_file"] = $tipsFile->store("portfolio/tips", "public");
        }

        $data["is_active"] = (bool) ($data["is_active"] ?? false);

        Portfolio::create($data);
    }

    public function updatePortfolio(Portfolio $portfolio, array $data, ?UploadedFile $gambar, ?UploadedFile $tipsFile): void
    {
        if ($gambar) {
            $this->deletePublicFile($portfolio->gambar, "images/landing/portofolio");
            $data["gambar"] = $this->uploadPublicImage($gambar, "images/landing/portofolio");
        }

        if ($tipsFile) {
            if ($portfolio->tips_file) {
                Storage::disk("public")->delete($portfolio->tips_file);
            }
            $data["tips_file"] = $tipsFile->store("portfolio/tips", "public");
        }

        $data["is_active"] = (bool) ($data["is_active"] ?? false);

        $portfolio->update($data);
    }

    public function deletePortfolio(Portfolio $portfolio): void
    {
        $this->deletePublicFile($portfolio->gambar, "images/landing/portofolio");
        if ($portfolio->tips_file) {
            Storage::disk("public")->delete($portfolio->tips_file);
        }
        $portfolio->delete();
    }

    // ── Team ────────────────────────────────────────────────

    public function getTeamData(): array
    {
        return ["teams" => Team::orderBy("urutan")->get()];
    }

    public function storeTeam(array $data, ?UploadedFile $foto): void
    {
        if ($foto) {
            $data["foto"] = $this->uploadPublicImage($foto, "images/landing/team");
        }

        Team::create($data);
    }

    public function updateTeam(Team $team, array $data, ?UploadedFile $foto): void
    {
        if ($foto) {
            $this->deletePublicFile($team->foto, "images/landing/team");
            $data["foto"] = $this->uploadPublicImage($foto, "images/landing/team");
        }

        $team->update($data);
    }

    public function deleteTeam(Team $team): void
    {
        $this->deletePublicFile($team->foto, "images/landing/team");
        $team->delete();
    }

    // ── Clients ─────────────────────────────────────────────

    public function getClientsData(): array
    {
        return ["clients" => Client::latest()->get()];
    }

    public function storeClient(array $data, UploadedFile $logo): void
    {
        $data["logo"] = $this->uploadPublicImage($logo, "images/landing/clients");
        Client::create($data);
    }

    public function updateClient(Client $client, array $data, ?UploadedFile $logo): void
    {
        if ($logo) {
            $this->deletePublicFile($client->logo, "images/landing/clients");
            $data["logo"] = $this->uploadPublicImage($logo, "images/landing/clients");
        }

        $client->update($data);
    }

    public function deleteClient(Client $client): void
    {
        $this->deletePublicFile($client->logo, "images/landing/clients");
        $client->delete();
    }

    // ── Helpers ──────────────────────────────────────────────

    private function uploadPublicImage(UploadedFile $file, string $directory): string
    {
        $filename = time() . "_" . $file->getClientOriginalName();
        $file->move(public_path($directory), $filename);
        return $filename;
    }

    private function deletePublicFile(?string $filename, string $directory): void
    {
        if ($filename && file_exists(public_path($directory . "/" . $filename))) {
            unlink(public_path($directory . "/" . $filename));
        }
    }
}