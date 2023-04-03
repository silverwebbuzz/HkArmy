<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Helpers\Helper;
use App\Http\Models\Categories;

class CategoriesController extends Controller
{
    public function index()
    {
        if(in_array('categories_management_read', Helper::module_permission(Session::get('user')['role_id']))){
            $Categories = [];
            $Categories = Categories::orderBy('id', 'DESC')->get();
            return view('Categories.categories_list',compact('Categories'));
        }else{
            return redirect('/');
        }
    }

    public function create()
    {
        return view('Categories.categories_add');
    }

    public function store(Request $request)
    {
        if(in_array('categories_management_create', Helper::module_permission(Session::get('user')['role_id']))){
            $result = Categories::create([
                'name_ch' => $request->chinesecategories_name,
                'name_en' => $request->englishcategories_name,
                'status' => $request->status ?? 2
            ]);
            if($result){
                return redirect('categories')->with('success_msg', 'Categories added successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return redirect('/');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if(in_array('categories_management_write', Helper::module_permission(Session::get('user')['role_id']))){
            $Categories = Categories::find($id);
            return view('Categories.categories_edit',compact('Categories'));
        }else{
            return redirect('/');
        }
        
    }

    public function update(Request $request, $id)
    {
        $result = Categories::find($id)->update([
            'name_ch' => $request->chinesecategories_name,
            'name_en' => $request->englishcategories_name,
            'status' => $request->status ?? 2
        ]);
        if($result){
            return redirect('categories')->with('success_msg', 'Categories updated successfully.');
        }else{
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

    public function destroy($id)
    {
        $result = Categories::find($id)->delete();
        if($result){
            $message = 'Categories deleted successfully..';
            $status = true;
        }else{
            $message = 'Please try again';
            $status = false;
        }
        return response()->json(['status' => $status,'message' => $message]);
    }
}
