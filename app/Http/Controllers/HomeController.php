<?php

namespace App\Http\Controllers;

use App\Models\Property;

class HomeController extends Controller
{
    public function index()
    {
        $properties = Property::with('pictures')->available(false)->recent()->limit(4)->get();
        return view('home', [
            'properties' => $properties,
        ]);
    }
}
