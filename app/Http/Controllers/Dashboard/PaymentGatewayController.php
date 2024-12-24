<?php


namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\PaymentGateway;
use App\Traits\ImageProcessing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{
    use ImageProcessing;

    public function index()
    {
        $gateways = PaymentGateway::orderBy('arrange')->orderByDesc('created_at')->get();
        return view('dashboard.payment-gateway.index', compact('gateways'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name_en' => 'required|unique:payment_gateways|max:100',
            'name_ar' => 'required|unique:payment_gateways|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
            'arrange' => 'integer',
        ], [
            'name_en.required' => 'يرجى إدخال اسم بوابة الدفع باللغة الإنجليزية',
            'name_en.unique' => 'اسم بوابة الدفع باللغة الإنجليزية مُسجل مسبقًا',
            'name_en.max' => 'يجب ألا يتجاوز اسم بوابة الدفع باللغة الإنجليزية 100 حرف',
            'name_ar.required' => 'يرجى إدخال اسم بوابة الدفع باللغة العربية',
            'name_ar.unique' => 'اسم بوابة الدفع باللغة العربية مُسجل مسبقًا',
            'name_ar.max' => 'يجب ألا يتجاوز اسم بوابة الدفع باللغة العربية 100 حرف',
            'image.image' => 'يرجى اختيار صورة صالحة',
            'image.mimes' => 'صيغ الصور المدعومة هي: jpeg, png, jpg, gif, svg',
            'status.boolean' => 'قيمة حالة بوابة الدفع يجب أن تكون صحيحة أو خاطئة',
            'arrange.integer' => 'قيمة ترتيب بوابة الدفع يجب أن تكون عددًا صحيحًا',
        ]);

        if ($validator->fails()) {
            session()->flash('delete', 'لم يتم حفظ بوابة الدفع بسبب مشكله ما');

            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data['image'] = $this->saveImage($request->file('image'), 'category');
        $category = new PaymentGateway;
        $category->name_en = $request->input('name_en');
        $category->name_ar = $request->input('name_ar');
        $category->image =  'imagesfp/category/' . $data['image'];
        $category->status = $request->input('status', true);
        // $category->data = "test";
        $category->key = $request->input('key');
        $category->arrange = $request->input('arrange', 1);
        $category->save();
        session()->flash('Add', 'تم اضافة بوابة الدفع بنجاح ');

        return back();
    }

    public function updateStatusCatogery(Request $request)
    {
        $isToggleOnString = (string) $request->isToggleOn;
        $status = true;
        $categoryId = $request->input('categoryId');
        if ($isToggleOnString == "true") {
            $status = true;
        } else {
            $status = false;
        }



        $category = PaymentGateway::find($categoryId);

        if ($category) {
            // Update the status field
            $category->status = $status;
            $category->save();

            return response()->json(['success' => true, 'message' => 'category status  updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'category not found']);
    }
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required|max:100|unique:payment_gateways,name_en,' . $request->id . ',id',
            'name_ar' => 'required|max:100|unique:payment_gateways,name_ar,' . $request->id . ',id',
            'image' => 'nullable|image',
            'status' => 'boolean',
            'arrange' => 'integer',
        ], [
            'name_en.required' => 'يرجى إدخال اسم بوابة الدفع باللغة الإنجليزية',
            'name_en.max' => 'يجب أن يكون طول اسم بوابة الدفع باللغة الإنجليزية حتى 100 حرف',
            'name_en.unique' => 'اسم بوابة الدفع باللغة الإنجليزية مسجل بالفعل',
            'name_ar.required' => 'يرجى إدخال اسم بوابة الدفع باللغة العربية',
            'name_ar.max' => 'يجب أن يكون طول اسم بوابة الدفع باللغة العربية حتى 100 حرف',
            'name_ar.unique' => 'اسم بوابة الدفع باللغة العربية مسجل بالفعل',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة',
            'status.boolean' => 'حالة بوابة الدفع يجب أن تكون صحيحة أو خاطئة',
            'arrange.integer' => 'الترتيب يجب أن يكون عددًا صحيحًا',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $category = PaymentGateway::findOrFail($request->id);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('image')) {
            // Delete the existing image
            $this->deleteImage($category->image);

            // Save the new image
            $data['image'] =  $this->saveImage($request->file('image'), 'category');
            $data['image'] = 'imagesfp/category/' . $data['image'];
        }

        $category->update($data);
        session()->flash('Add', 'تم تحديث بيانات بوابة الدفع بنجاح ');
        return back();
    }

    public function destroy(Request $request)
    {
        $category = PaymentGateway::find($request->id);

        // Delete the associated image
        $this->deleteImage($category->image);

        // Delete the category
        $category->delete();
        session()->flash('delete', 'تم حذف بوابة الدفع ');
        return back();
    }
}
