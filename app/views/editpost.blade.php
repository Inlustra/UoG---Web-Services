@extends('layouts.main')

@section('sidebar-top')
<button class="pure-button pure-button-primary"><a href="{{URL::to('posts/create')}}">Create post</a></button>
@stop

@section('sidebar-menu')
@stop


@section('content')
         <div class="content pure-u-1">
         <div>
             {{ Form::open(array('route' => array('posts.edit.post',$post->id), 'class'=>'pure-form pure-g')) }}
                 <div class="posts">
                     <h1 class="content-subhead">Edit Post...</h1>

                     <div class="post">
                         <div class="post-header">

                             <h2 class="post-title">Post Title:
                             @if($errors->has('title'))
                                  <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('title')}}</i></span>
                             @endif</h2>
                             <h2>{{ Form::text('title', $post->title, array('id'=>'title','class'=>'pure-input-1','placeholder' => 'Title...')) }}</h2>
                              @if($errors->has('postcode'))
                                  <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('postcode')}}</i></span>
                              @endif
                              @if($errors->has('price'))
                                  <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('price')}}</i></span>
                              @endif
                              @if($errors->has('sitterType'))
                                  <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('sitterType')}}</i></span>
                              @endif
                                <p class="post-meta" style="clear:both;">
                                      Post-code: {{ Form::text('postcode', $post->location, array('id'=>'postcode','class'=>'pure-input-3-8','placeholder' => 'SE10 ***')) }}
                                      under
                                  @foreach($sitterTypes as $sitterType)
                                    <label for="sitterType-{{$sitterType->id}}">
                                      <span class="post-category" style="background-color:{{$sitterType->color}}">{{$sitterType->name}}</span>
                                      <input name="sitterType[]" type="checkbox"  value="{{$sitterType->id}}" id="sitterType-{{$sitterType->id}}"
                                      {{$sitterType}}

                                      @if(in_array($sitterType->id, $post_sitterTypes))
                                      checked="yes"
                                      @endif>
                                    </label>
                                   @endforeach
                                </p>
                         </div>

                         <div class="post-description">
                         <h3 class="post-title sr-only">Post Content:</h3>
                             @if($errors->has('content'))
                                  <span class="pure-badge-error"><i class="fa fa-exclamation-circle"> {{$errors->first('content')}}</i></span>
                             @endif
                             <p>
                                {{ Form::textarea('content', $post->content, array('id'=>'postcode','class'=>'pure-input-2-5',
                                'style'=>'font-family: Georgia, "Cambria", serif;','placeholder' => 'Post content...')) }}
                             </p>
                         </div>
                     </div>
                     <div class="pure-g">
                         <div class="pure-u-1-3"></div>
                         <div class="pure-u-1-3">
                         </div>
                         <div class="pure-u-1-3 pure-u-sm-1">
                            {{Form::submit('Edit Post', array('name'=>'post', 'value'=>'Post', 'class'=>'pure-button pure-button-primary'));}}
                          @if(Auth::user()->canSticky())
                              <input name="pinned" type="checkbox"  value="1" id="pinned" {{$post->pinned ?"checked='yes'":""}}> Sticky Post?
                           @endif
                          </div>
                     </div>
                 </div>
             {{ Form::close() }}
             </div>
         </div>
@stop