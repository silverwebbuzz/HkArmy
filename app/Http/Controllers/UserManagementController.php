<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\RolePermission;
use App\Http\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use Mail;

class UserManagementController extends Controller
{
    public function index(Request $request){
        $Query ='';
        $items  = $request->items ?? 10;
        $Query = User::with('Role');
        if(isset($request->submit)){
            if(isset($request->filter_gender)){
                $Query->where('Gender',$request->filter_gender);
            }
            if(isset($request->user_status)){
                $Query->where('Status',$request->user_status);
            }
            if(isset($request->search_text)){
                $Query->where(function($query) use($request){
                    $query->where('UserName','Like','%'.$request->search_text.'%')
                        ->orWhere('English_name','Like','%'.$request->search_text.'%')
                        ->orWhere('Chinese_name','Like','%'.$request->search_text.'%')
                        ->orWhere('email','Like','%'.$request->search_text.'%');
                });
            }
        }
        $userData = $Query->where('Role_ID','<>',1)->sortable(['ID'=>'Desc'])->paginate($items);
        return view('UserManagement.user_list',compact('userData','items'));
    }

    public function create(){
        $roleData = RolePermission::where('role_name','<>','admin')->get();
        return view('UserManagement.add_user',compact('roleData'));
    }

    public function store(Request $request){
        $rules = array(
            'role_type' => 'required',
            'userName' => 'required',
			'gender' => 'required',
			'email' => 'required|unique:users|email',
			'contact_no' => ['required','digits:8','numeric'],
            'password'  => ['required','min:6'],
            'confirm_password' => ['required','same:password']
		);
		if(empty($request->englishName) && empty($request->chineseName)){
			$rules += array(
				'englishName' => 'required',
				'chineseName' => 'required'
			);	
		}
		
		$messages = array(
            'role_type.required' => 'Please select Role',
            'userName.required' => 'Please Enter User Name',
			'gender.required' => 'Please select gender.',
			'email.required' => 'Please enter email address.',
			'email.email' => "Please enter valid email address.",
            'email.unique' => "Email is already exists",
			'contact_no.required' => 'Please enter contact number.',
            'contact_no.digits'    => 'Please enter 8 digits of contact number',
            'contact_no.numeric'    => 'Please enter numeric values',
            'password.required'     => 'Please enter password',
            'confirm_password.required'   => 'Please enter confirm password',
            'confirm_password.same'     => 'Confirm password can not match',
            'password.min'          => 'Please enter minimum 6 character',
		);
		if(empty($request->englishName) && empty($request->chineseName)){
			$messages += array(
				'englishName.required' => 'Please enter english name.',
                'chineseName.required' => 'Please enter chinese name.'
			);
		}
		if ($this->validate($request, $rules, $messages) === FALSE) {
			return redirect()->back()->withInput();
		}
        
        $postData = [
            'Role_ID'           => $request->role_type,
            'UserName'          => $request->userName,
            'English_name'      => $request->englishName,
            'Chinese_name'      => $request->chineseName,
            'email'             => $request->email,
            'Gender'            => $request->gender,
            'Contact_number'    => $request->contact_no,
            'password'          => Hash::make($request->password)
        ];
        $createUser = User::create($postData);
        if($createUser){
            $lastinsertid = $createUser->ID;
            $ID = $lastinsertid;
            $Email = $request->email;
            $UserName = $request->userName;
            $Password =  Hash::make($request->password);
            $user_id = base64_encode($ID);
            $email_add = base64_encode($Email);
            $userdata = $user_id ."/". $email_add."/".trim($UserName);
            $public_path = public_path().'/image';
            $qrCode = new QrCode($userdata);
            $qrCode->setSize(200);
            $qrimag = trim($request->userName)."-".time().".png";
            $qrcodeimag = $qrCode->writeFile($public_path.'/'.$qrimag);
            $dataUri = $qrCode->writeDataUri();
            
            $dataarr = array(
                'name' => $request->userName,
                'email' =>$request->email,
                'password' => $request->password,
                'emailType' => 1,
                'subject' => "Credential",
                'qrcode' => $dataUri,
            );
            Mail::send('email.sendLoginCredential',$dataarr, function ($message) use ($dataarr,$qrimag,$public_path,$Email) {
                $attchmentImage = $public_path.'/'.$qrimag;
                $message->to($Email)
                ->subject('Credential')
                ->attach($attchmentImage, [
                    'as' => 'qrcode.png',
                    'mime' => 'image/png'
                ]);
            });
            $qrImage = User::find($lastinsertid);
            $qrImage->QrCode = $qrimag;
            $qrImage->save();
            return redirect('user-management')->with('success_msg', 'User add successfully.');
        }else{
            return back()->with('error_msg', 'Something went wrong.');
        }
    }

