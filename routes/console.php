<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('posts:publish-scheduled')->everyMinute();

// Se revisa a diario; el comando mismo decide si algún proyecto ya lleva más
// de 7 días sin medirse — si no, no gasta cuota de la API por gastarla.
Schedule::command('portfolio:refresh-pagespeed')->daily();
