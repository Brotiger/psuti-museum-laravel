<option value="">Не выбрано</option>
@foreach($events_search as $event)
    <option value="{{ $event->id }}">{{ $event->name }}</option>
@endforeach