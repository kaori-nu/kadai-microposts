
<ul class="media-list">
@foreach ($microposts as $micropost)
    <?php $user = $micropost->user; ?>
    <li class="media">
        <div class="media-left">
            <img class="media-object img-rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
        </div>
        <div class="media-body">
            <div>
                {!! link_to_route('users.show', $user->name, ['id' => $user->id]) !!} <span class="text-muted">posted at {{ $micropost->created_at }}</span>
            </div>
            <div>
                <p>{!! nl2br(e($micropost->content)) !!}</p>
            </div>
            <div>
                @if (Auth::user()->is_favorites($micropost->id))
                <div style="display: inline-block;">
                    {!! Form::open(['route' => ['user.unfavorite', $micropost->id], 'method' => 'delete']) !!}
                        {!! Form::submit('Unfavorite', ['class' => 'btn btn-success btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
                @else
                <div style="display: inline-block;">
                    {!! Form::open(['route' => ['user.favorite', $micropost->id]]) !!}
                        {!! Form::submit('Favorite', ['class' => 'btn btn-default btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
                @endif
                @if (Auth::user()->id == $micropost->user_id)
                <div style="display: inline-block;">
                    {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
                @endif
            </div>
        </div>
    </li>
@endforeach
</ul>
{!! $microposts->render() !!}