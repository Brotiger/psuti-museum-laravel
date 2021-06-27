<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Education;
use App\Models\Degree;
use App\Models\Title;
use App\Models\Reward;
use App\Models\Attainment;
use App\Models\Photo;
use App\Models\Video;
use App\Models\Unit;
use App\Models\Event;
use App\Models\User;
use App\Models\FileSize;
use App\Models\FileExt;
use App\Models\UnitEmployee;
use App\Models\PersonalFile;
use App\Models\AutobiographyFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use PhotoService;

class EmployeeController extends Controller
{
    public function search_employee(Request $request){
        $filter = [];

        if($request->input("lastName") != null){
            $filter[] = ["lastName", "like", '%' . $request->input("lastName") . '%'];
        }

        if($request->input("firstName") != null){
            $filter[] = ["firstName", "like", '%' . $request->input("firstName") . '%'];
        }

        if($request->input("secondName") != null){
            $filter[] = ["secondName", "like", '%' . $request->input("secondName") . '%'];
        }

        $employees_search = Employee::where($filter)->orderBy('lastName')->limit(15)->get();

        return view('ajax.searchEmployee', [
            'employees_search' => $employees_search
        ])->render();
    }

    public function index(){
        $site = env('DB_SITE', 'pguty');
        $employees_search = Employee::orderBy('lastName')->limit(15)->get();
        $units_search = Unit::orderBy('fullUnitName')->limit(15)->get();
        $events_search = Event::orderBy('name')->limit(15)->get();

        $file_size = env('FILE_SIZE', 0);
        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $file_ext = env('FILE_EXT', null);
        if($file_ext != null) $file_ext = explode(',', $file_ext);

        $units = Unit::orderBy('fullUnitName')->get();
        
        $params = [
            'site' => $site,
            'units' => $units,
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'file_ext' => $file_ext? implode(', ', $file_ext) : 'любые',
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            'employees_search' => $employees_search,
            'units_search' => $units_search,
            'events_search' => $events_search,
        ];

        return view('employees', $params);
    }

