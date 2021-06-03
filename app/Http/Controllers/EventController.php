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

class EventController extends Controller
{
    public function index(){
        $file_size = FileSize::where('name', 'file')->exists()? FileSize::where('name', 'file')->first()['size'] : 0;
        $photo_size = FileSize::where('name', 'photo')->exists()? FileSize::where('name', 'photo')->first()['size'] : 0;

        $photo_ext = FileExt::where('name', 'photo')->exists() && FileExt::where('name', 'photo')->first()['ext'] ? explode(', ', FileExt::where('name', 'photo')->first()['ext']) : null;
        $file_ext = FileExt::where('name', 'file')->exists() && FileExt::where('name', 'file')->first()['ext'] ? explode(', ', FileExt::where('name', 'file')->first()['ext']) : null;

        $counter = Event::where('addUserId', Auth::user()->id)->get()->count();

        $params = [
            'counter' => $counter,
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'file_ext' => $file_ext? implode(', ', $file_ext) : 'любые',
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
        ];

        return view('event', $params);
    }

    public function events_list(Request $request){

        $counter = Event::where('addUserId', Auth::user()->id)->get()->count();

        $filter = [
            ['addUserId', Auth::user()->id]
        ];
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
            'counter' => $counter
        ]);
    }

    public function add_event(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        //Если лимит превышен
        if($user['eventLimit'] <= 0){
            $response['errors'][] = 'limit'; 
            return $response;
        }

        if(isset($request)){
            $file_size = FileSize::where('name', 'file')->exists()? FileSize::where('name', 'file')->first()['size'] : 0;
            $photo_size = FileSize::where('name', 'photo')->exists()? FileSize::where('name', 'photo')->first()['size'] : 0;

            $photo_ext = FileExt::where('name', 'photo')->exists() && FileExt::where('name', 'photo')->first()['ext'] ? explode(', ', FileExt::where('name', 'photo')->first()['ext']) : null;
            $file_ext = FileExt::where('name', 'file')->exists() && FileExt::where('name', 'file')->first()['ext'] ? explode(', ', FileExt::where('name', 'file')->first()['ext']) : null;

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
                    if(Str::of($request->input("terminationDate"))->trim()->isNotEmpty()) $newUnit->terminationDate = trim($request->input("terminationDate"));
                    #Запись персональных данных
                    $newEvent->addUserId = Auth::user()->id;
                    $newEvent->save();

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){
                            $photoPath = $request->file("photo_" . $photoCountData)->store('uploads/event/photo', 'public');
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
                if($user['unitLimit'] > 0){
                    $user->unitLimit = $user['unitLimit'] - 1;
                    $user->save();
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
}