    public function show($id){
        //
    }

    public function edit($id){
       $userData = User::find($id);
       $roleData = RolePermission::where('role_name','<>','admin')->get();
       return view('UserManagement.edit_user',compact('userData','roleData'));
    }

    public function update(Request $request, $id){
        $rules = array(
            'role_type' => 'required',
            'userName' => 'required',
			'gender' => 'required',
			'email' => ['required','email',Rule::unique('users')->ignore($id)],
			'contact_no' => ['required','digits:8','numeric'],
		);
		if(empty($request->englishName) && empty($request->chineseName)){
			$rules += array(
				'englishName' => 'required',
				'chineseName' => 'required'
			);	
		}
		
		$messages = array(
            'role_type.required' => 'Please select Role',
            'userName.required' => 'Please Enter User Name',
			'gender.required' => 'Please select gender.',
			'email.required' => 'Please enter email address.',
			'email.email' => "Please enter valid email address.",
            'email.unique' => "Email is already exists",
			'contact_no.required' => 'Please enter contact number.',
            'contact_no.digits'    => 'Please enter 8 digits of contact number',
            'contact_no.numeric'    => 'Please enter numeric values',
		);
		if(empty($request->englishName) && empty($request->chineseName)){
			$messages += array(
				'englishName.required' => 'Please enter english name.',
                'chineseName.required' => 'Please enter chinese name.'
			);
		}
		if ($this->validate($request, $rules, $messages) === FALSE) {
			return redirect()->back()->withInput();
		}
        $postData = [
            'Role_ID'           => $request->role_type,
            'UserName'          => $request->userName,
            'English_name'      => $request->englishName,
            'Chinese_name'      => $request->chineseName,
            'email'             => $request->email,
            'Gender'            => $request->gender,
            'Contact_number'    => $request->contact_no
        ];
        $updateUser = User::find( $id)->update($postData);
        if($updateUser){
            return redirect('user-management')->with('success_msg', 'User updated successfully.');
        }else{
            return back()->with('error_msg', 'Something went wrong.');
        }
    }

    public function destroy($id){
        // if (in_array('members_delete', Helper::module_permission(Session::get('user')['role_id']))) {
			$user = User::where('ID', $id)->delete();
			if ($user) {
				$message = 'User deleted successfully..';
				$status = true;
			} else {
				$message = 'Please try again';
				$status = false;
			}
			return response()->json(['status' => $status, 'message' => $message]);
		// } else {
			// $message = 'You do not have permission.';
			// $status = false;
			// return response()->json(['status' => $status, 'message' => $message]);
		// }
    }

    public function changePassword(Request $request){
        $params = array();
        parse_str($request->formData, $params);
        if($params['newPassword'] != $params['confirmPassword']){
            return $this->sendError(__('Problem was occur please try again'), 422);
        }
        $userData = User::where('ID',$params['userId'])->first();
        if(!empty($userData)){
            if(User::find($params['userId'])->update(['password' => Hash::make($params['newPassword']) ])){
                $dataSet = [
                    'name' => ($userData->English_name) ? $userData->English_name : $userData->UserName,
                    'email'     => $userData->email,
                    'password'  => $params['newPassword'],
                    'emailType' => 2,//1 for change password
                ];
                $email = $dataSet['email'];
                Mail::send('email.sendLoginCredential',$dataSet, function ($message) use ($dataSet, $email) {
                    $message->to($email)
                    ->subject('Credential');
                });
                return response()->json(['status' => 'success','message' => 'Password change successfully',200]);
            }else{
                return response()->json(['status' => 'error','message' => 'Problem was occur please try again',  422]);
            }
        }
    }
}
