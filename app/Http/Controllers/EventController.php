<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\FileSize;
use App\Models\FileExt;
use App\Models\User;
use App\Models\EventPhoto;
use App\Models\EventVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

use PhotoService;

class EventController extends Controller
{
    public function search_event(Request $request){
        $filter = [];

        if($request->input("name") != null){
            $filter[] = ["name", "like", '%' . $request->input("name") . '%'];
        }

        if($request->input("dateFrom") != null){
            $filter[] = ["date", ">=", $request->input("dateFrom")];
        }
        if($request->input("dateTo") != null){
            $filter[] = ["date", "<", $request->input("dateTo")];
        }

        $events_search = Event::where($filter)->orderBy('name')->limit(15)->get();

        return view('ajax.searchEvent', [
            'events_search' => $events_search
        ])->render();
    }

    public function edit_event($id = null){
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

        $users_search = User::where([['id', '<>', Auth::user()->id]])->orderBy('name')->limit(15)->get();

        $params = [
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
            $eventParams = [
                ['id', $id],
            ];

            $admin = false;

            if(Auth::user()->rights['root'] || (Auth::user()->rights['eventAdmin'] != null && time() <= strtotime(Auth::user()->rights['eventAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if(!$admin){
                $eventParams[] = ['addUserId', Auth::user()->id];
            }

            $event = Event::where($eventParams);

            if($event->exists()){
                $params['id'] = $id;
                $params['event'] = $event->get()->first();
                $params['addUser'] = $event->get()->first()->addUserId;
                $params['admin'] = $admin;
                $params['user'] = $event->first()->user;
            }else{
                return redirect(route('events_list'));
            }
        }

        return view('eventsEdit', $params);
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

        $params = [
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'file_ext' => $file_ext? implode(', ', $file_ext) : 'любые',
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            'employees_search' => $employees_search,
            'units_search' => $units_search,
            'events_search' => $events_search,
            'site' => $site,
        ];

        return view('event', $params);
    }

    public function events_list(Request $request){

        $filter = [];

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['eventAdmin'] != null && time() <= strtotime(Auth::user()->rights['eventAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            $filter[] = ['addUserId', Auth::user()->id];
        }

        $next_query = [
            'name' => '',
            'dateFrom' => '',
            'dateTo' => '',
        ];

        if($request->input("name") != null){
            $filter[] = ["name", "like", '%' . $request->input("name") . '%'];
            $next_query['name'] = $request->input("name");
        }
        if($request->input("dateFrom") != null){
            $filter[] = ["date", ">=", $request->input("dateFrom")];
            $next_query['dateFrom'] = $request->input("dateFrom");
        }
        if($request->input("dateTo") != null){
            $filter[] = ["date", "<", $request->input("dateTo")];
            $next_query['dateTo'] = $request->input("dateTo");
        }

        $events = Event::where($filter)->orderBy("name")->paginate(50);

        return view('eventsList', [
            'events' => $events,
            'next_query' => $next_query,
            'site' => env('DB_SITE', 'pguty'),
            'admin' => $admin
        ]);
    }

    public function add_event(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['eventAdmin'] != null && time() <= strtotime(Auth::user()->rights['eventAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            if($user->limits['eventLimit'] <= 0){
                $response['errors'][] = 'limit'; 
                return $response;
            }
        }

        if(isset($request)){
            $file_size = env('FILE_SIZE', 0);
            $photo_size = env('IMG_SIZE', 0);

            $photo_ext = env('IMG_EXT', null);
            if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

            $file_ext = env('FILE_EXT', null);
            if($file_ext != null) $file_ext = explode(',', $file_ext);

            if(!trim($request->input("name")) ||  Event::where('name', $request->input("name"))->exists()) $errors[] = "name";
            if($request->input("date") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("date"))) $errors[] = "date";

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
                        $extError = true;

                        $ext = $request->file('photo_'.$photoCountCheck)->getClientOriginalExtension();
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
                    $newEvent = new Event;

                    if(Str::of($request->input("name"))->trim()->isNotEmpty()) $newEvent->name = trim($request->input("name"));
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newEvent->description = trim($request->input("description"));
                    if(Str::of($request->input("date"))->trim()->isNotEmpty()) $newEvent->date = trim($request->input("date"));
        
                    #Запись персональных данных
                    $newEvent->addUserId = Auth::user()->id;
                    $newEvent->save();

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){
                            $reqPhotoName = "photo_" . $photoCountData;

                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/event/photo', 2300);
                            
                            $newPhoto = new EventPhoto;
                            $newPhoto->event_id = $newEvent->id;
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
                            $newVideo = new EventVideo;
                            $newVideo->event_id = $newEvent->id;
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
                    if($user->limits['eventLimit'] > 0){
                        $user->limits->eventLimit = $user->limits['eventLimit'] - 1;
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

    public function delete_event(Request $request){
        $admin = false;

        if(Auth::user()->rights['root'] || (Auth::user()->rights['empAdmin'] != null && time() <= strtotime(Auth::user()->rights['empAdmin'].' 23:59:59'))){
            $admin = true;
        }

        if(!$admin){
            return;
        }

        $event = Event::where('id', $request->input('id'))->first();

        if($event->exists()){

            foreach($event->photos as $photo){
                Storage::disk('public')->delete($photo->photo);
            }

            $event->delete();

            Log::channel('event')->info('Delete event', [
                'who_id' => Auth::user()->id,
                'who_email' => Auth::user()->email,
                'who_name' => isset(Auth::user()->name)? Auth::user()->name : '',
                'event_id' => $event->id,
                'event_name' => $event->name,
            ]);
        }
        
        return true;
    }

    public function update_event(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $file_size = env('FILE_SIZE', 0);
        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $file_ext = env('FILE_EXT', null);
        if($file_ext != null) $file_ext = explode(',', $file_ext);

        $user = User::where("id", Auth::user()->id)->get()->first();

        if(isset($request)){
            $eventParams = [
                'id' => $request->input("id")
            ];

            $admin = false;

            if(Auth::user()->rights['root'] || (Auth::user()->rights['eventAdmin'] != null && time() <= strtotime(Auth::user()->rights['eventAdmin'].' 23:59:59'))){
                $admin = true;
            }

            if($request->input('addUserId')){
                if(!$admin) return;
                if($request->input('addUserId') != 'no'){
                    if($request->input('addUserId') == Auth::user()->id) return;
                }
            }

            if(!$admin){
                $eventParams[] = ['addUserId', Auth::user()->id];
            }

            if(!$request->input("id") ||  !Event::where($eventParams)->exists())  
            {
                return redirect(route('events_list'));
            }
                
            if(!trim($request->input("name"))) $errors[] = "name";
            if($request->input("date") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("date"))) $errors[] = "date";

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
                    $photoTmp = EventPhoto::where('id', $photo);

                    if($photoTmp->exists()){

                        $noRight = true;

                        if($photoTmp->first()->event->addUserId == Auth::user()->id){
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
                    $videoTmp = EventVideo::where('id', $video);

                    if($videoTmp->exists()){

                        $noRight = true;

                        if($videoTmp->first()->event->addUserId == Auth::user()->id){
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

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editEvent = Event::where("id", $request->input("id"));
                    $newEventInfo = [];

                    if(Str::of($request->input("name"))->trim()->isNotEmpty()) $newEventInfo['name'] = trim($request->input("name"));;
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newEventInfo['description'] = trim($request->input("description"));
                    if(Str::of($request->input("date"))->trim()->isNotEmpty()) $newEventInfo['date'] = trim($request->input("date"));

                    if($request->input('addUserId')){
                        if($request->input('addUserId') == 'no'){
                            $newEventInfo["addUserId"] = null;
                        }else{
                            $newEventInfo["addUserId"] = $request->input('addUserId');
                        }
                    }

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){
                            
                            $reqPhotoName = "photo_" . $photoCountData;

                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/event/photo', 2300);

                            $newPhoto = new EventPhoto;
                            $newPhoto->event_id = $editEvent->first()->id;
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
                            $newVideo = new EventVideo;
                            $newVideo->event_id = $editEvent->first()->id;
                            preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video['video'], $matches);
                            $newVideo->video = $matches[1];
                            if(Str::of($video["videoDate"])->trim()->isNotEmpty()) $newVideo->videoDate = trim($video["videoDate"]);
                            if(Str::of($video["videoName"])->trim()->isNotEmpty()) $newVideo->videoName = trim($video["videoName"]);
                            $newVideo->save();
                            $videoCountData++;
                        }
                    }

                    if($request->input("photoToDelete")){
                        $photoToDelete = explode(',',$request->input("photoToDelete"));

                        #Сохраняем каждую запись о образовании
                        foreach($photoToDelete as $index => $photo){
                            $oldPhoto = EventPhoto::where('id', $photo);
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
                            $oldVideo = EventVideo::where('id', $video);
                            if($oldVideo->exists()){
                                $oldVideo->delete();
                            }
                        }
                    }

                    $editEvent->update($newEventInfo);
                });

            $event = Event::where("id", $request->input("id"))->first();

            $response['photos'] = view('ajax.eventPhotos', [
                'event' => $event
            ])->render();

            $response['videos'] = view('ajax.eventVideos', [
                'event' => $event
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