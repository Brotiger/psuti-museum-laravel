<option value="">Не выбрано</option>
@foreach($employees_search as $employee)
    <option value="{{ $employee->id }}">{{ $employee->lastName }} {{ $employee->firstName }} {{ $employee->secondName }}</option>
@endforeach