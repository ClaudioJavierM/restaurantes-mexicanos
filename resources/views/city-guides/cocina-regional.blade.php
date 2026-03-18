@extends('layouts.app')

@section('title', 'Cocina Mexicana Regional: Las 7 Regiones Gastronomicas de Mexico')
@section('meta_description', 'Descubre las 7 regiones gastronomicas de Mexico: ingredientes iconicos, platillos emblematicos por region y que tipos de restaurantes mexicanos regionales encontraras en FAMER.')

@push('meta')
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/gastronomia/cocina-mexicana-regional') }}">
<meta property="og:title" content="Cocina Mexicana Regional: Las 7 Regiones Gastronomicas de Mexico">
<meta property="og:description" content="Las 7 regiones gastronomicas de Mexico: ingredientes icónicos, platillos emblematicos y la diversidad de la cocina mexicana regional.">
<meta property="og:image" content="{{ asset('images/og/cocina-regional.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Las 7 Regiones Gastronomicas de Mexico">
<meta name="twitter:description" content="Guia completa de la cocina mexicana regional: las 7 grandes regiones gastronomicas de Mexico.">
<link rel="canonical" href="{{ url('/gastronomia/cocina-mexicana-regional') }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url('/gastronomia/cocina-mexicana-regional') }}",
      "url": "{{ url('/gastronomia/cocina-mexicana-regional') }}",
      "name": "Cocina Mexicana Regional: Las 7 Regiones Gastronomicas de Mexico",
      "description": "Las 7 regiones gastronomicas de Mexico, sus ingredientes icónicos y platillos emblematicos.",
      "inLanguage": "es"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {"@type":"ListItem","position":1,"name":"Inicio","item":"{{ url('/') }}"},
        {"@type":"ListItem","position":2,"name":"Gastronomia","item":"{{ url('/gastronomia/antojitos-mexicanos') }}"},
        {"@type":"ListItem","position":3,"name":"Cocina Mexicana Regional","item":"{{ url('/gastronomia/cocina-mexicana-regional') }}"}
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Cuantas regiones gastronomicas tiene Mexico?",
          "acceptedAnswer": {"@type":"Answer","text":"Mexico tiene al menos 7 grandes regiones gastronomicas reconocidas: el Norte (cocina de ranchos y ganado), el Noroeste-Pacifico (mariscos y trigo), el Centro-Bajio (cocina mestiza baroque), el Centro (CDMX y Estado de Mexico), Oaxaca (la cocina mas compleja del pais), Veracruz-Golfo (influencia caribena y espanola) y el Sureste (Yucatan y Chiapas con influencia maya y caribena)."}
        },
        {
          "@type": "Question",
          "name": "Cual es la region gastronomica mas reconocida de Mexico?",
          "acceptedAnswer": {"@type":"Answer","text":"Oaxaca es internacionalmente la region gastronomica mas reconocida de Mexico. Sus 7 moles, el quesillo, el mezcal artesanal, las tlayudas y la diversidad de sus chiles la han convertido en destino de turismo gastronomico mundial."}
        },
        {
          "@type": "Question",
          "name": "Que ingredientes son unicos de la cocina del norte de Mexico?",
          "acceptedAnswer": {"@type":"Answer","text":"El norte de Mexico usa carne de res (machaca, carne seca, cortes a la parrilla), frijol pinto, tortilla de harina de trigo, chile colorado y queso chihuahua. La barbacoa de borrego en hoyo y el caldillo duranguense son platillos emblematicos del norte."}
        },
        {
          "@type": "Question",
          "name": "Que es la cocina yucateca y como se diferencia del resto de Mexico?",
          "acceptedAnswer": {"@type":"Answer","text":"La cocina yucateca tiene influencia maya, espanola y caribena. Usa el achiote (recado rojo), la naranja agria, el habanero (el chile mas picante de Mexico), la hoja de platano para envolver y tecnicas de coccion en hoyo (pib). El cochinita pibil, el queso relleno y los panuchos son sus platillos mas emblematicos."}
        },
        {
          "@type": "Question",
          "name": "Que estados componen la region gastronomica del Bajio?",
          "acceptedAnswer": {"@type":"Answer","text":"La region del Bajio incluye principalmente Guanajuato, Queretaro, Aguascalientes, San Luis Potosi y partes de Michoacan y Jalisco. Su cocina es rica en carnes en adobo, enchiladas mineras, chiles rellenos y dulces de leche y cajeta."}
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
<!-- Hero -->
<div class="bg-gradient-to-br from-emerald-800 via-emerald-600 to-amber-500 text-white">
    <div class="max-w-7xl mx-auto px-4 py-14 sm:px-6 lg:px-8">
        <nav class="text-sm mb-4 opacity-80" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:underline">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ url('/gastronomia/antojitos-mexicanos') }}" class="hover:underline">Gastronomia</a>
            <span class="mx-2">/</span>
            <span>Cocina Mexicana Regional</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            Cocina Mexicana Regional
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-3xl">
            Las 7 regiones gastronomicas de Mexico: ingredientes iconicos, platillos emblematicos y la increible diversidad culinaria de un pais que es en realidad una galaxia de cocinas.
        </p>
    </div>
</div>

<!-- Contenido -->
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Mexico No Es Un Solo Sabor</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Cuando alguien dice "me gusta la comida mexicana", en realidad esta diciendo que le gustan decenas de cocinas distintas que comparten un origen comun pero que tienen ingredientes, tecnicas, sabores y rituales completamente diferentes. La distancia culinaria entre una taqueria de la Ciudad de Mexico y un restaurante yucateco es comparable a la que existe entre la cocina italiana y la griega.
        </p>
        <p class="text-gray-700 leading-relaxed">
            Entender las regiones gastronomicas de Mexico es el primer paso para comer mejor en cualquier restaurante mexicano y para apreciar por que algunos platillos saben diferente segun donde se preparan.
        </p>
    </section>

    <!-- Las 7 regiones -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Las 7 Grandes Regiones Gastronomicas de Mexico</h2>

        <div class="space-y-10">

            <!-- 1. Norte -->
            <div class="border-l-4 border-amber-500 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">1. El Norte — Cocina de Ranchos y Frontera</h3>
                <p class="text-sm text-amber-600 font-medium mb-3">Chihuahua, Sonora, Coahuila, Nuevo Leon, Durango, Tamaulipas, Baja California</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    La cocina del norte esta moldeada por la aridez del desierto, la ganaderia extensiva y la influencia de la frontera con Estados Unidos. Aqui la tortilla es de harina de trigo (no de maiz), los cortes de carne a la parrilla son el eje de la alimentacion y los quesos de origen espanol se producen con leche de vaca local.
                </p>
                <div class="bg-amber-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Carne seca y machaca, frijol pinto, chile colorado, chile de arbol, queso chihuahua, tortilla de harina, cajeta de Celaya, nopal, sotol.</p>
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Carne asada con frijoles y tortilla de harina, machaca con huevo, caldillo duranguense, cabrito al pastor (Nuevo Leon), burritos de carne guisada, arrachera norteña.</p>
                </div>
            </div>

            <!-- 2. Noroeste Pacifico -->
            <div class="border-l-4 border-blue-500 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">2. Noroeste y Pacifico — Mariscos y Costa</h3>
                <p class="text-sm text-blue-600 font-medium mb-3">Sinaloa, Nayarit, Colima, Jalisco (costa), Baja California Sur</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    La cocina del Pacifico mexicano tiene en el mar su ingrediente principal. La riqueza biologica del Oceano Pacifico — camaron, pez sierra, atun, marlin — ha generado una cocina de mariscos que muchos consideran la mejor del pais. Sinaloa en particular es la cuna del aguachile y el ceviche moderno.
                </p>
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Camaron del Pacifico, pez sierra, marlin ahumado, chile mora, chile chiltepin, oregano seco, limón agrio, salsa Huichol.</p>
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Aguachile verde y negro, ceviche de camaron, pescado zarandeado, tamales de camaron (Nayarit), camarones a la diabla, campechana de mariscos.</p>
                </div>
            </div>

            <!-- 3. Centro-Bajio -->
            <div class="border-l-4 border-red-500 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">3. Bajio y Centro-Occidente — Cocina Barroca</h3>
                <p class="text-sm text-red-600 font-medium mb-3">Jalisco, Michoacan, Guanajuato, Queretaro, Aguascalientes, San Luis Potosi</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Esta region dio a Mexico algunos de sus platillos mas emblematicos e internacionalmente conocidos: las carnitas michoacanas, la birria tapatia, el pozole, la cajeta de Celaya. Es una cocina de fiesta, de tradicion y de sabores profundos construidos a fuego lento.
                </p>
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-red-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Chile chivo (guajillo), chile ancho, canela, clavo, manteca de cerdo, maiz azul, leche de cabra, queso cotija, tequila y mezcal de Jalisco.</p>
                    <p class="text-xs font-bold text-red-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Carnitas michoacanas, birria de chivo o res, pozole rojo y blanco, enchiladas mineras, caldo michi (Jalisco), chiles rellenos de Queretaro, corundas y uchepos de Michoacan.</p>
                </div>
            </div>

            <!-- 4. Centro (CDMX) -->
            <div class="border-l-4 border-gray-600 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">4. Centro — Ciudad de Mexico y Estado de Mexico</h3>
                <p class="text-sm text-gray-600 font-medium mb-3">CDMX, Estado de Mexico, Morelos, Hidalgo, Tlaxcala, Puebla</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    La Ciudad de Mexico es la mayor importadora de cocinas regionales del mundo: todo lo que se cocina en Mexico eventualmente llega a la capital y se adapta al paladar chilango. Pero la cocina original del centro tiene identidad propia: el pulque, el barbacoa de hoyo hidalguense, la cocina de Puebla y los insectos comestibles.
                </p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Epazote, chile chipotle, huitlacoche (hongo del maiz), flor de calabaza, nopales, chapulines, escamoles, penca de maguey, pulque.</p>
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Tacos de suadero y tripa (CDMX), barbacoa de hoyo (Hidalgo), chiles en nogada (Puebla), mole poblano, cemitas poblanas, mixiotes de carnero, tlacoyos de frijol.</p>
                </div>
            </div>

            <!-- 5. Oaxaca -->
            <div class="border-l-4 border-emerald-600 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">5. Oaxaca — La Cocina mas Compleja de Mexico</h3>
                <p class="text-sm text-emerald-700 font-medium mb-3">Oaxaca</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Oaxaca tiene 7 moles reconocidos (negro, rojo, coloradito, amarillo, verde, chichilo, manchamanteles), una cultura del maiz que data de 8,000 anos, el queso de hebra mas famoso del pais (quesillo) y el destilado artesanal mas complejo del mundo (mezcal de agave espadín y muchas otras variedades). No es exagerado decir que Oaxaca es la cocina mas importante de Mexico.
                </p>
                <div class="bg-emerald-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Chile negro (mulato), chile pasilla oaxaqueno, chile chilhuacle, chocolate oaxaqueno, quesillo, asiento (manteca sin refinar), hoja de hierbasanta, mezcal artesanal, tasajo, cecina.</p>
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Mole negro con pavo, tlayudas, tamales oaxaquenos en hoja de platano, memelas, tetelas, enfrijoladas, estofado, chileajo, chapulines tostados, chocolate caliente batido.</p>
                </div>
            </div>

            <!-- 6. Veracruz-Golfo -->
            <div class="border-l-4 border-teal-500 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">6. Veracruz y el Golfo — Mestizaje Caribeno</h3>
                <p class="text-sm text-teal-600 font-medium mb-3">Veracruz, Tabasco, partes de Tamaulipas</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    La cocina de Veracruz es la que mas influencia espanola y africana tiene en Mexico, gracias a que fue el principal puerto de entrada durante la colonia. Olivas, alcaparras y aceite de oliva conviven con epazote y chile chipotle. El arroz a la veracruzana y los mariscos del Golfo son su emblema.
                </p>
                <div class="bg-teal-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-teal-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Aceituna, alcaparra, azafran, chile chipotle seco, jitomate, platano macho, vainilla de Papantla, chile gordo veracruzano, camaron del Golfo.</p>
                    <p class="text-xs font-bold text-teal-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Huachinango a la veracruzana, arroz con mariscos, tamales de rajas en hoja de maiz, caldo de mariscos del Golfo, tostadas de camaron, zacahuil (tamal gigante huasteco), mondongo.</p>
                </div>
            </div>

            <!-- 7. Sureste -->
            <div class="border-l-4 border-orange-500 pl-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">7. Sureste — Yucatan, Chiapas y la Herencia Maya</h3>
                <p class="text-sm text-orange-600 font-medium mb-3">Yucatan, Campeche, Quintana Roo, Chiapas</p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    La cocina del sureste es la que mas fielmente preserva la herencia culinaria maya. El uso del achiote, la hoja de platano, el chile habanero (el mas picante de Mexico), la naranja agria y la tecnica de coccion en hoyo (pib) son caracteristicas que no se encuentran en ninguna otra region.
                </p>
                <div class="bg-orange-50 rounded-lg p-4">
                    <p class="text-xs font-bold text-orange-700 uppercase tracking-wide mb-2">Ingredientes iconicos</p>
                    <p class="text-sm text-gray-700">Achiote (recado rojo), chile habanero, naranja agria, hoja de platano, chaya (hoja verde yucateca), pepita de calabaza, chocolate (cacao maya), chile xcatic.</p>
                    <p class="text-xs font-bold text-orange-700 uppercase tracking-wide mt-3 mb-2">Platillos emblematicos</p>
                    <p class="text-sm text-gray-700">Cochinita pibil, queso relleno yucateco, sopa de lima, panuchos y salbutes, poc chuc (cerdo asado en naranja agria), papadzules (enchiladas con pepita), cocido de res yucateco.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-r from-emerald-700 to-emerald-600 rounded-2xl p-8 text-white mb-10">
        <h2 class="text-2xl font-bold mb-3">Busca Restaurantes por Cocina Regional</h2>
        <p class="opacity-90 mb-6">Filtra por tipo de cocina mexicana y encuentra restaurantes de tu region favorita cerca de ti.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/restaurantes') }}" class="bg-white text-emerald-700 font-bold px-6 py-3 rounded-full hover:bg-emerald-50 transition inline-block">
                Explorar restaurantes
            </a>
            <a href="{{ url('/gastronomia/antojitos-mexicanos') }}" class="border border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 transition inline-block">
                Guia de antojitos mexicanos
            </a>
        </div>
    </section>

    <!-- Cross-link MF Imports -->
    <section class="border border-gray-200 rounded-xl p-6 mb-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Equipa tu restaurante de cocina regional mexicana</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Mobiliario y decoracion autentica para cada tipo de restaurante mexicano: desde la rusticidad del norte hasta la elegancia de Oaxaca. Cazuelas, comals, mesas de madera y mas.</p>
        </div>
        <a href="https://mf-imports.com?utm_source=famer&utm_medium=gastronomia&utm_campaign=regional" target="_blank" rel="noopener" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-full transition whitespace-nowrap">
            Ver mobiliario autentico mexicano
        </a>
    </section>

    <!-- FAQ -->
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preguntas Frecuentes sobre Cocina Mexicana Regional</h2>
        <div class="space-y-4">
            @foreach([
                ['Cuantas regiones gastronomicas tiene Mexico?', 'Al menos 7 grandes regiones: Norte, Noroeste-Pacifico, Centro-Bajio, Centro (CDMX/Puebla), Oaxaca, Veracruz-Golfo y el Sureste (Yucatan/Chiapas).'],
                ['Cual es la region gastronomica mas reconocida de Mexico?', 'Oaxaca es internacionalmente la mas reconocida por sus 7 moles, el quesillo, el mezcal artesanal y la diversidad de chiles y tecnicas ancestrales.'],
                ['Que ingredientes son unicos de la cocina del norte de Mexico?', 'Carne seca, machaca, frijol pinto, tortilla de harina de trigo, chile colorado y queso chihuahua. La barbacoa de borrego en hoyo es emblema del norte.'],
                ['Que es la cocina yucateca y como se diferencia del resto?', 'Tiene influencia maya, espanola y caribena. Usa achiote, naranja agria, chile habanero y coccion en hoyo (pib). El cochinita pibil y los panuchos son sus platillos mas conocidos.'],
                ['Que estados componen la region gastronomica del Bajio?', 'Principalmente Guanajuato, Queretaro, Aguascalientes, San Luis Potosi y partes de Michoacan y Jalisco. Famosa por carnitas, birria y la cajeta de Celaya.']
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
