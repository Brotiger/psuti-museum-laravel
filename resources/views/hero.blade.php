<x-app-layout>
    <div class="container">
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Герой успешно добавлен.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-limit-message">Ошибка! Лимит на данную таблицу превышен, для увиличения лимита свяжитесь с администратором.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, сократите количество прикрепляемых файлов.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        @include('components.addHref')
        <!-- форма ввода -->
        <form enctype="multipart/form-data" id="addHeroForm" class="addHeroForm mt-5">
            <h1 class="h1">Добавление героя</h1>
            <div class="my-4">
                <h2 class="h2 mb-4">Персональная информация</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>
                    </div>
                    <div class="row">
                        <label for="heroImg" class="col-sm-3 col-form-label">Фото</label>
                        <div class="col-sm-9">
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
                        <input type="text" class="form-control" id="lastName" placeholder="Фамилия" data-field form-field autocomplete="off">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="firstName" class="col-sm-3 col-form-label">Имя*</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="firstName" placeholder="Имя" data-field form-field autocomplete="off">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="secondName" class="col-sm-3 col-form-label">Отчество</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="secondName" placeholder="Отчество" data-field form-field autocomplete="off">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="dateBirthday" class="col-3 col-form-label">Дата рождения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="dateBirthday" data-field form-field>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="description" class="col-3 col-form-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="14" placeholder="Описание" data-field form-field></textarea>
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
                <ul id="academicRewardList">
                </ul>
                <p class="mb-4">Для добавления наград нажмите кнопку <strong>добавить</strong></p>
                <button class="btn btn-primary" type="button" id="addReward">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Фотографии</h2>
                <ul id="photoList">
                </ul>
                <p class="mb-4">Для добавления фотографии нажмите кнопку <strong>добавить</strong></p>
                <button class="btn btn-primary" type="button" id="addPhoto">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Видео</h2>
                <ul id="videoList">
                </ul>
                <p class="mb-4">Для добавления видео нажмите кнопку <strong>добавить</strong></p>
                <button class="btn btn-primary" type="button" id="addVideo">Добавить</button>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-danger mb-4" type="button" id="reset">Сбросить</button>
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
@include('components.js.addHref')
<script src="/js/hideMessage.js"></script>
<script>
    $(document).ready(function(){
        var rewardCount = 0;
        var photoCount = 0;
        var videoCount = 0;

        $('#wwii').click(function(){
            let attr = $(this).attr('value');

            if(attr == 'off'){
                $(this).attr('value', 'on');
            }else{
                $(this).attr('value', 'off');
            }
        })

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

        $(".addHeroForm").delegate(".delete", "click", function(){
            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".addHeroForm").delegate("input", "click", function(){
            $(this).removeClass("errorField");
        });

        $(".addHeroForm").delegate("select", "click", function(){
            $(this).removeClass("errorField");
        });

        //Награды
        $("#addReward").on("click", function(){
            $("#academicRewardList").append('<li class="my-4 rewardBlock" style="display: none" id="rewardBlock_'+ rewardCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="reward_'+ rewardCount +'" class="col-3 col-form-label">Награда*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control reward" type="text" id="reward_'+ rewardCount +'" placeholder="Награда" autocomplete="off">'
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
            $("#rewardBlock_" + rewardCount).slideDown(300);
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
                        + '<input type="file" name="photo" id="photo_'+ photoCount +'" class="photo" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}" >'
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

        $("#addHeroForm").submit(function(event){
            event.preventDefault();

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
                    url: "{{ route('add_hero') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data){
                        if (data.success) {
                            $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                            $('#searchEmp').click();
                            resetForm();
                        } else if(data.errors){
                            if(data.errors.indexOf('limit') == -1){
                                $('#error-global-message').fadeIn(300).delay(2000).fadeOut(300);
                                data.errors.forEach(function(ell){
                                    $("#" + ell).addClass("errorField");
                                });
                            }else{
                                $('#error-limit-message').fadeIn(300).delay(3500).fadeOut(300);
                            }
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
                        scrollTop();
                    }
                });
                if(res.status == 0){
                    stopLoading();
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            }else{//Если тело запроса слишком большое
                stopLoading();
                $('#error-body-message').fadeIn(300).delay(4000).fadeOut(300);
            }
            scrollTop();
            
        });
        $("#reset").on("click", function(){
            scrollTop();
            resetForm();
            });
        function resetForm(){
            $("[form-field]").each(function(){
                $(this).removeClass("errorField");
                $(this).val("");
            });

            $(".rewardBlock").slideUp(300, function(){ $(this).remove()});
            $(".photoBlock").slideUp(300, function(){ $(this).remove()});
            $(".videoBlock").slideUp(300, function(){ $(this).remove()});

            rewardCount = 0;
            photoCount = 0;
            videoCount = 0;
        }
    });
</script>