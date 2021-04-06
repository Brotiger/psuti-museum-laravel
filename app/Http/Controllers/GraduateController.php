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

class GraduateController extends Controller
{
    public function more_graduate($id = null){
        $counter = User::where("id", Auth::user()->id)->get()->first()->graduateCount;

        $params = [
            'counter' => $counter,
        ];

        if(isset($id)){
            $graduate = Graduate::where([
                ['id', $id],
                ['addUserId', Auth::user()->id]
            ]);
            if($graduate->exists()){
                $params['graduate'] = $graduate->get()->first();
            }else{
                return redirect(route('graduates_list'));
            }
        }

        return view('graduateMore', $params);
    }

    public function graduates_list(Request $request){
        $filter = [
            ['addUserId', Auth::user()->id]
        ];
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

        $graduates = Graduate::where($filter)->orderBy("firstName")->get();
        $counter = User::where("id", Auth::user()->id)->get()->first()->graduateCount;

        if($request->ajax()){
            return view('filters.graduatesList', [
                'graduates' => $graduates
            ])->render();
        }

        return view('graduatesList', [
            'graduates' => $graduates,
            'counter' => $counter
        ]);
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

        //Если лимит превышен
        if($user['graduateLimit'] <= 0){
            $response['errors'][] = 'limit'; 
            return $response;
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
                if($user['graduateLimit'] > 0){
                    $user->empLimit = $user['graduateLimit'] - 1;
                    $user->graduateCount = $user->graduateCount + 1;
                    $user->save();
                }
                $response['success'] = true;
            }
        }else{
            $response['errors'] = $errors;
        }
        return $response;
    }
}
