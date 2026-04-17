<?php

namespace App\Http\Controllers;

use App\Models\Faction;
use App\Models\Personnel;
use App\Models\Rank;
use App\Models\UnitClass;
use App\Models\Weapon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonnelController extends Controller
{
    /**
     * Menampilkan halaman utama (Tabel Daftar Personel)
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $factionFilter = $request->query('faction');
        $unitClassFilter = $request->query('unit_class');
        $rankFilter = $request->query('rank');
        $perPage = $request->query('perPage', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $personnels = Personnel::with(['faction', 'rank', 'unitClass', 'weapon']);

        if ($search) {
            $personnels->where('name', 'ilike', '%'.$search.'%');
        }

        if ($factionFilter) {
            $personnels->whereHas('faction', function ($q) use ($factionFilter) {
                $q->where('name', 'ilike', '%'.$factionFilter.'%');
            });
        }

        if ($unitClassFilter) {
            $personnels->whereHas('unitClass', function ($q) use ($unitClassFilter) {
                $q->where('name', 'ilike', '%'.$unitClassFilter.'%');
            });
        }

        if ($rankFilter) {
            $personnels->whereHas('rank', function ($q) use ($rankFilter) {
                $q->where('name', 'ilike', '%'.$rankFilter.'%');
            });
        }

        $personnels = $personnels->latest()->paginate($perPage);

        return Inertia::render('Personnel/Index', [
            'personnels' => $personnels,
            'filters' => [
                'search' => $search,
                'faction' => $factionFilter,
                'unit_class' => $unitClassFilter,
                'rank' => $rankFilter,
                'perPage' => $perPage,
            ],
        ]);
    }

    /**
     * Menampilkan form deployment
     */
    public function create()
    {
        return Inertia::render('Personnel/Create', [
            'factions' => Faction::select('id', 'name')->get(),
            'ranks' => Rank::select('id', 'name', 'level')->orderBy('level')->get(),
            'unitClasses' => UnitClass::select('id', 'name')->get(),
            'weapons' => Weapon::select('id', 'name', 'type')->get(),
        ]);
    }

    /**
     * Menyimpan data dari form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'required|exists:factions,id',
            'rank_id' => 'required|exists:ranks,id',
            'unit_class_id' => 'required|exists:unit_classes,id',
            'weapon_id' => 'required|exists:weapons,id',
        ]);

        Personnel::create($validated);

        // Redirect kembali ke tabel index
        return redirect()->route('personnel.index');
    }

    public function edit(Personnel $personnel)
    {
        return Inertia::render('Personnel/Edit', [
            'personnel' => $personnel, // Mengirim data prajurit yang dipilih
            'factions' => Faction::select('id', 'name')->get(),
            'ranks' => Rank::select('id', 'name', 'level')->orderBy('level')->get(),
            'unitClasses' => UnitClass::select('id', 'name')->get(),
            'weapons' => Weapon::select('id', 'name', 'type')->get(),
        ]);
    }

    /**
     * Menerima perubahan dan meng-update database.
     */
    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'required|exists:factions,id',
            'rank_id' => 'required|exists:ranks,id',
            'unit_class_id' => 'required|exists:unit_classes,id',
            'weapon_id' => 'required|exists:weapons,id',
        ]);

        $personnel->update($validated);

        return redirect()->route('personnel.index');
    }

    /**
     * Menghapus (Discharge) personel dari database.
     */
    public function destroy(Personnel $personnel)
    {
        $personnel->delete();

        return redirect()->route('personnel.index');
    }
}
