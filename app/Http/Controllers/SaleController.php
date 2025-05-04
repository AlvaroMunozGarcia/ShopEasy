<?php

namespace App\Http\Controllers;

use App\Models\Client;
// Quita Purchase y Provider si no los usas aquí
// use App\Models\Purchase;
// use App\Models\Provider;
use App\Models\Product; // Necesario para actualizar stock
use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\Models\Sale;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Auth; // Asegúrate que esté importado
use Carbon\Carbon; // Asegúrate que esté importado

// Quita 'use function Ramsey\Uuid\v1;' si no lo usas

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
        $products = Product::get();
        return view('admin.sale.create', compact('clients', 'products'));
    }

    public function store(StoreRequest $request) // Laravel ya validó aquí
    {
        // 1. La validación ya se hizo en StoreRequest (incluyendo consistencia y stock)
    
        // 2. Preparar detalles y calcular subtotal (Ahora es seguro iterar)
        $detailsData = [];
        $subtotalGeneral = 0;
    
        // Ya no necesitas el 'if' de consistencia aquí
        // Ya no necesitas la validación de stock dentro del loop
    
        foreach ($request->product_id as $key => $productId) {
            // Los datos son consistentes y hay stock gracias al StoreRequest
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
    
        // Ya no necesitas la validación de empty($detailsData) aquí,
        // porque 'product_id.min:1' en StoreRequest ya lo asegura.
    
        // 3. Calcular total incluyendo impuesto
        $tax = $request->tax;
        $total = $subtotalGeneral * (1 + ($tax / 100));
    
        // 4. Crear y guardar la cabecera de la Venta
        $sale = Sale::create([
            'client_id' => $request->client_id,
            'user_id' => Auth::id(),
            'sale_date' => Carbon::now(),
            'tax' => $tax,
            'total' => $total,
            'status' => 'VALID',
        ]);
    
        // 5. Guardar los detalles
        $sale->saleDetails()->createMany($detailsData);
    
        // 6. Actualizar el stock de los productos vendidos
        foreach ($detailsData as $detail) {
            // Usamos findOrFail para seguridad, aunque StoreRequest ya validó la existencia
            $product = Product::findOrFail($detail['product_id']);
            $product->decrement('stock', $detail['quantity']);
        }
    
        return redirect()->route('sales.index')->with('success', 'Venta registrada correctamente.');
    }


    public function show(Sale $sale)
    {
        // Verifica las relaciones antes de pasar a la vista
        $sale->load(['client', 'user', 'saleDetails.product']); // Carga ansiosa
        return view('admin.sale.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        // El método edit debería mostrar un formulario de edición, no la vista show
        $clients = Client::get();
        $products = Product::get(); // Necesitarás los productos para editar
        // Carga los detalles actuales para mostrarlos en el formulario
        $sale->load('saleDetails.product');
        return view('admin.sale.edit', compact('sale', 'clients', 'products')); // Cambia a la vista de edición
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
        // Lógica para actualizar la venta (similar a store pero con update)
        // ¡Necesitas implementar esto!
        // - Recalcular total si cambian detalles/impuesto
        // - Actualizar detalles (borrar antiguos y crear nuevos, o actualizar existentes)
        // - Ajustar stock (revertir stock antiguo, decrementar nuevo)
        // $sale->update($request->all()); // Esto no será suficiente
        return redirect()->route('sales.index')->with('success', 'Venta actualizada (Lógica pendiente).');
    }

    public function destroy(Sale $sale) // Cambiado de Purchase a Sale
    {
        // En lugar de borrar, cambia el estado a 'CANCELLED'
        // y revierte el stock
        if ($sale->status == 'VALID') {
             $sale->load('saleDetails.product'); // Carga detalles
             foreach ($sale->saleDetails as $detail) {
                 $product = $detail->product;
                 if ($product) {
                     $product->increment('stock', $detail->quantity); // Devuelve stock
                 }
             }
             $sale->update(['status' => 'CANCELLED']);
             return redirect()->route('sales.index')->with('success', 'Venta anulada correctamente.');
        } else {
             return redirect()->route('sales.index')->with('error', 'Esta venta ya está anulada o tiene un estado inválido.');
        }

        // $sale->delete(); // Evita borrar físicamente si quieres mantener historial
    }


    public function pdf(Sale $sale)
    {
        
        $sale->load('client', 'user', 'saleDetails.product');
        $pdfCreationDate = Carbon::now()->format('Y-m-d H:i:s');

        $pdf = PDF::loadView('admin.sale.pdf', compact('sale', 'pdfCreationDate'));
        $fileName = 'venta_' . $sale->id . '_' . $sale->sale_date->format('Ymd') . '.pdf';

        return $pdf->stream($fileName);
    }


}
