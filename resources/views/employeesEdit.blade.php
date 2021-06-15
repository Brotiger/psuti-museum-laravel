<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> сотрудников</div>
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о сотруднике успешно обновлена.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, сократите количество прикрепляемых файлов.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        @include('components.addHref')
        <form enctype="multipart/form-data" id="addEmpForm" class="editEmpForm mt-5">
            <h1 class="h1">Редактирование сотрудника</h1>
            <div class="my-4">
                <h2 class="h2 mb-4">Персональная информация</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}. Новый файл заменит существующий</small></span>
                    </div>
                    <div class="row">
                        <label for="empImg" class="col-sm-3 col-form-label">Фото</label>
                        <div class="col-sm-9" id="imgEmp">
                            @if($employee->img)
                                <div class="mb-2">
                                    <img src="{{ '/storage/'.$employee->img }}" class="mb-1">
                                    <button class="btn btn-danger delete" type="button" id="deleteImg">Удалить</button>
                                </div>
                            @endif
                            <input type="file" name="image" id="empImg" form-field accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">
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
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="14" placeholder="Описание" data-field form-field>{{ !empty($employee)? $employee->description : '' }}</textarea>
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
                <h2 class="h2 my-4">Фотографии</h2>
                <p class="mb-4">Для добавления фотографии нажмите кнопку <strong>добавить</strong></p>
                <ul id="photoList">
                    @foreach($employee->photos as $index => $photo)
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
                                <input class="form-control photoName" type="text" id="photoName_{{ $index }}" placeholder="Название фотографии" autocomplete="off" value="{{ $photo->photoName }}" disabled>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                        <label for="photoDate_{{ $index }}" class="col-3 col-form-label">Дата фотографии</label>
                            <div class="col-sm-9">
                                <input class="form-control photoDate" type="date" id="photoDate_{{ $index }}" placeholder="Дата фотографии" value="{{ $photo->photoDate }}" disabled>
                            </div>
                        </div>
                        <button class="btn btn-danger delete" type="button" photo-id="{{$photo->id}}">Удалить</button>
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
                    @foreach($employee->videos as $index => $video)
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
                                <input class="form-control videoName" type="text" id="videoName_{{ $index }}" placeholder="Название видео" autocomplete="off" disabled value="{{ $video->videoName }}">
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <label for="videoDate_{{ $index }}" class="col-3 col-form-label">Дата видео</label>
                            <div class="col-sm-9">
                                <input class="form-control videoDate" type="date" id="videoDate_{{ $index }}" placeholder="Дата видео" disabled value="{{ $video->videoDate }}">
                            </div>
                        </div>
                        <button class="btn btn-danger delete" type="button" video-id="{{$video->id}}">Удалить</button>
                    </li>
                    @endforeach
                </ul>
                <button class="btn btn-primary" type="button" id="addVideo">Добавить</button>
            </div>
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Личное дело</h2>
                <div class="mb-3">
                    <div class="row mb-1">
                        <span class="offset-3 col-9"><small>Максимальный вес файла: {{ $file_size }} КБ. Допустимые расширения: {{ $file_ext }}. Новый файл заменит существующий</small></span>
                    </div>
                    <div class="mb row">
                        <label for="titlePersonalFile" class="col-sm-3 col-form-label">Титульный лист</label>
                        <div class="col-sm-9" id="personalFile">
                            @if($personals)
                                <div class="mb-2">
                                    <img src="{{ '/storage/'.$personals }}" class="mb-1">
                                    <button class="btn btn-danger delete" type="button" id="deletePersonalFile">Удалить</button>
                                </div>
                            @endif
                            <input type="file" id="titlePersonalFile" form-field accept="{{  '.'.str_replace(', ', ', .', $file_ext) }}">
                        </div>
                    </div>
                </div>
                <h3 class="h4 my-4">Автобиография</h3>
                <p class="mb-4">Для добавления информации о автобиографии нажмите кнопку <strong>добавить</strong></p>
                <ul id="autobiographyList">
                    @foreach($employee->autobiographys as $index => $autobiography)
                    <li class="my-4 autobiographyBlockOld" id="autobiographyBlock_{{ $index }}">
                        <div class="mb-3">
                            <div class="row">
                                <label for="autobiography_{{ $index }}" class="col-sm-3 col-form-label">Лист</label>
                                <div class="col-sm-9">
                                    <img src="{{ '/storage/'.$autobiography->file }}">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-danger delete" type="button" autobiography-id="{{ $autobiography->id }}">Удалить</button>
                    </li>
                    @endforeach
                </ul>
                <button class="btn btn-primary" type="button" id="addAutobiography">Добавить</button>
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
    $(document).ready(function(){
        var educationCount = {{ !empty($employee)? $employee->educations->count(): 0}};
        var academicDegreeCount = {{ !empty($employee)? $employee->degrees->count(): 0}};
        var academicTitleCount = {{ !empty($employee)? $employee->titles->count(): 0}};
        var academicRewardCount = {{ !empty($employee)? $employee->rewards->count(): 0}};
        var attainmentCount = {{ !empty($employee)? $employee->attainments->count(): 0}};
        var unitCount = {{ !empty($employee)? $employee->units->count(): 0}};
        var photoCount = {{ !empty($employee)? $employee->photos->count(): 0}};
        var videoCount = {{ !empty($employee)? $employee->videos->count(): 0}};
        var autobiographyCount = {{ !empty($employee)? $employee->autobiographys->count(): 0}};
        var photoToDelete = [];
        var videoToDelete = [];
        var autobiographyToDelete = [];
        var deleteImg = false;
        var deletePersonalFile = false;

        $("form").delegate("#photoList input[type='file']", "change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $photo_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $("form").delegate("#autobiographyList input[type='file']", "change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $file_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $("#titlePersonalFile").on("change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $file_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $("#empImg").on("change", function(e){
            if(e.currentTarget.files[0] && e.currentTarget.files[0].size > {{ $photo_size * 1024 }} ){
                if(!$(this).hasClass('errorField')){
                    $(this).addClass('errorField');
                }
            }
        });

        $(".editEmpForm").delegate(".delete", "click", function(){
            if($(this).attr('photo-id')){
                photoToDelete.push($(this).attr('photo-id'));
            }
            if($(this).attr('video-id')){
                videoToDelete.push($(this).attr('video-id'));
            }
            if($(this).attr('autobiography-id')){
                autobiographyToDelete.push($(this).attr('autobiography-id'));
            }

            if($(this).attr('id') == 'deleteImg'){
                deleteImg = true;
            }

            if($(this).attr('id') == 'deletePersonalFile'){
                deletePersonalFile = true;
            }
            
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
                        + '<input type="file" name="photo" id="autobiography_'+ autobiographyCount +'" class="autobiography" accept="{{  '.'.str_replace(', ', ', .', $file_ext) }}">'
                    + '</div>'
                    + '</div>'
                + '</div>'
                + '<button class="btn btn-danger delete" type="button">Удалить</button>'
            + '</li>');
            $("#autobiographyBlock_" + autobiographyCount).slideDown(300);
            autobiographyCount++;
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
            var photo = [];
            var video = [];
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
                    "video": $(this).find(".video").val(),
                    "id": ell.id.replace("videoBlock_", "")
                });
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
            formData.append('photoToDelete', photoToDelete);
            formData.append('videoToDelete', videoToDelete);
            formData.append('autobiographyToDelete', autobiographyToDelete);
            if(deleteImg){
                formData.append('deleteImg', true);
            }
            if(deletePersonalFile){
                formData.append('deletePersonalFile', true);
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
                        $('#photoList').html(data.photos);
                        $('#imgEmp').html(data.imgEmp);
                        $('#videoList').html(data.videos);
                        $('#autobiographyList').html(data.autobiographys);
                        $('#personalFile').html(data.personalFile);
                        if (data.success) {
                            $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                            photoToDelete = [];
                            videoToDelete = [];
                            autobiographyToDelete = [];
                            deleteImg = false;
                            deletePersonalFile = false;
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