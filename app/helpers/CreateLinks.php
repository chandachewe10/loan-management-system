<?php
namespace App\helpers;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

class CreateLinks {
    
    public static function goTo(string $link, string $title, ?string $tooltip)
{
    return new HtmlString(Blade::render('filament::components.link', [
        'color' => 'primary',
        'tooltip' => $tooltip,
        'href' => $link,
        'target' => '_blank',
        'slot' => $title,
        'icon' => 'heroicon-o-arrow-top-right-on-square',
    ]));
}
}
