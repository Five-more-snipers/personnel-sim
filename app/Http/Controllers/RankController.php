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
            'ranks' => Rank::all(),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Rank/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        Rank::create($validated);

        return redirect()->route('ranks.index');
    }

    public function edit(Rank $rank)
    {
        return Inertia::render('Master/Rank/Edit', ['rank' => $rank]);
    }

    public function update(Request $request, Rank $rank)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
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
