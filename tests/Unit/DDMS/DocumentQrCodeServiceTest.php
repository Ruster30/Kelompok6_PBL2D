<?php

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentQrVerification;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use App\Services\DocumentQrCodeService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

function makeTestDocument(DocumentStatus $status, DocumentSource $source): Document
{
    $document = new Document([
        "status" => $status,
        "document_source" => $source,
    ]);
    $document->id = 42;

    return $document;
}

beforeEach(function () {
    Storage::fake("public");
    $this->qrRepo = mock(DocumentQrVerificationRepositoryInterface::class);
    $this->service = new DocumentQrCodeService($this->qrRepo);
});

it("throws when document is not published", function () {
    $document = makeTestDocument(DocumentStatus::Draft, DocumentSource::Generated);

    $this->service->getOrCreateQrCode($document);
})->throws(ValidationException::class);

it("throws when document source is not generated", function () {
    $document = makeTestDocument(DocumentStatus::Published, DocumentSource::Uploaded);

    $this->service->getOrCreateQrCode($document);
})->throws(ValidationException::class);

it("throws when verification token is missing", function () {
    $document = makeTestDocument(DocumentStatus::Published, DocumentSource::Generated);

    $this->qrRepo
        ->shouldReceive("findByDocument")
        ->with($document->id)
        ->once()
        ->andReturnNull();

    $this->service->getOrCreateQrCode($document);
})->throws(ValidationException::class);

it("returns existing qr path when file still exists", function () {
    $document = makeTestDocument(DocumentStatus::Published, DocumentSource::Generated);

    Storage::disk("public")->put("document-qr/existing-token.png", "mock");

    $qr = new DocumentQrVerification([
        "verification_token" => "existing-token",
        "qr_path" => "document-qr/existing-token.png",
    ]);

    $this->qrRepo
        ->shouldReceive("findByDocument")
        ->with($document->id)
        ->once()
        ->andReturn($qr);

    $result = $this->service->getOrCreateQrCode($document);

    expect($result)->toBe("document-qr/existing-token.png");
});

it("generates and stores qr png for a published document", function () {
    $document = makeTestDocument(DocumentStatus::Published, DocumentSource::Generated);

    $token = "a1b2c3d4-e5f6-7890-abcd-ef1234567890";
    $qr = new DocumentQrVerification([
        "verification_token" => $token,
        "qr_path" => null,
    ]);

    $this->qrRepo
        ->shouldReceive("findByDocument")
        ->with($document->id)
        ->once()
        ->andReturn($qr);

    $this->qrRepo
        ->shouldReceive("update")
        ->once()
        ->andReturnUsing(function (DocumentQrVerification $model, array $data) {
            $model->qr_path = $data["qr_path"];

            return $model;
        });

    $result = $this->service->getOrCreateQrCode($document);

    expect($result)->toBe("document-qr/" . $token . ".png");
    Storage::disk("public")->assertExists($result);

    $png = Storage::disk("public")->get($result);
    expect(substr($png, 1, 3))->toBe("PNG");
});