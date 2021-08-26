@foreach($page->posts as $index => $post)
    <li class="my-4 postBlockOld" id="postBlock_{{ $index }}" record-id='{{ $post->id }}'>
        <div class="form-group mb-3 row">
            <label for="title_{{ $index }}" class="col-3 col-form-label">Заголовок</label>
            <div class="col-sm-9">
                <input class="form-control title" type="text" maxlength="191" id="title_{{ $index }}" placeholder="Заголовок" autocomplete="off" value="{{ $post->title }}" @php if(!$access) echo "disabled" @endphp>
            </div>
        </div>
        <div class="form-group mb-3 row">
            <label for="description_{{ $index }}" class="col-3 col-form-label">Описание*</label>
            <div class="col-sm-9">
                <textarea class="form-control border border-secondary rounded-0 description" id="description_{{ $index }}" rows="14" placeholder="Описание" @php if(!$access) echo "disabled" @endphp>{{ !empty($post)? $post->description : '' }}</textarea>
            </div>
            @if($access)
            <span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>
            <div class="col-sm-9 offset-3 mt-2">
                <input type="button" class="btn btn-primary" value="Сотрудники" addEmpHref>
                <input type="button" class="btn btn-primary mx-1" value="Подразделения" addUnitHref>
                <input type="button" class="btn btn-primary" value="События" addEventHref>
            </div>
            @endif
        </div>
        @if($post->photo || $access)
        <div class="mb-3 row">
            <label for="post_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
            <div class="col-sm-9">
            @if($post->photo)
                <div>
                    <img src="{{ '/storage/'.$post->photo }}">
                    @if($access)
                        <button class="btn btn-danger delete mt-3" type="button" deletePostPhoto="{{ $post->id }}">Удалить</button>
                    @endif
                </div>
            @endif
            @if($access)
                <div class="row mb-1">
                    <span><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>
                </div>
                <div class="row">
                    <div>
                        <input type="file" name="photo" id="post_{{ $index }}" class="post" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">
                    </div>
                </div>
            @endif
            </div>
        </div>
        @endif
        @if($access)
            <button class="btn btn-danger delete" type="button" post-id="{{ $post->id }}">Удалить</button>
        @endif
        <hr class="mt-4">
    </li>
@endforeach