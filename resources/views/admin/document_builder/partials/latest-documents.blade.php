<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:28px 32px;margin-bottom:24px;">
    <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:6px;">
        <i class="fas fa-clock" style="color:#6366f1;margin-right:6px;"></i>
        Dokumen Terbaru
    </h2>
    <p style="color:#64748b;font-size:13px;margin-bottom:24px;">
        Daftar dokumen yang telah dibuat, diurutkan berdasarkan waktu pembuatan terbaru.
    </p>

    @if($latestDocuments->isEmpty())
        <div style="text-align:center;padding:40px 20px;color:#64748b;font-size:14px;">
            <i class="fas fa-file" style="font-size:32px;margin-bottom:12px;opacity:0.5;"></i><br>
            Belum ada dokumen yang dibuat.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover" style="font-size:13px;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th style="padding:12px;border-bottom:1px solid #e2e8f0;">Judul</th>
                        <th style="padding:12px;border-bottom:1px solid #e2e8f0;">Jenis</th>
                        <th style="padding:12px;border-bottom:1px solid #e2e8f0;">Status</th>
                        <th style="padding:12px;border-bottom:1px solid #e2e8f0;">Nomor</th>
                        <th style="padding:12px;border-bottom:1px solid #e2e8f0;">Dibuat</th>
                        <th style="padding:12px;border-bottom:1px solid #e2e8f0;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestDocuments as $document)
                    <tr>
                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                            <div style="font-weight:600;color:#0f172a;">{{ $document->nama_file }}</div>
                            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                                @if($document->event)
                                    {{ $document->event->nama_event }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                            @php
                                $typeClasses = [
                                    "proposal" => "badge-mendatang",
                                    "surat_kontrak" => "badge-aktif",
                                    "invoice" => "badge-selesai",
                                    "rab" => "badge-pending",
                                    "kontrak" => "badge-aktif",
                                    "laporan" => "badge-purple",
                                    "kwitansi" => "badge-selesai",
                                    "lainnya" => "badge-pending",
                                ];
                                $typeClass = $typeClasses[$document->tipe] ?? "badge-pending";
                            @endphp
                            <span class="badge {{ $typeClass }}" style="font-size:12px;padding:4px 8px;">
                                {{ $document->tipe_label }}
                            </span>
                        </td>
                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                            <x-document-status-badge :status="$document->status" />
                        </td>
                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-size:12px;color:#475569;">
                            @if($document->numbering)
                                {{ $document->numbering->document_number }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-size:12px;color:#475569;">
                            {{ $document->created_at->format("d M Y, H:i") }}
                        </td>
                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                            <a href="{{ route('admin.document_builder.preview', $document->id) }}" class="btn-action" style="background:#3b82f6;color:#fff;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($latestDocuments->hasPages())
            <div style="margin-top:20px;">
                {{ $latestDocuments->links() }}
            </div>
        @endif
    @endif
</div>
