<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {
            $countries = Country::orderBy('created_at')->with('cities')->get();
            return successResponse(['countries' => $countries]);
        } catch (\Throwable $e) {
            return errorResponse(__('custom.failed_to_retrieve_data'));
        }
    }
}