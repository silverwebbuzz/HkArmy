<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\EilteModel;
use Session;
use App\Helpers\Helper;

class EliteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
            $Eiltes = EilteModel::all()->toArray();
            return view('Eiltes.eilte_list',compact('Eiltes'));
        }else{
            return redirect('/');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(in_array('settings_create', Helper::module_permission(Session::get('user')['role_id']))){
            return view('Eiltes.eilte_add');
        }else{
            return redirect('/');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $Eiltes = new EilteModel;
        $Eiltes->elite_ch = !empty($request->chineseelite) ? $request->chineseelite : NULL;
        $Eiltes->elite_en = !empty($request->englishelite) ? $request->englishelite : NULL;
        $Eiltes->status = isset($request->status) ? $request->status : "2";
        $result = $Eiltes->save();  // save data
        if($result){
            return redirect('elite')->with('success_msg', 'Eilte added successfully.');
        }else{
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(in_array('settings_write', Helper::module_permission(Session::get('user')['role_id']))){
            $Eilte = EilteModel::find($id)->toArray();
            return view('Eiltes.eilte_edit',compact('Eilte'));
        }else{
            return redirect('/');
        }
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
        $Eiltes = EilteModel::find($id);
        $Eiltes->elite_ch = !empty($request->chineseelite) ? $request->chineseelite : NULL;
        $Eiltes->elite_en = !empty($request->englishelite) ? $request->englishelite : NULL;
        $Eiltes->status = isset($request->status) ? $request->status : "2";
        $result = $Eiltes->save();  // save data
        if($result){
            return redirect('elite')->with('success_msg', 'Eilte updated successfully.');
        }else{
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Eiltes = EilteModel::where('id',$id)->delete();
        if($Eiltes){
            $message = 'Eilte deleted successfully..';
            $status = true;
        }else{
            $message = 'Please try again';
            $status = false;
        }
        return response()->json(['status' => $status,'message' => $message]);
    }
}
