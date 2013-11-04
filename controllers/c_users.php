<?php
/**
 * Created by PhpStorm.
 * User: Jeff Linson
 */

class users_controller extends base_controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        # This is a catch in the event user types "/users/" or "/users/index" in the URL.
        # - this is a double check; nav-bar does not have any call to "/users/index".
        # If user is blank, they're not logged in; redirect them to the login page
        # - otherwise, use the profile page as the default user page.
        if(!$this->user) {
            Router::redirect('/users/login');
        } else {
            Router::redirect('/users/profile');
        }
    } # End of index()

    public function signup($error_msg = NULL) {

        # First, set the content of the template with a view file
        $this->template->content = View::instance('v_users_signup');

        # Now set the <title> tag
        $this->template->title = "Sign Up" . " | " . APP_NAME;

        # Pass back any validation error messages
        if (!empty($error_msg)) {
            $this->template->content->user_msg = $error_msg;
        } else {
            $this->template->content->user_msg = "Please Sign-up";
        }

        # Sign-up specific CSS/JS includes
        $client_files_head = Array("/css/signin.css");
        $this->template->client_files_head = Utils::load_client_files($client_files_head);

        # Auto-timezone detection javascript
        $client_files_body = Array("/js/jstz-1.0.4.min.js");
        $this->template->client_files_body = Utils::load_client_files($client_files_body);
        $this->template->client_files_body .= "<br><script>$('input[name=timezone]').val(jstz.determine().name());</script>";

        # Set current menu item
        $this->template->nav_active = "signup";

        # Render the view
        echo $this->template;

    } # End of signup()

    public function p_signup() {

        # Augment the $_POST array with additional required parameters
        $_POST['created'] = Time::now();
        $_POST['modified'] = $_POST['created'];
        $_POST['last_login'] = $_POST['created'];
        # TODO: Need to deal with 'handle' and validate uniqueness
        $_POST['handle'] = '@' . $_POST['first_name'] . $_POST['last_name'];
        # TODO: Also need to validate email uniqueness (see User.php)

        # Perform basic input validation
        if ($_POST['password'] != $_POST['password_confirm']) {

            # Direct method() call does not refresh URL; use Router::redirect() - jbl
            # $this->signup("The passwords don't match. Please retry.");
            $error_msg = "The passwords don't match. Please retry.";
            Router::redirect("/users/signup/" . $error_msg);

        } else {
            # Encrypt the password and clear the password_confirm
            $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
            if (isset($_POST['password_confirm'])) unset($_POST['password_confirm']);

            # Create an encrypted token via their email address and a random string
            $_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());

            # TODO: Address 'remember_me' functionality
            if (isset($_POST['remember_me'])) unset($_POST['remember_me']);

            # Basic non-error-checked insert into DB; relies on auto-sanitize on inserts
            $user_id = DB::instance(DB_NAME)->insert('users', $_POST);

            if (!empty($user_id) and ($user_id > 0)) {

                # Acknowledge the sign-up and redirect to home
                # TODO: Pass "Success" to /users/profile/success to user that as validation (and prompt for more data)
                # TODO: If validation fails, return to the signup page; reset the entered values
                # $this->profile($user_id); - doesn't properly refresh the URL; user Router::redirect() - jbl
                Router::redirect("/users/profile/" . $user_id);

            } else {

                # Some error occurred - return to signup
                # - as above, the direct method call doesn't properly refresh the URL; user Router::redirect() - jbl
                # $this->signup("The sign-up failed to process. Please retry or contact support.");
                $error_msg = "The sign-up failed to process. Please retry or contact support.";
                Router::redirect("/users/signup/" . $error_msg);

            }

        } # endif

    } # End of p_signup()

    public function login($error_msg = NULL) {
        /** login() is called if login fails (provides other options, e.g. password reset)
         *  - primary login form built into nav-bar.
         */
        # First, set the content of the template with a view file
        $this->template->content = View::instance('v_users_login');

        # Now set the <title> tag
        $this->template->title = "Login" . " | " . APP_NAME;

        # Pass back any validation error messages
        if (!empty($error_msg)) {
            $this->template->content->user_msg = $error_msg;
        } else {
            $this->template->content->user_msg = "Please Login";
        }

        # Login specific CSS/JS includes
        $client_files_head = Array("/css/signin.css");
        $this->template->client_files_head = Utils::load_client_files($client_files_head);

        # Set current menu item
        $this->template->nav_active = "";

        # Render the view
        echo $this->template;

    } # End of method

    public function p_login() {
        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);

        # Hash submitted password so we can compare it against one in the db
        $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

        # Search the db for this email and password
        # Retrieve the token if it's available
        $q = "SELECT token
                FROM users
               WHERE email = '".$_POST['email']."'
                 AND password = '".$_POST['password']."'";

        $token = DB::instance(DB_NAME)->select_field($q);

        # If we didn't find a matching token in the database, it means login failed
        if(!$token) {

            # Send them back to the login page
            Router::redirect("/users/login/");

        # But if we did, login succeeded!
        } else {
            /** Store this token in a cookie using setcookie()
             *  Important Note: *Nothing* else can echo to the page before setcookie is called
             *  Not even one single white space.
             *  param 1 = name of the cookie
             *  param 2 = the value of the cookie
             *  param 3 = when to expire
             *  param 4 = the path of the cooke (a single forward slash sets it for the entire domain)
             */
            setcookie("token", $token, strtotime('+1 year'), '/');

            # Send them to the main page - or whever you want them to go
            Router::redirect("/");

        }
    } # End of p_login()

    public function logout() {

        # Generate and save a new token for next login
        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

        # Create the data array we'll use with the update method
        # In this case, we're only updating one field, so our array only has one entry
        $data = Array("token" => $new_token);

        # Do the update
        DB::instance(DB_NAME)->update("users", $data, "WHERE token = '".$this->user->token."'");

        # Delete their token cookie by setting it to a date in the past - effectively logging them out
        setcookie("token", "", strtotime('-1 year'), '/');

        # Send them back to the main index.
        Router::redirect("/");

    } # End of logout()


    public function profile($user_name = NULL) {

        # If user is blank, they're not logged in; redirect them to the login page
        # - this is a double check; nav-bar should not enable "profile" for non-logged-in user.
        # - this is necessary in event user types "/users/profile" in the URL.
        if(!$this->user) {
            Router::redirect('/users/login');
        }

        # If they weren't redirected away, continue:

        # Setup view
        $this->template->content = View::instance('v_users_profile');
        $this->template->title   = "Profile for " . $this->user->first_name;

        # Render template
        echo $this->template;

    } # End of profile()

} # End of c_users.php