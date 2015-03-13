@extends('layouts.main')

@section('sidebar-top')
    @if(Auth::check())
        <a href="{{URL::to('posts/create')}}"><button class="pure-button pure-button-primary">Create post</button></a>
    @endif
@stop

@section('sidebar-menu')

@stop

@section('content')

      <div class="content pure-u-1">
         <div class="posts">
            {{ Form::open(array('route' => 'posts.search', 'class'=>'pure-form pure-g', 'method' => 'get')) }}
            <div>
                <h1 class="content-subhead">Search</h1>

                {{ Form::text('postcode', Input::old('postcode'),
                array('id'=>'postcode','placeholder' => 'Postcode')) }}
                @foreach($sitterTypes as $sitterType)
                <label for="sitterType-{{$sitterType->id}}">
                  <span class="post-category" style="background-color:{{$sitterType->color}}">{{$sitterType->name}}</span>
                  <input name="sitterType[]" type="checkbox"  value="{{$sitterType->id}}" id="sitterType-{{$sitterType->id}}"
                  @if(in_array($sitterType->id, is_null(Input::old('sitterType')) ? array() : Input::old('sitterType')))
                    checked
                  @endif/>
                </label>
               @endforeach
               <div style="float:right;">
                <input name="images" type="checkbox"  value="1" id="images" @if(Input::has('images')) checked="yes"@endif/> <label for="images">Image Required</label>
                @if(Auth::check())
                <input name="user" type="checkbox"  value="{{Auth::user()->id}}" id="user" @if(Input::has('user')) checked="yes"@endif/> <label for="user">My Posts</label>
                @endif
                <button class="pure-button pure-button-secondary" type="submit">Search</button>
            </div>
            <div style="clear:both;"></div>
            </div>
            {{Form::close()}}
          </div>
       </div>
      <div class="content pure-u-1">
                @if(isset($pinned) && count($pinned) > 0)
                 <!-- A wrapper for all the blog posts -->
                 <div class="posts">
                     <h1 class="content-subhead">Pinned Posts</h1>

                     <!-- A single blog post -->
                     @foreach($pinned as $post)
                        @include('layouts.post', array('post'=>$post))
                     @endforeach
                 </div>
                @endif
                 <div class="posts">
                     <h1 class="content-subhead">{{$title or 'Recent Posts'}}</h1>
                     @foreach($posts as $post)
                        @include('layouts.post', array('post'=>$post))
                     @endforeach
                     <?php echo $posts->appends(Input::except('page'))->links(); ?>
                 </div>

                 <div class="footer">
                     <div class="pure-menu pure-menu-horizontal pure-menu-open">
                         <ul>
                             <li><a href="http://purecss.io/">About</a></li>
                             <li><a href="http://twitter.com/yuilibrary/">Twitter</a></li>
                             <li><a href="http://github.com/yahoo/pure/">GitHub</a></li>
                         </ul>
                     </div>
                 </div>
         </div>
@stop
@section('scripts')
    <script type="text/javascript">
        $('form').validate();
    </script>
@endsection