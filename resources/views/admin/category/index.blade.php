@extends('layouts.admin')

@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">

        {{-- Mensajes Flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Card principal --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Categorías</h5>
                <div>
                    <button id="exportPdfButtonList" class="btn btn-info btn-sm fw-semibold me-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar Lista a PDF
                    </button>
                    <a href="{{ route('categories.create') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Crear Categoría
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="categoriesTable" class="table table-bordered table-hover mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                {{-- <th>Descripción</th> --}}
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="text-center">{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    {{-- <td>{{ $category->description ?? 'N/A' }}</td> --}}
                                    <td class="text-center">
                                        <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-info me-1" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta categoría?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No se encontraron categorías.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginación opcional --}}
            @if(method_exists($categories, 'links'))
                <div class="card-footer d-flex justify-content-center">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportPdfButtonList');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text("Listado de Categorías", 14, 22);
            doc.autoTable({
                html: '#categoriesTable',
                startY: 30,
                theme: 'grid', // Opcional: 'striped', 'plain'
                headStyles: { fillColor: [22, 160, 133], textColor: 255, fontStyle: 'bold' }, // Estilo para cabecera
                // Si quieres excluir la columna de "Acciones" del PDF:
                // columns: [
                //     { header: 'ID', dataKey: 0 }, // Asume que el ID es la primera columna visible
                //     { header: 'Nombre', dataKey: 1 } // Asume que el Nombre es la segunda columna visible
                // ],
                // O puedes ocultar la columna antes de llamar a autoTable y mostrarla después,
                // o usar `columnStyles` para darle un ancho de 0 a la columna de acciones.
                // Por simplicidad, este ejemplo incluye todas las columnas visibles.
            });
            doc.save('listado_categorias.pdf');
        });
    }
});
</script>
@endpush
