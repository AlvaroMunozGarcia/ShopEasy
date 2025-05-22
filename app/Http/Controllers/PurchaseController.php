<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Provider;
use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\PDF as DomPDFPDF;

use function Ramsey\Uuid\v1;

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
        // Verifica que el array 'details' exista
        if (!isset($request->details) || !is_array($request->details)) {
            return redirect()->back()->withErrors(['details' => 'Debe agregar al menos un producto.']);
        }
    
        $total = 0;
        $details = [];
    
        foreach ($request->details as $detail) {
            // Validación defensiva para cada item
            if (
                !isset($detail['product_id']) ||
                !isset($detail['quantity']) ||
                !isset($detail['price']) ||
                !is_numeric($detail['quantity']) ||
                !is_numeric($detail['price'])
            ) {
                continue; // Opcional: podrías lanzar un error si algún detalle está incompleto
            }
    
            $subtotal = $detail['quantity'] * $detail['price'];
            $total += $subtotal;
    
            $details[] = [
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
            ];
        }
    
        // Si no hay detalles válidos
        if (empty($details)) {
            return redirect()->back()->withErrors(['details' => 'Todos los detalles están vacíos o mal formateados.']);
        }
    
        // Crear la compra
        $purchase = Purchase::create([
            'provider_id' => $request->provider_id,
            'purchase_date' => Carbon::now('America/Lima'),
            'tax' => $request->tax,
            'total' => $total,
            'user_id' => Auth::id(),
        ]);
    
        // Crear detalles
        $purchase->purchaseDetails()->createMany($details);

        // 7. Actualizar el stock de los productos comprados
        foreach ($details as $detailItem) {
            $product = Product::findOrFail($detailItem['product_id']);
            $product->increment('stock', $detailItem['quantity']);
        }

        return redirect()->route('purchases.index')->with('success', 'Compra registrada correctamente.');
    }
    

    public function show(Purchase $purchase)
    {
        return view('admin.purchase.show',compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $providers=Provider::get();
        return view('admin.purchase.show', compact('purchase'));
    }

    public function update(UpdateRequest $request, Purchase $purchase)
    {
        //$purchase->update($request->all());
        //return redirect()->route('purchase.index');
    }

    public function destroy(Purchase $purchase)
    {
       //$purchase->delete();
       //return redirect()->route('purchases.index'); 
    }





    // En e:\ProyectoDAW\ShopEasy\app\Http\Controllers\PurchaseController.php

// ... (otros métodos) ...

/**
 * Muestra una vista optimizada para impresión.
 */
public function printView(Purchase $purchase)
{
    // Carga las relaciones necesarias si no lo hiciste antes
    $purchase->load(['provider', 'user', 'purchaseDetails.product']);

    // Puedes calcular el subtotal aquí o dentro de la vista si prefieres
    $subtotal = 0;
    foreach ($purchase->purchaseDetails as $detail) {
        $subtotal += ($detail->quantity * $detail->price);
    }

    // Devuelve la vista específica para impresión
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
