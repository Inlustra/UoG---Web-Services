<?php
use SoapBox\Formatter\Formatter;

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 15/10/2014
 * Time: 19:05
 */
class APIController extends BaseController
{
    public function listPosts($ext = '.xml')
    {
        $page = 0;
        if(Input::has('page')) {
            if(is_numeric(Input::get('page')))
            $page = Input::get('page') * 5;
        }
        if (!(Input::has('images')
            || Input::has('user')
            || Input::has('sitterType')
            || Input::has('content')
            || Input::has('title')
            || Input::has('type')
            || Input::has('postcode')
            || Input::has('location'))
        ) {

            $posts = Post::with('images', 'sitterTypes')->take(5)->skip($page)->get()->toArray();
            if ($ext == '.xml') {
                return Response::xml("post", $posts);
            } else if ($ext == '.json') {
                return Response::json(array('posts' => $posts));
            } else {
                return Response::make('Response type not understood, available: .json, .xml', 404);
            }
        }
        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), static::$searchPostRules);
        if ($validator->fails()) {
            return Response::make('Area supplied was not a greenwich postcode', 400); // send back the input (not the password) so that we can repopulate the form
        }

        $query = new Post;
        if (Input::has('user')) {
            $query = $query->byuser(Input::get('user'));
        }
        if (Input::has('sitterType')) {
            $query = $query->ofType(Input::get('sitterType'));
        }
        if (Input::has('type')) {
            $query = $query->likeType(Input::get('type'));
        }
        if (Input::has('content')) {
            $query = $query->content(Input::get('content'));
        }
        if (Input::has('title')) {
            $query = $query->title(Input::get('title'));
        }
        if (Input::has('location')) {
            $query = $query->location(Input::get('location'));
        }
        if (Input::has('postcode')) {
            $postcode = trim(Input::get('postcode'));
            if (!empty($postcode)) {
                $postcodeDetails = Postcode::getCoordinates(Input::get('postcode'));
                if (is_null($postcodeDetails)) {
                    return Response::make('Internal error occured when attempting to get the
                     location specified, are you sure you entered it correctly?', 400);
                }
                $query = $query->distance($postcodeDetails['latitude'], $postcodeDetails['longitude']);
            }
        }
        $withimages = true;
        if (Input::has('images')) {
            $imgs = trim(Input::get('images'));
            if (strcasecmp($imgs, '0') == '0') {
                $query = $query->withoutimages();
                $withimages = false;
            } else {
                $query = $query->withimages();
            }
        }
        $array = array(
            'sitterTypes' => SitterType::all(),
            'posts' => $query->paginate(10),
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
        if ($withimages) {
            $query = $query->with('images');
        }
        if ($ext == '.xml') {
            return Response::xml("post", $query->with('sitterTypes')->take(5)->skip($page)->get()->toArray());
        } else if ($ext == '.json') {
            return Response::json(array('posts' => $query->with('sitterTypes')->take(5)->skip($page)->get()->toArray()));
        } else {
            return Response::make('Response type not understood, available: .json, .xml', 404);
        }
    }

    public function getImage($id)
    {
        if (is_null($image = PostImage::find($id))) {
            return Response::make("image not found", 404);
        }
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: inline; filename=\"" . $image->file_name . "\"");
        header("Content-Type: image/jpg");
        header("Content-Transfer-Encoding: binary");
        readfile(public_path()."/img/posts/".$image->file_name, $image->file_name);
    }

    public function getUser($ext = ".xml", $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return Response::make("", 404);
        }
        if ($ext == '.xml') {
            return Response::xml("user", $user->toArray(), "ArrayOfUser");
        } else if ($ext == '.json') {
            return Response::json(array('users' => $user));
        } else {
            return Response::make('Response type not understood, available: .json, .xml', 404);
        }
    }

    public function getSingleUser($id)
    {
        return $this->getUser(".xml", $id);
    }

    public function listUsers($ext = ".xml")
    {
        $users = User::all();
        if ($ext == '.xml') {
            return Response::xml("users", $users->toArray(), "ArrayOfUser");
        } else if ($ext == '.json') {
            return Response::json(array('users' => $users));
        } else {
            return Response::make('Response type not understood, available: .json, .xml', 404);
        }
    }

    public function listSitterTypes($ext = ".xml")
    {
        $users = SitterType::all();
        if ($ext == '.xml') {
            return Response::xml("sitterTypes", $users->toArray(),"ArrayOfSitterType");
        } else if ($ext == '.json') {
            return Response::json(array('sitterTypes' => $users));
        } else {
            return Response::make('Response type not understood, available: .json, .xml', 404);
        }
    }



    public function getSinglePost($id)
    {
        return $this->getPost(".xml", $id);
    }

    public function getPost($ext = '.xml', $id)
    {
        $posts = Post::with('images', 'sitterTypes')->find($id);
        if (is_null($posts)) {
            return Response::make("", 404);
        }
        if ($ext == '.xml') {
            return Response::xml("post", $posts->toArray());
        } else if ($ext == '.json') {
            return Response::json(array('post' => $posts));
        } else {
            return Response::make('Response type not understood, available: .json, .xml', 404);
        }
    }

    public function deletePost($id)
    {
        $post = Post::find($id);
        if (!is_null($post)) {
            return Response::make("", 204);
        }
        return Response::xml(array('posts' => $post));
    }

    public function createPost()
    {
        HelperUtil::toEloquent(new Post, Request::instance()->getContent())->save();
        return Response::make("", 204);
    }

    public static $searchPostRules = [
        'postcode' => 'greenwich|postcode'
    ];

    public function updatePost()
    {

    }

}