<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Http\Models\ServiceHourPackage;

class ServiceHourPackageController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$hourpackages = ServiceHourPackage::all()->toArray();
		return view('servicehourpackage.list_hour_package',compact('hourpackages'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return view('servicehourpackage.add_hour_package');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$hourpackages = new ServiceHourPackage;
		$hourpackages->package_name = $request->packagename;
		$hourpackages->hours = $request->hours;
		$hourpackages->status = $request->status;
		$result = $hourpackages->save();
		if($result){
			return redirect('service-hour-package')->with('success_msg', 'Package add successfully.');
		}else{
			return back()->with('error_msg', 'Something went wrong.');
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
		$hourpackage = ServiceHourPackage::find($id)->toArray();
		return view('servicehourpackage.edit_hour_package',compact('hourpackage'));
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
		$hourpackages = ServiceHourPackage::find($id);
		$hourpackages->package_name = $request->packagename;
		$hourpackages->hours = $request->hours;
		$hourpackages->status = $request->status;
		$result = $hourpackages->save();
		if($result){
			return redirect('service-hour-package')->with('success_msg', 'Package updated successfully.');
		}else{
			return back()->with('error_msg', 'Something went wrong.');
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
		//
	}
}
