<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use App\Models\User; // <-- Menggunakan model User, bukan Dokter
use Illuminate\Http\Request;

class JadwalPeriksaController extends Controller
{
    public function index()
    {
        $jadwals = JadwalPeriksa::with('dokter')->get();
        return view('admin.jadwal_periksa.index', compact('jadwals'));
    }

    public function create()
    {
        // Ambil data User yang role-nya adalah 'dokter'
        $dokters = User::where('role', 'dokter')->get();
        return view('admin.jadwal_periksa.create', compact('dokters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Validasi diubah ke tabel users
            'id_dokter' => 'required|exists:users,id', 
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'id_dokter.required' => 'Pilih dokter terlebih dahulu.',
            'hari.required' => 'Hari periksa wajib diisi.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        JadwalPeriksa::create([
            'id_dokter' => $request->id_dokter,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('jadwal_periksa.index')->with('success', 'Jadwal periksa berhasil ditambahkan!');
    }

    public function edit(JadwalPeriksa $jadwal_periksa)
    {
        $dokters = User::where('role', 'dokter')->get();
        return view('admin.jadwal_periksa.edit', compact('jadwal_periksa', 'dokters'));
    }

    public function update(Request $request, JadwalPeriksa $jadwal_periksa)
    {
        $request->validate([
            'id_dokter' => 'required|exists:users,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        $jadwal_periksa->update([
            'id_dokter' => $request->id_dokter,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('jadwal_periksa.index')->with('success', 'Jadwal periksa berhasil diperbarui!');
    }

    public function destroy(JadwalPeriksa $jadwal_periksa)
    {
        $jadwal_periksa->delete();
        return redirect()->route('jadwal_periksa.index')->with('success', 'Jadwal periksa berhasil dihapus!');
    }
}