<?php
/**
 * Created by PhpStorm.
 * User: Jeff Linson
 */

class users_controller extends base_controller {

    public function __construct() {
        parent::__construct();
        echo "users_controller construct() called<br><br>";
    }

    public function index() {
        echo "This is the index page.<br>";
        echo "Time:" . Time::now();
    }

    public function signup() {
        echo "This is the signup page.";
    }

    public function login() {
        echo "This is the login page.";
    }

    public function logout() {
        echo "This is the logout page.";
    }

    public function profile($user_name = NULL) {

        $this->template->content = View::instance('v_users_profile');
        $this->template->title = "Profile";
        $this->template->content->user_name = $user_name;

        echo $this->template;
    }
} # eoc