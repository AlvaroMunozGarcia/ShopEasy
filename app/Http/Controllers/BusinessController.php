<?php

namespace App\Http\Controllers;
use App\Models\Business;
use App\Http\Requests\Business\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class BusinessController extends Controller
{
    public function index()
    {
       $business=Business::where('id',1)->firstOrFail();
       return view('admin.business.index',compact('business'));
    }

    public function update(UpdateRequest $request, Business $Business)
    {
        $Business->update($request->all());
        return redirect()->route('business.index');
    }
}
