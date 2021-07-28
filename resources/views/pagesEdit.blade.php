@section('custom_css')
    <link rel="stylesheet" href="/css/tabs.css">
@endsection
<x-app-layout>
    <div class="container">
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о странице успешно обновлена.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно. Проверьте вкладки - "Страница", "Фото архив", "Видео архив"<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, сократите количество прикрепляемых файлов.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        @include('components.addHref')
        <form enctype="multipart/form-data" id="editPageForm" class="editPageForm mt-5">
            <h1 class="h1">Редактирование страницы - {{ $page->title }}</h1>
            @if($admin)
                @include('components.changeOwner')
            @endif
            <div class="my-4">
                <div class="tab">
                    <button class="tablinks" onclick="openCity(event, 'page')" type="button" id="defaultTab">Страница</button>
                    <button class="tablinks" onclick="openCity(event, 'photoArchive')" type="button">Фото архив</button>
                    <button class="tablinks" onclick="openCity(event, 'videoArchive')" type="button">Видео архив</button>
                    <button class="tablinks" onclick="openCity(event, 'history')" type="button">История от первого лица</button>
                </div>
                <hr>
                <div id="page" class="tabcontent">
                    <h2 class="h2 my-4">Записи</h2>
                    @if(isset($page->posts) && !$access)
                        <p>Записей пока что нету</p>
                        <hr class="mt-3">
                    @endif
                    <ul id="postList">
                        @foreach($page->posts as $index => $post)
                        <li class="my-4 postBlockOld" id="postBlock_{{ $index }}" record-id='{{ $post->id }}'>
                            <div class="form-group mb-3 row">
                                <label for="title_{{ $index }}" class="col-3 col-form-label">Заголовок</label>
                                    <div class="col-sm-9">
                                        <input class="form-control title" type="text" id="title_{{ $index }}" placeholder="Заголовок" autocomplete="off" value="{{ $post->title }}" @php if(!$access) echo "disabled" @endphp>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label for="description_{{ $index }}" class="col-3 col-form-label">Описание*</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control border border-secondary rounded-0 description" id="description_{{ $index }}" rows="14" placeholder="Описание" @php if(!$access) echo "disabled" @endphp>{{ !empty($post)? $post->description : '' }}</textarea>
                                    </div>
                                    @if($access)
                                    <span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>
                                    <div class="col-sm-9 offset-3 mt-2">
                                        <input type="button" class="btn btn-primary" value="Сотрудники" addEmpHref>
                                        <input type="button" class="btn btn-primary mx-1" value="Подразделения" addUnitHref>
                                        <input type="button" class="btn btn-primary" value="События" addEventHref>
                                    </div>
                                    @endif
                                </div>
                                @if($post->photo || $access)
                                <div class="mb-3 row">
                                    <label for="post_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
                                    <div class="col-sm-9">
                                    @if($post->photo)
                                        <div>
                                            <img src="{{ '/storage/'.$post->photo }}">
                                            @if($access)
                                                <button class="btn btn-danger delete mt-3" type="button" deletePostPhoto="{{ $post->id }}">Удалить</button>
                                            @endif
                                        </div>
                                    @endif
                                    @if($access)
                                        <div class="row mb-1">
                                            <span><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>
                                        </div>
                                        <div class="row">
                                            <div>
                                                <input type="file" name="photo" id="post_{{ $index }}" class="post" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                                @endif
                            @if($access)
                                <button class="btn btn-danger delete" type="button" post-id="{{ $post->id }}">Удалить</button>
                            @endif
                            <hr class="mt-4">
                        </li>
                        @endforeach
                    </ul>
                    @if($access)
                        <p class="mb-4">Для добавления записи нажмите кнопку <strong>добавить</strong></p>
                        <button class="btn btn-primary" type="button" id="addPost">Добавить</button>
                        <hr class="mt-4">
                    @endif
                </div>
                <div id="photoArchive" class="tabcontent">
                        <h2 class="h2 my-4">Фотографии</h2>
                        @if(isset($page->photos) && !$access)
                            <p>Фотографий пока что нету</p>
                            <hr class="mt-3">
                        @endif
                        <ul id="photoList">
                            @foreach($page->photos as $index => $photo)
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
                                        <input class="form-control photoName" type="text" id="photoName_{{ $index }}" placeholder="Название фотографии" autocomplete="off" disabled value="{{ $photo->photoName }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label for="photoDate_{{ $index }}" class="col-3 col-form-label">Дата фотографии</label>
                                    <div class="col-sm-9">
                                        <input class="form-control photoDate" type="date" id="photoDate_{{ $index }}" placeholder="Дата фотографии" disabled value="{{ $photo->photoDate }}">
                                    </div>
                                </div>
                                @if($access)
                                    <button class="btn btn-danger delete" type="button" photo-id="{{ $photo->id }}">Удалить</button>
                                @endif
                                <hr class="mt-4">
                            </li>
                            @endforeach
                        </ul>
                        @if($access)
                            <p class="mb-4">Для добавления фотографии нажмите кнопку <strong>добавить</strong></p>
                            <button class="btn btn-primary" type="button" id="addPhoto">Добавить</button>
                            <hr class="mt-4">
                        @endif
                </div>
                <div id="videoArchive" class="tabcontent">
                    <h2 class="h2 my-4">Видео</h2>
                    @if(isset($page->videos) && !$access)
                        <p>Видео пока что нету</p>
                        <hr class="mt-3">
                    @endif
                    <ul id="videoList">
                        @foreach($page->videos as $index => $video)
                        <li class="my-4 videoBlockOld" id="videoBlock_{{ $index }}">
                            <div class="mb-3">
                                <div class="row">
                                    <label for="video_{{ $index }}" class="col-sm-3 col-form-label">Видео</label>
                                    <div class="col-sm-9">
                                        <iframe width="481" height="315" src="{{ 'https://www.youtube.com/embed/'.$video->video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label for="videoName_{{ $index }}" class="col-3 col-form-label">Название видео</label>
                                <div class="col-sm-9">
                                    <input class="form-control videoName" type="text" id="videoName_{{ $index }}" placeholder="Название видео" autocomplete="off" disabled value="{{ $video->videoName }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label for="videoDate_{{ $index }}" class="col-3 col-form-label">Дата видео</label>
                                <div class="col-sm-9">
                                    <input class="form-control videoDate" type="date" id="videoDate_{{ $index }}" placeholder="Дата видео" disabled value="{{ $video->videoDate }}">
                                </div>
                            </div>
                            @if($access)
                                <button class="btn btn-danger delete" type="button" video-id="{{ $video->id }}">Удалить</button>
                            @endif
                            <hr class="mt-4">
                        </li>
                        @endforeach
                    </ul>
                    @if($access)
                        <p class="mb-4">Для добавления видео нажмите кнопку <strong>добавить</strong></p>
                        <button class="btn btn-primary" type="button" id="addVideo">Добавить</button>
                        <hr class="mt-4">
                    @endif
                </div>
                <div id="history" class="tabcontent">
                    <h2 class="h2 my-4">История</h2>
                    <ul id="historyList">
                        @foreach($page->history as $index => $history)
                        <li class="my-4 historyBlockOld" id="historyBlock_{{ $index }}">
                            <div class="form-group mb-3 row">
                                <label for="commentAuth_{{ $index }}" class="col-3 col-form-label">Автор</label>
                                <div class="col-sm-9 mb-3">
                                    <input class="form-control commentAuth" type="text" id="commentAuth_{{ $index }}" placeholder="Автор" autocomplete="off" disabled value="{{ isset($history->user)? (isset($history->user->name) ? $history->user->name : $history->user->email) : 'Без автора (автор удален)' }}">
                                </div>
                                <label for="comment_{{ $index }}" class="col-3 col-form-label">История</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control border border-secondary rounded-0 comment" id="comment_{{ $index }}" rows="7" disabled placeholder="История">{{ $history->comment }}</textarea>
                                </div>
                            </div>
                            @if($me->id == $history->addUserId || $admin)
                                <button class="btn btn-danger delete" type="button" history-id="{{ $history->id }}">Удалить</button>
                            @endif
                            <hr class="mt-4">
                        </li>
                        @endforeach
                    </ul>
                    <p class="mb-4">Для добавления истории нажмите кнопку <strong>добавить</strong></p>
                    <button class="btn btn-primary" type="button" id="addHistory">Добавить</button>
                    <hr class="mt-4">
                </div>
            </div>
            <div class="form-group mt-4">
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
@include('components.js.addHref')
<script src="/js/hideMessage.js"></script>
@if($admin)
    @include('components.js.changeOwner')
