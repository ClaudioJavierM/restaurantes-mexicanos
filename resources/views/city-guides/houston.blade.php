@extends('layouts.app')

@section('title', 'Restaurantes Mexicanos en Houston | La Mejor Comida Mexicana de Texas')
@section('meta_description', 'Guia completa de restaurantes mexicanos en Houston: East End, Magnolia y EaDo. Por que Houston tiene una de las mejores escenas de comida mexicana en Estados Unidos.')

@push('meta')
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/guias/restaurantes-mexicanos-en-houston') }}">
<meta property="og:title" content="Restaurantes Mexicanos en Houston | Guia Completa Texas">
<meta property="og:description" content="Los mejores restaurantes mexicanos en Houston: East End, Magnolia, EaDo. Por que Houston es la capital de la comida mexicana en Texas.">
<meta property="og:image" content="{{ asset('images/og/houston-guide.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Restaurantes Mexicanos en Houston">
<meta name="twitter:description" content="Guia de los mejores restaurantes mexicanos en Houston, Texas.">
<link rel="canonical" href="{{ url('/guias/restaurantes-mexicanos-en-houston') }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url('/guias/restaurantes-mexicanos-en-houston') }}",
      "url": "{{ url('/guias/restaurantes-mexicanos-en-houston') }}",
      "name": "Restaurantes Mexicanos en Houston | Guia Completa Texas",
      "description": "Los mejores restaurantes mexicanos en Houston por barrio: East End, Magnolia, EaDo y la historia de la cocina mexicana en la ciudad mas diversa de Texas.",
      "inLanguage": "es"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {"@type":"ListItem","position":1,"name":"Inicio","item":"{{ url('/') }}"},
        {"@type":"ListItem","position":2,"name":"Guias","item":"{{ url('/guia') }}"},
        {"@type":"ListItem","position":3,"name":"Restaurantes Mexicanos en Houston","item":"{{ url('/guias/restaurantes-mexicanos-en-houston') }}"}
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Por que Houston tiene la mejor comida mexicana de Texas?",
          "acceptedAnswer": {"@type":"Answer","text":"Houston es la ciudad mas diversa de Estados Unidos y tiene la mayor concentracion de comunidades latinoamericanas del sur. Su cercania con la frontera, la fuerte inmigracion desde Tamaulipas, Veracruz y Puebla, y una cultura local que abraza la comida callejera crean la combinacion perfecta para una escena mexicana excepcional."}
        },
        {
          "@type": "Question",
          "name": "Cual es el mejor barrio para comer mexicano en Houston?",
          "acceptedAnswer": {"@type":"Answer","text":"East End (tambien llamado Segundo Barrio o East Houston) es el corazon de la comunidad mexicana en Houston. Magnolia Park, adyacente al East End, tiene algunas de las taquerias mas antiguas y autenticas de la ciudad."}
        },
        {
          "@type": "Question",
          "name": "Que diferencia la comida mexicana de Houston de la de San Antonio o Austin?",
          "acceptedAnswer": {"@type":"Answer","text":"Houston tiene una influencia tex-mex pero tambien una fuerte presencia de cocina interior mexicana: veracruzana, tamaulipeca y mas recientemente oaxaquena y sinaloense. San Antonio es mas tex-mex tradicional; Austin tiene mas nueva cocina mexicana de fusion."}
        },
        {
          "@type": "Question",
          "name": "Hay mercados mexicanos en Houston?",
          "acceptedAnswer": {"@type":"Answer","text":"Si. El mercado de la Avenida Harrisburg en East End y varios supermercados latinos como Fiesta Mart y La Michoacana tienen productos mexicanos importados, carnes preparadas y antojitos listos para llevar."}
        },
        {
          "@type": "Question",
          "name": "Que platillos mexicanos son los mas populares en Houston?",
          "acceptedAnswer": {"@type":"Answer","text":"Los favoritos en Houston son: tacos de barbacoa de cabeza el domingo por la manana, fajitas de arrachera, tamales de puerco en temporada navidena, menudo los sabados, y los burritos de carne guisada al estilo del norte de Mexico."}
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<!-- Hero -->
<div class="bg-gradient-to-br from-red-700 via-amber-600 to-emerald-700 text-white">
    <div class="max-w-7xl mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <nav class="text-sm mb-4 opacity-80" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:underline">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ route('city-guides.states') }}" class="hover:underline">Guias</a>
            <span class="mx-2">/</span>
            <span>Houston</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            Restaurantes Mexicanos en Houston
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-3xl">
            Houston tiene una de las mejores escenas de comida mexicana de Estados Unidos. Descubre por que y donde comer en la ciudad mas diversa de Texas.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=Houston&estado=TX') }}" class="bg-white text-red-700 font-semibold px-5 py-2 rounded-full hover:bg-red-50 transition">
                Ver restaurantes en Houston
            </a>
        </div>
    </div>
