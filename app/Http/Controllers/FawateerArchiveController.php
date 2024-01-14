<?php

namespace App\Http\Controllers;

use App\Models\fawateer;
use Illuminate\Http\Request;

class FawateerArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fawateers=fawateer::onlyTrashed()->get();
        return view('fawateer.fawateer_archive',compact('fawateers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        
        $id = $request->invoice_id;
        $flight = fawateer::withTrashed()->where('id', $id)->restore();
        session()->flash('restore_invoice');
        return redirect('/fawateer');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoices = fawateer::withTrashed()->where('id',$request->invoice_id)->first();
        $invoices->forceDelete();
        session()->flash('delete_invoice');
        return redirect('/Archive');
    }
}
