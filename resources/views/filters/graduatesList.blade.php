@foreach($graduates as $graduate)
    <tr class="recordRow" graduate-id="{{ $graduate->id }}">
        <td>{{ $graduate->lastName }}</td>
        <td>{{ $graduate->firstName }}</td>
        <td>{{ $graduate->secondName }}</td>
        <td>{{ $graduate->registrationNumber }}</td>
        <td>{{ !empty($graduate->dateBirthday)? date('m-d-Y', strtotime($graduate->dateBirthday)) : '' }}</td>
        <td>{{ $graduate->enteredYear }}</td>
        <td colspan=2>{{ $graduate->exitYear }}</td>
    </tr>
@endforeach