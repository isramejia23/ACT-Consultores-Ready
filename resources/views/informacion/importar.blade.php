@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-file-earmark-spreadsheet"></i> Importar Archivos Excel</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! nl2br(e(session('success'))) !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Importar Tareas -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i> Importar Tareas</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Sube el archivo Excel de tareas. Se crean facturas y tareas automáticamente para los clientes existentes.</p>
                    <form action="{{ route('importar.excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Archivo Excel de tareas:</label>
                            <input type="file" class="form-control" name="archivo" accept=".xlsx,.xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload me-1"></i> Importar Tareas
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Importar Cobros -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Importar Cobros</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Sube el archivo Excel de cobros. Se cruzan automáticamente con las facturas existentes por número de documento.</p>
                    <form action="{{ route('importar.cobros') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="archivo_cobros" class="form-label">Archivo Excel de cobros:</label>
                            <input type="file" class="form-control" name="archivo_cobros" accept=".xlsx,.xls" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-upload me-1"></i> Importar Cobros
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection