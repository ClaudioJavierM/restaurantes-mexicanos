<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TwimlController extends Controller
{
    /**
     * Return TwiML to speak verification code for restaurant claim.
     * Called by Twilio when the outbound call connects.
     *
     * URL: /webhooks/twilio/claim-twiml?token={signed_token}
     */
    public function claimVerification(Request $request): Response
    {
        $token = $request->query('token');

        if (!$token) {
            Log::warning('TwiML request missing token');
            return $this->errorTwiml();
        }

        $cacheKey = 'twiml_token_' . $token;
        $data = Cache::get($cacheKey);

        if (!$data || !isset($data['code']) || !isset($data['restaurant_name'])) {
            Log::warning('TwiML token invalid or expired', ['token' => $token]);
            return $this->errorTwiml();
        }

        $code = $data['code'];
        $restaurantName = $data['restaurant_name'];

        // Split code into individual digits with pauses
        $digits = implode('. ', str_split($code));

        $twiml = '<?xml version="1.0" encoding="UTF-8"?>';
        $twiml .= '<Response>';
        $twiml .= '<Say language="es-MX" voice="Polly.Mia">';
        $twiml .= 'Hola. Esta es una llamada de verificación de Restaurantes Mexicanos Famosos, ';
        $twiml .= 'para el restaurante ' . htmlspecialchars($restaurantName, ENT_XML1) . '. ';
        $twiml .= '</Say>';
        $twiml .= '<Pause length="1"/>';
        $twiml .= '<Say language="es-MX" voice="Polly.Mia">';
        $twiml .= 'Tu código de verificación es: ' . $digits . '. ';
        $twiml .= '</Say>';
        $twiml .= '<Pause length="2"/>';
        $twiml .= '<Say language="es-MX" voice="Polly.Mia">';
        $twiml .= 'Repito, tu código de verificación es: ' . $digits . '. ';
        $twiml .= '</Say>';
        $twiml .= '<Pause length="1"/>';
        $twiml .= '<Say language="es-MX" voice="Polly.Mia">';
        $twiml .= 'Ingresa este código en el sitio web para completar tu verificación. Gracias.';
        $twiml .= '</Say>';
        $twiml .= '</Response>';

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Return error TwiML when something goes wrong.
     */
    protected function errorTwiml(): Response
    {
        $twiml = '<?xml version="1.0" encoding="UTF-8"?>';
        $twiml .= '<Response>';
        $twiml .= '<Say language="es-MX" voice="Polly.Mia">';
        $twiml .= 'Lo sentimos, hubo un error al procesar tu verificación. ';
        $twiml .= 'Por favor intenta de nuevo desde el sitio web.';
        $twiml .= '</Say>';
        $twiml .= '</Response>';

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }
}
