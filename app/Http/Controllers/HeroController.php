<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employee;
use App\Models\Unit;
use App\Models\Event;
use App\Models\Hero;
use App\Models\User;
use App\Models\HeroReward;
use App\Models\HeroPhoto;
use App\Models\HeroVideo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use PhotoService;

class HeroController extends Controller
{
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
        
        $params = [
            'site' => $site,
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'file_ext' => $file_ext? implode(', ', $file_ext) : 'любые',
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            'employees_search' => $employees_search,
            'units_search' => $units_search,
            'events_search' => $events_search,
        ];

        return view('hero', $params);
    }

    public function add_hero(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['heroAdmin'] != null && time() <= strtotime(Auth::user()->rights['heroAdmin'].' 23:59:59'))){
            $admin = true;
        }
        
        //Если лимит превышен
        if(!$admin){
            if($user->limits['heroLimit'] <= 0){
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

            if(Hero::where([
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
                        $errors[] = "heroImg";
                    }
                }
                if((filesize($request->file("image")) < $photo_size * 1024) != 1){
                    $errors[] = "heroImg";
                }
            }

            if(!trim($request->input("firstName"))) $errors[] = "firstName";
            if(!trim($request->input("lastName"))) $errors[] = "lastName";
            if($request->input("secondName") && !trim($request->input("secondName")))$errors[] = "secondName";
            if($request->input("dateBirthday") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("dateBirthday"))) $errors[] = "dateBirthday";

            #Проверяем информацию о наградах
            if($request->input("reward")){
                $rewards = json_decode($request->input("reward"), true);
                
                foreach($rewards as $reward){

                    if(Str::of($reward["id"])->trim()->isEmpty()) continue;
                    if(Str::of($reward["reward"])->trim()->isEmpty()) $errors[] = "reward_" . $reward["id"];
                    if($reward["rewardDate"] && Str::of($reward["rewardDate"])->trim()->isEmpty()) $errors[] = "rewardDate_" . $reward["id"];
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

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $newHero = new Hero;
                    
                    #Дописать удаление фотографии в случае не удачно транзакции
                    if($request->file("image"))
                    {
                        $file_path = PhotoService::resize($request, 'image', 'uploads/hero/personal', 800);
                        $newHero->img = $file_path;
                    }

                    if(Str::of($request->input("firstName"))->trim()->isNotEmpty()) $newHero->firstName = trim($request->input("firstName"));
                    if(Str::of($request->input("lastName"))->trim()->isNotEmpty()) $newHero->lastName = trim($request->input("lastName"));
                    if(Str::of($request->input("secondName"))->trim()->isNotEmpty()) $newHero->secondName = trim($request->input("secondName"));
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newHero->description = trim($request->input("description"));
                    if(Str::of($request->input("dateBirthday"))->trim()->isNotEmpty()) $newHero->dateBirthday = trim($request->input("dateBirthday"));

                    #Запись персональных данных
                    $newHero->addUserId = Auth::user()->id;
                    $newHero->save();

                    if($request->input("reward")){
                        $rewards = json_decode($request->input("reward"), true);

                        foreach($rewards as $reward){
                            $newReward = new HeroReward;
                            $newReward->hero_id = $newHero->id;
                            $newReward->reward = trim($reward["reward"]);
                            if(Str::of($reward["rewardDate"])->trim()->isNotEmpty()) $newReward->rewardDate = trim($reward["rewardDate"]);
                            $newReward->save();
                        }
                    }

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);
            
                        $photoCountData = 0;
            
                        foreach($photos as $photo){
            
                            $reqPhotoName = "photo_" . $photoCountData;
            
                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/hero/photo', 2300);
            
                            $newPhoto = new HeroPhoto;
                            $newPhoto->hero_id = $newHero->id;
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
                            $newVideo = new HeroVideo;
                            $newVideo->hero_id = $newHero->id;
                            preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video['video'], $matches);
                            $newVideo->video = $matches[1];
                            if(Str::of($video["videoDate"])->trim()->isNotEmpty()) $newVideo->videoDate = trim($video["videoDate"]);
                            if(Str::of($video["videoName"])->trim()->isNotEmpty()) $newVideo->videoName = trim($video["videoName"]);
                            $newVideo->save();
                            $videoCountData++;
                        }
                    }
                });
                #Проверка успешно ли прошла транзакция
                if($exception){
                    $response['success'] = false;
                }else{
                    if(!$admin){
                        if($user->limits->heroLimit > 0){
                            $user->limits->update(["heroLimit" => $user->limits->heroLimit - 1]);
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

    public function heroes_list(Request $request, $site = null){

        $filter = [];

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['heroAdmin'] != null && time() <= strtotime(Auth::user()->rights['heroAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            $filter[] = ['addUserId', Auth::user()->id];
        }

        $next_query = [
            'lastName' => '',
            'firstName' => '',
            'secondName' => '',
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
        if($request->input("dateBirthdayFrom") != null){
            $filter[] = ["dateBirthday", ">=", $request->input("dateBirthdayFrom")];
            $next_query['dateBirthdayFrom'] = $request->input("dateBirthdayFrom");
        }
        if($request->input("dateBirthdayTo") != null){
            $filter[] = ["dateBirthday", "<=", $request->input("dateBirthdayTo")];
            $next_query['dateBirthdayTo'] = $request->input("dateBirthdayTo");
        }

        $heroes = Hero::where($filter)->orderBy("lastName")->paginate(50);

        return view('heroesList', [
            'heroes' => $heroes,
            'next_query' => $next_query,
            'site' => $site,
            'admin' => $admin
        ]);
    }

    public function delete_hero(Request $request){
        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['heroAdmin'] != null && time() <= strtotime(Auth::user()->rights['heroAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            return;
        }

        $hero = Hero::where('id', $request->input('id'))->first();

        if($hero->exists()){
            Storage::disk('public')->delete($hero->img);

            foreach($hero->photos as $photo){
                Storage::disk('public')->delete($photo->photo);
            }

            $hero->delete();

            Log::channel('hero')->info('Delete hero', [
                'who_id' => Auth::user()->id,
                'who_email' => Auth::user()->email,
                'who_name' => isset(Auth::user()->name)? Auth::user()->name : '',
                'hero_id' => $hero->id,
                'hero_first_name' => $hero->firstName,
                'hero_last_name' => $hero->lastName,
                'hero_second_name' => isset($hero->secondName)? $hero->secondName : '',
            ]);
        }
        
        return true;
    }

    public function edit_hero($site = null, $id = null){
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
            $heroParams = [
                ['id', $id],
            ];

            if(Auth::user()->rights['root'] || (Auth::user()->rights['heroAdmin'] != null && time() <= strtotime(Auth::user()->rights['heroAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if(!$admin){
                $heroParams[] = ['addUserId', Auth::user()->id];
            }

            $hero = Hero::where($heroParams);
            
            if($hero->exists()){     
                $params['id'] = $id;
                $params['hero'] = $hero->get()->first();
                $params['addUser'] = $hero->get()->first()->addUserId;
                $params['user'] = $hero->first()->user;
                $params['admin'] = $admin;
            }else{
                return redirect(route('heroes_list'));
            }
        }

        return view('heroEdit', $params);
    }

    public function update_hero(Request $request){

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

            $heroParams = [
                ['id', $request->input("id")],
            ];

            if(Auth::user()->rights['root'] || (Auth::user()->rights['heroAdmin'] != null && time() <= strtotime(Auth::user()->rights['heroAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if(!$admin){
                $heroParams[] = ['addUserId', Auth::user()->id];
            }

            if(!$request->input("id") ||  !Hero::where($heroParams)->exists())
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
                        $errors[] = "heroImg";
                    }
                }
                if((filesize($request->file("image")) < $photo_size * 1024) != 1){
                    $errors[] = "heroImg";
                }
            }

            if(!trim($request->input("firstName"))) $errors[] = "firstName";
            if(!trim($request->input("lastName"))) $errors[] = "lastName";
            if($request->input("secondName") && !trim($request->input("secondName")))$errors[] = "secondName";
            if($request->input("dateBirthday") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("dateBirthday"))) $errors[] = "dateBirthday";

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
                    $photoTmp = HeroPhoto::where('id', $photo);

                    if($photoTmp->exists()){

                        $noRight = true;

                        if($photoTmp->first()->hero->addUserId == Auth::user()->id){
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
                    $videoTmp = HeroVideo::where('id', $video);

                    if($videoTmp->exists()){

                        $noRight = true;

                        if($videoTmp->first()->hero->addUserId == Auth::user()->id){
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

            #Проверяем информацию о наградах
            if($request->input("reward")){
                $rewards = json_decode($request->input("reward"), true);
                
                foreach($rewards as $reward){

                    if(Str::of($reward["id"])->trim()->isEmpty()) continue;
                    if(Str::of($reward["reward"])->trim()->isEmpty()) $errors[] = "reward_" . $reward["id"];
                    if($reward["rewardDate"] && Str::of($reward["rewardDate"])->trim()->isEmpty()) $errors[] = "rewardDate_" . $reward["id"];
                }
            }

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editHero = Hero::where("id", $request->input("id"));
                    $newHeroInfo = [];

                    if($request->input('addUserId')){
                        if($request->input('addUserId') == 'no'){
                            $newHeroInfo["addUserId"] = null;
                        }else{
                            $newHeroInfo["addUserId"] = $request->input('addUserId');
                        }
                    }
                    
                    #Дописать удаление фотографии в случае не удачно транзакции
                    if($request->input("deleteImg")){
                        Storage::disk('public')->delete($editHero->first()->img);
                        $newHeroInfo['img'] = null;
                    }

                    if($request->file("image"))
                    {
                        Storage::disk('public')->delete($editHero->first()->img);
                        $file_path = PhotoService::resize($request, 'image', 'uploads/hero/personal', 800);
                        $newHeroInfo['img'] = $file_path;
                    }
                    
                    if(Str::of($request->input("firstName"))->trim()->isNotEmpty()) $newHeroInfo['firstName'] = trim($request->input("firstName"));
                    if(Str::of($request->input("lastName"))->trim()->isNotEmpty()) $newHeroInfo['lastName'] = trim($request->input("lastName"));

                    $newHeroInfo['secondName'] = trim($request->input("secondName"));
                    $newHeroInfo['description'] = trim($request->input("description"));
                    
                    if($request->input("dateBirthday")) $newHeroInfo['dateBirthday'] = trim($request->input("dateBirthday"));

                    #Запись персональных данных
                    $editHero->update($newHeroInfo);

                    if($request->input("photoToDelete")){
                        $photoToDelete = explode(',',$request->input("photoToDelete"));

                        #Сохраняем каждую запись о образовании
                        foreach($photoToDelete as $index => $photo){
                            $oldPhoto = HeroPhoto::where('id', $photo);
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

                            $oldVideo = HeroVideo::where('id', $video);
                            if($oldVideo->exists()){
                                $oldVideo->delete();
                            }
                        }
                    }

                    if($request->input("reward")){
                        $rewards = json_decode($request->input("reward"), true);

                        HeroReward::where('hero_id', $editHero->first()->id)->delete();

                        foreach($rewards as $reward){
                            $newReward = new HeroReward;
                            $newReward->hero_id = $editHero->first()->id;
                            $newReward->reward = trim($reward["reward"]);
                            if(Str::of($reward["rewardDate"])->trim()->isNotEmpty()) $newReward->rewardDate = trim($reward["rewardDate"]);
                            $newReward->save();
                        }
                    }

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){

                            $reqPhotoName = "photo_" . $photoCountData;

                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/emp/photo', 2300);

                            $newPhoto = new HeroPhoto;
                            $newPhoto->hero_id = $editHero->first()->id;
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
                            $newVideo = new HeroVideo;
                            $newVideo->hero_id = $editHero->first()->id;
                            preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video['video'], $matches);
                            $newVideo->video = $matches[1];
                            if(Str::of($video["videoDate"])->trim()->isNotEmpty()) $newVideo->videoDate = trim($video["videoDate"]);
                            if(Str::of($video["videoName"])->trim()->isNotEmpty()) $newVideo->videoName = trim($video["videoName"]);
                            $newVideo->save();
                            $videoCountData++;
                        }
                    }
                });

            $hero = Hero::where("id", $request->input("id"))->first();

            $response['photos'] = view('ajax.heroPhotos', [
                'hero' => $hero
            ])->render();

            $response['videos'] = view('ajax.heroVideos', [
                'hero' => $hero
            ])->render();

            $response['imgHero'] = view('ajax.imgHero', [
                'hero' => $hero
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