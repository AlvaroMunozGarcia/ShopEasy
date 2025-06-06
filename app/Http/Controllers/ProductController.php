<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Category;
use App\Models\Provider; 
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D; 


class ProductController extends Controller
{

    public function index(Request $request) 
    {
        $query = Product::with(['category', 'provider'])->orderBy('id', 'desc');

        $filtered_provider_name = null;
        $provider_id_for_breadcrumb_link = null; 
        $provider_id_for_create_link = null; 

        if ($request->has('provider_id') && $request->provider_id) {
            $provider_id = $request->provider_id;
            $query->where('provider_id', $provider_id); 

            $provider = Provider::find($provider_id); 
            if ($provider) {
                $filtered_provider_name = $provider->name;
                $provider_id_for_breadcrumb_link = $provider->id;
                $provider_id_for_create_link = $provider->id; 
            }
        }

        $products = $query->get(); 
        return view('admin.product.index', compact(
            'products',
            'filtered_provider_name', 
            'provider_id_for_breadcrumb_link', 
            'provider_id_for_create_link' 
        ));
    }
    public function create(Request $request) 
    {
        $categories=Category::get();
        $providers=Provider::get();
        $selected_provider_id = $request->query('provider_id', null);
        if ($selected_provider_id && !Provider::find($selected_provider_id)) {
            $selected_provider_id = null; 
        }

        return view('admin.product.create', compact(
            'categories',
            'providers',
            'selected_provider_id' 
             ));
    }
    public function store(StoreRequest $request)
    {
        $attributesToCreate = $request->validated(); 

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
        $path = $file->store('products', 'public'); 
        $attributesToCreate['image'] = $path; 
        } else {
            $attributesToCreate['image'] = null;
        }

        unset($attributesToCreate['picture']);
        Product::create($attributesToCreate);
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }
    public function show(Product $product)
    {
       
        return view('admin.product.show', compact('product')); 
    }
    public function edit(Product $product)
    {
        $categories=Category::get();
        $providers=Provider::get();
        return view('admin.product.edit', compact('product', 'categories', 'providers')); 

    }
    public function update(UpdateRequest $request, Product $product)
    {
        $attributesToUpdate = $request->validated(); 

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image); 
            }
        $path = $file->store('products', 'public'); 
        $attributesToUpdate['image'] = $path; 
        }
        unset($attributesToUpdate['picture']);
        $product->update($attributesToUpdate);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }
    public function destroy(Product $product)
    {
    if ($product->image) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image); 
    }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto archivado correctamente.');
    }
}
