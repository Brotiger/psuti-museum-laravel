<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Employee;
use App\Models\Unit;
use App\Models\Event;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public function edit_page($id = null){
        $site = env('DB_SITE', 'pguty');

        $employees_search = Employee::orderBy('lastName')->limit(15)->get();
        $units_search = Unit::orderBy('fullUnitName')->limit(15)->get();
        $events_search = Event::orderBy('name')->limit(15)->get();

        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $params = [
            'id' => $id,
            'photo_size' => $photo_size,
            'photo_ext' => $photo_ext? implode(', ', $photo_ext) : 'любые',
            'employees_search' => $employees_search,
            'units_search' => $units_search,
            'events_search' => $events_search,
            'site' => $site,
        ];

        if(isset($id)){
            $page = Page::where([
                ['id', $id],
            ]);
            if($page->exists()){
                $params['id'] = $id;
                $params['page'] = $page->get()->first();
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

        $errors = [];

        $photo_size = env('IMG_SIZE', 0);

        $photo_ext = env('IMG_EXT', null);
        if($photo_ext != null) $photo_ext = explode(',', $photo_ext);

        $user = User::where("id", Auth::user()->id)->get()->first();
        
        if(isset($request)){
            if(!$request->input("id") ||  !Page::where([
                ['id', $request->input("id")],
                ])->exists())
            {
                return redirect(route('pages_list'));
            }
            
            if($request->input("post")){
                $posts = json_decode($request->input("post"), true);
                
                $postCountCheck = 0;
                foreach($posts as $post){
                    if(Str::of($post["id"])->trim()->isEmpty()) continue;
                    if($request->file("post_" . $postCountCheck)){
                        if((filesize($request->file("post_" . $postCountCheck)) < $photo_size * 1024) != 1){
                            $errors[] = "post_" . $post["id"];
                            continue;
                        }

                        if(!is_null($photo_ext)){
                            $ext = $request->file('post_'.$postCountCheck)->getClientOriginalExtension();
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
                    $postCountCheck++;
                }
            }

            #Если поля вальдны, сохраняем их в бд
            if(empty($errors)){
                $exception = DB::transaction(function() use ($request){
                    $editPage = Page::where("id", $request->input("id"));
                    $newPostInfo = [];

                    if($request->input("post")){
                        $posts = json_decode($request->input("post"), true);

                        $postCountData = 0;

                        foreach($posts as $post){
                            
                            $newPost = new Post;
                            $newPost->page_id = $editPage->first()->id;

                            $reqPhotoName = "post_" . $postCountData;

                            if($request->file($reqPhotoName)){
                                $photoPath = PhotoService::resize($request, $reqPhotoName, 'uploads/page/photo', 2300);
                                $newPost->photo = $photoPath;
                            }
                            if(Str::of($post["title"])->trim()->isNotEmpty()) $newPost->title = trim($post["title"]);
                            if(Str::of($post["description"])->trim()->isNotEmpty()) $newPost->description = trim($post["description"]);
                            $newPost->save();
                            $postCountData++;
                        }
                    }

                    if($request->input("postToDelete")){
                        $postToDelete = explode(',',$request->input("postToDelete"));

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

                    //$editPost->update($newPostInfo);
                });

            $page = Page::where("id", $request->input("id"))->first();

            $response['posts'] = view('ajax.pagePosts', [
                'page' => $page
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
