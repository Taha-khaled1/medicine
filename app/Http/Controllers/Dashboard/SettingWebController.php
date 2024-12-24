<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Gift;
use App\Models\Maintenance;
use App\Models\SettingWeb;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ImageProcessing;
use Illuminate\Support\Facades\Validator;

class SettingWebController extends Controller
{
    use ImageProcessing;
    function __construct()
    {
        $this->middleware('permission:اعدادت الصفحات', ['only' => ['index', 'updatewebsite', 'show', 'edit', 'update', 'destroy']]);
        $this->middleware('permission:اعدادت الهدايا', ['only' => ['gift', 'updateGift']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $settings = SettingWeb::first();

        return view('dashboard.setting.setting_web', compact('settings'));
    }



    public function gift()
    {
        $gift = Gift::first();
        return view('dashboard.setting.gift-setting', compact('gift'));
    }



    public function updateGift(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            // 'first_order' => 'nullable|string','confirmation_message' => 'nullable|string',
            // 'first_order_price' => 'nullable|numeric',
            // 'buying_specified_value' => 'nullable|string',
            // 'buying_specified_value_price' => 'nullable|numeric',
            'number_play' => 'nullable|numeric',
            // 'specified_value_price' => 'nullable|numeric',
            'confirmation_message' => 'nullable|string',
            'image_footer1' => 'nullable|string',
        ]);

        $giftData = [
            // 'first_order' => $request->input('first_order'),
            // 'confirmation_message' => $request->input('confirmation_message'),
            // 'first_order_price' => $request->input('first_order_price', 0),
            'number_play' => $request->input('number_play', 1),
            'confirmation_message' => $request->input('confirmation_message'),
            'image_footer1' => $request->input('image'),
            // 'buying_specified_value' => $request->input('buying_specified_value'),
            // 'buying_specified_value_price' => $request->input('buying_specified_value_price', 0),
            // 'specified_value_price' => $request->input('specified_value_price', 0),
        ];

        if ($request->hasFile('image')) {
            // echo "ss";
            $giftData['image_footer1'] = $this->saveImage($request->file('image'), 'setting');
            $giftData['image_footer1'] = 'imagesfp/setting/' .  $giftData['image_footer1'];
        }
        // return $giftData;
        Gift::updateOrCreate(['id' => $request->input('id')], $giftData);
        // $gift->save();
        setEnvironmentValue('CONFIRMATION_MESSAGE', $request->input('confirmation_message'));
        session()->flash('edit', 'تم تعديل الاعدادت بنجاح');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SettingWeb  $settingWeb
     * @return \Illuminate\Http\Response
     */
    public function colorweb()
    {
        $color = SettingWeb::select('color_primery')->first();
        return response()->json(['color' => $color], 200);
    }


    public function update(Request $request)
    {

        $rules = [
            'about_us_ar' => 'nullable|string',
            'about_us_en' => 'nullable|string',
            'terms_ar' => 'nullable|string',
            'terms_en' => 'nullable|string',
            'privacy_ar' => 'nullable|string',
            'privacy_en' => 'nullable|string',
            'return_policy_ar' => 'nullable|string',
            'return_policy_en' => 'nullable|string',
            'store_policy_ar' => 'nullable|string',
            'store_policy_en' => 'nullable|string',
            'seller_policy_ar' => 'nullable|string',
            'seller_policy_en' => 'nullable|string',
            'color_primery' => 'nullable|string',
            'color_second_primery' => 'nullable|string',
            'licance_web' => 'nullable|string',
            'banner' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        $settingWeb = SettingWeb::first();

        if ($settingWeb) {
            $settingWeb->update($validatedData);
            session()->flash('edit', 'تم تعديل الاعدادت بنجاح');
            return redirect()->route('setting_web')->with('edit', 'تم تعديل الاعدادت بنجاح');
        } else {
            session()->flash('delete', 'لم يتم تعديل الاعدادت');
            return redirect()->route('setting_web')->with('Erorr', 'لم يتم تعديل الاعدادت ');
        }
    }


    public function updatewebsite(Request $request)
    {
        // return $request;
        $website = Maintenance::first();
        $website->ismaintenanc = $request->active;
        $website->content = $request->content ?? "";
        $website->save();
        return back();
    }
}
