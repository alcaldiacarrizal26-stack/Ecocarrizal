@php
    // Listado ampliado de árboles protegidos en el Municipio Carrizal
    $arbolesProtegidos = [
        [
            'nombre_comun' => 'Araguaney',
            'nombre_cientifico' => 'Handroanthus chrysanthus',
            'categoria' => 'Árbol Nacional / Flora Protegida',
            'descripcion' => 'Árbol emblemático de Venezuela. Destaca por su espléndida floración amarilla intensa en temporada seca. En Carrizal cumple un rol vital en la protección y estabilización de los suelos montañosos contra la erosión.',
            'imagen' => asset('images/especies/araguaney.jpeg'), // <-- Ruta para insertar imagen 1
            'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200'
        ],
        [
            'nombre_comun' => 'Apamate',
            'nombre_cientifico' => 'Tabebuia rosea',
            'categoria' => 'Flora Silvestre Protegida',
            'descripcion' => 'Especie forestal de gran porte con hermosas flores rosadas o violáceas. Muy abundante en los corredores ecológicos de los Altos Mirandinos, aporta una alta capacidad de retención hídrica y sombra protectora.',
            'imagen' => asset('images/especies/apamate.jpeg'), // <-- Ruta para insertar imagen 2
            'badge_color' => 'bg-pink-50 text-pink-700 border-pink-200'
        ],
        [
            'nombre_comun' => 'Bucare Ceiba',
            'nombre_cientifico' => 'Erythrina poeppigiana',
            'categoria' => 'Árbol de Sombra y Conservación',
            'descripcion' => 'Reconocido por sus llamativas flores rojo-anaranjadas. Funciona como fijador natural de nitrógeno en el suelo y es refugio indispensable para la avifauna local en las zonas protectoras del municipio.',
            'imagen' => asset('images/especies/bucare.jpeg'), // <-- Ruta para insertar imagen 3
            'badge_color' => 'bg-red-50 text-red-700 border-red-200'
        ],
        [
            'nombre_comun' => 'Cují Yaque',
            'nombre_cientifico' => 'Prosopis juliflora',
            'categoria' => 'Flora Xerófila Autóctona',
            'descripcion' => 'Árbol altamente resistente adaptado a laderas y microclimas secos locales. Protege los suelos áridos de la degradación y provee alimento esencial para polinizadores y fauna silvestre de la región.',
            'imagen' => asset('images/especies/cuji.jpeg'), // <-- Ruta para insertar imagen 4
            'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
        ]
    ];
@endphp

<section id="especies" class="py-20 bg-slate-50/80 border-y border-slate-200/60 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-primary/10 text-primary rounded-full">Biodiversidad Local</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Especies de Árboles Protegidos en Carrizal</h2>
            <p class="text-slate-600 text-base">Conoce el catálogo forestal de especies vegetales autóctonas bajo régimen de protección especial en los corredores ecológicos y zonas protectoras del Municipio.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($arbolesProtegidos as $arbol)
                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    
                    <!-- Contenedor de la Imagen (Preparado para insertar archivo gráfico) -->
                    <div class="h-52 bg-slate-100 relative overflow-hidden group">
                        <!-- Inserta aquí la etiqueta <img> apuntando a tu variable o ruta -->
                        <img src="{{ $arbol['imagen'] }}" 
                             alt="{{ $arbol['nombre_comun'] }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?q=80&w=600&auto=format&fit=crop';">
                        
                        <!-- Etiqueta flotante de categoría -->
                        <div class="absolute top-3 left-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg backdrop-blur-md bg-white/90 border shadow-xs {{ $arbol['badge_color'] }}">
                                {{ $arbol['categoria'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Contenido descriptivo -->
                    <div class="p-6 space-y-2.5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 leading-tight">{{ $arbol['nombre_comun'] }}</h3>
                            <p class="text-xs italic text-emerald-700 font-medium mb-2">{{ $arbol['nombre_cientifico'] }}</p>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ $arbol['descripcion'] }}
                            </p>
                        </div>
                        
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-semibold uppercase">
                            <span>Especie Protegida</span>
                            <span class="text-emerald-600">Carrizal</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>