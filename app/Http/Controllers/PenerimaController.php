<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Http\Request;

class PenerimaController extends Controller
{
    public function index()
    {
        $penerima = Penerima::paginate(10); // 10 data per halaman
        return view('penerima.indexpenerima', compact('penerima'));
    }

    public function create()
    {
        return view('penerima.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer',
            'jumlah_anak' => 'required|integer',
            'kelayakan_rumah' => 'required',
            'pendapatan_perbulan' => 'required|numeric',
        ]);
        Penerima::create($validated);
        return redirect()->route('penerima.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $penerima = Penerima::findOrFail($id);
        return view('penerima.edit', compact('penerima'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'usia' => 'required|integer',
            'jumlah_anak' => 'required|integer',
            'kelayakan_rumah' => 'required',
            'pendapatan_perbulan' => 'required|numeric',
        ]);
        $penerima = Penerima::findOrFail($id);
        $penerima->update($validated);
        return redirect()->route('penerima.index')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {
        $penerima = Penerima::findOrFail($id);
        $penerima->delete();
        return redirect()->route('penerima.index')->with('success', 'Data berhasil dihapus!');
    }
}
