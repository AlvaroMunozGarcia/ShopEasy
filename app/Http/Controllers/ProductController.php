<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request; // <-- Añadir esta línea
use Milon\Barcode\DNS1D; // <-- Añadir esta línea


class ProductController extends Controller
{

    public function index(Request $request) // <-- Modificar para aceptar Request
    {
        // Iniciar la consulta de productos con las relaciones necesarias
        $query = Product::with(['category', 'provider'])->orderBy('id', 'desc');

        $filtered_provider_name = null;
        $provider_id_for_breadcrumb_link = null; // Para usar en breadcrumbs o enlaces
        $provider_id_for_create_link = null; // Para el botón "Añadir Producto" en la vista index

        // Verificar si se está filtrando por proveedor
        if ($request->has('provider_id') && $request->provider_id) {
            $provider_id = $request->provider_id;
            $query->where('provider_id', $provider_id); // Aplicar el filtro

            $provider = Provider::find($provider_id); // Obtener el proveedor para mostrar su nombre
            if ($provider) {
                $filtered_provider_name = $provider->name;
                $provider_id_for_breadcrumb_link = $provider->id;
                $provider_id_for_create_link = $provider->id; // Mantener el filtro al añadir nuevo
            }
        }

        $products = $query->get(); // Ejecutar la consulta

        // Pasar las variables a la vista
        return view('admin.product.index', compact(
            'products',
            'filtered_provider_name', // Para mostrar que está filtrado
            'provider_id_for_breadcrumb_link', // Para enlaces en la vista
            'provider_id_for_create_link' // Para el botón de añadir
        ));
    }
    public function create(Request $request) // <-- Modificar para aceptar Request
    {
        $categories=Category::get();
        $providers=Provider::get();

        // Obtener el provider_id de la query string, si existe
        $selected_provider_id = $request->query('provider_id', null);

        // Opcional: Validar que el provider_id es válido si quieres ser extra seguro
        if ($selected_provider_id && !Provider::find($selected_provider_id)) {
            $selected_provider_id = null; // Si no es válido, no preseleccionar nada
        }

        return view('admin.product.create', compact(
            'categories',
            'providers',
            'selected_provider_id' // <-- Pasar la variable a la vista
        ));
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
            if ($product->image && file_exists(public_path('image/' . $product->image))) {
                unlink(public_path('image/' . $product->image));
            }
            $attributesToUpdate['image'] = $new_image_name; // Establecer el nuevo nombre de imagen
        }

        // Remover 'picture' del array si existe, ya que no es una columna de BD
        unset($attributesToUpdate['picture']);
        // --- DEBUG 2: Ver los atributos que se van a actualizar DESPUÉS de la validación ---
        // Para la segunda prueba, comenta DEBUG 1 y DESCOMENTA esta línea:
        // // dd($attributesToUpdate);
        // --- FIN DEBUG 2 ---
        $product->update($attributesToUpdate);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }
}
