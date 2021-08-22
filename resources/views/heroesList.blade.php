<x-app-layout>
    @if($admin)
        @include('components.deleteRecord')
        <input type="hidden" value="{{ route('delete_hero') }}" id="routeToDelete">
    @endif
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список {{ $admin? '' : 'добавленных вами' }} героев</h1>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Дата рождения</th>
                        <th width="60">Действия</th>
                        <th width="60"></th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{route('heroes_list', [ 'site' => env('DB_SITE')])}}">
                            <th><input type="text" class="form-control" placeholder="Фамилия" filter-field id="lastName" name="lastName" autocomplete="off" value="{{ request()->input('lastName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Имя" filter-field id="firstName" autocomplete="off" name="firstName" value="{{ request()->input('firstName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Отчество" filter-field id="secondName" autocomplete="off" name="secondName" value="{{ request()->input('secondName') }}"></th>
                            <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="dateBirthdayFrom" name="dateBirthdayFrom" value="{{ request()->input('dateBirthdayFrom') }}"><input type="date" class="form-control" placeholder="По:" filter-field id="dateBirthdayTo" name="dateBirthdayTo" value="{{ request()->input('dateBirthdayTo') }}"></th>
                            <th><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($heroes->count() > 0)
                <tbody id="heroesTable">
                    @foreach($heroes as $hero)
                        <tr class="recordRow" row-record-id="{{ $hero->id }}">
                            <td>{{ $hero->lastName }}</td>
                            <td>{{ $hero->firstName }}</td>
                            <td>{{ $hero->secondName }}</td>
                            <td>{{ !empty($hero->dateBirthday)? date('m-d-Y', strtotime($hero->dateBirthday)) : '' }}</td>
                            <td>
                                @if($admin)
                                    <button class="form-control btn btn-danger" deleteRecord record-id="{{ $hero->id }}"><i class="bi bi-trash-fill"></i></button>
                                @endif
                            </td>
                            <td><button class="form-control btn btn-primary" viewRecord record-id="{{ $hero->id }}"><i class="bi bi-pencil-square"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($heroes->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $heroes->appends($next_query)->links() }}
    </div>
</x-app-layout>
@if($admin)
    @include('components.js.deleteRecord')
@endif
<script>
    $(document).ready(function(){
        $('#heroesTable').delegate('[viewRecord]', 'click', function(event){
            let recordId = $(this).attr('record-id')
            window.location.href = '/heroes/{{ $site }}/more/' + recordId;
        });
        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>