@foreach($units as $unit)
    <tr class="recordRow" unit-id="{{ $unit->id }}">
        <td>{{ $unit->fullUnitName }}</td>
        <td>{{ $unit->shortUnitName }}</td>
        <td>{{ $unit->typeUnit }}</td>
        <td>{{ $unit->creationDate }}</td>
        <td colspan="2">{{ $unit->terminationDate }}</td>
    </tr>
@endforeach