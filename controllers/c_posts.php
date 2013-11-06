<?php
/**
 * Created by PhpStorm.
 * User: Jeff
 */

class posts_controller extends base_controller {

    # Initialize the validation error message array used by add()
    # - array allows passing short-msg key in param with long-msg for user display
    # - variable naming: $error => $error_msg  - concatenates into usr_msg (here) [user_msg (in view)]
    protected $error_msg = array(
        "post-added"      =>  "<strong>Last post successfully saved.</strong>",
        "post-updated"    =>  "<strong>Last post successfully updated.</strong>",
        "blank-post"      =>  "<strong>Empty post. Nothing saved.</strong>",
        "no-changes"      =>  "<strong>Nothing changed. Post not modified.</strong>",
        "confirm-delete"  =>  "<strong>Confirm delete.  Press 'Delete Post' to confirm.</strong>",
        "deleted"         =>  "<strong>Last post successfully deleted.</strong>",
        "long-post"       =>  "Posts are limited to 255 characters. Post truncated.",
        "spcl-chars"      =>  "HTML special characters encode.",
        "trimmed"         =>  "Trailing spaces were removed from the post.",
        "dberror"         =>  "<strong>A database error occurred. Process failed.</strong> Please retry or contact support.",
        "error"           =>  "An unknown error occurred. Processing failed. Please re-try."
    );

    /*------------------------------------------------------------------------------------------------*/
    public function __construct() {
        parent::__construct();

        # Make sure user is logged in if they want to use anything in this controller
        if(!$this->user) {
            //die("Members only. <a href='/users/login'>Login</a>");
            Router::redirect('/users/login');
        }
    }

