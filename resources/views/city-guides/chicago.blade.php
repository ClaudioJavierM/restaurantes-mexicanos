@extends('layouts.app')

@section('title', 'Restaurantes Mexicanos en Chicago | Guia Completa de Cocina Mexicana Autentica')
@section('meta_description', 'Descubre los mejores restaurantes mexicanos en Chicago. Guia completa de Pilsen, Little Village, barrios historicos y los mejores tacos, tamales y mole en la Ciudad del Viento.')

@push('meta')
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/guias/restaurantes-mexicanos-en-chicago') }}">
<meta property="og:title" content="Restaurantes Mexicanos en Chicago | Guia Completa">
<meta property="og:description" content="Los mejores restaurantes mexicanos en Chicago: Pilsen, Little Village y mas. Guia de foodie para descubrir cocina mexicana autentica en Illinois.">
<meta property="og:image" content="{{ asset('images/og/chicago-guide.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Restaurantes Mexicanos en Chicago">
<meta name="twitter:description" content="Guia completa de los mejores restaurantes mexicanos en Chicago por barrio.">
<link rel="canonical" href="{{ url('/guias/restaurantes-mexicanos-en-chicago') }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url('/guias/restaurantes-mexicanos-en-chicago') }}",
      "url": "{{ url('/guias/restaurantes-mexicanos-en-chicago') }}",
      "name": "Restaurantes Mexicanos en Chicago | Guia Completa",
      "description": "Descubre los mejores restaurantes mexicanos en Chicago por barrio: Pilsen, Little Village y mas.",
      "inLanguage": "es"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {"@type":"ListItem","position":1,"name":"Inicio","item":"{{ url('/') }}"},
        {"@type":"ListItem","position":2,"name":"Guias","item":"{{ url('/guia') }}"},
        {"@type":"ListItem","position":3,"name":"Restaurantes Mexicanos en Chicago","item":"{{ url('/guias/restaurantes-mexicanos-en-chicago') }}"}
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Cual es el mejor barrio para comer mexicano en Chicago?",
          "acceptedAnswer": {"@type":"Answer","text":"Pilsen y Little Village son los barrios mas reconocidos. Pilsen es conocido por su escena artistica y restaurantes de cocina regional mexicana; Little Village (La Villita) ofrece taquerias autenticas y panaderias que operan desde los anos 70."}
        },
        {
          "@type": "Question",
          "name": "Donde encontrar tacos autenticos en Chicago?",
          "acceptedAnswer": {"@type":"Answer","text":"La Avenida 26 en Little Village concentra docenas de taquerias autenticas. Tambien encontraras excelentes opciones en el barrio de Pilsen sobre la Calle 18 y en Humboldt Park."}
        },
        {
          "@type": "Question",
          "name": "Hay restaurantes mexicanos regionales en Chicago?",
          "acceptedAnswer": {"@type":"Answer","text":"Si. Chicago tiene restaurantes especializados en cocina oaxaquena, jaliscience, poblana y michoacana. Encontraras mole negro oaxaqueno, birria de res estilo Jalisco, pozole rojo y tamales michoacanos."}
        },
        {
          "@type": "Question",
          "name": "Como llegar a Pilsen desde el centro de Chicago?",
          "acceptedAnswer": {"@type":"Answer","text":"Pilsen esta a 15-20 minutos en metro (linea Pink, estacion 18th Street) desde el Loop. En Uber o taxi son aproximadamente 10 minutos con trafico normal."}
        },
        {
          "@type": "Question",
          "name": "Que tipo de cocina mexicana es mas popular en Chicago?",
          "acceptedAnswer": {"@type":"Answer","text":"En Chicago predominan la cocina jaliscience (tacos de birria, pozole, tortas ahogadas), la michoacana (carnitas) y la poblana. En los ultimos anos la cocina oaxaquena ha ganado mucha popularidad con sus tlayudas y mole negro."}
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<!-- Hero -->
<div class="bg-gradient-to-br from-emerald-700 via-green-600 to-red-700 text-white">
    <div class="max-w-7xl mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <nav class="text-sm mb-4 opacity-80" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:underline">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ route('city-guides.states') }}" class="hover:underline">Guias</a>
            <span class="mx-2">/</span>
            <span>Chicago</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            Restaurantes Mexicanos en Chicago
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-3xl">
            Guia completa de los mejores lugares para comer autentica comida mexicana en la Ciudad del Viento: barrios, cocinas regionales y consejos de foodie.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=Chicago&estado=IL') }}" class="bg-white text-emerald-700 font-semibold px-5 py-2 rounded-full hover:bg-emerald-50 transition">
                Ver restaurantes en Chicago
            </a>
            <a href="{{ route('city-guides.states') }}" class="border border-white text-white font-semibold px-5 py-2 rounded-full hover:bg-white/10 transition">
                Explorar otras ciudades
            </a>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

    <!-- Introduccion -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Chicago: Capital de la Cocina Mexicana en el Medio Oeste</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Chicago alberga una de las comunidades mexicanas mas grandes y arraigadas de Estados Unidos, con mas de 700,000 residentes de origen mexicano. Esta presencia se traduce en una escena gastronomica extraordinaria: desde taquerias que abren a las 5 de la manana hasta restaurantes de cocina regional que representan lo mejor de Oaxaca, Jalisco, Michoacan y Puebla.
        </p>
        <p class="text-gray-700 leading-relaxed">
            A diferencia de otras ciudades, en Chicago la cocina mexicana no es tendencia reciente: lleva mas de 60 anos evolucionando en los barrios del sur y oeste de la ciudad, creando una identidad gastro-cultural unica que mezcla tradicion y adaptacion.
        </p>
    </section>

    <!-- Barrios -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Los Mejores Barrios para Comer Mexicano en Chicago</h2>

        <div class="space-y-6">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6">
                <h3 class="text-xl font-bold text-emerald-800 mb-2">Pilsen (18th Street)</h3>
                <p class="text-gray-700 leading-relaxed">
                    El corazon cultural mexicano de Chicago. La Calle 18 es el eje gastronomico donde encontraras restaurantes de cocina oaxaquena, fondas familiares con mole de olla, panaderias con conchas recien horneadas y taquerias que preparan barbacoa los fines de semana. Pilsen tambien tiene una vibrante escena de cafes y restaurantes de nueva cocina mexicana.
                </p>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <h3 class="text-xl font-bold text-red-800 mb-2">Little Village (La Villita) — Avenida 26</h3>
                <p class="text-gray-700 leading-relaxed">
                    Conocida como el "Mexico de Chicago", la Avenida 26 (W 26th Street) es quiza la calle comercial mexicana mas concurrida del Medio Oeste. Aqui encontraras taquerias autenticas, carnicerias con preparados para asar, restaurants de mariscos estilo Sinaloa y birrerias con caldos que se cocinan desde la madrugada.
                </p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <h3 class="text-xl font-bold text-amber-800 mb-2">Humboldt Park y Logan Square</h3>
                <p class="text-gray-700 leading-relaxed">
                    En el noroeste de Chicago, estos barrios combinan restaurantes mexicanos tradicionales con propuestas mas contemporaneas. Logan Square en particular ha visto una explosion de restaurantes de cocina mexicana de autor, muchos dirigidos por chefs de segunda y tercera generacion.
                </p>
            </div>
        </div>
    </section>

    <!-- Tipos de restaurantes -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">5 Tipos de Restaurantes Mexicanos que No Puedes Perder en Chicago</h2>
        <ol class="space-y-4 list-none">
            @foreach([
                ['Taqueria de madrugada', 'Las mejores taquerias de Chicago abren a las 4-5 AM para atender a trabajadores del turno nocturno. Busca tacos de suadero, cabeza y tripa en Little Village.'],
                ['Birreria del fin de semana', 'La birria de res estilo Jalisco es un ritual dominical en Chicago. Las birrerias autenticas ofrecen caldos con consomme, arroz, frijoles y las tortillas hechas a mano.'],
                ['Restaurante de cocina oaxaquena', 'Mole negro, tlayudas con tasajo y memelas de maiz azul. Chicago tiene varias opciones de cocina oaxaquena que rivalizan con las de la propia Oaxaca.'],
                ['Marisqueria estilo Sinaloa', 'Ceviches, aguachiles verdes y negros, coctel de camaron con clamato. La marisqueria mexicana ha ganado terreno en Chicago gracias a la inmigracion sinaloense.'],
                ['Panaderia-cafe de barrio', 'El desayuno mexicano completo: pan dulce recien hecho, atole, champurrado y tacos de canasta. Las panaderias de Pilsen son un ritual que no debes perderte.']
            ] as [$tipo, $desc])
            <li class="flex gap-4">
                <span class="text-2xl font-black text-emerald-600 w-8 flex-shrink-0">{{ $loop->iteration }}</span>
                <div>
                    <h4 class="font-bold text-gray-900">{{ $tipo }}</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
            </li>
            @endforeach
        </ol>
    </section>

    <!-- Historia -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Historia de la Comunidad Mexicana en Chicago</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Los primeros trabajadores mexicanos llegaron a Chicago en la decada de 1910, atraidos por los trabajos en los ferrocarriles, los mataderos y la industria metalurgica. Para los anos 50, Pilsen y Near West Side eran los centros de esta comunidad creciente. Con la llegada de nuevas oleadas migratorias, los negocios mexicanos prosperaron hasta convertir La Villita en un vibrante centro economico.
        </p>
        <p class="text-gray-700 leading-relaxed">
            Hoy, la tercera y cuarta generacion de familias mexicanas en Chicago ha transformado la escena gastronomica: chefs de formacion internacional regresan a sus barrios de origen para abrir restaurantes que honran las recetas de sus abuelas con tecnicas contemporaneas.
        </p>
    </section>

    <!-- CTA FAMER -->
    <section class="bg-gradient-to-r from-emerald-600 to-green-700 rounded-2xl p-8 text-white mb-10">
        <h2 class="text-2xl font-bold mb-3">Encuentra Restaurantes Mexicanos en Chicago</h2>
        <p class="opacity-90 mb-6">Busca entre cientos de restaurantes mexicanos verificados en Chicago, filtrados por barrio, tipo de cocina y calificacion.</p>
        <a href="{{ url('/restaurantes?ciudad=Chicago&estado=IL') }}" class="bg-white text-emerald-700 font-bold px-6 py-3 rounded-full hover:bg-emerald-50 transition inline-block">
            Ver restaurantes en Chicago
        </a>
    </section>

    <!-- Equipa tu restaurante -->
    <section class="border border-gray-200 rounded-xl p-6 mb-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Tienes un restaurante mexicano en Chicago?</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
                Equipa tu negocio con mobiliario y equipos de cocina autenticos. Desde mesas rusticas de madera hasta estufas industriales — todo lo que necesitas para crear el ambiente perfecto.
            </p>
        </div>
        <a href="https://mf-imports.com?utm_source=famer&utm_medium=city-guide&utm_campaign=chicago" target="_blank" rel="noopener" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-full transition whitespace-nowrap">
            Ver mobiliario para restaurantes
        </a>
    </section>

    <!-- FAQ -->
    <section class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preguntas Frecuentes</h2>
        <div class="space-y-4">
            @foreach([
                ['Cual es el mejor barrio para comer mexicano en Chicago?', 'Pilsen y Little Village son los mas reconocidos. Pilsen destaca por su escena artistica y cocina regional; Little Village ofrece taquerias autenticas desde los anos 70.'],
                ['Donde encontrar tacos autenticos en Chicago?', 'La Avenida 26 en Little Village tiene docenas de taquerias. Tambien encontraras excelentes opciones en Pilsen sobre la Calle 18 y en Humboldt Park.'],
                ['Hay restaurantes mexicanos regionales en Chicago?', 'Si. Chicago tiene restaurantes de cocina oaxaquena, jaliscience, poblana y michoacana con mole negro, birria, pozole y tamales autenticos.'],
                ['Como llegar a Pilsen desde el centro?', 'Pilsen esta a 15-20 minutos en metro (linea Pink, estacion 18th Street) desde el Loop.'],
                ['Que tipo de cocina mexicana es mas popular en Chicago?', 'Predominan la jaliscience (birria, pozole), la michoacana (carnitas) y en los ultimos anos la oaxaquena con tlayudas y mole negro.']
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