    public function edit_employee($id = null){
        $site = env('DB_SITE', 'pguty');
        $employees_search = Employee::orderBy('lastName')->limit(15)->get();
        $units_search = Unit::orderBy('fullUnitName')->limit(15)->get();
        $events_search = Event::orderBy('name')->limit(15)->get();

        $admin = false;

        $file_size = env('FILE_SIZE', 0);
        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $file_ext = env('FILE_EXT', null);
        if($file_ext != null) $file_ext = explode(',', $file_ext);

        $units = Unit::orderBy('fullUnitName')->get();

        $users_search = User::where([['id', '<>', Auth::user()->id]])->orderBy('name')->limit(15)->get();

        $params = [
            'units' => $units,
            'id' => $id,
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'file_ext' => $file_ext? implode(', ', $file_ext) : 'любые',
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            'employees_search' => $employees_search,
            'units_search' => $units_search,
            'events_search' => $events_search,
            'site' => $site,
            'users_search' => $users_search
        ];

        if(isset($id)){
            $empParams = [
                ['id', $id],
            ];

            if(Auth::user()->rights['root'] || (Auth::user()->rights['empAdmin'] != null && time() <= strtotime(Auth::user()->rights['empAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if(!$admin){
                $empParams[] = ['addUserId', Auth::user()->id];
            }

            $employee = Employee::where($empParams);
            
            if($employee->exists()){
                $personals = $employee->first()->personals->first();
                if($personals){
                    $personals = $personals->file;
                }
                
                $params['id'] = $id;
                $params['employee'] = $employee->get()->first();
                $params['addUser'] = $employee->get()->first()->addUserId;
                $params['personals'] = $personals;
                $params['user'] = $employee->first()->user;
                $params['admin'] = $admin;
            }else{
                return redirect(route('employees_list'));
            }
        }

        return view('employeesEdit', $params);
    }

    public function employees_list(Request $request){

        $filter = [];

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['empAdmin'] != null && time() <= strtotime(Auth::user()->rights['empAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            $filter[] = ['addUserId', Auth::user()->id];
        }

        $next_query = [
            'lastName' => '',
            'firstName' => '',
            'secondName' => '',
            'firedFrom' => '',
            'firedTo' => '',
            'hiredFrom' => '',
            'hiredTo' => '',
            'dateBirthdayFrom' => '',
            'dateBirthdayTo' => '',
        ];

        if($request->input("lastName") != null){
            $filter[] = ["lastName", "like", '%' . $request->input("lastName") . '%'];
            $next_query['lastName'] = $request->input("lastName");
        }
        if($request->input("firstName") != null){
            $filter[] = ["firstName", "like", '%' . $request->input("firstName") . '%'];
            $next_query['firstName'] = $request->input("firstName");
        }
        if($request->input("secondName") != null){
            $filter[] = ["secondName", "like", '%' . $request->input("secondName") . '%'];
            $next_query['secondName'] = $request->input("secondName");
        }
        if($request->input("firedFrom") != null){
            $filter[] = ["fired", ">=", $request->input("firedFrom")];
            $next_query['firedFrom'] = $request->input("firedFrom");
        }
        if($request->input("firedTo") != null){
            $filter[] = ["fired", "<", $request->input("firedTo")];
            $next_query['firedTo'] = $request->input("firedTo");
        }
        if($request->input("hiredFrom") != null){
            $filter[] = ["hired", ">=", $request->input("hiredFrom")];
            $next_query['hiredFrom'] = $request->input("hiredFrom");
        }
        if($request->input("hiredTo") != null){
            $filter[] = ["hired", "<", $request->input("hiredTo")];
            $next_query['hiredTo'] = $request->input("hiredTo");
        }
        if($request->input("dateBirthdayFrom") != null){
            $filter[] = ["dateBirthday", ">=", $request->input("dateBirthdayFrom")];
            $next_query['dateBirthdayFrom'] = $request->input("dateBirthdayFrom");
        }
        if($request->input("dateBirthdayTo") != null){
            $filter[] = ["dateBirthday", "<", $request->input("dateBirthdayTo")];
            $next_query['dateBirthdayTo'] = $request->input("dateBirthdayTo");
        }

        $employees = Employee::where($filter)->orderBy("lastName")->paginate(50);

        return view('employeesList', [
            'employees' => $employees,
            'next_query' => $next_query,
            'site' => env('DB_SITE', 'pguty')
        ]);
    }

    public function add_employee(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['empAdmin'] != null && time() <= strtotime(Auth::user()->rights['empAdmin'].' 23:59:59'))){
            $admin = true;
        }
        
        //Если лимит превышен
        if(!$admin){
            if($user->limits['empLimit'] <= 0){
                $response['errors'][] = 'limit'; 
                return $response;
            }
        }

        $file_path = null;

        if(isset($request)){
            $file_size = env('FILE_SIZE', 0);
            $photo_size = env('IMG_SIZE', 0);

            $photo_ext = env('IMG_EXT', null);
            if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

            $file_ext = env('FILE_EXT', null);
            if($file_ext != null) $file_ext = explode(',', $file_ext);

            if(Employee::where([
                ['firstName',  $request->input("firstName")],
                ['lastName', $request->input("lastName")],
                ['secondName', $request->input("secondName")],
                ['dateBirthday', $request->input("dateBirthday")],
            ])->exists()){
                $errors[] = "firstName";
                $errors[] = "lastName";
                $errors[] = "secondName";
                $errors[] = "dateBirthday";
            }
            
            if($request->file("image")){
                if(!is_null($photo_ext)){
                    $ext = $request->file('image')->getClientOriginalExtension();
                    $extError = true;
                    foreach($photo_ext as $value){
                        if($ext == $value){
                            $extError = false;
                        }
                    }

                    if($extError){
                        $errors[] = "empImg";
                    }
                }
                if((filesize($request->file("image")) < $photo_size * 1024) != 1){
                    $errors[] = "empImg";
                }
            }

            if(!trim($request->input("firstName"))) $errors[] = "firstName";
            if(!trim($request->input("lastName"))) $errors[] = "lastName";
            if($request->input("secondName") && !trim($request->input("secondName")))$errors[] = "secondName";
            if($request->input("dateBirthday") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("dateBirthday"))) $errors[] = "dateBirthday";
            if($request->input("fired") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("fired"))) $errors[] = "fired";
            if($request->input("hired") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("hired"))) $errors[] = "hired";
            
            #Проверка информации о образовании
            if($request->input("educations")){
                $educations = json_decode($request->input("educations"), true);
                
                foreach($educations as $education){

                    if(Str::of($education["id"])->trim()->isEmpty()) continue;
                    if(Str::of($education["university"])->trim()->isEmpty()) $errors[] = "university_" . $education["id"];
                    if($education["specialty"] && Str::of($education["specialty"])->trim()->isEmpty()) $errors[] = "specialty_" . $education["id"];
                    if($education["expirationDate"] && Str::of($education["expirationDate"])->trim()->isEmpty()) $errors[] = "expirationDate_" . $education["id"];
                }
            }

            #Проверка информации о научных степенях
            if($request->input("academicDegree")){
                $academicDegree = json_decode($request->input("academicDegree"), true);
                
                foreach($academicDegree as $degree){

                    if(Str::of($degree["id"])->trim()->isEmpty()) continue;
                    if(Str::of($degree["degree"])->trim()->isEmpty()) $errors[] = "degree_" . $degree["id"];
                    if($degree["assignmentDate"] && Str::of($degree["assignmentDate"])->trim()->isEmpty()) $errors[] = "assignmentDate_" . $degree["id"];
                    if($degree["universityDefense"] && Str::of($degree["universityDefense"])->trim()->isEmpty()) $errors[] = "universityDefense_" . $degree["id"];
                    if($degree["topic"] && Str::of($degree["topic"])->trim()->isEmpty()) $errors[] = "topic_" . $degree["id"];
                }
            }

            #Проверка информации о подразделениях
            if($request->input("unit")){
                $units = json_decode($request->input("unit"), true);
                
                foreach($units as $unit){
                    if(Str::of($unit["id"])->trim()->isEmpty()) continue;
                    if(!Unit::where('id', $unit["unit"])->exists()) $errors[] = "unit_" . $unit["id"];
                    if($unit["post"] && Str::of($unit["post"])->trim()->isEmpty()) $errors[] = "post_" . $unit["id"];
                    if($unit["recruitmentDate"] && Str::of($unit["recruitmentDate"])->trim()->isEmpty()) $errors[] = "recruitmentDate_" . $unit["id"];
                }
            }
            
            #Проверяем информацию о научных званиях
            if($request->input("academicTitle")){
                $titles = json_decode($request->input("academicTitle"), true);
                
                foreach($titles as $title){
                    $localError = [];
                    if(Str::of($title["id"])->trim()->isEmpty()) continue;
                    if(Str::of($title["title"])->trim()->isEmpty()) $errors[] = "title_" . $title["id"];
                    if($title["titleDate"] && Str::of($title["titleDate"])->trim()->isEmpty()) $errors[] = "titleDate_" . $title["id"];
                }
            }

            #Проверяем информацию о наградах
            if($request->input("academicReward")){
                $rewards = json_decode($request->input("academicReward"), true);
                
                foreach($rewards as $reward){

                    if(Str::of($reward["id"])->trim()->isEmpty()) continue;
                    if(Str::of($reward["reward"])->trim()->isEmpty()) $errors[] = "reward_" . $reward["id"];
                    if($reward["rewardDate"] && Str::of($reward["rewardDate"])->trim()->isEmpty()) $errors[] = "rewardDate_" . $reward["id"];
                }
            }

            if($request->input("attainment")){
                $attainments = json_decode($request->input("attainment"), true);
                
                foreach($attainments as $attainment){

                    if(Str::of($attainment["id"])->trim()->isEmpty()) continue;
                    if(Str::of($attainment["attainment"])->trim()->isEmpty()) $errors[] = "attainment_" . $attainment["id"];
                    if($attainment["attainmentDate"] && Str::of($attainment["attainmentDate"])->trim()->isEmpty()) $errors[] = "attainmentDate_" . $attainment["id"];
                }
            }

            if($request->input("photo")){
                $photos = json_decode($request->input("photo"), true);
                
                $photoCountCheck = 0;
                foreach($photos as $photo){
                    if(Str::of($photo["id"])->trim()->isEmpty()) continue;
                    if(!$request->file("photo_" . $photoCountCheck) || (filesize($request->file("photo_" . $photoCountCheck)) < $photo_size * 1024) != 1){
                        $errors[] = "photo_" . $photo["id"];
                        continue;
                    }

                    if(!is_null($photo_ext)){
                        $ext = $request->file('photo_'.$photoCountCheck)->getClientOriginalExtension();
                        $extError = true;
                        foreach($photo_ext as $value){
                            if($ext == $value){
                                $extError = false;
                            }
                        }

                        if($extError){
                            $errors[] = "photo_" . $photo["id"];
                        }
                    }

                    if($photo["photoName"] && Str::of($photo["photoName"])->trim()->isEmpty()) $errors[] = "photoName_" . $photo["id"];
                    if($photo["photoDate"] && Str::of($photo["photoDate"])->trim()->isEmpty()) $errors[] = "photoDate_" . $photo["id"];
                    $photoCountCheck++;
                }
            }

            if($request->input("video")){
                $videos = json_decode($request->input("video"), true);
                
                $videoCountCheck = 0;
                foreach($videos as $video){
                    if(Str::of($video["id"])->trim()->isEmpty()) continue;
                    if($video["videoName"] && Str::of($video["videoName"])->trim()->isEmpty()) $errors[] = "videoName_" . $video["id"];
                    if($video["videoDate"] && Str::of($video["videoDate"])->trim()->isEmpty()) $errors[] = "videoDate_" . $video["id"];
                    if(!$video["video"] || Str::of($video["video"])->trim()->isEmpty() || !preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video["video"])) $errors[] = "video_" . $video["id"];
                    $videoCountCheck++;
                }
            }

