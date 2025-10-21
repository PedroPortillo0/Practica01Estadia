@extends('admin.daily-quotes.layout')

@section('title', 'Editar Frase Diaria')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="mb-0"><i class="bi bi-pencil"></i> Editar Frase Diaria</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.daily-quotes.update', $quote['id']) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="quote" class="form-label">Frase *</label>
                        <textarea 
                            class="form-control @error('quote') is-invalid @enderror" 
                            id="quote" 
                            name="quote" 
                            rows="4" 
                            required
                            placeholder="Escribe la frase motivacional aquí...">{{ old('quote', $quote['quote']) }}</textarea>
                        @error('quote')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Máximo 1000 caracteres</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="day_of_year" class="form-label">Día del Año *</label>
                        <input 
                            type="number" 
                            class="form-control @error('day_of_year') is-invalid @enderror" 
                            id="day_of_year" 
                            name="day_of_year" 
                            min="1" 
                            max="366" 
                            value="{{ old('day_of_year', $quote['day_of_year']) }}"
                            required>
                        @error('day_of_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Entre 1 y 366</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1"
                                {{ old('is_active', $quote['is_active'] ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <span id="status-text">{{ old('is_active', $quote['is_active'] ? '1' : '0') == '1' ? 'Activa' : 'Inactiva' }}</span>
                            </label>
                        </div>
                        <small class="text-muted">Las frases inactivas no aparecerán en la API</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="author" class="form-label">Autor *</label>
                        <input 
                            type="text" 
                            class="form-control @error('author') is-invalid @enderror" 
                            id="author" 
                            name="author" 
                            value="{{ old('author', $quote['author']) }}"
                            placeholder="Nombre del autor"
                            required>
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoría *</label>
                        <select 
                            class="form-select @error('category') is-invalid @enderror" 
                            id="category" 
                            name="category"
                            required>
                            <option value="">Selecciona una categoría</option>
                            <option value="Filosofica" {{ old('category', $quote['category']) == 'Filosofica' ? 'selected' : '' }}>Filosófica</option>
                            <option value="Estoica" {{ old('category', $quote['category']) == 'Estoica' ? 'selected' : '' }}>Estoica</option>
                            <option value="Motivacional" {{ old('category', $quote['category']) == 'Motivacional' ? 'selected' : '' }}>Motivacional</option>
                            <option value="Inspiracional" {{ old('category', $quote['category']) == 'Inspiracional' ? 'selected' : '' }}>Inspiracional</option>
                            <option value="Liderazgo" {{ old('category', $quote['category']) == 'Liderazgo' ? 'selected' : '' }}>Liderazgo</option>
                            <option value="Sabiduria" {{ old('category', $quote['category']) == 'Sabiduria' ? 'selected' : '' }}>Sabiduría</option>
                            <option value="Exito" {{ old('category', $quote['category']) == 'Exito' ? 'selected' : '' }}>Éxito</option>
                            <option value="Perseverancia" {{ old('category', $quote['category']) == 'Perseverancia' ? 'selected' : '' }}>Perseverancia</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.daily-quotes.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Actualizar Frase
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('is_active').addEventListener('change', function() {
        const statusText = document.getElementById('status-text');
        if (this.checked) {
            statusText.textContent = 'Activa';
            statusText.classList.remove('text-danger');
            statusText.classList.add('text-success');
        } else {
            statusText.textContent = 'Inactiva';
            statusText.classList.remove('text-success');
            statusText.classList.add('text-danger');
        }
    });
</script>
@endsection

