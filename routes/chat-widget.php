<?php
// Chat Widget API Routes for Restaurantes Mexicanos Famosos
// Updated with owner-specific responses

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Route::prefix('chat')->group(function () {

    // Get featured restaurants
    Route::get('/restaurants/featured', function () {
        try {
            $restaurants = DB::table('restaurants')
                ->leftJoin('states', 'restaurants.state_id', '=', 'states.id')
                ->where('restaurants.is_active', 1)
                ->where('restaurants.is_featured', 1)
                ->orderByDesc('restaurants.average_rating')
                ->limit(4)
                ->get([
                    'restaurants.id', 'restaurants.name', 'restaurants.slug',
                    'restaurants.city', 'restaurants.average_rating',
                    'restaurants.total_reviews', 'restaurants.image',
                    'states.name as state_name'
                ]);

            return response()->json(['restaurants' => $restaurants]);
        } catch (\Exception $e) {
            Log::error('Chat restaurants featured error: ' . $e->getMessage());
            return response()->json(['restaurants' => []]);
        }
    });

    // Get nearby restaurants
    Route::get('/restaurants/nearby', function (Request $request) {
        try {
            $lat = floatval($request->get('lat', 0));
            $lng = floatval($request->get('lng', 0));

            if ($lat === 0.0 || $lng === 0.0) {
                return response()->json(['restaurants' => []]);
            }

            $restaurants = DB::table('restaurants')
                ->leftJoin('states', 'restaurants.state_id', '=', 'states.id')
                ->where('restaurants.is_active', 1)
                ->whereNotNull('restaurants.latitude')
                ->whereNotNull('restaurants.longitude')
                ->selectRaw("restaurants.*, states.name as state_name,
                    (3959 * acos(cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?)) + sin(radians(?))
                    * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
                ->having('distance', '<', 25)
                ->orderBy('distance')
                ->limit(5)
                ->get();

            return response()->json(['restaurants' => $restaurants]);
        } catch (\Exception $e) {
            Log::error('Chat restaurants nearby error: ' . $e->getMessage());
            return response()->json(['restaurants' => []]);
        }
    });

    // Search restaurants
    Route::get('/restaurants/search', function (Request $request) {
        try {
            $query = $request->get('q', '');
            $city = $request->get('city', '');

            $restaurants = DB::table('restaurants')
                ->leftJoin('states', 'restaurants.state_id', '=', 'states.id')
                ->leftJoin('categories', 'restaurants.category_id', '=', 'categories.id')
                ->where('restaurants.is_active', 1)
                ->where(function ($q) use ($query, $city) {
                    if ($query) {
                        $q->where('restaurants.name', 'LIKE', "%{$query}%")
                          ->orWhere('categories.name', 'LIKE', "%{$query}%")
                          ->orWhere('restaurants.mexican_region', 'LIKE', "%{$query}%");
                    }
                    if ($city) {
                        $q->where('restaurants.city', 'LIKE', "%{$city}%");
                    }
                })
                ->orderByDesc('restaurants.average_rating')
                ->limit(5)
                ->get([
                    'restaurants.id', 'restaurants.name', 'restaurants.slug',
                    'restaurants.city', 'restaurants.average_rating',
                    'restaurants.total_reviews', 'restaurants.image',
                    'states.name as state_name', 'categories.name as category_name'
                ]);

            return response()->json(['restaurants' => $restaurants]);
        } catch (\Exception $e) {
            Log::error('Chat restaurants search error: ' . $e->getMessage());
            return response()->json(['restaurants' => []]);
        }
    });

    // Process message
    Route::post('/message', function (Request $request) {
        try {
            $message = $request->input('message', '');
            $sessionId = $request->input('session_id', '');
            $language = $request->input('language', 'es');
            $isOwnerPage = $request->input('is_owner_page', false);

            // Store message
            try {
                DB::table('chat_messages')->insert([
                    'session_id' => $sessionId,
                    'message' => $message,
                    'sender' => 'user',
                    'page_type' => $isOwnerPage ? 'owner' : 'visitor',
                    'created_at' => now()
                ]);
            } catch (\Exception $e) {}

            $lowerMessage = strtolower($message);
            $response = '';
            $html = false;

            // ============================================
            // OWNER-SPECIFIC RESPONSES (when on owner pages)
            // ============================================
            if ($isOwnerPage) {

                // Pricing/cost questions
                if (preg_match('/(precio|cost|cuanto|how much|tarifa|rate|cobr|charge|pago|payment|plan)/i', $message)) {
                    $response = $language === 'es' ?
                        'Tenemos 3 planes:\n\n• GRATIS - Perfil basico, responder resenas\n• PRO ($39/mes) - Menu digital, fotos ilimitadas, reservaciones\n• PREMIUM ($79/mes) - Anuncios destacados, analytics, soporte VIP\n\n¿Te gustaria comenzar con el plan gratuito?' :
                        'We have 3 plans:\n\n• FREE - Basic profile, respond to reviews\n• PRO ($39/mo) - Digital menu, unlimited photos, reservations\n• PREMIUM ($79/mo) - Featured listings, analytics, VIP support\n\nWould you like to start with the free plan?';
                }
                // Claim/register questions
                elseif (preg_match('/(reclamar|claim|registr|register|como|how|empez|start|unir|join)/i', $message)) {
                    $response = $language === 'es' ?
                        '¡Es muy facil! Solo necesitas:\n\n1. Buscar tu restaurante en /claim\n2. Verificar que eres el dueno (email o telefono)\n3. ¡Listo! Tu perfil esta activo\n\nTodo el proceso toma menos de 5 minutos y es 100% GRATIS.' :
                        'It\'s very easy! You just need to:\n\n1. Search for your restaurant at /claim\n2. Verify you\'re the owner (email or phone)\n3. Done! Your profile is active\n\nThe whole process takes less than 5 minutes and is 100% FREE.';
                }
                // Benefits questions
                elseif (preg_match('/(beneficio|benefit|ventaja|advantage|porque|why|vale|worth|sirve|help)/i', $message)) {
                    $response = $language === 'es' ?
                        'Al registrar tu restaurante obtendras:\n\n✅ Visibilidad en busquedas locales\n✅ Responder a resenas de clientes\n✅ Estadisticas de visitas y clicks\n✅ Badge de "Verificado"\n✅ Menu y fotos actualizables\n\nMiles de personas buscan restaurantes aqui cada dia. ¿Tu restaurante esta visible?' :
                        'By registering your restaurant you\'ll get:\n\n✅ Visibility in local searches\n✅ Respond to customer reviews\n✅ Visit and click statistics\n✅ "Verified" badge\n✅ Updatable menu and photos\n\nThousands of people search for restaurants here every day. Is your restaurant visible?';
                }
                // FAMER/grader questions
                elseif (preg_match('/(famer|grader|califica|score|puntua|rating|evalua)/i', $message)) {
                    $response = $language === 'es' ?
                        'El FAMER Score analiza tu restaurante en 5 areas:\n\n📸 Fotos y presentacion\n⭐ Resenas y reputacion\n📱 Presencia digital\n📍 Informacion completa\n🎯 Engagement con clientes\n\nEs GRATIS y te da recomendaciones para mejorar. ¿Quieres calificar tu restaurante ahora?' :
                        'The FAMER Score analyzes your restaurant in 5 areas:\n\n📸 Photos and presentation\n⭐ Reviews and reputation\n📱 Digital presence\n📍 Complete information\n🎯 Customer engagement\n\nIt\'s FREE and gives you recommendations to improve. Want to grade your restaurant now?';
                }
                // Verification questions
                elseif (preg_match('/(verifica|verify|comprueba|prove|confirma|confirm|dueno|owner)/i', $message)) {
                    $response = $language === 'es' ?
                        'Para verificar que eres el dueno, puedes:\n\n📧 Recibir codigo por email del negocio\n📱 Recibir codigo por telefono del negocio\n📄 Subir documento (licencia, permiso)\n\nLa verificacion es rapida y segura. ¿Cual metodo prefieres?' :
                        'To verify you\'re the owner, you can:\n\n📧 Receive code via business email\n📱 Receive code via business phone\n📄 Upload document (license, permit)\n\nVerification is quick and secure. Which method do you prefer?';
                }
                // Support/help
                elseif (preg_match('/(ayuda|help|soporte|support|problema|problem|error|no puedo|cant|issue)/i', $message)) {
                    $response = $language === 'es' ?
                        'Estoy aqui para ayudarte. Puedes:\n\n💬 Escribirme tu pregunta aqui\n📱 WhatsApp: +1 (214) 987-6068\n📧 Email: owners@restaurantesmexicanos.com\n\n¿Cual es el problema que tienes?' :
                        'I\'m here to help. You can:\n\n💬 Write your question here\n📱 WhatsApp: +1 (214) 987-6068\n📧 Email: owners@restaurantesmexicanos.com\n\nWhat problem are you having?';
                }
                // Greeting on owner page
                elseif (preg_match('/^(hola|hi|hello|buenos|buenas|hey)/i', $message)) {
                    $response = $language === 'es' ?
                        '¡Hola! Soy Carmen, asesora para duenos de restaurantes. Estoy aqui para ayudarte a:\n\n• Reclamar tu restaurante (GRATIS)\n• Conocer los beneficios de estar listado\n• Elegir el plan ideal para ti\n\n¿Ya tienes tu restaurante en nuestro directorio?' :
                        'Hi! I\'m Carmen, advisor for restaurant owners. I\'m here to help you:\n\n• Claim your restaurant (FREE)\n• Learn the benefits of being listed\n• Choose the ideal plan for you\n\nIs your restaurant already in our directory?';
                }
                // Default owner response
                else {
                    $response = $language === 'es' ?
                        'Como dueno de restaurante, puedo ayudarte con:\n\n• Reclamar tu restaurante gratis\n• Conocer nuestros planes y precios\n• Calificar tu restaurante con FAMER Score\n• Resolver dudas sobre el proceso\n\n¿Que te gustaria saber?' :
                        'As a restaurant owner, I can help you with:\n\n• Claiming your restaurant for free\n• Learning about our plans and pricing\n• Rating your restaurant with FAMER Score\n• Answering questions about the process\n\nWhat would you like to know?';
                }
            }
            // ============================================
            // VISITOR RESPONSES (regular pages)
            // ============================================
            else {
                // Restaurant search patterns
                if (preg_match('/(restaurante|restaurant|busco|quiero|encuentra|find|looking for|cerca|near|en |in )/i', $message)) {
                    $searchTerms = preg_replace('/^(busco|necesito|quiero|encuentra|looking for|find me|show me)\s*/i', '', $message);

                    $city = '';
                    if (preg_match('/\b(en|in|cerca de|near)\s+([a-zA-Z\s]+)/i', $message, $matches)) {
                        $city = trim($matches[2]);
                    }

                    $restaurants = DB::table('restaurants')
                        ->leftJoin('states', 'restaurants.state_id', '=', 'states.id')
                        ->where('restaurants.is_active', 1)
                        ->where(function ($q) use ($searchTerms, $city) {
                            $q->where('restaurants.name', 'LIKE', "%{$searchTerms}%")
                              ->orWhere('restaurants.mexican_region', 'LIKE', "%{$searchTerms}%");
                            if ($city) {
                                $q->orWhere('restaurants.city', 'LIKE', "%{$city}%");
                            }
                        })
                        ->orderByDesc('restaurants.average_rating')
                        ->limit(3)
                        ->get();

                    if ($restaurants->count() > 0) {
                        $response = $language === 'es' ? 'Encontre estos restaurantes:' : 'I found these restaurants:';
                        foreach ($restaurants as $r) {
                            $stars = str_repeat('★', round($r->average_rating)) . str_repeat('☆', 5 - round($r->average_rating));
                            $response .= '<div class="rmf-restaurant-card">';
                            $response .= '<h4>' . htmlspecialchars($r->name) . '</h4>';
                            $response .= '<div class="rating">' . $stars . ' (' . $r->total_reviews . ')</div>';
                            $response .= '<div class="location">📍 ' . htmlspecialchars($r->city) . '</div>';
                            $response .= '<a href="/restaurantes/' . $r->slug . '" class="btn">' . ($language === 'es' ? 'Ver detalles' : 'View details') . '</a>';
                            $response .= '</div>';
                        }
                        $html = true;
                    } else {
                        $response = $language === 'es' ?
                            'No encontre restaurantes con ese criterio. ¿Quieres buscar por tipo de comida o ciudad?' :
                            'I couldn\'t find restaurants with that criteria. Want to search by food type or city?';
                    }
                }
                // Food type queries
                elseif (preg_match('/(tacos|burrito|enchilada|tamale|pozole|mole|carnitas|birria|torta|quesadilla|marisco|seafood|oaxaque|jalisco|yucate|veracruz)/i', $message, $matches)) {
                    $foodType = $matches[1];

                    $restaurants = DB::table('restaurants')
                        ->leftJoin('states', 'restaurants.state_id', '=', 'states.id')
                        ->leftJoin('categories', 'restaurants.category_id', '=', 'categories.id')
                        ->where('restaurants.is_active', 1)
                        ->where(function ($q) use ($foodType) {
                            $q->where('restaurants.name', 'LIKE', "%{$foodType}%")
                              ->orWhere('categories.name', 'LIKE', "%{$foodType}%")
                              ->orWhere('restaurants.mexican_region', 'LIKE', "%{$foodType}%");
                        })
                        ->orderByDesc('restaurants.average_rating')
                        ->limit(3)
                        ->get();

                    if ($restaurants->count() > 0) {
                        $response = $language === 'es' ?
                            "¡Excelente! Estos restaurantes tienen {$foodType}:" :
                            "Excellent! These restaurants have {$foodType}:";
                        foreach ($restaurants as $r) {
                            $stars = str_repeat('★', round($r->average_rating)) . str_repeat('☆', 5 - round($r->average_rating));
                            $response .= '<div class="rmf-restaurant-card">';
                            $response .= '<h4>' . htmlspecialchars($r->name) . '</h4>';
                            $response .= '<div class="rating">' . $stars . '</div>';
                            $response .= '<div class="location">📍 ' . htmlspecialchars($r->city) . '</div>';
                            $response .= '<a href="/restaurantes/' . $r->slug . '" class="btn">' . ($language === 'es' ? 'Ver detalles' : 'View details') . '</a>';
                            $response .= '</div>';
                        }
                        $html = true;
                    } else {
                        $response = $language === 'es' ?
                            "No encontre restaurantes de {$foodType}. ¿Quieres ver los mejor calificados?" :
                            "I couldn't find {$foodType} restaurants. Want to see top-rated ones?";
                    }
                }
                // Owner/registration inquiry from visitor
                elseif (preg_match('/(registr|agregar|añadir|listar|register|add|list my|mi negocio|my business|owner|dueno|dueño)/i', $message)) {
                    $response = $language === 'es' ?
                        '¿Tienes un restaurante mexicano? ¡Registralo GRATIS! Visita /for-owners para conocer los beneficios o ve directo a /claim para reclamar tu negocio.' :
                        'Do you own a Mexican restaurant? Register it FREE! Visit /for-owners to learn the benefits or go directly to /claim to claim your business.';
                }
                // Reviews inquiry
                elseif (preg_match('/(opinion|resena|reseña|review|calific|rating|estrellas|stars)/i', $message)) {
                    $response = $language === 'es' ?
                        'Las resenas ayudan a otros a encontrar buenos restaurantes. ¿Quieres ver los mejor calificados o dejar una resena?' :
                        'Reviews help others find good restaurants. Want to see top-rated ones or leave a review?';
                }
                // Greeting
                elseif (preg_match('/^(hola|hi|hello|buenos|buenas|hey)/i', $message)) {
                    $response = $language === 'es' ?
                        '¡Hola! Soy Carmen, tu guia de restaurantes mexicanos. ¿Que tipo de comida se te antoja hoy?' :
                        'Hi! I\'m Carmen, your Mexican restaurant guide. What type of food are you craving today?';
                }
                // Contact
                elseif (preg_match('/(contact|hablar|ayuda|help|humano|person)/i', $message)) {
                    $response = $language === 'es' ?
                        '¡Con gusto! WhatsApp: +1 (214) 987-6068 o email: contacto@restaurantesmexicanos.com' :
                        'Happy to help! WhatsApp: +1 (214) 987-6068 or email: contacto@restaurantesmexicanos.com';
                }
                // Default visitor response
                else {
                    $response = $language === 'es' ?
                        'Puedo ayudarte a encontrar restaurantes mexicanos. ¿Buscas algun tipo de comida (tacos, mariscos) o en alguna ciudad?' :
                        'I can help you find Mexican restaurants. Looking for a specific food type (tacos, seafood) or in a particular city?';
                }
            }

            // Store bot response
            try {
                DB::table('chat_messages')->insert([
                    'session_id' => $sessionId,
                    'message' => strip_tags($response),
                    'sender' => 'bot',
                    'page_type' => $isOwnerPage ? 'owner' : 'visitor',
                    'created_at' => now()
                ]);
            } catch (\Exception $e) {}

            return response()->json([
                'response' => $response,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            Log::error('Chat message error: ' . $e->getMessage());
            return response()->json([
                'response' => 'Lo siento, hubo un error. Por favor intenta de nuevo.',
                'html' => false
            ]);
        }
    });
});
