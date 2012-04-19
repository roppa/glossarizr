<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('wordmodel');
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