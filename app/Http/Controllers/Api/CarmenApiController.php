<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\ClaimVerification;
use Illuminate\Http\Request;

class CarmenApiController extends Controller
{
    /**
     * Buscar estado de una orden por numero de orden o telefono
     */
    public function orderStatus(Request $request)
    {
        $orderNumber = $request->input('order_number');
        $phone = $request->input('phone');

        $query = Order::with(['restaurant:id,name,phone', 'items']);

        if ($orderNumber) {
            $query->where('order_number', $orderNumber);
        } elseif ($phone) {
            $query->where('customer_phone', $phone)->latest();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere numero de orden o telefono'
            ], 400);
        }

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontro ninguna orden con esos datos'
            ]);
        }

        $statusMessages = [
            'pending' => 'Pendiente - El restaurante aun no ha confirmado tu orden',
            'confirmed' => 'Confirmada - El restaurante esta preparando tu orden',
            'preparing' => 'En preparacion - Tu comida se esta cocinando',
            'ready' => 'Lista - Tu orden esta lista para recoger o en camino',
            'delivered' => 'Entregada - Tu orden fue entregada exitosamente',
            'cancelled' => 'Cancelada - Esta orden fue cancelada'
        ];

        return response()->json([
            'success' => true,
            'order' => [
                'number' => $order->order_number,
                'status' => $order->status,
                'status_description' => $statusMessages[$order->status] ?? $order->status,
                'restaurant' => $order->restaurant->name,
                'restaurant_phone' => $order->restaurant->phone,
                'total' => number_format($order->total, 2),
                'items_count' => $order->items->count(),
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'estimated_time' => $order->estimated_time ?? '30-45 minutos'
            ]
        ]);
    }

    /**
     * Buscar estado de una reservacion
     */
    public function reservationStatus(Request $request)
    {
        $confirmationCode = $request->input('confirmation_code');
        $phone = $request->input('phone');

        $query = Reservation::with(['restaurant:id,name,phone,address']);

        if ($confirmationCode) {
            $query->where('confirmation_code', $confirmationCode);
        } elseif ($phone) {
            $query->where('phone', $phone)
                  ->where('reservation_date', '>=', now()->subDay())
                  ->latest();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere codigo de confirmacion o telefono'
            ], 400);
        }

        $reservation = $query->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontro ninguna reservacion con esos datos'
            ]);
        }

        $statusMessages = [
            'pending' => 'Pendiente de confirmacion por el restaurante',
            'confirmed' => 'Confirmada - Te esperamos!',
            'cancelled' => 'Cancelada',
            'completed' => 'Completada - Gracias por tu visita'
        ];

        return response()->json([
            'success' => true,
            'reservation' => [
                'confirmation_code' => $reservation->confirmation_code,
                'status' => $reservation->status,
                'status_description' => $statusMessages[$reservation->status] ?? $reservation->status,
                'restaurant' => $reservation->restaurant->name,
                'restaurant_phone' => $reservation->restaurant->phone,
                'restaurant_address' => $reservation->restaurant->address,
                'date' => $reservation->reservation_date->format('l, d F Y'),
                'time' => $reservation->reservation_time->format('h:i A'),
                'guests' => $reservation->guests,
                'name' => $reservation->name
            ]
        ]);
    }

    /**
     * Buscar restaurantes por nombre, ciudad o tipo de comida
     */
    public function searchRestaurant(Request $request)
    {
        $query = $request->input('query');
        $city = $request->input('city');
        $cuisine = $request->input('cuisine_type');

        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->where('name', 'like', "%{$query}%")
                       ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->when($city, function ($q) use ($city) {
                $q->where('city', 'like', "%{$city}%");
            })
            ->when($cuisine, function ($q) use ($cuisine) {
                $q->where('cuisine_type', 'like', "%{$cuisine}%");
            })
            ->select('id', 'name', 'phone', 'address', 'city', 'cuisine_type', 'rating', 'is_verified')
            ->limit(5)
            ->get();

        if ($restaurants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron restaurantes con esos criterios'
            ]);
        }

        return response()->json([
            'success' => true,
            'count' => $restaurants->count(),
            'restaurants' => $restaurants->map(function ($r) {
                return [
                    'name' => $r->name,
                    'phone' => $r->phone,
                    'address' => $r->address,
                    'city' => $r->city,
                    'cuisine' => $r->cuisine_type,
                    'rating' => $r->rating ? number_format($r->rating, 1) . '/5' : 'Sin calificacion',
                    'verified' => $r->is_verified ? 'Verificado' : 'No verificado'
                ];
            })
        ]);
    }

    /**
     * Obtener informacion completa de un restaurante
     */
    public function restaurantInfo(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurantName = $request->input('restaurant_name');

        $query = Restaurant::with(['menuItems' => function ($q) {
            $q->where('is_available', true)->limit(10);
        }]);

        if ($restaurantId) {
            $restaurant = $query->find($restaurantId);
        } elseif ($restaurantName) {
            $restaurant = $query->where('name', 'like', "%{$restaurantName}%")->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere ID o nombre del restaurante'
            ], 400);
        }

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurante no encontrado'
            ]);
        }

        return response()->json([
            'success' => true,
            'restaurant' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'description' => $restaurant->description,
                'phone' => $restaurant->phone,
                'address' => $restaurant->address,
                'city' => $restaurant->city,
                'state' => $restaurant->state,
                'cuisine_type' => $restaurant->cuisine_type,
                'hours' => $restaurant->business_hours,
                'rating' => $restaurant->rating,
                'is_verified' => $restaurant->is_verified,
                'accepts_reservations' => $restaurant->accepts_reservations,
                'accepts_orders' => $restaurant->accepts_online_orders,
                'delivery_available' => $restaurant->has_delivery,
                'popular_items' => $restaurant->menuItems->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'price' => '$' . number_format($item->price, 2),
                        'description' => $item->description
                    ];
                })
            ]
        ]);
    }

    /**
     * Verificar estado de reclamacion de restaurante
     */
    public function claimStatus(Request $request)
    {
        $email = $request->input('email');
        $phone = $request->input('phone');
        $restaurantName = $request->input('restaurant_name');

        $query = ClaimVerification::with(['restaurant:id,name']);

        if ($email) {
            $query->where('email', $email);
        } elseif ($phone) {
            $query->where('phone', $phone);
        } elseif ($restaurantName) {
            $query->whereHas('restaurant', function ($q) use ($restaurantName) {
                $q->where('name', 'like', "%{$restaurantName}%");
            });
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere email, telefono o nombre del restaurante'
            ], 400);
        }

        $claim = $query->latest()->first();

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontro ninguna solicitud de reclamacion'
            ]);
        }

        $statusMessages = [
            'pending' => 'Pendiente - Tu solicitud esta siendo revisada (1-3 dias habiles)',
            'in_review' => 'En revision - Estamos verificando la informacion',
            'approved' => 'Aprobada - Ya puedes acceder a tu panel de administracion',
            'rejected' => 'Rechazada - Contacta soporte para mas informacion'
        ];

        return response()->json([
            'success' => true,
            'claim' => [
                'restaurant' => $claim->restaurant->name ?? 'N/A',
                'status' => $claim->status,
                'status_description' => $statusMessages[$claim->status] ?? $claim->status,
                'submitted_at' => $claim->created_at->format('d/m/Y'),
                'contact_email' => $claim->email
            ]
        ]);
    }

    /**
     * Resumen del dashboard para dueno de restaurante
     */
    public function ownerSummary(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $email = $request->input('owner_email');

        if ($email) {
            $restaurant = Restaurant::where('owner_email', $email)->first();
            if ($restaurant) {
                $restaurantId = $restaurant->id;
            }
        }

        if (!$restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere ID del restaurante o email del dueno'
            ], 400);
        }

        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurante no encontrado'
            ]);
        }

        // Estadisticas del dia
        $todayOrders = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', today())
            ->get();

        $todayReservations = Reservation::where('restaurant_id', $restaurantId)
            ->whereDate('reservation_date', today())
            ->get();

        // Estadisticas del mes
        $monthOrders = Order::where('restaurant_id', $restaurantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        return response()->json([
            'success' => true,
            'restaurant' => $restaurant->name,
            'today' => [
                'orders' => $todayOrders->count(),
                'orders_revenue' => '$' . number_format($todayOrders->sum('total'), 2),
                'pending_orders' => $todayOrders->where('status', 'pending')->count(),
                'reservations' => $todayReservations->count(),
                'confirmed_reservations' => $todayReservations->where('status', 'confirmed')->count()
            ],
            'month' => [
                'total_orders' => $monthOrders->count(),
                'total_revenue' => '$' . number_format($monthOrders->sum('total'), 2),
                'average_order' => '$' . number_format($monthOrders->avg('total') ?? 0, 2)
            ],
            'subscription' => [
                'plan' => $restaurant->subscription_plan ?? 'free',
                'status' => $restaurant->subscription_status ?? 'active',
                'expires_at' => $restaurant->subscription_expires_at ? $restaurant->subscription_expires_at->format('d/m/Y') : 'N/A'
            ]
        ]);
    }

    /**
     * Obtener sugerencias de restaurantes basadas en ubicación y preferencias
     * Usado cuando el usuario inicia el chat
     */
    public function getSuggestions(Request $request)
    {
        $city = $request->input("city");
        $state = $request->input("state");
        $foodType = $request->input("food_type");
        $country = $request->input("country", "US");
        
        $query = Restaurant::where("status", "approved")
            ->where("country", $country)
            ->with(["category", "state"]);
        
        // Filtrar por ubicación si está disponible
        if ($city) {
            $query->where("city", "like", "%{$city}%");
        } elseif ($state) {
            $query->whereHas("state", function($q) use ($state) {
                $q->where("name", "like", "%{$state}%")
                  ->orWhere("code", strtoupper($state));
            });
        }
        
        // Filtrar por tipo de comida
        if ($foodType) {
            $foodType = strtolower($foodType);
            $query->where(function($q) use ($foodType) {
                $q->where("name", "like", "%{$foodType}%")
                  ->orWhere("description", "like", "%{$foodType}%")
                  ->orWhereHas("category", function($q2) use ($foodType) {
                      $q2->where("name", "like", "%{$foodType}%");
                  });
            });
        }
        
        $restaurants = $query->orderByDesc("average_rating")
            ->orderByDesc("total_reviews")
            ->limit(5)
            ->get();
        
        if ($restaurants->isEmpty()) {
            // Fallback: buscar cualquier restaurante en el país
            $restaurants = Restaurant::where("status", "approved")
                ->where("country", $country)
                ->orderByDesc("average_rating")
                ->limit(5)
                ->get();
        }
        
        $currency = $country === "MX" ? "MXN" : "USD";
        $locationText = $city ? "en {$city}" : ($state ? "en {$state}" : "");
        
        return response()->json([
            "success" => true,
            "location" => $locationText,
            "currency" => $currency,
            "count" => $restaurants->count(),
            "suggestions" => $restaurants->map(function($r) use ($currency) {
                $priceRange = $currency === "MXN" 
                    ? "$" . rand(80, 150) . "-$" . rand(200, 350) . " MXN"
                    : "$" . rand(10, 20) . "-$" . rand(25, 45) . " USD";
                    
                return [
                    "id" => $r->id,
                    "name" => $r->name,
                    "slug" => $r->slug,
                    "city" => $r->city,
                    "state" => $r->state->code ?? "",
                    "category" => $r->category->name ?? "Mexicano",
                    "rating" => number_format($r->average_rating ?? 4.0, 1),
                    "reviews" => $r->total_reviews ?? 0,
                    "price_range" => $priceRange,
                    "phone" => $r->phone,
                    "url" => "https://restaurantesmexicanosfamosos.com/restaurante/{$r->slug}"
                ];
            }),
            "quick_options" => [
                "🌮 Tacos",
                "🦐 Mariscos", 
                "🍲 Birria",
                "🫔 Antojitos",
                "🥗 Comida Oaxaqueña",
                "📍 Cerca de mi"
            ]
        ]);
    }
    
    /**
     * Mensaje de bienvenida inicial para Carmen
     */
    public function getWelcomeMessage(Request $request)
    {
        $country = $request->input("country", "US");
        $city = $request->input("city");
        
        $greeting = $city 
            ? "¡Hola! 👋 Soy Carmen, tu asistente de Restaurantes Mexicanos Famosos. Veo que estás en {$city}."
            : "¡Hola! 👋 Soy Carmen, tu asistente de Restaurantes Mexicanos Famosos.";
        
        return response()->json([
            "success" => true,
            "message" => $greeting . "\n\n¿Qué tipo de comida mexicana te gustaría hoy?",
            "quick_replies" => [
                ["text" => "🌮 Tacos", "value" => "tacos"],
                ["text" => "🦐 Mariscos", "value" => "mariscos"],
                ["text" => "🍲 Birria", "value" => "birria"],
                ["text" => "🫔 Antojitos", "value" => "antojitos"],
                ["text" => "📍 Restaurantes cerca", "value" => "cerca"]
            ]
        ]);
    }

    /**
     * Obtener información de planes de suscripción
     * Para dueños interesados en mejorar su plan
     */
    public function subscriptionPlans(Request $request)
    {
        $currentPlan = $request->input('current_plan', 'free');

        $plans = [
            'free' => [
                'name' => 'Gratuito',
                'price' => '$0',
                'price_monthly' => 0,
                'features' => [
                    '✓ Perfil básico del restaurante',
                    '✓ Aparecer en búsquedas',
                    '✓ Información de contacto',
                    '✗ Sin destacado en búsquedas',
                    '✗ Sin analytics',
                    '✗ Sin cupones'
                ]
            ],
            'premium' => [
                'name' => 'Premium',
                'price_first_month' => '$9.99',
                'price_monthly' => '$39/mes',
                'promo' => '¡Primer mes solo $9.99!',
                'features' => [
                    '✓ Todo lo del plan Gratuito',
                    '✓ Destacado en búsquedas de tu ciudad',
                    '✓ Analytics básicos (visitas, clics)',
                    '✓ Responder a reseñas',
                    '✓ Subir hasta 10 fotos',
                    '✓ Menú digital básico',
                    '✓ Badge de "Verificado"'
                ],
                'best_for' => 'Ideal para restaurantes que quieren más visibilidad'
            ],
            'elite' => [
                'name' => 'Elite',
                'price_monthly' => '$79/mes',
                'features' => [
                    '✓ Todo lo del plan Premium',
                    '✓ Destacado #1 en tu ciudad',
                    '✓ Analytics avanzados (conversiones, tendencias)',
                    '✓ Sistema de cupones y promociones',
                    '✓ Pedidos online integrados',
                    '✓ Reservaciones online',
                    '✓ Fotos ilimitadas',
                    '✓ Menú digital completo con precios',
                    '✓ Soporte prioritario',
                    '✓ Badge "Elite" dorado'
                ],
                'best_for' => 'Para restaurantes que quieren maximizar ventas online'
            ]
        ];

        $recommendation = '';
        if ($currentPlan === 'free') {
            $recommendation = '💡 Te recomiendo el Plan Premium para empezar. ¡El primer mes es solo $9.99! Podrás ver analytics de cuántas personas ven tu restaurante.';
        } elseif ($currentPlan === 'premium') {
            $recommendation = '💡 Con el Plan Elite podrías activar pedidos y reservaciones online, y aparecer primero en las búsquedas de tu ciudad.';
        }

        return response()->json([
            'success' => true,
            'current_plan' => $currentPlan,
            'plans' => $plans,
            'recommendation' => $recommendation,
            'subscribe_url' => 'https://restaurantesmexicanosfamosos.com/owner/subscription',
            'contact' => 'Para ayuda con tu suscripción, escribe a soporte@restaurantesmexicanosfamosos.com'
        ]);
    }

    /**
     * Guía para reclamar un restaurante
     * Proporciona pasos y requisitos
     */
    public function claimGuide(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurantName = $request->input('restaurant_name');

        $restaurant = null;
        if ($restaurantId) {
            $restaurant = Restaurant::find($restaurantId);
        } elseif ($restaurantName) {
            $restaurant = Restaurant::where('name', 'like', "%{$restaurantName}%")->first();
        }

        $alreadyClaimed = $restaurant && $restaurant->is_claimed;

        if ($alreadyClaimed) {
            return response()->json([
                'success' => true,
                'already_claimed' => true,
                'message' => "El restaurante '{$restaurant->name}' ya ha sido reclamado por su dueño. Si crees que esto es un error, contacta a soporte.",
                'support_email' => 'soporte@restaurantesmexicanosfamosos.com'
            ]);
        }

        $steps = [
            '1️⃣ **Busca tu restaurante** en nuestra página',
            '2️⃣ **Haz clic en "Reclamar este negocio"** en la página del restaurante',
            '3️⃣ **Completa el formulario** con tu información de contacto',
            '4️⃣ **Verificación**: Te contactaremos por teléfono o email para verificar que eres el dueño',
            '5️⃣ **¡Listo!** Una vez verificado, tendrás acceso al panel de administración'
        ];

        $requirements = [
            '📋 Ser el dueño o gerente autorizado del restaurante',
            '📞 Teléfono del restaurante para verificación',
            '📧 Email para crear tu cuenta',
            '🆔 Opcional: Documento que acredite la propiedad'
        ];

        $benefits = [
            '✅ Actualizar información, fotos y menú',
            '✅ Responder a reseñas de clientes',
            '✅ Ver estadísticas de visitas',
            '✅ Recibir contactos de clientes interesados',
            '✅ Acceder a planes Premium y Elite'
        ];

        $claimUrl = $restaurant
            ? "https://restaurantesmexicanosfamosos.com/restaurante/{$restaurant->slug}#claim"
            : "https://restaurantesmexicanosfamosos.com/claim";

        return response()->json([
            'success' => true,
            'already_claimed' => false,
            'restaurant' => $restaurant ? [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'city' => $restaurant->city
            ] : null,
            'steps' => $steps,
            'requirements' => $requirements,
            'benefits' => $benefits,
            'claim_url' => $claimUrl,
            'estimated_time' => '1-3 días hábiles para verificación',
            'support_email' => 'soporte@restaurantesmexicanosfamosos.com'
        ]);
    }

    /**
     * Verificar si un restaurante está reclamado y dar opciones
     */
    public function checkRestaurantOwnership(Request $request)
    {
        $restaurantName = $request->input('restaurant_name');
        $city = $request->input('city');

        $query = Restaurant::query();

        if ($restaurantName) {
            $query->where('name', 'like', "%{$restaurantName}%");
        }
        if ($city) {
            $query->where('city', 'like', "%{$city}%");
        }

        $restaurant = $query->first();

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => "No encontré un restaurante con ese nombre. ¿Quieres que te ayude a agregarlo a nuestra plataforma?",
                'add_restaurant_url' => 'https://restaurantesmexicanosfamosos.com/agregar-restaurante'
            ]);
        }

        $response = [
            'success' => true,
            'found' => true,
            'restaurant' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'city' => $restaurant->city,
                'state' => $restaurant->state->name ?? '',
                'is_claimed' => $restaurant->is_claimed,
                'subscription_plan' => $restaurant->subscription_plan ?? 'free',
                'rating' => $restaurant->average_rating,
                'url' => "https://restaurantesmexicanosfamosos.com/restaurante/{$restaurant->slug}"
            ]
        ];

        if ($restaurant->is_claimed) {
            $response['message'] = "✅ '{$restaurant->name}' ya está reclamado. Si eres el dueño y necesitas acceso, contacta a soporte.";
            $response['next_action'] = 'contact_support';
        } else {
            $response['message'] = "🎉 ¡'{$restaurant->name}' está disponible para reclamar! Como dueño, podrás actualizar la información, responder reseñas y acceder a estadísticas.";
            $response['next_action'] = 'claim';
            $response['claim_url'] = "https://restaurantesmexicanosfamosos.com/restaurante/{$restaurant->slug}#claim";
        }

        return response()->json($response);
    }
}
