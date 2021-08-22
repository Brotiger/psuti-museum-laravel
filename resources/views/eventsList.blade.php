<x-app-layout>
    @if($admin)
        @include('components.deleteRecord')
        <input type="hidden" value="{{ route('delete_event') }}" id="routeToDelete">
    @endif
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами событий</h1>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название события</th>
                        <th colspan="2">Дата события</th>
                        <th width="60">Действия</th>
                        <th width="60"></th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{ route('events_list', [ 'site' => env('DB_SITE')]) }}">
                            <th><input type="text" class="form-control" placeholder="Название события" filter-field autocomplete="off" name="name" value="{{ request()->input('name') }}"></th>
                            <th><input type="date" class="form-control" placeholder="С:" filter-field name="dateFrom" value="{{ request()->input('dateFrom') }}"></th><th><input type="date" class="form-control" placeholder="По:" filter-field name="dateTo" value="{{ request()->input('dateTo') }}"></th>
                            <th><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($events->count() > 0)
                <tbody id="eventsTable">
                    @foreach($events as $event)
                        <tr class="recordRow" row-record-id="{{ $event->id }}">
                            <td>{{ $event->name }}</td>
                            <td colspan="2">{{ !empty($event->date)? date('m-d-Y', strtotime($event->date)) : '' }}</td>
                            <td>
                                @if($admin)
                                    <button class="form-control btn btn-danger" deleteRecord record-id="{{ $event->id }}"><i class="bi bi-trash-fill"></i></button>
                                @endif
                            </td>
                            <td><button class="form-control btn btn-primary" viewRecord record-id="{{ $event->id }}"><i class="bi bi-pencil-square"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($events->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $events->appends($next_query)->links() }}
    </div>
</x-app-layout>
@if($admin)
    @include('components.js.deleteRecord')
@endif
<script>
    $(document).ready(function(){
        $('#eventsTable').delegate('[viewRecord]', 'click', function(){
            let recordId = $(this).attr('record-id')
            window.location.href = '/events/{{ $site }}/more/' + recordId;
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>