<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function index()
    {
        $complaints = ContactUs::all();

        return view('dashboard.contact-us.index', compact('complaints'));
    }


   public function destroy(Request $request)
    {
        $color = ContactUs::find($request->id);
        $color->delete();
        session()->flash('delete', 'تم حذف الشكوي بنجاح');
        return redirect()->route('contactus')->with('success', 'contactus deleted successfully');
    }
}