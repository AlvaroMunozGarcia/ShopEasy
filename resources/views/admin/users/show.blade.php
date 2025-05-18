@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles del Usuario')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Usuario: <span id="userNameShow">{{ $user->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9" id="userId">{{ $user->id }}</dd>

                    <dt class="col-sm-3">Nombre:</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd> {{-- Ya capturado en userNameShow --}}

                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9" id="userEmail">{{ $user->email }}</dd>

                    <dt class="col-sm-3">Roles:</dt>
                    <dd class="col-sm-9" id="userRoles">
                        @forelse ($user->roles as $role)
                            <span class="badge bg-info text-dark me-1">{{ $role->name }}</span>
                        @empty
                            <span>Sin roles asignados.</span>
                        @endforelse
                    </dd>

                    <dt class="col-sm-3">Fecha de Creación:</dt>
                    <dd class="col-sm-9" id="userCreatedAt">{{ $user->created_at->format('d/m/Y H:i:s') }}</dd>

                    <dt class="col-sm-3">Última Actualización:</dt>
                    <dd class="col-sm-9" id="userUpdatedAt">{{ $user->updated_at->format('d/m/Y H:i:s') }}</dd>
                </dl>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning fw-semibold me-2">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-semibold">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal for PDF Export Options --}}
    <div class="modal fade" id="pdfDetailExportModal" tabindex="-1" aria-labelledby="pdfDetailExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfDetailExportModalLabel">Exportar Detalles a PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pdfDetailFilenameInput" class="form-label">Nombre del archivo:</label>
                        <input type="text" class="form-control" id="pdfDetailFilenameInput" placeholder="nombre_archivo.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPdfDetailExportBtn">Confirmar y Exportar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pdfDetailModalEl = document.getElementById('pdfDetailExportModal');
    const pdfDetailModal = pdfDetailModalEl ? new bootstrap.Modal(pdfDetailModalEl) : null;
    const pdfDetailFilenameInput = document.getElementById('pdfDetailFilenameInput');

    function exportUserDetailsToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const userName = document.getElementById('userNameShow')?.innerText || 'Usuario';
            const userId = document.getElementById('userId')?.innerText;
            const defaultFilename = `detalle_usuario_${(userId || 'N_A').replace(/[^a-z0-9]/gi, '_')}.pdf`;
            const finalFilename = filename || defaultFilename;

            doc.setFontSize(18);
            doc.text(`Detalles del Usuario: ${userName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                let value = 'N/A';
                if (valueElement) {
                    if (valueId === 'userRoles') {
                        const badges = valueElement.querySelectorAll('.badge');
                        value = badges.length > 0 ? Array.from(badges).map(badge => badge.textContent.trim()).join(', ') : 'Sin roles asignados.';
                    } else {
                        value = valueElement.innerText.trim();
                    }
                }
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "userId");
            // El nombre ya está en el título
            addDetail("Email", "userEmail");
            addDetail("Roles", "userRoles");
            addDetail("Fecha de Creación", "userCreatedAt");
            addDetail("Última Actualización", "userUpdatedAt");

            doc.save(finalFilename);
        } catch (error) {
            console.error("Error al generar PDF de detalles de usuario:", error);
            alert("Error al generar PDF de detalles de usuario. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const userId = document.getElementById('userId')?.innerText || 'N_A';
            const userName = document.getElementById('userNameShow')?.innerText.replace(/[^a-z0-9]/gi, '_').substring(0,30) || 'usuario';
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_${userName}_${userId}_${todayForFilename}.pdf`.replace(/[^a-z0-9_.-]/gi, '_').replace(/__+/g, '_');
            pdfDetailModal.show();
        }
    });

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportUserDetailsToPdf(filename);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush
