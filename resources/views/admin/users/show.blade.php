@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles del Usuario')

@section('page_header')
    Detalles del Usuario: <span class="text-muted" id="userNameHeader">{{ $user->name }}</span>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
@endsection

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> {{-- El título principal ya está en @page_header --}}
                <h5 class="mb-0">Información Detallada <small class="text-white-50" id="userNameShowCardHeader">({{ $user->name }})</small></h5>
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
                    <dd class="col-sm-9">{{ $user->name }}</dd> {{-- El nombre ya está en el @page_header y en el card-header --}}

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
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
    // Función para obtener el nombre de archivo personalizado
    function getCustomFilename(baseName, extension) {
        const now = new Date();
        // Formato de fecha YYYY-MM-DD
        const datePart = now.toISOString().slice(0, 10);
        const defaultName = `${baseName}_${datePart}`;

        // Mostrar prompt al usuario
        let userFilename = prompt("Introduce el nombre del archivo:", defaultName);

        // Si el usuario cancela, devuelve null
        if (userFilename === null) {
            return null;
        }

        // Usar el nombre del usuario si no está vacío, de lo contrario usar el por defecto
        let finalFilename = userFilename.trim() === '' ? defaultName : userFilename.trim();

        // Asegurarse de que la extensión esté presente
        if (!finalFilename.toLowerCase().endsWith(`.${extension}`)) {
            finalFilename += `.${extension}`;
        }
        return finalFilename;
    }

document.addEventListener('DOMContentLoaded', function () {
    function exportUserDetailsToPdf() {
        try {
            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') { console.error("jsPDF no está cargado."); alert("Error: jsPDF no está cargado."); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const userName = document.getElementById('userNameHeader')?.innerText || // Del nuevo @page_header
                             '{{ $user->name }}'; // Fallback directo

            const baseFilename = `detalle_usuario_${userName.replace(/\s+/g, '_')}`;
            const finalFilename = getCustomFilename(baseFilename, 'pdf');

            if (!finalFilename) return; // El usuario canceló

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
        exportUserDetailsToPdf();
    });

});
</script>
@endpush
