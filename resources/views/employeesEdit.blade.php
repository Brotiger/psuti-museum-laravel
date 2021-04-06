<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> сотрудников</div>
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о сотруднике успешно обновлена.</div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <form enctype="multipart/form-data" id="addEmpForm" class="editEmpForm mt-5">
            <h1 class="h1">Редактирование сотрудника</h1>
            <div class="my-4">
                <h2 class="h2 mb-4">Персональная информация</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Новый файл заменит существующий</small></span>
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
                        <input type="text" class="form-control" id="lastName" placeholder="Фамилия" data-field form-field autocomplete="off" value="{{ !empty($employee)? $employee->lastName : '' }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="firstName" class="col-sm-3 col-form-label">Имя*</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="firstName" placeholder="Имя" data-field form-field autocomplete="off" value="{{ !empty($employee)? $employee->firstName : '' }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="secondName" class="col-sm-3 col-form-label">Отчество</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="secondName" placeholder="Отчество" data-field form-field autocomplete="off" value="{{ !empty($employee)? $employee->secondName : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="dateBirthday" class="col-3 col-form-label">Дата рождения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="dateBirthday" data-field form-field value="{{ !empty($employee)? $employee->dateBirthday : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="hired" class="col-3 col-form-label">Дата приема</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="hired" data-field form-field value="{{ !empty($employee)? $employee->hired : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="fired" class="col-3 col-form-label">Дата увольнения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="fired" data-field form-field value="{{ !empty($employee)? $employee->fired : '' }}">
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="description" class="col-3 col-form-label">Описание</label>
                    <div class="col-sm-9">
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="7" placeholder="Описание" data-field form-field>{{ !empty($employee)? $employee->description : '' }}</textarea>
                    </div>
                </div>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Образование</h2>
                <p class="mb-4">Для добавления информации о образовании нажмите кнопку <strong>добавить</strong></p>
                <ul id="educationList">
                    @if(!empty($employee))
                        @foreach($employee->educations as $index => $education)
                            <li class="my-4 educations" id="education_{{ $index }}">
                                <div class="form-group mb-3 row">
                                <label for="university_{{ $index }}" class="col-3 col-form-label">ВУЗ*</label>
                                    <div class="col-sm-9">
                                        <input class="form-control university" type="text" id="university_{{ $index }}" placeholder="ВУЗ" autocomplete="off" value="{{ $education->university }}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                <label for="specialty_{{ $index }}" class="col-3 col-form-label">Специальность</label>
                                    <div class="col-sm-9">
                                        <input class="form-control specialty" type="text" id="specialty_{{ $index }}" placeholder="Специальность" autocomplete="off" value="{{ $education->specialty }}">
                                    </div>
                                </div> 
                                <div class="form-group mb-3 row">
                                <label for="expirationDate_{{ $index }}" class="col-3 col-form-label">Дата окончания</label>
                                    <div class="col-sm-9">
                                        <input class="form-control expirationDate" type="date" id="expirationDate_{{ $index }}" placeholder="Дата окончания" value="{{ $education->expirationDate }}">
                                    </div>
                                </div>
                                <button class="btn btn-danger delete" type="button">Удалить</button>
                            </li>
                        @endforeach
                    @endif
                </ul>
                <button class="btn btn-primary" type="button" id="addEducation">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Подразделения</h2>
                <p class="mb-4">Для добавления информации о подразделениях нажмите кнопку <strong>добавить</strong></p>
                <ul id="unitList">
                @if(!empty($employee))
                    @foreach($employee->units as $index => $eu)
                    <li class="my-4 unitBlock" id="unitBlock_{{ $index }}">
                        <div class="form-group mb-3 row">
                        <label for="unit_{{ $index }}" class="col-3 col-form-label">Подразделение*</label>
                            <div class="col-sm-9">
                                <select class="unit custom-select custom-select-lg form-control border border-secondary rounded-0" id="unit_{{ $index}}">
                                    <option value="">Не выбрано</option>
                                    @foreach($units as $unit)
                                        <option {{$eu->unit_id == $unit->id? 'selected': ''}} value="{{ $unit->id }}">{{ $unit->fullUnitName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                        <label for="post_{{ $index }}" class="col-3 col-form-label">Должност</label>
                            <div class="col-sm-9">
                                <input class="form-control post" type="text" id="post_{{ $index }}" placeholder="Должность" autocomplete="off" value="{{ $eu->post }}">
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                        <label for="recruitmentDate_{{ $index }}" class="col-3 col-form-label">Дата приема</label>
                            <div class="col-sm-9">
                                <input class="form-control recruitmentDate" type="date" id="recruitmentDate_{{ $index }}" placeholder="Дата приема" value="{{ $eu->recruitmentDate }}">
                            </div>
                        </div>
                        <button class="btn btn-danger delete" type="button">Удалить</button>
                    </li>
                    @endforeach
                @endif
                </ul>
                <button class="btn btn-primary" type="button" id="addUnit">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Ученые степени</h2>
                <p class="mb-4">Для добавления ученой степени нажмите кнопку <strong>добавить</strong></p>
                <ul id="academicDegreeList">
                    @if(!empty($employee))
                        @foreach($employee->degrees as $index => $degree)
                        <li class="my-4 academicDegree" id="academicDegree_{{ $index }}">
                            <div class="form-group mb-3 row">
                                <label for="degree_{{ $index }}" class="col-3 col-form-label">Ученая степень*</label>
                                <div class="col-sm-9">
                                    <input class="form-control degree" type="text" id="degree_{{ $index }}" placeholder="Ученая степень" autocomplete="off" value="{{ $degree->degree }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label for="assignmentDate_{{ $index }}" class="col-3 col-form-label">Дата присвоения</label>
                                <div class="col-sm-9">
                                    <input class="form-control assignmentDate" type="date" id="assignmentDate_{{ $index }}" placeholder="Дата присвоения" value="{{ $degree->assignmentDate }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label for="topic_{{ $index }}" class="col-3 col-form-label">Тема диссертации</label>
                                <div class="col-sm-9">
                                    <input class="form-control topic" type="text" id="topic_{{ $index }}" placeholder="Тема диссертации" autocomplete="off" value="{{ $degree->topic }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label for="universityDefense_{{ $index }}" class="col-3 col-form-label">ВУЗ</label>
                                <div class="col-sm-9">
                                    <input class="form-control universityDefense" type="text" id="universityDefense_{{ $index }}" placeholder="ВУЗ" autocomplete="off" value="{{ $degree->universityDefense }}">
                                </div>
                            </div>
                            <button class="btn btn-danger delete" type="button">Удалить</button>
                        </li>
                        @endforeach
                    @endif
                </ul>
                <button class="btn btn-primary" type="button" id="addAcademicDegree">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Ученые звания</h2>
                <p class="mb-4">Для добавления ученого звания нажмите кнопку <strong>добавить</strong></p>
                <ul id="academicTitleList">
                    @if(!empty($employee))
                        @foreach($employee->titles as $index => $title)
                        <li class="my-4 academicTitle" id="academicTitle_{{ $index }}">
                            <div class="form-group mb-3 row">
                            <label for="title_{{ $index }}" class="col-3 col-form-label">Ученое звание*</label>
                                <div class="col-sm-9">
                                    <input class="form-control title" type="text" id="title_{{ $index }}" placeholder="Ученое звание" autocomplete="off" value="{{ $title->title }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                            <label for="titleDate_{{ $index }}" class="col-3 col-form-label">Дата присвоения</label>
                                <div class="col-sm-9">
                                    <input class="form-control titleDate" type="date" id="titleDate_{{ $index }}" placeholder="Дата присвоения" value="{{ $title->titleDate }}">
                                </div>
                            </div>
                            <button class="btn btn-danger delete" type="button">Удалить</button>
                        </li>
                        @endforeach
                    @endif
                </ul>
                <button class="btn btn-primary" type="button" id="addAcademicTitle">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Награды</h2>
                <p class="mb-4">Для добавления наград нажмите кнопку <strong>добавить</strong></p>
                <ul id="academicRewardList">
                    @if(!empty($employee))
                        @foreach($employee->rewards as $index => $reward)
                        <li class="my-4 academicReward" id="academicReward_{{ $index }}">
                            <div class="form-group mb-3 row">
                            <label for="reward_{{ $index }}" class="col-3 col-form-label">Награда*</label>
                                <div class="col-sm-9">
                                    <input class="form-control reward" type="text" id="reward_{{ $index }}" placeholder="Награда" autocomplete="off" value="{{ $reward->reward }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                            <label for="rewardDate_{{ $index }}" class="col-3 col-form-label">Дата присвоения</label>
                                <div class="col-sm-9">
                                    <input class="form-control rewardDate" type="date" id="rewardDate_{{ $index }}" placeholder="Дата присвоения" value="{{ $reward->rewardDate }}">
                                </div>
                            </div>
                            <button class="btn btn-danger delete" type="button">Удалить</button>
                        </li>
                        @endforeach
                    @endif
                </ul>
                <button class="btn btn-primary" type="button" id="addAcademicReward">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Достижения</h2>
                <p class="mb-4">Для добавления достижения нажмите кнопку <strong>добавить</strong></p>
                <ul id="attainmentList">
                    @if(!empty($employee))
                        @foreach($employee->attainments as $index => $attainment)
                        <li class="my-4 attainmentBlock" id="attainmentBlock_{{ $index }}">
                            <div class="form-group mb-3 row">
                            <label for="attainment_{{ $index }}" class="col-3 col-form-label">Достижение*</label>
                                <div class="col-sm-9">
                                    <input class="form-control attainment" type="text" id="attainment_{{ $index }}" placeholder="Достижение" autocomplete="off" value="{{ $attainment->attainment }}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                            <label for="attainmentDate_{{ $index }}" class="col-3 col-form-label">Дата достижения</label>
                                <div class="col-sm-9">
                                    <input class="form-control attainmentDate" type="date" id="attainmentDate_{{ $index }}" placeholder="Дата достижения" value="{{ $attainment->attainmentDate }}">
                                </div>
                            </div>
                            <button class="btn btn-danger delete" type="button">Удалить</button>
                        </li>
                        @endforeach
                    @endif
                </ul>
                <button class="btn btn-primary" type="button" id="addAttainment">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Личное дело</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $file_size }} КБ. Новый файл заменит существующий</small></span>
                    </div>
                    <div class="mb row">
                        <label for="titlePersonalFile" class="col-sm-3 col-form-label">Титульный лист</label>
                        <div class="col-sm-9">
                            <input type="file" id="titlePersonalFile" form-field>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        var educationCount = {{ !empty($employee)? $employee->educations->count(): 0}};
        var academicDegreeCount = {{ !empty($employee)? $employee->degrees->count(): 0}};
        var academicTitleCount = {{ !empty($employee)? $employee->titles->count(): 0}};
        var academicRewardCount = {{ !empty($employee)? $employee->rewards->count(): 0}};
        var attainmentCount = {{ !empty($employee)? $employee->attainments->count(): 0}};
        var unitCount = {{ !empty($employee)? $employee->units->count(): 0}};

        $(".editEmpForm").delegate(".delete", "click", function(){
            var $parent = $(this).parent();
            $parent.slideUp(300, function(){ $(this).remove()});
        });

        $(".editEmpForm").delegate("input", "click", function(){
                $(this).removeClass("errorField");
            });

        $(".editEmpForm").delegate("select", "click", function(){
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

            academicDegreeCount++;
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

        $("#addEmpForm input").click(function(){
            $(this).removeClass("errorField");
        });

        $("#addEmpForm").submit(function(event){
            startLoading();
            let formData = new FormData();
            var educations = [];
            var academicDegree = [];
            var academicTitle = [];
            var academicReward = [];
            var attainment = [];
            var unit = [];

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

            formData.append('image', $("#empImg")[0].files[0]);
            formData.append('titlePersonalFile', $("#titlePersonalFile")[0].files[0]);

            formData.append('id', '{{ $id }}');
            
            $('[data-field]').each(function(i, ell){
                formData.append(ell.id, $(this).val());
            });

            let res = $.ajax({
                type: "POST",
                url: "{{ route('update_employee') }}",
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
                    } else if(data.errors){
                        $('#error-global-message').fadeIn(300).delay(2000).fadeOut(300);
                        data.errors.forEach(function(ell){
                            $("#" + ell).addClass("errorField");
                        });
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
    });
</script>