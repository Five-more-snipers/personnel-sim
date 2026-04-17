<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Personnel;
use App\Models\Faction;
use App\Models\Rank;
use App\Models\UnitClass;
use App\Models\Weapon;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    /**
     * Menampilkan halaman utama (Tabel Daftar Personel)
     */
    public function index(Request $request)
    {
        //Fungsi Search
        $search = $request->query('search');
        $personnels = Personnel::with(['faction', 'rank', 'unitClass', 'weapon']);

        //Keyword Search Filternya
        if ($search) {
            $personnels->where('name', 'ilike', '%' . $search . '%');
        }

        $personnels = $personnels->latest()->get();


        return Inertia::render('Personnel/Index', [
            'personnels' => $personnels,
            'filters' => [
                'search' => $search,
            ], // Mengirim data personel dan filter pencarian ke view
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