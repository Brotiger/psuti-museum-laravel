<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> подразделений</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами событий</h1>
            <div class="row mb-1">
                <span class="col-9"><small>Для того что бы отредактировать информацию о событии нажмите на него</small></span>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название события</th>
                        <th colspan="2">Дата события</th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{ route('events_list') }}">
                            <th><input type="text" class="form-control" placeholder="Название события" filter-field autocomplete="off" name="name" value="{{ request()->input('name') }}"></th>
                            <th><input type="date" class="form-control" placeholder="С:" filter-field name="dateFrom" value="{{ request()->input('dateFrom') }}"></th><th><input type="date" class="form-control" placeholder="По:" filter-field name="dateTo" value="{{ request()->input('dateTo') }}"></th>
                            <th><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($events->count() > 0)
                <tbody id="eventsTable">
                    @foreach($events as $event)
                        <tr class="recordRow" event-id="{{ $event->id }}">
                            <td>{{ $event->name }}</td>
                            <td colspan="2">{{ !empty($event->date)? date('m-d-Y', strtotime($event->date)) : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($events->count() == 0)
            <p>
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $events->appends($next_query)->links() }}
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        $('#eventsTable').delegate('.recordRow', 'click', function(){
            let unitId = $(this).attr('event-id')
            window.location.href = '/events/{{ $site }}/more/' + unitId;
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>