<?php

namespace App\Http\Controllers;

use App\Models\WidgetToken;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function script(Request $request)
    {
        $js = <<<'JS'
(function() {
    var script = document.currentScript;
    var token = script.getAttribute('data-token');
    if (!token) return;
    
    var container = document.getElementById('famer-widget-' + token);
    if (!container) {
        container = document.createElement('div');
        container.id = 'famer-widget-' + token;
        script.parentNode.insertBefore(container, script);
    }
    
    var iframe = document.createElement('iframe');
    iframe.src = 'BASE_URL/widget/embed/' + token;
    iframe.style.cssText = 'width:100%;height:400px;border:none;border-radius:12px;';
    iframe.setAttribute('loading', 'lazy');
    container.appendChild(iframe);
})();
JS;
        $js = str_replace('BASE_URL', config('app.url'), $js);
        
        return response($js)->header('Content-Type', 'application/javascript');
    }

    public function embed(string $token)
    {
        $widget = WidgetToken::where('token', $token)->where('is_active', true)->first();
        
        if (!$widget) {
            return response('Widget not found', 404);
        }

        $widget->incrementViews();
        $restaurant = $widget->restaurant;
        $reviews = $restaurant->reviews()->where('status', 'approved')->latest()->take(3)->get();
        
        return view('widgets.restaurant-embed', [
            'restaurant' => $restaurant,
            'reviews' => $reviews,
            'settings' => $widget->settings,
        ]);
    }

    public function preview(string $token)
    {
        return $this->embed($token);
    }
}
