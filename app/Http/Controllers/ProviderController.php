<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Http\Requests\Provider\UpdateRequest;
use App\Http\Requests\Provider\StoreRequest;

class ProviderController extends Controller
{
    
    public function index()
    {
        $providers=Provider::get();
        return view('admin.provider.index', compact('providers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.provider.create');
    }

    public function store(StoreRequest $request)
    {
        Provider::create($request->all());
        return redirect()->route('providers.index');
    }

    public function show(Provider $provider)
    {
        return view('admin.provider.show', compact('provider') );
    }

    public function edit(Provider $provider)
    {
        return view('admin.provider.edit',compact('provider'));

    }

   
    public function update(UpdateRequest $request, Provider $provider)
    {
        $provider->update($request->all());
        return redirect()->route('providers.index');
    }

  
    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->route('providers.index')
                         ->with('success', 'Proveedor archivado correctamente.');
    }
}
