<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'email'           => 'required|email|max:255',
                'phone'           => 'nullable|string|max:50',
                'restaurant_name' => 'nullable|string|max:255',
                'restaurant_slug' => 'nullable|string|max:255',
                'issue_type'      => 'required|in:verification,claim,subscription,data_error,other',
                'message'         => 'required|string|min:10',
            ]);

            $ticket = SupportTicket::create($validated);

            // Send notification email to admin
            $this->sendAdminNotification($ticket);

            return response()->json([
                'success'       => true,
                'ticket_number' => $ticket->ticket_number,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('SupportTicket store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el ticket. Por favor intenta de nuevo.',
            ], 500);
        }
    }

    private function sendAdminNotification(SupportTicket $ticket): void
    {
        try {
            $issueLabels = [
                'verification'  => 'Verificación',
                'claim'         => 'Reclamar perfil',
                'subscription'  => 'Suscripción',
                'data_error'    => 'Error en datos',
                'other'         => 'Otro',
            ];

            $issueLabel = $issueLabels[$ticket->issue_type] ?? $ticket->issue_type;
            $subject    = "[FAMER Soporte] #{$ticket->ticket_number} — {$issueLabel} — " . ($ticket->restaurant_name ?? 'N/A');

            $body = implode("\n", [
                "Nuevo ticket de soporte recibido via Carmen Widget",
                str_repeat('─', 50),
                "",
                "Ticket #: {$ticket->ticket_number}",
                "Tipo:     {$issueLabel}",
                "Estado:   Abierto",
                "Fuente:   {$ticket->source}",
                "",
                "─── Datos del solicitante ───────────────────────",
                "Nombre:      {$ticket->name}",
                "Email:       {$ticket->email}",
                "Teléfono:    " . ($ticket->phone ?? '—'),
                "",
                "─── Restaurante ─────────────────────────────────",
                "Nombre:      " . ($ticket->restaurant_name ?? '—'),
                "Slug/URL:    " . ($ticket->restaurant_slug ?? '—'),
                "",
                "─── Mensaje ─────────────────────────────────────",
                $ticket->message,
                "",
                str_repeat('─', 50),
                "Ver en admin: " . url('/admin/support-tickets/' . $ticket->id),
                "Creado: " . $ticket->created_at->format('Y-m-d H:i:s') . ' UTC',
            ]);

            Mail::raw($body, function ($message) use ($subject) {
                $message->to('isaacjv79@gmail.com')
                        ->from(
                            config('mail.from.address', 'soporte@restaurantesmexicanosfamosos.com.mx'),
                            config('mail.from.name', 'FAMER Soporte')
                        )
                        ->subject($subject);
            });

        } catch (\Throwable $e) {
            // Log but don't fail the request — ticket was already created
            Log::warning('SupportTicket admin notification failed', [
                'ticket_number' => $ticket->ticket_number,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
