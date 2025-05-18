<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Category;
use App\Models\Provider;
use Milon\Barcode\DNS1D; // <-- Añadir esta línea


class ProductController extends Controller
{
    
    public function index()
    {
        $products=Product::get();
        return view('admin.product.index', compact('products'));
    }
    public function create()
    {
        $categories=Category::get();
        $providers=Provider::get();
        return view('admin.product.create', compact('categories','providers'));
    }
    public function store(StoreRequest $request)
    {
        $attributesToCreate = $request->validated(); // Obtener datos validados

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("/image/"), $image_name);
            $attributesToCreate['image'] = $image_name; // Guardar el nombre de la imagen
        } else {
            $attributesToCreate['image'] = null; // Opcional: establecer a null o un valor por defecto si no se sube imagen
        }

        // Remover 'picture' del array si existe, ya que no es una columna de BD
        unset($attributesToCreate['picture']);
        Product::create($attributesToCreate);
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }
    public function show(Product $product)
    {
        // Generar el código de barras HTML usando el campo 'code'
        // 'C128' es un tipo común, puedes cambiarlo (ej: 'EAN13' si tus códigos son EAN)
        // Los parámetros son: valor, tipo, altura (px), ancho_factor (1, 2, 3...), color, mostrar_valor_abajo (bool)
        $barcodeGenerator = new DNS1D();
        $barcodeHtml = $barcodeGenerator->getBarcodeHTML($product->code, 'C128', 2, 33, 'black', true);

        return view('admin.product.show', compact('product', 'barcodeHtml')); // <-- Pasar el HTML a la vista
    }
    public function edit(Product $product)
    {
        $categories=Category::get();
        $providers=Provider::get();
        return view('admin.product.edit', compact('product', 'categories', 'providers')); // <-- Vista correcta

    }
    public function update(UpdateRequest $request, Product $product)
    {
        $attributesToUpdate = $request->validated(); // Obtener datos validados

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $new_image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("/image/"), $new_image_name);

            // Opcional: Eliminar la imagen anterior si existe
            if ($product->image && file_exists(public_path('image/' . $product->image))) {
                unlink(public_path('image/' . $product->image));
            }
            $attributesToUpdate['image'] = $new_image_name; // Establecer el nuevo nombre de imagen
        }

        // Remover 'picture' del array si existe, ya que no es una columna de BD
        unset($attributesToUpdate['picture']);
        $product->update($attributesToUpdate);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }
}
