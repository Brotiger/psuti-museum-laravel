<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> файлов</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами выпускников</h1>
            @if($graduates->count() > 0)
            <div class="row mb-1">
                <span class="col-9"><small>Для того что бы просмотреть подробную информацию о выпускнике нажмите на него</small></span>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Регистрационный номер</th>
                        <th>Дата рождения</th>
                        <th>Год поступления</th>
                        <th colspan="2">Год окончания</th>
                    </tr>
                    <tr>
                        <th><input type="text" class="form-control" placeholder="Фамилия" filter-field id="lastName" autocomplete="off"></th>
                        <th><input type="text" class="form-control" placeholder="Имя" filter-field id="firstName" autocomplete="off"></th>
                        <th><input type="text" class="form-control" placeholder="Отчество" filter-field id="secondName" autocomplete="off"></th>
                        <th><input type="number" class="form-control" placeholder="Номер" filter-field id="registrationNumber" autocomplete="off"></th>
                        <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="dateBirthdayFrom"><input type="date" class="form-control" placeholder="По:" filter-field id="dateBirthdayTo"></th>
                        <th><input type="number" class="form-control mb-1" placeholder="С:" filter-field id="enteredYearFrom"><input type="number" class="form-control" placeholder="По:" filter-field id="enteredYearTo"></th>
                        <th><input type="number" class="form-control mb-1" placeholder="С:" filter-field id="exitYearFrom"><input type="number" class="form-control" placeholder="По:" filter-field id="exitYearTo"></th>
                        <th><button class="form-control btn btn-danger mb-1" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                    </tr>
                </thead>
                <tbody id="graduatesTable">
                    @foreach($graduates as $graduate)
                        <tr class="recordRow" graduate-id="{{ $graduate->id }}">
                            <td>{{ $graduate->lastName }}</td>
                            <td>{{ $graduate->firstName }}</td>
                            <td>{{ $graduate->secondName }}</td>
                            <td>{{ $graduate->registrationNumber }}</td>
                            <td>{{ !empty($graduate->dateBirthday)? date('m-d-Y', strtotime($graduate->dateBirthday)) : '' }}</td>
                            <td>{{ $graduate->enteredYear }}</td>
                            <td colspan=2>{{ $graduate->exitYear }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>
                Здесь будет отображаться список добавленных вами выпускники, что бы добавить выпускников перейдите на подвкладку <strong>добавить выпускников</strong>
            </p>
            @endif
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        $('#graduatesTable').delegate('.recordRow', 'click', function(){
            let graduateId = $(this).attr('graduate-id')
            window.location.href = 'more_graduate' + '/' + graduateId;
        });

        $('#search').on('click', function(){
            startLoading();
            var formData = {};

            $('[filter-field]').each(function(i, ell){
                formData[ell.id] = $(this).val();
            });

            let res = $.ajax({
                url: "{{route('graduates_list')}}",
                type: "GET",
                processData: true,
                contentType: false,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    stopLoading();
                    let positionParameters = location.pathname.indexOf('?');
                    let url = location.pathname.substring(positionParameters, location.pathname.length);
                    let newUrl = url + '?';
                    for(key in formData){
                        if(formData[key]){
                            newUrl += key + "=" + formData[key] + "&";
                        }
                    }
                    newUrl = newUrl.slice(0, -1);
                    history.pushState({}, '', newUrl);

                    $('#graduatesTable').html(data)
                },
                error: function(data){
                    stopLoading();
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).val("");
            });
            $('#search').click();
        });
    });
</script>