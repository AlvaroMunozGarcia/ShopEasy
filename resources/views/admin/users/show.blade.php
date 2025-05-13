@extends('layouts.admin') {{-- O tu layout principal --}}

@section('title', 'Detalles del Usuario')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalles del Usuario: <span id="userNameShow">{{ $user->name }}</span></h5>
                <div>
                    <button id="exportDetailPdfButton" class="btn btn-sm btn-info me-2">
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
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i> Editar Usuario
                </a>
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
    const exportButton = document.getElementById('exportDetailPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let yPos = 15;

            const userName = document.getElementById('userNameShow')?.innerText || 'Usuario';
            const userId = document.getElementById('userId')?.innerText;

            doc.setFontSize(18);
            doc.text(`Detalles del Usuario: ${userName}`, 14, yPos); yPos += 10;

            doc.setFontSize(12);
            function addDetail(label, valueId) {
                const valueElement = document.getElementById(valueId);
                // Para los roles, tomamos el texto de los badges
                let value = valueElement ? valueElement.innerText.replace(/\n/g, ' ').replace(/\s+/g, ' ').trim() : 'N/A';
                doc.text(`${label}: ${value}`, 14, yPos);
                yPos += 7;
            }

            addDetail("ID", "userId");
            addDetail("Email", "userEmail");
            addDetail("Roles", "userRoles");
            addDetail("Fecha de Creación", "userCreatedAt");
            addDetail("Última Actualización", "userUpdatedAt");

            doc.save(`detalle_usuario_${userId || 'N_A'}.pdf`);
        });
    }
});
</script>
@endpush
