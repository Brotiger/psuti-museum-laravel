<x-app-layout>
    @if($admin)
        @include('components.deleteRecord')
        <input type="hidden" value="{{ route('delete_employee') }}" id="routeToDelete">
    @endif
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами сотрудников</h1>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Дата рождения</th>
                        <th>Дата приема</th>
                        <th>Дата увольнения</th>
                        <th width="60">Действия</th>
                        <th width="60"></th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{route('employees_list', [ 'site' => env('DB_SITE')])}}">
                            <th><input type="text" class="form-control" placeholder="Фамилия" filter-field id="lastName" name="lastName" autocomplete="off" value="{{ request()->input('lastName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Имя" filter-field id="firstName" autocomplete="off" name="firstName" value="{{ request()->input('firstName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Отчество" filter-field id="secondName" autocomplete="off" name="secondName" value="{{ request()->input('secondName') }}"></th>
                            <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="dateBirthdayFrom" name="dateBirthdayFrom" value="{{ request()->input('dateBirthdayFrom') }}"><input type="date" class="form-control" placeholder="По:" filter-field id="dateBirthdayTo" name="dateBirthdayTo" value="{{ request()->input('dateBirthdayTo') }}"></th>
                            <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="hiredFrom" name="hiredFrom" value="{{ request()->input('hiredFrom') }}"><input type="date" class="form-control" placeholder="По:" filter-field id="hiredTo" name="hiredTo" value="{{ request()->input('hiredTo') }}"></th>
                            <th><input type="date" placeholder="С:" class="form-control mb-1" filter-field id="firedFrom" name="firedFrom" value="{{ request()->input('firedFrom') }}"><input type="date" placeholder="По:" class="form-control" filter-field id="firedTo" name="firedTo" value="{{ request()->input('firedTo') }}"></th>
                            <th><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($employees->count() > 0)
                <tbody id="employeesTable">
                    @foreach($employees as $employee)
                        <tr class="recordRow" row-record-id="{{ $employee->id }}">
                            <td>{{ $employee->lastName }}</td>
                            <td>{{ $employee->firstName }}</td>
                            <td>{{ $employee->secondName }}</td>
                            <td>{{ !empty($employee->dateBirthday)? date('m-d-Y', strtotime($employee->dateBirthday)) : '' }}</td>
                            <td>{{ !empty($employee->hired)? date('m-d-Y', strtotime($employee->hired)) : '' }}</td>
                            <td>{{ !empty($employee->fired)? date('m-d-Y', strtotime($employee->fired)) : '' }}</td>
                            <td>
                                @if($admin)
                                    <button class="form-control btn btn-danger" deleteRecord record-id="{{ $employee->id }}"><i class="bi bi-trash-fill"></i></button>
                                @endif
                            </td>
                            <td><button class="form-control btn btn-primary" viewRecord record-id="{{ $employee->id }}"><i class="bi bi-pencil-square"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($employees->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $employees->appends($next_query)->links() }}
    </div>
</x-app-layout>
@if($admin)
    @include('components.js.deleteRecord')
@endif
<script>
    $(document).ready(function(){
        $('#employeesTable').delegate('[viewRecord]', 'click', function(event){
            let recordId = $(this).attr('record-id')
            window.location.href = '/employees/{{ $site }}/more/' + recordId;
        });
        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>