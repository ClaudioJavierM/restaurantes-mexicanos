@extends('layouts.app')

@section('title', 'Restaurantes Mexicanos en Nueva York | Guia Completa NYC')
@section('meta_description', 'Descubre los mejores restaurantes mexicanos en Nueva York: Jackson Heights, El Barrio, Hell\'s Kitchen y Sunset Park Brooklyn. Guia de cocina mexicana autentica en NYC.')

@push('meta')
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/guias/restaurantes-mexicanos-en-nueva-york') }}">
<meta property="og:title" content="Restaurantes Mexicanos en Nueva York | Guia NYC">
<meta property="og:description" content="Los mejores restaurantes mexicanos en Nueva York por barrio: Jackson Heights, East Harlem, Hell's Kitchen y Sunset Park Brooklyn.">
<meta property="og:image" content="{{ asset('images/og/nueva-york-guide.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Restaurantes Mexicanos en Nueva York">
<meta name="twitter:description" content="Guia completa de cocina mexicana autentica en NYC por barrio.">
<link rel="canonical" href="{{ url('/guias/restaurantes-mexicanos-en-nueva-york') }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url('/guias/restaurantes-mexicanos-en-nueva-york') }}",
      "url": "{{ url('/guias/restaurantes-mexicanos-en-nueva-york') }}",
      "name": "Restaurantes Mexicanos en Nueva York | Guia Completa NYC",
      "description": "Los mejores restaurantes mexicanos en Nueva York por barrio: Jackson Heights, El Barrio, Hell's Kitchen y Sunset Park Brooklyn.",
      "inLanguage": "es"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {"@type":"ListItem","position":1,"name":"Inicio","item":"{{ url('/') }}"},
        {"@type":"ListItem","position":2,"name":"Guias","item":"{{ url('/guia') }}"},
        {"@type":"ListItem","position":3,"name":"Restaurantes Mexicanos en Nueva York","item":"{{ url('/guias/restaurantes-mexicanos-en-nueva-york') }}"}
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "En que barrio de Nueva York hay mas restaurantes mexicanos?",
          "acceptedAnswer": {"@type":"Answer","text":"Jackson Heights en Queens es el centro de la comunidad mexicana en Nueva York, especialmente de poblanos y oaxaquenos. East Harlem (El Barrio) es historicamente el barrio latino mas antiguo de NYC con fuerte presencia mexicana."}
        },
        {
          "@type": "Question",
          "name": "Donde comer tacos autenticos en Nueva York?",
          "acceptedAnswer": {"@type":"Answer","text":"Los mejores tacos autenticos en Nueva York se encuentran en Jackson Heights (Queens), Sunset Park (Brooklyn) y los trucks de comida en el Bronx. Roosevelt Avenue en Jackson Heights es imprescindible."}
        },
        {
          "@type": "Question",
          "name": "Hay restaurantes de cocina poblana en Nueva York?",
          "acceptedAnswer": {"@type":"Answer","text":"Si. Nueva York tiene la mayor comunidad poblana fuera de Mexico, especialmente en Brooklyn y el Bronx. Encontraras cemitas poblanas, mole poblano, chiles en nogada (en temporada) y tlacoyos."}
        },
        {
          "@type": "Question",
          "name": "Cual es la diferencia entre los restaurantes mexicanos de Manhattan y los del outer boroughs?",
          "acceptedAnswer": {"@type":"Answer","text":"Los restaurantes de Manhattan (Hell's Kitchen, West Village) tienden a ser de nueva cocina mexicana con precios altos. Los de Queens, Brooklyn y el Bronx son mas autenticos, economicos y frecuentados por la comunidad mexicana local."}
        },
        {
          "@type": "Question",
          "name": "Donde comer cocina oaxaquena en Nueva York?",
          "acceptedAnswer": {"@type":"Answer","text":"Jackson Heights en Queens tiene varias opciones de cocina oaxaquena. Tambien encontraras restaurantes oaxaquenos en Sunset Park (Brooklyn) y en el Lower East Side de Manhattan."}
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<!-- Hero -->
<div class="bg-gradient-to-br from-gray-800 via-emerald-700 to-red-700 text-white">
    <div class="max-w-7xl mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <nav class="text-sm mb-4 opacity-80" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:underline">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ route('city-guides.states') }}" class="hover:underline">Guias</a>
            <span class="mx-2">/</span>
            <span>Nueva York</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            Restaurantes Mexicanos en Nueva York
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-3xl">
            Guia completa de cocina mexicana autentica en NYC: desde las taquerias de Roosevelt Avenue hasta los restaurantes de mole en Sunset Park Brooklyn.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=New+York&estado=NY') }}" class="bg-white text-gray-800 font-semibold px-5 py-2 rounded-full hover:bg-gray-100 transition">
                Ver restaurantes en Nueva York
            </a>
        </div>
    </div>
</div>

