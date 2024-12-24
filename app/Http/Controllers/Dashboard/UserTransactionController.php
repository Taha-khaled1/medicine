<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\UserTransaction;
use Illuminate\Http\Request;

class UserTransactionController extends Controller
{
    public function index()
    {
        $transaction = UserTransaction::with('user')->get();
        return view('dashboard.transaction.index', compact('transaction'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $size = UserTransaction::find($request->id);
        $size->delete();
        session()->flash('delete', 'تم الحذف بنجاح ');
        return redirect()->route('transaction.index')->with('success', 'size deleted successfully');
    }
}
