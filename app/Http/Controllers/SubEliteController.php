<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\EilteModel;
use App\Http\Models\SubElite;
use Session;
use App\Helpers\Helper;

class SubEliteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
            $SubElite = SubElite::with('elite')->orderBy('id','desc')->get()->toArray();
            return view('SubElite.subeilte_list',compact('SubElite'));
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
            $Eiltes = EilteModel::where('status','1')->get()->toArray();
            return view('SubElite.subeilte_add',compact('Eiltes'));
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
        $SubElite = new SubElite;
        $SubElite->elite_id = !empty($request->elite) ? $request->elite : NULL;
        $SubElite->subelite_ch = !empty($request->chinesesubelite) ? $request->chinesesubelite : NULL;
        $SubElite->subelite_en = !empty($request->englishsubelite) ? $request->englishsubelite : NULL;
        $SubElite->status = isset($request->status) ? $request->status : "2";
        $result = $SubElite->save();  // save data
        if($result){
            return redirect('subelite')->with('success_msg', 'SubEilte added successfully.');
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
            $Eiltes = EilteModel::where('status','1')->get()->toArray();
            $SubElite = SubElite::find($id)->toArray();
            return view('SubElite.subeilte_edit',compact('Eiltes','SubElite'));
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
        $SubElite = SubElite::find($id);
        $SubElite->elite_id = !empty($request->elite) ? $request->elite : NULL;
        $SubElite->subelite_ch = !empty($request->chinesesubelite) ? $request->chinesesubelite : NULL;
        $SubElite->subelite_en = !empty($request->englishsubelite) ? $request->englishsubelite : NULL;
        $SubElite->status = isset($request->status) ? $request->status : "2";
        $result = $SubElite->save();  // save data
        if($result){
            return redirect('subelite')->with('success_msg', 'SubEilte updated successfully.');
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
        $SubElite = SubElite::where('id',$id)->delete();
        if($SubElite){
            $message = 'SubElite deleted successfully..';
            $status = true;
        }else{
            $message = 'Please try again';
            $status = false;
        }
        return response()->json(['status' => $status,'message' => $message]);
    }
}
