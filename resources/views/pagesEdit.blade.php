@section('custom_css')
    <link rel="stylesheet" href="/css/tabs.css">
@endsection
<x-app-layout>
    <div class="container">
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация на странице успешно обновлена.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, сократите количество прикрепляемых файлов.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        @include('components.addHref')
        <form enctype="multipart/form-data" id="editPageForm" class="editPageForm mt-5">
            <h1 class="h1">Редактирование страницы - {{ $page->title }}</h1>
            <div class="my-4">
                <div class="tab">
                    <button class="tablinks" onclick="openCity(event, 'page')" type="button" id="defaultTab">Страница</button>
                    <button class="tablinks" onclick="openCity(event, 'photoArchive')" type="button">Фото архив</button>
                    <button class="tablinks" onclick="openCity(event, 'videoArchive')" type="button">Видео архив</button>
                </div>
                <hr>
                <div id="page" class="tabcontent">
                    <h2 class="h2 my-4">Записи</h2>
                    <ul id="postList">
                        @foreach($page->posts as $index => $post)
                        <li class="my-4 postBlockOld" id="postBlock_{{ $index }}" record-id='{{ $post->id }}'>
                            <div class="form-group mb-3 row">
                                <label for="title_{{ $index }}" class="col-3 col-form-label">Заголовок</label>
                                    <div class="col-sm-9">
                                        <input class="form-control title" type="text" id="title_{{ $index }}" placeholder="Заголовок" autocomplete="off" value="{{ $post->title }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <label for="description_{{ $index }}" class="col-3 col-form-label">Описание*</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control border border-secondary rounded-0 description" id="description_{{ $index }}" rows="14" placeholder="Описание">{{ !empty($post)? $post->description : '' }}</textarea>
                                    </div>
                                    <span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>
                                    <div class="col-sm-9 offset-3 mt-2">
                                        <input type="button" class="btn btn-primary" value="Сотрудники" addEmpHref>
                                        <input type="button" class="btn btn-primary mx-1" value="Подразделения" addUnitHref>
                                        <input type="button" class="btn btn-primary" value="События" addEventHref>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="post_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
                                    <div class="col-sm-9">
                                    @if($post->photo)
                                        <div>
                                            <img src="{{ '/storage/'.$post->photo }}"  class="mb-1">
                                            <button class="btn btn-danger delete" type="button" deletePostPhoto="{{ $post->id }}">Удалить</button>
                                        </div>
                                    @endif
                                    <div class="row mb-1">
                                        <span><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>
                                    </div>
                                    <div class="row">
                                        <div>
                                            <input type="file" name="photo" id="post_{{ $index }}" class="post" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            <button class="btn btn-danger delete" type="button" post-id="{{ $post->id }}">Удалить</button>
                            <hr class="mt-4">
                        </li>
                        @endforeach
                    </ul>
                    <p class="mb-4">Для добавления записи нажмите кнопку <strong>добавить</strong></p>
                    <button class="btn btn-primary" type="button" id="addPost">Добавить</button>
                </div>
                <div id="photoArchive" class="tabcontent">
                </div>
                <div id="videoArchive" class="tabcontent">
                </div>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
@include('components.js.addHref')
<script src="/js/hideMessage.js"></script>
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
        var postToDelete = [];
        var deletePostPhoto = [];

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

            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".editPageForm").delegate("input, textarea", "click", function(){
                $(this).removeClass("errorField");
        });

        //Фотографии
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

            formData.append("postUpdate", JSON.stringify(postUpdate));
            //Конец добавления данных о фото

            formData.append('postToDelete', postToDelete);
            formData.append('deletePostPhoto', deletePostPhoto);

            formData.append('id', '{{ $id }}');

            $('#error-global-message, #success-message, #error-limit-message, #error-message, #error-body-message').hide();

            let sizeCount = 0;

            for(let pair of formData.entries()) {
                sizeCount += (typeof pair[1] === "string") ? pair[1].length : pair[1].size;
            }
            
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
                        $('#postList').html(data.posts);
                        if (data.success) {
                            $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                            $('#counter').text(Number($('#counter').text()) + 1);
                            postToDelete = [];
                            videoToDelete = [];
                        } else if(data.errors){
                                $('#error-global-message').fadeIn(300).delay(2000).fadeOut(300);
                                data.errors.forEach(function(ell){
                                    $("#" + ell).addClass("errorField");
                                });
                        }else{
                            $('#error-message').fadeIn(300).delay(30000).fadeOut(300);
                        }
                    },
                    error: function(data){
                        $('#server-error-file').html('File: ' + data.responseJSON.file);
                        $('#server-error-line').html('Line: ' + data.responseJSON.line);
                        $('#server-error-message').html('Message: ' + data.responseJSON.message);

                        $('#error-message').fadeIn(300).delay(45000).fadeOut(300, function(){
                            $('#server-error-file, #server-error-line, #server-error-message').html('');
                        });
                    }
                });
                if(res.status == 0){
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            }else{
                $('#error-body-message').fadeIn(300).delay(4000).fadeOut(300);
            }

            stopLoading();
            scrollTop();
            event.preventDefault();
        });
    });
</script>