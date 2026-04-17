<?php

namespace App\Http\Controllers;

use App\Models\Faction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactionController extends Controller
{
    public function index()
    {
            $search = request()->query('search');   
            $perPage = request()->query('perPage', 10);
            $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

            $factions = Faction::query();

            if ($search) {
                $factions->where('name', 'ilike', '%' . $search . '%');
            }

            $factions = $factions->orderBy('name')->paginate($perPage);
    
            return Inertia::render('Master/Faction/Index', [
                'factions' => $factions,
                'filters' => [
                    'search' => $search,
                    'perPage' => $perPage,
                ],  
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
