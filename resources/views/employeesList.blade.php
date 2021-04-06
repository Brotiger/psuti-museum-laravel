<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> сотрудников</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами сотрудников</h1>
            @if($employees->count() > 0)
            <div class="row mb-1">
                <span class="col-9"><small>Для того что бы отредактировать информацию о сотруднике нажмите на него</small></span>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Дата рождения</th>
                        <th>Дата приема</th>
                        <th colspan="2">Дата увольнения</th>
                    </tr>
                    <tr>
                        <th><input type="text" class="form-control" placeholder="Фамилия" filter-field id="lastName" autocomplete="off"></th>
                        <th><input type="text" class="form-control" placeholder="Имя" filter-field id="firstName" autocomplete="off"></th>
                        <th><input type="text" class="form-control" placeholder="Отчество" filter-field id="secondName" autocomplete="off"></th>
                        <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="dateBirthdayFrom"><input type="date" class="form-control" placeholder="По:" filter-field id="dateBirthdayTo"></th>
                        <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="hiredFrom"><input type="date" class="form-control" placeholder="По:" filter-field id="hiredTo"></th>
                        <th><input type="date" placeholder="С:" class="form-control mb-1" filter-field id="firedFrom"><input type="date" placeholder="По:" class="form-control" filter-field id="firedTo"></th>
                        <th><button class="form-control btn btn-danger mb-1" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                    </tr>
                </thead>
                <tbody id="employeesTable">
                    @foreach($employees as $employee)
                        <tr class="recordRow" employee-id="{{ $employee->id }}">
                            <td>{{ $employee->lastName }}</td>
                            <td>{{ $employee->firstName }}</td>
                            <td>{{ $employee->secondName }}</td>
                            <td>{{ !empty($employee->dateBirthday)? date('m-d-Y', strtotime($employee->dateBirthday)) : '' }}</td>
                            <td>{{ !empty($employee->hired)? date('m-d-Y', strtotime($employee->hired)) : '' }}</td>
                            <td colspan="2">{{ !empty($employee->fired)? date('m-d-Y', strtotime($employee->fired)) : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>
                Здесь будет отображаться список добавленных вами сотрудников, что бы добавить сотрудника перейдите на подвкладку <strong>добавить сотрудника</strong>
            </p>
            @endif
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        $('#employeesTable').delegate('.recordRow', 'click', function(){
            let empId = $(this).attr('employee-id')
            window.location.href = 'edit_employee' + '/' + empId;
        });

        $('#search').on('click', function(){
            startLoading();
            var formData = {};

            $('[filter-field]').each(function(i, ell){
                formData[ell.id] = $(this).val();
            });

            let res = $.ajax({
                url: "{{route('employees_list')}}",
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
                    //newUrl += "page={{ isset($_GET['page']) ? $_GET['page'] : 1 }}";
                    history.pushState({}, '', newUrl);

                    $('#employeesTable').html(data)
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