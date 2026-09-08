<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CasoExitoController extends Controller
{
    /**
     * El índice: la lista de los casos publicados.
     */
    public function index(): View
    {
        return view('casos-exito.index', [
            'casos' => config('casos-exito'),
        ]);
    }

    /**
     * Un caso. Un slug que no está en el registro no existe como dirección.
     */
    public function show(string $proyecto): View
    {
        $caso = config('casos-exito.'.$proyecto);

        abort_if($caso === null, 404);

        return view('casos-exito.'.$caso['vista'], [
            'slug' => $proyecto,
            'caso' => $caso,
        ]);
    }
}
