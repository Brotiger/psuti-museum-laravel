<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> подразделений</div>
        <div class="alert alert-success" style="display: none" role="alert" id="success-message">Информация о подразделении успешно обновлена.</div>
        <div class="alert alert-warning" style="display: none" role="alert" id="error-global-message">Ошибка! Некоторые поля заполненны не верно.</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
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
                        <textarea class="form-control border border-secondary rounded-0" id="description" rows="7" placeholder="Описание" data-field form-field>{{ !empty($unit)? $unit->description : '' }}</textarea>
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

        $(".editUnitForm").delegate("input", "click", function(){
                $(this).removeClass("errorField");
        });

        $("#editUnitForm").submit(function(event){
            let formData = new FormData();

            startLoading();

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
                    stopLoading();
                    if (data.success) {
                        $('#success-message').fadeIn(300).delay(2000).fadeOut(300);
                        $('#counter').text(Number($('#counter').text()) + 1);
                        resetForm();
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