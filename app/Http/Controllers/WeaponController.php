<?php

namespace App\Http\Controllers;

use App\Models\Weapon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WeaponController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Weapon/Index', [
            'weapons' => Weapon::all(),
            'error' => session('error'),
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
            'type' => 'required|string|max:255',
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
            'type' => 'required|string|max:255',
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
