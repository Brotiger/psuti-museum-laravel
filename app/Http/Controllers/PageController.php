<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Employee;
use App\Models\Unit;
use App\Models\History;
use App\Models\Event;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ArchiveVideo;
use App\Models\ArchivePhoto;

use PhotoService;

class PageController extends Controller
{
    public function pages_list(Request $request){

        $filter = [];

        $next_query = [
            'title' => '',
        ];

        if($request->input("title") != null){
            $filter[] = ["title", "like", '%' . $request->input("title") . '%'];
            $next_query['title'] = $request->input("title");
        }

        $pages = Page::where($filter)->orderBy("title")->paginate(50);

        return view('pagesList', [
            'pages' => $pages,
            'next_query' => $next_query,
        ]);
    }

    public function edit_page($alias = null){
        $site = 'pguty';

        $access = false;

        $employees_search = Employee::orderBy('lastName')->limit(15)->get();
        $units_search = Unit::orderBy('fullUnitName')->limit(15)->get();
        $events_search = Event::orderBy('name')->limit(15)->get();

        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $me = User::where("id", Auth::user()->id)->get()->first();

        $users_search = User::where([['id', '<>', Auth::user()->id]])->orderBy('name')->limit(15)->get();

        $params = [
            'alias' => $alias,
            'photo_size' => $photo_size,
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            'employees_search' => $employees_search,
            'units_search' => $units_search,
            'events_search' => $events_search,
            'site' => $site,
            'me' => $me,
            'users_search' => $users_search
        ];

        if(isset($alias)){
            $admin = false;

            if(Auth::user()->rights['root'] || (Auth::user()->rights['pageAdmin'] != null && time() <= strtotime(Auth::user()->rights['pageAdmin'].' 23:59:59'))){
                $admin = true;
            }

            $page = Page::where([
                ['alias', $alias],
            ]);

            if($page->exists()){

                if($page->first()->addUserId == Auth::user()->id || Auth::user()->rights['root'] || (Auth::user()->rights['pageAdmin'] != null && time() <= strtotime(Auth::user()->rights['pageAdmin'].' 23:59:59'))){
                    $access = true;
                }

                $params['access'] = $access;
                $params['alias'] = $alias;
                $params['page'] = $page->get()->first();
                $params['addUser'] = $page->get()->first()->addUserId;
                $params['user'] = $page->first()->user;
                $params['admin'] = $admin;
            }else{
                return redirect(route('pages_list'));
            }
        }

        return view('pagesEdit', $params);
    }

