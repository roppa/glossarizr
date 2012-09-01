<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('wordmodel');
		$this->load->model('usermodel');
		
		$this->load->library('session');
		
		include_once('./twitter/twitteroauth.php');
	}
	
	function login () 
	{		
		if ($this->uri->segment(3) == 'twitter') {
			$twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
			// Requesting authentication tokens, the parameter is the URL we will be redirected to
			$request_token = $twitteroauth->getRequestToken(site_url() . '/home/twitter');
			// Saving them into the session
			$this->session->set_userdata('oauth_token', $request_token['oauth_token']);
			$this->session->set_userdata('oauth_token_secret', $request_token['oauth_token_secret']);
			
			// If everything goes well..
			if ($twitteroauth->http_code == 200) {
			    // Let's generate the URL and redirect
			    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
			    redirect($url, 'refresh');
			} else {
			    // It's a bad idea to kill the script, but we've got to know when there's an error.
			    die('Something wrong happened.');
			}
		} else if ($this->uri->segment(3) == 'facebook') {
		
		} else {
			//redirect 404?
		}	
	}
	
	function twitter ()
	{
		//codeigniter get request parameters
		$params = $this->input->get(NULL, TRUE);
		
		if (!empty($params['oauth_verifier']) && ($this->session->userdata('oauth_token') != false) && ($this->session->userdata('oauth_token_secret') != false))
		{
			$oauth_verifier = (isset($params['oauth_verifier'])) ? $params['oauth_verifier'] : '';
		    // We've got everything we need
		    $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $this->session->userdata('oauth_token'), $this->session->userdata('oauth_token_secret'));
			// Let's request the access token
			 $access_token = $twitteroauth->getAccessToken($oauth_verifier);
			//Save it in a session var
		    $this->session->set_userdata('access_token', $access_token);
			// Let's get the user's info
		    $user_info = $twitteroauth->get('account/verify_credentials');
		    /*
		    //to get all values:
		    print_r($user_info);
		    */
		    if (isset($user_info->error)) {
		        // Something's wrong, go back to login 
		        redirect(site_url() . '/home/login/twitter', 'refresh');
		    } else {
		        $uid = $user_info->id;
		        $username = $user_info->name;
		        $userdata = $this->usermodel->get_user($uid, 'twitter', $username);
		        if(!empty($userdata)){
		            $this->session->set_userdata('id', $userdata['id']);
		            $this->session->set_userdata('oauth_id', $uid);
		            $this->session->set_userdata('username', $userdata['username']);
		            $this->session->set_userdata('oauth_provider', $userdata['oauth_provider']);
		            redirect(site_url(), 'refresh');
		        }
		    }
		    
		    
		} else {
		    // Something's missing, go back to square 1
		    redirect(site_url() . '/home/login/twitter', 'refresh');
		}
	}

	function logout () 
	{
		$this->session->sess_destroy();
		redirect(base_url(), 'refresh');
	}

	function index()
	{
		$this->load->library('form_validation');
		$this->load->helper('word_list');

		$this->form_validation->set_rules('words', 'Word/s', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('home');
		}
		else
		{
			//curl variables
			//pass in parameter, either single word or array. If array, loop through
			$apiKey = '1766cba83f05e0a627fbe111ab8ae039';
			$baseUrl = 'https://api.pearson.com/longman/dictionary/0.1/';
			$dataFmt = '.json';
			$searchUrl = $baseUrl . 'entry' . $dataFmt;
			$searchUrl .= '?apikey=' . $apiKey . '&q=';

			//get list of words
			$data['word_list'] = word_list($this->form_validation->set_value('words'));
			$data['defined_words'] = array();
			$data['undefined_words'] = array();
			$result_str = '';
			//now get definitions for each
			//first, check to see if it is in mongo.db
			//if it isn't, get it from dictionary site, then add json object into the db, then process word
			for ($i = 0; $i < sizeof($data['word_list']); $i++) {
				// Start session (also wipes existing/previous sessions)
				$this->curl->create($searchUrl . $data['word_list'][$i]);
				// Options
				$this->curl->options(array(CURLOPT_BUFFERSIZE => 10, CURLOPT_SSL_VERIFYPEER => FALSE));
				// Execute - returns json object, Entries->Entries - if only one entry it returns head, body etc. Otherwise it returns {},{},{}
				$json = $this->curl->execute();
				//format json into an array
				$entries = json_decode($json, true);
				//if entries not null
				if ($entries['Entries'] != '') {
					$result_str .= '<article><ol>';
					$result_str .= $this->wordmodel->process_entries ($entries['Entries']['Entry']);
					$result_str .= '</ol></article>';
					array_push($data['defined_words'], $data['word_list'][$i]);
				} else {
					array_push($data['undefined_words'], $data['word_list'][$i]);
				}
			}

			$data['results'] = $result_str;
			$this->load->view('home', $data);
		}	

	}
	
	function word() {
		$this->load->view('word');
	}
	
	function json() {
		$this->load->view('json');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */