<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    protected function userOwnsRestaurant($user, Restaurant $restaurant): bool
    {
        // Check via user_id (hasMany relationship)
        if ($restaurant->user_id && $user->id === $restaurant->user_id) {
            return true;
        }
        
        // Check via owner_id (legacy/alternate field)
        if ($restaurant->owner_id && $user->id === $restaurant->owner_id) {
            return true;
        }
        
        // Check via restaurants relationship
        if ($user->restaurants()->where('restaurants.id', $restaurant->id)->exists()) {
            return true;
        }
        
        return false;
    }
    
    public function download(Restaurant $restaurant)
    {
        // Check if restaurant is claimed
        if (!$restaurant->is_claimed) {
            abort(403, 'Solo restaurantes verificados pueden descargar el certificado.');
        }
        
        // Check ownership
        if (!$this->userOwnsRestaurant(auth()->user(), $restaurant)) {
            abort(403, 'No tienes permiso para descargar este certificado.');
        }
        
        $data = [
            'restaurant' => $restaurant,
            'year' => date('Y'),
            'issue_date' => now()->format('F d, Y'),
            'certificate_id' => 'FAMER-' . strtoupper(substr(md5($restaurant->id . $restaurant->created_at), 0, 8)),
        ];
        
        $pdf = Pdf::loadView('certificates.verified-restaurant', $data);
        $pdf->setPaper('letter', 'landscape');
        
        $filename = 'Certificado_FAMER_' . str_replace(' ', '_', $restaurant->name) . '_' . date('Y') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function preview(Restaurant $restaurant)
    {
        if (!$restaurant->is_claimed) {
            abort(403, 'Solo restaurantes verificados pueden ver el certificado.');
        }
        
        // Check ownership
        if (!$this->userOwnsRestaurant(auth()->user(), $restaurant)) {
            abort(403, 'No tienes permiso para ver este certificado.');
        }
        
        $data = [
            'restaurant' => $restaurant,
            'year' => date('Y'),
            'issue_date' => now()->format('F d, Y'),
            'certificate_id' => 'FAMER-' . strtoupper(substr(md5($restaurant->id . $restaurant->created_at), 0, 8)),
        ];
        
        $pdf = Pdf::loadView('certificates.verified-restaurant', $data);
        $pdf->setPaper('letter', 'landscape');
        
        return $pdf->stream('certificado_preview.pdf');
    }

    
    /**
     * Download ranking certificate for a restaurant
     */
    public function rankingCertificate(Restaurant $restaurant, ?int $year = null)
    {
        $year = $year ?? now()->year;
        
        // Get the best ranking for this restaurant
        $ranking = $restaurant->rankings()
            ->where("year", $year)
            ->orderBy("position")
            ->first();
        
        if (!$ranking) {
            abort(404, "Este restaurante no tiene ranking para el año {$year}.");
        }
        
        // Check ownership
        if (!auth()->check() || !$this->userOwnsRestaurant(auth()->user(), $restaurant)) {
            abort(403, "No tienes permiso para descargar este certificado.");
        }
        
        $data = [
            "restaurant" => $restaurant,
            "ranking" => $ranking,
            "year" => $year,
            "issue_date" => now()->format("F d, Y"),
            "certificate_id" => "FAMER-RANK-" . strtoupper(substr(md5($restaurant->id . $ranking->id), 0, 8)),
        ];
        
        $pdf = Pdf::loadView("certificates.ranking-certificate", $data);
        $pdf->setPaper("letter", "landscape");
        
        $filename = "Certificado_Ranking_FAMER_" . str_replace(" ", "_", $restaurant->name) . "_" . $year . ".pdf";
        
        return $pdf->download($filename);
    }
    
    /**
     * Preview ranking certificate
     */
    public function rankingCertificatePreview(Restaurant $restaurant, ?int $year = null)
    {
        $year = $year ?? now()->year;
        
        $ranking = $restaurant->rankings()
            ->where("year", $year)
            ->orderBy("position")
            ->first();
        
        if (!$ranking) {
            abort(404, "Este restaurante no tiene ranking para el año {$year}.");
        }
        
        // Check ownership
        if (!auth()->check() || !$this->userOwnsRestaurant(auth()->user(), $restaurant)) {
            abort(403, "No tienes permiso para ver este certificado.");
        }
        
        $data = [
            "restaurant" => $restaurant,
            "ranking" => $ranking,
            "year" => $year,
            "issue_date" => now()->format("F d, Y"),
            "certificate_id" => "FAMER-RANK-" . strtoupper(substr(md5($restaurant->id . $ranking->id), 0, 8)),
        ];
        
        $pdf = Pdf::loadView("certificates.ranking-certificate", $data);
        $pdf->setPaper("letter", "landscape");
        
        return $pdf->stream("certificado_ranking_preview.pdf");
    }

}