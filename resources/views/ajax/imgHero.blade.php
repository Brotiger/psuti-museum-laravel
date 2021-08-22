<div class="col-sm-9" id="imgEmp">
    @if($hero->img)
        <div class="mb-2">
            <img src="{{ '/storage/'.$hero->img }}" class="mb-1">
            <button class="btn btn-danger delete" type="button" id="deleteImg">Удалить</button>
        </div>
    @endif
    <input type="file" name="image" id="heroImg" form-field>
</div>