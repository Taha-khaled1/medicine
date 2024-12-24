<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Country;
use App\Traits\ImageProcessing;
use Illuminate\Support\Facades\Validator;
use Monarobase\CountryList\CountryListFacade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    use ImageProcessing;

    function __construct()
    {
        // $this->middleware('permission:الدول و الضرائب', ['only' => ['index', 'store', 'show', 'edit', 'update', 'destroy']]);
        //  $this->middleware('permission:اعدادت الهدايا', ['only' => ['gift','updateGift']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countriesList = DB::table("countries")->get();
        $countries = Country::all();
        return view('dashboard.country.index', compact('countries', 'countriesList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $countryId = $request->countryId;
        $data = $request->except('_token', 'countryId');

        $validatedData = Validator::make($data, [
            'code' => 'required|unique:countries_admins|string|max:255',
            'exchange_rate' => 'required|numeric|max:255',
            'country_tax' => [
                'nullable',
                'numeric',
                'between:0,999999.9'
            ],
        ], [
            'code.required' => 'يرجي ادخال كود الدوله',
            'code.unique' => 'كود الدوله مسجل مسبقا',
            'exchange_rate.required' => 'يرجي ادخال سعر الصرف',
            'exchange_rate.numeric' => 'يرجي ادخال رقم',
            'country_tax.numeric' => 'يرجي ادخال رقم',
        ]);

        if ($validatedData->fails()) {
            return redirect()->route('countries')->withErrors($validatedData)->withInput();
        }

        $country = DB::table("countries")
            ->whereId($countryId)
            ->first();

        $data['name_ar'] = $country->name_ar;
        $data['name_en'] = $country->name_en;

        if ($request->hasFile('image')) {
            $data['image'] = 'imagesfp/countries/' . $this->saveImage($request->file('image'), 'countries');
        }

        Country::create($data);

        return redirect()->route('countries')->with('success', 'تم اضافة الدوله بنجاح');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $countryId = $request->id;
        $data = $request->except('_token', '_method');

        $validatedData = Validator::make($data, [
            //            'code' => 'required|unique:countries_admins|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:countries_admins,code,' . $countryId
            ],
            'exchange_rate' => 'required|numeric|max:255',
            'country_tax' => [
                'nullable',
                'numeric',
                'between:0,999999.9'
            ],
        ], [
            'code.required' => 'يرجي ادخال كود الدوله',
            'code.unique' => 'كود الدوله مسجل مسبقا',
            'exchange_rate.required' => 'يرجي ادخال سعر الصرف',
            'exchange_rate.numeric' => 'يرجي ادخال رقم',
            'country_tax.numeric' => 'يرجي ادخال رقم',
        ]);

        if ($validatedData->fails()) {
            return redirect()->route('countries')->withErrors($validatedData)->withInput();
        }

        $country = Country::findOrFail($countryId);

        if ($request->hasFile('image')) {
            $this->deleteImage($country->image);
            $data['image'] = 'imagesfp/countries/' . $this->saveImage($request->file('image'), 'countries');
        }

        $country->update($data);

        return back()->with('success', 'تم تعديل الدوله بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $country = Country::findorFail($id);
        $country->delete();

        return back()->with('success', 'تم حذف الدوله بنجاح');
    }
}
