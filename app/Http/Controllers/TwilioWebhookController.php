<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class TwilioWebhookController extends Controller
{
    /**
     * Handle incoming SMS messages from Twilio
     */
    public function handleIncomingSms(Request $request)
    {
        $from = $request->input('From');
        $body = strtoupper(trim($request->input('Body')));
        
        Log::info('Twilio SMS received', [
            'from' => $from,
            'body' => $body,
        ]);

        // Normalize phone number (remove +1 prefix if present)
        $normalizedPhone = $this->normalizePhone($from);
        
        // Find user by phone
        $user = User::where('phone', $normalizedPhone)
            ->orWhere('phone', $from)
            ->orWhere('phone', 'LIKE', '%' . substr($normalizedPhone, -10))
            ->first();

        $response = new MessagingResponse();

        // Handle opt-out keywords
        if (in_array($body, ['STOP', 'UNSUBSCRIBE', 'CANCEL', 'END', 'QUIT'])) {
            if ($user) {
                $user->optOutFromSmsMarketing();
                Log::info('User opted out from SMS', ['user_id' => $user->id, 'phone' => $from]);
            }
            $response->message('FAMER: Has sido dado de baja de nuestros mensajes. No recibirás más SMS promocionales. Responde FAMER para volver a suscribirte.');
            return response($response, 200)->header('Content-Type', 'text/xml');
        }

        // Handle opt-in keywords
        if (in_array($body, ['FAMER', 'JOIN', 'UNIRSE', 'START', 'YES'])) {
            if ($user) {
                $user->optInToSmsMarketing();
                Log::info('User opted in to SMS', ['user_id' => $user->id, 'phone' => $from]);
                $response->message('FAMER: ¡Bienvenido! Ahora recibirás ofertas exclusivas y actualizaciones de tu restaurante. Responde STOP para darte de baja en cualquier momento. Msg&data rates may apply.');
            } else {
                $response->message('FAMER: Para suscribirte, primero registra tu restaurante en restaurantesmexicanosfamosos.com/register. Responde STOP para no recibir más mensajes.');
            }
            return response($response, 200)->header('Content-Type', 'text/xml');
        }

        // Handle HELP keyword
        if (in_array($body, ['HELP', 'AYUDA', 'INFO'])) {
            $response->message('FAMER: Somos el directorio de restaurantes mexicanos más grande. Responde STOP para darte de baja, FAMER para suscribirte. Visita restaurantesmexicanosfamosos.com o contacta soporte@famer.com');
            return response($response, 200)->header('Content-Type', 'text/xml');
        }

        // Default response for unrecognized messages
        $response->message('FAMER: Gracias por tu mensaje. Para soporte visita restaurantesmexicanosfamosos.com. Responde STOP para darte de baja, HELP para ayuda.');
        
        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Handle Twilio delivery status callbacks
     */
    public function handleStatusCallback(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');
        $to = $request->input('To');
        
        Log::info('Twilio status callback', [
            'message_sid' => $messageSid,
            'status' => $status,
            'to' => $to,
        ]);

        // You can store delivery status in database if needed
        // SmsLog::where('twilio_sid', $messageSid)->update(['status' => $status]);

        return response('OK', 200);
    }

    /**
     * Normalize phone number format
     */
    private function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 1 and has 11 digits, remove leading 1
        if (strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
            $cleaned = substr($cleaned, 1);
        }
        
        return $cleaned;
    }
}