<!-- Contenido -->
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">La Escena Mexicana de Nueva York: Autentica y Sorprendente</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Nueva York tiene mas de 700,000 residentes de origen mexicano, siendo los poblanos el grupo mas numeroso, seguidos por oaxaquenos, guerrerenses y michoacanos. Esta diversidad de origenes se refleja en una escena gastronomica que a menudo sorprende a los visitantes que esperan encontrar solo tex-mex o burritos californianos.
        </p>
        <p class="text-gray-700 leading-relaxed">
            La clave es saber donde buscar. Los restaurantes de los outer boroughs — Queens, Brooklyn, el Bronx — son en muchos casos superiores a los de Manhattan en autenticidad y precio. Una cemita poblana en Sunset Park puede transportarte directamente a Puebla.
        </p>
    </section>

    <!-- Barrios -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Los Mejores Barrios para Comer Mexicano en Nueva York</h2>
        <div class="space-y-5">
            @foreach([
                ['Jackson Heights, Queens', 'Roosevelt Avenue entre las calles 69 y 90 es la meca de la comida callejera latinoamericana en NYC. Los trucks y puestos de tacos operan hasta las 3 AM. Busca especialmente tacos de canasta, quesadillas de huitlacoche y elotes con crema.', 'border-l-4 border-emerald-500 pl-5'],
                ['East Harlem (El Barrio), Manhattan', 'El barrio latino mas antiguo de Manhattan tiene una presencia mexicana que data de los anos 40. La Avenida Lexington y la calle 116 tienen restaurantes de cocina mexicana tradicional, panaderias y mercados de productos importados de Mexico.', 'border-l-4 border-red-500 pl-5'],
                ["Hell's Kitchen, Manhattan", 'Esta zona del Midwesty oeste ha experimentado una explosion de restaurantes mexicanos de nueva cocina. Encontraras desde mezcalerias con propuestas de cocteleria hasta restaurantes con chefs de renombre internacional. Precios mas altos, propuestas mas experimentales.', 'border-l-4 border-amber-500 pl-5'],
                ['Sunset Park, Brooklyn', 'La comunidad mexicana de Sunset Park (concentrada en la 5a Avenida) es principalmente poblana. Las cemitas, las tortas de milanesa y el mole poblano son especialidades de este barrio que muy pocos turistas conocen.', 'border-l-4 border-blue-500 pl-5']
            ] as [$barrio, $desc, $cls])
            <div class="{{ $cls }}">
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $barrio }}</h3>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Historia -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Historia de la Comunidad Mexicana en Nueva York</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            A diferencia de California o Texas, la presencia mexicana masiva en Nueva York es relativamente reciente. La primera ola migratoria significativa llego en los anos 80 y 90, principalmente desde Puebla — razon por la cual los neoyorquinos de origen mexicano son frecuentemente llamados "poblanos" de manera generica, aunque vengan de muchos estados.
        </p>
        <p class="text-gray-700 leading-relaxed">
            Esta comunidad construyo un tejido economico solido: restaurantes, panaderias, empresas de construccion y servicios. Hoy la segunda generacion de mexicano-neoyorquinos esta abriendo restaurantes que fusionan las tradiciones de sus padres con la sofisticacion culinaria de una ciudad global.
        </p>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-r from-gray-800 to-emerald-700 rounded-2xl p-8 text-white mb-10">
        <h2 class="text-2xl font-bold mb-3">Encuentra Restaurantes Mexicanos en Nueva York</h2>
        <p class="opacity-90 mb-6">Descubre restaurantes mexicanos verificados en todos los boroughs de Nueva York.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=New+York&estado=NY') }}" class="bg-white text-gray-800 font-bold px-6 py-3 rounded-full hover:bg-gray-100 transition inline-block">
                Ver restaurantes en NYC
            </a>
            <a href="{{ url('/gastronomia/antojitos-mexicanos') }}" class="border border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 transition inline-block">
                Guia de antojitos mexicanos
            </a>
        </div>
    </section>

    <!-- Cross-link MF Imports -->
    <section class="border border-gray-200 rounded-xl p-6 mb-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Equipa tu restaurante mexicano en Nueva York</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Mobiliario autentico mexicano para restaurantes: mesas de madera de pino, sillas de equipal, barras de cantera y decoracion artesanal que crea el ambiente perfecto.</p>
        </div>
        <a href="https://mf-imports.com?utm_source=famer&utm_medium=city-guide&utm_campaign=nueva-york" target="_blank" rel="noopener" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-full transition whitespace-nowrap">
            Ver mobiliario para restaurantes
        </a>
    </section>

    <!-- FAQ -->
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preguntas Frecuentes</h2>
        <div class="space-y-4">
            @foreach([
                ['En que barrio de Nueva York hay mas restaurantes mexicanos?', 'Jackson Heights en Queens es el centro de la comunidad mexicana. East Harlem es el barrio latino mas historico de NYC con fuerte presencia mexicana.'],
                ['Donde comer tacos autenticos en Nueva York?', 'Los mejores tacos estan en Jackson Heights (Roosevelt Avenue), Sunset Park (Brooklyn) y los trucks del Bronx.'],
                ['Hay restaurantes de cocina poblana en Nueva York?', 'Si. NYC tiene la mayor comunidad poblana fuera de Mexico. Encontraras cemitas, mole poblano y chiles en nogada en Sunset Park y el Bronx.'],
                ['Cual es la diferencia entre los restaurantes de Manhattan y los outer boroughs?', 'Manhattan tiene nueva cocina mexicana con precios altos. Queens, Brooklyn y el Bronx son mas autenticos, economicos y frecuentados por mexicanos locales.'],
                ['Donde comer cocina oaxaquena en Nueva York?', 'Jackson Heights (Queens) y Sunset Park (Brooklyn) tienen varias opciones de cocina oaxaquena con tlayudas, mole y mezcal.']
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
