<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Traits\ImageProcessing;

class SettingController extends Controller
{   
      function __construct()
    {
         $this->middleware('permission:الاعدادت الرئيسيه', ['only' => ['index','show','edit','store','update','destroy']]);
        //  $this->middleware('permission:اعدادت الهدايا', ['only' => ['gift','updateGift']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    use ImageProcessing;

    public function index()
    {
        // $setting = Setting::first();
        $setting = Setting::where('user_id', Auth::user()->id)->first();
        return view('dashboard.setting.index', ['setting' => $setting]);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
     
          try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                // 'Website' => 'nullable|url',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'twitter' => 'nullable|string|max:255',
                'facebook' => 'nullable|string|max:255',
                'google' => 'nullable|string|max:255',
                'linkedin' => 'nullable|string|max:255',
                'aboutcompany' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'user_id'=>'required'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $data = [
                'company_name' => $request->username,
                'email' => $request->email, 
                'user_id' => $request->user_id,
                 'isadmin' => false,
                'company_phone' => $request->phone,
                'company_address' => $request->address,
                'twitter' => $request->twitter,
                'facebook' => $request->facebook,
                'google' => $request->google,
                'linkedin' => $request->linkedin,
                'biographical_information' => $request->aboutcompany,
                
            ];
            $setting = new Setting;
            if ($request->hasFile('image')) {
                $image = $this->saveImage($request->file('image'), 'setting');
                $data['logo'] = 'imagesfp/setting/' . $image;
            }

            $setting->create($data);

            session()->flash('Add', 'تم تحديث البيانات بنجاح');
            return back();
        } catch (\Throwable $th) {
            session()->flash('error', 'حدث خطأ أثناء تحديث البيانات. يُرجى المحاولة مرة أخرى.');
            return back();
        }
    }

    public function show(Setting $setting)
    {
        //
    }


    public function edit(Setting $setting)
    {
        //
    }

public function update(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'google' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'aboutcompany' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            session()->flash('delete', 'يوجد مشكله ما');
            return back()->withErrors($validator)->withInput();
        }

        $setting = Setting::findOrFail($request->user_id);



        $data = [
            'company_name' => $request->username,
            'email' => $request->email,
            // 'logo' => $data['logo'],
            'company_phone' => $request->phone,
            'company_address' => $request->address,
            'twitter' => $request->twitter,
            'facebook' => $request->facebook,
            'google' => $request->google,
            'linkedin' => $request->linkedin,
            'biographical_information' => $request->aboutcompany,
        ];
        if ($request->hasFile('image')) {
            $image = $this->saveImage($request->file('image'), 'setting');
            $data['logo'] = 'imagesfp/setting/' . $image;
        }
        $setting->update($data);

        session()->flash('Add', 'تم تحديث البيانات بنجاح');
        return back();
    } catch (\Throwable $th) {
        session()->flash('error', 'حدث خطأ أثناء تحديث البيانات. يُرجى المحاولة مرة أخرى.');
        return back();
    }
}



    public function destroy(Setting $setting)
    {
        //
    }
}