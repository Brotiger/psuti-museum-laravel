<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> подразделений</div>
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о подразделении успешно обновлена.</div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        @include('components.addHref')
        <form enctype="multipart/form-data" id="editUnitForm" class="editUnitForm mt-5">
            <h1 class="h1">Редактирование подразделения</h1>
            <div class="my-4">
                <h2 class="h2 mb-4">Общая информация</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Название подразделения должно быть уникальным, иначе данное поле будет выделено красным</small></span>
                    </div>
                    <div class="row">
                        <label for="fullUnitName" class="col-sm-3 col-form-label">Полное название подразделения*</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="fullUnitName" placeholder="Полное название подразделения" data-field form-field autocomplete="off" value="{{ !empty($unit)? $unit->fullUnitName : '' }}">
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="shortUnitName" class="col-sm-3 col-form-label">Сокращенное название подразделения</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="shortUnitName" placeholder="Сокращенное название подразделения" data-field form-field autocomplete="off" value="{{ !empty($unit)? $unit->shortUnitName : '' }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="typeUnit" class="col-sm-3 col-form-label">Тип подразделения</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="typeUnit" placeholder="Тип подразделения" data-field form-field autocomplete="off" value="{{ !empty($unit)? $unit->typeUnit : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="creationDate" class="col-3 col-form-label">Дата создания</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="creationDate" data-field form-field value="{{ !empty($unit)? $unit->creationDate : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="terminationDate" class="col-3 col-form-label">Дата прекращения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="terminationDate" data-field form-field value="{{ !empty($unit)? $unit->terminationDate : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="description" class="col-3 col-form-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="14" placeholder="Описание" data-field form-field>{{ !empty($unit)? $unit->description : '' }}</textarea>
                    </div>
                    <span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>
                    <div class="col-sm-9 offset-3 mt-2">
                        <input type="button" class="btn btn-primary" value="Сотрудники" id="addEmpHref">
                        <input type="button" class="btn btn-primary" value="Подразделения" id="addUnitHref">
                        <input type="button" class="btn btn-primary" value="События" id="addEventHref">
                    </div>
                </div>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Фотографии</h2>
                <p class="mb-4">Для добавления фотографии нажмите кнопку <strong>добавить</strong></p>
                <ul id="photoList">
                    @foreach($unit->photos as $index => $photo)
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
                        <button class="btn btn-danger delete" type="button" photo-id="{{ $photo->id }}">Удалить</button>
                    </li>
                    @endforeach
                </ul>
                <button class="btn btn-primary" type="button" id="addPhoto">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Видео</h2>
                <p class="mb-4">Для добавления видео нажмите кнопку <strong>добавить</strong></p>
                <ul id="videoList">
                    @foreach($unit->videos as $index => $video)
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
                        <button class="btn btn-danger delete" type="button" video-id="{{ $video->id }}">Удалить</button>
                    </li>
                    @endforeach
                </ul>
                <button class="btn btn-primary" type="button" id="addVideo">Добавить</button>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
@include('components.js.addHref')
<script>
    $(document).ready(function(){
        var photoCount = {{ !empty($unit)? $unit->photos->count(): 0}};
        var videoCount = {{ !empty($unit)? $unit->photos->count(): 0}};
        var photoToDelete = [];
        var videoToDelete = [];

        $(".editUnitForm").delegate(".delete", "click", function(){
            if($(this).attr('photo-id')){
                photoToDelete.push($(this).attr('photo-id'));
            }
            if($(this).attr('video-id')){
                videoToDelete.push($(this).attr('video-id'));
            }

            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".editUnitForm").delegate("input", "click", function(){
                $(this).removeClass("errorField");
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
                        + '<input type="file" name="photo" id="photo_'+ photoCount +'" class="photo">'
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
            + '</li>');
            $("#videoBlock_" + videoCount).slideDown(300);
            videoCount++;
        });

        $("#editUnitForm").submit(function(event){
            let formData = new FormData();

            startLoading();

            var photo = [];
            var video = [];

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

            formData.append('photoToDelete', photoToDelete);
            formData.append('videoToDelete', videoToDelete);

            formData.append('id', '{{ $id }}');

            $('[data-field]').each(function(i, ell){
                formData.append(ell.id, $(this).val());
            });

            let res = $.ajax({
                type: "POST",
                url: "{{ route('update_unit') }}",
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
                    stopLoading();
                    if (data.success) {
                        $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                        $('#counter').text(Number($('#counter').text()) + 1);
                        photoToDelete = [];
                        videoToDelete = [];
                    } else if(data.errors){
                            $('#error-global-message').fadeIn(300).delay(2000).fadeOut(300);
                            data.errors.forEach(function(ell){
                                $("#" + ell).addClass("errorField");
                            });
                    }else{
                        $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                    }
                },
                error: function(data){
                    stopLoading();
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
            if(res.status == 0){
                $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
            }
            scrollTop();
            event.preventDefault();
        });
    });
</script>