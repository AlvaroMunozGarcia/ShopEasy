<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Provider;
use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;

class PurchaseController extends Controller
{
    
    public function index()
    {
        $purchases=Purchase::get();
        return view('admin.purchase.index',compact('purchases'));
    }
    public function create()
    {
        $providers=Provider::get();
        $products=Product::get();
        return view('admin.purchase.create',compact('providers','products'));
    }
    public function store(StoreRequest $request)
    {

        DB::beginTransaction();
        try {
            $subtotal_details = 0;
            $purchase_details_data = [];

            foreach ($request->details as $detail) {
                $line_subtotal = $detail['quantity'] * $detail['price'];
                $subtotal_details += $line_subtotal;

                $purchase_details_data[] = [
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ];
            }
            if (empty($purchase_details_data)) {
                DB::rollBack();
                return redirect()->back()->withInput()->withErrors(['details' => 'Debe agregar al menos un producto válido a la compra.']);
            }

            $tax_rate = $request->tax; 
            $grand_total_purchase = $subtotal_details * (1 + ($tax_rate / 100));

            $purchase = Purchase::create([
                'provider_id' => $request->provider_id,
                'user_id' => Auth::id(),
                'purchase_date' => Carbon::now('America/Lima'), 
                'tax' => $tax_rate,
                'total' => $grand_total_purchase, 
                'status' => 'VALID', 
            ]);

            $purchase->purchaseDetails()->createMany($purchase_details_data);

            foreach ($purchase_details_data as $detailItem) {
                $product = Product::findOrFail($detailItem['product_id']);
                $product->increment('stock', $detailItem['quantity']);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Compra registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al registrar la compra. Por favor, inténtelo de nuevo.');
        }
    }
    

    public function show(Purchase $purchase)
    {
        $purchase->load(['provider', 'user', 'purchaseDetails.product']);
        return view('admin.purchase.show',compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $providers=Provider::get();
        $products=Product::get(); 
        $purchase->load('purchaseDetails.product'); 
        return view('admin.purchase.edit', compact('purchase', 'providers', 'products'));
    }

    public function update(UpdateRequest $request, Purchase $purchase)
    {
        //$purchase->update($request->all());
        //return redirect()->route('purchase.index');
    }

    public function destroy(Purchase $purchase)
    {
       //return redirect()->route('purchases.index'); 
    }






/**
 * Muestra una vista optimizada para impresión.
 */
public function printView(Purchase $purchase)
{
    $purchase->loadMissing(['provider', 'user', 'purchaseDetails.product']);
    $subtotal = 0;
    foreach ($purchase->purchaseDetails as $detail) {
        $subtotal += ($detail->quantity * $detail->price);
    }
    return view('admin.purchase.print', compact('purchase', 'subtotal'));
}











    public function pdf(Purchase $purchase)
    {
       $subtotal=0;
       $purchaseDetails =$purchase->purchaseDetails;
       foreach($purchaseDetails as $purchaseDetail){
        $subtotal+=($purchaseDetail->quantity*$purchaseDetail->price);
        
       }
       $pdf =PDF::loadView('admin.purchase.pdf',compact('purchase','subtotal'));
       return $pdf->stream('Reporte_de_compra_'.$purchase->id.'.pdf'); 
    }



}
