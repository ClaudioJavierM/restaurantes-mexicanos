<?php

namespace App\Http\Controllers;

use App\Models\OwnerCampaignSend;
use App\Models\RestaurantCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OwnerEmailTrackingController extends Controller
{
    public function trackOpen(string $token)
    {
        $send = OwnerCampaignSend::where('tracking_token', $token)->first();
        
        if ($send) {
            $send->markOpened();
        }
        
        // Return 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        
        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen($pixel),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
    
    public function trackClick(string $token, Request $request)
    {
        $send = OwnerCampaignSend::where('tracking_token', $token)->first();
        
        if ($send) {
            $send->markClicked();
        }
        
        $url = $request->query('url');
        if ($url) {
            $url = base64_decode($url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return redirect()->away($url);
            }
        }
        
        return redirect('/');
    }
    
    public function unsubscribe(string $token)
    {
        $send = OwnerCampaignSend::where('tracking_token', $token)
            ->with(['customer', 'campaign.restaurant'])
            ->first();
        
        if (!$send) {
            return view('emails.unsubscribe-error', [
                'message' => 'Enlace invalido o expirado.'
            ]);
        }
        
        $customer = $send->customer;
        $restaurant = $send->campaign->restaurant;
        
        // Unsubscribe the customer
        $customer->unsubscribe();
        
        // Increment unsubscribe count on campaign
        $send->campaign->increment('unsubscribed_count');
        
        return view('emails.unsubscribe-success', [
            'restaurant' => $restaurant,
            'email' => $customer->email,
        ]);
    }
}
