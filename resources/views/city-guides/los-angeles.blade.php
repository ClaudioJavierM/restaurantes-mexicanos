@extends('layouts.app')

@section('title', 'Restaurantes Mexicanos en Los Angeles | Guia de Cocina Mexicana Autentica en LA')
@section('meta_description', 'Los mejores restaurantes mexicanos en Los Angeles: East LA, Boyle Heights, cocina regional autentica. Guia completa de foodie tours mexicanos en Los Angeles, California.')

@push('meta')
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/guias/restaurantes-mexicanos-en-los-angeles') }}">
<meta property="og:title" content="Restaurantes Mexicanos en Los Angeles | Guia Completa LA">
<meta property="og:description" content="Descubre los mejores restaurantes mexicanos en Los Angeles: East LA, Boyle Heights y mas. La guia definitiva de comida mexicana autentica en California.">
<meta property="og:image" content="{{ asset('images/og/los-angeles-guide.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Restaurantes Mexicanos en Los Angeles">
<meta name="twitter:description" content="Guia completa de cocina mexicana autentica en Los Angeles por barrio.">
<link rel="canonical" href="{{ url('/guias/restaurantes-mexicanos-en-los-angeles') }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url('/guias/restaurantes-mexicanos-en-los-angeles') }}",
      "url": "{{ url('/guias/restaurantes-mexicanos-en-los-angeles') }}",
      "name": "Restaurantes Mexicanos en Los Angeles | Guia Completa",
      "description": "Los mejores restaurantes mexicanos en Los Angeles por barrio: East LA, Boyle Heights, cocina regional autentica.",
      "inLanguage": "es"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {"@type":"ListItem","position":1,"name":"Inicio","item":"{{ url('/') }}"},
        {"@type":"ListItem","position":2,"name":"Guias","item":"{{ url('/guia') }}"},
        {"@type":"ListItem","position":3,"name":"Restaurantes Mexicanos en Los Angeles","item":"{{ url('/guias/restaurantes-mexicanos-en-los-angeles') }}"}
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Cual es el mejor lugar para comer mexicano autentico en Los Angeles?",
          "acceptedAnswer": {"@type":"Answer","text":"Boyle Heights y East LA son el epicentro de la cocina mexicana autentica en Los Angeles. La Avenida Cesar Chavez en Boyle Heights concentra panaderias, carnicerias y taquerias que llevan decadas sirviendo a la comunidad."}
        },
        {
          "@type": "Question",
          "name": "Donde comer tacos de birria en Los Angeles?",
          "acceptedAnswer": {"@type":"Answer","text":"Los Angeles es uno de los epicentros de la fiebre de los tacos de birria con queso (birria quesatacos). Los mejores se encuentran en trucks y puestos de East LA, Boyle Heights y la zona de Huntington Park."}
        },
        {
          "@type": "Question",
          "name": "Hay restaurantes de cocina oaxaquena en Los Angeles?",
          "acceptedAnswer": {"@type":"Answer","text":"Si. Los Angeles tiene una comunidad oaxaquena muy grande, especialmente en Koreatown y Pico-Union. Encontraras tlayudas, mole negro, tasajo, memelas y mezcal artesanal en varios restaurantes de la ciudad."}
        },
        {
          "@type": "Question",
          "name": "Que es un foodie tour mexicano en Los Angeles?",
          "acceptedAnswer": {"@type":"Answer","text":"Un foodie tour mexicano en LA tipicamente recorre Boyle Heights y East LA, probando birria, tacos de canasta, agua de horchata, churros y pan dulce. Algunas empresas ofrecen tours guiados en fin de semana."}
        },
        {
          "@type": "Question",
          "name": "Cual es la cocina mexicana regional mas autentica en Los Angeles?",
          "acceptedAnswer": {"@type":"Answer","text":"En Los Angeles encontraras cocina jaliscience, oaxaquena, sinaloense, guerrerense y michoacana. La comunidad de Guerrero aporta algunos de los mejores puestos de pozole y tamales guerrerenses de Estados Unidos."}
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<!-- Hero -->
<div class="bg-gradient-to-br from-amber-600 via-red-600 to-green-700 text-white">
    <div class="max-w-7xl mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <nav class="text-sm mb-4 opacity-80" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:underline">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ route('city-guides.states') }}" class="hover:underline">Guias</a>
            <span class="mx-2">/</span>
            <span>Los Angeles</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            Restaurantes Mexicanos en Los Angeles
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-3xl">
            La guia definitiva de cocina mexicana autentica en LA: East LA, Boyle Heights, cocina regional de Oaxaca, Jalisco, Sinaloa y mas.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=Los+Angeles&estado=CA') }}" class="bg-white text-amber-700 font-semibold px-5 py-2 rounded-full hover:bg-amber-50 transition">
                Ver restaurantes en Los Angeles
            </a>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Por Que Los Angeles es la Capital Mundial de la Comida Mexicana</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Con mas de 4 millones de residentes de origen mexicano en el condado de Los Angeles, LA no es simplemente una ciudad con restaurantes mexicanos: es una ciudad que respira, vive y se alimenta de la cultura culinaria mexicana. Los Angeles tiene mas restaurantes mexicanos por habitante que cualquier otra ciudad fuera de Mexico.
        </p>
        <p class="text-gray-700 leading-relaxed">
            Desde trucks de birria con filas de tres horas hasta restaurantes de alta cocina mexicana con reservas de semanas de anticipacion, el espectro de opciones en Los Angeles es incomparable.
        </p>
    </section>

    <!-- Barrios -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Barrios Historicos con la Mejor Cocina Mexicana en LA</h2>

        <div class="grid md:grid-cols-2 gap-5">
            @foreach([
                ['Boyle Heights', 'El barrio mexicano original de Los Angeles. La Avenida Cesar Chavez es su columna vertebral gastronomica: panaderias con 50 anos de historia, mercados de carnitas, taquerias de madrugada y restaurantes familiares que no aceptan credito.', 'bg-red-50 border-red-200 text-red-800'],
                ['East Los Angeles', 'Tecnico territorio del condado (no de la ciudad), East LA es el corazon cultural de la mexicanidad en California. Whittier Blvd. y Eastern Ave. son las arterias de una cocina popular vibrante.', 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                ['Koreatown / Pico-Union', 'Hogar de una gran comunidad oaxaquena y guatemalteca. Encontraras restaurantes de mole negro, tlayudas con tasajo y mezcal artesanal a precios accesibles.', 'bg-amber-50 border-amber-200 text-amber-800'],
                ['Huntington Park & South LA', 'Zona de trucks legendarios de birria, marisquerias sinaloenses y carnicerias con preparados tradicionales. Menos turistica, mas autentica.', 'bg-blue-50 border-blue-200 text-blue-800']
            ] as [$barrio, $desc, $cls])
            <div class="border rounded-xl p-5 {{ $cls }}">
                <h3 class="text-lg font-bold mb-2">{{ $barrio }}</h3>
                <p class="text-sm leading-relaxed text-gray-700">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Tipos de cocina regional -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cocina Regional Mexicana mas Autentica en Los Angeles</h2>
        <div class="space-y-4">
            @foreach([
                ['Cocina Jaliscience', 'Birria de res y chivo, pozole rojo, tortas ahogadas y tequila. Los Angeles tiene algunas de las mejores birrerias fuera de Jalisco.'],
                ['Cocina Oaxaquena', 'Mole negro con pavo, tlayudas, tasajo, memelas y chocolate caliente. La comunidad oaxaquena en LA es una de las mas grandes del mundo fuera de Mexico.'],
                ['Cocina Sinaloense', 'Mariscos estilo Mazatlan: aguachile negro, ceviche de camaron, pescado zarandeado y tamales de elote. Imprescindible en Huntington Park.'],
                ['Cocina Guerrerense', 'Pozole blanco, tamales de rajas con queso y la inigualable cecina guerrerense. Comunidad muy activa en el Sur de Los Angeles.'],
                ['Cocina Michoacana', 'Carnitas de puerco al estilo Michoacan cocinadas en cobre, corundas y uchepos (tamales de elote tierno). Encontraras carnicerias michoacanas en toda la ciudad.']
            ] as [$tipo, $desc])
            <div class="flex gap-4 border-b border-gray-100 pb-4">
                <span class="text-2xl">🌮</span>
                <div>
                    <h4 class="font-bold text-gray-900">{{ $tipo }}</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Guia Foodie Tour -->
    <section class="mb-10 bg-gray-50 rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Guia para Foodie Tours Mexicanos en Los Angeles</h2>
        <p class="text-gray-700 mb-4">Si tienes un dia para explorar la cocina mexicana de LA, este es el itinerario recomendado:</p>
        <ol class="space-y-3">
            <li class="flex gap-3"><span class="bg-emerald-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold flex-shrink-0">1</span><p class="text-gray-700 text-sm"><strong>7 AM:</strong> Desayuno en una panaderia de Boyle Heights — pan dulce, atole y tacos de canasta.</p></li>
            <li class="flex gap-3"><span class="bg-emerald-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold flex-shrink-0">2</span><p class="text-gray-700 text-sm"><strong>10 AM:</strong> Mercado de East LA — frutas con chamoy, elotes y antojitos.</p></li>
            <li class="flex gap-3"><span class="bg-emerald-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold flex-shrink-0">3</span><p class="text-gray-700 text-sm"><strong>1 PM:</strong> Almuerzo de birria con consomme en uno de los trucks legendarios de East LA.</p></li>
            <li class="flex gap-3"><span class="bg-emerald-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold flex-shrink-0">4</span><p class="text-gray-700 text-sm"><strong>4 PM:</strong> Tlayudas y mole negro en un restaurante oaxaqueno de Koreatown.</p></li>
            <li class="flex gap-3"><span class="bg-emerald-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold flex-shrink-0">5</span><p class="text-gray-700 text-sm"><strong>8 PM:</strong> Cena de mariscos en una marisqueria sinaloense del Sur de LA.</p></li>
        </ol>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-r from-red-600 to-amber-600 rounded-2xl p-8 text-white mb-10">
        <h2 class="text-2xl font-bold mb-3">Encuentra Restaurantes Mexicanos en Los Angeles</h2>
        <p class="opacity-90 mb-6">Busca y descubre restaurantes mexicanos verificados en toda el area de Los Angeles.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=Los+Angeles&estado=CA') }}" class="bg-white text-red-700 font-bold px-6 py-3 rounded-full hover:bg-red-50 transition inline-block">
                Ver restaurantes en LA
            </a>
            <a href="{{ url('/gastronomia/cocina-mexicana-regional') }}" class="border border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 transition inline-block">
                Conocer cocina regional mexicana
            </a>
        </div>
    </section>

    <!-- Cross-link -->
    <section class="border border-gray-200 rounded-xl p-6 mb-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Tienes un restaurante mexicano en Los Angeles?</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Equipa tu restaurante con mobiliario autentico mexicano: sillas de madera, mesas de cantera, decoracion artesanal y equipos de cocina de alta resistencia.</p>
        </div>
        <a href="https://mf-imports.com?utm_source=famer&utm_medium=city-guide&utm_campaign=los-angeles" target="_blank" rel="noopener" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-full transition whitespace-nowrap">
            Ver mobiliario para restaurantes
        </a>
    </section>

    <!-- FAQ -->
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preguntas Frecuentes</h2>
        <div class="space-y-4">
            @foreach([
                ['Cual es el mejor lugar para comer mexicano autentico en Los Angeles?', 'Boyle Heights y East LA son el epicentro. La Avenida Cesar Chavez concentra panaderias, carnicerias y taquerias con decadas de historia.'],
                ['Donde comer tacos de birria en Los Angeles?', 'Los mejores se encuentran en trucks de East LA, Boyle Heights y Huntington Park. Los tacos de birria con queso (quesatacos) son los mas buscados.'],
                ['Hay restaurantes de cocina oaxaquena en Los Angeles?', 'Si. Los Angeles tiene una gran comunidad oaxaquena en Koreatown y Pico-Union con tlayudas, mole negro y mezcal artesanal.'],
                ['Que es un foodie tour mexicano en Los Angeles?', 'Un recorrido por Boyle Heights y East LA probando birria, tacos de canasta, agua fresca, churros y pan dulce. Algunas empresas ofrecen tours guiados los fines de semana.'],
                ['Cual es la cocina mexicana regional mas autentica en Los Angeles?', 'Jaliscience, oaxaquena, sinaloense y guerrerense son las mas representadas. LA tiene algunas de las mejores birrerias fuera de Jalisco.']
            ] as [$q, $a])
            <details class="border border-gray-200 rounded-lg">
                <summary class="px-5 py-4 font-semibold text-gray-800 cursor-pointer hover:bg-gray-50">{{ $q }}</summary>
                <p class="px-5 pb-4 text-gray-600 text-sm leading-relaxed">{{ $a }}</p>
            </details>
            @endforeach
        </div>
    </section>

</div>
@endsection
