<?php

namespace App\Http\Controllers;

use App\Models\Printer; // Asegúrate que el modelo Printer esté importado
use App\Http\Requests\Printer\UpdateRequest; // Importa el FormRequest que creamos
// Quita 'use Illuminate\Http\Request;' si ya no lo necesitas directamente

class PrinterController extends Controller
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
     * Muestra el formulario para editar la información de la impresora.
     * Asumimos que solo hay UNA configuración de impresora principal.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
       // Intenta obtener el primer registro de Printer. Si no existe, fallará.
       // Asegúrate de que exista un registro (p.ej., mediante seeders o manualmente).
       $printer = Printer::firstOrFail(); // Obtiene el primer registro o lanza excepción
       return view('admin.printer.index', compact('printer'));
    }

    /**
     * Actualiza la información de la impresora.
     *
     * @param  \App\Http\Requests\Printer\UpdateRequest  $request
     * @param  \App\Models\Printer  $printer (Inyectado por Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Printer $printer) // Cambiado $Printer a $printer para convención
    {
        // Actualiza usando solo los datos validados por UpdateRequest
        $printer->update($request->validated());

        // Redirige a la ruta con nombre completo del admin y añade mensaje flash de éxito
        return redirect()->route('admin.printer.index')
                         ->with('success', 'Configuración de impresora actualizada correctamente.');
    }
}
