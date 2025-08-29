<?php

namespace App\Http\Controllers;

use App\Models\Allowance;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Session;
use App\Models\Configuration;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users=User::orderBy('name')->get();
        return view('user.users',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_user()
    {
        $roles=Role::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $employees=Employee::where('activeness','like','active')->doesntHave('user')->orderBy('name','asc')->get();

        return view('user.user',compact('roles','employees'));
    }

    public function save_user(Request $request)
    {
        $user=User::where('login_name','like',$request->get('user_login') )->first();
        if($user!='')
        {
            return  redirect()->back()->with('error','This user login name already exist!');
        }

        $activeness=$request->get('activeness');
        if($activeness=='')
            $activeness='inactive';

         $roles=$request->get('roles');
         //print_r(json_encode($roles));die;

        $user=New User;
        $user->name=$request->get('name');
        $user->password=Hash::make($request->password);
        $user->login_name=$request->get('user_login');
        $user->employee_id=$request->get('employee_id');
        $user->activeness=$activeness;
        $user->save();

        $user->roles()->attach($roles);

        return redirect()->back()->with('success','User Added!');
    }

    public function edit_user(User $user)
    {
        $roles=Role::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $employees=Employee::where('activeness','like','active')->doesntHave('user')->orWhere('id',$user['employee_id'])->orderBy('name','asc')->get();

        return view('user.edit_user',compact('user','roles','employees'));
    }

    public function update_user(Request $request)
    {
        $user=User::where('id','<>',$request->id)->where('login_name','like',$request->get('user_login') )->first();
        if($user!='')
        {
            return  redirect()->back()->with('error','This user login name already exist!');
        }

        $activeness=$request->get('activeness');
        if($activeness=='')
            $activeness='inactive';

         $roles=$request->get('roles');
         //print_r(json_encode($roles));die;

        $user=User::find($request->id);
        $user->name=$request->get('name');

        if($request->password!='')
        $user->password=Hash::make($request->password);
    
        $user->login_name=$request->get('user_login');
        $user->employee_id=$request->get('employee_id');
        $user->activeness=$activeness;
        $user->save();


           $user->roles()->sync($roles);

        return redirect()->back()->with('success','User Updated!');
    }


    public function dashboard()
    {

        $name=Configuration::company_full_name();
        $short_name=Configuration::company_short_name();
        $abbreviation=Configuration::company_abbreviation();
        $factory_address=Configuration::company_factory_address();
        
        $pending_orders=Order::pending_count();
      

      return view('dashboard',compact('pending_orders','short_name','abbreviation'));
        
    }


    public function login()
    {

       

        if (Auth::check()) {
             return redirect()->intended('dashboard');
          }
        
        
 $name=Configuration::company_full_name();
        $short_name=Configuration::company_short_name();
        $abbreviation=Configuration::company_abbreviation();
        $factory_address=Configuration::company_factory_address();

      return view('login',compact('short_name'));
        
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('login_name', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }
        else
        {
            return redirect()->back()->with('error','Invalid Login!');
        }
    }

    public function logout(Request $request)
    {

        //print_r(json_encode($request));die;
        
          //Session::flush();
        $request->session()->invalidate();

         $request->session()->regenerateToken();
           Auth::logout();
      return redirect()->intended('login');
        
    }

    public function create_role()
    {
        $roles=Role::orderBy('sort_order','asc')->get();
        return view('user.role',compact('roles'));
    }

    public function save_role(Request $request)
    {
        $activeness=$request->get('activeness');
        if($activeness=='')
            $activeness='inactive';

        $role=New Role;
        $role->name=$request->get('name');
        $role->sort_order=$request->get('sort_order');
        $role->activeness=$activeness;
        $role->save();

        return redirect()->back()->with('success','Role Added!');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

     
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
