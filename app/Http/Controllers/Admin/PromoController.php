<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::latest()->get();
        return view('admin.promo.index', compact('promos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_promo'      => 'required|string|unique:promo,kode_promo',
            'nama_promo'      => 'required|string|max:100',
            'diskon_persen'   => 'required|numeric|min:0|max:100',
            'maks_diskon'     => 'required|numeric|min:0',
            'berlaku_dari'    => 'required|date',
            'berlaku_hingga'  => 'required|date|after_or_equal:berlaku_dari',
            'maks_penggunaan' => 'required|integer|min:1',
        ]);

        Promo::create($request->all());
        return back()->with('success', 'Promo berhasil dibuat.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return back()->with('success', 'Promo berhasil dihapus.');
    }
}
