<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of all services.
     */
    public function index()
    {
        $services = Service::all();

        return view('services.index', compact('services'));
    }

    /**
     * Display the specified service.
     */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        // Fetch other services to show in the footer navigation
        $otherServices = Service::where('slug', '!=', $slug)->get();

        return view('services.show', compact('service', 'otherServices'));
    }
}