@endif
<script>
    function openCity(evt, cityName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    document.getElementById("defaultTab").click();
</script>
<script>
    $(document).ready(function(){
        var postCount = {{ !empty($page)? $page->posts->count(): 0}};
        var photoCount = {{ !empty($page)? $page->photos->count(): 0}};
        var videoCount = {{ !empty($page)? $page->photos->count(): 0}};
        var historyCount = {{ !empty($page)? $page->history->count(): 0}};

        var postToDelete = [];
        var deletePostPhoto = [];
        var photoToDelete = [];
        var videoToDelete = [];
        var historyToDelete = [];

        $("form").delegate("#postList input[type='file']", "change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $photo_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $(".editPageForm").delegate(".delete", "click", function(){
            if($(this).attr('post-id')){
                postToDelete.push($(this).attr('post-id'));
            }

            if($(this).attr('deletePostPhoto')){
                deletePostPhoto.push($(this).attr('deletePostPhoto'));
            }

            if($(this).attr('photo-id')){
                photoToDelete.push($(this).attr('photo-id'));
            }

            if($(this).attr('video-id')){
                videoToDelete.push($(this).attr('video-id'));
            }

            if($(this).attr('history-id')){
                historyToDelete.push($(this).attr('history-id'));
            }

            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".editPageForm").delegate("input, textarea", "click", function(){
                $(this).removeClass("errorField");
        });

        //История от первого лица
        $("#addHistory").on("click", function(){
            $("#historyList").append('<li class="my-4 historyBlock" style="display: none" id="historyBlock_'+ historyCount +'">'
                        @if(!isset($me->name))
                        + '<div class="row mb-1">'
                            + '<span class="offset-3 col-9"><small>Укажите имя в настройках профиля</small></span>'
                        + '</div>'
                        @endif
                    + '<div class="form-group mb-3 row">'
                        + '<label for="commentAuth_'+ historyCount +'" class="col-3 col-form-label">Автор</label>'
                        + '<div class="col-sm-9 mb-3">'
                            + '<input class="form-control commentAuth" type="text" id="commentAuth_'+ historyCount +'" placeholder="Автор" autocomplete="off" disabled value="{{ isset($me->name) ? $me->name : $me->email }}">'
                        + '</div>'
                        + '<label for="comment_'+ historyCount +'" class="col-3 col-form-label">История*</label>'
                        + '<div class="col-sm-9">'
                            + '<textarea class="form-control border border-secondary rounded-0 comment" id="comment_'+ historyCount +'" rows="7" placeholder="История"></textarea>'
                        + '</div>'
                    + '</div>'
                    + '<button class="btn btn-danger delete" type="button" history-id="'+ historyCount +'">Удалить</button>'
                    + '<hr class="mt-4">'
            + '</li>');
            $("#historyBlock_" + historyCount).slideDown(300);
            historyCount++;
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
                        + '<input class="form-control photoName" type="text" id="photoName_'+ photoCount +'" placeholder="Название фотографии" autocomplete="off">'
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
                        + '<input class="form-control video" type="text" id="video_'+ videoCount +'" placeholder="Ссылка на видео с YouTube" autocomplete="off" class="video">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="videoName_'+ videoCount +'" class="col-3 col-form-label">Название видео</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control videoName" type="text" id="videoName_'+ videoCount +'" placeholder="Название видео" autocomplete="off">'
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

        //Записи
        $("#addPost").on("click", function(){
            $("#postList").append('<li class="my-4 postBlock" style="display: none" id="postBlock_'+ postCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="title_'+ postCount +'" class="col-3 col-form-label">Заголовок</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control title" type="text" id="title_'+ postCount +'" placeholder="Заголовок" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                    + '<label for="description_'+ postCount +'" class="col-3 col-form-label">Описание*</label>'
                    + '<div class="col-sm-9">'
                        + '<textarea class="form-control border border-secondary rounded-0 description" id="description_'+ postCount +'" rows="14" placeholder="Описание" data-field form-field>{{ !empty($event)? $event->description : '' }}</textarea>'
                    + '</div>'
                    + '<span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>'
                    + '<div class="col-sm-9 offset-3 mt-2">'
                        + '<input type="button" class="btn btn-primary" value="Сотрудники" addEmpHref>'
                        + '<input type="button" class="btn btn-primary mx-1" value="Подразделения" addUnitHref>'
                        + '<input type="button" class="btn btn-primary" value="События" addEventHref>'
                    + '</div>'
                + '</div>'
                + '<div class="mb-3">'
                    + '<div class="row mb-1">'
                        +   '<span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>'
                        + '</div>'
                    + '<div class="row">'
                    + '<label for="post_'+ postCount +'" class="col-sm-3 col-form-label">Фото</label>'
                    + '<div class="col-sm-9">'
                        + '<input type="file" name="photo" id="post_'+ postCount +'" class="post" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">'
                    + '</div>'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
                + '<hr class="mt-4">'
            + '</li>');
            $("#postBlock_" + postCount).slideDown(300);
            postCount++;
        });

        $("#editPageForm").submit(function(event){
            let formData = new FormData();

            startLoading();

            var post = [];
            var postUpdate = [];

            var photo = [];
            var video = [];
            var history = [];

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

            //Начадл добавления данных о истории
            $(".historyBlock").each(function(i, ell){
                history.push({
                    "comment": $(this).find(".comment").val(),
                    "id": ell.id.replace("historyBlock_", "")
                });
            });
            formData.append("history", JSON.stringify(history));
            //Конец добавления данных о истории

            formData.append('photoToDelete', photoToDelete);
            formData.append('videoToDelete', videoToDelete);
            formData.append('historyToDelete', historyToDelete);

            //Начадл добавления данных о фото
            $(".postBlock").each(function(i, ell){
                post.push({
                    "title": $(this).find(".title").val(),
                    "description": $(this).find(".description").val(),
                    "id": ell.id.replace("postBlock_", "")
                });
                if($(this).find(".post")[0].files[0] !== 'undefined'){
                    formData.append("post_" + post[post.length - 1]['id'], $(this).find(".post")[0].files[0]);
                }
            });
            
            formData.append("post", JSON.stringify(post));
            //Конец добавления данных о фото

            @if($access)
            //Начадл добавления данных о фото
            $(".postBlockOld").each(function(i, ell){
                postUpdate.push({
                    "title": $(this).find(".title").val(),
                    "description": $(this).find(".description").val(),
                    "id": ell.id.replace("postBlock_", ""),
                    "record-id": $(this).attr('record-id')
                });
                if($(this).find(".post")[0].files[0] !== 'undefined'){
                    formData.append("post_" + postUpdate[postUpdate.length - 1]['id'], $(this).find(".post")[0].files[0]);
                }
            });
            @endif
            
            formData.append("postUpdate", JSON.stringify(postUpdate));
            //Конец добавления данных о фото

            formData.append('postToDelete', postToDelete);
            formData.append('deletePostPhoto', deletePostPhoto);

            formData.append('alias', '{{ $alias }}');

            $('#error-global-message, #success-message, #error-limit-message, #error-message, #error-body-message').hide();

            let sizeCount = 0;

            for(let pair of formData.entries()) {
                sizeCount += (typeof pair[1] === "string") ? pair[1].length : pair[1].size;
            }

            $('[data-field]').each(function(i, ell){
                formData.append(ell.id, $(this).val());
            });
            
            if(sizeCount < @php echo env("MAX_BODY_SIZE", 0) @endphp * 1024){
                let res = $.ajax({
                    type: "POST",
                    url: "{{ route('update_page') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    tataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data){
                        $('#photoList').html(data.photos);
                        $('#videoList').html(data.videos);
                        $('#postList').html(data.posts);
                        $('#historyList').html(data.history);
                        if (data.success) {
                            $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                            postToDelete = [];
                            deletePostPhoto = [];
                            photoToDelete = [];
                            videoToDelete = [];
                            historyToDelete = [];
                            $('textarea.comment').attr('disabled', true);
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
                        $('#server-error-file').html('File: ' + data.responseJSON.file);
                        $('#server-error-line').html('Line: ' + data.responseJSON.line);
                        $('#server-error-message').html('Message: ' + data.responseJSON.message);

                        $('#error-message').fadeIn(300).delay(45000).fadeOut(300, function(){
                            $('#server-error-file, #server-error-line, #server-error-message').html('');
                        });
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