            if($request->input("autobiography")){
                $autobiographys = json_decode($request->input("autobiography"), true);
                
                $autobiographyCountCheck = 0;
                foreach($autobiographys as $autobiography){

                    if(Str::of($autobiography["id"])->trim()->isEmpty()) continue;
                    if(!$request->file("autobiography_" . $autobiographyCountCheck) || (filesize($request->file("autobiography_" . $autobiographyCountCheck)) < $file_size * 1024) != 1){
                        $errors[] = "autobiography_" . $autobiography["id"];
                        continue;
                    }

                    if(!is_null($file_ext)){
                        $ext = $request->file('autobiography_'.$autobiographyCountCheck)->getClientOriginalExtension();
                        $extError = true;
                        foreach($file_ext as $value){
                            if($ext == $value){
                                $extError = false;
                            }
                        }

                        if($extError){
                            $errors[] = "autobiography_" . $autobiography["id"];
                        }
                    }

                    $autobiographyCountCheck++;
                }
            }

            if($request->file("titlePersonalFile")){
                if(!is_null($file_ext)){
                    $ext = $request->file('titlePersonalFile')->getClientOriginalExtension();
                    $extError = true;
                    foreach($file_ext as $value){
                        if($ext == $value){
                            $extError = false;
                        }
                    }

                    if($extError){
                        $errors[] = "titlePersonalFile";
                    }
                }
                if((filesize($request->file("titlePersonalFile")) < $file_size * 1024) != 1){
                    $errors[] = "titlePersonalFile";
                }
            }

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $newEmployee = new Employee;
                    
                    #Дописать удаление фотографии в случае не удачно транзакции
                    if($request->file("image"))
                    {
                        $file_path = PhotoService::resize($request, 'image', 'uploads/emp/personal', 800);
                        $newEmployee->img = $file_path;
                    }

                    if(Str::of($request->input("firstName"))->trim()->isNotEmpty()) $newEmployee->firstName = trim($request->input("firstName"));
                    if(Str::of($request->input("lastName"))->trim()->isNotEmpty()) $newEmployee->lastName = trim($request->input("lastName"));
                    if(Str::of($request->input("secondName"))->trim()->isNotEmpty()) $newEmployee->secondName = trim($request->input("secondName"));
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newEmployee->description = trim($request->input("description"));
                    if(Str::of($request->input("dateBirthday"))->trim()->isNotEmpty()) $newEmployee->dateBirthday = trim($request->input("dateBirthday"));
                    if(Str::of($request->input("hired"))->trim()->isNotEmpty()) $newEmployee->hired = trim($request->input("hired"));
                    if(Str::of($request->input("fired"))->trim()->isNotEmpty()) $newEmployee->fired = trim($request->input("fired"));
                    #Запись персональных данных
                    $newEmployee->addUserId = Auth::user()->id;
                    $newEmployee->save();

