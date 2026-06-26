<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LinkClickController
{
    public function click(Request $request, Link $link): RedirectResponse
    {
        abort_unless($link->is_active, 404);

        $link->clicks()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $destination = $link->getResolvedUrl() ?? route('home');
        return redirect()->away($destination);
    }
}
