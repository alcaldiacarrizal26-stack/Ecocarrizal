@props([
    'name' => 'inspector_id',
    'id' => 'select_inspector_id',
    'selected' => null,
    'required' => false,
    'inspectores' => [], // Puede pasarse directamente desde la vista Blade o cargarse por JS
    'class' => ''
])

<div class="w-full">
    <label for="{{ $id }}" class="block text-xs font-semibold text-slate-600 uppercase mb-1">
        Asignar Inspector {{ $required ? '*' : '' }}
    </label>
    <select name="{{ $name }}" id="{{ $id }}" {{ $attributes }} {{ $required ? 'required' : '' }}
            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all {{ $class }}">
        <option value="">-- Seleccionar Fiscal / Inspector --</option>
        
        {{-- Rendereado SSR si se le pasan los inspectores desde el controlador --}}
        @foreach($inspectores as $inspector)
            <option value="{{ $inspector->id }}" {{ (string)$selected === (string)$inspector->id ? 'selected' : '' }}>
                {{ $inspector->user->name ?? 'S/N' }} 
                ({{ $inspector->codigo_credencial ?? 'Sin credencial' }})
                @if($inspector->especialidad) - [{{ $inspector->especialidad }}]@endif
            </option>
        @endforeach
    </select>
</div>