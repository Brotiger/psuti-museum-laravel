@foreach($page->posts as $index => $post)
    <li class="my-4 postBlockOld" id="postBlock_{{ $index }}">
        <div class="form-group mb-3 row">
            <label for="title_{{ $index }}" class="col-3 col-form-label">Заголовок</label>
                <div class="col-sm-9">
                    <input class="form-control title" type="text" id="title_{{ $index }}" placeholder="Заголовок" autocomplete="off" disabled value="{{ $post->title }}">
                </div>
            </div>
            <div class="form-group mb-3 row">
                <label for="description" class="col-3 col-form-label">Описание*</label>
                <div class="col-sm-9">
                    <textarea class="form-control border border-secondary rounded-0" id="description" rows="14" placeholder="Описание" disabled>{{ !empty($post)? $post->description : '' }}</textarea>
                </div>
            </div>
            @if($post->photo)
            <div class="mb-3">
                <div class="row">
                    <label for="post_{{ $index }}" class="col-sm-3 col-form-label">Фото</label>
                    <div class="col-sm-9">
                        <img src="{{ '/storage/'.$post->photo }}">
                    </div>
                </div>
            </div>
            @endif
        <button class="btn btn-danger delete" type="button" post-id="{{ $post->id }}">Удалить</button>
        <hr class="mt-4">
    </li>
@endforeach