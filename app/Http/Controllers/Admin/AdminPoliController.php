<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poli;

class AdminPoliController extends Controller
{
    public function index()
    {
        $polis = Poli::all();
        return view('admin.polis.index', compact('polis'));
    }

    public function create()
    {
        return view('admin.polis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_poli' => 'required|string|max:25',
            'keterangan' => 'nullable|string',
        ]);

        Poli::create($validated);
        return redirect()->route('polis.index')->with('success', 'Data Poli berhasil ditambahkan.');
    }

    public function edit(Poli $poli)
    {
        return view('admin.polis.edit', compact('poli'));
    }

    public function update(Request $request, Poli $poli)
    {
        $validated = $request->validate([
            'nama_poli' => 'required|string|max:25',
            'keterangan' => 'nullable|string',
        ]);

        $poli->update($validated);
        return redirect()->route('polis.index')->with('success', 'Data Poli berhasil diubah.');
    }

    public function destroy(Poli $poli)
    {
        $poli->delete();
        return redirect()->route('polis.index')->with('success', 'Data Poli berhasil dihapus.');
    }
}
