<?php

namespace App\Http\Controllers;

use App\Models\Weapon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WeaponController extends Controller
{
    public function index()
    {
        $search = request()->query('search');
        $categoryFilter = request()->query('category');
        $perPage = request()->query('perPage', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $weapons = Weapon::query();

        if ($search) {
            $weapons->where('name', 'ilike', '%'.$search.'%');
        }

        if ($categoryFilter) {
            $weapons->where('category', 'ilike', '%'.$categoryFilter.'%');
        }

        $weapons = $weapons->withCount('personnels')->orderBy('name')->paginate($perPage);

        return Inertia::render('Master/Weapon/Index', [
            'weapons' => $weapons,
            'filters' => [
                'search' => $search,
                'category' => $categoryFilter,
                'perPage' => $perPage,
            ],
            'error' => session('error'),
        ]);
    }

    public function show(Weapon $weapon)
    {
        return Inertia::render('Master/Weapon/Show', [
            'weapon' => $weapon->loadCount('personnels'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Weapon/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Weapon::create($validated);

        return redirect()->route('weapons.index');
    }

    public function edit(Weapon $weapon)
    {
        return Inertia::render('Master/Weapon/Edit', ['weapon' => $weapon]);
    }

    public function update(Request $request, Weapon $weapon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $weapon->update($validated);

        return redirect()->route('weapons.index');
    }

    public function destroy(Weapon $weapon)
    {
        if ($weapon->personnels()->exists()) {
            return redirect()->route('weapons.index')->with('error', 'Cannot delete this weapon because it is still being used by personnel.');
        }
        $weapon->delete();

        return redirect()->route('weapons.index');
    }
}
