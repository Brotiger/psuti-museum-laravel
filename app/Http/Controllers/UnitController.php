<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\UnitPhoto;
use App\Models\UnitVideo;
use App\Models\FileSize;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UnitController extends Controller
{
    public function edit_unit($id = null){
        $file_size = FileSize::where('name', 'file')->exists()? FileSize::where('name', 'file')->first()['size'] : 0;
        $photo_size = FileSize::where('name', 'photo')->exists()? FileSize::where('name', 'photo')->first()['size'] : 0;
        $video_size = FileSize::where('name', 'video')->exists()? FileSize::where('name', 'video')->first()['size'] : 0;

        $counter = Unit::where('addUserId', Auth::user()->id)->get()->count();
        $params = [
            'counter' => $counter,
            'id' => $id,
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'video_size' => $video_size
        ];
        if(isset($id)){
            $unit = Unit::where([
                ['id', $id],
                ['addUserId', Auth::user()->id]
            ]);
            if($unit->exists()){
                $params['id'] = $id;
                $params['unit'] = $unit->get()->first();
            }else{
                return redirect(route('units_list'));
            }
        }

        return view('unitsEdit', $params);
    }

    public function units_list(Request $request){
        $filter = [
            ['addUserId', Auth::user()->id]
        ];
        if($request->input("fullUnitName") != null) $filter[] = ["fullUnitName", "like", '%' . $request->input("fullUnitName") . '%'];
        if($request->input("shortUnitName") != null) $filter[] = ["shortUnitName", "like", '%' . $request->input("shortUnitName") . '%'];
        if($request->input("typeUnit") != null) $filter[] = ["typeUnit", "like", '%' . $request->input("typeUnit") . '%'];
        if($request->input("creationDateFrom") != null) $filter[] = ["creationDate", ">=", $request->input("creationDateFrom")];
        if($request->input("creationDateTo") != null) $filter[] = ["creationDate", "<", $request->input("creationDateTo")];
        if($request->input("terminationDateFrom") != null) $filter[] = ["terminationDate", ">=", $request->input("terminationDateFrom")];
        if($request->input("terminationDateTo") != null) $filter[] = ["terminationDate", "<", $request->input("terminationDateTo")];

        $units = Unit::where($filter)->orderBy("fullUnitName")->get();
        $counter = Unit::where('addUserId', Auth::user()->id)->get()->count();

        if($request->ajax()){
            return view('filters.unitsList', [
                'units' => $units
            ])->render();
        }

        return view('unitsList', [
            'units' => $units,
            'counter' => $counter
        ]);
    }

    public function index(){
        $file_size = FileSize::where('name', 'file')->exists()? FileSize::where('name', 'file')->first()['size'] : 0;
        $photo_size = FileSize::where('name', 'photo')->exists()? FileSize::where('name', 'photo')->first()['size'] : 0;
        $video_size = FileSize::where('name', 'video')->exists()? FileSize::where('name', 'video')->first()['size'] : 0;

        $counter = Unit::where('addUserId', Auth::user()->id)->get()->count();
        return view('units', [
            'counter' => $counter,
            'file_size' => $file_size,
            'photo_size' => $photo_size,
            'video_size' => $video_size
        ]);
    }

    public function add_unit(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        //Если лимит превышен
        if($user['unitLimit'] <= 0){
            $response['errors'][] = 'limit'; 
            return $response;
        }

        if(isset($request)){
            $file_size = FileSize::where('name', 'file')->exists()? FileSize::where('name', 'file')->first()['size'] : 0;
            $photo_size = FileSize::where('name', 'photo')->exists()? FileSize::where('name', 'photo')->first()['size'] : 0;
            $video_size = FileSize::where('name', 'video')->exists()? FileSize::where('name', 'video')->first()['size'] : 0;

            if(!trim($request->input("fullUnitName")) ||  Unit::where('fullUnitName', $request->input("fullUnitName"))->exists()) $errors[] = "fullUnitName";
            if($request->input("shortUnitName") && Str::of($request->input("shortUnitName"))->trim()->isEmpty()) $errors[] = "shortUnitName";
            if($request->input("unitType") && Str::of($request->input("unitType"))->trim()->isEmpty()) $errors[] = "unitType";
            if($request->input("creationDate") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("creationDate"))) $errors[] = "creationDate";
            if($request->input("terminationDate") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("terminationDate"))) $errors[] = "terminationDate";

            if($request->input("photo")){
                $photos = json_decode($request->input("photo"), true);
                
                $photoCountCheck = 0;
                foreach($photos as $photo){

                    if(Str::of($photo["id"])->trim()->isEmpty()) continue;
                    if(!$request->file("photo_" . $photoCountCheck) || (filesize($request->file("photo_" . $photoCountCheck)) < $photo_size * 1024) != 1) $errors[] = "photo_" . $photo["id"];
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
                    if(!$request->file("video_" . $videoCountCheck) || (filesize($request->file("video_" . $videoCountCheck)) < $video_size * 1024) != 1) $errors[] = "video_" . $video["id"];
                    if($video["videoName"] && Str::of($video["videoName"])->trim()->isEmpty()) $errors[] = "videoName_" . $video["id"];
                    if($video["videoDate"] && Str::of($video["videoDate"])->trim()->isEmpty()) $errors[] = "videoDate_" . $video["id"];
                    $videoCountCheck++;
                }
            }


            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $newUnit = new Unit;

                    if(Str::of($request->input("fullUnitName"))->trim()->isNotEmpty()) $newUnit->fullUnitName = trim($request->input("fullUnitName"));
                    if(Str::of($request->input("shortUnitName"))->trim()->isNotEmpty()) $newUnit->shortUnitName = trim($request->input("shortUnitName"));
                    if(Str::of($request->input("typeUnit"))->trim()->isNotEmpty()) $newUnit->typeUnit = trim($request->input("typeUnit"));
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newUnit->description = trim($request->input("description"));
                    if(Str::of($request->input("creationDate"))->trim()->isNotEmpty()) $newUnit->creationDate = trim($request->input("creationDate"));
                    if(Str::of($request->input("terminationDate"))->trim()->isNotEmpty()) $newUnit->terminationDate = trim($request->input("terminationDate"));
                    #Запись персональных данных
                    $newUnit->addUserId = Auth::user()->id;
                    $newUnit->save();

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){
                            $photoPath = $request->file("photo_" . $photoCountData)->store('uploads/unit/photo', 'public');
                            $newPhoto = new UnitPhoto;
                            $newPhoto->unit_id = $newUnit->id;
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
                            $videoPath = $request->file("video_" . $videoCountData)->store('uploads/unit/video', 'public');
                            $newVideo = new UnitVideo;
                            $newVideo->unit_id = $newUnit->id;
                            $newVideo->video = $videoPath;
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

    public function update_unit(Request $request){
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $errors = [];

        $user = User::where("id", Auth::user()->id)->get()->first();

        if(isset($request)){
            if(!$request->input("id") ||  !Unit::where([
                ['addUserId', Auth::user()->id],
                ['id', $request->input("id")],
                ])->exists())
            {
                return redirect(route('units_list'));
            }
                
            if(!trim($request->input("fullUnitName"))) $errors[] = "fullUnitName";
            if($request->input("shortUnitName") && Str::of($request->input("shortUnitName"))->trim()->isEmpty()) $errors[] = "shortUnitName";
            if($request->input("unitType") && Str::of($request->input("unitType"))->trim()->isEmpty()) $errors[] = "unitType";
            if($request->input("creationDate") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("creationDate"))) $errors[] = "creationDate";
            if($request->input("terminationDate") && !preg_match('~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~', $request->input("terminationDate"))) $errors[] = "terminationDate";

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editUnit = Unit::where("id", $request->input("id"));
                    $newUnitInfo = [];

                    if(Str::of($request->input("fullUnitName"))->trim()->isNotEmpty()) $newUnitInfo['fullUnitName'] = trim($request->input("fullUnitName"));
                    if(Str::of($request->input("shortUnitName"))->trim()->isNotEmpty()) $newUnitInfo['shortUnitName'] = trim($request->input("shortUnitName"));
                    if(Str::of($request->input("typeUnit"))->trim()->isNotEmpty()) $newUnitInfo['typeUnit'] = trim($request->input("typeUnit"));
                    if(Str::of($request->input("description"))->trim()->isNotEmpty()) $newUnitInfo['description'] = trim($request->input("description"));
                    if(Str::of($request->input("creationDate"))->trim()->isNotEmpty()) $newUnitInfo['creationDate'] = trim($request->input("creationDate"));
                    if(Str::of($request->input("terminationDate"))->trim()->isNotEmpty()) $newUnitInfo['terminationDate'] = trim($request->input("terminationDate"));

                    $editUnit->update($newUnitInfo);
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
