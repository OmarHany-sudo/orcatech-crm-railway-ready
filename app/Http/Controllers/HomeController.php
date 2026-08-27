<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $knowledgeBaseArticles = KnowledgeBaseArticle::latest()->take(5)->get();

        return view('home', ['knowledgeBaseArticles' => $knowledgeBaseArticles]);
    }

    public function dashboard(): RedirectResponse
    {
        $user = request()->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $team = $user->currentTeam ?? $user->ownedTeams()->first();

        if ($team === null) {
            return redirect()->route('home');
        }

        // OrcaTech is one CRM application. Super-admin capabilities remain
        // permission-controlled inside the same tenant-aware panel.
        return redirect()->route('filament.app.pages.dashboard', ['tenant' => $team]);
    }
}
