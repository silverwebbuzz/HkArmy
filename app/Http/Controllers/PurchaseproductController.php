<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\ProductModel;
use App\Http\Models\CartModel;
use App\Http\Models\OrderModel;
use App\Http\Models\OrderItems;
use App\Http\Models\User;
use Session;
use App\Helpers\Helper;

class PurchaseproductController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(Session::get('user')['role_id'] == '1'){
			$purchaseProducts = OrderModel::with('getUsers')->with('orderItems')->orderBy('id','desc')->get()->toArray();
		}else{
			$purchaseProducts = OrderModel::where('user_id',Session::get('user')['user_id'])->with('getUsers')->with('orderItems')->orderBy('id','desc')->get()->toArray();
		}
		//$users = User::where('Role_ID','!=',1)->get()->toArray();
		$users = User::all()->toArray();
		$products = ProductModel::where('status','1')->get()->toArray();
		return view('Purchaseproduct.purchase-product-list',compact('purchaseProducts','users','products'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$products = ProductModel::where('status','1')->get()->toArray();
		$users = User::where('Role_ID','!=',1)->get()->toArray();
		return view('Purchaseproduct.purchase-product-add',compact('products','users'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$rules = array(
			'product' => 'required',
			'member' => 'required',
			'product_uniform_type' => 'required',
			'product_size' => 'required',
			'product_amount' => 'required|numeric',
		);
		$messages = array(
			'product.required' => 'Please select product.',
			'member.required' => 'Please select member',
			'product_uniform_type.required' => "Please select uniform type.",
			'product_size.required' => "Please select size.",
			'product_amount.required' => "Please enter amount.",
			'product_amount.numeric' => "Please enter valid amount.",
		);
		 if($this->validate($request, $rules, $messages) === FALSE){
			return redirect()->back()->withInput();
		}
		$products = ProductModel::where('id',$request->product)->first();
		$order = new OrderModel();
		$order->user_id = !empty($request->member) ? $request->member : '';
		$order->totalqty = '1';
		$order->product_total_amount = !empty($request->product_amount) ? $request->product_amount : '';
		$order->blilling_address = '';
		$order->shipping_address = '';
		$order->order_notes = '';
		$order->order_date = date('Y-m-d');
		$result = $order->save();
		if(!empty($result)){
			$order_last_insert_id = $order->id;
			$orderItems = new OrderItems();
			$orderItems->order_id = $order_last_insert_id;
			$orderItems->product_id = !empty($request->product) ? $request->product : '';
			$orderItems->user_id = !empty($request->member) ? $request->member : '';
			$orderItems->product_name = $products->product_name;
			$orderItems->product_amount = !empty($request->product_amount) ? $request->product_amount : '';
			$orderItems->product_qty = '1';
			$orderItems->product_sku = $products->product_sku;
			$orderItems->product_size = !empty($request->product_size) ? $request->product_size : '';
			$orderItems->product_uniform_type = !empty($request->product_uniform_type) ? $request->product_uniform_type : '';
			$orderItems->transaction_type = !empty($request->transaction_type) ? $request->transaction_type : '';
			$orderItems->save();
		}
		if($result){
			return redirect('purchase-product')->with('success_msg', 'Purchase Product added successfully.');
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
		$products = ProductModel::where('status','1')->get()->toArray();
		$users = User::where('Role_ID','!=',1)->get()->toArray();
		$purchaseproduct = OrderItems::find($id)->toArray();
		return view('Purchaseproduct.purchase-product-edit',compact('products','users','purchaseproduct'));
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
		$rules = array(
			'product' => 'required',
			'member' => 'required',
			'product_uniform_type' => 'required',
			'product_size' => 'required',
			'product_amount' => 'required|numeric',
		);
		$messages = array(
			'product.required' => 'Please select product.',
			'member.required' => 'Please select member',
			'product_uniform_type.required' => "Please select uniform type.",
			'product_size.required' => "Please select size.",
			'product_amount.required' => "Please enter amount.",
			'product_amount.numeric' => "Please enter valid amount.",
		);
		 if($this->validate($request, $rules, $messages) === FALSE){
			return redirect()->back()->withInput();
		}
		$products = ProductModel::where('id',$request->product)->first();
		$orderItems = OrderItems::find($id);
		$orderItems->product_id = !empty($request->product) ? $request->product : '';
		$orderItems->user_id = !empty($request->member) ? $request->member : '';
		$orderItems->product_name = $products->product_name;
		$orderItems->product_amount = !empty($request->product_amount) ? $request->product_amount : '';
		$orderItems->product_qty = '1';
		$orderItems->product_sku = $products->product_sku;
		$orderItems->product_size = !empty($request->product_size) ? $request->product_size : '';
		$orderItems->product_uniform_type = !empty($request->product_uniform_type) ? $request->product_uniform_type : '';
		$orderItems->transaction_type = !empty($request->transaction_type) ? $request->transaction_type : '';
		$result = $orderItems->save();
		if($result){
			return redirect('purchase-product')->with('success_msg', 'Purchase Product updated successfully.');
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
		$PurchaseProduct = OrderItems::where('id',$id)->delete();
		if($PurchaseProduct){
			$message = 'Purchase Product deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}

	public function SearchPurchaseProduct(Request $request){
		
		$member = !empty($request->member) ? $request->member : '';
		$productname = !empty($request->productname) ? $request->productname : '';
		$product_sku = !empty($request->product_sku)  ? $request->product_sku : '';
		$query = OrderItems::where('user_id','!=','1');
		if(isset($request->member) && !empty($member)){
			$query->where('user_id',$member);
		}
		if(isset($request->productname) && !empty($productname)){
			$query->where('product_name',$productname);
		}
		if(isset($request->product_sku) && !empty($product_sku)){
			$query->where('product_sku',$product_sku);
		}
		$purchaseProducts = $query->with('orderdata')->with('getUsers')->get()->toArray();
		$html = '';
		$html .= '<table id="seacrch-purchase-productTable" class="table">
					<thead>
						<tr>
							<th>'. __('languages.member.Member_Name') .'</th>
							<th>'. __('languages.member.Member_Number').'</th>
							<th>'. __('languages.Product.Product_name').'</th>
							<th>'. __('languages.Product.Product_sku').'</th>
							<th>'. __('languages.Product.Product_qty').'</th>
							<th>'. __('languages.Product.Amount').'</th>
							<th>'. __('languages.Product.Date').'</th>
							<th>'. __('languages.Action').'</th>
						</tr>
					</thead>
					<tbody>';
		if(!empty($purchaseProducts)){
			foreach($purchaseProducts as $val){
				if(!empty($val['orderdata'])){
					foreach($val['orderdata'] as $row){
						$html .= '<tr>';
						if(!empty($val['get_users']['UserName'])){
							$html .='<td>'.$val['get_users']['UserName'].'</td>';
						}else{
							$html .='<td>'.$val['get_users']['Chinese_name'] .' & '.$val['get_users']['English_name'] .'</td>';
						}
							$html .='<td>'.$val['get_users']['MemberCode'] .'</td>
							<td>'.$val['product_name'] .'</td>
							<td>'.$val['product_sku'] .'</td>
							<td>'.$val['product_qty'] .'</td>
							<td>'.$val['product_amount'] .'</td>
							<td>'.date('d/m/Y',strtotime($row['order_date'])) .'</td>
							<td>
								<a href="'.route('purchase-product.edit',$row['id']) .'"><i class="bx bx-edit-alt"></i></a>
								<a href="javascript:void(0);" data-id="'.$row['id'] .'" class="deletepurchaseproduct"><i class="bx bx-trash-alt"></i> </a>
							</td>
						</tr>';
					}
				}
			}
		}
		$html .='</tbody></table>';
		return response()->json(array('status' => 1,'list'=>$html));
	}
}