    public function update_page(Request $request){
        
        $response = [
            "errors" => false,
            "success" => false 
        ];

        $access = false;
        $admin = false;

        $errors = [];

        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $user = User::where("id", Auth::user()->id)->get()->first();
        
        if(isset($request)){

            if (!$request->input("alias")){
                return redirect(route('pages_list'));
            }

            $page = Page::where([
                ['alias', $request->input("alias")],
            ]);

            if(!$page->exists())
            {
                return redirect(route('pages_list'));
            }

            if($page->first()->addUserId == Auth::user()->id || Auth::user()->rights['root'] || (Auth::user()->rights['pageAdmin'] != null && time() <= strtotime(Auth::user()->rights['pageAdmin'].' 23:59:59'))){
                $access = true;
            }

            if(Auth::user()->rights['root'] || (Auth::user()->rights['pageAdmin'] != null && time() <= strtotime(Auth::user()->rights['pageAdmin'].' 23:59:59'))){
                $admin = true; //для проверки права удаления коментрариев
            }

            if($request->input('addUserId')){
                if(!$admin) return;
                if($request->input('addUserId') != 'no'){
                    if($request->input('addUserId') == Auth::user()->id) return;
                }
            }
            
            if($request->input("post")){
                $posts = json_decode($request->input("post"), true);

                if($posts && !$access) return;
                
                foreach($posts as $post){
                    if(Str::of($post["id"])->trim()->isEmpty()) continue;
                    if($request->file("post_" . $post["id"])){
                        if((filesize($request->file("post_" . $post["id"])) < $photo_size * 1024) != 1){
                            $errors[] = "post_" . $post["id"];
                            continue;
                        }

                        if(!is_null($photo_ext)){
                            $ext = $request->file('post_'.$post["id"])->getClientOriginalExtension();
                            $extError = true;
                            foreach($photo_ext as $value){
                                if($ext == $value){
                                    $extError = false;
                                }
                            }

                            if($extError){
                                $errors[] = "post_" . $post["id"];
                            }
                        }
                    }

                    if($post["title"] && Str::of($post["title"])->trim()->isEmpty()) $errors[] = "title_" . $post["id"];
                    if(Str::of($post["description"])->trim()->isEmpty()) $errors[] = "description_" . $post["id"];
                }
            }

            if($request->input("photo")){
                $photos = json_decode($request->input("photo"), true);

                if($photos && !$access) return;
                
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

                if($videos && !$access) return;
                
                $videoCountCheck = 0;
                foreach($videos as $video){
                    if(Str::of($video["id"])->trim()->isEmpty()) continue;
                    if($video["videoName"] && Str::of($video["videoName"])->trim()->isEmpty()) $errors[] = "videoName_" . $video["id"];
                    if($video["videoDate"] && Str::of($video["videoDate"])->trim()->isEmpty()) $errors[] = "videoDate_" . $video["id"];
                    if(!$video["video"] || Str::of($video["video"])->trim()->isEmpty() || !preg_match('~^https:\/\/www.youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)~', $video["video"])) $errors[] = "video_" . $video["id"];
                    $videoCountCheck++;
                }
            }

            if($request->input("postUpdate")){
                $posts = json_decode($request->input("postUpdate"), true);

                if($posts && !$access) return;
                
                foreach($posts as $post){
                    if(Str::of($post["id"])->trim()->isEmpty()) continue;
                    if($request->file("post_" . $post['id'])){
                        if((filesize($request->file("post_" . $post['id'])) < $photo_size * 1024) != 1){
                            $errors[] = "post_" . $post["id"];
                            continue;
                        }

                        if(!is_null($photo_ext)){
                            $ext = $request->file('post_'.$post['id'])->getClientOriginalExtension();
                            $extError = true;
                            foreach($photo_ext as $value){
                                if($ext == $value){
                                    $extError = false;
                                }
                            }

                            if($extError){
                                $errors[] = "post_" . $post["id"];
                            }
                        }
                    }

                    if($post["title"] && Str::of($post["title"])->trim()->isEmpty()) $errors[] = "title_" . $post["id"];
                    if(Str::of($post["description"])->trim()->isEmpty()) $errors[] = "description_" . $post["id"];
                }
            }

            if($request->input("history")){
                $histories = json_decode($request->input("history"), true);
                
                foreach($histories as $history){
                    if(Str::of($history["id"])->trim()->isEmpty()) continue;
                    if(Str::of($history["comment"])->trim()->isEmpty()) $errors[] = "comment_" . $history["id"];
                }
            }

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editPage = Page::where("alias", $request->input("alias"));

                    $newPageInfo = [];

                    $access = false;

                    if($editPage->first()->addUserId == Auth::user()->id || Auth::user()->rights['root'] || (Auth::user()->rights['pageAdmin'] != null && time() <= strtotime(Auth::user()->rights['pageAdmin'].' 23:59:59'))){
                        $access = true;
                    }

                    $newPostInfo = [];

                    if($request->input('addUserId')){
                        if($request->input('addUserId') == 'no'){
                            $newPageInfo["addUserId"] = null;
                        }else{
                            $newPageInfo["addUserId"] = $request->input('addUserId');
                        }
                    }

                    $editPage->update($newPageInfo);

                    if($request->input("post")){
                        $posts = json_decode($request->input("post"), true);

                        foreach($posts as $post){
                            
                            $newPost = new Post;
                            $newPost->page_id = $editPage->first()->id;

                            $reqPhotoName = "post_" . $post["id"];

                            if($request->file($reqPhotoName)){
                                $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/page/photo', 2300);
                                $newPost->photo = $photoPath;
                            }
                            if(Str::of($post["title"])->trim()->isNotEmpty()) $newPost->title = trim($post["title"]);
                            if(Str::of($post["description"])->trim()->isNotEmpty()) $newPost->description = trim($post["description"]);
                            $newPost->save();
                        }
                    }

                    if($request->input("history")){
                        $histories = json_decode($request->input("history"), true);

                        foreach($histories as $history){
                            $newHistory = new History;
                            $newHistory->page_id = $editPage->first()->id;
                            $newHistory->addUserId =  Auth::user()->id;
                            $newHistory->comment = trim($history["comment"]);
                            $newHistory->save();
                        }
                    }

                    if($request->input("deletePostPhoto")){
                        $deletePostPhoto = explode(',',$request->input("deletePostPhoto"));

                        if($deletePostPhoto && !$access) return;

                        foreach($deletePostPhoto as $index => $post){
                            $oldPost = Post::where('id', $post);
                            if($oldPost->exists()){
                                if($oldPost->first()->photo != null){
                                    Storage::disk('public')->delete($oldPost->first()->photo);
                                }
                                $oldPost->update([
                                    'photo' => null
                                ]);
                            }
                        }
                    }

                    if($request->input("postUpdate")){
                        $posts = json_decode($request->input("postUpdate"), true);

                        if($posts && !$access) return;

                        foreach($posts as $post){
                            $newPostInfo = [];

                            $updatePost = Post::where("id", $post['record-id']);

                            $reqPhotoName = "post_" . $post["id"];

                            if($request->file($reqPhotoName)){
                                $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/page/photo', 2300);
                                $newPostInfo['photo'] = $photoPath;
                                Storage::disk('public')->delete($updatePost->first()->photo);
                            }
                            $newPostInfo['title'] = trim($post["title"]);
                            if(Str::of($post["description"])->trim()->isNotEmpty()) $newPostInfo['description'] = trim($post["description"]);
                            $updatePost->update($newPostInfo);
                        }
                    }

                    if($request->input("postToDelete")){
                        $postToDelete = explode(',',$request->input("postToDelete"));

                        if($postToDelete && !$access) return;

                        foreach($postToDelete as $index => $post){
                            $oldPost = Post::where('id', $post);
                            if($oldPost->exists()){
                                if($oldPost->first()->photo != null){
                                    Storage::disk('public')->delete($oldPost->first()->photo);
                                }
                                $oldPost->delete();
                            }
                        }
                    }

                    if($request->input("historyToDelete")){
                        $historyToDelete = explode(',',$request->input("historyToDelete"));

                        foreach($historyToDelete as $index => $history){
                            $oldHistory = History::where('id', $history);
                            
                            $noRights = true;

                            if(Auth::user()->id == $oldHistory->first()->addUserId){
                                $noRights = false;
                            }

                            if(Auth::user()->rights['root']){
                                $noRights = false;
                            }

                            if(Auth::user()->rights['pageAdmin'] != null && time() <= strtotime(Auth::user()->rights['pageAdmin'].' 23:59:59')){
                                $noRights = false;
                            }

                            if($noRights){
                                continue;
                            }

                            if($oldHistory->exists()){
                                $oldHistory->delete();
                            }
                        }
                    }

                    if($request->input("photo")){
                        $photos = json_decode($request->input("photo"), true);

                        $photoCountData = 0;

                        foreach($photos as $photo){
                            
                            $reqPhotoName = "photo_" . $photoCountData;

                            $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/page/photo', 2300);

                            $newPhoto = new ArchivePhoto;
                            $newPhoto->page_id = $editPage->first()->id;
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
                            $newVideo = new ArchiveVideo;
                            $newVideo->page_id = $editPage->first()->id;
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

                        if($photoToDelete && !$access) return;

                        #Сохраняем каждую запись о образовании
                        foreach($photoToDelete as $index => $photo){
                            $oldPhoto = ArchivePhoto::where('id', $photo);
                            if($oldPhoto->exists()){
                                Storage::disk('public')->delete($oldPhoto->first()->photo);
                                $oldPhoto->delete();
                            }
                        }
                    }

                    if($request->input("videoToDelete")){
                        $videoToDelete = explode(',',$request->input("videoToDelete"));

                        if($videoToDelete && !$access) return;

                        #Сохраняем каждую запись о образовании
                        foreach($videoToDelete as $index => $video){
                            $oldVideo = ArchiveVideo::where('id', $video);
                            if($oldVideo->exists()){
                                $oldVideo->delete();
                            }
                        }
                    }
                });

            $page = Page::where("alias", $request->input("alias"))->first();

            $response['posts'] = view('ajax.pagePosts', [
                'page' => $page,
                'photo_size' => $photo_size,
                'access' => $access,
                'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            ])->render();

            $response['photos'] = view('ajax.pagePhotos', [
                'access' => $access,
                'page' => $page
            ])->render();

            $response['videos'] = view('ajax.pageVideos', [
                'access' => $access,
                'page' => $page
            ])->render();

            $response['history'] = view('ajax.history', [
                'access' => $access,
                'page' => $page,
                'user' => $user,
                'admin' => $admin
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
