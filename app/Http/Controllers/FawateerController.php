<?php

namespace App\Http\Controllers;

use App\Models\fawateer;
use App\Models\Fawateer_attachments;
use App\Models\Fawateer_details;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddFawateer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class FawateerController extends Controller
{
   
    public function index()
    {
        $fawateers = fawateer::all();
        return view('fawateer.fawateer', compact('fawateers'));
    }

    ///////////////////////////////////////
   
    public function create()
    {
        $sections = Section::all();
        return view('fawateer.add_fawateer', compact('sections'));
    }


///////////////////////////////////////////////

   
    public function store(Request $request)
    {
        fawateer::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = fawateer::latest()->first()->id;

        Fawateer_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = fawateer::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new Fawateer_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }

        //mail notification
        // $user=User::first();
        // Notification::send($user, new AddFawateer($invoice_id));

        $user = User::find(Auth::user()->id);
        $invoices = fawateer::latest()->first();
        Notification::send($user, new \App\Notifications\AddNotifications($invoices));

        // event(new MyEventClass('hello world'));
        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }


//////////////////////////////////////////////////////


    public function show($id)
    {
        $fawateer = fawateer::where('id', $id)->first();
        return view('fawateer.status_update', compact('fawateer'));
    }


    ///////////////////////////////////////////////////

    public function edit($id)
    {
        $fawateer = fawateer::where('id', $id)->first();
        $sections = Section::all();
        return view('fawateer.edit_fawateer', compact('fawateer', 'sections'));
    }

    public function update(Request $request)
    {
        $invoices = fawateer::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

///////////////////////////////////////////////

public function fawateer_paid()
{
    $fawateers = fawateer::where('Value_Status', 1)->get();
    return view('fawateer.fawateer_paid', compact('fawateers'));
}

///////////////////////////////////////////////

    public function fawateer_unpaid()
    {
        $fawateers = fawateer::where('Value_Status', 2)->get();
        return view('fawateer.fawateer_unpaid', compact('fawateers'));
    }
    

    ///////////////////////////////////////////////
    
    public function fawateer_partial()
    {
        $fawateers = fawateer::where('Value_Status', 3)->get();
        return view('fawateer.fawateer_partial', compact('fawateers'));
    }
    
    ///////////////////////////////////////////////
  
    public function destroy(request $request)
    {

        $id = $request->invoice_id;
        $fawateers = Fawateer::where('id', $id)->first();
        $Details = Fawateer_attachments::where('invoice_id', $id)->first();

        $id_page = $request->id_page;


        if (!$id_page == 2) {

            if (!empty($Details->invoice_number)) {

                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }

            $fawateers->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/fawateer');
        } else {

            $fawateers->delete();
            session()->flash('archive_invoice');
            return redirect('/fawateer_archive');
        }
    }

//////////////////////////////////////////////////////////////////

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

 ////////////////////////////////////////////////////////////

    public function Status_Update($id, Request $request)
    {
        $fawateer = fawateer::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $fawateer->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            Fawateer_details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $fawateer->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            Fawateer_details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('edit', 'تم تعديل حالة الدفع بنجاح');
        return redirect('/fawateer');
    }

    ////////////////////////////////////////////////////

    public function Print_Fawateer($id)
    {

        $fawateers = fawateer::where('id', $id)->first();
        return view('fawateer.print_fawateer', compact('fawateers'));
    }

////////////////////////////////////////////////////////


    public function MarkAsRead_all(Request $request)
    {

        $userUnreadNotification = auth()->user()->unreadNotifications;

        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }
}
