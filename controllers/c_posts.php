<?php
/**
 * Created by PhpStorm.
 * User: Jeff
 */

class posts_controller extends base_controller {

    public function __construct() {
        parent::__construct();

        # Make sure user is logged in if they want to use anything in this controller
        if(!$this->user) {
            die("Members only. <a href='/users/login'>Login</a>");
        }
    }


    public function index() {

        # Set up the View
        $this->template->content = View::instance('v_posts_index');
        $this->template->title   = "View Nuts" . " | " . APP_NAME;

        # CSS/JS includes
        # - head
        // $client_files_head = Array("");
        // $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
        //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # Build the query
        $q = "SELECT posts.content,
                     posts.created,
                     posts.user_id AS post_user_id,
                     users_users.user_id AS follower_id,
                     users.first_name,
                     users.last_name
                 FROM posts
           INNER JOIN users_users
                   ON posts.user_id = users_users.user_id_followed
           INNER JOIN users
                   ON posts.user_id = users.user_id
                WHERE users_users.user_id = " . $this->user->user_id .
                " AND users_users.created > users_users.dropped";

        # Run the query
        $posts = DB::instance(DB_NAME)->select_rows($q);

        # Pass data to the View
        $this->template->content->posts = $posts;
        $this->template->content->usertimezone = $this->user->timezone;

        # Set current menu item
        $this->template->nav_active = "dropdown";

        # Render the View
        echo $this->template;

    }

    public function add() {

        # Setup view
        $this->template->content = View::instance('v_posts_add');
        $this->template->title   = "Post New Nuts" . " | " . APP_NAME;

        # CSS/JS includes
        # - head
        // $client_files_head = Array("");
        // $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
        //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # Set current menu item
        $this->template->nav_active = "dropdown";

        # Render template
        echo $this->template;

    }

    public function p_add() {

        # Associate this post with this user
        $_POST['user_id']  = $this->user->user_id;

        # Unix timestamp of when this post was created / modified
        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();

        # Insert
        # Note we didn't have to sanitize any of the $_POST data because we're using the insert method which does it for us
        DB::instance(DB_NAME)->insert('posts', $_POST);

        # Quick and dirty feedback
        echo "Your post has been added. <a href='/posts/add'>Add another</a>";

    }

    public function users() {

        # Set up the View
        $this->template->content = View::instance("v_posts_users");
        $this->template->title   = "Follow Nuts" . " | " . APP_NAME;

        # CSS/JS includes
        # - head
        // $client_files_head = Array("");
        // $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
        //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # ------------------------------------------------------------------------------------
        #  Multi-Select method replaced with single DB select
        #  - single DB connect / retrieve is much more efficient
        #  - old code retained to avoid regression and clarify the change - jbl
        #
        #    # Build the query to get all the users
        #    $q = "SELECT *
        #            FROM users";
        #
        #    # Execute the query to get all the users.
        #    # Store the result array in the variable $users
        #    $users = DB::instance(DB_NAME)->select_rows($q);
        #
        #    # Build the query to figure out what connections does this user already have?
        #    # I.e. who are they following
        #    $q = "SELECT *
        #            FROM users_users
        #           WHERE user_id = "  .$this->user->user_id;
        #
        #    # Execute this query with the select_array method
        #    # select_array will return our results in an array and use the "users_id_followed" field as the index.
        #    # This will come in handy when we get to the view
        #    # Store our results (an array) in the variable $connections
        #    $connections = DB::instance(DB_NAME)->select_array($q, 'user_id_followed');
        # ------------------------------------------------------------------------------------

        # Single DB Select method
        $q = "SELECT users.user_id,
                     users.first_name,
                     users.last_name,
                     users_users.created,
                     users_users.dropped
                FROM users
           LEFT JOIN users_users
                  ON users.user_id = users_users.user_id_followed
                 AND users_users.user_id = " . $this->user->user_id;

        # Execute the query to get all the users into $users array.
        $users = DB::instance(DB_NAME)->select_rows($q);

        # Pass data (users and connections) to the view
        $this->template->content->users       = $users;
        $this->template->content->usertimezone = $this->user->timezone;
        # $this->template->content->connections = $connections;   // - no longer needed (see above)

        # Set current menu item
        $this->template->nav_active = "dropdown";

        # Render the view
        echo $this->template;
    }

    public function follow($user_id_followed) {

        # Prepare the data array to be inserted
        $data = Array(
            "created" => Time::now(),
            "dropped" => 0,
            "user_id" => $this->user->user_id,
            "user_id_followed" => $user_id_followed
        );

        # Do the insert
        DB::instance(DB_NAME)->insert('users_users', $data);

        # Send them back
        Router::redirect("/posts/users");

    }

    public function refollow($user_id_followed) {

        # Prepare the data array for the update
        $data = Array(
            "created" => Time::now()
        );
        $where_condition = 'WHERE user_id = '.$this->user->user_id.' AND user_id_followed = '.$user_id_followed;

        # Do the update
        DB::instance(DB_NAME)->update('users_users',$data, $where_condition);

        # Send them back
        Router::redirect("/posts/users");

    }

    public function unfollow($user_id_followed) {

        # Don't delete this connection, just drop it
        $data = Array(
            "dropped" => Time::now()
        );
        $where_condition = 'WHERE user_id = '.$this->user->user_id.' AND user_id_followed = '.$user_id_followed;

        # Do the update
        DB::instance(DB_NAME)->update('users_users',$data, $where_condition);

        # Send them back
        Router::redirect("/posts/users");

    }

} # eoc