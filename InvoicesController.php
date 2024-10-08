<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\invoices_details;
use App\Models\invoices_attachments;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index()
    {

        $invoices = invoices::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

      
        invoices::create([
          
            'invoice_number'=>$request->invoice_number,
            'invoice_Date'=>$request->invoice_Date,
            'Due_date'=>$request->Due_date,
            'product'=>$request->product,
            'section_id'=>$request->Section,
            'Amount_collection'=>$request->Amount_collection,
            'Amount_Commission'=>$request->Amount_Commission,
            'Discount'=>$request->Discount,
            'Value_VAT'=>$request->Value_VAT,
            'Rate_VAT'=>$request->Rate_VAT,
            'Total'=>$request->Total,
            'Status'=>'غير مدفوعة',
            'Value_Status'=>2,
            'note'=>$request->note,
           
           
              ]);
             $invoice_id = invoices::latest()->first()->id;
            invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);
            if($request->hasFile('pic')){
                $invoice_id = invoices::latest()->first()->id;
                $image = $request->file('pic');
                $file_name = $image->getClientOriginalName();
                $invoice_number  = $request->invoice_number;

                $attachments = new invoices_attachments;
                $attachments->file_name = $file_name;
                $attachments->invoice_number = $invoice_number;
                $attachments->Created_by = Auth::user()->name;
                $attachments->invoice_id = $invoice_id;
                $attachments->save();

                $imageName = $request->pic->getClientOriginalName();

                $request->pic->move(public_path('Attachments/'. $invoice_number), $imageName);

            }
            session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
            return back();

        

            //    return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoices $invoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices $invoices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoices $invoices)
    {
        //
    }
    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

}