                    #Проверяем есть ли информация о образовании
                    if($request->input("educations")){
                        $educations = json_decode($request->input("educations"), true);
                        #Сохраняем каждую запись о образовании
                        foreach($educations as $education){
                            $newEducation = new Education;
                            $newEducation->employee_id = $newEmployee->id;
                            if(Str::of($education["expirationDate"])->trim()->isNotEmpty()) $newEducation->expirationDate = trim($education["expirationDate"]);
                            $newEducation->university = trim($education["university"]);
                            if(Str::of($education["specialty"])->trim()->isNotEmpty()) $newEducation->specialty = trim($education["specialty"]);
                            $newEducation->save();
                        }
                    }

                    #Проверяем есть ли информация о степенях
                    if($request->input("academicDegree")){
                        $academicDegree = json_decode($request->input("academicDegree"), true);

                        foreach($academicDegree as $degree){
                            $newDegree = new Degree;
                            $newDegree->employee_id = $newEmployee->id;
                            $newDegree->degree = trim($degree["degree"]);
                            if(Str::of($degree["assignmentDate"])->trim()->isNotEmpty()) $newDegree->assignmentDate = trim($degree["assignmentDate"]);
                            if(Str::of($degree["universityDefense"])->trim()->isNotEmpty()) $newDegree->universityDefense = trim($degree["universityDefense"]);
                            if(Str::of($degree["topic"])->trim()->isNotEmpty()) $newDegree->topic = trim($degree["topic"]);
                            $newDegree->save();
                        }
                    }

                    if($request->input("academicTitle")){
                        $titles = json_decode($request->input("academicTitle"), true);

                        foreach($titles as $title){
                            $newTitle = new Title;
                            $newTitle->employee_id = $newEmployee->id;
                            $newTitle->title = trim($title["title"]);
                            if(Str::of($title["titleDate"])->trim()->isNotEmpty()) $newTitle->titleDate = trim($title["titleDate"]);
                            $newTitle->save();
                        }
                    }

                    if($request->input("academicReward")){
                        $rewards = json_decode($request->input("academicReward"), true);

                        foreach($rewards as $reward){
                            $newReward = new Reward;
                            $newReward->employee_id = $newEmployee->id;
                            $newReward->reward = trim($reward["reward"]);
                            if(Str::of($reward["rewardDate"])->trim()->isNotEmpty()) $newReward->rewardDate = trim($reward["rewardDate"]);
                            $newReward->save();
                        }
                    }

