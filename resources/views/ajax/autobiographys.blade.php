@foreach($employee->autobiographys as $index => $autobiography)
<li class="my-4 autobiographyBlockOld" id="autobiographyBlock_{{ $index }}">
    <div class="mb-3">
        <div class="row">
            <label for="autobiography_{{ $index }}" class="col-sm-3 col-form-label">Лист</label>
            <div class="col-sm-9">
                <img src="{{ '/storage/'.$autobiography->file }}">
            </div>
        </div>
    </div>
    <button class="btn btn-danger delete" type="button" autobiography-id="{{ $autobiography->id }}">Удалить</button>
    <hr class="mt-4">
</li>
@endforeach