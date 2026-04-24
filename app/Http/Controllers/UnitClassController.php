<?php

namespace App\Http\Controllers;

use App\Models\UnitClass;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitClassController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/UnitClass/Index', [
            'unitClasses' => UnitClass::withCount('personnels')->orderBy('name')->get(),
            'error' => session('error'),
        ]);
    }

    public function show(UnitClass $unitClass)
    {
        return Inertia::render('Master/UnitClass/Show', [
            'unitClass' => $unitClass->loadCount('personnels'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/UnitClass/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        UnitClass::create($validated);

        return redirect()->route('unit-classes.index');
    }

    public function edit(UnitClass $unitClass)
    {
        return Inertia::render('Master/UnitClass/Edit', ['unitClass' => $unitClass]);
    }

    public function update(Request $request, UnitClass $unitClass)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $unitClass->update($validated);

        return redirect()->route('unit-classes.index');
    }

    public function destroy(UnitClass $unitClass)
    {
        if ($unitClass->personnels()->exists()) {
            return redirect()->route('unit-classes.index')->with('error', 'Cannot delete this unit class because it is still being used by personnel.');
        }
        $unitClass->delete();

        return redirect()->route('unit-classes.index');
    }
}
