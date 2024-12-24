<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
     function __construct()
    {
         $this->middleware('permission:الالوان', ['only' => ['index']]);
         $this->middleware('permission:اضافة لون', ['only' => ['store']]);
         $this->middleware('permission:تعديل لون', ['only' => ['update']]);
         $this->middleware('permission:حذف لون', ['only' => ['destroy']]);
    }
    public function index()
    {
        $colors = Color::all();
    

        return view('dashboard.color.index', compact('colors'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|unique:colors|max:100',
            'name_ar' => 'required|unique:colors|max:100',
            'color_code' => 'required|max:50',

        ], [
            'name_en.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            'name_en.unique' => 'اسم الفئة باللغة الإنجليزية مُسجل مسبقًا',
            'name_en.max' => 'يجب ألا يتجاوز اسم الفئة باللغة الإنجليزية 100 حرف',
            'name_ar.required' => 'يرجى إدخال اسم الفئة باللغة العربية',
            'name_ar.unique' => 'اسم الفئة باللغة العربية مُسجل مسبقًا',
            'name_ar.max' => 'يجب ألا يتجاوز اسم الفئة باللغة العربية 100 حرف',

        ]);

        if ($validator->fails())
        {
            session()->flash('delete', 'لم يتم حفظ بسبب مشكله ما');

            return redirect()->back()->withErrors($validator)->withInput();
        }
        $color = new Color;
        $color->name_en = $request->input('name_en');
        $color->name_ar = $request->input('name_ar');
        $color->color_code = $request->input('color_code');
        $color->save();
        session()->flash('Add', 'تم الاضافه بنجاح ');

        return redirect()->route('colors.index')->with('success', 'color created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|max:100|unique:colors,name_en,' . $request->id . ',id',
            'name_ar' => 'required|max:100|unique:colors,name_ar,' . $request->id . ',id',
            'color_code' => 'required|max:50',
        ], [
            'name_en.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            'name_en.max' => 'يجب أن يكون طول اسم الفئة باللغة الإنجليزية حتى 100 حرف',
            'name_en.unique' => 'اسم الفئة باللغة الإنجليزية مسجل بالفعل',
            'name_ar.required' => 'يرجى إدخال اسم الفئة باللغة العربية',
            'name_ar.max' => 'يجب أن يكون طول اسم الفئة باللغة العربية حتى 100 حرف',
            'name_ar.unique' => 'اسم الفئة باللغة العربية مسجل بالفعل',

        ]);


        if ($validator->fails())
        { session()->flash('delete', 'لم يتم حفظ بسبب مشكله ما');
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $color = Color::findOrFail($request->id);

        $data = $request->except(['_token', '_method']);


        $color->update($data);
        session()->flash('Add', 'تم تحديث البيانات  بنجاح ');
        return redirect()->route('colors.index')->with('success', 'color updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $color = Color::find($request->id);




        $color->delete();
        session()->flash('delete', 'تم الحذف  ');
        return redirect()->route('colors.index')->with('success', 'color deleted successfully');
    }
}