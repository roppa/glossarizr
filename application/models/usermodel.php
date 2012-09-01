<?php 
class Usermodel extends CI_Model {

    function __construct()
    {
    	//Call the Model constructor
		parent::__construct();
	}
	
	function get_user ($uid, $oauth_provider, $username) 
	{
		$query = $this->db->query("SELECT * FROM `users` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			$query = $this->db->query("INSERT INTO `users` (oauth_provider, oauth_uid, username) VALUES ('$oauth_provider', $uid, '$username')");
			$query = $this->db->query("SELECT * FROM `users` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
			if ($query->num_rows() > 0) {
				return $query->row_array();
			}
		}
	}
}