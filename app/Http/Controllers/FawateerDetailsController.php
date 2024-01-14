<?php

namespace App\Http\Controllers;

use App\Models\fawateer;
use App\Models\Fawateer_attachments;
use App\Models\Fawateer_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class FawateerDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Fawateer_details $fawateer_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $fawateer = fawateer::where('id', $id)->first();
        $details = Fawateer_details::where('id_Invoice', $id)->get();
        $attachments = Fawateer_attachments::where('invoice_id', $id)->get();

        return view('fawateer.fawateer_details', compact('fawateer', 'details', 'attachments'));
        // return view('tabs');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fawateer_details $fawateer_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoices = Fawateer_attachments::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

    public function openfile($invoice_number, $file_name)
    {


        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->file($files);
    }


    public function getfile($invoice_number, $file_name)
    {


        $content = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->download($content);
    }
}
