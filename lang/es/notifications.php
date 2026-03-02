<?php

return [
    'salutation' => 'Saludos,<br>El Equipo de Restaurantes Mexicanos Famosos',

    // Review Approved Notification
    'review_approved' => [
        'subject' => '¡Tu Reseña Ha Sido Aprobada!',
        'greeting' => '¡Hola :name!',
        'message' => '¡Excelentes noticias! Tu reseña para **:restaurant** ha sido aprobada y ya está visible en nuestro sitio.',
        'details' => 'Tu calificación: :rating/5 estrellas - ":title"',
        'action' => 'Ver Tu Reseña',
        'thanks' => '¡Gracias por contribuir a nuestra comunidad y ayudar a otros a descubrir restaurantes mexicanos auténticos!',
        'notification_message' => 'Tu reseña para :restaurant ha sido aprobada',
    ],

    // Suggestion Approved Notification
    'suggestion_approved' => [
        'subject' => '¡Tu Sugerencia de Restaurante Ha Sido Aprobada!',
        'greeting' => '¡Hola :name!',
        'message' => '¡Excelentes noticias! Tu sugerencia para **:restaurant** ha sido revisada y aprobada.',
        'details' => 'Ubicación: :city, :state',
        'action' => 'Ver Página del Restaurante',
        'thanks' => '¡Gracias por ayudarnos a expandir nuestro directorio de restaurantes mexicanos auténticos. Tu contribución hace la diferencia!',
        'notification_message' => 'Tu sugerencia para :restaurant ha sido aprobada',
    ],
];
