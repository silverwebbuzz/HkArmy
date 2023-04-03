<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\EilteModel;
use App\Http\Models\SubElite;
use App\Http\Models\User;
use App\Http\Models\Subteam;
use App\Http\Models\EventType;
use App\Http\Models\Events;
use App\Http\Models\EventPosttypeModel;
use App\Http\Models\EventSchedule;
use App\Http\Models\RolePermission;
use App\Http\Models\QualificationModel;
use App\Http\Models\Remarks;
use App\Http\Models\RelatedActivityHistory;
use App\Http\Models\Specialty;
use App\Http\Models\AssignAwards;
use App\Http\Models\AwardsBadgesCategories;
use App\Http\Models\BadgeAssign;
use Carbon\Carbon;

class ImportController extends Controller
{
    function importMember(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_users');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_users';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata[$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);

                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database
                       $special_instructor = null;
                        foreach($importData_arr as $importData){
                            //Team
                            if(!empty($importData[0])){
                                $elite = EilteModel::where('elite_en', trim(ucfirst($importData[0])))->orwhere('elite_ch',trim(ucfirst($importData[0])))->first();
                            }
                            if(!empty($importData[3])){
                                $subteam = Subteam::where('subteam_en',$importData[3])->orwhere('subteam_ch',$importData[3])->first();
                            }
                            if(!empty($importData[7])){
                                $subelite = SubElite::where('elite_id',$elite->id)->where(function ($q) use($importData){
                                    $q->orwhere('subelite_en',$importData[7])->orwhere('subelite_ch',$importData[7]);
                                    })->first();
                            }
                            // $lastRecord = User::latest()->withTrashed()->first();      
                            $lastRecord = User::orderBy('ID','DESC')->withTrashed()->first();
                            $postData = [
                                'team'                          => !empty($elite->id) ? $elite->id : 0,
                                'elite_team'                    => !empty($subteam->id) ? $subteam->id : 0,
                                'team_effiective_date'          => (!empty($importData[4])) ? date('j F, Y',strtotime(trim($importData[4]))) : '',
                                'rank_effiective_date'          => (!empty($importData[5])) ? date('j F, Y',strtotime(trim($importData[5]))) : '',   
                                'Reference_number'              => (!empty($importData[6])) ? trim($importData[6]) : '',  
                                'Chinese_name'                  => (!empty($importData[8])) ? trim($importData[8]) : '',
                                'English_name'                  => (!empty($importData[9])) ? trim($importData[9]) : '',
                                'DOB'                           => (!empty($importData[10])) ? date('j F, Y',strtotime(trim($importData[10]))) : '',
                                'age'                           => Carbon::parse(trim($importData[10]))->diff(Carbon::now())->y,
                                'Gender'                        => (!empty($importData[11]) && ucfirst($importData[11]) == "Male") ? 1 : 2,
                                'email'                         => (!empty($importData[12])) ? $importData[12] : '',
                                'Contact_number'                => (!empty($importData[12])) ? $importData[13] : '',
                                'Contact_number_1'              => (!empty($importData[14])) ? $importData[14] : '',
                                'Contact_number_2'              => (!empty($importData[15])) ? $importData[15] : '',
                                'Chinese_address'               => (!empty($importData[16])) ? $importData[16] : '',
                                'english_address'               => (!empty($importData[17])) ? $importData[17] : '',
                                'Nationality'                   => (!empty($importData[18])) ? $importData[18] : '',
                                'Occupation'                    => (!empty($importData[19])) ? $importData[19] : '',
                                'ID_Number'                     => (!empty($importData[20])) ? $importData[20] : '',
                                'School_Name'                   => (!empty($importData[23])) ? $importData[23] : '',
                                'Subject'                       => (!empty($importData[24])) ? $importData[24] : '',
                                'Emergency_contact_name'        => (!empty($importData[27])) ? $importData[27] : '',
                                'EmergencyContact'              => (!empty($importData[28])) ? $importData[28] : '',
                                'JoinDate'                      => (!empty($importData[31])) ? date('j F, Y',strtotime($importData[31])) : '',
                                'hour_point'                    => (!empty($importData[35])) ? trim($importData[35]) : 0,
                                'member_token'                  => (!empty($importData[35])) ? trim($importData[35]) : 0,
                                'hour_point_rate'               => (!empty($importData[35])) ? (trim($importData[35]) * 10) : 0,
                                //'Role_ID'                       => (!empty($importData[36]) && ucfirst($importData[36]) == "User") ? 2 : 1,
                                'Role_ID'                       => 2,
                                'MemberCode'                    => '0'.($lastRecord->MemberCode + 1),
                                //'MemberCode'                    => (!empty($importData[38])) ? $importData[38] : NULL,
                                'Status'                        => (!empty($importData[37]) && ucfirst($importData[37]) == "Active") ? 1 : 0 
                            ];
                            if(!empty($subelite)){
                                $postData += [
                                        'rank_team'           => $subelite->id
                                ];
                            }
                            if($elite->id ==2){
                                $postData += [   
                                            'Specialty_Instructor'          => (ucfirst($importData[1])=="Yes") ? 1 : 0,
                                            'Specialty_Instructor_text'     => $importData[2]
                                        ] ;
                            }
                            if(!empty($importData[21])){
                                $qualification = QualificationModel::where('qualification_ch',$importData[21])->orWhere('qualification_en',$importData[21])->first();
                                if($qualification){
                                    if($qualification->id != 10){
                                        $postData += [   
                                            'Qualification'          => $qualification->id,
                                        ] ;
                                    }else{
                                        $postData += [   
                                            'Qualification'          => $qualification->id,
                                            'note'                   => $importData[22]
                                        ] ;
                                    }
                                    

                                }
                            }
                            if(!empty($importData[25]) && ucfirst($importData[25])=='No'){
                                $postData += [
                                    'Health_declaration'            => (!empty($importData[25])) ? $importData[25] : '',
                                ];
                            }else{
                                $postData += [
                                    'Health_declaration'            => (!empty($importData[25])) ? $importData[25] : '',
                                    'Health_declaration_text'       => (!empty($importData[26])) ? $importData[26] : '',
                                ];
                            }

                            //Relationship
                            if(!empty($importData[29]) && ucfirst($importData[29]=='Other')){
                                $postData += [
                                    'Relationship' => 6,
                                    'Relationship_text' => $importData[30] 
                                ];
                            }else if(!empty($importData[29]) && ucfirst($importData[29]=='Father/Son')){
                                $postData += [
                                    'Relationship' => 1,
                                ];
                            }else if(!empty($importData[29]) && ucfirst($importData[29]=='Mother/Son')){
                                $postData += [
                                    'Relationship' => 2,
                                ];
                            }else if(!empty($importData[29]) && ucfirst($importData[29]=='Father/Daugther')){
                                $postData += [
                                    'Relationship' => 3,
                                ];
                            }else if(!empty($importData[29]) && ucfirst($importData[29]=='Mother/Daugther')){
                                $postData += [
                                    'Relationship' => 4,
                                ];
                            }else if(!empty($importData[29]) && ucfirst($importData[29]=='Brother/sister')){
                                $postData += [
                                    'Relationship' => 5,
                                ];
                            }

                            $remark = Remarks::where('remarks_en',trim($importData[32]))->orwhere('remarks_ch',trim($importData[32]))->first();
                            if(!empty($remark) && $remark->id != 4){
                                $postData += [
                                    'Remarks'   => $remark->id,
                                ];
                            }else{
                                $postData += [
                                    'Remarks'   => $remark->id ?? null,
                                    'Remarks_desc' => $importData[33],
                                    'remark_date'   => $importData[34]
                                ];
                            }
                            $createRecord = User::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('users')->with('success_msg', __('Member added Successfully'));
                    }else{
                        return redirect('users')->with('error_msg', __('Something went wrong.'));
                    }
                }
            }
        }
    }

    function importEvent(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_events');
        }
        if($request->isMethod('post')){
            $flag = true;
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_events';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            if(!empty($importData[1])){
                                $eventtype = EventType::where('event_type_name_en',trim($importData[1]))->orwhere('event_type_name_ch',trim($importData[1]))->first();
                            }

                            //Event Code Create Dynamically.
                            if(!empty($eventtype)){
                                if ($eventtype->type_id == '0') {
                                    $eventFirstCode = ($eventtype->id == '1') ? 'T' : 'E';
                                    $Event_count = Events::where('event_type', $eventtype->id)->count();
                                    $event_number = (1 + $Event_count);
                                    $eventCode = $eventFirstCode . '' . date('y') . '' . sprintf("%02d", $event_number);
                                } else {
                                    $eventFirstCode = 'S';
                                    $Event_count = Events::where('event_type', $eventtype->type_id)->count();
                                    $event_number = (1 + $Event_count);
                                    $eventCode = $eventFirstCode . '' . date('y') . '' . sprintf("%02d", $event_number);
                                }
                            }

                            // Get Multiple Dates
                            if(!empty($importData[8])){
                                $dates = explode('|',trim($importData[8]));
                            }
                            // Get Times
                            if(!empty($importData[9])){
                                $time = explode('|',trim($importData[9]));
                            }
                            //Get Cost Method(Event Post Type)
                            if(!empty($importData[4])){
                                $eventposttype = explode('|',trim($importData[4]));
                            }
                            if(!empty($importData[5])){
                                $eventMoney = explode('|',trim($importData[5]));
                            }
                            if(!empty($importData[6])){
                                $eventToken = explode('|',trim($importData[6]));
                            }
                            if(!empty($importData[7])){
                                $eventMoney_Token = explode('|',trim($importData[7]));
                            }
                            $postData = [
                                'event_name'        => trim($importData[0]),
                                'event_type'        => !empty($eventtype) ? $eventtype->id : '',
                                'event_code'        => $eventCode,
                                'startdate'         => date('l,j F,Y',strtotime($dates[0])),
                                'enddate'           => date('l,j F,Y',strtotime($dates[0])),
                                'start_time'        => $time[0],
                                'end_time'           => $time[1],
                                'event_hours'       => intval($time[1]) - intval($time[0]),
                                'occurs'            => 'Once',
                                'status'            => 2
                            ];
                            if(!empty($importData[2]) && ucfirst($importData[2]) == "Yes"){
                                $postData += [
                                    'assessment'        => trim(ucfirst($importData[2])),
                                    'assessment_text'   => trim($importData[3])
                                ];
                            }
                             $createRecord = Events::create($postData);
                            if($createRecord){
                                // Insert Record in Event Post Type
                                foreach($eventposttype as $postType){
                                    if($postType == "Money"){
                                        foreach($eventMoney as $eventMoneyValue){
                                            $Insertposttype = [
                                                'event_id'      => !empty($eventtype) ? $eventtype->id : '',
                                                'event_code'    => $eventCode,
                                                'post_type'      => 1,
                                                'post_value'     => $eventMoneyValue            
                                            ];
                                            EventPosttypeModel::create($Insertposttype);
                                        }
                                    }elseif($postType == "Token"){
                                        foreach($eventToken as $eventTokenValue){
                                            $Insertposttype = [
                                                'event_id'       => !empty($eventtype) ? $eventtype->id : '',
                                                'event_code'     => $eventCode,
                                                'post_type'      => 2,
                                                'post_value'     => $eventTokenValue            
                                            ];
                                            EventPosttypeModel::create($Insertposttype);
                                        }                                       
                                    }else{
                                        foreach($eventMoney_Token as $eventMoneyTokenValue){
                                            $Insertposttype = [
                                                'event_id'       => !empty($eventtype) ? $eventtype->id : '',
                                                'event_code'     => $eventCode,
                                                'post_type'      => 3,
                                                'post_value'     => $eventMoneyTokenValue            
                                            ];
                                            EventPosttypeModel::create($Insertposttype);
                                        }
                                    }
                                }
                                //Insert Record in Event Schedule
                                foreach($dates as $dateValues){
                                   
                                    $arraySchedule = [
                                        'event_id'      => !empty($eventtype) ? $eventtype->id : '',
                                        'event_code'    => $eventCode,
                                        'occurs'        => 'Once',
                                        'status'        => 2,
                                        'start_time'    => $time[0],
                                        'end_time'      => $time[1],
                                        'event_hours'   => intval($time[1]) - intval($time[0]),
                                        'date'          => date('m/d/Y',strtotime($dateValues))
                                    ];
                                    $eventschedule = EventSchedule::create($arraySchedule);
                                }
                                
                            }else{
                                $flag = false;
                                break;
                            }
                        }
                    }
                    if($createRecord && $flag == true){
                        return redirect('eventManagement')->with('success_msg', __('Event added Successfully'));
                    }else{
                        return redirect('eventManagement')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }
    }

    function importRole(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_roles');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_roles';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                       $special_instructor = null;
                        foreach($importData_arr as $importData){
                            //Team
                            if(!empty($importData[0])){
                                $role_name = $importData[0];
                            }
                            if(!empty($importData[1])){
                                $description = $importData[1];
                            }
                            if(!empty($importData[2])){
                                $status = !empty(ucfirst(trim($importData[2])) == "Active") ? 1 : 0;
                            }
                           
                            $postData = [
                                'role_name'                     => $role_name,
                                'description'                   => $description,
                                'status'                        => $status,
                            ];
                            $createRecord = RolePermission::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('import-roles')->with('success_msg', __('Role added Successfully'));
                    }else{
                        return redirect('import-roles')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }
    }

    function importTeam(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_teams');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_team';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                       $special_instructor = null;
                        foreach($importData_arr as $importData){
                            //Team
                            if(!empty($importData[0])){
                                $chineseName = $importData[0];
                            }
                            if(!empty($importData[1])){
                                $englishName = $importData[1];
                            }
                            if(!empty($importData[2])){
                                $status = !empty(ucfirst(trim($importData[2])) == "Active") ? 1 : 0;
                            }
                           
                            $postData = [
                                'elite_ch'                     => $chineseName,
                                'elite_en'                     => $englishName,
                                'status'                       => $status
                            ];
                            $createRecord = EilteModel::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('team')->with('success_msg', __('Team added Successfully'));
                    }else{
                        return redirect('team')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }
    }

    function importSubTeam(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_sub_teams');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_sub_team';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            //Team
                            if(!empty($importData[0])){
                                $team = Subteam::where('elite_ch',trim($importData[0]))->orWhere('elite_en',trim($importData[0]))->first();
                            }
                            if(!empty($importData[1])){
                                $englishName = trim($importData[1]);
                            }
                            if(!empty($importData[2])){
                                $chineseName = trim($importData[2]);
                            }
                            if(!empty($importData[3])){
                                $status = !empty(ucfirst(trim($importData[3])) == "Active") ? 1 : 0;
                            }
                           
                            $postData = [
                                'elite_id'                       => $team->id,
                                'subteam_ch'                     => $chineseName,
                                'subteam_en'                     => $englishName,
                                'status'                         => $status
                            ];
                            $createRecord = Subteam::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('subteam')->with('success_msg', __('Sub Team added Successfully'));
                    }else{
                        return redirect('subteam')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }
    }

    function importRank(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_ranks');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_rank';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            //Team
                            if(!empty($importData[0])){
                                $team = EilteModel::where('elite_ch',trim($importData[0]))->orWhere('elite_en',trim($importData[0]))->first();
                            }
                            if(!empty($importData[1])){
                                $englishName = trim($importData[1]);
                            }
                            if(!empty($importData[2])){
                                $chineseName = trim($importData[2]);
                            }
                            if(!empty($importData[3])){
                                $status = !empty(ucfirst(trim($importData[3])) == "Active") ? 1 : 0;
                            }
                           
                            $postData = [
                                'elite_id'                        => $team->id,
                                'subelite_ch'                     => $chineseName,
                                'subelite_en'                     => $englishName,
                                'status'                          => $status
                            ];
                            $createRecord = SubElite::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('rank')->with('success_msg', __('Rank added Successfully'));
                    }else{
                        return redirect('rank')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }
    }

    public function importQualification(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_qualification');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_qualification';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            //Team
                            $postData = [
                                'qualification_ch'                        => !empty($importData[0]) ? trim($importData[0]) : '',
                                'qualification_en'                        => !empty($importData[1]) ? trim($importData[1]) : '',
                                'status'                                  => !empty($importData[2] && trim(ucfirst($importData[2])=='Active')) ? 1 : 0
                            ];
                            $createRecord = QualificationModel::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('qualification')->with('success_msg', __('Qualification added Successfully'));
                    }else{
                        return redirect('qualification')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }  
    }

    public function importActivityHistory(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_related_activity_history');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_activity_history';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            //Team
                            $postData = [
                                'ActivityHistory_en'                        => !empty($importData[0]) ? trim($importData[0]) : '',
                                'ActivityHistory_ch'                        => !empty($importData[1]) ? trim($importData[1]) : '',
                                'status'                                    => !empty($importData[2] && trim(ucfirst($importData[2])=='Active')) ? 1 : 0
                            ];
                            $createRecord = RelatedActivityHistory::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('related-activity-history')->with('success_msg', __('Activity History added Successfully'));
                    }else{
                        return redirect('related-activity-history')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }  
    }

    public function importSpeciality(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_speciality');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_speciality';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            $postData = [
                                'specialty_ch'                      => !empty($importData[0]) ? trim($importData[0]) : '',
                                'specialty_en'                      => !empty($importData[1]) ? trim($importData[1]) : '',
                                'status'                            => !empty($importData[2] && trim(ucfirst($importData[2])=='Active')) ? 1 : 0
                            ];
                            $createRecord = Specialty::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('specialty')->with('success_msg', __('Speciality added Successfully'));
                    }else{
                        return redirect('specialty')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        }  
    }

    public function importRemark(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_remark');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_remark';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            $postData = [
                                'remarks_ch'                      => !empty($importData[0]) ? trim($importData[0]) : '',
                                'remarks_en'                      => !empty($importData[1]) ? trim($importData[1]) : '',
                                'status'                          => !empty($importData[2] && trim(ucfirst($importData[2])=='Active')) ? 1 : 0
                            ];
                            $createRecord = Remarks::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('remarks')->with('success_msg', __('Remark added Successfully'));
                    }else{
                        return redirect('remarks')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        } 
    }

    public function importEventType(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_event_type');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_event_type';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database      
                        foreach($importData_arr as $importData){
                            $postData = [
                                'event_type_name_ch'                      => !empty($importData[1]) ? trim($importData[1]) : '',
                                'event_type_name_en'                      => !empty($importData[2]) ? trim($importData[2]) : '',
                                'status'                                  => !empty($importData[3] && trim(ucfirst($importData[3])=='Active')) ? 1 : 0
                            ];
                            if(ucfirst(trim($importData[0]))=="Training"){
                                $postData += [
                                    'type_id'           => 1
                                ];
                            }elseif(ucfirst(trim($importData[0]))=="Activity"){
                                $postData += [
                                    'type_id'           => 2
                                ];
                            }elseif(ucfirst(trim($importData[0]))=="Service"){
                                $postData += [
                                    'type_id'           => 3
                                ];
                            }else{
                                $postData += [
                                    'type_id'           => 0
                                ];
                            }
                            $createRecord = EventType::create($postData);
                        }
                    }
                    if($createRecord){
                        return redirect('event-type')->with('success_msg', __('Event Type added Successfully'));
                    }else{
                        return redirect('event-type')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        } 
    }

    public function importAwardBadges(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_award_members');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_award_members';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    $award = '';
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database  
                        $flag = 1;    
                        foreach($importData_arr as $importData){
                           if(!empty($importData[0]) && !empty($importData[1])){
                                $award = AwardsBadgesCategories::where('categories_type',trim(strtolower($importData[0])))->where('name_en',trim($importData[1]))->orwhere('name_ch',trim($importData[1]))->first();
                           }
                           if(!empty($importData[4])){
                               $users = explode('|',$importData[4]);
                               foreach($users as $user){
                                   $memberCode = substr($user,1);
                                   $user = User::where('MemberCode',$memberCode)->first();
                                   $postData = [
                                    'user_id'        => !empty($user) ? $user->ID : 0,
                                    'award_id'       => !empty($award) ? $award->id : 0,
                                    'reference_number' => !empty($importData[2]) ? trim($importData[2]) : 0,
                                    'issue_date'       => !empty($importData[3]) ? date('Y-m-d',strtotime(trim($importData[3]))) : NULL,
                                    'assigned_date'     => !empty($importData[5]) ? date('Y-m-d',strtotime(trim($importData[5]))) : NULL,
                                   ];
                                   $createRecord = AssignAwards::create($postData);
                                   if(empty($createRecord)){
                                       $flag = 0;
                                       break;
                                   }
                               }
                           }  
                        }
                    }
                    if($flag == 1){
                        return redirect('award-assigned-member-list')->with('success_msg', __('Award Members added Successfully'));
                    }else{
                        return redirect('award-assigned-member-list')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        } 
    }

    public function importBadgesMember(Request $request){
        if($request->isMethod('get')){
            return view('import_files.import_badge_members');
        }
        if($request->isMethod('post')){
            $file = $request->file('user_file');
            
            // File Details 
            $filename = $file->getClientOriginalName();
            $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
            $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
            $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;
                        
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads/import_badge_members';
                    
                    // Upload file
                    $file->move(public_path($location), $filename);

                    // Import CSV to Database
                    $filepath = public_path($location."/".$filename);
                    // Reading file
                    $file = fopen($filepath,"r");
                   
                    $importData_arr = array();
                    $i = 0;
                    
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i != 0){
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }   
                        }
                        $i++;
                    }
                    fclose($file);
                    $badges = '';
                    if(isset($importData_arr) && !empty($importData_arr)){
                        // Insert to MySQL database  
                        $flag = 1;    
                        foreach($importData_arr as $importData){
                           if(!empty($importData[0]) && !empty($importData[1])){
                                $badges = AwardsBadgesCategories::where('categories_type',trim(strtolower($importData[0])))->where('name_en',trim($importData[1]))->orwhere('name_ch',trim($importData[1]))->first();
                           }
                           if(!empty($importData[4])){
                               $users = explode('|',$importData[4]);
                               foreach($users as $user){
                                   $memberCode = substr($user,1);
                                   $user = User::where('MemberCode',$memberCode)->first();
                                   $postData = [
                                    'user_id'        => !empty($user) ? $user->ID : 0,
                                    'badge_id'       => !empty($badges) ? $badges->id : 0,
                                    'reference_number' => !empty($importData[2]) ? trim($importData[2]) : 0,
                                    'issue_date'       => !empty($importData[3]) ? date('Y-m-d',strtotime(trim($importData[3]))) : NULL,
                                    'assigned_date'     => !empty($importData[5]) ? date('Y-m-d',strtotime(trim($importData[5]))) : NULL,
                                   ];
                                   $createRecord = BadgeAssign::create($postData);
                                   if(empty($createRecord)){
                                       $flag = 0;
                                       break;
                                   }
                               }
                           }  
                        }
                    }
                    if($flag == 1){
                        return redirect('badge-assigned-member-list')->with('success_msg', __('Badges Members added Successfully'));
                    }else{
                        return redirect('badge-assigned-member-list')->with('error_msg', __('Something went wrong.'));
                    }
                    
                }
            }
        } 
    }
}
