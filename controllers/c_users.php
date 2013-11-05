<?php
/**
 * Created by PhpStorm.
 * User: Jeff Linson
 */

class users_controller extends base_controller {

    # Initialize the validation error message array used by login() and signup()
    # - array allows passing short-msg key in param with long-msg for user display
    protected $error_msg = array(
        "blank-field"    =>  "Missing information. All data is required. Please retry.",
        "blank-email"    =>  "Email address cannot be blank. Please retry.",
        "blank-pwd"      =>  "Password cannot be blank. Please re-enter.",
        "blank-name"     =>  "First and last name are required. Please re-enter.",
        "blank-handle"   =>  "The handle/user-id cannot be blank. Please re-enter.",
        "long-handle"    =>  "Handle/user-id is too long (exceeds 25 characters). Please re-enter.",
        "short-handle"   =>  "Handle/user-id too short (under 6 characters). Please re-enter.",
        "long-pwd"       =>  "Password is too long (exceeds 25 characters). Please re-enter.",
        "short-pwd"      =>  "Password is too short (under 6 characters). Please re-enter.",
        "invalid-login"  =>  "Email and password combination not found. Please re-try.",
        "pwd-mismatch"   =>  "The passwords don't match. Please re-confirm.",
        "handle-dupe"    =>  "The handle/user-id is already in use. Please try another ID.",
        "email-dupe"     =>  "Email already in database. Enter different email or request password reset.",
        "email-invalid"  =>  "Email is not a valid format. Please re-enter.",
        "dberror"        =>  "A database error occurred. Process failed. Please retry or contact support.",
        "error"          =>  "An unknown error occurred. Processing failed. Please re-try."
    );

