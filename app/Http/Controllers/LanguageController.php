<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App;
use File;
use Storage;
use App\Helpers\Helper;

class LanguageController extends Controller
{
	public function switchLang($lang)
	{
		if (array_key_exists($lang, Config::get('languages'))) {
			Session::put('locale', $locale);
			//return redirect()->back();
		}
		return Redirect::back();
	}

	public function Language(Request $request){
		if($request->isMethod('get')) {
			if(in_array('settings_write', Helper::module_permission(Session::get('user')['role_id']))){
				return view('languageUpload');
			}else{
				return redirect('/');
			}
		}
		if($request->isMethod('post')) {
			if($request->file('enlanguage')){
				$fullImagePath = null;
				$public_path = 'resources/lang/en';
				if($request->hasfile('enlanguage')){
					$image = $request->file('enlanguage');
					$name =  'languages.php';
					$image->move(base_path($public_path),$name);
					$fullImagePath = $public_path.'/'.$name;
				}
			}else{
				$fullImagePath = null;
				$public_path = 'resources/lang/ch';
				if($request->hasfile('chlanguage')){
					$image = $request->file('chlanguage');
					$name = 'languages.php';
					$image->move(base_path($public_path),$name);
					$fullImagePath = $public_path.'/'.$name;
				}
			}
			return redirect('language')->with('success_msg', 'File Upload successfully.');
		}
	}
}