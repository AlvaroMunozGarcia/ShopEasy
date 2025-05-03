<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Necesario para manejar archivos

class BusinessController extends Controller
{
    /**
     * Constructor para aplicar middleware de rol Admin.
     */
    public function __construct()
    {
        // Asegura que solo los administradores puedan acceder a estos métodos
        $this->middleware('role:Admin');
    }

    /**
     * Muestra el formulario para editar la información del negocio.
     * Como generalmente solo hay UNA entrada para Business, usamos index
     * para mostrar directamente el formulario de edición.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Intenta obtener el primer registro de Business. Si no existe, crea uno vacío
        // o redirige/muestra error. Para este ejemplo, asumimos que SIEMPRE habrá uno (quizás creado por seeders).
        $business = Business::firstOrFail(); // Asegura que exista al menos un registro

        return view('admin.business.index', compact('business'));
    }


    /**
     * Actualiza la información del negocio en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business  $business  (Inyectado por Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Business $business)
    {
        // Validación básica (puedes crear un FormRequest para validación más compleja)
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validación para el logo
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
            'ruc' => 'required|string|max:20', // Ajusta la longitud máxima según necesites
        ]);

        // Prepara los datos para actualizar
        $data = $request->except('logo'); // Excluye el logo por ahora

        // Manejo de la subida del logo
        if ($request->hasFile('logo')) {
            // 1. Borrar el logo anterior si existe
            if ($business->logo && Storage::disk('public')->exists($business->logo)) {
                Storage::disk('public')->delete($business->logo);
            }

            // 2. Guardar el nuevo logo
            // Guarda en 'storage/app/public/logos' y obtiene la ruta relativa
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path; // Guarda la ruta en la base de datos
        }

        // Actualiza el registro del negocio
        $business->update($data);

        // Redirige de vuelta al formulario con un mensaje de éxito
        return redirect()->route('admin.business.index')
                         ->with('success', 'Información del negocio actualizada correctamente.');
    }

    // Nota: No incluimos create, store, show, edit (individual), destroy
    // porque generalmente se maneja una única entidad 'Business'.
    // 'index' actúa como 'edit' y 'update' guarda los cambios.
}