    /*------------------------------------------------------------------------------------------------*/

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        # This is a catch in the event user types "/users/" or "/users/index" in the URL.
        # - this is a double check; nav-bar does not have any call to "/users/index".
        # If user is blank, they're not logged in; redirect them to the login page
        # - otherwise, use the profile page as the default user page.
        if (!$this->user) {
            Router::redirect('/users/login');
        } else {
            Router::redirect('/users/profile');
        }
    } # End of index()

    /* --------------------------------------------------------------------------------------------------------
    Process all common signup / login error checking here
    - while javascript may trap some of these errors, javascript may be disabled; this double-checks.
    - also, 'required' is not supported in IE9 or prior; need to double check here.
    -------------------------------------------------------------------------------------------------------- */
    public function cmmn_errorcheck() {
        # initialize the $error return value to empty string - signifies success until proven otherwise
        $error = "";

        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);

        foreach($_POST as $field => $value) {
            $_POST[$field] = trim($value);
            if (empty($value)) {
                switch ($field) {
                    case "first_name":
                    case "last_name":
                        $error = "blank-name";
                        break 2;
                    case "handle":
                        $error = "blank-handle";
                        break 2;
                    case "email":
                        $error = "blank-email";
                        break 2;
                    case "password":
                        $error = "blank-pwd";
                        break 2;
                    default:
                        $error = "blank-field";
                }
            }

        }

      return $error;

    } # End of cmmn_errorcheck()

    /**
     * Validate an email address.
     *  Provide email address (raw input)
     *  Returns true if the email address has the email format
     *  and the domain exists.
     *
     * Source: http://www.linuxjournal.com/article/9585?page=0,3
     *
     * Other option: user web svc, e.g. http://www.email-validator.net
     */
    public function valid_email($email) {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;

        } else {
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen-1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;

    } # End of valid_email()

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
    public function confirm_unique_handle($handle) {

        # No need to sanitize - was sanitized in cmmn_errorcheck()
        //$handle = DB::instance(DB_NAME)->sanitize($handle);

        $q = "SELECT user_id
			    FROM " . $this->userObj->users_table."
			   WHERE handle = '" . $handle . "'";

        $user_id = DB::instance(DB_NAME)->select_row($q);

        # If we don't have a user_id that means this handle is free to use
        if(!$user_id)
            return true;
        else
            return false;

    } # End of confirm_unique_handle()

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
    public function signup($error = NULL) {

        # First, set the content of the template with a view file
        $this->template->content = View::instance('v_users_signup');

        # Now set the <title> tag
        $this->template->title = "Sign Up" . " | " . APP_NAME;

        # Pass back any validation error messages
        if (!empty($error)) {
            $this->template->content->user_msg = $this->error_msg[$error];
        } else {
            $this->template->content->user_msg = "&nbsp;";
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

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
    public function p_signup() {

        # First check the password match - the most likely source of error (Signup Specific Trap)
        if ($_POST['password'] != $_POST['password_confirm']) {

            # Direct method() call [e.g. $this->signup("Password-Mismatch");] does not refresh URL.
            # - use Router::redirect() - jbl
            $error = "pwd-mismatch";
            Router::redirect("/users/signup/" . $error);

        }

        # Now test the password length (Signup Specific Test)
        # - is the password too long or too short
        $pwd_len = strlen($_POST['password']);
        if ($pwd_len > 25) {
            $error = "long-pwd";
            Router::redirect("/users/signup/" . $error);
        } elseif ($pwd_len < 6) {
            $error = "short-pwd";
            Router::redirect("/users/signup/" . $error);
        }


        # Now call the common error checking for signup / login
        $error = $this->cmmn_errorcheck();
        if ($error != "") {
            Router::redirect("/users/signup/" . $error);
        }

        # Now test the email address (Signup Specific Test)
        # - is the email a valid format / valid domain
        if (!$this->valid_email($_POST['email'])) {
            $error = "email-invalid";
            Router::redirect("/users/signup/" . $error);
        }
        # - is the email unique (use the User library f(n))
        $available = $this->userObj->confirm_unique_email($_POST['email']);
        if (!$available) {
            $error = "email-dupe";
            Router::redirect("/users/signup/" . $error);
        }

        # Now test the handle/user-id (Signup Specific Test)
        # - is the handle too long or too short
        $id_len = strlen($_POST["handle"]);
        if ($id_len > 25) {
            $error = "long-handle";
            Router::redirect("/users/signup/" . $error);
        } elseif ($id_len < 6) {
            $error = "short-handle";
            Router::redirect("/users/signup/" . $error);
        }
        # - is the handle unique
        $available = $this->confirm_unique_handle($_POST['handle']);
        if (!$available) {
            $error = "handle-dupe";
            Router::redirect("/users/signup/" . $error);
        }

        # Now augment the $_POST array with additional required parameters
        $_POST['created'] = Time::now();
        $_POST['modified'] = $_POST['created'];
        $_POST['last_login'] = $_POST['created'];

        # Encrypt the password and clear the password_confirm
        # - to keep the encryption logic in one place (i.e. User) - call the common f(n)
        # - doesn't save on code, but better to maintain in one place
        //$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
        $_POST['password'] = $this->userObj->hash_password($_POST['password']);
        if (isset($_POST['password_confirm'])) unset($_POST['password_confirm']);

        # Create an encrypted token via their email address and a random string
        # - to keep the encryption logic in one place (i.e. User) - call the common f(n)
        # - doesn't save on code, but better to maintain in one place
        //$_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());
        $_POST['token'] = $this->userObj->generate_token($_POST['email']);

        # TODO: Address 'remember_me' functionality
        if (isset($_POST['remember_me'])) unset($_POST['remember_me']);

        # Basic non-error-checked insert into DB; relies on auto-sanitize on library f(n) inserts
        $user_id = DB::instance(DB_NAME)->insert('users', $_POST);

        if (!empty($user_id) and ($user_id > 0)) {

            # Acknowledge the sign-up and redirect to home
            # TODO: Pass "Success" to /users/profile/success to user that as validation (and prompt for more data)
            # TODO: If validation fails, return to the signup page; reset the entered values
            # $this->profile($user_id); - doesn't properly refresh the URL; user Router::redirect() - jbl
            //Router::redirect("/users/profile/" . $user_id);
            $this->db_login();

        } else {

            # Some error occurred - return to signup
            # - as above, the direct method call doesn't properly refresh the URL; user Router::redirect() - jbl
            # $this->signup("The sign-up failed to process. Please retry or contact support.");
            $error = "dberror";
            Router::redirect("/users/signup/" . $error);

        }

    } # End of p_signup()

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
    public function login($error = NULL) {
        /** login() is called if login fails (provides other options, e.g. password reset)
         *  - primary login form built into nav-bar.
         */
        # First, set the content of the template with a view file
        $this->template->content = View::instance('v_users_login');

        # Now set the <title> tag
        $this->template->title = "Login" . " | " . APP_NAME;

        # Pass back any validation error messages
        if (!empty($error)) {
            $this->template->content->user_msg = $this->error_msg[$error];
        } else {
            $this->template->content->user_msg = "&nbsp;";
        }

        # Login specific CSS/JS includes
        $client_files_head = Array("/css/signin.css");
        $this->template->client_files_head = Utils::load_client_files($client_files_head);

        # Set current menu item
        $this->template->nav_active = "";

        # Render the view
        echo $this->template;

    } # End of method

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
    public function p_login() {
        # Perform common input errorcheck -
        # - full login form should trap 'required' fields, but nav-bar login does not.
        # - 'required' is not supported in IE9 or less, so double check here.
        $error = $this->cmmn_errorcheck();
        if ($error != "") {
            Router::redirect("/users/login/" . $error);
        }

        # Hash submitted password so we can compare it against one in the db
        # - to keep the encryption logic in one place (i.e. User) - call the common f(n)
        # - doesn't save on code, but better to maintain in one place
        //$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
        $_POST['password'] = $this->userObj->hash_password($_POST['password']);

        # Now pass control to the database portion shared by login and signup
        $this->db_login();

    } # End of p_login()

    /*-------------------------------------------------------------------------------------------------
    DB_login serves to take functionality shared by p_login and p_signup and consolidate.
    - validations and password hashing have already been performed in the separate methods.
	-------------------------------------------------------------------------------------------------*/
    public function db_login() {
        # Search the db for this email and password
        # Retrieve the token if it's available
        $q = "SELECT token
                FROM users
               WHERE email = '" . $_POST['email'] . "'
                 AND password = '" . $_POST['password'] . "'";

        $token = DB::instance(DB_NAME)->select_field($q);

        # If we didn't find a matching token in the database, it means login failed
        if (!$token) {
            # Send them back to the login page
            $error = "invalid-login";
            Router::redirect("/users/login/" . $error);

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

            # Send them to the main page - or wherever you want them to go
            Router::redirect("/users/profile");

        }
    } # End of p_login()

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
    public function logout() {

        # Generate and save a new token for next login
        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

        # Create the data array we'll use with the update method
        # In this case, we're only updating one field, so our array only has one entry
        $data = array("token" => $new_token);

        # Do the update
        DB::instance(DB_NAME)->update("users", $data, "WHERE token = '".$this->user->token."'");

        # Delete their token cookie by setting it to a date in the past - effectively logging them out
        setcookie("token", "", strtotime('-1 year'), '/');

        # Send them back to the main index.
        Router::redirect("/");

    } # End of logout()

    /*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
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