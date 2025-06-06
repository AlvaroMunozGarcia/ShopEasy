<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class BusinessController extends Controller
{
    /**
     * Constructor para aplicar middleware de rol Admin.
     */
    public function __construct()
    {
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
        $business = Business::firstOrFail(); 

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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
            'ruc' => 'required|string|max:20', 
        ]);
        $data = $request->except('logo'); 
        if ($request->hasFile('logo')) {
            if ($business->logo && Storage::disk('public')->exists($business->logo)) {
                Storage::disk('public')->delete($business->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path; 
        }
        $business->update($data);
        return redirect()->route('admin.business.index')
                         ->with('success', 'Información del negocio actualizada correctamente.');
    }

 
}
