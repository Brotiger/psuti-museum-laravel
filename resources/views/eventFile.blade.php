<x-app-layout>
    <div class="container">
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">События успешно добавлены.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-limit-message">Ошибка! Лимит на данную таблицу превышен, для увиличения лимита свяжитесь с администратором.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-body-message">Ошибка! Тело запроса превышает максимум который может обработать web сервер, уменьшите вес прикрепляемого файла.<i class="bi bi-x-circle" close></i></div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, сделайте скриншот данного сообщения и отправьте системнному администратором на следующий адрес - @php echo env('ADMIN_MAIL') @endphp.<div id="server-error-file"></div><div id="server-error-line"></div><div id="server-error-message"></div><i class="bi bi-x-circle" close></i></div>
        <form enctype="multipart/form-data" id="addGraduateForm" class="addGraduateForm mt-5">
            <h1 class="h1">Добавление событий из файла</h1>
            <div class="my-4">
                <div class="row mb-1">
                    <span class="offset-3 col-9"><small>Список событий должен быть уникальным, так же файл должен иметь расширение html, иначе данное поле будет выделено красным</small></span>
                </div>
                <div class="row">
                    <label for="file" class="col-sm-3 col-form-label">Файл</label>
                    <div class="col-sm-9">
                        <input type="file" name="file" id="file" form-field accept=".txt">
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group mt-4">
                <button class="btn btn-danger mb-4" type="button" id="reset">Сбросить</button>
                <button class="btn btn-primary mb-4" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</x-app-layout>
<script src="/js/hideMessage.js"></script>
<script>
    $(document).ready(function(){
        $(".addGraduateForm").delegate("input", "click", function(){
            $(this).removeClass("errorField");
        });

        $("#addGraduateForm").submit(function(event){
            let formData = new FormData();
            startLoading();
            formData.append('file', $("#file")[0].files[0]);

            $('#error-global-message, #success-message, #error-limit-message, #error-message, #error-body-message').hide();

            let sizeCount = 0;

            for(let pair of formData.entries()) {
                sizeCount += (typeof pair[1] === "string") ? pair[1].length : pair[1].size;
            }

            if(sizeCount < @php echo env("MAX_BODY_SIZE", 0) @endphp * 1024){
                let res = $.ajax({
                    type: "POST",
                    url: "{{ route('add_event_file') }}",
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
                        stopLoading();
                    },
                    error: function(data){
                        if(data.status == 422){
                            $("#file").addClass("errorField");
                            $('#error-global-message').fadeIn(300).delay(2000).fadeOut(300);
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

        $("#reset").on("click", function(){
            resetForm();
            scrollTop();
            });

        function resetForm(){
            $("[form-field]").each(function(){
                $(this).removeClass("errorField");
                $(this).val("");
            });
        }
    });
</script>