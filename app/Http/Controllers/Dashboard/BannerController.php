<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Traits\ImageProcessing;

class BannerController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:البنرات الإعلانية', ['only' => ['index', 'store', 'show', 'edit', 'update', 'destroy']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    use ImageProcessing;
    public function index()
    {
        $banners = Banner::orderBy('arrange')->get();
        $SubCategorys = SubCategory::all();
        return view('dashboard.banner.index', ['banners' => $banners, "SubCategorys" =>  $SubCategorys]);
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:banners|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
            'arrange' => 'integer',
        ], [

            'name.required' => 'يرجى إدخال اسم الفئة باللغة العربية',
            'name.unique' => 'اسم الفئة باللغة العربية مُسجل مسبقًا',
            'name.max' => 'يجب ألا يتجاوز اسم الفئة باللغة العربية 100 حرف',
            'image.image' => 'يرجى اختيار صورة صالحة',
            'image.mimes' => 'صيغ الصور المدعومة هي: jpeg, png, jpg, gif, svg',
            'status.boolean' => 'قيمة حالة الفئة يجب أن تكون صحيحة أو خاطئة',
            'arrange.integer' => 'قيمة ترتيب الفئة يجب أن تكون عددًا صحيحًا',
        ]);

        if ($validator->fails()) {
            session()->flash('delete', 'لم يتم حفظ الاعلان بسبب مشكله ما');

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data['image'] = $this->saveImage($request->file('image'), 'banner');
        $banner = new Banner;
        // $banner->name_en = $request->input('name_en');
        $banner->name = $request->input('name');
        $banner->type = $request->input('type');
        $banner->banner_url = $request->input('banner_url');
        $banner->image =  'imagesfp/banner/' . $data['image'];
        $banner->status = $request->input('status', true);
        $banner->arrange = $request->input('arrange', 1);
        $banner->save();
        session()->flash('Add', 'تم اضافة الاعلان بنجاح ');

        return redirect()->route('banners')->with('success', 'لآanner created successfully');
    }


    public function updateStatusBanner(Request $request)
    {
        $isToggleOnString = (string) $request->isToggleOn;
        $status = true;
        $categoryId = $request->input('categoryId');
        if ($isToggleOnString == "true") {
            $status = true;
        } else {
            $status = false;
        }



        $banner = Banner::find($categoryId);

        if ($banner) {
            // Update the status field
            $banner->status = $status;
            $banner->save();

            return response()->json(['success' => true, 'message' => 'banner status  updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'banner not found']);
    }



    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'name_en' => 'required|max:100|unique:categories,name_en,' . $request->id . ',id',
            'name' => 'required|max:100|unique:banners,name,' . $request->id . ',id',
            'image' => 'nullable|image',
            'status' => 'boolean',
            'arrange' => 'integer',
        ], [
            // 'name_en.required' => 'يرجى إدخال اسم الفئة باللغة الإنجليزية',
            // 'name_en.max' => 'يجب أن يكون طول اسم الفئة باللغة الإنجليزية حتى 100 حرف',
            // 'name_en.unique' => 'اسم الفئة باللغة الإنجليزية مسجل بالفعل',
            'name.required' => 'يرجى إدخال اسم الفئة باللغة العربية',
            'name.max' => 'يجب أن يكون طول اسم الفئة باللغة العربية حتى 100 حرف',
            'name.unique' => 'اسم الفئة باللغة العربية مسجل بالفعل',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة',
            'status.boolean' => 'حالة الفئة يجب أن تكون صحيحة أو خاطئة',
            'arrange.integer' => 'الترتيب يجب أن يكون عددًا صحيحًا',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $banner = Banner::findOrFail($request->id);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('image')) {
            $this->deleteImage($banner->image);
            $data['image'] =  $this->saveImage($request->file('image'), 'category');
            $data['image'] = 'imagesfp/category/' . $data['image'];
        }

        $banner->update($data);
        session()->flash('Add', 'تم تحديث بيانات البانر بنجاح ');
        return redirect()->route('banners')->with('success', 'Category updated successfully');
    }

    public function destroy(Request $request)
    {
        $category = Banner::find($request->id);

        // Delete the associated image
        $this->deleteImage($category->image);

        // Delete the category
        $category->delete();
        session()->flash('delete', 'تم حذف البانر ');
        return redirect()->route('banners')->with('success', 'Category deleted successfully');
    }
}