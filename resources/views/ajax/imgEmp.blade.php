<div class="col-sm-9" id="imgEmp">
    @if($employee->img)
        <div class="mb-2">
            <img src="{{ '/storage/'.$employee->img }}" class="mb-1">
            <button class="btn btn-danger delete" type="button" id="delete-img">Удалить</button>
        </div>
    @endif
    <input type="file" name="image" id="empImg" form-field>
</div>