@extends('admin.daily-quotes.layout')

@section('title', 'Gestión de Frases Diarias')

@section('content')
<!-- Información estadística (siempre visible) -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-check" style="font-size: 2rem; color: #3182ce;"></i>
                <h5 class="mt-3">Día del Año Actual</h5>
                <h2 style="color: #2c5282;">{{ date('z') + 1 }}</h2>
                <p class="text-muted mb-0">{{ date('d/m/Y') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-collection" style="font-size: 2rem; color: #5a6c7d;"></i>
                <h5 class="mt-3">Total Frases</h5>
                <h2 style="color: #2c5282;">{{ $total }}</h2>
                <p class="text-muted mb-0">de 366 posibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-bar-chart" style="font-size: 2rem; color: #48bb78;"></i>
                <h5 class="mt-3">Cobertura</h5>
                <h2 style="color: #2c5282;">{{ $total > 0 ? round(($total / 366) * 100, 1) : 0 }}%</h2>
                <p class="text-muted mb-0">del año completo</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla con scroll independiente -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0"><i class="bi bi-list-ul"></i> Frases Diarias</h3>
            <p class="mb-0 opacity-75">Total: {{ $total }} frases configuradas</p>
        </div>
        <a href="{{ route('admin.daily-quotes.create') }}" class="btn btn-light">
            <i class="bi bi-plus-circle"></i> Nueva Frase
        </a>
    </div>
    <div class="card-body p-0">
        @if(count($quotes) > 0)
            <!-- Contenedor con scroll independiente -->
            <div class="table-scroll-container">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Día</th>
                            <th>Frase</th>
                            <th>Autor</th>
                            <th>Categoría</th>
                            <th width="100">Estado</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotes as $quote)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $quote['day_of_year'] }}</span>
                                </td>
                                <td>
                                    <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $quote['quote'] }}
                                    </div>
                                </td>
                                <td>{{ $quote['author'] }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $quote['category'] }}</span>
                                </td>
                                <td>
                                    @if($quote['is_active'])
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activa</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactiva</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.daily-quotes.edit', $quote['id']) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.daily-quotes.destroy', $quote['id']) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta frase?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3;"></i>
                <p class="text-muted mt-3">No hay frases configuradas</p>
                <a href="{{ route('admin.daily-quotes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Crear Primera Frase
                </a>
            </div>
        @endif
    </div>
    
    <!-- Paginación (siempre visible) -->
    @if(count($quotes) > 0 && $lastPage > 1)
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('admin.daily-quotes.index', ['page' => $currentPage - 1, 'limit' => $limit]) }}">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>
                    
                    @php
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    @if($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('admin.daily-quotes.index', ['page' => 1, 'limit' => $limit]) }}">1</a>
                        </li>
                        @if($start > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif
                    
                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ route('admin.daily-quotes.index', ['page' => $i, 'limit' => $limit]) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    
                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ route('admin.daily-quotes.index', ['page' => $lastPage, 'limit' => $limit]) }}">{{ $lastPage }}</a>
                        </li>
                    @endif
                    
                    <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('admin.daily-quotes.index', ['page' => $currentPage + 1, 'limit' => $limit]) }}">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @endif
</div>
@endsection

