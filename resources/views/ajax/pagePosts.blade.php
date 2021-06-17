@foreach($page->posts as $index => $post)
    <li class="my-4 postBlockOld" id="postBlock_{{ $index }}" record-id='{{ $post->id }}'>
        <div class="form-group mb-3 row">
            <label for="title_{{ $index }}" class="col-3 col-form-label">Заголовок</label>
                <div class="col-sm-9">
                    <input class="form-control title" type="text" id="title_{{ $index }}" placeholder="Заголовок" autocomplete="off" value="{{ $post->title }}">
                </div>
            </div>
            <div class="form-group mb-3 row">
                <label for="description_{{ $index }}" class="col-3 col-form-label">Описание*</label>
                <div class="col-sm-9">
                    <textarea class="form-control border border-secondary rounded-0 description" id="description_{{ $index }}" rows="14" placeholder="Описание">{{ !empty($post)? $post->description : '' }}</textarea>
                </div>
                <span class="offset-3 col-9"><small>Для добавления ссылки в описании, поставьте курсор в то место где хотите создать ссылку и выбирите один из вариантов предложенных ниже</small></span>
                <div class="col-sm-9 offset-3 mt-2">
                    <input type="button" class="btn btn-primary" value="Сотрудники" addEmpHref>
                    <input type="button" class="btn btn-primary mx-1" value="Подразделения" addUnitHref>
                    <input type="button" class="btn btn-primary" value="События" addEventHref>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="post_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
                <div class="col-sm-9">
                @if($post->photo)
                    <div>
                        <img src="{{ '/storage/'.$post->photo }}"  class="mb-1">
                        <button class="btn btn-danger delete" type="button" deletePostPhoto="{{ $post->id }}">Удалить</button>
                    </div>
                @endif
                <div class="row mb-1">
                    <span><small>Максимальный вес файла: {{ $photo_size }} КБ. Допустимые расширения: {{ $photo_ext }}</small></span>
                </div>
                <div class="row">
                    <div>
                        <input type="file" name="photo" id="post_{{ $index }}" class="post" accept="{{  '.'.str_replace(', ', ', .', $photo_ext) }}">
                    </div>
                </div>
                </div>
            </div>
        <button class="btn btn-danger delete" type="button" post-id="{{ $post->id }}">Удалить</button>
        <hr class="mt-4">
    </li>
@endforeach