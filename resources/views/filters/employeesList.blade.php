@foreach($employees as $employee)
    <tr class="recordRow" employee-id="{{ $employee->id }}">
        <td>{{ $employee->lastName }}</td>
        <td>{{ $employee->firstName }}</td>
        <td>{{ $employee->secondName }}</td>
        <td>{{ $employee->dateBirthday }}</td>
        <td>{{ $employee->hired }}</td>
        <td colspan="2">{{ $employee->fired }}</td>
    </tr>
@endforeach