<?php

class base_controller {
	
	public $user;
	public $userObj;
	public $template;
	public $email_template;

	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function __construct() {
						
		# Instantiate User obj
			$this->userObj = new User();
			
		# Authenticate / load user
			$this->user = $this->userObj->authenticate();					
						
		# Set up templates
			$this->template 	  = View::instance('_v_template');
			$this->email_template = View::instance('_v_email');			
								
		# So we can use $user in views			
			$this->template->set_global('user', $this->user);

        # Initialize the active menu item selector
            $this->template->nav_active = "";

        # Now set the login/logout navigation based on $this->user
        # FYI - if($user) fails here - jbl
            if($this->user) {
                $this->template->lognav = View::instance('_v_logout');
            } else {
                $this->template->lognav = View::instance('_v_login');
            }
	}
	
} # eoc
