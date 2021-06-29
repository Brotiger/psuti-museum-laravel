<div class="my-4">
    <h2 class="h2 mb-4">Владелец</h2>
        <div class="form-group mb-3 row">
            <label for="name" class="col-3 col-form-label">ФИО</label>
            <div class="col-sm-9 mb-3">
                <input class="form-control" type="text" id="addUserName" disabled value="{{ isset($user->name)? $user->name : '' }}">
            </div>
            <label for="email" class="col-3 col-form-label">Email</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="addUserEmail" disabled value="{{ isset($user->email)? $user->email: '' }}">
            </div>
            <input type="hidden" class="form-control" id="addUserId" autocomplete="off" value="{{ !empty($addUser)? $addUser : '' }}">
            <div class="mt-2 col-3">
            <input type="button" class="btn btn-primary" value="Сменить" id="changeAddUser">
        </div>
    </div>
</div>
<div class="form-group row modalAddUser p-3" style="display: none" id="modalAddUser">
    <h4 class="h4 px-0">Смена владельца записи</h4>
    <small class="mb-3 px-0">На выбор предлагаются первые 15 совпадений, если в списке нету того что вы искали, попробуйте задать параметры для поиска более конкретно</small>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="ФИО" id="searchUserByName">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Email" id="searchUserByEmail">
        <div class="px-0">
            <input type="button" class="btn btn-danger mb-3 resetAddUser" value="Сбросить">
            <input type="button" class="btn btn-primary mb-3" value="Поиск" id="searchUser">
        </div>
    </div>
    <hr class="mb-3">
    <label class="mb-1 px-0">Результаты поиска</label>
    <div class="px-0">
        <select class="custom-select custom-select-lg form-control border border-secondary rounded-0 mb-3" id="searchUserResult">
            <option value="">Не выбрано</option>
            <option value="no">Нету</option>
            @foreach($users_search as $user)
                <option value="{{ $user->id }}" userName="{{ $user->name }}" userEmail="{{ $user->email }}">{{ $user->name }} {{ $user->email }}</option>
            @endforeach
        </select>
    </div>
    <div class="px-0">
        <input type="button" class="btn btn-danger closeModal" value="Закрыть">
        <input type="button" class="btn btn-primary addModalHref" value="Выбрать" id="saveChangeOwner">
    </div>
</div>