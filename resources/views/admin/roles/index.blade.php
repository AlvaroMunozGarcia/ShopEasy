@extends('layouts.admin')

@section('title', 'Gestión de Roles')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        {{-- Mensajes Flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestión de Roles</h5>
                <div>
                    <button id="exportPdfButtonListTrigger" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                    {{-- Descomenta si habilitas la creación de roles --}}
                    {{-- 
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-shield-plus me-1"></i> Crear Nuevo Rol
                    </a>
                    --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rolesTable" class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Rol</th>
                                <th>Guard</th>
                                <th>Permisos Asociados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td class="text-center">{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td>
                                        @forelse ($role->permissions as $permission)
                                            <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-muted">Sin permisos asignados</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        {{-- 
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay roles definidos en el sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginación opcional --}}
            @if ($roles instanceof \Illuminate\Pagination\LengthAwarePaginator && $roles->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $roles->links() }}
                </div>
            @endif

        </div>

        {{-- Modal for PDF Export Options --}}
        <div class="modal fade" id="pdfExportModal" tabindex="-1" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfExportModalLabel">Exportar Listado a PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pdfFilenameInput" class="form-label">Nombre del archivo:</label>
                            <input type="text" class="form-control" id="pdfFilenameInput" placeholder="nombre_archivo.pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmPdfExportBtn">Confirmar y Exportar</button>
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
    const pdfModalEl = document.getElementById('pdfExportModal');
    const pdfModal = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
    const pdfFilenameInput = document.getElementById('pdfFilenameInput');

    function exportListToPdf(filename = 'listado_roles.pdf') {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
                console.error("jsPDF no está cargado.");
                alert("Error: La librería jsPDF no está cargada.");
                return;
            }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text("Listado de Roles", 14, 22);
            doc.autoTable({
                html: '#rolesTable', // Asegúrate que tu tabla tenga este ID
                startY: 30,
                theme: 'grid',
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' },
                // Puedes añadir más opciones de autoTable aquí si es necesario
            });
            doc.save(filename);
        } catch (error) {
            console.error("Error al generar PDF del listado de roles:", error);
            alert("Error al generar PDF del listado de roles. Verifique la consola para más detalles.");
        }
    }

    document.getElementById('exportPdfButtonListTrigger')?.addEventListener('click', () => {
        if (pdfModal && pdfFilenameInput) {
            const date = new Date();
            const todayForFilename = `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            pdfFilenameInput.value = `listado_roles_${todayForFilename}.pdf`;
            pdfModal.show();
        }
    });

    document.getElementById('confirmPdfExportBtn')?.addEventListener('click', () => {
        if (pdfFilenameInput) {
            const filename = pdfFilenameInput.value.trim() || `listado_roles_${new Date().toISOString().slice(0,10)}.pdf`;
            exportListToPdf(filename);
            if(pdfModal) pdfModal.hide();
        }
    });
});
</script>
@endpush
