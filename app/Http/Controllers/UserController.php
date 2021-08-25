<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLimit;
use App\Models\UserRight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function search_user(Request $request){
        $filter = [
            ['id', '<>', Auth::user()->id]
        ];

        if($request->input("name") != null){
            $filter[] = ["name", "like", '%' . $request->input("name") . '%'];
        }

        if($request->input("email") != null){
            $filter[] = ["email", "like", '%' . $request->input("email") . '%'];
        }

        $users_search = User::where($filter)->orderBy('name')->limit(15)->get();

        return view('ajax.searchUser', [
            'users_search' => $users_search
        ])->render();
    }

    public function users_list(Request $request){

        $filter = [
            ['id', '<>', Auth::user()->id]
        ];

        $access = false;
        $root = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['heroAdmin'] != null && time() <= strtotime(Auth::user()->rights['heroAdmin'].' 23:59:59')) || (Auth::user()->rights['unitAdmin'] != null && time() <= strtotime(Auth::user()->rights['unitAdmin'].' 23:59:59')) || (Auth::user()->rights['empAdmin'] != null && time() <= strtotime(Auth::user()->rights['empAdmin'].' 23:59:59')) || (Auth::user()->rights['eventAdmin'] != null && time() <= strtotime(Auth::user()->rights['eventAdmin'].' 23:59:59'))){
            $access = true;
        }

        if(Auth::user()->rights['root']){
            $root = true;
        }

        $user = Auth::user()->rights['root'];

        $next_query = [
            'name' => '',
            'dateFrom' => '',
            'dateTo' => '',
        ];

        if($request->input("name") != null){
            $filter[] = ["name", "like", '%' . $request->input("name") . '%'];
            $next_query['name'] = $request->input("name");
        }

        if($request->input("email") != null){
            $filter[] = ["email", "like", '%' . $request->input("email") . '%'];
            $next_query['email'] = $request->input("email");
        }

        $users = User::where($filter)->orderBy("name")->paginate(50);

        return view('usersList', [
            'user' => $user,
            'users' => $users,
            'next_query' => $next_query,
            'access' => $access,
            'root' => $root
        ]);
    }

    public function delete_user(Request $request){
        $root = false;

        if(Auth::user()->rights['root']){
            $root = true;
        }

        if(!$root){
            return;
        }

        $user = User::where('id', $request->input('id'))->first();

        if($user->exists()){

            if($user->rights['root']){
                return;
            }
    
            if($user->id == Auth::user()->id){
                return;
            }

            $user->delete();

            Log::channel('user')->info('Delete user', [
                'who_id' => Auth::user()->id,
                'who_email' => Auth::user()->email,
                'who_name' => isset(Auth::user()->name)? Auth::user()->name : '',
                'user_id' => $user->id,
                'unit_email' => $user->email,
                'unit_name' => $user->name,
            ]);
        }
        
        return true;
    }

    public function edit_user($id = null){

        $user = Auth::user();

        if($user->id == $id){
            return redirect(route('users_list'));
        }

        $access = [
            'empAdmin' => false,
            'unitAdmin' => false,
            'eventAdmin' => false,
            'graduateFileAdmin' => false,
            'heroAdmin' => false,
        ];

        if($user->rights['empAdmin'] != null && time() <= strtotime($user->rights['empAdmin'].' 23:59:59')){
            $access['empAdmin'] = true;
        }

        if($user->rights['unitAdmin'] != null && time() <= strtotime($user->rights['unitAdmin'].' 23:59:59')){
            $access['unitAdmin'] = true;
        }

        if($user->rights['eventAdmin'] != null && time() <= strtotime($user->rights['eventAdmin'].' 23:59:59')){
            $access['eventAdmin'] = true;
        }

        if($user->rights['graduateFileAdmin'] != null && time() <= strtotime($user->rights['graduateFileAdmin'].' 23:59:59')){
            $access['graduateFileAdmin'] = true;
        }

        if($user->rights['heroAdmin'] != null && time() <= strtotime($user->rights['heroAdmin'].' 23:59:59')){
            $access['heroAdmin'] = true;
        }

        if((!$access['graduateFileAdmin'] && !$access['eventAdmin'] && !$access['unitAdmin'] && !$access['empAdmin'] && !$access['heroAdmin'])){
            if(!$user->rights['root']){
                return redirect(route('users_list'));
            }
        }

        $params = [
            'root' => $user->rights['root'],
            'access' => $access,
        ];

        if(isset($id)){
            $userParams = [
                ['id', $id],
            ];

            $editUser = User::where($userParams);

            if($editUser->exists()){
                $params['id'] = $id;
                $params['editUser'] = $editUser->get()->first();
            }else{
                return redirect(route('users_list'));
            }
        }

        return view('userEdit', $params);
    }

    public function update_user(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        if(isset($request)){
            $userParams = [];
            
            if($request->input("id")){
                $userParams = [
                    'id' => $request->input("id")
                ];
            }else{
                return;  
            }

            $editUser = User::where($userParams);

            if(!$editUser->exists())
            {
                return;
            }

            if(Auth::user()->id == $request->input("id")){
                return;
            }

            if(!Auth::user()->rights['root']){

                if($request->input("empLimit")){
                    if(Auth::user()->rights['empAdmin'] == null || time() > strtotime(Auth::user()->rights['empAdmin'])){
                        return;
                    }
                }

                if($request->input("unitLimit")){
                    if(Auth::user()->rights['unitAdmin'] == null || time() > strtotime(Auth::user()->rights['unitAdmin'])){
                        return;
                    }
                }

                if($request->input("eventLimit")){
                    if(Auth::user()->rights['eventAdmin'] == null || time() > strtotime(Auth::user()->rights['eventAdmin'])){
                        return;
                    }
                }

                if($request->input("eventFileLimit")){
                    if(Auth::user()->rights['eventAdmin'] == null || time() > strtotime(Auth::user()->rights['eventAdmin'])){
                        return;
                    }
                }

                if($request->input("graduateFileLimit")){
                    if(Auth::user()->rights['graduateFileAdmin'] == null || time() > strtotime(Auth::user()->rights['graduateFileAdmin'])){
                        return;
                    }
                }

                if($request->input("heroLimit")){
                    if(Auth::user()->rights['heroAdmin'] == null || time() > strtotime(Auth::user()->rights['heroAdmin'])){
                        return;
                    }
                }

                if($editUser->first()->rights['root']){
                    return;
                }

                if($request->input("empAdmin")){
                    return;
                }

                if($request->input("unitAdmin")){
                    return;
                }

                if($request->input("eventAdmin")){
                    return;
                }

                if($request->input("heroAdmin")){
                    return;
                }

                if($request->input("graduateFileAdmin")){
                    return;
                }

                if($request->input("pageAdmin")){
                    return;
                }
            }

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editLimits = UserLimit::where("user_id", $request->input("id"));
                    $newLimitsInfo = [];

                    if($request->input("empLimit")){
                        $newLimitsInfo["empLimit"] = $request->input("empLimit");
                    }

                    if($request->input("unitLimit")){
                        $newLimitsInfo["unitLimit"] = $request->input("unitLimit");
                    }

                    if($request->input("eventLimit")){
                        $newLimitsInfo["eventLimit"] = $request->input("eventLimit");
                    }

                    if($request->input("eventFileLimit")){
                        $newLimitsInfo["eventFileLimit"] = $request->input("eventFileLimit");
                    }

                    if($request->input("graduateFileLimit")){
                        $newLimitsInfo["graduateFileLimit"] = $request->input("graduateFileLimit");
                    }

                    if($request->input("heroLimit")){
                        $newLimitsInfo["heroLimit"] = $request->input("heroLimit");
                    }

                    $editRights = UserRight::where("user_id", $request->input("id"));

                    $newRightsInfo = [];

                    if($request->input("empAdmin")){
                        $newRightsInfo["empAdmin"] = $request->input("empAdmin");
                    }

                    if($request->input("unitAdmin")){
                        $newRightsInfo["unitAdmin"] = $request->input("unitAdmin");
                    }

                    if($request->input("eventAdmin")){
                        $newRightsInfo["eventAdmin"] = $request->input("eventAdmin");
                    }

                    if($request->input("graduateFileAdmin")){
                        $newRightsInfo["graduateFilAdmin"] = $request->input("graduateFileAdmin");
                    }

                    if($request->input("heroAdmin")){
                        $newRightsInfo["heroAdmin"] = $request->input("heroAdmin");
                    }

                    if($request->input("pageAdmin")){
                        $newRightsInfo["pageAdmin"] = $request->input("pageAdmin");
                    }

                    $editLimits->update($newLimitsInfo);
                    $editRights->update($newRightsInfo);
                });

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
