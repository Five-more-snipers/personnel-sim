<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RankController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Rank/Index', [
            'ranks' => Rank::withCount('personnels')->orderBy('level')->get(),
            'error' => session('error'),
        ]);
    }

    public function show(Rank $rank)
    {
        return Inertia::render('Master/Rank/Show', [
            'rank' => $rank->loadCount('personnels'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Rank/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);
        Rank::create($validated);

        return redirect()->route('ranks.index');
    }

    public function edit(Rank $rank)
    {
        return Inertia::render('Master/Rank/Edit', ['rank' => $rank]);
    }

    public function update(Request $request, Rank $rank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);
        $rank->update($validated);

        return redirect()->route('ranks.index');
    }

    public function destroy(Rank $rank)
    {
        if ($rank->personnels()->exists()) {
            return redirect()->route('ranks.index')->with('error', 'Cannot delete this rank because it is still being used by personnel.');
        }
        $rank->delete();

        return redirect()->route('ranks.index');
    }
}
