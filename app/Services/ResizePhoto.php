<?php

namespace App\Services;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use App\Models\Photo;
use Illuminate\Http\Request;

class ResizePhoto {
    private $tmp_dir = "app/public/uploads/tmp/";

    private function createDir($dir){
        if (!file_exists(storage_path('app/public/'.$dir))){
            mkdir(storage_path('app/public/'.$dir), 0777, true);
        }

        if (!file_exists(storage_path($this->tmp_dir))){
            mkdir(storage_path($this->tmp_dir), 0777, true);
        }
    }

    public function resize(Request $request, $reqPhotoName, $dir, $width){
        $this->createDir($dir);

        $photo_size = env('IMG_SIZE', 0);
        
        $ext = $request->file($reqPhotoName)->getClientOriginalExtension();

        if($ext == 'svg'){
            $photoPath = $request->file($reqPhotoName)->store($dir, 'public');
        }else{
            $quality = 100;

            $file_name = Str::random(40).'.'.$ext;
            $photoPath = $dir.'/'.$file_name;
            $tmpFilePath = $this->tmp_dir.$file_name;
            
            $img = Image::make($request->file($reqPhotoName));

            if($img->width() > $width){

                $img->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(storage_path($tmpFilePath));

                unset($img);

                $img = Image::make(storage_path($tmpFilePath));

                unlink(storage_path($tmpFilePath));
            }

            $img_size = $img->filesize();
            $limit_size = ($photo_size / 3) * 1024;

            if($img_size > $limit_size){
                $quality = env("PHOTO_QUALITY", 85);
            }

            $img->save(storage_path('app/public/'.$photoPath), $quality);

        }
        
        return $photoPath;
    }
}