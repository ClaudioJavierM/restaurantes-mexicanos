<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        "elevenlabs_call_id", "agent_id", "caller_phone", "direction",
        "status", "transcript", "summary", "category",
        "duration_seconds", "metadata", "call_started_at", "call_ended_at",
    ];

    protected $casts = [
        "metadata" => "array",
        "call_started_at" => "datetime",
        "call_ended_at" => "datetime",
    ];

    public const CATEGORIES = [
        "order_inquiry" => "Consulta de Pedido",
        "reservation" => "Reservacion",
        "restaurant_search" => "Busqueda Restaurante",
        "owner_support" => "Soporte Duenos",
        "claim_status" => "Estado Reclamacion",
        "other" => "Otro",
    ];
}
