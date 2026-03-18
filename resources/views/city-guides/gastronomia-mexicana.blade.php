@extends('layouts.app')

@section('title', 'Antojitos Mexicanos: Los 10 Mejores de Mexico | Guia de Gastronomia Mexicana')
@section('meta_description', 'Guia completa de los 10 mejores antojitos mexicanos regionales: tacos, tamales, pozole, chiles en nogada y mas. Conoce su region de origen y como reconocer la autenticidad.')

@push('meta')
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/gastronomia/antojitos-mexicanos') }}">
<meta property="og:title" content="Los 10 Mejores Antojitos Mexicanos | Guia de Gastronomia">
<meta property="og:description" content="Descubre los 10 antojitos mexicanos mas emblemáticos: su region de origen, ingredientes autenticos y donde encontrarlos.">
<meta property="og:image" content="{{ asset('images/og/antojitos-guide.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Los 10 Mejores Antojitos Mexicanos">
<meta name="twitter:description" content="Guia de gastronomia mexicana: los antojitos regionales mas emblematicos de Mexico.">
<link rel="canonical" href="{{ url('/gastronomia/antojitos-mexicanos') }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url('/gastronomia/antojitos-mexicanos') }}",
      "url": "{{ url('/gastronomia/antojitos-mexicanos') }}",
      "name": "Antojitos Mexicanos: Los 10 Mejores | Guia de Gastronomia Mexicana",
      "description": "Guia completa de los 10 mejores antojitos mexicanos regionales: tacos, tamales, pozole, chiles en nogada, tlayudas y mas.",
      "inLanguage": "es"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {"@type":"ListItem","position":1,"name":"Inicio","item":"{{ url('/') }}"},
        {"@type":"ListItem","position":2,"name":"Gastronomia","item":"{{ url('/gastronomia/antojitos-mexicanos') }}"},
        {"@type":"ListItem","position":3,"name":"Antojitos Mexicanos","item":"{{ url('/gastronomia/antojitos-mexicanos') }}"}
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Que son los antojitos mexicanos?",
          "acceptedAnswer": {"@type":"Answer","text":"Los antojitos mexicanos son preparaciones de masa de maiz (y a veces trigo) que se comen como botana, refrigerio o comida principal en Mexico y en comunidades mexicanas en el extranjero. El termino viene de 'antojo' y engloba desde tacos hasta tlayudas, tostadas, sopes, gorditas, huaraches, quesadillas y muchos mas."}
        },
        {
          "@type": "Question",
          "name": "Cual es el antojito mexicano mas popular en Estados Unidos?",
          "acceptedAnswer": {"@type":"Answer","text":"El taco es sin duda el antojito mexicano mas popular en Estados Unidos. Sin embargo, los tamales (especialmente en temporada navidena), las quesadillas y el pozole han ganado terreno significativo en los ultimos anos."}
        },
        {
          "@type": "Question",
          "name": "Que son los tamales y de donde son?",
          "acceptedAnswer": {"@type":"Answer","text":"Los tamales son preparaciones de masa de maiz rellena (con cerdo, pollo, rajas, frijoles o dulce) envuelta en hoja de maiz o platano y cocida al vapor. Existen en toda Mexico pero cada region tiene su variante: michoacanos (corundas), oaxaquenos (en hoja de platano con mole negro), veracruzanos (en hoja de platano) y muchos mas."}
        },
        {
          "@type": "Question",
          "name": "Cuando se comen los chiles en nogada?",
          "acceptedAnswer": {"@type":"Answer","text":"Los chiles en nogada son el platillo de temporada por excelencia en Mexico. Se preparan entre agosto y septiembre, cuando la granada roja y las nueces de Castilla estan en su punto. Son originarios de Puebla y representan los colores de la bandera mexicana: verde (perejil), blanco (nogada) y rojo (granada)."}
        },
        {
          "@type": "Question",
          "name": "Como reconocer un restaurante mexicano autentico?",
          "acceptedAnswer": {"@type":"Answer","text":"Busca: tortillas hechas a mano (no prensadas industrialmente), salsas molidas en metate o molcajete, menus que incluyan platillos regionales especificos (no solo tex-mex), personal de origen mexicano, y el uso de chiles secos reales (ancho, mulato, pasilla) en los guisos. Un menu bilingue con espanol primero es buena senal."}
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<!-- Hero -->
<div class="bg-gradient-to-br from-green-700 via-white to-red-700 text-white" style="background: linear-gradient(135deg, #166534 0%, #15803d 40%, #dc2626 100%)">
    <div class="max-w-7xl mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <nav class="text-sm mb-4 opacity-80" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:underline">Inicio</a>
            <span class="mx-2">/</span>
            <span>Gastronomia</span>
            <span class="mx-2">/</span>
            <span>Antojitos Mexicanos</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            Los 10 Mejores Antojitos Mexicanos
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-3xl">
            Guia completa de los antojitos mexicanos regionales mas emblematicos: origen, ingredientes autenticos, y como encontrarlos en restaurantes mexicanos cerca de ti.
        </p>
    </div>
</div>

<!-- Contenido -->
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">La Gastronomia Mexicana: Un Patrimonio de la Humanidad</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            La UNESCO declaro la cocina mexicana Patrimonio Inmaterial de la Humanidad en 2010, reconociendo algo que los mexicanos ya sabemos: la gastronomia de Mexico es una de las mas complejas, diversas y ricas del mundo. Los antojitos son su expresion mas democratica y cotidiana.
        </p>
        <p class="text-gray-700 leading-relaxed">
            El termino "antojito" viene de "antojo" — ese impulso irresistible de comer algo especifico. Y es que la cocina mexicana tiene la capacidad unica de generar antojos: el olor a tortilla caliente, el color del mole negro, el vapor de los tamales en el primer frio de diciembre.
        </p>
    </section>

    <!-- Top 10 antojitos -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Top 10 Antojitos Mexicanos Regionales</h2>

        <div class="space-y-8">
            @foreach([
                [
                    'Tacos',
                    'Todo Mexico',
                    'La preparacion mexicana mas universal. Una tortilla de maiz caliente con cualquier guiso encima — desde suadero de la CDMX hasta birria de Jalisco, pasando por cochinita pibil de Yucatan. Cada region tiene sus tacos identitarios.',
                    'Busca tortilla de maiz (no de trigo, excepto en el norte), salsa hecha al momento en molcajete, y el guiso cocinado ese dia — nunca recalentado de dia anterior.',
                    'bg-red-50 border-red-200'
                ],
                [
                    'Tamales',
                    'Todo Mexico (variantes regionales)',
                    'Masa de maiz preparada con manteca, rellena y envuelta en hoja de maiz o platano. Cada estado tiene su version: corundas michoacanas, uchepos de elote tierno, tamales oaxaquenos con mole negro en hoja de platano, tamales veracruzanos, vaporcitos del norte.',
                    'La masa debe ser esponjosa y separarse facilmente de la hoja. Si esta pegada y compacta, fue hecha con poca manteca o masa seca. Los autenticos tienen aroma a maiz nixtamalizado.',
                    'bg-amber-50 border-amber-200'
                ],
                [
                    'Pozole',
                    'Guerrero y Jalisco (rojo), CDMX y Michoacan (blanco), Guerrero (verde)',
                    'Caldo de maiz cacahuazintle (hominy) con carne de cerdo o pollo, servido con lechuga, rabano, oregano, limon y tostadas. El rojo lleva chile guajillo y ancho; el blanco es mas delicado; el verde usa pepitas de calabaza y tomatillo.',
                    'El maiz debe estar bien abierto (florecido). Si el caldo sabe solo a pollo sin la profundidad del maiz, fue hecho con atajo. Los gueros (trozos de cerdo) deben ser variados.',
                    'bg-emerald-50 border-emerald-200'
                ],
                [
                    'Chiles en Nogada',
                    'Puebla (temporada: agosto-septiembre)',
                    'Chile poblano relleno de picadillo de carne y frutas de temporada, cubierto con nogada (salsa de nuez de Castilla, queso de cabra y jerez), adornado con granada roja y perejil. Representa los colores de la bandera mexicana.',
                    'Solo se deben preparar en temporada (agosto-septiembre). La nogada debe ser fresca — nunca de botella. Si el chile tiene nogada amarillenta u oxidada, no es autentico. El relleno debe incluir frutas de temporada como durazno y pera.',
                    'bg-blue-50 border-blue-200'
                ],
                [
                    'Tlayudas',
                    'Oaxaca',
                    'Tortilla oaxaquena grande (30-40 cm), tostada y untada con frijoles negros, asiento (manteca de cerdo no refinada), quesillo (Oaxaca), tasajo o cecina, y verduras. Es el plato emblematico de Oaxaca.',
                    'La tortilla debe ser de maiz oaxaqueno, con textura coriace y bordes ligeros. El quesillo debe derretirse ligeramente. El asiento da un sabor muy caracteristico que no puede reemplazarse con aceite.',
                    'bg-purple-50 border-purple-200'
                ],
                [
                    'Enchiladas',
                    'Todo Mexico (variantes regionales)',
                    'Tortillas de maiz pasadas por salsa de chile (rojo, verde, mole) y rellenas. Las enchiladas verdes son con tomatillo; las rojas con guajillo; las mineras (Guanajuato) llevan papa y zanahoria; las suizas se gratifican con crema y queso.',
                    'La tortilla debe suavizarse en la salsa, no freirse seca y bañarse despues. Las enchiladas autenticas tienen color profundo del chile tostado, no del colorante. El relleno debe ser guiso, no solo queso amarillo.',
                    'bg-orange-50 border-orange-200'
                ],
                [
                    'Sopes',
                    'Centro de Mexico',
                    'Disco grueso de masa de maiz con bordes levantados, frito ligeramente y cubierto con frijoles, carne, lechuga, queso fresco, crema y salsa. Cada puesto tiene su propia receta de masa.',
                    'La masa debe ser de nixtamal fresco, no de Maseca reconstituida. El grosor es clave: muy delgado y es una tostada; muy grueso y no se cuece bien. Busca el color dorado parejo en la base.',
                    'bg-teal-50 border-teal-200'
                ],
                [
                    'Cemitas Poblanas',
                    'Puebla',
                    'Torta en pan de sesamo con carne empanizada (milanesa) o carnitas, aguacate, queso de Oaxaca, chipotles en adobo, papalo (hierba aromatica unica de Puebla) y cebolla. Es el sanwich mas complejo de Mexico.',
                    'El papalo es el ingredient definitorio — sin el, no es cemita poblana. El pan debe ser esponjoso y dorado. El queso de Oaxaca se deshebra y el chipotle debe ser de lata, no en polvo.',
                    'bg-lime-50 border-lime-200'
                ],
                [
                    'Barbacoa',
                    'Hidalgo (de res), Jalisco (de chivo)',
                    'Carne cocida lentamente envuelta en pencas de maguey en hoyo underground (barbacoa tradicional) o en autoclave industrial. La de Hidalgo usa cabeza de res; la de Jalisco usa chivo o borrego entero.',
                    'La barbacoa tradicional en hoyo tiene un sabor ahumado de maguey que no puede reproducirse en autoclave. La carne debe deshebrar sola con el tenedor. Si tiene sabor a hierba de maguey, es un buen indicio.',
                    'bg-stone-50 border-stone-200'
                ],
                [
                    'Cochinita Pibil',
                    'Yucatan',
                    'Cerdo marinado en achiote y jugo de naranja agria, envuelto en hoja de platano y cocido en hoyo (pib) o horno. Se sirve en tacos o tortas con cebolla morada encurtida en naranja agria y habanero.',
                    'El color naranja-rojo debe venir del achiote (recado rojo), no del colorante. La carne debe estar extremadamente tierna. La naranja agria es indispensable — no puede sustituirse con naranja dulce. La cebolla debe estar encurtida, no cruda.',
                    'bg-rose-50 border-rose-200'
                ]
            ] as $i => [$nombre, $region, $desc, $autenticidad, $cls])
            <div class="border rounded-2xl overflow-hidden {{ $cls }}">
                <div class="p-6">
                    <div class="flex items-start gap-4 mb-4">
                        <span class="text-4xl font-black text-gray-300 leading-none">{{ $i + 1 }}</span>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $nombre }}</h3>
                            <p class="text-sm text-gray-500 font-medium">Region de origen: {{ $region }}</p>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-4">{{ $desc }}</p>
                    <div class="bg-white/60 rounded-lg p-4">
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Como reconocer la autenticidad</p>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $autenticidad }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- CTA FAMER -->
    <section class="bg-gradient-to-r from-emerald-600 to-green-700 rounded-2xl p-8 text-white mb-10">
        <h2 class="text-2xl font-bold mb-3">Encuentra Estos Antojitos Cerca de Ti</h2>
        <p class="opacity-90 mb-6">Busca restaurantes mexicanos verificados que sirvan estos antojitos autenticos en tu ciudad.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes') }}" class="bg-white text-emerald-700 font-bold px-6 py-3 rounded-full hover:bg-emerald-50 transition inline-block">
                Buscar restaurantes
            </a>
            <a href="{{ url('/gastronomia/cocina-mexicana-regional') }}" class="border border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 transition inline-block">
                Las 7 regiones gastronomicas de Mexico
            </a>
        </div>
    </section>

    <!-- Cross-link -->
    <section class="border border-gray-200 rounded-xl p-6 mb-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Equipa tu restaurante de antojitos mexicanos</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Desde comals de barro hasta tortilladoras industriales, cazuelas de ceramica y mobiliario rustico autentico para crear el ambiente perfecto de una taqueria o fonda mexicana.</p>
        </div>
        <a href="https://mf-imports.com?utm_source=famer&utm_medium=gastronomia&utm_campaign=antojitos" target="_blank" rel="noopener" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-full transition whitespace-nowrap">
            Ver equipo de cocina mexicana
        </a>
    </section>

    <!-- FAQ -->
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preguntas Frecuentes sobre Antojitos Mexicanos</h2>
        <div class="space-y-4">
            @foreach([
                ['Que son los antojitos mexicanos?', 'Preparaciones de masa de maiz que se comen como botana o comida principal. El termino engloba tacos, tamales, sopes, gorditas, quesadillas, tostadas, huaraches y muchos mas.'],
                ['Cual es el antojito mexicano mas popular en Estados Unidos?', 'El taco es el mas popular, seguido de los tamales (especialmente en navidad), las quesadillas y el pozole.'],
                ['Que son los tamales y de donde son?', 'Masa de maiz rellena y cocida al vapor en hoja de maiz o platano. Existen en toda Mexico con variantes regionales: michoacanas, oaxaquenas, veracruzanas y mas.'],
                ['Cuando se comen los chiles en nogada?', 'En temporada: agosto y septiembre, cuando la granada roja y las nueces de Castilla estan en su punto. Son originarios de Puebla.'],
                ['Como reconocer un restaurante mexicano autentico?', 'Tortillas hechas a mano, salsas en molcajete, chiles secos reales en los guisos, menu con platillos regionales especificos y personal de origen mexicano.']
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
