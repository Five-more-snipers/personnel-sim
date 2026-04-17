<?php

namespace App\Http\Controllers;

use App\Models\Faction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactionController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Faction/Index', [
            'factions' => Faction::all(),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Faction/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        Faction::create($validated);

        return redirect()->route('factions.index');
    }

    public function edit(Faction $faction)
    {
        return Inertia::render('Master/Faction/Edit', ['faction' => $faction]);
    }

    public function update(Request $request, Faction $faction)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $faction->update($validated);

        return redirect()->route('factions.index');
    }

    public function destroy(Faction $faction)
    {
        if ($faction->personnels()->exists()) {
            return redirect()->route('factions.index')->with('error', 'Cannot delete this faction because it is still being used by personnel.');
        }
        $faction->delete();

        return redirect()->route('factions.index');
    }
}
