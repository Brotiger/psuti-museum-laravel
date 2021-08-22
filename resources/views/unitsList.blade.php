<x-app-layout>
    @if($admin)
        @include('components.deleteRecord')
        <input type="hidden" value="{{ route('delete_unit') }}" id="routeToDelete">
    @endif
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список {{ $admin? '' : 'добавленных вами' }} подразделений</h1>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Полное название подразделения</th>
                        <th>Сокращенное название подразделения</th>
                        <th>Тип подразделения</th>
                        <th>Дата создания</th>
                        <th>Дата прекращения</th>
                        <th width="60">Действия</th>
                        <th width="60"></th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{route('units_list', [ 'site' => env('DB_SITE')])}}">
                            <th><input type="text" class="form-control" placeholder="Полное название подразделения" filter-field id="fullUnitName" autocomplete="off" name="fullUnitName" value="{{ request()->input('fullUnitName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Сокращенное название подразделения" filter-field id="shortUnitName" autocomplete="off" name="shortUnitName" value="{{ request()->input('shortUnitName') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Тип подразделения" filter-field id="typeUnit" autocomplete="off" name="typeUnit" value="{{ request()->input('typeUnit') }}"></th>
                            <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="creationDateFrom" name="creationDateFrom" value="{{ request()->input('creationDateFrom') }}"><input type="date" class="form-control" placeholder="По:" filter-field id="creationDateTo" name="creationDateTo" value="{{ request()->input('CreationDateTo') }}"></th>
                            <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="terminationDateFrom" name="terminationDateFrom" value="{{ request()->input('terminationDateFrom') }}"><input type="date" class="form-control" placeholder="По:" filter-field id="terminationDateTo" name="terminationDateTo" value="{{ request()->input('terminationDateTo') }}"></th>
                            <th><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($units->count() > 0)
                <tbody id="unitsTable">
                    @foreach($units as $unit)
                        <tr class="recordRow" row-record-id="{{ $unit->id }}">
                            <td>{{ $unit->fullUnitName }}</td>
                            <td>{{ $unit->shortUnitName }}</td>
                            <td>{{ $unit->typeUnit }}</td>
                            <td>{{ !empty($unit->creationDate)? date('m-d-Y', strtotime($unit->creationDate)) : '' }}</td>
                            <td>{{ !empty($unit->terminationDate)? date('m-d-Y', strtotime($unit->terminationDate)) : '' }}</td>
                            <td>
                                @if($admin)
                                    <button class="form-control btn btn-danger" deleteRecord record-id="{{ $unit->id }}"><i class="bi bi-trash-fill"></i></button>
                                @endif
                            </td>
                            <td><button class="form-control btn btn-primary" viewRecord record-id="{{ $unit->id }}"><i class="bi bi-pencil-square"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($units->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $units->appends($next_query)->links() }}
    </div>
</x-app-layout>
@if($admin)
    @include('components.js.deleteRecord')
@endif
<script>
    $(document).ready(function(){
        $('#unitsTable').delegate('[viewRecord]', 'click', function(){
            let recordId = $(this).attr('record-id')
            window.location.href = '/units/{{ $site }}/more/' + recordId;
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>