<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use Illuminate\Contracts\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $projects = PortfolioProject::with(['technologies', 'screenshots', 'technicalHighlights'])
            ->orderBy('sort_order')
            ->orderByDesc('started_year')
            ->get();

        return view('portfolio.index', compact('projects'));
    }

    public function show(PortfolioProject $project): View
    {
        $project->load(['technologies', 'screenshots', 'technicalHighlights']);

        return view('portfolio.show', compact('project'));
    }
}