    /*-------------------------------------------------------------------------------------------------
    Index() is the basic view of posts (for people being followed).
	-------------------------------------------------------------------------------------------------*/
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
                     users.last_name,
                     users.handle
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

    } # End of index()

    /*-------------------------------------------------------------------------------------------------
    cmmn_errorcheck() - consolidates validations common to both add, update and delete
    - uses an $error to get around inability to pass an array as a Router parameter
    -------------------------------------------------------------------------------------------------*/
    public function cmmn_errorcheck($error) {
        $start_len = strlen($_POST['content']);

        # Now handle htmlspecialcharacters (may increase length)
        $_POST['content'] = htmlspecialchars($_POST['content']);

        $end_len = strlen($_POST['content']);

        if ($end_len != $start_len) {
            # We must have messed with their post - flag the change
            # non-fatal error - just concat error string to the return
            $error = AppUtils::push_message($error, "spcl-chars");
        }
        if ($end_len > 255) {
            # Post is too long for database - truncate and notify user.
            # - should never get here if textarea limit does its job (and spcl-chars aren't added).
            $_POST['content'] = substr($_POST['content'],0,255);
            $error = AppUtils::push_message($error, "long-post");
        }

        return $error;
    }

    /*-------------------------------------------------------------------------------------------------
    Add - allows users to create new posts
    - uses an $err_str to get around inability to pass an array as a Router parameter
    -------------------------------------------------------------------------------------------------*/
    public function add($err_str = NULL) {

        # Setup view
        $this->template->content = View::instance('v_posts_add');
        $this->template->title   = "Post New Nuts" . " | " . APP_NAME;

        # Build the user message - pass back message to the view
        if (!empty($err_str)) {
            $err_array = AppUtils::pop_message($err_str);
            $usr_msg = "";

            foreach ($err_array as $error) {
                if (isset($this->error_msg[$error])) {
                    $usr_msg = $usr_msg . $this->error_msg[$error] . "&nbsp;";
                }
            }
            $this->template->content->user_msg = $usr_msg;
        } else {
            $this->template->content->user_msg = "&nbsp;";
        }

        # CSS/JS includes
        # - head
        $client_files_head = Array("/css/posts.css");
        $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
        //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # Set current menu item
        $this->template->nav_active = "dropdown";

        # Render template
        echo $this->template;

    } # End of add()

    /*-------------------------------------------------------------------------------------------------
    p_add() - receives $_POST from v_posts_add -
     -------------------------------------------------------------------------------------------------*/
    public function p_add() {
        # Perform some basic validations
        # Initialize the $error
        $error = "";

        # Check post length (varchar(255) limit - textarea limit should restrict
        $start_len = strlen($_POST['content']);
        $_POST['content'] = trim($_POST['content']);
        $end_len = strlen($_POST['content']);

        if ($start_len != $end_len) {
            # non-fatal error - just concat error string to the return
            $error = AppUtils::push_message($error, "trimmed");
        }
        if (empty($_POST['content'])) {
            # No point in going further - we have nothing to save.
            # - this intentionally over-writes anything in $error - jbl
            $error = AppUtils::push_message($error, "blank-post");
            Router::redirect("/posts/add/" . $error);
        }

        $error = $this->cmmn_errorcheck($error);

         # Associate this post with this user
        $_POST['user_id']  = $this->user->user_id;

        # Unix timestamp of when this post was created / modified
        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();

        # Insert
        # Note we didn't have to sanitize any of the $_POST data because we're using the insert method which does it for us
        $rtn = DB::instance(DB_NAME)->insert_row('posts', $_POST);

        if (empty($rtn) or ($rtn <= 0)) {
            # DB insert failed - fatal error
            # - this intentionally over-writes anything in $error - jbl
            $error = "dberror";
            Router::redirect("/posts/add/" . $error);
        }

        # If we got here, then nothing fatal happened
        $error = AppUtils::push_message($error, "post-added");
        Router::redirect("/posts/add/" . $error);

    } # End of p_add()

    /*-------------------------------------------------------------------------------------------------
    edit() - allows users to edit their own posts -
    - uses an $err_str to get around inability to pass an array as a Router parameter
 	-------------------------------------------------------------------------------------------------*/
    public function edit($msg_str = NULL) {

        # Setup view - slightly modified add() view -
        $this->template->content = View::instance('v_posts_edit');
        $this->template->title   = "Edit Your Nuts" . " | " . APP_NAME;

        $post_id = 0;
        $delete_flg = 0;
         # Grab the post_id or Build the user message - pass back the result to the view
        if (!empty($msg_str)) {
            $err_array = AppUtils::pop_message($msg_str);
            $usr_msg = "";

            if (is_numeric($err_array[0])) {
                # Not an error, put rather a post_id param
                $post_id = DB::instance(DB_NAME)->sanitize( htmlspecialchars($err_array[0]) );
            }
            # Parse array to build the user message.
            foreach ($err_array as $error) {
                if (isset($this->error_msg[$error])) {
                    $usr_msg = $usr_msg . $this->error_msg[$error] . "&nbsp;";
                    if ($error == "confirm-delete") {
                        # Set the delete confirm flag
                        $delete_flg = 1;
                    }
                }
            }
            $this->template->content->user_msg = $usr_msg;

        } else {
            $this->template->content->user_msg = "&nbsp;";
        }

        # CSS/JS includes
        # - head
        $client_files_head = Array("/css/posts.css");
        $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
        //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # Build the query
        $q = "SELECT posts.post_id,
                     posts.content,
                     posts.created,
                     posts.modified,
                     users.first_name,
                     users.last_name,
                     users.handle
                 FROM posts
           INNER JOIN users
                   ON posts.user_id = users.user_id
                WHERE users.user_id = " . $this->user->user_id;

        # Run the query
        $posts = DB::instance(DB_NAME)->select_rows($q);

        # Pass data to the View
        $this->template->content->content = "";
        $this->template->content->delete_flg = $delete_flg;
        $this->template->content->content_id = $post_id;
        $this->template->content->posts = $posts;

        # Set current menu item
        $this->template->nav_active = "dropdown";

        # Render template
        echo $this->template;


        } # End of edit()

    /*-------------------------------------------------------------------------------------------------
    p_edit() - shows a list of users to follow or un-follow
	-------------------------------------------------------------------------------------------------*/
    public function p_edit() {
        $error = "";

        # Flag a confirmed post delete
        if ($_POST['delete_flg'] == 1) {
            # Delete confirmed
            # - this must be our second pass thru
            $where_condition = 'WHERE user_id = '. $this->user->user_id .
                                ' AND post_id = '. $_POST['content_id'];

            # Do the delete
            $rtn = DB::instance(DB_NAME)->delete('posts', $where_condition);

            if ($rtn > 0) {
                $error = "deleted";
            } else {
                $error = "dberror";
            }
            # Send them back
            Router::redirect("/posts/edit/" . $error);
        }

        # Check post length (varchar(255) limit - textarea limit should restrict
        $start_len = strlen($_POST['content']);
        $_POST['content'] = trim($_POST['content']);
        $end_len = strlen($_POST['content']);

        if ($start_len != $end_len) {
            # non-fatal error - just concat error string to the return
            $error = AppUtils::push_message($error, "trimmed");
        }

        if ($_POST['content'] == $_POST['orig_content']) {
            $error = AppUtils::push_message($error, "no-changes");
            Router::redirect("/posts/edit/" . $_POST['content_id'] . "&" . $error);
        }
        if (empty($_POST['content'])) {
            if ($_POST['delete_flg'] == '0')
                # No point in going further - we need to confirm the delete.
                # - this intentionally over-writes anything in $error - jbl
                $error = AppUtils::push_message($error, "confirm-delete");
                Router::redirect("/posts/edit/" . $_POST['content_id'] . "&" . $error);
        }

        $error = $this->cmmn_errorcheck($error);

        # Prepare the SQL
        $where_clause = "WHERE user_id = " . $this->user->user_id .
              " AND post_id = " . $_POST['content_id'];

        # Create the data array we'll use with the update method
        $data = array("content" => $_POST['content'],
                      "modified" => Time::now());

        # Update
        # Note we didn't have to sanitize any of the $_POST data because we're using the insert method which does it for us
        $rtn = DB::instance(DB_NAME)->update_row('posts', $data, $where_clause);

        if (empty($rtn) or ($rtn <= 0)) {
            # DB insert failed - fatal error
            # - this intentionally over-writes anything in $error - jbl
            $error = "dberror";
            Router::redirect("/posts/edit/" . $error);
        }

        # If we got here, then nothing fatal happened
        $error = AppUtils::push_message($error, "post-updated");
        Router::redirect("/posts/edit/" . $error);

    } # End of p_edit()

    /*-------------------------------------------------------------------------------------------------
    users() - shows a list of users to follow or un-follow
	-------------------------------------------------------------------------------------------------*/
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
                     users.handle,
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

    } # End of users()

    /*-------------------------------------------------------------------------------------------------
    Follow function called from v_posts_users -
	-------------------------------------------------------------------------------------------------*/
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

    } # End of follow()

    /*-------------------------------------------------------------------------------------------------
    Follow function called from v_posts_users -
	-------------------------------------------------------------------------------------------------*/
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

    } # End of refollow()

    /*-------------------------------------------------------------------------------------------------
    Follow function called from v_posts_users -
	-------------------------------------------------------------------------------------------------*/
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

    } # End of unfollow()

} # eoc