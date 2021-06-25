@foreach($page->history as $index => $history)
<li class="my-4 historyBlockOld" id="historyBlock_{{ $index }}">
    <div class="form-group mb-3 row">
        <label for="commentAuth_{{ $index }}" class="col-3 col-form-label">Автор</label>
        <div class="col-sm-9 mb-3">
            <input class="form-control commentAuth" type="text" id="commentAuth_{{ $index }}" placeholder="Автор" autocomplete="off" disabled value="{{ isset($history->user->name) ? $history->user->name : $history->user->email }}">
        </div>
        <label for="comment_{{ $index }}" class="col-3 col-form-label">История</label>
        <div class="col-sm-9">
            <textarea class="form-control border border-secondary rounded-0 comment" id="comment_{{ $index }}" rows="7" disabled placeholder="История">{{ $history->comment }}</textarea>
        </div>
    </div>
    @if($user->id == $history->addUserId || $admin)
        <button class="btn btn-danger delete" type="button" history-id="{{ $history->id }}">Удалить</button>
    @endif
    <hr class="mt-4">
</li>
@endforeach