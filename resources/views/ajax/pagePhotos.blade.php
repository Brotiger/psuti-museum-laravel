@foreach($page->photos as $index => $photo)
<li class="my-4 photoBlockOld" id="photoBlock_{{ $index }}">
    <div class="mb-3">
        <div class="row">
            <label for="photo_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
            <div class="col-sm-9">
                <img src="{{ '/storage/'.$photo->photo }}">
            </div>
        </div>
    </div>
    <div class="form-group mb-3 row">
        <label for="photoName_{{ $index }}" class="col-3 col-form-label">Название фотографии</label>
        <div class="col-sm-9">
            <input class="form-control photoName" type="text" id="photoName_{{ $index }}" placeholder="Название фотографии" autocomplete="off" disabled value="{{ $photo->photoName }}">
        </div>
    </div>
    <div class="form-group mb-3 row">
        <label for="photoDate_{{ $index }}" class="col-3 col-form-label">Дата фотографии</label>
        <div class="col-sm-9">
            <input class="form-control photoDate" type="date" id="photoDate_{{ $index }}" placeholder="Дата фотографии" disabled value="{{ $photo->photoDate }}">
        </div>
    </div>
    <button class="btn btn-danger delete" type="button" photo-id="{{ $photo->id }}">Удалить</button>
    <hr class="mt-4">
</li>
@endforeach