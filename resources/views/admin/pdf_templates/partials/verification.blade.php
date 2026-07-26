@if(isset($document) && $document && optional($document->numbering)->document_number)
<div style="page-break-before: always;"></div>
<div style="padding: 30px; border: 2px solid #22c55e; border-radius: 8px; margin: 20px; text-align: center; font-family: DejaVu Sans, sans-serif;">

    <h3 style="font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 16px;">VERIFIKASI DOKUMEN ELEKTRONIK</h3>

    <div style="font-size: 20px; font-weight: 700; color: #16a34a; text-transform: uppercase; border: 3px solid #16a34a; display: inline-block; padding: 6px 24px; border-radius: 6px; margin-bottom: 20px;">APPROVED</div>

    <table style="width: 80%; margin: 0 auto; border-collapse: collapse; font-size: 11px;">
        <tr><td style="padding: 5px; text-align: right; width: 40%; color: #64748b;">Nomor Dokumen</td>
            <td style="padding: 5px; text-align: left; font-weight: 700;">{{ $document->numbering->document_number }}</td></tr>
        <tr><td style="padding: 5px; text-align: right; color: #64748b;">Status</td>
            <td style="padding: 5px; text-align: left; font-weight: 700; color: #16a34a;">APPROVED</td></tr>
        @php
            $latestApproval = $document->approvals?->firstWhere('status', 'approved');
        @endphp
        @if($latestApproval && $latestApproval->approvedBy)
        <tr><td style="padding: 5px; text-align: right; color: #64748b;">Disetujui Oleh</td>
            <td style="padding: 5px; text-align: left; font-weight: 600;">{{ $latestApproval->approvedBy->name }}</td></tr>
        <tr><td style="padding: 5px; text-align: right; color: #64748b;">Jabatan</td>
            <td style="padding: 5px; text-align: left;">Director</td></tr>
        @endif
        @if($latestApproval && $latestApproval->reviewed_at)
        <tr><td style="padding: 5px; text-align: right; color: #64748b;">Tanggal Approval</td>
            <td style="padding: 5px; text-align: left;">{{ $latestApproval->reviewed_at->format('d F Y') }}</td></tr>
        @endif
        <tr><td style="padding: 5px; text-align: right; color: #64748b;">Tanggal Terbit</td>
            <td style="padding: 5px; text-align: left;">{{ now()->format('d F Y') }}</td></tr>
    </table>

    @if(optional($document->qrVerification)->qr_path)
    <div style="margin-top: 20px;">
        <img src="{{ storage_path('app/public/' . $document->qrVerification->qr_path) }}" style="width: 150px; height: 150px;" alt="QR Code">
        <p style="font-size: 9px; color: #94a3b8; margin-top: 6px; margin-bottom: 2px;">Scan QR untuk memverifikasi keaslian dokumen</p>
        <p style="font-size: 8px; color: #94a3b8; word-break: break-all;">{{ url('/verify/' . $document->qrVerification->verification_token) }}</p>
    </div>
    @endif

    <p style="font-size: 9px; color: #94a3b8; margin-top: 20px; line-height: 1.5;">
        Dokumen ini diterbitkan secara elektronik melalui DDMS (Document Draft Management System) CV. Alpha Multi Organizer.<br>
        Keaslian dokumen dapat diverifikasi melalui QR Code atau URL di atas.
    </p>
</div>
@endif