@foreach($page->videos as $index => $video)
    <li class="my-4 videoBlockOld" id="videoBlock_{{ $index }}">
        <div class="mb-3">
                <div class="row">
                    <label for="video_{{ $index }}" class="col-sm-3 col-form-label">Видео</label>
                    <div class="col-sm-9">
                        <iframe width="481" height="315" src="{{ 'https://www.youtube.com/embed/'.$video->video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3 row">
                <label for="videoName_{{ $index }}" class="col-3 col-form-label">Название видео</label>
                <div class="col-sm-9">
                    <input class="form-control videoName" type="text" maxlength="191" id="videoName_{{ $index }}" placeholder="Название видео" autocomplete="off" disabled value="{{ $video->videoName }}">
                </div>
            </div>
            <div class="form-group mb-3 row">
            <label for="videoDate_{{ $index }}" class="col-3 col-form-label">Дата видео</label>
            <div class="col-sm-9">
                <input class="form-control videoDate" type="date" id="videoDate_{{ $index }}" placeholder="Дата видео" disabled value="{{ $video->videoDate }}">
            </div>
        </div>
        @if($access)
            <button class="btn btn-danger delete" type="button" video-id="{{ $video->id }}">Удалить</button>
        @endif
        <hr class="mt-4">
    </li>
@endforeach