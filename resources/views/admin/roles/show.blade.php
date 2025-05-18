@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles del Rol')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Rol: <span id="roleNameShow">{{ $role->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButtonTrigger" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9" id="roleId">{{ $role->id }}</dd>

                    <dt class="col-sm-3">Nombre:</dt>
                    <dd class="col-sm-9">{{ $role->name }}</dd> {{-- Ya capturado en roleNameShow --}}

                    <dt class="col-sm-3">Guard Name:</dt>
                    <dd class="col-sm-9" id="roleGuardName">{{ $role->guard_name }}</dd>
                </dl>
                <hr>
                <h5>Permisos Asignados a este Rol:</h5>
                <div id="rolePermissionsList">
                    @if($role->permissions->count() > 0)
                        <ul>
                            @foreach ($role->permissions as $permission)
                                <li>{{ $permission->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Este rol no tiene permisos asignados directamente.</p>
                    @endif
                </div>
            </div>
            <div class="card-footer text-end">
                 {{-- Aquí iría botón de Editar si se implementa y se desea en esta vista --}}
                 {{-- <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i> Editar Rol
                </a> --}}
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

    function exportRoleDetailsToPdf(filename) {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
                console.error("jsPDF no está cargado.");
                alert("Error: La librería jsPDF no está cargada.");
                return;
            }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;
            const roleName = document.getElementById('roleNameShow')?.innerText || 'Rol';
            const roleId = document.getElementById('roleId')?.innerText;

            doc.setFontSize(18);
            doc.text(`Detalles del Rol: ${roleName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                let value = valueElement ? valueElement.innerText.trim() : 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "roleId");
            addDetail("Guard Name", "roleGuardName");
            yPos += 3; // Espacio extra antes de los permisos

            doc.text("Permisos Asignados:", 14, yPos); yPos += 7;
            const permissionsList = document.getElementById('rolePermissionsList');
            if (permissionsList) {
                const permissions = Array.from(permissionsList.querySelectorAll('li')).map(li => li.innerText.trim());
                if (permissions.length > 0) {
                    permissions.forEach(permission => {
                        if (yPos > 280) { // Salto de página si es necesario
                            doc.addPage();
                            yPos = 15;
                        }
                        doc.text(`- ${permission}`, 18, yPos);
                        yPos += 6;
                    });
                } else {
                    doc.text("Este rol no tiene permisos asignados directamente.", 18, yPos);
                    yPos += 6;
                }
            }

            doc.save(filename);
        } catch (error) {
            console.error("Error al generar PDF de detalles del rol:", error);
            alert("Error al generar PDF de detalles del rol. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportDetailPdfButtonTrigger')?.addEventListener('click', function () {
        if (pdfDetailModal && pdfDetailFilenameInput) {
            const roleId = document.getElementById('roleId')?.innerText || 'N_A';
            const roleName = document.getElementById('roleNameShow')?.innerText.replace(/[^a-z0-9]/gi, '_').substring(0,30) || 'rol';
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfDetailFilenameInput.value = `detalle_${roleName}_${roleId}_${todayForFilename}.pdf`.replace(/[^a-z0-9_.-]/gi, '_').replace(/__+/g, '_');
            pdfDetailModal.show();
        }
    });

    document.getElementById('confirmPdfDetailExportBtn')?.addEventListener('click', function () {
        const filename = pdfDetailFilenameInput ? pdfDetailFilenameInput.value.trim() : null;
        exportRoleDetailsToPdf(filename || `detalle_rol_${document.getElementById('roleId')?.innerText || 'N_A'}_${new Date().toISOString().slice(0,10)}.pdf`);
        if(pdfDetailModal) pdfDetailModal.hide();
    });
});
</script>
@endpush
