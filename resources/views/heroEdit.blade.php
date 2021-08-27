<x-app-layout>
    <div class="container">
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о герое успешно обновлена.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, сократите количество прикрепляемых файлов.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-csrf">Вы слишком долго бездействовали, ваша сессия устарела, перезагрузите страницу.<i class="bi bi-x-circle" close></i></div>
        @include('components.addHref')
        <form enctype="multipart/form-data" id="editHeroForm" class="editHeroForm mt-5">
            <h1 class="h1">Редактирование героя</h1>
            @if($admin)
                @include('components.changeOwner')
            @endif
            <div class="my-4">
                <h2 class="h2 mb-4">Персональная информация</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}. Новый файл заменит существующий</small></span>
                    </div>
                    <div class="row">
                        <label for="heroImg" class="col-sm-3 col-form-label">Фото</label>
                        <div class="col-sm-9" id="imgHero">
                            @if($hero->img)
                                <div class="mb-2">
                                    <img src="{{ '/storage/'.$hero->img }}" class="mb-1">
                                    <button class="btn btn-danger delete" type="button" id="deleteImg">Удалить</button>
                                </div>
                            @endif
                            <input type="file" name="image" id="heroImg" form-field accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <span class="offset-3 col-9"><small>Сочитание полей имя, фамилия, отчество, дата рождения должно быть уникальным, иначе все данные полня будут выделены красным</small></span>
                </div>
                <div class="mb-3 row">
                    <label for="lastName" class="col-sm-3 col-form-label">Фамилия*</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="lastName" maxlength="191" placeholder="Фамилия" data-field form-field autocomplete="off" value="{{ !empty($hero)? $hero->lastName : '' }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="firstName" class="col-sm-3 col-form-label">Имя*</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="firstName" maxlength="191" placeholder="Имя" data-field form-field autocomplete="off" value="{{ !empty($hero)? $hero->firstName : '' }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="secondName" class="col-sm-3 col-form-label">Отчество</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="secondName" maxlength="191" placeholder="Отчество" data-field form-field autocomplete="off" value="{{ !empty($hero)? $hero->secondName : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="dateBirthday" class="col-3 col-form-label">Дата рождения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="dateBirthday" data-field form-field value="{{ !empty($hero)? $hero->dateBirthday : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="description" class="col-3 col-form-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="14" placeholder="Описание" data-field form-field>{{ !empty($hero)? $hero->description : '' }}</textarea>
                    </div>
                    <span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>
                    <div class="col-sm-9 offset-3 mt-2">
                        <input type="button" class="btn btn-primary" value="Сотрудники" addEmpHref>
                        <input type="button" class="btn btn-primary" value="Подразделения" addUnitHref>
                        <input type="button" class="btn btn-primary" value="События" addEventHref>
                    </div>
                </div>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Награды</h2>
                <ul id="rewardList">
                    @if(!empty($hero))
                        @foreach($hero->rewards as $index => $reward)
                        <li class="my-4 rewardBlock" id="rewardBlock_{{ $index }}">
                            <div class="form-group mb-3 row">
                            <label for="reward_{{ $index }}" class="col-3 col-form-label">Награда*</label>
                                <div class="col-sm-9">
                                    <input class="form-control reward" type="text" maxlength="191" id="reward_{{ $index }}" placeholder="Награда" autocomplete="off" value="{{ $reward->reward }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                            <label for="rewardDate_{{ $index }}" class="col-3 col-form-label">Дата присвоения</label>
                                <div class="col-sm-9">
                                    <input class="form-control rewardDate" type="date" id="rewardDate_{{ $index }}" placeholder="Дата присвоения" value="{{ $reward->rewardDate }}">
                                </div>
                            </div>
                            <button class="btn btn-danger delete" type="button">Удалить</button>
                            <hr class="mt-4">
                        </li>
                        @endforeach
                    @endif
                </ul>
                <p class="mb-4">Для добавления наград нажмите кнопку <strong>добавить</strong></p>
                <button class="btn btn-primary" type="button" id="addReward">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Фотографии</h2>
                <ul id="photoList">
                    @foreach($hero->photos as $index => $photo)
                    <li class="my-4 photoBlockOld" id="photoBlock_{{ $index }}">
                        <div class="mb-3">
                            <div class="row">
                                <label for="photo_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
                                <div class="col-sm-9">
                                    <img src="{{ '/storage/'.$photo->photo }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                        <label for="photoName_{{ $index }}" class="col-3 col-form-label">Название фотографии</label>
                            <div class="col-sm-9">
                                <input class="form-control photoName" type="text" maxlength="191" id="photoName_{{ $index }}" placeholder="Название фотографии" autocomplete="off" value="{{ $photo->photoName }}" disabled>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                        <label for="photoDate_{{ $index }}" class="col-3 col-form-label">Дата фотографии</label>
                            <div class="col-sm-9">
                                <input class="form-control photoDate" type="date" id="photoDate_{{ $index }}" placeholder="Дата фотографии" value="{{ $photo->photoDate }}" disabled>
                            </div>
                        </div>
                        <button class="btn btn-danger delete" type="button" photo-id="{{$photo->id}}">Удалить</button>
                        <hr class="mt-4">
                    </li>
                    @endforeach
                </ul>
                <p class="mb-4">Для добавления фотографии нажмите кнопку <strong>добавить</strong></p>
                <button class="btn btn-primary" type="button" id="addPhoto">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Видео</h2>
                <ul id="videoList">
                    @foreach($hero->videos as $index => $video)
                    <li class="my-4 videoBlockOld" id="videoBlock_{{ $index }}">
                        <div class="mb-3">
                            <div class="row">
                                <label for="video_'+ videoCount +'" class="col-sm-3 col-form-label">Видео</label>
                                <div class="col-sm-9">
                                    <iframe width="481" height="315" src="{{ 'https://www.youtube.com/embed/'.$video->video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <label for="videoName_{{ $index }}" class="col-3 col-form-label">Название видео</label>
                            <div class="col-sm-9">
                                <input class="form-control videoName" type="text" maxlength="191" id="videoName_{{ $index }}" placeholder="Название видео" autocomplete="off" disabled value="{{ $video->videoName }}">
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <label for="videoDate_{{ $index }}" class="col-3 col-form-label">Дата видео</label>
                            <div class="col-sm-9">
                                <input class="form-control videoDate" type="date" id="videoDate_{{ $index }}" placeholder="Дата видео" disabled value="{{ $video->videoDate }}">
                            </div>
                        </div>
                        <button class="btn btn-danger delete" type="button" video-id="{{$video->id}}">Удалить</button>
                        <hr class="mt-4">
                    </li>
                    @endforeach
                </ul>
                <p class="mb-4">Для добавления видео нажмите кнопку <strong>добавить</strong></p>
                <button class="btn btn-primary" type="button" id="addVideo">Добавить</button>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
@if($admin)
    @include('components.js.changeOwner')
@endif
@include('components.js.addHref')
<script src="/js/hideMessage.js"></script>
<script>
    $(document).ready(function(){
        var rewardCount = {{ !empty($hero)? $hero->rewards->count(): 0}};

        var photoCount = {{ !empty($hero)? $hero->photos->count(): 0}};
        var videoCount = {{ !empty($hero)? $hero->videos->count(): 0}};

        var photoToDelete = [];
        var videoToDelete = [];
        var deleteImg = false;

        $("form").delegate("#photoList input[type='file']", "change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $photo_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $("#heroImg").on("change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $photo_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $(".editHeroForm").delegate(".delete", "click", function(){
            if($(this).attr('photo-id')){
                photoToDelete.push($(this).attr('photo-id'));
            }
            if($(this).attr('video-id')){
                videoToDelete.push($(this).attr('video-id'));
            }

            if($(this).attr('id') == 'deleteImg'){
                deleteImg = true;
            }
            
            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".editHeroForm").delegate("input", "click", function(){
                $(this).removeClass("errorField");
            });

        $(".editHeroForm").delegate("select", "click", function(){
            $(this).removeClass("errorField");
        });

        //Награды
        $("#addReward").on("click", function(){
            $("#rewardList").append('<li class="my-4 rewardBlock" style="display: none" id="rewardBlock_'+ rewardCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="reward_'+ rewardCount +'" class="col-3 col-form-label">Награда*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control reward" type="text" maxlength="191" id="reward_'+ rewardCount +'" placeholder="Награда" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="rewardDate_'+ rewardCount +'" class="col-3 col-form-label">Дата присвоения</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control rewardDate" type="date" id="rewardDate_'+ rewardCount +'" placeholder="Дата присвоения">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
                + '<hr class="mt-4">'
            + '</li>');
            $("#reward_" + rewardCount).slideDown(300);

            rewardCount++;
        });

        //Фотографии
        $("#addPhoto").on("click", function(){
            $("#photoList").append('<li class="my-4 photoBlock" style="display: none" id="photoBlock_'+ photoCount +'">'
                + '<div class="mb-3">'
                    + '<div class="row mb-1">'
                    +   '<span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>'
                    + '</div>'
                    + '<div class="row">'
                    + '<label for="photo_'+ photoCount +'" class="col-sm-3 col-form-label">Фото*</label>'
                    + '<div class="col-sm-9">'
                        + '<input type="file" name="photo" id="photo_'+ photoCount +'" class="photo" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">'
                    + '</div>'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="photoName_'+ photoCount +'" class="col-3 col-form-label">Название фотографии</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control photoName" type="text" maxlength="191" id="photoName_'+ photoCount +'" placeholder="Название фотографии" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="photoDate_'+ photoCount +'" class="col-3 col-form-label">Дата фотографии</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control photoDate" type="date" id="photoDate_'+ photoCount +'" placeholder="Дата фотографии">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
                + '<hr class="mt-4">'
            + '</li>');
            $("#photoBlock_" + photoCount).slideDown(300);
            photoCount++;
        });
        //Видео
        $("#addVideo").on("click", function(){
            $("#videoList").append('<li class="my-4 videoBlock" style="display: none" id="videoBlock_'+ videoCount +'">'
                + '<div class="form-group mb-3 row">'
                    + '<label for="video_'+ videoCount +'" class="col-3 col-form-label">Видео*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control video" type="text" maxlength="191" id="video_'+ videoCount +'" placeholder="Ссылка на видео с YouTube" autocomplete="off" class="video">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="videoName_'+ videoCount +'" class="col-3 col-form-label">Название видео</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control videoName" type="text" maxlength="191" id="videoName_'+ videoCount +'" placeholder="Название видео" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="videoDate_'+ videoCount +'" class="col-3 col-form-label">Дата видео</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control videoDate" type="date" id="videoDate_'+ videoCount +'" placeholder="Дата видео">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
                + '<hr class="mt-4">'
            + '</li>');
            $("#videoBlock_" + videoCount).slideDown(300);
            videoCount++;
        });

        $("#editHeroForm input").click(function(){
            $(this).removeClass("errorField");
        });

        $("#editHeroForm").submit(function(event){
            startLoading();
            let formData = new FormData();
            var reward = [];
            var photo = [];
            var video = [];

            //Начадл добавления данных о наградах
            $(".rewardBlock").each(function(i, ell){
                reward.push({
                    "reward": $(this).find(".reward").val(),
                    "rewardDate": $(this).find(".rewardDate").val(),
                    "id": ell.id.replace("reward_", "")
                });
            });
            formData.append("reward", JSON.stringify(reward));
            //Конец добавления данных о наградах

            //Начадл добавления данных о фото
            $(".photoBlock").each(function(i, ell){
                photo.push({
                    "photoDate": $(this).find(".photoDate").val(),
                    "photoName": $(this).find(".photoName").val(),
                    "id": ell.id.replace("photoBlock_", "")
                });
                formData.append("photo_" + i, $(this).find(".photo")[0].files[0]);
            });
            formData.append("photo", JSON.stringify(photo));
            //Конец добавления данных о фото
            //Начадл добавления данных о видео
            $(".videoBlock").each(function(i, ell){
                video.push({
                    "videoDate": $(this).find(".videoDate").val(),
                    "videoName": $(this).find(".videoName").val(),
                    "video": $(this).find(".video").val(),
                    "id": ell.id.replace("videoBlock_", "")
                });
            });
            formData.append("video", JSON.stringify(video));
            //Конец добавления данных о видео
            
            formData.append('image', $("#heroImg")[0].files[0]);
            
            formData.append('photoToDelete', photoToDelete);
            formData.append('videoToDelete', videoToDelete);

            if(deleteImg){
                formData.append('deleteImg', true);
            }

            formData.append('id', '{{ $id }}');
            
            $('[data-field]').each(function(i, ell){
                formData.append(ell.id, $(this).val());
            });

            $('#error-global-message, #success-message, #error-limit-message, #error-message, #error-body-message').hide();

            let sizeCount = 0;

            for(let pair of formData.entries()) {
                sizeCount += (typeof pair[1] === "string") ? pair[1].length : pair[1].size;
            }

            if(sizeCount < @php echo env("MAX_BODY_SIZE", 0) @endphp * 1024){
                let res = $.ajax({
                    type: "POST",
                    url: "{{ route('update_hero') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data){
                        $('#photoList').html(data.photos);
                        $('#imgHero').html(data.imgHero);
                        $('#videoList').html(data.videos);
                        if (data.success) {
                            $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                            photoToDelete = [];
                            videoToDelete = [];
                            deleteImg = false;
                        } else if(data.errors){
                            $('#error-global-message').fadeIn(300).delay(2000).fadeOut(300);
                            data.errors.forEach(function(ell){
                                $("#" + ell).addClass("errorField");
                            });
                        }else{
                            $('#error-message').fadeIn(300).delay(30000).fadeOut(300);
                        }
                        stopLoading();
                    },
                    error: function(data){
                        if(data.responseJSON.message == 'CSRF token mismatch.'){
                            $('#error-csrf').fadeIn(300).delay(3500).fadeOut(300);
                        }else{
                            $('#server-error-file').html('File: ' + data.responseJSON.file);
                            $('#server-error-line').html('Line: ' + data.responseJSON.line);
                            $('#server-error-message').html('Message: ' + data.responseJSON.message);

                            $('#error-message').fadeIn(300).delay(45000).fadeOut(300, function(){
                                $('#server-error-file, #server-error-line, #server-error-message').html('');
                            });
                        }
                        stopLoading();
                    }
                });
                if(res.status == 0){
                    stopLoading();
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            }else{
                stopLoading();
                $('#error-body-message').fadeIn(300).delay(4000).fadeOut(300);
            }
            scrollTop();
            event.preventDefault();
        });
    });
</script>