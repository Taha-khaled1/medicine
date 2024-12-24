<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
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
        $skills = Skill::all();
        return view('dashboard.skills.index', compact('skills'));
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
            'title' => 'required|unique:skills|max:100',
        ], [
            'title.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            'title.unique' => 'اسم الفئة باللغة الإنجليزية مُسجل مسبقًا',
            'title.max' => 'يجب ألا يتجاوز اسم الفئة باللغة الإنجليزية 100 حرف',


        ]);

        if ($validator->fails()) {
            session()->flash('delete', 'لم يتم الحفظ  بسبب مشكله ما');

            return redirect()->back()->withErrors($validator)->withInput();
        }
        $size = new Skill;
        $size->title = $request->input('title');
        $size->save();
        session()->flash('Add', 'تم الاضافه بنجاح ');

        return redirect()->route('skills.index')->with('success', 'size created successfully');
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
            'title' => 'required|max:100|unique:skills,title,' . $request->id . ',id',
        ], [
            'title.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            'title.max' => 'يجب أن يكون طول اسم الفئة باللغة الإنجليزية حتى 100 حرف',
            'title.unique' => 'اسم الفئة باللغة الإنجليزية مسجل بالفعل',


        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $size = Skill::findOrFail($request->id);

        $data = $request->except(['_token', '_method']);


        $size->update($data);
        session()->flash('Add', 'تم تحديث البيانات  بنجاح ');
        return redirect()->route('skills.index')->with('success', 'size updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $size = Skill::find($request->id);
        $size->delete();
        session()->flash('delete', 'تم الحذف بنجاح ');
        return redirect()->route('skills.index')->with('success', 'size deleted successfully');
    }
}
