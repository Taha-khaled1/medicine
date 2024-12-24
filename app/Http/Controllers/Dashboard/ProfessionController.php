<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfessionController extends Controller
{

    function __construct()
    {
        // $this->middleware('permission:الاحجام', ['only' => ['index']]);
        // $this->middleware('permission:اضافة حجم', ['only' => ['store']]);
        // $this->middleware('permission:تعديل حجم', ['only' => ['update']]);
        // $this->middleware('permission:حذف حجم', ['only' => ['destroy']]);
    }
    public function index()
    {
        $professions = Profession::all();
        return view('dashboard.profession.index', compact('professions'));
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
            'title_en' => 'required|unique:professions|max:100',
            'title_ar' => 'required|unique:professions|max:100',

        ], [
            'title_en.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            'title_en.unique' => 'اسم الفئة باللغة الإنجليزية مُسجل مسبقًا',
            'title_en.max' => 'يجب ألا يتجاوز اسم الفئة باللغة الإنجليزية 100 حرف',
            'title_ar.required' => 'يرجى إدخال اسم الفئة باللغة العربية',
            'title_ar.unique' => 'اسم الفئة باللغة العربية مُسجل مسبقًا',
            'title_ar.max' => 'يجب ألا يتجاوز اسم الفئة باللغة العربية 100 حرف',

        ]);

        if ($validator->fails()) {
            session()->flash('delete', 'لم يتم الحفظ  بسبب مشكله ما');

            return redirect()->back()->withErrors($validator)->withInput();
        }
        $size = new Profession;
        $size->title_en = $request->input('title_en');
        $size->title_ar = $request->input('title_ar');
        $size->save();
        session()->flash('Add', 'تم الاضافه بنجاح ');

        return redirect()->route('professions.index')->with('success', 'size created successfully');
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
            'title_en' => 'required|max:100|unique:professions,title_en,' . $request->id . ',id',
            'title_ar' => 'required|max:100|unique:professions,title_ar,' . $request->id . ',id',
        ], [
            'title_en.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            'title_en.max' => 'يجب أن يكون طول اسم الفئة باللغة الإنجليزية حتى 100 حرف',
            'title_en.unique' => 'اسم الفئة باللغة الإنجليزية مسجل بالفعل',
            'title_ar.required' => 'يرجى إدخال اسم الفئة باللغة العربية',
            'title_ar.max' => 'يجب أن يكون طول اسم الفئة باللغة العربية حتى 100 حرف',
            'title_ar.unique' => 'اسم الفئة باللغة العربية مسجل بالفعل',

        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $size = Profession::findOrFail($request->id);

        $data = $request->except(['_token', '_method']);


        $size->update($data);
        session()->flash('Add', 'تم تحديث البيانات  بنجاح ');
        return redirect()->route('professions.index')->with('success', 'size updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $size = Profession::find($request->id);
        $size->delete();
        session()->flash('delete', 'تم الحذف بنجاح ');
        return redirect()->route('professions.index')->with('success', 'size deleted successfully');
    }
}
