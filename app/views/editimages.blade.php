@extends('layouts.main')

@section('sidebar-top')
<button class="pure-button pure-button-primary"><a href="{{URL::to('posts/create')}}">Create post</a></button>
@stop

@section('sidebar-menu')
@stop


@section('content')
         <div class="grids-example">
                 <div class="pure-g">
                     @foreach($post->images()->get() as $image)
                         <div class="pure-u-1-4 pure-u-lg-1-8">
                             <img class="pure-img" src="{{'/~nt206/public/img/posts/thumbs/'.$image->file_name}}" alt="{{$image->description}}"/>
                         </div>
                         <a href="{{URL::route('posts.images.remove', array($post->id,$image->id));}}"><i class="fa fa-trash"></i></a>

                     @endforeach
                 </div>
             </div>
             {{ Form::open(array('route' => array('posts.images.upload',$post->id), 'class'=>'pure-form pure-g', 'files' => true)) }}
                 <div class="posts">
                     <h1 class="content-subhead">Post Images</h1>

                     <div class="post">
                         <div class="post-header">

                             <h2 class="post-title">Upload Image:</h2>
                             @if($errors->has('image'))
                                 <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('image')}}</i></span>
                             @endif
                             {{Form::file('image');}}
                             <br/>
                             <p>
                                Please describe your image.
                               </p>
                             @if($errors->has('description'))
                                <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('description')}}</i></span>
                           @endif
                           {{ Form::text('description', Input::old('description'), array('id'=>'description','class'=>'pure-input-1','placeholder' => 'Description...')) }}
                         </div>
                         <br/>
                         <div class="post-description">
                         <h3 class="post-title sr-only">Post Content:</h3>
                     </div>
                     <div class="pure-g">
                         <div class="pure-u-1-3"></div>
                         <div class="pure-u-1-3">
                         </div>
                         <div class="pure-u-1-3 pure-u-sm-1">
                            {{Form::submit('Submit', array('name'=>'post', 'value'=>'Post', 'class'=>'pure-button pure-button-primary'));}}

                         </div>
                     </div>
                 </div>
             {{ Form::close() }}
             </div>
         </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $('form').validate();
    </script>
@endsection