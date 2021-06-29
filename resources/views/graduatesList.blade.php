<x-app-layout>
    @if($admin)
        @include('components.deleteRecord')
        <input type="hidden" value="{{ route('delete_graduate') }}" id="routeToDelete">
    @endif
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами выпускников</h1>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Регистрационный номер</th>
                        <th>Дата рождения</th>
                        <th>Год поступления</th>
                        <th>Год окончания</th>
                        <th colspan="2">Действия</th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{route('graduates_list')}}">
                            <th><input type="text" class="form-control" placeholder="Фамилия" filter-field id="lastName" autocomplete="off" name="lastName" value="{{ request()->input('lastName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Имя" filter-field id="firstName" autocomplete="off" name="firstName" value="{{ request()->input('firstName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Отчество" filter-field id="secondName" autocomplete="off" name="secondName" value="{{ request()->input('secondName') }}"></th>
                            <th><input type="number" class="form-control" placeholder="Номер" filter-field id="registrationNumber" autocomplete="off" name="registrationNumber" value="{{ request()->input('registrationNumber') }}"></th>
                            <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="dateBirthdayFrom" name="dateBirthdayFrom" value="{{ request()->input('dateBirthdayFrom') }}"><input type="date" class="form-control" placeholder="По:" filter-field id="dateBirthdayTo" name="dateBirthdayTo" value="{{ request()->input('dateBirthdayTo') }}"></th>
                            <th><input type="number" class="form-control mb-1" placeholder="С:" filter-field id="enteredYearFrom" name="enteredYearFrom" value="{{ request()->input('enteredYearFrom') }}"><input type="number" class="form-control" placeholder="По:" filter-field id="enteredYearTo" name="enteredYearTo" value="{{ request()->input('enteredYearTo') }}"></th>
                            <th><input type="number" class="form-control mb-1" placeholder="С:" filter-field id="exitYearFrom" name="exitYearFrom" value="{{ request()->input('exitYearFrom') }}"><input type="number" class="form-control" placeholder="По:" filter-field id="exitYearTo" name="exitYearTo" value="{{ request()->input('exitYearTo') }}"></th>
                            <th width="40"><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th width="40"><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($graduates->count() > 0)
                <tbody id="graduatesTable">
                    @foreach($graduates as $graduate)
                        <tr class="recordRow" row-record-id="{{ $graduate->id }}">
                            <td>{{ $graduate->lastName }}</td>
                            <td>{{ $graduate->firstName }}</td>
                            <td>{{ $graduate->secondName }}</td>
                            <td>{{ $graduate->registrationNumber }}</td>
                            <td>{{ !empty($graduate->dateBirthday)? date('m-d-Y', strtotime($graduate->dateBirthday)) : '' }}</td>
                            <td>{{ $graduate->enteredYear }}</td>
                            <td>{{ $graduate->exitYear }}</td>
                            <td width="40">
                                @if($admin)
                                    <button class="form-control btn btn-danger" deleteRecord record-id="{{ $graduate->id }}"><i class="bi bi-trash-fill"></i></button>
                                @endif
                            </td>
                            <td width="40"><button class="form-control btn btn-primary" viewRecord record-id="{{ $graduate->id }}"><i class="bi bi-pencil-square"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($graduates->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $graduates->appends($next_query)->links() }}
    </div>
</x-app-layout>
@if($admin)
    @include('components.js.deleteRecord')
@endif
<script>
    $(document).ready(function(){
        $('#graduatesTable').delegate('[viewRecord]', 'click', function(){
            let recordId = $(this).attr('record-id')
            window.location.href = '/graduates/{{ $site }}/more/' + recordId;
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>