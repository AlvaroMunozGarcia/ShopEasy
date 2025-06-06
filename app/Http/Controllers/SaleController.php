<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product; 
use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon; 
 

class SaleController extends Controller
{

    public function index()
    {
        $sales = Sale::get();
        return view('admin.sale.index', compact('sales'));
    }

    public function create()
    {
        $clients = Client::get();
        $products = Product::where('status', 'ACTIVE')->orderBy('name')->get();
        return view('admin.sale.create', compact('clients', 'products'));
    }

    public function store(StoreRequest $request) 
    {
       
        DB::beginTransaction();
        try {
            $detailsData = [];
            $subtotalGeneral = 0;

            foreach ($request->product_id as $key => $productId) {
                $quantity = $request->quantity[$key];
                $price = $request->price[$key];
                $discount = $request->discount[$key];

                $lineSubtotal = ($quantity * $price) * (1 - $discount / 100);
                $subtotalGeneral += $lineSubtotal;

                $detailsData[] = [
                    "product_id" => $productId,
                    "quantity" => $quantity,
                    "price" => $price,
                    "discount" => $discount,
                ];
            }

            if (empty($detailsData)) {
                DB::rollBack();
                return redirect()->back()->withInput()->withErrors(['product_id' => 'Debe agregar al menos un producto a la venta.']);
            }

            $tax = $request->tax;
            $total = $subtotalGeneral * (1 + ($tax / 100));

            $sale = Sale::create([
                'client_id' => $request->client_id,
                'user_id' => Auth::id(),
                'sale_date' => Carbon::now('America/Lima'), 
                'tax' => $tax,
                'total' => $total,
                'status' => 'VALID',
            ]);

            $sale->saleDetails()->createMany($detailsData);

            foreach ($detailsData as $detail) {
                $product = Product::findOrFail($detail['product_id']);
                $product->decrement('stock', $detail['quantity']);
                if (isset($product->min_stock) && $product->min_stock > 0 && $product->stock <= $product->min_stock) {
                    session()->push('low_stock_alerts', "¡Alerta! El producto '{$product->name}' (ID: {$product->id}) ha alcanzado o está por debajo del stock mínimo. Stock actual: {$product->stock}, Mínimo: {$product->min_stock}.");
                }
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Venta registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al registrar la venta. Por favor, inténtelo de nuevo.');
        }
    }


    public function show(Sale $sale)
    {
        $sale->loadMissing(['client', 'user', 'saleDetails.product']); 
        return view('admin.sale.show', compact('sale')); 
    }

    public function edit(Sale $sale)
    {
        $clients = Client::get();
        $products = Product::get(); 
        $sale->loadMissing('saleDetails.product');
        return view('admin.sale.edit', compact('sale', 'clients', 'products')); 
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
        return redirect()->route('sales.index')->with('success', 'Venta actualizada (Lógica pendiente).');
    }

    public function destroy(Sale $sale) 
    {
        if ($sale->status !== 'VALID') {
            return redirect()->route('sales.index')->with('error', 'Esta venta no se puede anular porque no está en estado VÁLIDO.');
        }

        DB::beginTransaction();
        try {
            $sale->loadMissing('saleDetails.product'); 
            foreach ($sale->saleDetails as $detail) {
                $product = $detail->product;
                if ($product) {
                    $product->increment('stock', $detail->quantity); 
                }
            }
            $sale->update(['status' => 'CANCELLED']);
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Venta anulada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales.index')->with('error', 'Error al anular la venta. Por favor, inténtelo de nuevo.');
        }

    }

    /**
     * Genera una vista para la impresión/PDF de la venta.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function pdf(Sale $sale)
    {
        $sale->loadMissing(['client', 'user', 'saleDetails.product']);

        $subtotal = 0;
        foreach ($sale->saleDetails as $detail) {
            $lineSubtotal = ($detail->quantity * $detail->price) * (1 - $detail->discount / 100);
            $subtotal += $lineSubtotal;
        }
        return view('admin.sale.pdf', compact('sale', 'subtotal'));
    }



}
