<option value="">Не выбрано</option>
@foreach($units_search as $unit)
    <option value="{{ $unit->id }}">{{ $unit->fullUnitName }} {{ $unit->shortUnitName? '(' . $unit->shortUnitName . ')' : '' }}</option>
@endforeach