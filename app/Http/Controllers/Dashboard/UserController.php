<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\UserTransaction;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $userdata = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->orderBy('id', 'DESC')
            ->with('roles')
            ->get();
        foreach ($userdata as  $user) {
            $jobCounts = DB::table('jobs')
                ->join('offers', 'jobs.id', '=', 'offers.job_id')
                ->where('offers.user_id', $user->id)
                ->select(
                    DB::raw("COUNT(jobs.id) as total_jobs"),
                    DB::raw("COALESCE(SUM(CASE WHEN jobs.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_jobs"),
                )
                ->first();
            $user->jobCounts = $jobCounts;
            $completedJobs = Job::where('user_id', $user->id)->where('status', 'completed')->count();
            $user->completedJobs = $completedJobs;
        }
        // return  $userdata;
        $roles = Role::all();
        return view('dashboard.user.index', compact('userdata', 'roles'));
        // ->with('i', ($request->input('page', 1) - 1) * 5);
    }




    public function vendeors(Request $request)
    {
        $userdata = User::whereHas('roles', function ($query) {
            $query->where('name', 'vendor');
        })->orderBy('id', 'DESC')
            ->with('roles')
            ->get();
        foreach ($userdata as  $user) {
            $jobCounts = DB::table('jobs')
                ->join('offers', 'jobs.id', '=', 'offers.job_id')
                ->where('offers.user_id', $user->id)
                ->select(
                    DB::raw("COUNT(jobs.id) as total_jobs"),
                    DB::raw("COALESCE(SUM(CASE WHEN jobs.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_jobs"),
                )
                ->first();
            $user->jobCounts = $jobCounts;
            $completedJobs = Job::where('user_id', $user->id)->where('status', 'completed')->count();
            $user->completedJobs = $completedJobs;
        }
        $roles = Role::all();

        return view('dashboard.user.index', compact('userdata', 'roles'));
        // ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    public function userUpdateNote(Request $request)
    {
        // return $request;
        $user = User::find($request->id);
        $user->note =    $request->note;
        $user->save();
        session()->flash('Add', 'تم اضافة ملحوظه للمستخدم بنجاح');
        return back();
    }
    public function chargeWallet(Request $request)
    {
        // return $request;
        $user = User::find($request->pro_id);
        $user->wallet -= $request->wallet;
        $user->save();
        $userTrans = new UserTransaction();
        $userTrans->user_id = $user->id;
        $userTrans->total =  $request->wallet;
        $userTrans->save();
        session()->flash('Add', 'تم خصم الرصيد من المحفظه للمستخدم بنجاح');
        return back();
    }

    public function userUpdate($id)
    {
        $roles = Role::all();
        $user = User::with('roles')->find($id);

        return view('dashboard.user.update-user', compact('user', 'roles'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $isToggleOnString = (string) $request->isToggleOn;
        $status = '';
        $userId = $request->input('userId');
        if ($isToggleOnString == "true") {
            $status = 1;
        } else {
            $status = 0;
        }



        $user = User::find($userId);

        if ($user) {
            // Update the status field
            $user->status = $status;
            $user->save();

            return response()->json(['success' => true, 'message' => 'User status  updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'email:rfc,dns', 'indisposable'],
            'password' => 'required|string|min:8',
        ];
        $request->validate($rules);
        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->note = $request->note;
        $user->password = bcrypt($request->password);

        $user->save();
        $user->assignRole($request->input('roles'));
        session()->flash('Add', 'تم اضافة المستخدم بنجاح');
        return back()->with('success', 'User created successfully');;
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


    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }


    public function update(Request $request)
    {
        // return $request;
        $rules = [
            'pro_id' => 'required|exists:users,id',
            'name' => 'required|max:255',
            'note' => 'nullable|max:500',
            'phone' => 'nullable|numeric',
            'email' => 'required|email|max:255|unique:users,email,' . $request->pro_id,
            'password' => 'nullable|min:8|max:255',
            'roles' => 'required|array',
        ];

        $validatedData = $request->validate($rules);

        $id = $request->pro_id;
        $user = User::findOrFail($id);
        $user->name = $validatedData['name'];
        $user->phone = $validatedData['phone'];
        $user->email = $validatedData['email'];
        $user->note = $validatedData['note'];
        $user->email_verified_at = now();
        if ($validatedData['password']) {
            $user->password = bcrypt($validatedData['password']);
        }
        $user->save();

        // DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->roles()->sync($validatedData['roles']);

        session()->flash('Edit', 'تم تعديل المستخدم بنجاح');
        return back()->with('success', 'User updated successfully');
    }


    public function destroy(Request $request)
    {
        $property = User::findOrFail($request->pro_id);
        $property->delete();
        session()->flash('delete', 'تم حذف المستخدم بنجاح');
        return back();
    }
}
