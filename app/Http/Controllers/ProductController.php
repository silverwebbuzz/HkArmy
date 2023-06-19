<?php

namespace App\Http\Controllers;

use App\Http\Models\CartModel;
use App\Http\Models\OrderItems;
use App\Http\Models\OrderModel;
use App\Http\Models\ProductAssignModel;
use App\Http\Models\ProductCosttypeModel;
use App\Http\Models\ProductModel;
use App\Http\Models\User;
use App\Http\Models\ChildProduct;
use DB;
use Illuminate\Http\Request;
use Session;
use App\Http\Models\Categories;
use App\Http\Models\AssignProductOrder;
use App\Http\Models\SizeAttributes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Helpers\Helper;


class ProductController extends Controller {

	/**
	 * USE : Get Combo product suffix option list
	 */
	public function getProductSuffixList(Request $request){
		$Data = [];
		$status = true;
		$products = ProductModel::with('childProducts')->whereIn('id',$request->ProductIds)->get();
		$Data['html'] = (string)View::make('Product.product_suffix_option_list',compact('products'));
		return response()->json(['status' => $status, 'data' => $Data]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$Products = ProductModel::with('productCostType')->orderBy('id', 'desc')->get()->toArray();
		return view('Product.product_list', compact('Products'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$Categories = [];
		$SizeAttributes = [];
		$ProductList = [];
		$Categories = Categories::all();
		$SizeAttributes = SizeAttributes::all();
		$lastProduct = ProductModel::orderBy('id','desc')->withTrashed()->first();
		if(isset($lastProduct) && !empty($lastProduct)){
			$GenerateProductSku = '000'.($lastProduct->id+1);
		}else{
			$GenerateProductSku = '0001';
		}		
		$Products = ProductModel::where('parent_id', 0)->orderBy('id', 'desc')->get()->toArray();
		$ProductList = ProductModel::where('product_type',1)->orderBy('id', 'desc')->get()->toArray();
		return view('Product.product_add', compact('Products','ProductList','Categories','SizeAttributes','GenerateProductSku'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$total_amount = null;
		if(isset($request->sub_amount) && $request->sub_amount != 'NaN'){
			$total_amount = $request->sub_amount + $request->product_amount;
			$total_amount = number_format((float) $total_amount, 2, '.', '');
		}
		
		$public_path = 'assets/productimage';
		$fullImagePath = array();
		if ($request->hasfile('product_image')) {
			$images = $request->file('product_image');
			foreach ($images as $val) {
				$name = time() . $val->getClientOriginalName();
				$val->move(public_path($public_path), $name);
				$fullImagePath[] = $public_path . '/' . $name;
			}
		}

		$rules = array(
			'product_name' => 'required',
			'product_sku' => 'required',
			'uniformType' => 'required',
			//'size' => 'required',
			//'product_amount' => 'required|numeric',
			'status' => 'required',
		);
		$messages = array(
			'product_name.required' => 'Please enter product name.',
			'product_sku.required' => 'Please Enter product sku',
			'uniformType.required' => 'Please select uniform type',
			//'size.required' => "Please select size.",
			//'product_amount.required' => "Please enter amount",
			//'product_amount.numeric' => "Please enter valid amount",
			'status.required' => 'Please select status',
		);
		if ($this->validate($request, $rules, $messages) === FALSE) {
			return redirect()->back()->withInput();
		}

		$product = new ProductModel;
		$product->product_type = !empty($request->product_type) ? $request->product_type : 1; // Default 1 is single product
		if(isset($request->product_type) && $request->product_type == 2){
			$product->combo_product_ids = implode(',',$request->combo_product_ids);
		}
		$product->product_name = !empty($request->product_name) ? $request->product_name : NULL;
		$product->product_sku = !empty($request->product_sku) ? $request->product_sku : NULL;
		$product->uniformType = !empty($request->uniformType) ? $request->uniformType : NULL;
		$product->size = !empty($request->size) ? $request->size : NULL;
		//$product->product_amount = !empty($request->product_amount) ? $request->product_amount : NULL;
		$product->product_image = implode(',', $fullImagePath);
		if ($request->add_more_product == '1') {
			$product->parent_id = !empty($request->items) ? implode(",", $request->items) : NULL;
		}
		$product->sub_amount = isset($request->sub_amount) ? $request->sub_amount : NULL;
		$product->total_amount = isset($total_amount) ? $total_amount : NULL;
		$product->status = isset($request->status) ? $request->status : "2";
		$product->date = date('Y-m-d');
		$result = $product->save(); // save data
		if($result){			
			if(isset($request->product_suffix) && isset($request->product_suffix_name) && !empty($request->product_suffix) && !empty($request->product_suffix_name)){
				foreach($request->product_suffix as $productSuffixKey => $productSuffix){
					ChildProduct::create([
						'main_product_id' => $product->id,
						'product_suffix' => $productSuffix,
						'product_suffix_name' => $request->product_suffix_name[$productSuffixKey],
						'status' => $request->status
					]);
				}
			}
			
			// check product cost is exists or not
			$checkEventCost = DB::table('product_cost_type')->where('product_id', $product->id)->first();
			if(empty($checkEventCost)){
				if (!empty($request->event_money)) {
					foreach ($request->event_money as $key => $money) {
						if($money!=""){
							$productCostTypeModel = new ProductCosttypeModel;
							$productCostTypeModel->product_id = $product->id;
							$productCostTypeModel->cost_type = 1;
							$productCostTypeModel->cost_value = ($money!="") ? $money : null;
							$productCostTypeModel->save();
						}
					}
				}

				// Create default cost method "Money = 0"
				if(ProductCosttypeModel::where(['product_id' => $product->id, 'cost_type' => 1, 'cost_value' => 0])->doesntExist()){
					$productCostTypeModel = new ProductCosttypeModel;
					$productCostTypeModel->product_id = $product->id;
					$productCostTypeModel->cost_type = 1;
					$productCostTypeModel->cost_value = 0;
					$productCostTypeModel->save();	
				}
				
				if (!empty($request->event_token)) {
					foreach ($request->event_token as $key => $token) {
						if(!empty($token)){
							$productCostTypeModel = new ProductCosttypeModel;
							$productCostTypeModel->product_id = $product->id;
							$productCostTypeModel->cost_type = 2;
							$productCostTypeModel->cost_value = ($token) ? $token : null;
							$productCostTypeModel->save();
						}
					}
				}
				if (!empty($request->event_money_token)) {
					foreach ($request->event_money_token as $key => $moneytoken) {
						if ($moneytoken['money'] != "" && $moneytoken['token'] != "") {
							$productCostTypeModel = new ProductCosttypeModel;
							$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
							$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
							$productCostTypeModel->product_id = $product->id;
							$productCostTypeModel->cost_type = 3;
							$productCostTypeModel->cost_value = $money . "+" . $token;
							$productCostTypeModel->save();
						}
					}
				}
			}else{
				$delete = DB::table('product_cost_type')->where('product_id', $product->id)->delete();
				if($delete){
					if (!empty($request->event_money)) {
						foreach ($request->event_money as $key => $money) {
							if(!empty($money)){
								$productCostTypeModel = new ProductCosttypeModel;
								$productCostTypeModel->product_id = $product->id;
								$productCostTypeModel->cost_type = 1;
								$productCostTypeModel->cost_value = ($money) ? $money : null;
								$productCostTypeModel->save();
							}
						}
					}
					// Create default cost method "Money = 0"
					if(ProductCosttypeModel::where(['product_id' => $product->id, 'cost_type' => 1, 'cost_value' => 0])->doesntExist()){
						$productCostTypeModel = new ProductCosttypeModel;
						$productCostTypeModel->product_id = $product->id;
						$productCostTypeModel->cost_type = 1;
						$productCostTypeModel->cost_value = 0;
						$productCostTypeModel->save();
					}

					if (!empty($request->event_token)) {
						foreach ($request->event_token as $key => $token) {
							if(!empty($token)){
								$productCostTypeModel = new ProductCosttypeModel;
								$productCostTypeModel->product_id = $product->id;
								$productCostTypeModel->cost_type = 2;
								$productCostTypeModel->cost_value = ($token) ? $token : null;
								$productCostTypeModel->save();
							}
						}
					}
					if (!empty($request->event_money_token)) {
						foreach ($request->event_money_token as $key => $moneytoken) {
							if($moneytoken['money'] != "" && $moneytoken['token'] != ""){
								$productCostTypeModel = new ProductCosttypeModel;
								$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
								$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
								$productCostTypeModel->product_id = $product->id;
								$productCostTypeModel->cost_type = 3;
								$productCostTypeModel->cost_value = $money . "+" . $token;
								$productCostTypeModel->save();
							}
						}
					}
				}
			}
		}
		if ($result) {
			return redirect('product')->with('success_msg', 'Product added successfully.');
		} else {
			return back()->with('error_msg', 'Problem was error accured.. Please try again..');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$Categories = [];
		$SizeAttributes = [];
		$ChildProducts = [];
		$ComboProducts = [];
		$ChildProductSizeAttrbuteId = [];
		$Categories = Categories::all();
		$SizeAttributes = SizeAttributes::all();
		$product = ProductModel::find($id)->toArray();
		if(isset($product) && $product['product_type']==2){
			$ComboProducts = ProductModel::with('childProducts')->whereIn('id',explode(',',$product['combo_product_ids']))->get();
		}
		$ChildProducts = ChildProduct::where('main_product_id',$id)->get();
		$costType = ProductCosttypeModel::where('product_id', $id)->get();
		if ($product['parent_id'] != '0' && $product['parent_id'] != '' && $product['parent_id'] != NULL) {
			$productArray = explode(",", $product['parent_id']);
			if (!empty($productArray)) {
				foreach ($productArray as $key => $value) {
					$product['item'][$key] = ProductModel::find($value)->toArray();
				}
			}
		}
		$Products = ProductModel::where('parent_id', 0)->where('id', '!=', $id)->orderBy('id', 'desc')->get()->toArray();
		$ProductList = ProductModel::where('product_type',1)->orderBy('id', 'desc')->get()->toArray();
		return view('Product.product_edit', compact('product', 'Products','ProductList','ComboProducts','ChildProducts', 'costType','Categories','SizeAttributes'));
	}

	public function show(){

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$total_amount = null;
		if(isset($request->sub_amount) && $request->sub_amount != 'NaN'){
			$total_amount = $request->sub_amount + $request->product_amount;
			$total_amount = number_format((float) $total_amount, 2, '.', '');
		}

		$product = ProductModel::find($id);
		if (!empty($product)) {
			$product_images = json_decode($product->product_image);
		}
		$public_path = 'assets/productimage';
		if ($request->hasfile('product_image')) {
			$images = $request->file('product_image');
			foreach ($images as $val) {
				$name = time() . $val->getClientOriginalName();
				$val->move(public_path($public_path), $name);
				$fullImagePath[] = $public_path . '/' . $name;
			}
			if (!empty($product_images)) {
				$fullImagePath = array_merge($fullImagePath, $product_images);
			} else {
				$fullImagePath;
			}
		}
		$rules = array(
			'product_name' => 'required',
			'product_sku' => 'required',
			'uniformType' => 'required',
			//'size' => 'required',
			//'product_amount' => 'required|numeric',
			'status' => 'required',
		);
		$messages = array(
			'product_name.required' => 'Please enter product name.',
			'product_sku.required' => 'Please Enter product sku',
			'uniformType.required' => 'Please select uniform type',
			//'size.required' => "Please select size.",
			//'product_amount.required' => "Please enter amount",
			//'product_amount.numeric' => "Please enter valid amount",
			'status.required' => 'Please select status',
		);
		if ($this->validate($request, $rules, $messages) === FALSE) {
			return redirect()->back()->withInput();
		}
		$product = ProductModel::find($id);
		$product->product_type = !empty($request->product_type) ? $request->product_type : 1; // Default 1 is single product
		if(isset($request->product_type) && $request->product_type == 2){
			$product->combo_product_ids = implode(',',$request->combo_product_ids);
		}else{
			$product->combo_product_ids = null;
		}
		$product->product_name = !empty($request->product_name) ? $request->product_name : NULL;
		//$product->product_sku = !empty($request->product_sku) ? $request->product_sku : NULL;
		$product->uniformType = !empty($request->uniformType) ? $request->uniformType : NULL;
		$product->size = !empty($request->size) ? $request->size : NULL;
		//$product->product_amount = !empty($request->product_amount) ? $request->product_amount : NULL;

		if ($request->add_more_product == '1') {
			$product->parent_id = !empty($request->items) ? implode(",", $request->items) : NULL;
		} else {
			$product->parent_id = 0;
		}
		$product->sub_amount = isset($request->sub_amount) ? $request->sub_amount : NULL;
		$product->total_amount = isset($total_amount) ? $total_amount : NULL;

		$product->status = isset($request->status) ? $request->status : "2";
		if (isset($request->product_image) && !empty($fullImagePath)) {
			$product->product_image = implode(',', $fullImagePath);
		}
		$product->date = date('Y-m-d');
		$result = $product->save(); // save data
		if($result){
			// Update product suffix child product
			if(isset($request->product_suffix) && isset($request->product_suffix_name) && !empty($request->product_suffix) && !empty($request->product_suffix_name)){
				if(DB::table('child_products')->where('main_product_id', $id)->count()){
					$deleteChildProducts = DB::table('child_products')->where('main_product_id', $id)->delete();	
				}
				foreach($request->product_suffix as $productSuffixKey => $productSuffix){
					ChildProduct::create([
						'main_product_id' => $id,
						'product_suffix' => $productSuffix,
						'product_suffix_name' => $request->product_suffix_name[$productSuffixKey],
						'status' => $request->status
					]);
				}
			}

			//Update or create Event Money
			// if($request->event_money[0] == "" && $request->event_token[0] == "" && $request->event_money_token[0]['money']=="" && $request->event_money_token[0]['token'] ==""){
			// 	// create default product cost type
			// 	if(ProductCosttypeModel::where('product_id',$product->id)->where('cost_type',1)->where('cost_value',0)->exists()){
			// 		$ProductCosttypeModel = ProductCosttypeModel::where('product_id',$product->id)->where('cost_type',1)->where('cost_value',0)->first();
			// 		ProductCosttypeModel::find($ProductCosttypeModel->id)->update([
			// 			'product_id' => $product->id,
			// 			'cost_type' => 1,
			// 			'cost_value' => 0
			// 		]);
			// 	}else{
			// 		$productCostTypeModel = new ProductCosttypeModel;
			// 		$productCostTypeModel->product_id = $product->id;
			// 		$productCostTypeModel->cost_type = 1;
			// 		$productCostTypeModel->cost_value = 0;
			// 		$productCostTypeModel->save();
			// 	}
			// }else{
				if (!empty($request->event_money)) {
					foreach ($request->event_money as $productCostId => $money) {
						if(is_array($money)){
							if($money[0]!=""){
								ProductCosttypeModel::find($productCostId)->update([
									'product_id' => $product->id,
									'cost_type' => 1,
									'cost_value' => ($money[0]!="") ? $money[0] : null
								]);
							}
						}else{
							if($money){
								ProductCosttypeModel::create([
									'product_id' => $product->id,
									'cost_type' => 1,
									'cost_value' => ($money) ? $money : null
								]);
							}
						}
					}
				}
				// Update or create Event Token
				if (!empty($request->event_token)) {
					foreach ($request->event_token as $productCostId => $token) {
						if(is_array($token)){
							if($token[0]){
								ProductCosttypeModel::find($productCostId)->update([
									'product_id' => $product->id,
									'cost_type' => 2,
									'cost_value' => ($token[0]) ? $token[0] : null
								]);
							}
						}else{
							if($token){
								ProductCosttypeModel::create([
									'product_id' => $product->id,
									'cost_type' => 2,
									'cost_value' => ($token) ? $token : null
								]);
							}
						}
					}
				}
				// Update or create Event Money + Token
				if (!empty($request->event_money_token)) {
					foreach ($request->event_money_token as $productCostId => $moneytoken) {
						if(array_key_exists("money",$moneytoken) && array_key_exists("token",$moneytoken)){
							if ($moneytoken['money'] && $moneytoken['token']) {
								ProductCosttypeModel::create([
									'product_id' => $product->id,
									'cost_type' => 3,
									'cost_value' => $moneytoken['money']."+".$moneytoken['token']
								]);
							}
						}else{
							if($moneytoken[array_keys($moneytoken)[0]]['money'] && $moneytoken[array_keys($moneytoken)[0]]['token']){
								ProductCosttypeModel::find(array_keys($moneytoken)[0])->update([
									'product_id' => $product->id,
									'cost_type' => 3,
									'cost_value' => $moneytoken[array_keys($moneytoken)[0]]['money'] . "+" . $moneytoken[array_keys($moneytoken)[0]]['token']
								]);
							}
						}
					}
				}

				// Create default cost method "Money = 0"
				if(ProductCosttypeModel::where(['product_id' => $product->id, 'cost_type' => 1, 'cost_value' => 0])->doesntExist()){
					$productCostTypeModel = new ProductCosttypeModel;
					$productCostTypeModel->product_id = $product->id;
					$productCostTypeModel->cost_type = 1;
					$productCostTypeModel->cost_value = 0;
					$productCostTypeModel->save();	
				}
			//}
		}

		if ($result) {
			return redirect('product')->with('success_msg', 'Product updated successfully.');
		} else {
			return back()->with('error_msg', 'Problem was error accured.. Please try again..');
		}
	}

	/**
	 * USE : Delete child product
	 */
	public function deleteProductSuffix(Request $request){
		$delete = ChildProduct::where('main_product_id',$request->product_id)->where('id',$request->childProductSkuId)->delete();
		if ($delete) {
			$message = 'Product Suffix deleted successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	/**
	 * USE : Delete Post type value from event
	 */
	public function deleteCostType(Request $request, $postType){
		if($postType){
			$delete = ProductCosttypeModel::where('product_id',$request->product_id)->where('id',$request->post_id)->delete();
			if ($delete) {
				$message = 'Cost Type deleted successfully..';
				$status = true;
			} else {
				$message = 'Please try again';
				$status = false;
			}
			return response()->json(['status' => $status, 'message' => $message]);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$Product = ProductModel::where('id', $id)->delete();
		if ($Product) {
			$message = 'Product deleted successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	public function removeImage(Request $request) {
		$image = !empty($request->dataimage) ? $request->dataimage : '';
		$id = !empty($request->id) ? $request->id : '';
		$product = ProductModel::find($id);
		if (!empty($product)) {
			$product_image = explode(",", $product->product_image);
			if (in_array($image, $product_image)) {
				$diffProductimage = array_diff($product_image, array($image));
				$product->product_image = implode(",", $diffProductimage);
				$result = $product->save();
				if (!empty($result)) {
					$message = 'Product image deleted successfully.';
					$status = true;
				} else {
					$message = 'Something went wrong.';
					$status = false;
				}
			} else {
				$message = 'Something went wrong';
				$status = false;
				$product->product_image = implode(",", $product_image);
			}
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	public function productList() {
		$products = ProductModel::all()->toArray();
		return view('Product.all_product_list', compact('products'));
	}

	public function cartProduct() {
		$cartproduct = CartModel::where('user_id', Session::get('user')['user_id'])->with('getProduct')->orderBy('id', 'desc')->get()->toArray();
		return view('Product.product_cart', compact('cartproduct'));
	}

	public function addToCart(Request $request) {
		$product_id = !empty($request->productid) ? $request->productid : '';
		$user_id = !empty($request->user_id) ? $request->user_id : '';
		$amount = !empty($request->amount) ? $request->amount : '';
		$cartData = CartModel::where('user_id', $user_id)->where('product_id', $product_id)->get()->toArray();
		if (!empty($cartData)) {
			foreach ($cartData as $val) {
				if ($product_id == $val['product_id']) {
					$updateCart = CartModel::find($val['id']);
					$updateCart->totalAmount = $amount + $val['totalAmount'];
					$updateCart->qty = $val['qty'] + 1;
					$result = $updateCart->save();
					if (!empty($result)) {
						$redirect = '/cart';
						$message = 'Product updated in cart successfully.';
						$status = true;
					} else {
						$redirect = '/product-list';
						$message = 'Something went wrong';
						$status = false;
					}
				}
			}
		} else {
			$cart = new CartModel;
			$cart->user_id = $user_id;
			$cart->product_id = $product_id;
			$cart->totalAmount = $amount;
			$cart->qty = '1';
			$result = $cart->save();
			if (!empty($result)) {
				$redirect = '/cart';
				$message = 'Product added in cart successfully.';
				$status = true;
			} else {
				$redirect = '/product-list';
				$message = 'Something went wrong';
				$status = false;
			}
		}
		return response()->json(['status' => $status, 'message' => $message, 'redirect' => $redirect]);
	}

	public function removeCartProduct(Request $request) {
		$cartproductid = !empty($request->cartproductid) ? $request->cartproductid : '';
		$Cart = CartModel::where('id', $cartproductid)->delete();
		if ($Cart) {
			$message = 'Cart product deleted successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	public function checkoutProduct() {
		$Cart = CartModel::where('id', Session::get('user')['user_id'])->get();
		return view('Product.product_checkout');
	}

	public function checkoutCartUpdate(Request $request) {
		$finalarr = !empty($request->finalarr) ? $request->finalarr : '';
		foreach ($finalarr as $val) {
			$cartUpdate = CartModel::find($val['productID']);
			$cartUpdate->qty = $val['qty'];
			$cartUpdate->totalAmount = $val['amount'];
			$cartUpdate->save();
		}
		$redirect = '/checkout';
		return response()->json(['redirect' => $redirect]);
	}

	public function addOrder(Request $request) {
		$cartmodel = CartModel::where('user_id', Session::get('user')['user_id'])->with('getProduct')->get()->toArray();
		if (!empty($cartmodel)) {
			$totalqty = 0;
			$totalamount = 0;
			foreach ($cartmodel as $val) {
				$totalqty += $val['qty'];
				$totalamount += $val['totalAmount'];
			}
			$billing_adddress = array(
				'firstname' => !empty($request->firstname) ? $request->firstname : '',
				'lastname' => !empty($request->lastname) ? $request->lastname : '',
				'email' => !empty($request->email) ? $request->email : '',
				'phname' => !empty($request->phname) ? $request->phname : '',
				'compnay_name' => !empty($request->compnay_name) ? $request->compnay_name : '',
				'street_address' => !empty($request->street_address) ? $request->street_address : '',
				'aname' => !empty($request->aname) ? $request->aname : '',
				'city' => !empty($request->city) ? $request->city : '',
				'country' => !empty($request->country) ? $request->country : '',
				'postcode' => !empty($request->postcode) ? $request->postcode : '',
			);
			$shipping_address = array(
				'ship_first_name' => !empty($request->ship_first_name) ? $request->ship_first_name : '',
				'ship_last_name' => !empty($request->ship_last_name) ? $request->ship_last_name : '',
				'ship_email' => !empty($request->ship_email) ? $request->ship_email : '',
				'ship_phone_number' => !empty($request->ship_phone_number) ? $request->ship_phone_number : '',
				'ship_company_name' => !empty($request->ship_company_name) ? $request->ship_company_name : '',
				'ship_street_address' => !empty($request->ship_street_address) ? $request->ship_street_address : '',
				'ship_aprt_name' => !empty($request->ship_aprt_name) ? $request->ship_aprt_name : '',
				'ship_city' => !empty($request->ship_city) ? $request->ship_city : '',
				'ship_country' => !empty($request->ship_country) ? $request->ship_country : '',
				'ship_postcode' => !empty($request->ship_postcode) ? $request->ship_postcode : '',
			);
			$order = new OrderModel();
			$order->user_id = Session::get('user')['user_id'];
			$order->totalqty = $totalqty;
			$order->product_total_amount = $totalamount;
			$order->blilling_address = json_encode($billing_adddress);
			$order->shipping_address = json_encode($shipping_address);
			$order->order_notes = !empty($request->ship_order_note) ? $request->ship_order_note : '';
			$order->order_date = date('Y-m-d');
			$result = $order->save();
			if (!empty($result)) {
				$order_last_insert_id = $order->id;
				foreach ($cartmodel as $row) {
					$orderItems = new OrderItems();
					$orderItems->order_id = $order_last_insert_id;
					$orderItems->product_id = $row['get_product']['id'];
					$orderItems->user_id = Session::get('user')['user_id'];
					$orderItems->product_name = $row['get_product']['product_name'];
					$orderItems->product_amount = $row['totalAmount'];
					$orderItems->product_qty = $row['qty'];
					$orderItems->product_sku = $row['get_product']['product_sku'];

					$orderItems->save();
					$Cart = CartModel::where('id', $row['id'])->delete();
				}
				$message = 'Order Successfully place.';
				$status = true;
			} else {
				$message = 'Please try again';
				$status = false;
			}
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	public function addMoreProduct($id) {
		$product = ProductModel::find($id)->toArray();
		$loaded = view('Product.product_list_select', compact('product'))->render();
		echo json_encode(array('status' => 1, 'loaded' => $loaded, 'product' => $product));
	}

	public function get_all_product() {
		$html = '';
		$Products = ProductModel::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
		$html .= '<div class="product_main_cls">';
			// Product dropdown
			$html .= '<div class="product_select_cls">'.
						'<fieldset class="form-group">'.
							'<select class="form-control product_id" id="product_id" name="product_id">'.
								'<option value="">' . __('languages.Product.Select_product') . '</option>';
								if ($Products) {
									foreach ($Products as $product) {
										$html .= '<option value="' . $product['id'] . '">' . $product['product_name'] . '</option>';
									}
								}
		$html .= '</select></fieldset></div>';

		// Select child Product Dropdowns
		$html .='<div class="child_product_select_cls">'.
					'<fieldset class="form-group">' .
					'<select class="form-control" name="child_product_select" id="child_product_select">' .
						'<option>'. __("languages.member.select_product_code").'</option>'.
					'</select>' .
					'</fieldset>' .
				'</div>';

		$html .= '<div class="event-id-cls product_cost_cls">'.
					'<fieldset class="form-group">'.
						'<select class="form-control" id="product_cost_type" name="product_cost_type">'.
							'<option value="">' . __('languages.event.Select_post_type') . '</option>'.
						'</select>'.
					'</fieldset>';
		$html .= '<div class="form-row events-id-cls1"><input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_product"></div>';
		$html .= '</div>';
		return response()->json(['status' => 1, 'html' => $html]);
	}

	/**
	 * USE : Assigned product enrollment order to members
	 */
	public function AssignedProductEnrollmentMembers(Request $request){
		$assignExisting = false;
		if(isset($request->EnrollmentProductId) && !empty($request->EnrollmentProductId)){
			$ProductAssignModel = ProductAssignModel::where('assign_product_order_id',$request->EnrollmentProductId)->first();
			if(isset($ProductAssignModel) && !empty($ProductAssignModel)){
				$users = $request->user_id;
				if(!empty($users)){
					foreach($users as $key => $user_id){
						if(ProductAssignModel::where('assign_product_order_id', $ProductAssignModel->assign_product_order_id)->where('product_id',$ProductAssignModel->product_id)->where('user_id',$user_id)->doesntExist()){
							ProductAssignModel::Create([
								'assign_product_order_id' => $ProductAssignModel->assign_product_order_id,
								'product_id' => $ProductAssignModel->product_id,
								'child_product_id' => $ProductAssignModel->child_product_id,
								'user_id' => !empty($user_id) ? $user_id : NULL,
								'remark' => $ProductAssignModel->remark,
								'cost_type_id' => $ProductAssignModel->cost_type_id,
								'money' => $ProductAssignModel->money
							]);
						}
						$assignExisting = true;
					}
				}
			}
		}
		if($assignExisting){
			return response()->json(['status' => true, 'message' => "Assign Product successfully."]);
		}else{
			return response()->json(['status' => false, 'message' => "Assign Product allready exists."]);
		}
	}

	/**
	 * USE : Generate order id
	 */
	public function GenerateOrderId(){
		$LastOrderId = AssignProductOrder::max('order_id');
		if(isset($LastOrderId) && !empty($LastOrderId)){
			$OrderId = ((int) $LastOrderId + 1);
		}else{
			$OrderId = 10001;
		}
		return $OrderId;
	}

	/**
	 * USE : User can assign product to members
	 */
	public function productAssignUser(Request $request) {
		$users = $request->user_id;		
		$assignExisting = false;
		if (!empty($users)) {
			$currentDate = date('Y-m-d');
			$postData = [
				'order_id' => $this->GenerateOrderId(),
				'product_id' => $request->productid,
				'product_cost_type_id' => $request->costTypeId,
				'child_product_id' => implode(',',$request->childProductId),
				'order_date' => $currentDate,
				'created_by_user_id' => Session::get('user')['user_id'],
				'status' => 1
			];
			$assignBy =  Session::get('user')['username'];
			$product = ProductModel::find($request->productid)->product_name;
			$AssignProductOrder = AssignProductOrder::create($postData);
			$AssignProductOrderId = $AssignProductOrder->id;
			
			foreach ($users as $key => $user_id) {
				if($request->costTypeId){
					$costTypeData = ProductCosttypeModel::where('product_id', $request->productid)->where('id', $request->costTypeId)->first();
					$userMoney = User::where('ID', $user_id)->first();
					if ($costTypeData->cost_type == 1) {
						// $Products = ProductAssignModel::where('user_id', $user_id)->where('product_id', $request->productid)->first();
						// if (empty($Products)) {
							$userId = $user_id; 
							$assign = new ProductAssignModel;
							$assign->assign_product_order_id = $AssignProductOrder->id;
							$assign->product_id = !empty($request->productid) ? $request->productid : NULL;
							$assign->child_product_id = !empty($request->childProductId) ? implode(',',$request->childProductId) : NULL;
							$assign->user_id = !empty($user_id) ? $user_id : NULL;
							$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
							$assign->cost_type_id = $costTypeData->id ?? null;
							$assign->money = !empty($costTypeData->cost_value) ? $costTypeData->cost_value : NULL;
							$assign->save();
							$id = $assign->id;
					
							// if($assign->save()){
							// 	$remaningMoney = $userMoney->total_money - $costType->cost_value;
							// 	User::where('ID' , '=' , $user_id)->update(['total_money' => $remaningMoney]);
							// }

							$assignExisting = true;
						//}
					} else if ($costTypeData->cost_type == 2) {
						if ($userMoney->member_token != "" && $userMoney->member_token != 0 && $costTypeData->cost_value <= $userMoney->member_token) {
							// $Products = ProductAssignModel::where('user_id', $user_id)->where('product_id', $request->productid)->first();
							// if (empty($Products)) {
								$userId = $user_id;
								$assign = new ProductAssignModel;
								$assign->assign_product_order_id = $AssignProductOrder->id;
								$assign->product_id = !empty($request->productid) ? $request->productid : NULL;
								$assign->child_product_id = !empty($request->childProductId) ? implode(',',$request->childProductId) : NULL;
								$assign->user_id = !empty($user_id) ? $user_id : NULL;
								$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
								$assign->cost_type_id = $costTypeData->id ?? null;
								$assign->token = !empty($costTypeData->cost_value) ? $costTypeData->cost_value : NULL;
								$assign->save();
								$id = $assign->id;
								// if ($assign->save()) {
								// 	$remaningToken = $userMoney->member_token - $costTypeData->cost_value;
								// 	User::where('ID', '=', $user_id)->update(['member_token' => $remaningToken]);
								// }
								$assignExisting = true;
							//}
						} else {
							return response()->json(['status' => false, 'message' => "No enough token."]);
						}

					} else if ($costTypeData->cost_type == 3) {
						$moneyTokenvalue = explode("+", $costTypeData->cost_value);
						if ($userMoney->member_token != "" && $userMoney->member_token != 0 && $moneyTokenvalue[1] <= $userMoney->member_token) {
							// $Products = ProductAssignModel::where('user_id', $user_id)->where('product_id', $request->productid)->first();
							// if (empty($Products)) {
								$userId = $user_id;
								$assign = new ProductAssignModel;
								$assign->assign_product_order_id = $AssignProductOrder->id;
								$assign->product_id = !empty($request->productid) ? $request->productid : NULL;
								$assign->child_product_id = !empty($request->childProductId) ? implode(',',$request->childProductId) : NULL;
								$assign->user_id = !empty($user_id) ? $user_id : NULL;
								$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
								$assign->cost_type_id = $costTypeData->id ?? null;
								$assign->money = !empty($moneyTokenvalue[0]) ? $moneyTokenvalue[0] : NULL;
								$assign->token = !empty($moneyTokenvalue[1]) ? $moneyTokenvalue[1] : NULL;
								$assign->save();
								$id = $assign->id;

								// if ($assign->save()) {
								// 	$remaningMoney = $userMoney->total_money - $moneyTokenvalue[0];
								// 	$remaningToken = $userMoney->member_token - $moneyTokenvalue[1];
								// 	//User::where('ID' , '=' , $user_id)->update(['total_money' => $remaningMoney,'member_token' => $remaningToken]);
								// 	User::where('ID', '=', $user_id)->update(['member_token' => $remaningToken]);
								// }
								$assignExisting = true;
							//}
						} else {
							return response()->json(['status' => false, 'message' => "No enough token."]);
						}
					}
				} else {
					// $Products = ProductAssignModel::where('user_id', $user_id)->where('product_id', $request->productid)->first();
					// if (empty($Products)) {
						$userId = $user_id;
						$assign = new ProductAssignModel;
						$assign->assign_product_order_id = $AssignProductOrder->id;
						$assign->product_id = !empty($request->productid) ? $request->productid : NULL;
						$assign->child_product_id = !empty($request->childProductId) ? implode(',',$request->childProductId) : NULL;
						$assign->user_id = !empty($user_id) ? $user_id : NULL;
						$assign->save();
						$id = $assign->id();
						$assignExisting = true;
					//}
				}
			}
		}
		
		$assignTo = User::find($user_id)->English_name;
		$getData =  ['product' => $product, 'assign_product_order_id' => $AssignProductOrderId, 'assign_to_user' => $assignTo, 'assign_by_user' => $assignBy];
		Helper::InsertAuditLogfuncation($getData, $id, 'ProductAssign', 'Product');
		
		if ($assignExisting) {
			return response()->json(['status' => true, 'message' => "Assign Product successfully."]);
		} else {
			return response()->json(['status' => false, 'message' => "Assign Product allready exists."]);
		}
	}

	public function productHistory() {
		$result = ProductAssignModel::with('childProducts')->with('users')->with('product')->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
		return view('Product.transaction_history', compact('result'));
	}

	public function addRemark(Request $request) {
		$ProductAssign = ProductAssignModel::find($request->id);
		$ProductAssign->remark = !empty($request->remarks) ? $request->remarks : NULL;
		$result = $ProductAssign->save(); // save data
		if ($result) {
			return response()->json(['status' => 'true', 'message' => 'Remark Added Successfully']);
		} else {
			return response()->json(['status' => 'false']);
		}
	}

	public function productAssignStatusUpdate(Request $request, $id) {
		$productStatus = ProductAssignModel::find($id);
		$productStatus->status = $request->status;
		$result = $productStatus->save();
		if ($result) {
			$message = 'Status Updated successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	/**
	 * USE : Get Product Enrollment order list
	 */
	public function getProductEnrollmentList(){
		$ProductEnrollmentList = array();
		$ProductEnrollmentList = AssignProductOrder::orderBy('id','desc')->get()->toArray();
		$html = '';
        $html .= '<div class="product_enrollment_order_main_cls">';
            $html .= '<div class="product_enrollment_order_select_cls d-flex">';
                    $html .= '<div class="product-enrollment-selection">';
					$html .= '<select class="form-control enrollment_product_id mx-1" id="enrollment_product_id" name="enrollment_product_id">';
                    if(isset($ProductEnrollmentList) && !empty($ProductEnrollmentList)){
						$html .= '<option value="">'.__('languages.select_product_enrollment_order').'</option>';
                        foreach($ProductEnrollmentList as $enrollmentOrder){
							$html .= '<option value="'.$enrollmentOrder['id'].'">'.$enrollmentOrder['order_id'].'</option>';
						}
                    }else{
                        $html .= '<option value="">'.__('languages.no_available_enrollment_order').'</option>';
                    }
                    $html .= '</select>';
                    $html .= '<span class="product_enrollment_order_select_error error"></span></div>';
                $html .= '<div class="events-id-cls1">';
                	$html .='<input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_enrollment_product_order">';
                $html .='</div>';
			$html .='</div>';
			$html .='</div>';
        return response()->json(['status' => 1, 'html' => $html]);
	}
}