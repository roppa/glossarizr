<?php

class Upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index()
	{
		$this->load->view('upload_form', array('error' => ' ' ));
	}

	function do_upload()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'txt';
		$config['max_size']	= '100';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_form', $error);
		}
		else
		{
			$upload_data = $this->upload->data();
			$string = read_file($upload_data['full_path']);
			$string = trim(strtolower($string));
			$new_string = preg_replace("[^A-Za-z0-9/']", " ", $string);
			
			$stringArray = explode(" ", $new_string);
			
			$newArray = array_unique($stringArray);
			asort($newArray);
			
			foreach($newArray as $key => $value)
			{
			  if($value == "" || is_null($value))
			  {
				unset($newArray[$key]);
			  }
			
			}
			
			$words = array_values($newArray);
			$str = '';

			for($i = 0;$i < sizeof($words); $i++)
			{
				//$str .=  '<h1>' . $words[$i] . '</h1>';
				//$url = 'https://api.pearson.com/longman/dictionary/entry.html?q=' . $words[$i] . '&apikey=1766cba83f05e0a627fbe111ab8ae039';
				//$url = 'http://api.pearson.com/longman/dictionary/0.1/entry.json?apikey=1766cba83f05e0a627fbe111ab8ae039&q=cat';
				
		
				$apiKey = '1766cba83f05e0a627fbe111ab8ae039';
				$baseUrl = 'https://api.pearson.com/longman/dictionary/0.1';
				$dataFmt = '.json';
				$searchUrl = $baseUrl . '/entry' . $dataFmt;
				$searchUrl .= '?apikey=' . $apiKey . '&q=' . 'cat';
			
				echo '<p>' . $searchUrl . '</p>';
				//echo $this->curl->simple_get($searchUrl);
			
				//$this->curl->simple_get('http://api.pearson.com/longman/dictionary/0.1/entry.json?apikey=1766cba83f05e0a627fbe111ab8ae039&q=cat');
				echo $this->curl->simple_get('http://api.pearson.com/longman/dictionary/0.1/entry.json?apikey=1766cba83f05e0a627fbe111ab8ae039&q=cat', array(CURLOPT_SSL_VERIFYPEER => FALSE));
		
	
				//echo $this->curl->simple_get($url, array(CURLOPT_SSL_VERIFYPEER => FALSE));
				//var_dump(json_decode($json));
				//$str .= '<hr/>';
			}
			
			$data['words'] = $str;

			$this->load->view('upload_success', $data);
		}
	}
}
?>