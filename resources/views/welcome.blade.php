@extends('layouts.app')

@section('content')
   @if (Auth::check())
         <div class="row">
            <aside class="col-xs-4">
                {!! Form::open(['route' => 'microposts.store']) !!}
                    <div class="form-group">
                        {!! Form::textarea('content', old('content'), ['class' => 'form-control', 'rows' => '5']) !!}
                    </div>
                    {!! Form::submit('Post', ['class' => 'btn btn-primary btn-block']) !!}
                {!! Form::close() !!}
            </aside>
            <div class="col-xs-8">
                <ul class="nav nav-tabs nav-justified">
                    <li role="presentation" class="{{ Request::is('users/' . $user->id) ? 'active' : '' }}"><a href="{{ route('users.show', ['id' => $user->id]) }}">Microposts <span class="badge">{{ $count_microposts }}</span></a></li>
                    <li role="presentation" class="{{ Request::is('users/*/followings') ? 'active' : '' }}"><a href="{{ route('users.followings', ['id' => $user->id]) }}">Followings <span class="badge">{{ $count_followings }}</span></a></li>
                    <li role="presentation" class="{{ Request::is('users/*/followers') ? 'active' : '' }}"><a href="{{ route('users.followers', ['id' => $user->id]) }}">Followers <span class="badge">{{ $count_followers }}</span></a></li>
                    <li role="presentation" class="{{ Request::is('users/*/favorites') ? 'active' : '' }}"><a href="{{ route('users.favorites', ['id' => $user->id]) }}">Favorites <span class="badge">{{ $count_favorites }}</span></a></li>
                </ul>
                @if (count($microposts) > 0)
                    @include('microposts.microposts', ['microposts' => $microposts])
                @endif
            </div>
        </div>
    @else
       <div class="center jumbotron">
          <div class="text-center">
             <h1>Welcome to the Microposts</h1>
              {!! link_to_route('signup.get', 'Sign up now!', null, ['class' => 'btn btn-lg btn-primary']) !!}
          </div>
       </div>
    @endif
@endsection