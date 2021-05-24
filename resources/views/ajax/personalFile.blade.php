<div class="col-sm-9" id="personalFile">
    @if($personals)
        <div class="mb-2">
            <img src="{{ '/storage/'.$personals }}" class="mb-1">
            <button class="btn btn-danger delete" type="button" id="deletePersonalFile">Удалить</button>
        </div>
    @endif
    <input type="file" id="titlePersonalFile" form-field>
</div>