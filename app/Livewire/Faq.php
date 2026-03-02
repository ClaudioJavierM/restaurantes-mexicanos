<?php

namespace App\Livewire;

use Livewire\Component;

class Faq extends Component
{
    public array $faqs = [];
    
    public function mount()
    {
        $this->faqs = [
            'general' => [
                'title' => 'Preguntas Generales',
                'items' => [
                    [
                        'question' => 'Que es FAMER?',
                        'answer' => 'FAMER (Famous Mexican Restaurants) es el directorio mas completo de restaurantes mexicanos en Estados Unidos. Recopilamos informacion de multiples fuentes para ayudar a los comensales a encontrar autentica comida mexicana cerca de ellos.'
                    ],
                    [
                        'question' => 'Que son los FAMER Awards?',
                        'answer' => 'Los FAMER Awards son reconocimientos anuales que destacan a los mejores restaurantes mexicanos. Se basan en calificaciones de Yelp, Google, votos de la comunidad y otros factores de calidad. Hay premios a nivel ciudad, estado y nacional.'
                    ],
                ]
            ],
            'listings' => [
                'title' => 'Sobre los Listados',
                'items' => [
                    [
                        'question' => 'Por que mi restaurante ya aparece en FAMER?',
                        'answer' => 'FAMER recopila informacion de fuentes publicas como Yelp, Google Maps y otros directorios. Si tu restaurante esta listado en estas plataformas, es probable que aparezca automaticamente en nuestro directorio. Esto nos permite ofrecer un catalogo completo sin requerir que cada restaurante se registre manualmente.'
                    ],
                    [
                        'question' => 'De donde obtienen la informacion de los restaurantes?',
                        'answer' => 'Agregamos datos de Yelp, Google Places, y otras fuentes publicas. Combinamos calificaciones y resenas de multiples plataformas para dar una vision mas completa de cada restaurante.'
                    ],
                    [
                        'question' => 'Puedo eliminar mi restaurante del directorio?',
                        'answer' => 'Si deseas que tu restaurante no aparezca en FAMER, puedes contactarnos. Sin embargo, te recomendamos reclamar tu perfil en lugar de eliminarlo, ya que aparecer en directorios ayuda a que mas clientes te encuentren.'
                    ],
                    [
                        'question' => 'Por que mi calificacion es diferente a Yelp o Google?',
                        'answer' => 'FAMER combina calificaciones de multiples fuentes (Yelp, Google, votos de usuarios) para crear un puntaje unificado. Esto puede resultar en una calificacion ligeramente diferente a la de una sola plataforma.'
                    ],
                ]
            ],
            'claiming' => [
                'title' => 'Reclamar tu Restaurante',
                'items' => [
                    [
                        'question' => 'Como reclamo mi restaurante?',
                        'answer' => 'Busca tu restaurante en FAMER, haz clic en Reclamar este restaurante y sigue el proceso de verificacion. Necesitaras demostrar que eres el propietario o gerente autorizado.'
                    ],
                    [
                        'question' => 'Que beneficios tiene reclamar mi restaurante?',
                        'answer' => 'Al reclamar tu restaurante puedes: actualizar fotos e informacion, responder a resenas, acceder a estadisticas de visitas, generar codigos QR para votacion, y participar activamente en los FAMER Awards.'
                    ],
                    [
                        'question' => 'Es gratis reclamar mi restaurante?',
                        'answer' => 'Si, reclamar y gestionar tu restaurante en FAMER es completamente gratis. No hay cargos ocultos ni suscripciones obligatorias.'
                    ],
                    [
                        'question' => 'Como actualizo la informacion de mi restaurante?',
                        'answer' => 'Una vez que hayas reclamado tu restaurante, puedes acceder a tu dashboard y editar toda la informacion: horarios, menu, fotos, descripcion, y mas.'
                    ],
                ]
            ],
            'voting' => [
                'title' => 'Votacion y Rankings',
                'items' => [
                    [
                        'question' => 'Como funcionan los rankings FAMER?',
                        'answer' => 'Los rankings se calculan usando un algoritmo que considera: calificacion promedio de Yelp y Google, numero total de resenas, votos de la comunidad FAMER, y otros factores de calidad. Se actualizan periodicamente.'
                    ],
                    [
                        'question' => 'Como puedo obtener mas votos para mi restaurante?',
                        'answer' => 'Reclama tu restaurante y usa el codigo QR que te proporcionamos. Colocalo en tu local para que tus clientes puedan escanearlo y votar facilmente. Tambien puedes compartir el enlace en tus redes sociales.'
                    ],
                    [
                        'question' => 'Cada cuanto pueden votar los clientes?',
                        'answer' => 'Cada cliente puede votar una vez al mes por cada restaurante. Esto asegura que los votos reflejen experiencias recientes y evita manipulacion.'
                    ],
                ]
            ],
        ];
    }
    
    public function render()
    {
        return view('livewire.faq')
            ->layout('layouts.app', [
                'title' => 'Preguntas Frecuentes | FAMER',
                'description' => 'Respuestas a las preguntas mas comunes sobre FAMER, el directorio de restaurantes mexicanos.'
            ]);
    }
}
