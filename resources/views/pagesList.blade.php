<x-app-layout>
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список страниц</h1>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Название страницы</th>
                        <th width="60">Действия</th>
                        <th width="60"></th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{ route('pages_list') }}">
                            <th><input type="text" class="form-control" placeholder="Название страницы" filter-field autocomplete="off" name="title" value="{{ request()->input('title') }}"></th>
                            <th><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($pages->count() > 0)
                <tbody id="pagesTable">
                    @foreach($pages as $page)
                        <tr class="recordRow" page-id="{{ $page->id }}">
                            <td>{{ $page->title }}</td>
                            <td></td>
                            <td><button class="form-control btn btn-primary" viewRecord record-id="{{ $page->id }}"><i class="bi bi-pencil-square"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($pages->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        </div>
        {{ $pages->appends($next_query)->links() }}
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        $('#pagesTable').delegate('[viewRecord]', 'click', function(){
            let recordId = $(this).attr('record-id')
            window.location.href = '/pages/more/' + recordId;
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>