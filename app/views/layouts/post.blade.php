<div class="post">
   <div class="post-header">
       <img class="post-avatar" alt="No Avatar image" height="48" width="48" src="/~nt206/public/img/blank_user.gif"/>

            @if(Auth::check())
                @if($post->user_id === Auth::user()->id || Auth::user()->isAdmin() || Auth::user()->isModerator())
                    <a class="post-avatar" href="{{URL::route('posts.edit',$post->id);}}"><span class="post-avatar sr-only">Edit post</span><i class="fa fa-pencil"></i></a>
                    <a class="post-avatar" href="{{URL::route('posts.images',$post->id);}}"><span class="post-avatar sr-only">Edit images</span><i class="fa fa-image"></i></a>
                    <a class="post-avatar" href="{{URL::route('posts.remove',$post->id);}}"><span class="post-avatar sr-only">Delete post</span><i class="fa fa-remove"></i></a>
                @endif
            @endif
       <h2 class="post-title">{{$post->title}}</h2>

       <p class="post-meta">
            @if(Auth::check())
            @if(Auth::user()->canSticky())
            Created at: {{$post->created_at}} <br/>
            Edited at: {{$post->updated_at}} <br/>
            @endif
            @endif
            By <a class="post-author" href="#">{{!empty($post->user()->get()[0]->name) ? $post->user()->get()[0]->name :$post->user()->get()[0]->username }}</a>, {{$post->location}}
            @if($post->sitterTypes()->count() > 0)
                under
            @endif
            @foreach($post->sitterTypes()->get() as $sitterType)
            <a class="post-category" href="#" style="background-color: {{$sitterType->color}}">{{$sitterType->name}}</a>
            @endforeach
            @if(!is_null($post->distance))
            they are {{$post->distance}} miles away.
            @endif
       </p>
   </div>

   <div class="post-description">
       <p>
         {{$post->content}}
       </p>
   </div>
</div>
<div class="pure-g">
   @foreach($post->images()->get() as $image)
       <div class="pure-u-lg-1-8">
           <img class="pure-img" src="{{'/~nt206/public/img/posts/thumbs/'.$image->file_name}}" alt="{{$image->description}}"/>
       </div>
   @endforeach
</div>