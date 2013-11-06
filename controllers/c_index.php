<?php
/**
 * Created by PhpStorm.
 * User: Jeff Linson
 */

class index_controller extends base_controller {
	
	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
	} 
		
	/*-------------------------------------------------------------------------------------------------
	Accessed via http://localhost/index/index/  (or simply http://localhost/)
	-------------------------------------------------------------------------------------------------*/
	public function index() {
		
		# Any method that loads a view will commonly start with this
		# First, set the content of the template with a view file
		$this->template->content = View::instance('v_index_index');
			
		# Now set the <title> tag
		$this->template->title = APP_NAME . " | " . APP_TAGLINE;
	
		# CSS/JS includes
        # - head
        // $client_files_head = Array("");
        // $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
	    //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # Set current menu item
            $this->template->nav_active = "home";
	      					     		
		# Render the view
			echo $this->template;

	} # End of index()

    /*-------------------------------------------------------------------------------------------------
        Accessed via http://localhost/index/features/  - a static feature list page
        -------------------------------------------------------------------------------------------------*/
    public function features() {

        # Any method that loads a view will commonly start with this
        # First, set the content of the template with a view file
        $this->template->content = View::instance('v_index_features');

        # Now set the <title> tag
        $this->template->title = "Features" . " | " . APP_NAME;

        # CSS/JS includes
        # - head
        // $client_files_head = Array("");
        // $this->template->client_files_head = Utils::load_client_files($client_files_head);
        # - body
        //	$client_files_body = Array("");
        //	$this->template->client_files_body = Utils::load_client_files($client_files_body);

        # Set current menu item
        $this->template->nav_active = "features";

        # Render the view
        echo $this->template;

    } # End of features

} # End of c_index.php
