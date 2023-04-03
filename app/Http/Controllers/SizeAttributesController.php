<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Helpers\Helper;
use App\Http\Models\SizeAttributes;

class SizeAttributesController extends Controller
{

    public function index()
    {
        if(in_array('size_attribute_management_read', Helper::module_permission(Session::get('user')['role_id']))){
            $SizeAttributeList = [];
            $SizeAttributeList = SizeAttributes::orderBy('id', 'DESC')->get();
            return view('SizeAttributes.size_attributes_list',compact('SizeAttributeList'));
        }else{
            return redirect('/');
        }
    }

    public function create()
    {
        if(in_array('size_attribute_management_write', Helper::module_permission(Session::get('user')['role_id']))){
            return view('SizeAttributes.size_attributes_add');
        }else{
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
        if(in_array('size_attribute_management_write', Helper::module_permission(Session::get('user')['role_id']))){
            $result = SizeAttributes::create([
                'name_ch' => $request->chineseSizeAttributes,
                'name_en' => $request->englishSizeAttributes,
                'status' => $request->status ?? 2
            ]);
            if($result){
                return redirect('size-attributes')->with('success_msg', 'Size Attributes added successfully.');
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
        if(in_array('size_attribute_management_write', Helper::module_permission(Session::get('user')['role_id']))){
            $SizeAttribute = SizeAttributes::find($id);
            return view('SizeAttributes.size_attributes_edit',compact('SizeAttribute'));
        }else{
            return redirect('/');
        }
    }

    public function update(Request $request, $id)
    {
        if(in_array('size_attribute_management_write', Helper::module_permission(Session::get('user')['role_id']))){
            $result = SizeAttributes::find($id)->update([
                'name_ch' => $request->chineseSizeAttributes,
                'name_en' => $request->englishSizeAttributes,
                'status' => $request->status ?? 2
            ]);
            if($result){
                return redirect('size-attributes')->with('success_msg', 'Size Attributes updated successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return redirect('/');
        }
    }

    public function destroy($id)
    {
        $result = SizeAttributes::find($id)->delete();
        if($result){
            $message = 'Size Attributes deleted successfully..';
            $status = true;
        }else{
            $message = 'Please try again';
            $status = false;
        }
        return response()->json(['status' => $status,'message' => $message]);
    }
}
