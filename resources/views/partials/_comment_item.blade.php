<li class="comment-item" data-comment-id="{{ $comment->id }}">
    <div class="comment-content {{ $comment->parent_id ? 'bg-white border' : 'bg-light' }} p-2 rounded mb-1" tabindex="0">
        <strong>{{ $comment->user->name ?? 'Unknown' }}</strong>: {{ $comment->body }}
        <div class="comment-actions mt-1">
            <button class="comment-action-btn reply-btn small text-primary border-0 bg-transparent p-0 me-2" data-comment-id="{{ $comment->id }}">Reply</button>
            <button class="comment-action-btn copy-btn small text-secondary border-0 bg-transparent p-0" data-comment-body="{{ $comment->body }}" title="Copy" style="display: none;">📋</button>
            @if(auth()->check() && $comment->user_id == auth()->id())
                <button class="comment-action-btn delete-btn small text-danger border-0 bg-transparent p-0" data-comment-id="{{ $comment->id }}" title="Delete" style="display: none;">🗑️</button>
            @endif
        </div>
    </div>
    <div class="reply-form-container" style="display: none;">
        <form class="reply-form mt-2" data-parent-id="{{ $comment->id }}" data-meme-id="{{ $meme->id }}">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
            <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
            <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
        </form>
    </div>
    <ul class="replies-container mt-2 list-unstyled" style="margin-left: 20px; border-left: 2px solid #eee; padding-left: 10px;">
        @foreach($comment->replies as $reply)
            @include('partials._comment_item', ['comment' => $reply, 'meme' => $meme])
        @endforeach
    </ul>
</li>
