<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Purchase;
use App\Models\Provider;
use App\Models\Product;
use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use function Ramsey\Uuid\v1;

class SaleController extends Controller
{
    
    public function index()
    {
        $sales=Sale::get();
        return view('admin.sale.index',compact('sales'));
    }
public function create()
{
    $clients = Client::get();
    $products = Product::get(); 
    return view('admin.sale.create', compact('clients', 'products')); // <-- Añadir products
}

    public function store(StoreRequest $request)
    {
        $sale= Sale::create($request->all());
        foreach ($request->product_id as $key=>$product){
            $results[]= array("product_id"=>$request->product_id[$key],
            "quantity"=>$request->quantity[$key],"price"=>$request->price[$key],
        "discount"=>$request->discount[$key]);

        }
        $sale->shoppingDetails()->createMany($results);
        return redirect()->route('sales.index');
    }

    public function show(Sale $sale)
    {
        return view('admin.sale.show',compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $clients=Client::get();
        return view('admin.sale.show', compact('sale'));
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
       // $purchase->update($request->all());
        //return redirect()->route('purchase.index');
    }

    public function destroy(Purchase $purchase)
    {
       //$purchase->delete();
       //return redirect()->route('purchases.index'); 
    }
}