                    if($request->input("attainment")){
                        $attainments = json_decode($request->input("attainment"), true);

                        foreach($attainments as $attainment){
                            $newAttainment = new Attainment;
                            $newAttainment->employee_id = $newEmployee->id;
                            $newAttainment->attainment = trim($attainment["attainment"]);
                            if(Str::of($attainment["attainmentDate"])->trim()->isNotEmpty()) $newAttainment->attainmentDate = trim($attainment["attainmentDate"]);
                            $newAttainment->save();
                        }
                    }

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);
            
                        $photoCountData = 0;
            
                        foreach($photos as $photo){
            
                            $reqPhotoName = "photo_" . $photoCountData;
            
                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/emp/photo', 2300);
            
                            $newPhoto = new Photo;
                            $newPhoto->employee_id = $newEmployee->id;
                            $newPhoto->photo = $photoPath;
            
                            if(Str::of($photo["photoDate"])->trim()->isNotEmpty()) $newPhoto->photoDate = trim($photo["photoDate"]);
                            if(Str::of($photo["photoName"])->trim()->isNotEmpty()) $newPhoto->photoName = trim($photo["photoName"]);
            
                            $newPhoto->save();
            
                            $photoCountData++;
                        }
                    }

                    //PhotoService::resizeMany($request, $newEmployee->id, 'uploads/emp/photo');

                    if($request->input("video")){
                        $videos = json_decode($request->input("video"), true);

                        $videoCountData = 0;

                        foreach($videos as $video){
                            $newVideo = new Video;
                            $newVideo->employee_id = $newEmployee->id;
                            preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video['video'], $matches);
                            $newVideo->video = $matches[1];
                            if(Str::of($video["videoDate"])->trim()->isNotEmpty()) $newVideo->videoDate = trim($video["videoDate"]);
                            if(Str::of($video["videoName"])->trim()->isNotEmpty()) $newVideo->videoName = trim($video["videoName"]);
                            $newVideo->save();
                            $videoCountData++;
                        }
                    }

                    if($request->input("autobiography")){
                        $autobiographys = json_decode($request->input("autobiography"), true);

                        $autobiographyCountData = 0;

                        foreach($autobiographys as $autobiography){
                            $autobiographyPath = $request->file("autobiography_" . $autobiographyCountData)->store('uploads/emp/autobiography', 'public');
                            $newAutobiography = new AutobiographyFile;
                            $newAutobiography->employee_id = $newEmployee->id;
                            $newAutobiography->file = $autobiographyPath;
                            $newAutobiography->save();
                            $autobiographyCountData++;
                        }
                    }

                    if($request->input("unit")){
                        $units = json_decode($request->input("unit"), true);

                        foreach($units as $unit){
                            $newUnitEmployee = new UnitEmployee;
                            $newUnitEmployee->employee_id = $newEmployee->id;
                            $newUnitEmployee->unit_id = $unit["unit"];
                            if(Str::of($unit["post"])->trim()->isNotEmpty()) $newUnitEmployee->post = trim($unit["post"]);
                            if(Str::of($unit["recruitmentDate"])->trim()->isNotEmpty()) $newUnitEmployee->recruitmentDate = trim($unit["recruitmentDate"]);
                            $newUnitEmployee->save();
                        }
                    }

                    if($request->file("titlePersonalFile"))
                    {
                        $personalFile = new PersonalFile;
                        $personal_file_path = $request->file("titlePersonalFile")->store('uploads/emp/personal_file', 'public');
                        $personalFile->file = $personal_file_path;
                        $personalFile->employee_id = $newEmployee->id;
                        $personalFile->save();
                    }
                });
                #Проверка успешно ли прошла транзакция
                if($exception){
                    $response['success'] = false;
                }else{
                    if(!$admin){
                        if($user->limits->empLimit > 0){
                            $user->limits->empLimit = $user->limits->empLimit - 1;
                            $user->save();
                        }
                    }
                    $response['success'] = true;
                }
            #Если поля не валидны
            }else{
                $response['errors'] = $errors;
            }
            return $response;
        }
    }

    public function update_employee(Request $request){

        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $admin = false;

        $user = User::where("id", Auth::user()->id)->get()->first();

        $file_path = null;

        if(isset($request)){
            $file_size = env('FILE_SIZE', 0);
            $photo_size = env('IMG_SIZE', 0);

            $photo_ext = env('IMG_EXT', null);
            if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

            $file_ext = env('FILE_EXT', null);
            if($file_ext != null) $file_ext = explode(',', $file_ext);

            $empParams = [
                ['id', $request->input("id")],
            ];

            if(Auth::user()->rights['root'] || (Auth::user()->rights['empAdmin'] != null && time() <= strtotime(Auth::user()->rights['empAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if(!$admin){
                $empParams[] = ['addUserId', Auth::user()->id];
            }

            if(!$request->input("id") ||  !Employee::where($empParams)->exists())
            {
                //Если пользователь пытается отредактировать не свою запись
                return;
            }

            if($request->input('addUserId')){
                if(!$admin) return;
                if($request->input('addUserId') != 'no'){
                    if($request->input('addUserId') == Auth::user()->id) return;
                }
            }

            if($request->file("image")){
                if(!is_null($photo_ext)){
                    $ext = $request->file('image')->getClientOriginalExtension();
                    $extError = true;
                    foreach($photo_ext as $value){
                        if($ext == $value){
                            $extError = false;
                        }
                    }

                    if($extError){
                        $errors[] = "empImg";
                    }
                }
                if((filesize($request->file("image")) < $photo_size * 1024) != 1){
                    $errors[] = "empImg";
                }
            }

            if(!trim($request->input("firstName"))) $errors[] = "firstName";
            if(!trim($request->input("lastName"))) $errors[] = "lastName";
            if($request->input("secondName") && !trim($request->input("secondName")))$errors[] = "secondName";
            if($request->input("dateBirthday") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("dateBirthday"))) $errors[] = "dateBirthday";
            if($request->input("fired") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("fired"))) $errors[] = "fired";
            if($request->input("hired") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("hired"))) $errors[] = "hired";
            
            #Проверка информации о образовании
            if($request->input("educations")){
                $educations = json_decode($request->input("educations"), true);
                
                foreach($educations as $education){

                    if(Str::of($education["id"])->trim()->isEmpty()) continue;
                    if(Str::of($education["university"])->trim()->isEmpty()) $errors[] = "university_" . $education["id"];
                    if($education["specialty"] && Str::of($education["specialty"])->trim()->isEmpty()) $errors[] = "specialty_" . $education["id"];
                    if($education["expirationDate"] && Str::of($education["expirationDate"])->trim()->isEmpty()) $errors[] = "expirationDate_" . $education["id"];
                }
            }

            if($request->input("photo")){
                $photos = json_decode($request->input("photo"), true);
                
                $photoCountCheck = 0;
                foreach($photos as $photo){
                    if(Str::of($photo["id"])->trim()->isEmpty()) continue;
                    if(!$request->file("photo_" . $photoCountCheck) || (filesize($request->file("photo_" . $photoCountCheck)) < $photo_size * 1024) != 1){
                        $errors[] = "photo_" . $photo["id"];
                        continue;
                    }

                    if(!is_null($photo_ext)){
                        $ext = $request->file('photo_'.$photoCountCheck)->getClientOriginalExtension();
                        $extError = true;
                        foreach($photo_ext as $value){
                            if($ext == $value){
                                $extError = false;
                            }
                        }

                        if($extError){
                            $errors[] = "photo_" . $photo["id"];
                        }
                    }

                    if($photo["photoName"] && Str::of($photo["photoName"])->trim()->isEmpty()) $errors[] = "photoName_" . $photo["id"];
                    if($photo["photoDate"] && Str::of($photo["photoDate"])->trim()->isEmpty()) $errors[] = "photoDate_" . $photo["id"];
                    $photoCountCheck++;
                }
            }

            if($request->input("video")){
                $videos = json_decode($request->input("video"), true);
                
                $videoCountCheck = 0;
                foreach($videos as $video){
                    if(Str::of($video["id"])->trim()->isEmpty()) continue;
                    if($video["videoName"] && Str::of($video["videoName"])->trim()->isEmpty()) $errors[] = "videoName_" . $video["id"];
                    if($video["videoDate"] && Str::of($video["videoDate"])->trim()->isEmpty()) $errors[] = "videoDate_" . $video["id"];
                    if(!$video["video"] || Str::of($video["video"])->trim()->isEmpty() || !preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video["video"])) $errors[] = "video_" . $video["id"];
                    $videoCountCheck++;
                }
            }

            if($request->input("photoToDelete")){
                $photoToDelete = explode(',',$request->input("photoToDelete"));

                #Сохраняем каждую запись о образовании
                foreach($photoToDelete as $index => $photo){
                    $photoTmp = Photo::where('id', $photo);

                    if($photoTmp->exists()){

                        $noRight = true;

                        if($photoTmp->first()->employee->addUserId == Auth::user()->id){
                            $noRight = false; 
                        }

                        if($admin){
                            $noRight = false;
                        }

                        if($noRight){
                            return;
                        }
                    }
                }
            }

            if($request->input("videoToDelete")){
                $videoToDelete = explode(',',$request->input("videoToDelete"));

                #Сохраняем каждую запись о образовании
                foreach($videoToDelete as $index => $video){
                    $videoTmp = Video::where('id', $video);

                    if($videoTmp->exists()){

                        $noRight = true;

                        if($videoTmp->first()->employee->addUserId == Auth::user()->id){
                            $noRight = false;
                        }

                        if($admin){
                            $noRight = false;
                        }

                        if($noRight){
                            return;
                        }
                    }
                }
            }

            if($request->input("autobiographyToDelete")){
                $autobiographyToDelete = explode(',',$request->input("autobiographyToDelete"));

                #Сохраняем каждую запись о образовании
                foreach($autobiographyToDelete as $index => $autobiography){
                    $autobiographyTmp = AutobiographyFile::where('id', $autobiography);

                    if($autobiographyTmp->exists()){

                        $noRight = true;

                        if($autobiographyTmp->first()->employee->addUserId == Auth::user()->id){
                            $noRight = false;
                        }

                        if($admin){
                            $noRight = false;
                        }

                        if($noRight){
                            return;
                        }
                    }
                }
            }

            #Проверка информации о научных степенях
            if($request->input("academicDegree")){
                $academicDegree = json_decode($request->input("academicDegree"), true);
                
                foreach($academicDegree as $degree){

                    if(Str::of($degree["id"])->trim()->isEmpty()) continue;
                    if(Str::of($degree["degree"])->trim()->isEmpty()) $errors[] = "degree_" . $degree["id"];
                    if($degree["assignmentDate"] && Str::of($degree["assignmentDate"])->trim()->isEmpty()) $errors[] = "assignmentDate_" . $degree["id"];
                    if($degree["universityDefense"] && Str::of($degree["universityDefense"])->trim()->isEmpty()) $errors[] = "universityDefense_" . $degree["id"];
                    if($degree["topic"] && Str::of($degree["topic"])->trim()->isEmpty()) $errors[] = "topic_" . $degree["id"];
                }
            }

            #Проверка информации о подразделениях
            if($request->input("unit")){
                $units = json_decode($request->input("unit"), true);
                
                foreach($units as $unit){
                    if(Str::of($unit["id"])->trim()->isEmpty()) continue;
                    if(!Unit::where('id', $unit["unit"])->exists()) $errors[] = "unit_" . $unit["id"];
                    if($unit["post"] && Str::of($unit["post"])->trim()->isEmpty()) $errors[] = "post_" . $unit["id"];
                    if($unit["recruitmentDate"] && Str::of($unit["recruitmentDate"])->trim()->isEmpty()) $errors[] = "recruitmentDate_" . $unit["id"];
                }
            }
            
            #Проверяем информацию о научных званиях
            if($request->input("academicTitle")){
                $titles = json_decode($request->input("academicTitle"), true);
                
                foreach($titles as $title){
                    $localError = [];
                    if(Str::of($title["id"])->trim()->isEmpty()) continue;
                    if(Str::of($title["title"])->trim()->isEmpty()) $errors[] = "title_" . $title["id"];
                    if($title["titleDate"] && Str::of($title["titleDate"])->trim()->isEmpty()) $errors[] = "titleDate_" . $title["id"];
                }
            }

            #Проверяем информацию о наградах
            if($request->input("academicReward")){
                $rewards = json_decode($request->input("academicReward"), true);
                
                foreach($rewards as $reward){

                    if(Str::of($reward["id"])->trim()->isEmpty()) continue;
                    if(Str::of($reward["reward"])->trim()->isEmpty()) $errors[] = "reward_" . $reward["id"];
                    if($reward["rewardDate"] && Str::of($reward["rewardDate"])->trim()->isEmpty()) $errors[] = "rewardDate_" . $reward["id"];
                }
            }

            if($request->input("attainment")){
                $attainments = json_decode($request->input("attainment"), true);
                
                foreach($attainments as $attainment){

                    if(Str::of($attainment["id"])->trim()->isEmpty()) continue;
                    if(Str::of($attainment["attainment"])->trim()->isEmpty()) $errors[] = "attainment_" . $attainment["id"];
                    if($attainment["attainmentDate"] && Str::of($attainment["attainmentDate"])->trim()->isEmpty()) $errors[] = "attainmentDate_" . $attainment["id"];
                }
            }

            if($request->input("autobiography")){
                $autobiographys = json_decode($request->input("autobiography"), true);
                
                $autobiographyCountCheck = 0;
                foreach($autobiographys as $autobiography){

                    if(Str::of($autobiography["id"])->trim()->isEmpty()) continue;
                    if(!$request->file("autobiography_" . $autobiographyCountCheck) || (filesize($request->file("autobiography_" . $autobiographyCountCheck)) < $file_size * 1024) != 1){
                        $errors[] = "autobiography_" . $autobiography["id"];
                        continue;
                    }

                    if(!is_null($file_ext)){
                        $ext = $request->file('autobiography_'.$autobiographyCountCheck)->getClientOriginalExtension();
                        $extError = true;
                        foreach($file_ext as $value){
                            if($ext == $value){
                                $extError = false;
                            }
                        }

                        if($extError){
                            $errors[] = "autobiography_" . $autobiography["id"];
                        }
                    }

                    $autobiographyCountCheck++;
                }
            }

            if($request->file("titlePersonalFile")){
                if(!is_null($file_ext)){
                    $ext = $request->file('titlePersonalFile')->getClientOriginalExtension();
                    $extError = true;
                    foreach($file_ext as $value){
                        if($ext == $value){
                            $extError = false;
                        }
                    }

                    if($extError){
                        $errors[] = "titlePersonalFile";
                    }
                }
                if((filesize($request->file("titlePersonalFile")) < $file_size * 1024) != 1){
                    $errors[] = "titlePersonalFile";
                }
            }

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editEmployee = Employee::where("id", $request->input("id"));
                    $newEmpInfo = [];

                    if($request->input('addUserId')){
                        if($request->input('addUserId') == 'no'){
                            $newEmpInfo["addUserId"] = null;
                        }else{
                            $newEmpInfo["addUserId"] = $request->input('addUserId');
                        }
                    }
                    
                    #Дописать удаление фотографии в случае не удачно транзакции
                    if($request->input("deleteImg")){
                        Storage::disk('public')->delete($editEmployee->first()->img);
                        $newEmpInfo['img'] = null;
                    }

                    if($request->file("image"))
                    {
                        Storage::disk('public')->delete($editEmployee->first()->img);
                        $file_path = PhotoService::resize($request, 'image', 'uploads/emp/personal', 800);
                        $newEmpInfo['img'] = $file_path;
                    }
                    
                    if(Str::of($request->input("firstName"))->trim()->isNotEmpty()) $newEmpInfo['firstName'] = trim($request->input("firstName"));
                    if(Str::of($request->input("lastName"))->trim()->isNotEmpty()) $newEmpInfo['lastName'] = trim($request->input("lastName"));
                    if(Str::of($request->input("secondName"))->trim()->isNotEmpty()) $newEmpInfo['secondName'] = trim($request->input("secondName"));
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newEmpInfo['description'] = trim($request->input("description"));
                    if(Str::of($request->input("dateBirthday"))->trim()->isNotEmpty()) $newEmpInfo['dateBirthday'] = trim($request->input("dateBirthday"));
                    if(Str::of($request->input("hired"))->trim()->isNotEmpty()) $newEmpInfo['hired'] = trim($request->input("hired"));
                    if(Str::of($request->input("fired"))->trim()->isNotEmpty()) $newEmpInfo['fired'] = trim($request->input("fired"));
                    #Запись персональных данных
                    $editEmployee->update($newEmpInfo);

                    #Проверяем есть ли информация о образовании
                    if($request->input("educations")){
                        $educations = json_decode($request->input("educations"), true);

                        Education::where('employee_id', $editEmployee->first()->id)->delete();
                        #Сохраняем каждую запись о образовании
                        foreach($educations as $education){
                            $newEducation = new Education;
                            $newEducation->employee_id = $editEmployee->first()->id;
                            if(Str::of($education["expirationDate"])->trim()->isNotEmpty()) $newEducation->expirationDate = trim($education["expirationDate"]);
                            $newEducation->university = trim($education["university"]);
                            if(Str::of($education["specialty"])->trim()->isNotEmpty()) $newEducation->specialty = trim($education["specialty"]);
                            $newEducation->save();
                        }
                    }

                    if($request->input("photoToDelete")){
                        $photoToDelete = explode(',',$request->input("photoToDelete"));

                        #Сохраняем каждую запись о образовании
                        foreach($photoToDelete as $index => $photo){
                            $oldPhoto = Photo::where('id', $photo);
                            if($oldPhoto->exists()){
                                Storage::disk('public')->delete($oldPhoto->first()->photo);
                                $oldPhoto->delete();
                            }
                        }
                    }
                    
                    if($request->input("videoToDelete")){
                        $videoToDelete = explode(',',$request->input("videoToDelete"));

                        #Сохраняем каждую запись о образовании
                        foreach($videoToDelete as $index => $video){

                            $oldVideo = Video::where('id', $video);
                            if($oldVideo->exists()){
                                $oldVideo->delete();
                            }
                        }
                    }

                    if($request->input("autobiographyToDelete")){
                        $autobiographyToDelete = explode(',',$request->input("autobiographyToDelete"));

                        #Сохраняем каждую запись о образовании
                        foreach($autobiographyToDelete as $index => $autobiography){

                            $oldAutobiographyFile = AutobiographyFile::where('id', $autobiography);
                            if($oldAutobiographyFile->exists()){
                                Storage::disk('public')->delete($oldAutobiographyFile->first()->file);
                                $oldAutobiographyFile->delete();
                            }
                        }
                    }

                    #Проверяем есть ли информация о степенях
                    if($request->input("academicDegree")){
                        $academicDegree = json_decode($request->input("academicDegree"), true);

                        Degree::where('employee_id', $editEmployee->first()->id)->delete();

                        foreach($academicDegree as $degree){
                            $newDegree = new Degree;
                            $newDegree->employee_id = $editEmployee->first()->id;
                            $newDegree->degree = trim($degree["degree"]);
                            if(Str::of($degree["assignmentDate"])->trim()->isNotEmpty()) $newDegree->assignmentDate = trim($degree["assignmentDate"]);
                            if(Str::of($degree["universityDefense"])->trim()->isNotEmpty()) $newDegree->universityDefense = trim($degree["universityDefense"]);
                            if(Str::of($degree["topic"])->trim()->isNotEmpty()) $newDegree->topic = trim($degree["topic"]);
                            $newDegree->save();
                        }
                    }

                    if($request->input("academicTitle")){
                        $titles = json_decode($request->input("academicTitle"), true);
                        
                        Title::where('employee_id', $editEmployee->first()->id)->delete();

                        foreach($titles as $title){
                            $newTitle = new Title;
                            $newTitle->employee_id = $editEmployee->first()->id;
                            $newTitle->title = trim($title["title"]);
                            if(Str::of($title["titleDate"])->trim()->isNotEmpty()) $newTitle->titleDate = trim($title["titleDate"]);
                            $newTitle->save();
                        }
                    }

                    if($request->input("academicReward")){
                        $rewards = json_decode($request->input("academicReward"), true);

                        Reward::where('employee_id', $editEmployee->first()->id)->delete();

                        foreach($rewards as $reward){
                            $newReward = new Reward;
                            $newReward->employee_id = $editEmployee->first()->id;
                            $newReward->reward = trim($reward["reward"]);
                            if(Str::of($reward["rewardDate"])->trim()->isNotEmpty()) $newReward->rewardDate = trim($reward["rewardDate"]);
                            $newReward->save();
                        }
                    }

                    if($request->input("attainment")){
                        $attainments = json_decode($request->input("attainment"), true);

                        Attainment::where('employee_id', $editEmployee->first()->id)->delete();

                        foreach($attainments as $attainment){
                            $newAttainment = new Attainment;
                            $newAttainment->employee_id = $editEmployee->first()->id;
                            $newAttainment->attainment = trim($attainment["attainment"]);
                            if(Str::of($attainment["attainmentDate"])->trim()->isNotEmpty()) $newAttainment->attainmentDate = trim($attainment["attainmentDate"]);
                            $newAttainment->save();
                        }
                    }

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){

                            $reqPhotoName = "photo_" . $photoCountData;

                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/emp/photo', 2300);

                            $newPhoto = new Photo;
                            $newPhoto->employee_id = $editEmployee->first()->id;
                            $newPhoto->photo = $photoPath;
                            if(Str::of($photo["photoDate"])->trim()->isNotEmpty()) $newPhoto->photoDate = trim($photo["photoDate"]);
                            if(Str::of($photo["photoName"])->trim()->isNotEmpty()) $newPhoto->photoName = trim($photo["photoName"]);
                            $newPhoto->save();
                            $photoCountData++;
                        }
                    }

                    if($request->input("video")){
                        $videos = json_decode($request->input("video"), true);

                        $videoCountData = 0;

                        foreach($videos as $video){
                            $newVideo = new Video;
                            $newVideo->employee_id = $editEmployee->first()->id;
                            preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video['video'], $matches);
                            $newVideo->video = $matches[1];
                            if(Str::of($video["videoDate"])->trim()->isNotEmpty()) $newVideo->videoDate = trim($video["videoDate"]);
                            if(Str::of($video["videoName"])->trim()->isNotEmpty()) $newVideo->videoName = trim($video["videoName"]);
                            $newVideo->save();
                            $videoCountData++;
                        }
                    }

                    if($request->input("unit")){
                        $units = json_decode($request->input("unit"), true);

                        UnitEmployee::where('employee_id', $editEmployee->first()->id)->delete();

                        foreach($units as $unit){
                            $newUnitEmployee = new UnitEmployee;
                            $newUnitEmployee->employee_id = $editEmployee->first()->id;
                            $newUnitEmployee->unit_id = $unit["unit"];
                            if(Str::of($unit["post"])->trim()->isNotEmpty()) $newUnitEmployee->post = trim($unit["post"]);
                            if(Str::of($unit["recruitmentDate"])->trim()->isNotEmpty()) $newUnitEmployee->recruitmentDate = trim($unit["recruitmentDate"]);
                            $newUnitEmployee->save();
                        }
                    }

                    if($request->input("autobiography")){
                        $autobiographys = json_decode($request->input("autobiography"), true);

                        $autobiographyCountData = 0;

                        foreach($autobiographys as $autobiography){
                            $autobiographyPath = $request->file("autobiography_" . $autobiographyCountData)->store('uploads/emp/autobiography', 'public');
                            $newAutobiography = new AutobiographyFile;
                            $newAutobiography->employee_id = $editEmployee->first()->id;
                            $newAutobiography->file = $autobiographyPath;
                            $newAutobiography->save();
                            $autobiographyCountData++;
                        }
                    }

                    if($request->input("deletePersonalFile")){
                        $oldPersonalFile = PersonalFile::where('employee_id', $editEmployee->first()->id);
                        if($oldPersonalFile->exists()){
                            Storage::disk('public')->delete($oldPersonalFile->first()->file);
                            $oldPersonalFile->delete();
                        }
                    }

                    if($request->file("titlePersonalFile"))
                    {
                        $oldPersonalFile = PersonalFile::where('employee_id', $editEmployee->first()->id);
                        if($oldPersonalFile->exists()){
                            Storage::disk('public')->delete($oldPersonalFile->first()->file);
                            $oldPersonalFile->delete();
                        }

                        $personalFile = new PersonalFile;
                        $personal_file_path = $request->file("titlePersonalFile")->store('uploads/emp/personal_file', 'public');
                        $personalFile->file = $personal_file_path;
                        $personalFile->employee_id = $editEmployee->first()->id;
                        $personalFile->save();
                    }
                });

            $employee = Employee::where("id", $request->input("id"))->first();

            $response['photos'] = view('ajax.photos', [
                'employee' => $employee
            ])->render();

            $response['videos'] = view('ajax.videos', [
                'employee' => $employee
            ])->render();

            $response['autobiographys'] = view('ajax.autobiographys', [
                'employee' => $employee
            ])->render();

            $response['imgEmp'] = view('ajax.imgEmp', [
                'employee' => $employee
            ])->render();

            $personals = $employee->personals->first();
            if($personals){
                $personals = $personals->file;
            }

            $response['personalFile'] = view('ajax.personalFile', [
                'personals' => $personals
            ])->render();
            #Проверка успешно ли прошла транзакция
            if($exception){
                $response['success'] = false;
            }else{
                $response['success'] = true;
            }
            #Если поля не валидны
            }else{
                $response['errors'] = $errors;
            }

            return $response;
        }
    }
}
