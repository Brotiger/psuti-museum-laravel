<option value="">Не выбрано</option>
<option value="no">Нету</option>
@foreach($users_search as $user)
    <option value="{{ $user->id }}" userName="{{ $user->name }}" userEmail="{{ $user->email }}">{{ $user->name }} {{ $user->email }}</option>
@endforeach