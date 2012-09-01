<?php
	
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	/**
	 * User
	 *
	 *
	 * @package             
	 * @author              
	 * @license
	 * @version             1.0
	 */
	
	class User
	{
		protected $id;
		protected $username;
		protected $email;
		protected $created;
		protected $last_login;
		protected $first_name;
		protected $last_name;
		protected $object;
		
		public function __construct($properties)
		{
			$this->object =& get_instance();
			
			$this->id = $properties['id'];
			$this->username = $properties['username'];
			$this->email = $properties['email'];
			$this->created = $properties['created'];
			$this->last_login = $properties['last_login'];
			$this->website = $properties['website'];
			$this->first_name = $properties['first_name'];
			$this->last_name = $properties['last_name'];
			$this->status = $properties['status'];
			$this->about_me = $properties['about_me'];
			$this->sex = $properties['sex'];
			$this->addresses = $properties['addresses'];
			$this->interests = $properties['interests'];            
		}
		
		public function get_id()
		{
			return $this->id;
		}
		
		public function get_username()
		{
			return $this->username;
		}
		
		public function get_email()
		{
			return $this->email;
		}
		
		// You can pass optional date format string to this method, defaults to "26th February, 1983"
		public function get_member_since($format = "dS F, Y")
		{
			$oDate = new DateTime($this->created);
			$sDate = $oDate->format($format);
			return $sDate;
		}
		
		public function get_last_login()
		{
			return $this->last_login;
		}
		
		public function get_name()
		{
			return $this->first_name . ' ' . $this->last_name;
		}

		public function get_addresses ()
		{
			if (sizeof($this->addresses) > 0) {
				$address_string = '';
				foreach($this->addresses as $address)
					$address_string .= $address->get_vcard();
				return $address_string;
			}
			return false;
		}
	}
?>