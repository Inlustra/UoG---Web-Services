<?php

class PostController extends CustomRestController
{


    function getModel()
    {
        return Post;
    }

    public static $searchPostRules = [
        'postcode' => 'greenwich|postcode'
    ];

    public static $createPostRules = array(
        'title' => 'required|max:45',
        'content' => 'required',
        'sitterType' => 'required|min:1',
        'postcode' => 'required|greenwich|postcode'
    );

    public static $createPostRulesAdmin = array(
        'title' => 'required|max:45',
        'content' => 'required',
    );

    public static $uploadImageRules = array(
        'image' => 'required|image|max:500',
        'description' => 'required'
    );

    public function showHome()
    {
        Input::flush();
        return $this->showPosts()->with([
            'pinned' => Post::pinned()->orderBy('created_at', 'DESC')->get(),
            'posts' => Post::notpinned()->orderBy('created_at', 'DESC')->paginate(5)]);
    }

    public function showPosts()
    {
        Form::setValidation(static::$searchPostRules);
        return View::make('showposts')->with('sitterTypes', SitterType::all());
    }

    public function searchPosts()
    {
        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), static::$searchPostRules);
        if ($validator->fails()) {
            return Redirect::route('home')
                ->with('error', ['The Postcode you entered was not a valid Royal Borough of Greenwich Postcode'])// send back all errors to the login form
                ->withInput(Input::all()); // send back the input (not the password) so that we can repopulate the form
        }

        $query = new Post;
        if (Input::has('user')) {
            $query = $query->byuser(Input::get('user'));
        }
        if (Input::has('sitterType')) {
            $query = $query->ofType(Input::get('sitterType'));
        }
        if (Input::has('postcode')) {
            $postcode = trim(Input::get('postcode'));
            if (!empty($postcode)) {
                $postcodeDetails = Postcode::getCoordinates(Input::get('postcode'));
                if (is_null($postcodeDetails)) {
                    return Redirect::route('home')
                        ->withInput(Input::all())
                        ->with('error', array('Unfortunately, we weren\'t able to
                    gather details on your location, please try again later.'));
                }
                $query = $query->distance($postcodeDetails['latitude'], $postcodeDetails['longitude']);
            }
        }

        if (Input::has('images')) {
            $query = $query->withimages();
        }
        $array = array(
            'sitterTypes' => SitterType::all(),
            'posts' => $query->paginate(5),
            'title' => 'Search',
        );
        if (Input::has('postcode')) {
            foreach ($array['posts'] as $post) {
                $coordA = Geotools::coordinate(array($postcodeDetails['latitude'], $postcodeDetails['longitude']));
                $coordB = Geotools::coordinate(array($post->lat, $post->lon));
                $distance = number_format(Geotools::distance()->setFrom($coordA)->setTo($coordB)->in('mi')->vincenty(), 2);
                $post->distance = $distance;
            }
        }
        Input::flash();
        return $this->showPosts()->with($array);
    }

    public function showCreatePost()
    {
        Form::setValidation(static::$createPostRules);
        return View::make('createpost')->with('sitterTypes', SitterType::all());
    }

    public function showEditPost($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::user()->id || Auth::user()->isAdmin() || Auth::user()->isModerator()) {
            $post_sitterTypes = array();
            foreach ($post->sitterTypes()->get() as $sitterType) {
                array_push($post_sitterTypes, $sitterType->id);
            }
            Form::setValidation(static::$createPostRules);
            return View::make('editpost')->with(array('post' => $post,
                'sitterTypes' => SitterType::all(),
                'post_sitterTypes' => $post_sitterTypes));
        }
        return $this->showPosts()->with('message', array('Error: You are not allowed to do that.'));

    }

    public function performEditPost($id)
    {


        // run the validation rules on the inputs from the form
        $rules = static::$createPostRules;
        if(Auth::user()->isAdmin()) {
            $rules = static::$createPostRulesAdmin;
        }
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)// send back all errors to the login form
                ->withInput(Input::all()); // send back the input (not the password) so that we can repopulate the form
        }
        $post = Post::find($id);

        if ($post->user_id === Auth::user()->id || Auth::user()->isAdmin() || Auth::user()->isModerator()) {
            $post->user_id = Auth::user()->id;
            $post->title = Input::get('title');
            $post->location = Input::get('postcode');
            $post->content = Input::get('content');
            if (Auth::user()->canSticky()) {
                if (Input::get('pinned') === '1') {
                    $post->pinned = true;
                } else {
                    $post->pinned = false;
                }
            }
            if (!empty($post->location)) {

                $postcodeDetails = Postcode::getCoordinates($post->location);
                if (is_null($postcodeDetails)) {
                    return Redirect::back()
                        ->withErrors($validator)// send back all errors to the login form
                        ->withInput(Input::all())
                        ->with('message', array('Unfortunately, we weren\'t able to
                    gather details on your location, please try again later.'));
                }
                $post->lat = $postcodeDetails['latitude'];
                $post->lon = $postcodeDetails['longitude'];
            }
            $post->save();
            $sitterTypes = Input::get('sitterType');
            if(!Input::has('sitterType')) {
                $sitterTypes = array();
            }

            $post->sitterTypes()->sync($sitterTypes);
            return Redirect::to('/')->with('message', array('Your service was successfully edited.'));
        }

        return Redirect::to('/')->with('message', array('You are not allowed to do that.'));
    }

    public function createPost()
    {
        if (Auth::check()) {
            $rules = static::$createPostRules;
            if(Auth::user()->isAdmin()) {
                $rules = static::$createPostRulesAdmin;
            }
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('posts/create')
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput(Input::all()); // send back the input (not the password) so that we can repopulate the form
            }
            $post = new Post;
            $post->user_id = Auth::user()->id;
            $post->title = Input::get('title');
            $post->location = Input::get('postcode');
            $post->content = Input::get('content');
            if (Auth::user()->canSticky()) {
                if (Input::get('pinned') === '1') {
                    $post->pinned = true;
                }
            }
            if (!empty($post->location)) {
                $postcodeDetails = Postcode::getCoordinates($post->location);
                if (is_null($postcodeDetails)) {
                    return Redirect::to('posts/create')
                        ->withErrors($validator)// send back all errors to the login form
                        ->withInput(Input::all())
                        ->with('message', array('Unfortunately, we weren\'t able to
                    gather details on your location, please try again later.'));
                }
                $post->lat = $postcodeDetails['latitude'];
                $post->lon = $postcodeDetails['longitude'];
            }
            $post->save();
            $sitterTypes = Input::get('sitterType');
            if(!Input::has('sitterType')) {
                $sitterTypes = array();
            }

            $post->sitterTypes()->sync($sitterTypes);
            return Redirect::to('/')->with('message', array('Your service was successfully posted.'));
        }
        return Redirect::route('user.login')->with('message', array('You must be logged in to do that!'));
    }


    public function showPostImages($id)
    {
        $post = Post::findOrFail($id);
        Form::setValidation(static::$uploadImageRules);
        return View::make('editimages')->with(array('post' => $post));
    }

    public function addPostImage($id)
    {
        $input = array('image' => Input::file('image'),
            'description' => Input::get('description'));
        // Within the ruleset, make sure we let the validator know that this
        // file should be an image


        // Now pass the input and rules into the validator
        $validator = Validator::make($input, static::$uploadImageRules);
        // Check to see if validation fails or passes
        if ($validator->fails()) {
            // Redirect with a helpful message to inform the user that
            // the provided file was not an adequate type
            return Redirect::route('posts.images', $id)->withErrors($validator);
        }
        $post = Post::findOrFail($id);
        $file = Input::file('image');
        $filename = Auth::user()->id . '_' . $id . '_' . str_random(10) . '_' . $file->getClientOriginalName();
        $destinationPath = public_path() . '/img/posts/';
        Input::file('image')->move($destinationPath, $filename);
        // open 4/3 image for example
        $imagef = Image::make($destinationPath . $filename);
        $imagef->resizeCanvas(150, 150, 'center');
        File::makeDirectory($destinationPath . 'thumbs/', 0777, true, true);
        $imagef->save($destinationPath . 'thumbs/' . $filename);

        $image = new PostImage;
        $image->user_id = Auth::user()->id;
        $image->description = Input::get('description');
        $image->file_name = $filename;
        $image->save();

        $post->images()->save($image);
        return Redirect::route('posts.images', $id)->with('message', array('Success: File upload was successful'));

    }

    public function performRemovePost($post_id)
    {
        $post = Post::findOrFail($post_id);
        if ($post->user_id === Auth::user()->id || Auth::user()->isAdmin() || Auth::user()->isModerator()) {
            $post->delete();
            return Redirect::route('home')->with('message', array('Success: The post was successfully deleted'));
        }
        return Redirect::route('home')->with('message', array('Error: You are not allowed to do that.'));
    }

    public function performRemoveImage($id, $image_id)
    {
        $image = PostImage::findOrFail($image_id);
        $post = Post::findOrFail($id);
        if ($post->user_id === Auth::user()->id || Auth::user()->isAdmin() || Auth::user()->isModerator()) {
            $path = public_path() . '/img/posts/';
            if (File::exists($path . $image->file_name)) {
                File::delete($path . $image->file_name);
            }
            $path = public_path() . '/img/posts/thumbs/';
            if (File::exists($path . $image->file_name)) {
                File::delete($path . $image->file_name);
            }
            $image->delete();
            return Redirect::route('posts.images', $post->id)->with('message', array('Success: File was successfully deleted.'));
        }
        return Redirect::route('posts.images', $post->id)->with('message', array('You are not allowed to do that'));
    }
}
