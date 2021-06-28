<x-app-layout>
    <div class="container-fluid px-4">
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
        @if($access)
            <h1 class="h1">Список пользователей</h1>
            <div class="row mb-1">
                <span class="col-9"><small>Для того что бы отредактировать права пользователя нажмите на него</small></span>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th colspan="3">Email</th>
                    </tr>
                    <tr>
                        <form method="GET" action="{{route('users_list')}}">
                            <th><input type="text" class="form-control" placeholder="ФИО" filter-field id="name" name="name" autocomplete="off" value="{{ request()->input('name') }}"></th>
                            <th><input type="text" class="form-control" placeholder="Email" filter-field id="email" autocomplete="off" name="email" value="{{ request()->input('email') }}"></th>
                            <th width="40"><button class="form-control btn btn-danger" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button></th><th width="40"><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                        </form>
                    </tr>
                </thead>
                @if($users->count() > 0 && $access)
                <tbody id="usersTable">
                    @foreach($users as $user)
                        <tr class="recordRow" user-id="{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td colspan="3">{{ $user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
            @if($users->count() == 0)
            <p class="text-center">
                Ничего не найдено
            </p>
            @endif
        @endif

            @if(!$access)
            <p class="text-center">
                У вас нету прав доступа к данному интерфейсу, если они вам нужны обратиться к главному админестратору для их выдачи
            </p>
            @endif
        </div>
        {{ $users->appends($next_query)->links() }}
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        $('#usersTable').delegate('.recordRow', 'click', function(){
            let userId = $(this).attr('user-id')
            window.location.href = '/users/more/' + userId;
        });
        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).attr("value", "");
            });
        });
    });
</script>