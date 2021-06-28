<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Imports\GraduatesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\GraduateCount;
use App\Models\Graduate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GraduateController extends Controller
{
    public function more_graduate($id = null){
        $params = [];

        if(isset($id)){
            $graduateParams = [
                ['id', $id],
            ];

            $admin = false;

            if(Auth::user()->rights['root'] || (Auth::user()->rights['graduateAdmin'] != null && time() <= strtotime(Auth::user()->rights['graduateAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if(!$admin){
                $graduateParams[] = ['addUserId', Auth::user()->id];
            }

            $graduate = Graduate::where($graduateParams);

            if($graduate->exists()){
                $params['graduate'] = $graduate->get()->first();
            }else{
                return redirect(route('graduates_list'));
            }
        }

        return view('graduateMore', $params);
    }

    public function graduates_list(Request $request){
        $filter = [];

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['graduateAdmin'] != null && time() <= strtotime(Auth::user()->rights['graduateAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            $filter[] = ['addUserId', Auth::user()->id];
        }

        if($request->input("firstName") != null) $filter[] = ["firstName", "like", '%' . $request->input("firstName") . '%'];
        if($request->input("lastName") != null) $filter[] = ["lastName", "like", '%' . $request->input("lastName") . '%'];
        if($request->input("registrationNumber") != null) $filter[] = ["registrationNumber", "like", '%' . $request->input("registrationNumber") . '%'];
        if($request->input("secondName") != null) $filter[] = ["secondName", "like", '%' . $request->input("secondName") . '%'];
        if($request->input("dateBirthdayFrom") != null) $filter[] = ["dateBirthday", ">=", $request->input("dateBirthdayFrom")];
        if($request->input("dateBirthdayTo") != null) $filter[] = ["dateBirthday", "<", $request->input("dateBirthdayTo")];
        if($request->input("enteredYearFrom") != null) $filter[] = ["enteredYear", ">=", $request->input("enteredYearFrom")];
        if($request->input("enteredYearTo") != null) $filter[] = ["enteredYear", "<", $request->input("enteredYearTo")];
        if($request->input("exitYearFrom") != null) $filter[] = ["exitYear", ">=", $request->input("exitYearFrom")];
        if($request->input("exitYearTo") != null) $filter[] = ["exitYear", "<", $request->input("exitYearTo")];

        $next_query = [
            'firstName' => '',
            'lastName' => '',
            'registrationNumber' => '',
            'secondName' => '',
            'dateBirthdayFrom' => '',
            'dateBirthdayTo' => '',
            'enteredYearFrom' => '',
            'enteredYearTo' => '',
            'exitYearFrom' => '',
            'exitYearTo' => '',
        ];

        if($request->input("firstName") != null){
            $filter[] = ["firstName", "like", '%' . $request->input("firstName") . '%'];
            $next_query['firstName'] = $request->input("firstName");
        }
        if($request->input("lastName") != null){
            $filter[] = ["lastName", "like", '%' . $request->input("lastName") . '%'];
            $next_query['lastName'] = $request->input("lastName");
        }
        if($request->input("registrationNumber") != null){
            $filter[] = ["registrationNumber", "like", '%' . $request->input("registrationNumber") . '%'];
            $next_query['registrationNumber'] = $request->input("registrationNumber");
        }
        if($request->input("secondName") != null){
            $filter[] = ["secondName", "like", '%' . $request->input("secondName") . '%'];
            $next_query['secondName'] = $request->input("secondName");
        }
        if($request->input("dateBirthdayFrom") != null){
            $filter[] = ["dateBirthday", ">=", $request->input("dateBirthdayFrom")];
            $next_query['dateBirthdayFrom'] = $request->input("dateBirthdayFrom");
        }
        if($request->input("dateBirthdayTo") != null){
            $filter[] = ["dateBirthday", "<", $request->input("dateBirthdayTo")];
            $next_query['dateBirthdayTo'] = $request->input("dateBirthdayTo");
        }
        if($request->input("enteredYearFrom") != null){
            $filter[] = ["enteredYear", ">=", $request->input("enteredYearFrom")];
            $next_query['enteredYearFrom'] = $request->input("enteredYearFrom");
        }
        if($request->input("enteredYearTo") != null){
            $filter[] = ["enteredYear", "<", $request->input("enteredYearTo")];
            $next_query['enteredYearTo'] = $request->input("enteredYearTo");
        }
        if($request->input("exitYearFrom") != null){
            $filter[] = ["exitYear", ">=", $request->input("exitYearFrom")];
            $next_query['exitYearFrom'] = $request->input("exitYearFrom");
        }
        if($request->input("exitYearTo") != null){
            $filter[] = ["exitYear", "<", $request->input("exitYearTo")];
            $next_query['exitYearTo'] = $request->input("exitYearTo");
        }

        $graduates = Graduate::where($filter)->orderBy("firstName")->paginate(50);

        return view('graduatesList', [
            'graduates' => $graduates,
            'next_query' => $next_query,
            'site' => env('DB_SITE', 'pguty'),
            'admin' => $admin
        ]);
    }

    public function delete_graduate(Request $request){
        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['eventAdmin'] != null && time() <= strtotime(Auth::user()->rights['eventAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            return;
        }

        $graduate = Graduate::where('id', $request->input('id'))->first();

        if($graduate->exists()){

            $graduate->delete();

            Log::channel('graduate')->info('Delete graduate', [
                'who_id' => Auth::user()->id,
                'who_email' => Auth::user()->email,
                'who_name' => isset(Auth::user()->name)? Auth::user()->name : '',
                'graduate_id' => $graduate->id,
                'graduate_first_name' => isset($graduate->firstName)? $graduate->firstName : '',
                'graduate_last_name' => isset($graduate->lastName)? $graduate->lastName : '',
                'graduate_second_name' => isset($graduate->secondName)? $graduate->secondName : '',
            ]);
        }
        
        return true;
    }

    public function index(){
        $user = User::where("id", Auth::user()->id)->get()->first();

        return view('graduate',[
            "counter" => $user->graduateCount
        ]);
    }
    public function add_graduate(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['graduateAdmin'] != null && time() <= strtotime(Auth::user()->rights['graduateAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            if($user->limits['graduateLimit'] <= 0){
                $response['errors'][] = 'limit'; 
                return $response;
            }
        }

        if(isset($request)){
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx'
            ])->validate();

            if(!$validator) $errors[] = "file";
        }
        if(empty($errors)){
            $exception = DB::transaction(function() use ($request){
                
               $exception = Excel::import(new GraduatesImport(), $request->file("file"));

            });
            #Проверка успешно ли прошла транзакция
            if($exception){
                $response['success'] = false;
            }else{
                if(!$admin){
                    if($user->limits['graduateLimit'] > 0){
                        $user->limits->graduateLimit = $user->limits['graduateLimit'] - 1;
                        $user->save();
                    }
                }
                $response['success'] = true;
            }
        }else{
            $response['errors'] = $errors;
        }
        return $response;
    }
}
