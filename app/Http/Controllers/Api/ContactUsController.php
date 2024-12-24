<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email' => 'nullable|string|email',
        ]);

        ContactUs::create($validatedData);

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
        ], 200);
    }


}