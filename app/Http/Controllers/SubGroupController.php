<?php

namespace App\Http\Controllers;

use App\Models\Faction;
use App\Models\SubGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubGroupController extends Controller
{
    public function index()
    {
        $search = request()->query('search');
        $factionFilter = request()->query('faction');
        $perPage = request()->query('perPage', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $subGroups = SubGroup::with('faction');

        if ($search) {
            $subGroups->where('name', 'ilike', '%' . $search . '%');
        }

        if ($factionFilter) {
            $subGroups->whereHas('faction', function ($q) use ($factionFilter) {
                $q->where('name', 'ilike', '%' . $factionFilter . '%');
            });
        }

        $subGroups = $subGroups->orderBy('name')->paginate($perPage);

        return Inertia::render('Master/SubGroup/Index', [
            'subGroups' => $subGroups,
            'filters' => [
                'search' => $search,
                'faction' => $factionFilter,
                'perPage' => $perPage,
            ],
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/SubGroup/Create', [
            'factions' => Faction::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'required|exists:factions,id',
        ]);

        SubGroup::create($validated);

        return redirect()->route('sub-groups.index');
    }

    public function edit(SubGroup $subGroup)
    {
        return Inertia::render('Master/SubGroup/Edit', [
            'subGroup' => $subGroup,
            'factions' => Faction::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SubGroup $subGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'required|exists:factions,id',
        ]);

        $subGroup->update($validated);

        return redirect()->route('sub-groups.index');
    }

    public function destroy(SubGroup $subGroup)
    {
        if ($subGroup->personnels()->exists()) {
            return redirect()->route('sub-groups.index')->with('error', 'Cannot delete this sub-group because it is still being used by personnel.');
        }
        $subGroup->delete();

        return redirect()->route('sub-groups.index');
    }
}