</div>

<!-- Contenido -->
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Por Que Houston Tiene Una de las Mejores Escenas de Comida Mexicana de EE.UU.</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Houston es la ciudad mas diversa de Estados Unidos segun el censo, y esa diversidad tiene un sabor muy especifico: mexicano. Con mas de 900,000 residentes de origen mexicano y una ubicacion a solo cuatro horas de la frontera con Tamaulipas, Houston disfruta de una cocina mexicana que combina la tradicion del norte de Mexico con las influencias de comunidades de Veracruz, Puebla, Oaxaca y mas estados.
        </p>
        <p class="text-gray-700 leading-relaxed mb-4">
            A diferencia de San Antonio (que tira mas hacia el Tex-Mex clasico) o Austin (mas nueva cocina de fusion), Houston tiene autenticidad: taquerias abiertas 24 horas, mercados de carnes preparadas con recetas de tres generaciones, y restaurantes donde el menu esta escrito primero en espanol.
        </p>
    </section>

    <!-- Barrios -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Los Mejores Barrios para Comer Mexicano en Houston</h2>

        <div class="grid md:grid-cols-2 gap-5">
            @foreach([
                ['East End (Segundo Barrio)', 'El barrio mexicano historico de Houston. La Avenida Harrisburg y la zona alrededor de Navigation Boulevard tienen las taquerias mas antiguas de la ciudad, muchas operando desde los anos 60 y 70 con recetas familiares intactas.', 'bg-red-50 border-red-200'],
                ['Magnolia Park', 'Adyacente al East End, Magnolia Park es conocida por sus carnes a la parrilla los fines de semana. Las taquerias de la zona son punto de reunion de la comunidad los domingos por la manana.', 'bg-amber-50 border-amber-200'],
                ['EaDo (East Downtown)', 'La zona mas nueva y gentrificada del este de Houston. Aqui conviven las taquerias de siempre con nuevos restaurantes de cocina mexicana contemporanea, mezcalerias y bares de autor con inspiracion mexicana.', 'bg-emerald-50 border-emerald-200'],
                ['Gulfton & Sharpstown', 'La zona con la mayor densidad de inmigracion reciente en Houston. Restaurantes de cocina veracruzana, oaxaquena y michoacana conviven con mercados de productos importados que abastecen a toda la ciudad.', 'bg-blue-50 border-blue-200']
            ] as [$barrio, $desc, $cls])
            <div class="border rounded-xl p-5 {{ $cls }}">
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $barrio }}</h3>
                <p class="text-sm leading-relaxed text-gray-700">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Platillos icónicos -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Platillos Mexicanos Imperdibles en Houston</h2>
        <div class="grid md:grid-cols-3 gap-4">
            @foreach([
                ['Barbacoa de cabeza', 'El ritual dominical por excelencia. Tacos de barbacoa de cabeza de res, cocida lentamente en hoyo o autoclave. Se sirve de madrugada hasta las 2 PM del domingo.'],
                ['Fajitas de arrachera', 'Houston reclama ser la ciudad donde las fajitas modernas se popularizaron. Busca cortes de arrachera marinada con la acidez de la naranja agria.'],
                ['Tamales de puerco', 'En temporada navidena, cada familia mexicana en Houston produce o encarga docenas de tamales. Las tamaleras del East End trabajan todo diciembre.'],
                ['Menudo rojo', 'El sabado por la manana con menudo es tan tipico de Houston como el BBQ. Los mejores panzos de res y maiz cacahuazintle se sirven hasta el mediodia.'],
                ['Carne guisada', 'Guiso de carne en salsa roja servido en burrito o con huevo. Es la herencia tex-mex que Houston ha refinado en su propia expresion culinaria.'],
                ['Mariscos al estilo sinaloense', 'Aguachile negro, ceviche tostada y campechana con clamato. La costa del Pacifico llego a Houston gracias a la inmigracion sinaloense de los 2000s.']
            ] as [$platillo, $desc])
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <h4 class="font-bold text-gray-900 mb-2">{{ $platillo }}</h4>
                <p class="text-gray-600 text-xs leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-r from-red-700 to-amber-600 rounded-2xl p-8 text-white mb-10">
        <h2 class="text-2xl font-bold mb-3">Encuentra Restaurantes Mexicanos en Houston</h2>
        <p class="opacity-90 mb-6">Busca y descubre restaurantes mexicanos verificados en Houston y el area de Harris County.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes?ciudad=Houston&estado=TX') }}" class="bg-white text-red-700 font-bold px-6 py-3 rounded-full hover:bg-red-50 transition inline-block">
                Ver restaurantes en Houston
            </a>
            <a href="{{ url('/gastronomia/cocina-mexicana-regional') }}" class="border border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 transition inline-block">
                Conocer cocina regional mexicana
            </a>
        </div>
    </section>

    <!-- Cross-link -->
    <section class="border border-gray-200 rounded-xl p-6 mb-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Abriendo un restaurante mexicano en Houston?</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Equipa tu restaurante con mobiliario autentico de Mexico: mesas de madera maciza, sillas de cuero rustico, barras de cantera y equipos de cocina de alta durabilidad.</p>
        </div>
        <a href="https://mf-imports.com?utm_source=famer&utm_medium=city-guide&utm_campaign=houston" target="_blank" rel="noopener" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-full transition whitespace-nowrap">
            Ver mobiliario para restaurantes
        </a>
    </section>

    <!-- FAQ -->
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preguntas Frecuentes</h2>
        <div class="space-y-4">
            @foreach([
                ['Por que Houston tiene la mejor comida mexicana de Texas?', 'Por su cercania a la frontera, la diversidad de comunidades mexicanas (tamaulipecos, veracruzanos, poblanos) y una cultura que abraza la comida callejera autentica.'],
                ['Cual es el mejor barrio para comer mexicano en Houston?', 'East End (Segundo Barrio) es el corazon historico. Magnolia Park tiene las taquerias mas autenticas. EaDo mezcla tradicion con propuestas contemporaneas.'],
                ['Que diferencia la comida mexicana de Houston de San Antonio?', 'Houston tiene mas influencia del interior de Mexico (veracruzana, oaxaquena, sinaloense). San Antonio es mas tex-mex clasico. Houston es mas autentica en general.'],
                ['Hay mercados mexicanos en Houston?', 'Si. El mercado de la Avenida Harrisburg en East End y supermercados como Fiesta Mart y La Michoacana tienen productos mexicanos importados.'],
                ['Que platillos mexicanos son los mas populares en Houston?', 'Barbacoa de cabeza (domingo), fajitas de arrachera, tamales de puerco (navidad), menudo rojo (sabado) y carne guisada en burrito.']
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
