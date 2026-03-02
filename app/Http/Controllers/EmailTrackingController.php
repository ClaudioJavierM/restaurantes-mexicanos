<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EmailTrackingController extends Controller
{
    /**
     * Track email open via invisible pixel
     */
    public function trackOpen(string $token): Response
    {
        try {
            $emailLog = EmailLog::where('tracking_token', $token)->first();

            if ($emailLog) {
                $emailLog->markAsOpened();
                Log::debug("Email opened: {$emailLog->to_email} (Token: {$token})");
            }
        } catch (\Exception $e) {
            Log::error("Error tracking email open: " . $e->getMessage());
        }

        // Return a 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Content-Length', strlen($pixel))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Track email click and redirect
     */
    public function trackClick(Request $request, string $token)
    {
        $url = $request->get('url');

        if (!$url) {
            return redirect('/');
        }

        try {
            $emailLog = EmailLog::where('tracking_token', $token)->first();

            if ($emailLog) {
                $emailLog->markAsClicked();
                Log::debug("Email link clicked: {$emailLog->to_email} -> {$url}");
            }
        } catch (\Exception $e) {
            Log::error("Error tracking email click: " . $e->getMessage());
        }

        // Redirect to original URL
        return redirect()->away($url);
    }

    /**
     * Handle unsubscribe request
     */
    public function unsubscribe(Request $request)
    {
        $email = $request->get('email');
        $token = $request->get('token');

        // Verify token
        $expectedToken = md5($email . config('app.key'));

        if ($token !== $expectedToken) {
            return view('emails.unsubscribe-error', [
                'message' => 'El enlace de cancelación es inválido o ha expirado.',
            ]);
        }

        // TODO: Mark email as unsubscribed in a dedicated table
        // For now, we'll just show a confirmation

        Log::info("Email unsubscribed: {$email}");

        return view('emails.unsubscribe-success', [
            'email' => $email,
        ]);
    }

    /**
     * Preview a campaign email (admin only)
     */
    public function preview(int $campaignId)
    {
        if (!auth()->user()?->is_admin) {
            abort(403);
        }

        $campaign = \App\Models\EmailCampaign::findOrFail($campaignId);

        // Sample data for preview
        $mergeData = [
            'restaurant_name' => 'Mi Restaurante Ejemplo',
            'owner_name' => 'Juan Pérez',
            'owner_email' => 'ejemplo@restaurante.com',
            'restaurant_city' => 'Los Angeles',
            'restaurant_state' => 'California',
            'famer_score' => '85',
            'famer_grade' => 'B+',
            'claim_url' => url('/claim/mi-restaurante'),
            'dashboard_url' => url('/dashboard'),
            'unsubscribe_url' => url('/unsubscribe?email=ejemplo@restaurante.com&token=example'),
            'tracking_pixel' => url('/email/track/open/preview-' . time()),
        ];

        $content = $campaign->renderContent($mergeData);

        return view('emails.campaign', [
            'htmlContent' => $content,
            'previewText' => $campaign->preview_text,
            'campaignName' => $campaign->name,
            'isTest' => true,
        ]);
    }
}
