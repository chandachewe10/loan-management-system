<?php

namespace App\Filament;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class CustomLogOutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        
            return redirect('/');
        
    }
}
