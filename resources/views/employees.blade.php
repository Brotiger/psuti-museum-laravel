<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> сотрудников</div>
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Сотрудник успешно добавлен.</div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.</div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-limit-message">Ошибка! Лимит на данную таблицу превышен, для увидичения лимита свяжитесь с системным администратором.</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <form enctype="multipart/form-data" id="addEmpForm" class="addEmpForm mt-5">
            <h1 class="h1">Добавление сотрудника</h1>
            <div class="my-4">
                <h2 class="h2 mb-4">Персональная информация</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>
                    </div>
                    <div class="row">
                        <label for="empImg" class="col-sm-3 col-form-label">Фото</label>
                        <div class="col-sm-9">
                            <input type="file" name="image" id="empImg" form-field>
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
                    <label for="hired" class="col-3 col-form-label">Дата приема</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="hired" data-field form-field>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="fired" class="col-3 col-form-label">Дата увольнения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="fired" data-field form-field>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="description" class="col-3 col-form-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="7" placeholder="Описание" data-field form-field></textarea>
                    </div>
                </div>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Образование</h2>
                <p class="mb-4">Для добавления информации о образовании нажмите кнопку <strong>добавить</strong></p>
                <ul id="educationList">
                </ul>
                <button class="btn btn-primary" type="button" id="addEducation">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Подразделения</h2>
                <p class="mb-4">Для добавления информации о подразделениях нажмите кнопку <strong>добавить</strong></p>
                <ul id="unitList">
                </ul>
                <button class="btn btn-primary" type="button" id="addUnit">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Ученые степени</h2>
                <p class="mb-4">Для добавления ученой степени нажмите кнопку <strong>добавить</strong></p>
                <ul id="academicDegreeList">
                </ul>
                <button class="btn btn-primary" type="button" id="addAcademicDegree">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Ученые звания</h2>
                <p class="mb-4">Для добавления ученого звания нажмите кнопку <strong>добавить</strong></p>
                <ul id="academicTitleList">
                </ul>
                <button class="btn btn-primary" type="button" id="addAcademicTitle">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Награды</h2>
                <p class="mb-4">Для добавления наград нажмите кнопку <strong>добавить</strong></p>
                <ul id="academicRewardList">
                </ul>
                <button class="btn btn-primary" type="button" id="addAcademicReward">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Достижения</h2>
                <p class="mb-4">Для добавления достижения нажмите кнопку <strong>добавить</strong></p>
                <ul id="attainmentList">
                </ul>
                <button class="btn btn-primary" type="button" id="addAttainment">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Фотографии</h2>
                <p class="mb-4">Для добавления фотографии нажмите кнопку <strong>добавить</strong></p>
                <ul id="photoList">
                </ul>
                <button class="btn btn-primary" type="button" id="addPhoto">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Видео</h2>
                <p class="mb-4">Для добавления видео нажмите кнопку <strong>добавить</strong></p>
                <ul id="videoList">
                </ul>
                <button class="btn btn-primary" type="button" id="addVideo">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Личное дело</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $file_size }} КБ. Допустимые расширения: {{ $file_ext }}</small></span>
                    </div>
                    <div class="mb row">
                        <label for="titlePersonalFile" class="col-sm-3 col-form-label">Титульный лист</label>
                        <div class="col-sm-9">
                            <input type="file" id="titlePersonalFile" form-field>
                        </div>
                    </div>
                </div>
                <h3 class="h4 my-4">Автобиография</h3>
                <p class="mb-4">Для добавления информации о автобиографии нажмите кнопку <strong>добавить</strong></p>
                <ul id="autobiographyList">
                </ul>
                <button class="btn btn-primary" type="button" id="addAutobiography">Добавить</button>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-danger mb-4" type="button" id="reset">Сбросить</button>
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        var educationCount = 0;
        var academicDegreeCount = 0;
        var academicTitleCount = 0;
        var academicRewardCount = 0;
        var attainmentCount = 0;
        var photoCount = 0;
        var videoCount = 0;
        var unitCount = 0;
        var autobiographyCount = 0;

        $(".addEmpForm").delegate(".delete", "click", function(){
            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".addEmpForm").delegate("input", "click", function(){
            $(this).removeClass("errorField");
        });

        $(".addEmpForm").delegate("select", "click", function(){
            $(this).removeClass("errorField");
        });
        
        //Образование
        $("#addEducation").on("click", function(){
            $("#educationList").append('<li class="my-4 educations" style="display: none" id="education_'+ educationCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="university_'+ educationCount +'" class="col-3 col-form-label">ВУЗ*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control university" type="text" id="university_'+ educationCount +'" placeholder="ВУЗ" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="specialty_'+ educationCount +'" class="col-3 col-form-label">Специальность</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control specialty" type="text" id="specialty_'+ educationCount +'" placeholder="Специальность" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="expirationDate_'+ educationCount +'" class="col-3 col-form-label">Дата окончания</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control expirationDate" type="date" id="expirationDate_'+ educationCount +'" placeholder="Дата окончания">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#education_" + educationCount).slideDown(300);
            educationCount++;
        });
        //Подразделение
        $("#addUnit").on("click", function(){
            $("#unitList").append('<li class="my-4 unitBlock" style="display: none" id="unitBlock_'+ unitCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="unit_'+ unitCount +'" class="col-3 col-form-label">Подразделение*</label>'
                    + '<div class="col-sm-9">'
                        + '<input type="text" class="search-select form-control mb-3" placeholder="Фильтр списка подразделений">'
                        + '<select class="unit custom-select custom-select-lg form-control border border-secondary rounded-0" id="unit_'+ unitCount +'">'
                        + '<option value="">Не выбрано</option>'
                        @foreach($units as $unit)
                        + '<option value="{{ $unit->id }}">{{ $unit->fullUnitName }}</option>'
                        @endforeach
                        + '</select>'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="post_'+ unitCount +'" class="col-3 col-form-label">Должность</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control post" type="text" id="post_'+ unitCount +'" placeholder="Должность" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="recruitmentDate_'+ unitCount +'" class="col-3 col-form-label">Дата приема</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control recruitmentDate" type="date" id="recruitmentDate_'+ unitCount +'" placeholder="Дата приема">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#unitBlock_" + unitCount).slideDown(300);
            unitCount++;
        });
        //Ученые степени
        $("#addAcademicDegree").on("click", function(){
            $("#academicDegreeList").append('<li class="my-4 academicDegree" style="display: none" id="academicDegree_'+ academicDegreeCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="degree_'+ academicDegreeCount +'" class="col-3 col-form-label">Ученая степень*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control degree" type="text" id="degree_'+ academicDegreeCount +'" placeholder="Ученая степень" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="assignmentDate_'+ academicDegreeCount +'" class="col-3 col-form-label">Дата присвоения</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control assignmentDate" type="date" id="assignmentDate_'+ academicDegreeCount +'" placeholder="Дата присвоения">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="topic_'+ academicDegreeCount +'" class="col-3 col-form-label">Тема диссертации</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control topic" type="text" id="topic_'+ academicDegreeCount +'" placeholder="Тема диссертации" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="universityDefense_'+ academicDegreeCount +'" class="col-3 col-form-label">ВУЗ</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control universityDefense" type="text" id="universityDefense_'+ academicDegreeCount +'" placeholder="ВУЗ" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#academicDegree_" + academicDegreeCount).slideDown(300);
            academicDegreeCount++;;
        });
        //Ученые звания
        $("#addAcademicTitle").on("click", function(){
            $("#academicTitleList").append('<li class="my-4 academicTitle" style="display: none" id="academicTitle_'+ academicTitleCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="title_'+ academicTitleCount +'" class="col-3 col-form-label">Ученое звание*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control title" type="text" id="title_'+ academicTitleCount +'" placeholder="Ученое звание" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="titleDate_'+ academicTitleCount +'" class="col-3 col-form-label">Дата присвоения</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control titleDate" type="date" id="titleDate_'+ academicTitleCount +'" placeholder="Дата присвоения">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#academicTitle_" + academicTitleCount).slideDown(300);
            academicTitleCount++;
        });
        //Награды
        $("#addAcademicReward").on("click", function(){
            $("#academicRewardList").append('<li class="my-4 academicReward" style="display: none" id="academicReward_'+ academicRewardCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="reward_'+ academicRewardCount +'" class="col-3 col-form-label">Награда*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control reward" type="text" id="reward_'+ academicRewardCount +'" placeholder="Награда" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="rewardDate_'+ academicRewardCount +'" class="col-3 col-form-label">Дата присвоения</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control rewardDate" type="date" id="rewardDate_'+ academicRewardCount +'" placeholder="Дата присвоения">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#academicReward_" + academicRewardCount).slideDown(300);
            academicRewardCount++;
        });
        //Достижения
        $("#addAttainment").on("click", function(){
            $("#attainmentList").append('<li class="my-4 attainmentBlock" style="display: none" id="attainmentBlock_'+ attainmentCount +'">'
                + '<div class="form-group mb-3 row">'
                + '<label for="attainment_'+ attainmentCount +'" class="col-3 col-form-label">Достижение*</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control attainment" type="text" id="attainment_'+ attainmentCount +'" placeholder="Достижение" autocomplete="off">'
                    + '</div>'
                + '</div>'
                + '<div class="form-group mb-3 row">'
                + '<label for="attainmentDate_'+ attainmentCount +'" class="col-3 col-form-label">Дата достижения</label>'
                    + '<div class="col-sm-9">'
                        + '<input class="form-control attainmentDate" type="date" id="attainmentDate_'+ attainmentCount +'" placeholder="Дата достижения">'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#attainmentBlock_" + attainmentCount).slideDown(300);
            attainmentCount++;
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
                + '<div class="mb-3">'
                    + '<div class="row mb-1">'
                        +   '<span class="offset-3 col-9"><small>Максимальный вес файла: {{ $video_size }} КБ. Допустимые расширения: {{ $video_ext }}</small></span>'
                        + '</div>'
                    + '<div class="row">'
                    + '<label for="video_'+ videoCount +'" class="col-sm-3 col-form-label">Видео*</label>'
                    + '<div class="col-sm-9">'
                        + '<input type="file" id="video_'+ videoCount +'" class="video">'
                    + '</div>'
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
        //Личное дело
        $("#addAutobiography").on("click", function(){
            $("#autobiographyList").append('<li class="my-4 autobiographyBlock" style="display: none" id="autobiographyBlock_'+ autobiographyCount +'">'
                + '<div class="mb-3">'
                    + '<div class="row mb-1">'
                        +   '<span class="offset-3 col-9"><small>Максимальный вес файла: {{ $file_size }} КБ. Допустимые расширения: {{ $file_ext }}</small></span>'
                        + '</div>'
                    + '<div class="row">'
                    + '<label for="autobiography_'+ autobiographyCount +'" class="col-sm-3 col-form-label">Лист</label>'
                    + '<div class="col-sm-9">'
                        + '<input type="file" name="photo" id="autobiography_'+ autobiographyCount +'" class="autobiography">'
                    + '</div>'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#autobiographyBlock_" + autobiographyCount).slideDown(300);
            autobiographyCount++;
        });

        /*$("#addEmpForm input").click(function(){
            $(this).removeClass("errorField");
        });*/

        $('#unitList').delegate('.search-select', 'keyup', function(){
            if($(this).val()){
                options = $(this).next('select').children('option');
                $search = $(this).val();
                options.each((i, ell) => {
                    ell.style.display = 'block';
                    
                    if(ell.innerHTML.indexOf($search) == -1){
                        ell.style.display = 'none'
                    }
                });
            }else{
                $(this).next('select').children('option').each((i, ell) => {
                    ell.style.display = 'block';
                });
            }
        });

        $("#addEmpForm").submit(function(event){
            startLoading();
            let formData = new FormData();
            var educations = [];
            var academicDegree = [];
            var academicTitle = [];
            var academicReward = [];
            var attainment = [];
            var photo = [];
            var video = [];
            var unit = [];
            var autobiography = [];
            //Начадл добавления данных о образовании
            $(".educations").each(function(i, ell){
                educations.push({
                    "university": $(this).find(".university").val(),
                    "specialty": $(this).find(".specialty").val(),
                    "expirationDate": $(this).find(".expirationDate").val(),
                    "id": ell.id.replace("education_", "")
                });
            });
            formData.append("educations", JSON.stringify(educations));
            //Конец добавления данных о образовании
            //Начадл добавления данных о подразделениях
            $(".unitBlock").each(function(i, ell){
                unit.push({
                    "unit": $(this).find(".unit").val(),
                    "post": $(this).find(".post").val(),
                    "recruitmentDate": $(this).find(".recruitmentDate").val(),
                    "id": ell.id.replace("unitBlock_", "")
                });
            });
            formData.append("unit", JSON.stringify(unit));
            //Конец добавления данных о подразделениях
            //Начадл добавления данных о научных степенях
            $(".academicDegree").each(function(i, ell){
                academicDegree.push({
                    "degree": $(this).find(".degree").val(),
                    "assignmentDate": $(this).find(".assignmentDate").val(),
                    "topic": $(this).find(".topic").val(),
                    "universityDefense": $(this).find(".universityDefense").val(),
                    "id": ell.id.replace("academicDegree_", "")
                });
            });
            formData.append("academicDegree", JSON.stringify(academicDegree));
            //Конец добавления данных о научных степенях
            //Начадл добавления данных о научных званиях
            $(".academicTitle").each(function(i, ell){
                academicTitle.push({
                    "title": $(this).find(".title").val(),
                    "titleDate": $(this).find(".titleDate").val(),
                    "id": ell.id.replace("academicTitle_", "")
                });
            });
            formData.append("academicTitle", JSON.stringify(academicTitle));
            //Конец добавления данных о научных званиях
            //Начадл добавления данных о наградах
            $(".academicReward").each(function(i, ell){
                academicReward.push({
                    "reward": $(this).find(".reward").val(),
                    "rewardDate": $(this).find(".rewardDate").val(),
                    "id": ell.id.replace("academicReward_", "")
                });
            });
            formData.append("academicReward", JSON.stringify(academicReward));
            //Конец добавления данных о наградах
            //Начадл добавления данных о достижениях
            $(".attainmentBlock").each(function(i, ell){
                attainment.push({
                    "attainment": $(this).find(".attainment").val(),
                    "attainmentDate": $(this).find(".attainmentDate").val(),
                    "id": ell.id.replace("attainmentBlock_", "")
                });
            });
            formData.append("attainment", JSON.stringify(attainment));
            //Конец добавления данных о достижениях
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
                    "id": ell.id.replace("videoBlock_", "")
                });
                formData.append("video_" + i, $(this).find(".video")[0].files[0]);
            });
            formData.append("video", JSON.stringify(video));
            //Конец добавления данных о видео
            //Начадл добавления данных о автобиографии
            $(".autobiographyBlock").each(function(i, ell){
                autobiography.push({
                    "id": ell.id.replace("autobiographyBlock_", "")
                });
                formData.append("autobiography_" + i, $(this).find(".autobiography")[0].files[0]);
            });
            formData.append("autobiography", JSON.stringify(autobiography));
            //Конец добавления информации о автобиографии
            formData.append('image', $("#empImg")[0].files[0]);
            formData.append('titlePersonalFile', $("#titlePersonalFile")[0].files[0]);
            
            $('[data-field]').each(function(i, ell){
                formData.append(ell.id, $(this).val());
            });
            let res = $.ajax({
                type: "POST",
                url: "{{ route('add_employee') }}",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    stopLoading();
                    if (data.success) {
                        $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                        $('#counter').text(Number($('#counter').text()) + 1);
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
                        $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                    }
                    scrollTop();
                },
                error: function(data){
                    stopLoading();
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                    scrollTop();
                }
            });
            if(res.status == 0){
                $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
            }
            scrollTop();
            event.preventDefault();
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
            $(".educations").slideUp(300, function(){ $(this).remove()});
            $(".academicDegree").slideUp(300, function(){ $(this).remove()});
            $(".academicTitle").slideUp(300, function(){ $(this).remove()});
            $(".academicReward").slideUp(300, function(){ $(this).remove()});
            $(".attainmentBlock").slideUp(300, function(){ $(this).remove()});
            $(".photoBlock").slideUp(300, function(){ $(this).remove()});
            $(".videoBlock").slideUp(300, function(){ $(this).remove()});
            $(".unitBlock").slideUp(300, function(){ $(this).remove()});
            $(".autobiographyBlock").slideUp(300, function(){ $(this).remove()});
            educationCount = 0;
            academicDegreeCount = 0;
            academicTitleCount = 0;
            academicRewardCount = 0;
            attainmentCount = 0;
            photoCount = 0;
            videoCount = 0;
            unitCount = 0;
            autobiographyCount = 0;
        }
    });
</script>