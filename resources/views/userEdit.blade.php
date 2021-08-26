<x-app-layout>
    <div class="container">
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о пользователи успешно обновлена.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, сократите количество прикрепляемых файлов.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        <form enctype="multipart/form-data" id="editUserForm" class="editUserForm mt-5">
            <h1 class="h1">Администрирование</h1>
            <div class="my-4">
                <h2 class="h2 mb-4">Пользователь</h2>
                <div class="form-group mb-3 row">
                    <label for="name" class="col-3 col-form-label">ФИО</label>
                    <div class="col-sm-9 mb-3">
                        <input class="form-control" type="text" id="name" disabled value="{{ $editUser->name }}">
                    </div>
                    <label for="email" class="col-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" id="email" disabled value="{{ $editUser->email }}">
                    </div>
                </div>
            </div>
            @if($root)
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Права администрирования</h2>
                <div class="form-group mb-3 row">
                    <label for="empAdmin" class="col-3 col-form-label">Сотрудники</label>
                    <div class="col-sm-9 mb-3">
                        <input class="form-control" type="date" id="empAdmin" data-field form-field value="{{ $editUser->rights->empAdmin }}">
                    </div>
                    <label for="unitAdmin" class="col-3 col-form-label">Подразделения</label>
                    <div class="col-sm-9 mb-3">
                        <input class="form-control" type="date" id="unitAdmin" data-field form-field value="{{ $editUser->rights->unitAdmin }}">
                    </div>
                    <label for="eventAdmin" class="col-3 col-form-label">События</label>
                    <div class="col-sm-9 mb-3">
                        <input class="form-control" type="date" id="eventAdmin" data-field form-field value="{{ $editUser->rights->eventAdmin }}">
                    </div>
                    <label for="graduateAdmin" class="col-3 col-form-label">Выпускники</label>
                    <div class="col-sm-9 mb-3">
                        <input class="form-control" type="date" id="graduateAdmin" data-field form-field value="{{ $editUser->rights->graduateAdmin }}">
                    </div>
                    <label for="heroAdmin" class="col-3 col-form-label">Герои</label>
                    <div class="col-sm-9 mb-3">
                        <input class="form-control" type="date" id="heroAdmin" data-field form-field value="{{ $editUser->rights->heroAdmin }}">
                    </div>
                    <label for="pageAdmin" class="col-3 col-form-label">Страницы</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" id="pageAdmin" data-field form-field value="{{ $editUser->rights->pageAdmin }}">
                    </div>
                </div>
            </div>
            @endif
            <div class="my-4">
                <hr>
                <h2 class="h2 my-4">Лимиты</h2>
                <div class="form-group mb-3 row">
                    @if($access['empAdmin'] || $root)
                        <label for="empLimit" class="col-3 col-form-label">Сотрудники</label>
                        <div class="col-sm-9 mb-3">
                            <input class="form-control" type="number" id="empLimit" data-field form-field value="{{ $editUser->limits->empLimit }}">
                        </div>
                    @endif
                    @if($access['unitAdmin'] || $root)
                        <label for="unitLimit" class="col-3 col-form-label">Подразделения</label>
                        <div class="col-sm-9 mb-3">
                            <input class="form-control" type="number" id="unitLimit" data-field form-field value="{{ $editUser->limits->unitLimit }}">
                        </div>
                    @endif
                    @if($access['eventAdmin'] || $root)
                        <label for="eventLimit" class="col-3 col-form-label">События</label>
                        <div class="col-sm-9 mb-3">
                            <input class="form-control" type="number" id="eventLimit" data-field form-field value="{{ $editUser->limits->eventLimit }}">
                        </div>
                    @endif
                    @if($access['eventAdmin'] || $root)
                        <label for="eventFileLimit" class="col-3 col-form-label">События (Файл)</label>
                        <div class="col-sm-9 mb-3">
                            <input class="form-control" type="number" id="eventFileLimit" data-field form-field value="{{ $editUser->limits->eventFileLimit }}">
                        </div>
                    @endif
                    @if($access['graduateAdmin'] || $root)
                        <label for="graduateFileLimit" class="col-3 col-form-label">Выпускники (Файл)</label>
                        <div class="col-sm-9 mb-3">
                            <input class="form-control" type="number" id="graduateFileLimit" data-field form-field value="{{ $editUser->limits->graduateFileLimit }}">
                        </div>
                    @endif
                    @if($access['heroAdmin'] || $root)
                        <label for="heroLimit" class="col-3 col-form-label">Герои</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="number" id="heroLimit" data-field form-field value="{{ $editUser->limits->heroLimit }}">
                        </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
<script src="/js/hideMessage.js"></script>
<script>
    $(document).ready(function(){

        $(".editUserForm").delegate("input", "click", function(){
            $(this).removeClass("errorField");
        });

        $("#editUserForm").submit(function(event){
            startLoading();
            let formData = new FormData();
            
            $('[data-field]').each(function(i, ell){
                formData.append(ell.id, $(this).val());
            });

            $('#error-global-message, #success-message, #error-message, #error-body-message').hide();

            let sizeCount = 0;

            for(let pair of formData.entries()) {
                sizeCount += (typeof pair[1] === "string") ? pair[1].length : pair[1].size;
            }

            formData.append('id', '{{ $id }}');

            if(sizeCount < @php echo env("MAX_BODY_SIZE", 0) @endphp * 1024){
                let res = $.ajax({
                    type: "POST",
                    url: "{{ route('update_user') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    tataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data){
                        if (data.success) {
                            $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                            resetForm();
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

        function resetForm(){
            $("[form-field]").each(function(){
                $(this).removeClass("errorField");
            });
        }
    });
</script>