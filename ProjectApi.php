<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// Load the Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';

class ProjectApi extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Load the user model
		$this->load->model('Web_model');
	}

	public function login_post()
	{
		// Get the post data
		$email = $this->post('email');
		$password = $this->post('password');

		// Validate the post data
		if (!empty($email) && !empty($password)) {

			// Check if any user exists with the given credentials
			$con['returnType'] = 'single';
			$con['conditions'] = array(
				'email' => $email,
				'password' => $password
			);
			$user = $this->Web_model->getRows($con);

			if ($user) {
				// Set the response and exit
				$this->response([
					'status' => "TRUE",
					'message' => 'User login successful.',
					'data' => $user
				], REST_Controller::HTTP_OK);
			} else {
				// Set the response and exit
				//BAD_REQUEST (400) being the HTTP response code
				$this->response([
					'status' => "FALSE",
					'message' => 'Wrong email or password.'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			// Set the response and exit
			$this->response([
				'status' => "FALSE",
				'message' => 'Provide email and password.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function registration_post()
	{
		// Get the post data
		$name = strip_tags($this->post('name'));
		$gender = strip_tags($this->post('gender'));
		$email = strip_tags($this->post('email'));
		$phone = strip_tags($this->post('phone'));


		$usernumrow = $this->db->get('registration')->num_rows();
		$user_num_row = $usernumrow + 1;
		$user_id = "USER00" . $user_num_row;

		// Validate the post data
		if (!empty($email)) {

			// Check if the given user_name already exists
			$con['returnType'] = 'count';
			$con['conditions'] = array(
				'email' => $email,
			);

			$userCount = $this->Web_model->getRows($con);

			if ($userCount > 0) {
				// Set the response and exit
				$this->response([
					'Status' => "FALSE",
					'Message' => 'The given email already exists.'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				// Insert user data
				$userData = array(
					'user_id' => $user_id,
					'name' => $name,
					'phone' => $phone,
					'gender' => $gender,
					'email' => $email,
				);
				$insert = $this->db->insert('registration', $userData);

				// Check if the user data is inserted
				if ($insert) {
					// Set the response and exit
					$this->response([
						'Status' => "TRUE",
						'Message' => 'The user has been added successfully.',
						'Data' => $userData
					], REST_Controller::HTTP_OK);
				} else {
					// Set the response and exit
					$this->response([
						'Status' => "FALSE",
						'Message' => 'Some problems occurred, please try again.'
					], REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} else {
			// Set the response and exit
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Provide complete user info to add.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function add_flat_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['flat'] = $this->post('flat');
		$field['city'] = $this->post('city');
		$field['society'] = $this->post('society');
		$field['building'] = $this->post('building');
		$field['identity'] = $this->post('identity');
		$field['occupancy'] = $this->post('occupancy');

		date_default_timezone_set('Asia/Kolkata');
		$field['created_at'] = date('Y-m-d H:i');

		$query = $this->db->get_where('registration', array('user_id' => $field['user_id']));
		$row = $query->num_rows();

		if ($row > 0) {
			$this->db->where('user_id', $field['user_id']);
			$this->db->update('registration', $field);
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Flat, Villa Has been Added',
				'Data' => $field
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'No Data Found'
			], REST_Controller::HTTP_OK);
		}
	}

	public function city_get()
	{
		// Returns all the users data if the id not specified,
		// Otherwise, a single user will be returned.
		//$con = $id?array('id' => $id):'';
		$data = $this->db->get('city')->result_array();

		// Check if the user data exists

		//echo "<pre>"; print_r($data); die;
		$i = '1';
		$add = array();
		if (!empty($data)) {
			$add[0]['city'] = 'Select City';
			foreach ($data as $value => $val) {
				$address = $val['city'];
				$add[$i]['city'] = $address;
				$i++;
			}
			//print_r($add); 
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
			//  }
			// Set the response and exit
			//OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function building_post()
	{

		$society = strip_tags($this->post('society'));
		$this->db->where('society', $society);
		$data = $this->db->get('building')->result_array();


		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$address = $val['building'];
				$add[$i]['building'] = $address;
				$i++;
			}
			//print_r($add); 
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
			//  }
			// Set the response and exit
			//OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function flat_post()
	{
		$building = strip_tags($this->post('building'));
		$this->db->where('building', $building);
		$data = $this->db->get('flat')->result_array();
		$i = '1';
		$add = array();
		if (!empty($data)) {
			$add[0]['flat'] = 'Select Flat';
			foreach ($data as $value => $val) {
				$address = $val['flat'];
				$status = $val['status'];

				if ($status = 1) {
					$add[$i]['status'] = 'Approved';
				} else {
					$add[$i]['status'] = 'Pending';
				}

				$add[$i]['flat'] = $address;
				$i++;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function search_society_post()
	{
		$city = $this->post('city');
		$society = $this->post('society');

		if (empty($city)) {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Fields are empty'
			], REST_Controller::HTTP_OK);
		} else {
			$this->db->where('city', $city);
			$this->db->like('society', $society);
			$data = $this->db->get('society')->result_array();
			$i = '0';
			$add = array();
			if (!empty($data)) {
				foreach ($data as $value => $val) {
					$address = $val['society'];
					$add[$i]['society'] = $address;
					$i++;
				}
				if (!empty($data)) {
					$this->response([
						'Status' => "TRUE",
						'Message' => 'Data Availaible.',
						'Data' =>  $add
					], REST_Controller::HTTP_OK);
				} else {
					$this->response([
						'Status' => "FALSE",
						'Message' => 'Nothing was found.',
						'Data' => $data
					], REST_Controller::HTTP_NOT_FOUND);
				}
			}
		}
	}



	public function add_demo_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['city'] = $this->post('city');
		$field['society'] = $this->post('society');



		if (!empty($field)) {
			$insert = $this->db->insert('demo', $field);
			if ($insert > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Demo Has been Added',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Empty Fields Passed'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function categoryid_get()
	{
		$data = $this->db->get('category')->result_array();
		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$address = $val['category'];
				$add[$i]['category_id'] = $address;
				$i++;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}


	function addFamilyMember_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['category_id'] = $this->post('category');
		$field['name'] = $this->post('name');
		$field['phone'] = $this->post('phone');
		$field['gender'] = $this->post('gender');
		$field['valid_from'] = $this->post('valid_from');
		$field['valid_to'] = $this->post('valid_to');

		date_default_timezone_set('Asia/Kolkata');
		$field['date'] = date('d-m-Y h:ia');

		$usernumrow = $this->db->get('member')->num_rows();
		$user_num_row = $usernumrow + 1;
		$member_id = "MBR00" . $user_num_row;

		$field['member_id'] = $member_id;

		$base64       = $this->input->post('member_pic');

		if (!empty($field)) {
			$ImageName = "Family_pic_" . time();
			$PROFILE_DIRECTORY = './uploads/family/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array(
					'user_id' => $field['user_id'],
					'category_id' => $field['category_id'],
					'member_id' => $field['member_id'],
					'name' => $field['name'],
					'phone' => $field['phone'],
					'gender' => $field['gender'],
					'valid_from' => $field['valid_from'],
					'valid_to' => $field['valid_to'],
					'date' => $field['date'],
					'image' => $imageName
				);
				$this->db->insert('member', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Family Member successfully",
					"Data" => $data
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "Family Member successfully",
						"Data" => $data
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function oldaddFamilyMember_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['category_id'] = $this->post('category');
		$field['name'] = $this->post('name');
		$field['phone'] = $this->post('phone');
		$field['gender'] = $this->post('gender');
		$field['valid_from'] = $this->post('valid_from');
		$field['valid_to'] = $this->post('valid_to');
		$base64 = $this->input->post('member_pic');


		$ImageName = "Family_pic_" . time();
		$PROFILE_DIRECTORY = './uploads/family/';
		$img = @imagecreatefromstring(base64_decode($base64));

		if ($img != false) {
			$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
			$path = $PROFILE_DIRECTORY . $imageName;
			$field['image'] = $imageName;
		} else {
			$field['image'] = "";
		}


		$usernumrow = $this->db->get('member')->num_rows();
		$user_num_row = $usernumrow + 1;
		$member_id = "MBR00" . $user_num_row;

		$field['member_id'] = $member_id;






		if ($field['user_id'] == "") {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Fields are empty',
				'Data' => $field
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {



			$insert = $this->db->insert('member', $field);

			if ($insert) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Family Member Has been Added',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Problems Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		}
	}



	function user_otp_post()
	{
		$mobile = strip_tags($this->post('phone'));
		$query = $this->db->get_where('registration', array('phone' => $this->post('phone')));
		$row = $query->num_rows();


		if ($row > 0) {
			$data = array();
			$data1 = array();
			$ran = mt_rand('1000', '3000');
			$otp = "$ran";
			$data['status'] = "TRUE";
			$data['OTP'] = $otp;

			$query = $this->db->get_where('registration', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Already exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $data], REST_Controller::HTTP_OK);

			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		} else {
			$query = $this->db->get_where('registration', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$val = $data;
			$ran = mt_rand('1000', '3000');
			$otp = "$ran";
			$data['status'] = "TRUE";
			$data['OTP'] = $otp;
			$this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $val], REST_Controller::HTTP_OK);

			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		}
	}

	function userphone_login_post()
	{
		$mobile = $this->post('phone');
		$query = $this->db->get_where('registration', array('phone' => $this->post('phone')));
		$row = $query->num_rows();
		if ($row > 0) {
			$data = array();
			$data1 = array();
			$query = $this->db->get_where('registration', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Exists', 'mobile' => $mobile, 'Data' => $data], REST_Controller::HTTP_OK);
		} else {
			$query = $this->db->get_where('registration', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$val = $data;
			$data['status'] = "TRUE";
			$this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'mobile' => $mobile, 'Data' => $val], REST_Controller::HTTP_OK);
		}
	}

	public function add_description_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['name'] = $this->post('name');
		$field['email'] = $this->post('email');
		$field['description'] = $this->post('description');

		date_default_timezone_set('Asia/Kolkata');
		$field['created_at'] = date('Y-m-d H:i');

		if (!empty($field)) {
			$this->db->insert('description', $field);
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Description Has been Added',
				'Data' => $field
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Some Problems Occured! Please Try Again'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function add_feedback_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['feedback'] = $this->post('feedback');

		date_default_timezone_set('Asia/Kolkata');
		$field['created_at'] = date('d-m-Y h:ia');

		if (!empty($field)) {
			$this->db->insert('feedback', $field);
			$this->response([
				'Status' => "TRUE",
				'Message' => 'feedback Has been Added',
				'Data' => $field
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Some Problems Occured! Please Try Again'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function addedFamilyMemberList_post()
	{
		$user_id = strip_tags($this->post('user_id'));
		$this->db->where('user_id', $user_id);
		$data = $this->db->get('member')->result_array();
		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$stats[$i]['status'] = $val['status'];
				if ($stats[$i]['status'] == "") {
					$add[$i]['member_id'] = $val['member_id'];
					$name = $val['name'];
					$phone = $val['phone'];
					$gender = $val['gender'];
					$category = $val['category_id'];
					$image = $val['image'];
					$add[$i]['name'] = $name;
					$add[$i]['phone'] = $phone;
					$add[$i]['gender'] = $gender;
					$add[$i]['category'] = $category;
					$add[$i]['image'] = $image;
					$add[$i]['valid_from'] = $val['valid_from'];
					$add[$i]['valid_to'] = $val['valid_to'];
					$i++;

					$this->response([
						'Status' => "TRUE",
						'Message' => 'Data Availaible.',
						'Data' => $add
					], REST_Controller::HTTP_OK);
				} else {
					$this->response([
						'Status' => "FALSE",
						'Message' => 'None was found.',
						'Data' => $add
					], REST_Controller::HTTP_NOT_FOUND);
				}
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function visitor_post()
	{

		$data = $this->db->get('category')->result_array();
		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$category = $val['category'];
				$add[$i]['category'] = $category;
				$i++;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function visitor_list_post()
	{

		$user_id = strip_tags($this->post('user_id'));

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('visitor')->result_array();

		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$phone = $val['phone'];
				$date = $val['date'];
				$category = $val['category'];
				$image = $val['image'];
				$add[$i]['name'] = $val['name'];
				$add[$i]['phone'] = $phone;
				$add[$i]['date'] = $date;
				$add[$i]['gender'] = $val['gender'];

				$add[$i]['image'] = $image;

				$this->db->where('category_id', $category);
				$data = $this->db->get('category')->row_array();

				$add[$i]['category'] = $data['category'];
				$i++;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}


	public function categoryID_list_post()
	{

		$category = strip_tags($this->post('category'));
		$this->db->where('category', $category);
		$data = $this->db->get('visitor')->result_array();

		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$name = $val['name'];
				$date = $val['date'];
				$category = $val['category'];
				$phone = $val['phone'];
				$add[$i]['name'] = $name;
				$add[$i]['date'] = $date;
				$add[$i]['category'] = $category;
				$add[$i]['phone'] = $phone;
				$add[$i]['image'] = $val['image'];

				$add[$i]['gender'] = $val['gender'];
				$i++;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function flatList_post()
	{
		$user_id = $this->post('user_id');

		$this->db->where('user_id', $user_id);

		$data = $this->db->get('registration')->result_array();

		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$address = $val['flat'];
				$add[$i]['flat'] = $address;
				$add[$i]['society'] = $val['society'];
				$add[$i]['addDate'] = $val['flatDate'];
				$i++;
			}
			//print_r($add); 
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
			//  }
			// Set the response and exit
			//OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function registerGuard_post()
	{
		// Get the post data
		$name = strip_tags($this->post('name'));
		$gender = strip_tags($this->post('gender'));
		$phone = strip_tags($this->post('phone'));
		$address = strip_tags($this->post('address'));
		$city = strip_tags($this->post('city'));
		$society_id = strip_tags($this->post('society_id'));


		$usernumrow = $this->db->get('guard')->num_rows();
		$user_num_row = $usernumrow + 1;
		$user_id = "GRD00" . $user_num_row;

		// Validate the post data
		if (!empty($name) && !empty($phone)) {

			// Insert user data
			$userData = array(
				'guard_id' => $user_id,
				'name' => $name,
				'gender' => $gender,
				'phone' => $phone,
				'address' => $address,
				'city' => $city,
				'society_id' => $society_id,
			);
			$insert = $this->db->insert('guard', $userData);

			// Check if the user data is inserted
			if ($insert) {
				// Set the response and exit
				$this->response([
					'Status' => "TRUE",
					'Message' => 'The Guard has been added successfully.',
					'Data' => $userData
				], REST_Controller::HTTP_OK);
			} else {
				// Set the response and exit
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some problems occurred, please try again.'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			// Set the response and exit
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Provide complete user info to add.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	function deleteFlat_post()
	{
		$user_id['user_id'] = $this->post('user_id');
		$userData['flat'] = NULL;
		if (!empty($userData)) {
			$this->db->where('user_id', $this->post('user_id'));
			$query = $this->db->get('registration');
			$data = $query->num_rows();
			if ($data) {
				$this->db->where('user_id', $this->post('user_id'));
				$delete = $this->db->update('registration', $userData);
				$this->response(['Status' => "TRUE", 'Message' => 'Deleted successfully.', 'Data' => $user_id], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => "FALSE", 'Message' => "Data Not found."], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(['Status' => "FALSE", 'message' => "Provide complete information."], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function visitorOnGate_get()
	{
		$data = $this->db->get('visitor')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['name'] = $val['name'];
				$visit['phone'] = $val['phone'];
				$visit['reason'] = $val['reason'];
				$visit['gender'] = $val['gender'];
				$visit['image'] = $val['image'];
				$visit['category'] = $val['category'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function accept_post()
	{
		$this->response([
			'Status' => "TRUE",
			'Message' => 'Accepted Message',
			'Data' => 'Accepted'
		], REST_Controller::HTTP_OK);
	}

	public function reject_post()
	{
		$this->response([
			'Status' => "TRUE",
			'Message' => 'Rejected Message',
			'Data' => 'Rejected'
		], REST_Controller::HTTP_OK);
	}

	public function addNewVisitor_post()
	{
		// Get the post data
		$building = strip_tags($this->post('building'));
		$flat = strip_tags($this->post('flat'));
		$category = strip_tags($this->post('category'));
		$name = strip_tags($this->post('name'));
		$phone = strip_tags($this->post('phone'));
		$add_date = strip_tags($this->post('add_date'));

		$visitornumrow = $this->db->get('visitor')->num_rows();
		$visitor_num_row = $visitornumrow + 1;
		$visitor_id = "WRK00" . $visitor_num_row;


		// Validate the post data
		if (!empty($name) && !empty($phone)) {

			// Insert user data
			$userData = array(
				'worker_id' => $building,
				'building' => $building,
				'flat' => $flat,
				'category' => $category,
				'name' => $name,
				'phone' => $phone,
				'add_date' => $add_date,
			);
			$insert = $this->db->insert('visitor', $userData);

			// Check if the user data is inserted
			if ($insert) {
				// Set the response and exit
				$this->response([
					'Status' => "TRUE",
					'Message' => 'The Visitor has been added successfully.',
					'Data' => $userData
				], REST_Controller::HTTP_OK);
			} else {
				// Set the response and exit
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some problems occurred, please try again.'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			// Set the response and exit
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Provide complete visitor info to add.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function guestOnGate_get()
	{
		$data = $this->db->get('guest')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['name'] = $val['name'];
				$visit['phone'] = $val['phone'];
				$visit['reason'] = $val['reason'];
				$visit['gender'] = $val['gender'];
				$visit['image'] = $val['image'];
				$visit['category'] = $val['category'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function dailyWorker_get()
	{
		$data = $this->db->get('worker')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['name'] = $val['name'];
				$visit['phone'] = $val['phone'];
				$visit['image'] = $val['image'];
				$visit['gender'] = $val['gender'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function securityGuard_get()
	{
		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['name'] = $val['name'];
				$visit['date'] = $val['date'];
				$visit['image'] = $val['image'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function guardLogin_otp_post()
	{
		$mobile = strip_tags($this->post('phone'));
		$query = $this->db->get_where('guard', array('phone' => $this->post('phone')));
		$row = $query->num_rows();


		if ($row > 0) {
			$data = array();
			$data1 = array();
			$ran = mt_rand('1000', '3000');
			$otp = "$ran";
			$data['status'] = "TRUE";
			$data['OTP'] = $otp;

			$query = $this->db->get_where('guard', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$this->response(['Status' => "TRUE", 'Message' => 'Mobile Number Exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Name' => $data['name'], 'Guard_id' => $data['guard_id'], 'Gender' => $data['gender'], 'Address' => $data['address'], 'Data' => $data], REST_Controller::HTTP_OK);

			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		} else {
			$this->response(['Status' => "FALSE", 'Message' => 'Mobile Not Registered! Please Signup First.', 'Mobile' => $mobile], REST_Controller::HTTP_OK);
		}
	}

	public function allbuilding_get()
	{

		$data = $this->db->get('building')->result_array();


		$i = '1';

		$add = array();
		if (!empty($data)) {

			foreach ($data as $value => $val) {
				$address = $val['building'];
				$add[0]['building'] = 'Select Building';
				$add[$i]['building'] = $address;
				$i++;
			}
			//print_r($add); 
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
			//  }
			// Set the response and exit
			//OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function allcategory_get()
	{

		$data = $this->db->get('category')->result_array();


		$i = '1';
		$add = array();
		if (!empty($data)) {
			$add[0]['category'] = 'Select Category';
			foreach ($data as $value => $val) {
				$address = $val['category'];
				$add[$i]['category'] = $address;
				$i++;
			}
			//print_r($add); 
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
			//  }
			// Set the response and exit
			//OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}


	function guardphone_login_post()
	{
		$mobile = $this->post('phone');
		$query = $this->db->get_where('guard', array('phone' => $this->post('phone')));
		$row = $query->num_rows();
		if ($row > 0) {
			$data = array();
			$data1 = array();
			$query = $this->db->get_where('guard', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Exists', 'mobile' => $mobile, 'Data' => $data], REST_Controller::HTTP_OK);
		} else {
			$query = $this->db->get_where('guard', array('phone' => $this->post('phone')));
			$data = $query->row_array();
			$val = $data;
			$data['status'] = "TRUE";
			$this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'mobile' => $mobile, 'Data' => $val], REST_Controller::HTTP_OK);
		}
	}

	public function allvisitor_get()
	{
		$this->db->order_by("id", "desc");
		$data = $this->db->get('visitor')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				//$visit['id'] = $val['id'];
				$visit['visitor_id'] = $val['worker_id'];
				$visit['name'] = $val['name'];
				$visit['date'] = $val['date'];
				$visit['category'] = $val['category'];
				$visit['flat'] = $val['flat'];
				$visit['status'] = $val['status'];
				$visit['image'] = $val['image'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function permitDeliveryBoy_get()
	{
		$this->db->order_by("id", "desc");

		$this->db->where('status', "PERMITTED");
		$this->db->where('category', "CTG005");
		$data = $this->db->get('visitor')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['permission'] = "Allowed";
				$visit['name'] = $val['name'];
				$visit['date'] = $val['date'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function getRegisterPhone_get()
	{
		$this->db->order_by("id", "desc");

		$data = $this->db->get('registration')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['phone'] = $val['phone'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function getAddedDemo_post()
	{

		$user_id = $this->post('user_id');

		$this->db->order_by("id", "desc");

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('demo')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['user_id'] = $val['user_id'];
				$visit['city'] = $val['city'];
				$visit['society'] = $val['society'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $visit
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function upload_userpic_post()
	{
		$user_id   = $this->input->post('user_id');
		$base64       = $this->input->post('user_pic');

		$data = $this->db->get_where('registration', array('user_id' => $user_id))->row_array();
		if (!empty($data)) {
			$ImageName = "User_pic_" . time();
			$PROFILE_DIRECTORY = './uploads/user/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('user_pic' => $imageName);
				$this->db->where('user_id', $user_id);
				$this->db->update('registration', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "ID Proof Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "ID Proof Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function userProfilePic_post()
	{

		$user_id = $this->post('user_id');

		$this->db->order_by("id", "desc");

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('registration')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['user_id'] = $val['user_id'];
				$visit['user_pic'] = $val['user_pic'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function upload_guardpic_post()
	{
		$user_id   = $this->input->post('guard_id');
		$base64       = $this->input->post('guard_pic');

		$data = $this->db->get_where('guard', array('guard_id' => $guard_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Guard_pic_" . time();
			$PROFILE_DIRECTORY = './uploads/guard/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('image' => $imageName);
				$this->db->where('guard_id', $user_id);
				$this->db->update('registration', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "ID Proof Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "ID Proof Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function upload_familymember_post()
	{
		$member_id   = $this->input->post('member_id');
		$base64       = $this->input->post('member_pic');

		$data = $this->db->get_where('member', array('member_id' => $member_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Family_pic_" . time();
			$PROFILE_DIRECTORY = './uploads/family/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('image' => $imageName);
				$this->db->where('member_id', $member_id);
				$this->db->update('member', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "ID Proof Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "ID Proof Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function feedback_post()
	{

		$user_id = $this->post('user_id');

		$this->db->order_by("id", "desc");

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('feedback')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['feedback'] = $val['feedback'];
				$visit['created_at'] = $val['created_at'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function description_post()
	{

		$user_id = $this->post('user_id');

		$this->db->order_by("id", "desc");

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('description')->result_array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$visit['description'] = $val['description'];
				$visit['created_at'] = $val['created_at'];
				$visitor[$value] = $visit;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $visitor
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function userImage_post()
	{

		$user_id = $this->post('user_id');

		$this->db->order_by("id", "desc");

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('registration')->row_array();
		if (!empty($data)) {

			$user_pic['user_pic'] = $data['user_pic'];

			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $user_pic
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function addValidFlat_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['flat'] = $this->post('flat');

		$queryFlat = $this->db->get_where('flat', array('flat' => $field['flat']))->row_array();

		if ($queryFlat['status'] == 2 || empty($queryFlat)) {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Flat is not valid',
				'Data' => $field
			], REST_Controller::HTTP_OK);
		} else {
			$query = $this->db->get_where('registration', array('user_id' => $field['user_id']));
			$row = $query->num_rows();

			if ($row > 0) {
				$this->db->where('user_id', $field['user_id']);
				$this->db->update('registration', $field);
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Flat Has been Added',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'No Data Found'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		}
	}

	public function approvedFamilyMemberList_post()
	{
		$user_id = strip_tags($this->post('user_id'));
		$this->db->where('user_id', $user_id);
		$this->db->where('status', "PERMITTED");
		$data = $this->db->get('member')->result_array();
		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$name = $val['name'];
				$phone = $val['phone'];
				$gender = $val['gender'];
				$category = $val['category_id'];
				$image = $val['image'];
				$add[$i]['member_id'] = $val['member_id'];
				$add[$i]['name'] = $name;
				$add[$i]['phone'] = $phone;
				$add[$i]['gender'] = $gender;
				$add[$i]['category'] = $category;
				$add[$i]['image'] = $image;
				$add[$i]['date'] = $val['date'];
				$i++;

				$this->response([
					'Status' => "TRUE",
					'Message' => 'Data Availaible.',
					'Data' => $add
				], REST_Controller::HTTP_OK);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function rejectedFamilyMemberList_post()
	{
		$user_id = strip_tags($this->post('user_id'));
		$this->db->where('user_id', $user_id);
		$this->db->where('status', "NOT PERMITTED");
		$data = $this->db->get('member')->result_array();
		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$add[$i]['member_id'] = $val['member_id'];
				$name = $val['name'];
				$phone = $val['phone'];
				$gender = $val['gender'];
				$category = $val['category_id'];
				$image = $val['image'];
				$add[$i]['name'] = $name;
				$add[$i]['phone'] = $phone;
				$add[$i]['gender'] = $gender;
				$add[$i]['category'] = $category;
				$add[$i]['image'] = $image;
				$add[$i]['date'] = $val['date'];
				$i++;

				$this->response([
					'Status' => "TRUE",
					'Message' => 'Data Availaible.',
					'Data' => $add
				], REST_Controller::HTTP_OK);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function updateFamilyMember_post()
	{
		$member_id   = $this->input->post('member_id');
		$field['category_id'] = $this->post('category');
		$field['name'] = $this->post('name');
		$field['phone'] = $this->post('phone');
		$field['gender'] = $this->post('gender');
		$field['valid_from'] = $this->post('valid_from');
		$field['valid_to'] = $this->post('valid_to');

		date_default_timezone_set('Asia/Kolkata');
		$field['date'] = date('d-m-Y h:ia');


		$dataQ = $this->db->get_where('member', array('member_id' => $member_id))->row_array();

		$base64       = $this->input->post('member_pic');

		if (!empty($dataQ)) {
			$ImageName = "Family_pic_" . time();
			$PROFILE_DIRECTORY = './uploads/family/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array(
					'category_id' => $field['category_id'],
					'name' => $field['name'],
					'phone' => $field['phone'],
					'gender' => $field['gender'],
					'valid_from' => $field['valid_from'],
					'valid_to' => $field['valid_to'],
					'date' => $field['date'],
					'image' => $imageName
				);
				$this->db->where('member_id', $member_id);
				$this->db->update('member', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Family Member successfully updated",
					"Data" => $data
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "Family Member successfully updated",
						"Data" => $data
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data UPDATION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function oldupdateFamilyMember_post()
	{
		$field['member_id'] = $this->post('member_id');
		$field['name'] = $this->post('name');
		$field['phone'] = $this->post('phone');
		$field['gender'] = $this->post('gender');
		$field['category_id'] = $this->post('category');
		$field['valid_from'] = $this->post('valid_from');
		$field['valid_to'] = $this->post('valid_to');
		$base64 = $this->input->post('member_pic');

		$query = $this->db->get_where('member', array('member_id' => $field['member_id']));
		$row = $query->num_rows();

		$ImageName = "Family_pic_" . time();
		$PROFILE_DIRECTORY = './uploads/family/';
		$img = @imagecreatefromstring(base64_decode($base64));

		$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
		$path = $PROFILE_DIRECTORY . $imageName;
		$field['image'] = $imageName;

		if ($row > 0) {
			$this->db->where('member_id', $field['member_id']);
			$this->db->update('member', $field);
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Member Has been Updated',
				'Data' => $field
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'No Data Found'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	function deleteFamilyMember_post()
	{
		$field['member_id'] = $this->post('member_id');
		if (!empty($field)) {
			$this->db->where('member_id', $this->post('member_id'));
			$query = $this->db->get('member');
			$data = $query->num_rows();
			if ($data) {
				$this->db->where('member_id', $this->post('member_id'));
				$delete = $this->db->delete('member');
				$this->response(['Status' => "TRUE", 'Message' => 'Deleted successfully.', 'Data' => $field['member_id']], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => "FALSE", 'Message' => "Data Not found."], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(['Status' => "FALSE", 'message' => "Provide complete information."], REST_Controller::HTTP_BAD_REQUEST);
		}
	}


	function uploadTestImage_post()
	{
		$base64       = $this->input->post('image');

		if (TRUE) {
			$ImageName = "Test_Image_" . time();
			$PROFILE_DIRECTORY = './uploads/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('image' => $imageName);
				$this->db->insert('testImage', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function updateUserProfile_post()
	{
		$user_id = $this->post('user_id');
		$field['name'] = $this->post('name');
		$field['phone'] = $this->post('mobile');
		$field['email'] = $this->post('email');
		$field['gender'] = $this->post('gender');
		$base64 = $this->input->post('user_pic');


		$dataQ = $this->db->get_where('registration', array('user_id' => $user_id))->row_array();



		if (!empty($dataQ)) {
			$ImageName = "User_pic_" . time();
			$PROFILE_DIRECTORY = './uploads/user/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array(
					'name' => $field['name'],
					'phone' => $field['phone'],
					'gender' => $field['gender'],
					'email' => $field['email'],
					'image' => $imageName
				);
				$this->db->where('user_id', $user_id);
				$this->db->update('registration', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "User successfully updated",
					"Data" => $data
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "User successfully updated",
						"Data" => $data
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data UPDATION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function addSecurityVisitor_post()
	{
		// Get the post data
		$building = strip_tags($this->post('building'));
		$flat = strip_tags($this->post('flat'));
		$category = strip_tags($this->post('category'));
		$gender = strip_tags($this->post('gender'));
		$name = strip_tags($this->post('name'));
		$phone = strip_tags($this->post('phone'));

		$visitornumrow = $this->db->get('visitor')->num_rows();
		$visitor_num_row = $visitornumrow + 1;
		$visitor_id = "WRK00" . $visitor_num_row;



		// Validate the post data
		if (!empty($name) && !empty($phone)) {

			// Insert user data
			$userData = array(
				'worker_id' => $visitor_id,
				'building' => $building,
				'flat' => $flat,
				'category' => $category,
				'gender' => $gender,
				'name' => $name,
				'phone' => $phone,
			);
			$insert = $this->db->insert('visitor', $userData);

			// Check if the user data is inserted
			if ($insert) {
				// Set the response and exit
				$this->response([
					'Status' => "TRUE",
					'Message' => 'The Visitor has been added successfully.',
					'Data' => $userData
				], REST_Controller::HTTP_OK);
			} else {
				// Set the response and exit
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some problems occurred, please try again.'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			// Set the response and exit
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Provide complete visitor info to add.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function getSocietyByCity_post()
	{

		$city = strip_tags($this->post('city'));
		$this->db->where('city', $city);
		$data = $this->db->get('society')->result_array();


		$i = '0';
		$soc = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$socie = $val['society'];
				$soc[$i]['society'] = $socie;
				$soc[$i]['society_id'] = $val['society_id'];
				$i++;
			}
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $soc
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function guardLogin_post()
	{
		// Get the post data
		$mobile = $this->post('mobile');
		$password = $this->post('password');

		// Validate the post data
		if (!empty($mobile) && !empty($password)) {

			$data = $this->db->get_where('guard', array('phone' => $mobile, 'password' => $password))->row_array();

			if (!empty($data)) {

				$stats = $data['status'];

				if ($stats == 'PERMITTED') {
					$this->response([
						'status' => "TRUE",
						'message' => 'Guard Approved! Login successful.',
						'data' => [$data]
					], REST_Controller::HTTP_OK);
				} else if ($stats == 'NOT PERMITTED') {
					$this->response([
						'status' => "FALSE",
						'message' => 'Guard Not Approved! Login Unsuccessful.',
						'data' => [$data]
					], REST_Controller::HTTP_OK);
				} else {
					$this->response([
						'status' => "FALSE",
						'message' => 'Guard Status Pending! Login Unsuccessful.',
						'data' => [$data]
					], REST_Controller::HTTP_OK);
				}
			} else {
				$this->response([
					'status' => "FALSE",
					'message' => 'Wrong Mobile or password.'
				], REST_Controller::HTTP_OK);
			}
		} else {
			// Set the response and exit
			$this->response([
				'status' => "FALSE",
				'message' => 'Provide email and password.'
			], REST_Controller::HTTP_OK);
		}
	}

	public function totalGuard_get()
	{

		$data = $this->db->count_all_results('guard');
		if ($data > 0) {

			$this->db->where('status', 'PERMITTED');
			$grd['approved'] = $this->db->count_all_results('guard');

			$this->db->where('status', 'PERMITTED');
			$grd['rejected'] = $this->db->count_all_results('guard');

			$this->db->where('status', '');
			$grd['pending'] = $this->db->count_all_results('guard');

			$this->response([
				'status' => "TRUE",
				'message' => 'Data Available.',
				'data' => [$grd]
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => "FALSE",
				'message' => 'No Data Found.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function approvedGuardList_get()
	{

		$this->db->where('status', "PERMITTED");

		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_OK);
		}
	}

	public function approvedFilterGuardList_post()
	{
		$valid_from = $this->post('valid_from');
		$valid_to = $this->post('valid_to');

		$this->db->where('status', "PERMITTED");
		$this->db->where('valid_from BETWEEN "' . date('d-m-Y', strtotime($valid_from)) . '" and "' . date('d-m-Y', strtotime($valid_to)) . '"')->where('valid_to BETWEEN "' . date('d-m-Y', strtotime($valid_from)) . '" and "' . date('d-m-Y', strtotime($valid_to)) . '"');

		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_OK);
		}
	}

	public function rejectedGuardList_get()
	{
		$this->db->where('status', "NOT PERMITTED");
		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_OK);
		}
	}

	public function rejectedFilterGuardList_post()
	{
		$valid_from = $this->post('valid_from');
		$valid_to = $this->post('valid_to');
		$valid_from = $this->post('valid_from');
		$valid_to = $this->post('valid_to');
		$this->db->where('status', "NOT PERMITTED");
		$this->db->where('valid_from BETWEEN "' . date('d-m-Y', strtotime($valid_from)) . '" and "' . date('d-m-Y', strtotime($valid_to)) . '"')->where('valid_to BETWEEN "' . date('d-m-Y', strtotime($valid_from)) . '" and "' . date('d-m-Y', strtotime($valid_to)) . '"');
		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_OK);
		}
	}

	public function pendingFilterGuardList_post()
	{
		$valid_from = $this->post('valid_from');
		$valid_to = $this->post('valid_to');
		$this->db->where('status', "");
		$this->db->where('valid_from BETWEEN "' . date('d-m-Y', strtotime($valid_from)) . '" and "' . date('d-m-Y', strtotime($valid_to)) . '"')->where('valid_to BETWEEN "' . date('d-m-Y', strtotime($valid_from)) . '" and "' . date('d-m-Y', strtotime($valid_to)) . '"');
		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
			], REST_Controller::HTTP_OK);
		}
	}

	public function pendingGuardList_get()
	{
		$this->db->where('status', "");
		$data = $this->db->get('guard')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function userFeedbackList_post()
	{
		$user_id = $this->post('user_id');
		$this->db->where('user_id', $user_id);
		$this->db->order_by("id", "desc");
		$data = $this->db->get('feedback')->result_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function guardLoginDetails_post()
	{
		$guard_id = $this->post('guard_id');
		$this->db->where('guard_id', $guard_id);
		$data = $this->db->get('guard')->row_array();
		if (!empty($data)) {
			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => [$data]
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function guardSearchUser_post()
	{
		$search_user = $this->post('search');

		if (empty($search_user)) {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Fields are empty'
			], REST_Controller::HTTP_OK);
		} else {
			$this->db->where('user_id', $search_user)->or_where('name', $search_user);
			$data = $this->db->get('registration')->result_array();
			$i = '0';
			$add = array();
			if (!empty($data)) {
				foreach ($data as $value => $val) {
					$add[$i]['user_id'] = $val['user_id'];
					$add[$i]['name'] = $val['name'];
					$add[$i]['email'] = $val['email'];
					$add[$i]['phone'] = $val['phone'];
					$add[$i]['gender'] = $val['gender'];
					$add[$i]['address'] = $val['address'];
					$add[$i]['flat'] = $val['flat'];
					$add[$i]['city'] = $val['city'];
					$add[$i]['society'] = $val['society'];
					$add[$i]['building'] = $val['building'];
					$add[$i]['status'] = $val['status'];
					$add[$i]['user_pic'] = $val['user_pic'];
					$add[$i]['valid_from'] = $val['valid_from'];
					$add[$i]['valid_to'] = $val['valid_to'];

					$i++;
				}
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Data Availaible.',
					'Data' =>  $add
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'No Data Found.',
					'Data' => $data
				], REST_Controller::HTTP_OK);
			}
		}
	}

	public function userBuildFlatSociety_post()
	{
		$user_id = $this->post('user_id');
		$this->db->where('user_id', $user_id);
		$data = $this->db->get('registration')->row_array();
		if (!empty($data)) {


			$get['user_id'] = $data['user_id'];
			$get['society'] = $data['society'];
			$get['building'] = $data['building'];
			$get['flat'] = $data['flat'];


			$this->response([
				'Status' => "TRUE",
				'Message' => 'Data Availaible.',
				'Data' => [$get]
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'None was found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function deleteUserBSFlat_post()
	{
		$user_id['user_id'] = $this->post('user_id');
		$userData['society'] = NULL;
		$userData['building'] = NULL;
		$userData['flat'] = NULL;
		if (!empty($userData)) {
			$this->db->where('user_id', $this->post('user_id'));
			$query = $this->db->get('registration');
			$data = $query->num_rows();
			if ($data) {
				$this->db->where('user_id', $this->post('user_id'));
				$delete = $this->db->update('registration', $userData);
				$this->response(['Status' => "TRUE", 'Message' => 'Deleted successfully.', 'Data' => $user_id], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => "FALSE", 'Message' => "Data Not found."], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(['Status' => "FALSE", 'message' => "Provide complete information."], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function login_post()
	{
		// Get the post data
		$user_id = $this->post('user_id');
		$username = $this->post('user_name');
		$password = $this->post('password');

		// Validate the post data
		if (!empty($username) && !empty($password) && !empty($user_id)) {

			// Check if any user exists with the given credentials
			$con['returnType'] = 'single';
			$con['conditions'] = array(
				'user_name' => $username,
				'password' => $password,
				'user_id' => $user_id
			);
			$user = $this->Photo_model->getRows($con);

			if ($user) {
				// Set the response and exit
				$this->response([
					'Status' => TRUE,
					'Message' => 'User login successful.',
					'Data' => $user
				], REST_Controller::HTTP_OK);
			} else {
				// Set the response and exit
				//BAD_REQUEST (400) being the HTTP response code
				$this->response("Wrong userid, email or password.", REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			// Set the response and exit
			$this->response("Provide user, email and password.", REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function registration_post()
	{
		// Get the post data
		$user_name = strip_tags($this->post('user_name'));
		$password = strip_tags($this->post('password'));
		$mobile_no = strip_tags($this->post('mobile_no'));
		$email = strip_tags($this->post('email'));
		$address = strip_tags($this->post('address'));
		$gender = strip_tags($this->post('gender'));

		$usernumrow = $this->db->get('user')->num_rows();
		$user_num_row = $usernumrow + 1;
		$user_id = "USER00" . $user_num_row;

		// Validate the post data
		if (!empty($user_name) && !empty($password)) {

			// Check if the given user_name already exists
			$con['returnType'] = 'count';
			$con['conditions'] = array(
				'user_name' => $user_name,
			);

			$userCount = $this->Photo_model->getRows($con);

			if ($userCount > 0) {
				// Set the response and exit
				$this->response("The given user_name already exists.", REST_Controller::HTTP_BAD_REQUEST);
			} else {
				// Insert user data
				$userData = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'password' => $password,
					'mobile_no' => $mobile_no,
					'email' => $email,
					'address' => $address,
					'gender' => $gender,
				);
				$insert = $this->Photo_model->insert($userData);

				// Check if the user data is inserted
				if ($insert) {
					// Set the response and exit
					$this->response([
						'Status' => TRUE,
						'Message' => 'The user has been added successfully.',
						'Data' => $insert
					], REST_Controller::HTTP_OK);
				} else {
					// Set the response and exit
					$this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} else {
			// Set the response and exit
			$this->response("Provide complete user info to add.", REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function category_get()
	{
		$categories = $this->Photo_model->all_category();

		if (!empty($categories)) {
			$this->response([
				'Status' => TRUE,
				'Message' => 'Categories Data Availaible.',
				'Data' => $categories
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No category was found.',
				'Data' => $categories
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function dashboard_get()
	{
		// Returns all the users data if the id not specified,
		// Otherwise, a single user will be returned.
		//$con = $id?array('id' => $id):'';
		$data['categories'] = $this->Photo_model->all_category();
		$data['banner'] = $this->Photo_model->all_banner();
		$data['coupon'] = $this->Photo_model->getActiceCoupon();

		// Check if the user data exists
		if (!empty($data)) {
			// Set the response and exit
			//OK (200) being the HTTP response code
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' => $data
			], REST_Controller::HTTP_OK);
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => FALSE,
				'Message' => 'None was found.',
				'Data' => $data
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function search_vendor_post()
	{
		$category = strip_tags($this->post('category'));
		$address = strip_tags($this->post('address'));
		$start_date = strip_tags($this->post('start_date'));
		$end_date = strip_tags($this->post('end_date'));
		$start_time = strip_tags($this->post('start_time'));
		$end_time = strip_tags($this->post('end_time'));

		if (empty($category) or empty($address) or empty($start_date) or empty($end_date)) {
			$this->response([
				'Status' => FALSE,
				'Message' => 'Fields are empty'
			], REST_Controller::HTTP_OK);
		} else {


			$this->db->where('category', $category)->where('address', $address)->where('start_time', $start_time)->where('end_time', $end_time)->where('start_date BETWEEN "' . date('d-m-Y', strtotime($start_date)) . '" and "' . date('d-m-Y', strtotime($end_date)) . '"')->where('end_date BETWEEN "' . date('d-m-Y', strtotime($start_date)) . '" and "' . date('d-m-Y', strtotime($end_date)) . '"');


			$data = $this->db->get_where('partner')->result_array();

			foreach ($data as $key => $val) {



				$partner['partner_id'] = $val['partner_id'];
				$partner['partner_name'] = $val['partner_name'];
				$partner['shop_name'] = $val['shop_name'];
				$partner['mobile_no'] = $val['mobile_no'];
				$partner['email'] = $val['email'];
				$partner['address'] = $val['address'];
				$partner['city'] = $val['city'];
				$partner['pincode'] = $val['pincode'];
				$partner['gender'] = $val['gender'];
				$partner['rating'] = $val['rating'];
				$partner['profile_pic'] = $val['profile_pic'];
				$partner['id_proof'] = $val['id_proof'];
				$partner['orig_price'] = $val['orig_price'];
				$partner['idproof_status'] = $val['idproof_status'];
				$partner['passbook'] = $val['passbook'];
				$partner['passbook_status'] = $val['passbook_status'];
				$partner['address_proof'] = $val['address_proof'];
				$partner['addressproof_status'] = $val['addressproof_status'];
				$partner['partner_status'] = $val['partner_status'];
				$partner['avail_city'] = $val['avail_city'];
				$partner['part_latitude'] = $val['part_latitude'];
				$partner['part_longitude'] = $val['part_longitude'];
				$partner['part_photos'] = $val['part_photos'];

				$this->db->where('categ_id', $val['category']);
				$cat = $this->db->get('category')->row_array();
				$partner['category'] = $cat['cate_name'];

				$profile[$key] = $partner;
			}

			if (!empty($data)) {
				$this->response([
					'Status' => TRUE,
					'Message' => 'Data Availaible.',
					'Data' =>  $profile
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => FALSE,
					'Message' => 'Nothing was found.',
					'Data' => $data
				], REST_Controller::HTTP_NOT_FOUND);
			}
		}
	}

	public function location_get()
	{
		// Returns all the users data if the id not specified,
		// Otherwise, a single user will be returned.
		//$con = $id?array('id' => $id):'';
		$data = $this->Photo_model->allpartner();

		// Check if the user data exists

		//echo "<pre>"; print_r($data); die;
		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$address = $val['address'];
				$add[$i]['Location'] = $address;
				$i++;
			}
			//print_r($add); 
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
			//  }
			// Set the response and exit
			//OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => FALSE,
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function allCity_get()
	{
		// Returns all the users data if the id not specified,
		// Otherwise, a single user will be returned.
		//$con = $id?array('id' => $id):'';
		$data = $this->db->get('location')->result_array();


		$i = '0';
		$add = array();
		if (!empty($data)) {
			foreach ($data as $value => $val) {
				$add[$i]['city_id'] = $val['city_id'];
				$address = $val['city'];

				$add[$i]['city'] = $address;
				$i++;
			}

			// Check if the user data exists

			// Set the response and exit
			//OK (200) being the HTTP response code
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' => $add
			], REST_Controller::HTTP_OK);
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => FALSE,
				'Message' => 'None was found.',
				'Data' => $add
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function partner_reg_post()
	{
		// Get the post data
		$partner_name = strip_tags($this->post('partner_name'));
		$mobile_no = strip_tags($this->post('mobile_no'));
		$email = strip_tags($this->post('email'));
		$address = strip_tags($this->post('address'));
		$city = strip_tags($this->post('city'));
		$pincode = strip_tags($this->post('pincode'));
		$gender = strip_tags($this->post('gender'));
		$category = strip_tags($this->post('category'));
		$avail_city = strip_tags($this->post('avail_city'));
		$shop_name = strip_tags($this->post('shop_name'));

		$partnernumrow = $this->db->get('partner')->num_rows();
		$partner_num_row = $partnernumrow + 1;
		$partner_id = "PART00" . $partner_num_row;

		// Validate the post data
		if (!empty($partner_name)) {

			// Check if the given partner_name already exists
			$con['returnType'] = 'count';
			$con['conditions'] = array(
				'email' => $email,
			);

			$partnerCount = $this->Photo_model->getRowsPartner($con);

			if ($partnerCount > 0) {
				// Set the response and exit
				$this->response([
					'Status' => FALSE,
					'Message' => 'Email Already Exists',
					'Data' => "",
					'Error' => REST_Controller::HTTP_BAD_REQUEST
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				// Insert partner data
				$partnerData = array(
					'partner_id' => $partner_id,
					'partner_name' => $partner_name,
					'mobile_no' => $mobile_no,
					'email' => $email,
					'address' => $address,
					'city' => $city,
					'pincode' => $pincode,
					'gender' => $gender,
					'category' => $category,
					'avail_city' => $avail_city,
					'shop_name' => $shop_name,
				);
				$insert = $this->Photo_model->insertPart($partnerData);

				// Check if the partner data is inserted
				if ($insert) {

					$part_id = $partner_id;
					$partid['Partner'] = $part_id;
					// Set the response and exit
					$this->response([
						'Status' => TRUE,
						'Message' => 'The partner has been added successfully.',
						'Data' => $partid
					], REST_Controller::HTTP_OK);
				} else {
					// Set the response and exit
					$this->response([
						'Status' => FALSE,
						'Message' => 'Some problems occurred, please try again.',
						'Data' => $insert,
						'Error' => REST_Controller::HTTP_BAD_REQUEST
					], REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} else {
			// Set the response and exit
			$this->response([
				'Status' => FALSE,
				'Message' => 'Provide complete partner info to add.',
				'Data' => ""
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function getVendorById_post()
	{
		$partner_id = strip_tags($this->post('partner_id'));
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();

			$partner['partner_id'] = $data['partner_id'];
			$partner['partner_name'] = $data['partner_name'];
			$partner['shop_name'] = $data['shop_name'];
			$partner['mobile_no'] = $data['mobile_no'];
			$partner['email'] = $data['email'];
			$partner['address'] = $data['address'];
			$partner['city'] = $data['city'];
			$partner['pincode'] = $data['pincode'];
			$partner['gender'] = $data['gender'];
			$partner['category'] = $data['category'];
			$partner['id_proof'] = $data['id_proof'];
			$partner['idproof_status'] = $data['idproof_status'];
			$partner['passbook'] = $data['passbook'];
			$partner['passbook_status'] = $data['passbook_status'];
			$partner['address_proof'] = $data['address_proof'];
			$partner['addressproof_status'] = $data['addressproof_status'];
			$partner['partner_status'] = $data['partner_status'];
			$partner['avail_city'] = $data['avail_city'];
			$partner['part_latitude'] = $data['part_latitude'];
			$partner['part_longitude'] = $data['part_longitude'];
			$partner['part_photos'] = $data['part_photos'];

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $partner
			], REST_Controller::HTTP_OK);
		}
	}

	public function getPartnerById_post()
	{
		$partner_id = $this->input->post('partner_id');
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();

		if (empty($row)) {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Partner Data Found.',
				'Data' => ""
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();

			$partner['partner_id'] = $data['partner_id'];
			$partner['partner_name'] = $data['partner_name'];
			$partner['shop_name'] = $data['shop_name'];
			$partner['mobile_no'] = $data['mobile_no'];
			$partner['email'] = $data['email'];
			$partner['address'] = $data['address'];
			$partner['city'] = $data['city'];
			$partner['pincode'] = $data['pincode'];
			$partner['gender'] = $data['gender'];
			$partner['category'] = $data['category'];
			$partner['id_proof'] = $data['id_proof'];
			$partner['idproof_status'] = $data['idproof_status'];
			$partner['passbook'] = $data['passbook'];
			$partner['passbook_status'] = $data['passbook_status'];
			$partner['address_proof'] = $data['address_proof'];
			$partner['addressproof_status'] = $data['addressproof_status'];
			$partner['partner_status'] = $data['partner_status'];
			$partner['avail_city'] = $data['avail_city'];
			$partner['part_latitude'] = $data['part_latitude'];
			$partner['part_longitude'] = $data['part_longitude'];
			$partner['part_photos'] = $data['part_photos'];

			$this->response([
				"Status" => TRUE,
				"Message" => "Partner data Found",
				"Data" => $partner
			], REST_Controller::HTTP_OK);
		}
	}

	function upload_idproof_post()
	{
		$partner_id   = $this->input->post('partner_id');
		$base64       = $this->input->post('id_proof');

		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();
		if (!empty($data)) {
			$ImageName = "ID_Proof_" . time();
			$PROFILE_DIRECTORY = './uploads/partner_doc/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('id_proof' => $imageName, 'idproof_status' => '1');
				$this->db->where('partner_id', $partner_id);
				$this->db->update('partner', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "ID Proof Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "ID Proof Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function partner_otp_post()
	{
		$dateid    = date("d/m/Y");
		$mobile = $this->post('mobile_no');
		$query = $this->db->get_where('partner', array('mobile_no' => $this->post('mobile_no')));
		$row = $query->num_rows();
		if ($row > 0) {
			$data = array();
			$data1 = array();
			$ran = mt_rand('1000', '3000');
			$otp = "1000";
			$data1['Status'] = "False";
			$data = $this->db->get_where('partner', array('mobile_no' => $this->post('mobile_no')))->row_array();

			if ($data['partner_status'] == 1) {
				$prtd = $data['partner_id'];

				$partner['id'] = $data['id'];
				$partner['partner_id'] = $data['partner_id'];
				$partner['partner_name'] = $data['partner_name'];
				$partner['shop_name'] = $data['shop_name'];
				$partner['mobile_no'] = $data['mobile_no'];
				$partner['email'] = $data['email'];
				$partner['address'] = $data['address'];
				$partner['city'] = $data['city'];
				$partner['pincode'] = $data['pincode'];
				$partner['gender'] = $data['gender'];
				$partner['category'] = $data['category'];
				$partner['id_proof'] = $data['id_proof'];
				$partner['idproof_status'] = $data['idproof_status'];
				$partner['passbook'] = $data['passbook'];
				$partner['passbook_status'] = $data['passbook_status'];
				$partner['address_proof'] = $data['address_proof'];
				$partner['addressproof_status'] = $data['addressproof_status'];
				$partner['partner_status'] = $data['partner_status'];
				$partner['avail_city'] = $data['avail_city'];
				$partner['part_latitude'] = $data['part_latitude'];
				$partner['part_longitude'] = $data['part_longitude'];
				$partner['part_photos'] = $data['part_photos'];

				$this->response(['Status' => 'TRUE', 'Message' => 'Data Exist', 'OTP' => $otp, 'partner_id' => $prtd, 'Data' => $partner], REST_Controller::HTTP_OK);
			} else if ($data['partner_status'] == 2) {
				$prtd = $data['partner_id'];

				$partner['id'] = $data['id'];
				$partner['partner_id'] = $data['partner_id'];
				$partner['partner_name'] = $data['partner_name'];
				$partner['shop_name'] = $data['shop_name'];
				$partner['mobile_no'] = $data['mobile_no'];
				$partner['email'] = $data['email'];
				$partner['address'] = $data['address'];
				$partner['city'] = $data['city'];
				$partner['pincode'] = $data['pincode'];
				$partner['gender'] = $data['gender'];
				$partner['category'] = $data['category'];
				$partner['id_proof'] = $data['id_proof'];
				$partner['idproof_status'] = $data['idproof_status'];
				$partner['passbook'] = $data['passbook'];
				$partner['passbook_status'] = $data['passbook_status'];
				$partner['address_proof'] = $data['address_proof'];
				$partner['addressproof_status'] = $data['addressproof_status'];
				$partner['partner_status'] = $data['partner_status'];
				$partner['avail_city'] = $data['avail_city'];
				$partner['part_latitude'] = $data['part_latitude'];
				$partner['part_longitude'] = $data['part_longitude'];
				$partner['part_photos'] = $data['part_photos'];

				$this->response(['Status' => 'TRUE', 'Message' => 'Partner Not Approved', 'OTP' => $otp, 'partner_id' => $prtd, 'Data' => $partner], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => 'TRUE', 'Message' => 'Data Not Verified', 'OTP' => $otp], REST_Controller::HTTP_OK);
			}

			$username = 'Saatvik';
			$password = 'saatvik@100';
			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		} else {
			$query = $this->db->get_where('partner', array('mobile_no' => $this->post('mobile_no')));
			$data = $query->result_array();
			$val = $data;
			$ran = mt_rand('1000', '3000');
			$otp = "1000";
			$data['Status'] = "TRUE";
			$data['OTP'] = $otp;
			$this->response(['Status' => 'TRUE', 'Message' => 'Data not Exist or Partner', 'OTP' => $otp, 'Partner ID' => 'Null', 'Data' => $val], REST_Controller::HTTP_OK);
			$username = 'Saatvik';
			$password = 'saatvik@100';
			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		}
	}

	function upload_profile_post()
	{
		$partner_id   = $this->input->post('partner_id');
		$base64       = $this->input->post('profile_pic');
		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Profile_" . time();
			$PROFILE_DIRECTORY = './uploads/partner_doc/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('profile_pic' => $imageName);
				$this->db->where('partner_id', $partner_id);
				$this->db->update('partner', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Profile Pic Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "Profile Pic Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function upload_passbook_post()
	{
		$partner_id   = $this->input->post('partner_id');
		$base64       = $this->input->post('passbook');
		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Passbook_" . time();
			$PROFILE_DIRECTORY = './uploads/partner_doc/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('passbook' => $imageName, 'passbook_status' => '1');
				$this->db->where('partner_id', $partner_id);
				$this->db->update('partner', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Passbook Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "FALSE",
						"Message" => "Passbook Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function upload_address_proof_post()
	{
		$partner_id   = $this->input->post('partner_id');
		$base64       = $this->input->post('address_proof');
		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Address_proof_" . time();
			$PROFILE_DIRECTORY = './uploads/partner_doc/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('address_proof' => $imageName, 'addressproof_status' => '1');
				$this->db->where('partner_id', $partner_id);
				$this->db->update('partner', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Address Proof Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "TRUE",
						"Message" => "Address Proof Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function partner_category_update_post()
	{
		$partner_id = $this->input->post('partner_id');
		$partnerData['category'] = $this->post('category');

		$query = $this->db->get_where('partner', array('partner_id' => $partner_id));
		$row = $query->num_rows();
		if ($row > 0) {
			$this->db->where('partner_id', $partner_id);
			$updatedata = $this->db->update('partner', $partnerData);
			if ($updatedata) {
				$this->response(['Status' => 'TRUE', 'Message' => 'Data Exist', 'Message' => 'Partner Category Updated successfully', 'Data' => $partnerData], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => 'FALSE', 'Message' => 'Data Not Found'], REST_Controller::HTTP_NOT_FOUND);
				$this->response(['Status' => 'FALSE', 'Partner ID' => 'NULL', 'Message' => "Some problems occurred, please try again."], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(['Status' => 'FALSE', 'Message' => 'Provide complete Partner information to create.'], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function partner_city_update_post()
	{
		$partner_id = $this->input->post('partner_id');
		$partnerData['city'] = $this->post('city');

		$query = $this->db->get_where('partner', array('partner_id' => $partner_id));
		$row = $query->num_rows();
		if ($row > 0) {
			$this->db->where('partner_id', $partner_id);
			$updatedata = $this->db->update('partner', $partnerData);
			if ($updatedata) {
				$this->response(['Status' => 'TRUE', 'Message' => 'Data Exist', 'Message' => 'Partner City Updated successfully', 'Data' => $partnerData], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => 'FALSE', 'Message' => 'Data Not Found'], REST_Controller::HTTP_NOT_FOUND);
				$this->response(['Status' => 'FALSE', 'Partner ID' => 'NULL', 'Message' => "Some problems occurred, please try again."], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(['Status' => 'FALSE', 'Message' => 'Provide complete Partner information to create.'], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function get_partner_data_post()
	{
		$partner_id = $this->input->post('partner_id');
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();

			$partner['partner_id'] = $data['partner_id'];
			$partner['partner_name'] = $data['partner_name'];
			$partner['shop_name'] = $data['shop_name'];
			$partner['mobile_no'] = $data['mobile_no'];
			$partner['mobile_no'] = $data['mobile_no'];
			$partner['email'] = $data['email'];
			$partner['address'] = $data['address'];
			$partner['city'] = $data['city'];
			$partner['pincode'] = $data['pincode'];
			$partner['gender'] = $data['gender'];
			$partner['category'] = $data['category'];
			$partner['id_proof'] = $data['id_proof'];
			$partner['idproof_status'] = $data['idproof_status'];
			$partner['passbook'] = $data['passbook'];
			$partner['passbook_status'] = $data['passbook_status'];
			$partner['address_proof'] = $data['address_proof'];
			$partner['addressproof_status'] = $data['addressproof_status'];
			$partner['partner_status'] = $data['partner_status'];
			$partner['avail_city'] = $data['avail_city'];

			$this->response([
				"Status" => TRUE,
				"Message" => "Partner data Found",
				"Data" => $partner
			], REST_Controller::HTTP_OK);
		}
	}

	function upload_part_photos_post()
	{
		$partner_id   = $this->input->post('partner_id');
		$base64       = $this->input->post('part_photos');
		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Portfolio_" . time();
			$PROFILE_DIRECTORY = './uploads/part_portfolio/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('part_photos' => $imageName);
				$this->db->where('partner_id', $partner_id);
				$this->db->update('partner', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Portfolio Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "TRUE",
						"Message" => "Portfolio Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function partner_shoot_type_post()
	{
		$partner_id = $this->input->post('partner_id');
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();

			$partner['Shoot Type'] = $data['shoot_type'];

			$this->response([
				"Status" => TRUE,
				"Message" => "Partner data Found",
				"Data" => $partner
			], REST_Controller::HTTP_OK);
		}
	}

	function update_shoot_type_post()
	{
		$partner_id = $this->input->post('partner_id');
		$shootData['shoot_type'] = $this->post('shoot_type');
		$query = $this->db->get_where('partner', array('partner_id' => $partner_id));
		$row = $query->num_rows();
		if ($row) {
			$query =  $this->db->where('partner_id', $this->post('partner_id'));
			$query = $this->db->update('partner', $shootData);
			$this->response(['Status' => TRUE, 'Message' => 'Updated Shoot Type successfully.', 'Data' => $shootData], REST_Controller::HTTP_OK);
		} else {
			$this->response(['Status' => FALSE, 'Message' => "Failed No data Available"], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	function update_partner_post()
	{
		$partner_id = $this->input->post('partner_id');

		$partData['partner_name'] = $this->post('partner_name');
		$partData['mobile_no'] = $this->post('mobile_no');
		$partData['email'] = $this->post('email');
		$partData['address'] = $this->post('address');
		$partData['city'] = $this->post('city');
		$partData['pincode'] = $this->post('pincode');
		$partData['gender'] = $this->post('gender');
		$partData['shop_name'] = $this->post('shop_name');


		$query = $this->db->get_where('partner', array('partner_id' => $partner_id));
		$row = $query->num_rows();
		if ($row) {
			$query =  $this->db->where('partner_id', $this->post('partner_id'));
			$query = $this->db->update('partner', $partData);
			$this->response(['Status' => TRUE, 'Message' => 'Updated Partner successfully.', 'Data' => $partData], REST_Controller::HTTP_OK);
		} else {
			$this->response(['Status' => FALSE, 'Message' => "Failed No data Available"], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	function upload_part_videos_post()
	{
		$partner_id   = $this->input->post('partner_id');
		$base64       = $this->input->post('part_videos');
		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();
		if (!empty($data)) {
			$ImageName = "Video_" . time();
			$PROFILE_DIRECTORY = './uploads/part_videos/';
			$img = @imagecreatefromstring(base64_decode($base64));
			if ($img != false) {
				$imageName  = ($ImageName != '') ? $ImageName . '.mp4' : 'sign' . generate_unique_code1() . time() . '.mp4';
				$path = $PROFILE_DIRECTORY . $imageName;
				$data = array('part_videos' => $imageName);
				$this->db->where('partner_id', $partner_id);
				$this->db->update('partner', $data);
				$this->response([
					"Status" => "TRUE",
					"Message" => "Video Uploaded successfully"
				], REST_Controller::HTTP_OK);
				if (imagejpeg($img, $path)) {
					return $imageName;
					$this->response([
						"Status" => "TRUE",
						"Message" => "Video Uploaded successfully"
					], REST_Controller::HTTP_OK);
				} else {
					$Message = array('Message' => 'Data INSERTION FAILED');
					echo json_encode($Message);
				}
			}
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	function update_partner_desc_post()
	{
		$partner_id = $this->input->post('partner_id');

		$partData['partner_desc'] = $this->post('partner_desc');


		$query = $this->db->get_where('partner', array('partner_id' => $partner_id));
		$row = $query->num_rows();
		if ($row) {
			$query =  $this->db->where('partner_id', $this->post('partner_id'));
			$query = $this->db->update('partner', $partData);
			$this->response(['Status' => TRUE, 'Message' => 'Updated Partner successfully.', 'Data' => $partData], REST_Controller::HTTP_OK);
		} else {
			$this->response(['Status' => FALSE, 'Message' => "Failed No data Available"], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	function update_shootType_post()
	{
		$shootData['partner_id'] = $this->input->post('partner_id');
		$shootData['type'] = $this->post('type');
		$shootData['num_photos'] = $this->post('num_photos');
		$shootData['price'] = $this->post('price');
		$shootData['disc_price'] = $this->post('disc_price');

		if (!empty($shootData)) {

			// Check if the given user_name already exists
			$con['returnType'] = 'count';
			$con['conditions'] = array(
				'type' => $shootData['type'],
				'num_photos' => $shootData['num_photos'],
				'partner_id' => $shootData['partner_id'],
			);

			$userCount = $this->Photo_model->getRowsForShootType($con);

			if ($userCount > 0) {
				// Set the response and exit
				$this->db->where('partner_id', $shootData['partner_id']);
				$this->db->where('type', $shootData['type']);
				$this->db->where('num_photos', $shootData['num_photos']);
				$update = $this->db->update('shoot_type', $shootData);

				$this->response([
					'Status' => TRUE,
					'Message' => 'The Shoot Type has updated successfully.',
					'Data' => $shootData
				], REST_Controller::HTTP_OK);
			} else {
				$insert = $this->db->insert('shoot_type', $shootData);

				// Check if the user data is inserted
				if ($insert) {
					// Set the response and exit
					$this->response([
						'Status' => TRUE,
						'Message' => 'The Shoot Type has been added successfully.',
						'Data' => $shootData
					], REST_Controller::HTTP_OK);
				} else {
					// Set the response and exit
					$this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} else {
			// Set the response and exit
			$this->response("Provide complete user info to add.", REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function userlogin_otp_post()
	{
		$dateid    = date("d/m/Y");
		$mobile = $this->post('mobile_no');
		$query = $this->db->get_where('user', array('mobile_no' => $this->post('mobile_no')));
		$row = $query->num_rows();
		if ($row > 0) {
			$data = array();
			$data1 = array();
			$ran = mt_rand('1000', '3000');
			$otp = "1000";
			$data1['Status'] = "False";
			$data = $this->db->get_where('user', array('mobile_no' => $this->post('mobile_no')))->row_array();



			$this->response(['Status' => 'TRUE', 'Message' => 'Data Exist', 'OTP' => $otp, 'Data' => $data], REST_Controller::HTTP_OK);
			$username = 'Saatvik';
			$password = 'saatvik@100';
			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		} else {
			$query = $this->db->get_where('user', array('mobile_no' => $this->post('mobile_no')));
			$data = $query->result_array();
			$val = $data;
			$ran = mt_rand('1000', '3000');
			$otp = "1000";
			$data['Status'] = "TRUE";
			$data['OTP'] = $otp;
			$this->response(['Status' => 'TRUE', 'Message' => 'Data not Exist', 'OTP' => $otp, 'Data' => $val], REST_Controller::HTTP_OK);
			$username = 'Saatvik';
			$password = 'saatvik@100';
			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		}
	}

	public function checkout_post()
	{
		$partner_id = $this->post('partner_id');

		$query = $this->db->get_where('order_details', array('part_id' => $partner_id));
		$row = $query->result_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('part_id', $partner_id);
			$query = $this->db->get('order_details');
			$val = $query->result_array();

			foreach ($val as $key => $data) {

				$partner['address'] = $data['address'];

				$partner['subtotal'] = $data['subtotal'];
				$partner['gst'] = $data['gst'];
				$partner['discount'] = $data['discount'];
				$partner['total'] = $data['tot_amount'];

				$partner_id = $data['part_id'];


				$this->db->where('partner_id', $partner_id);
				$partner_details = $this->db->get('partner')->row_array();
				$partner['partner_name'] = $partner_details['partner_name'];
				$partner['shop_name'] = $partner_details['shop_name'];
				$partner['mobile_no'] = $partner_details['mobile_no'];
				$partner['address'] = $partner_details['address'];
				$partner['start_date'] = $partner_details['start_date'];
				$partner['end_date'] = $partner_details['end_date'];
				$partner['start_time'] = $partner_details['start_time'];
				$partner['end_time'] = $partner_details['end_time'];


				$profile[$key] = $partner;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $profile
			], REST_Controller::HTTP_OK);
		}
	}

	public function getVendor_post()
	{

		$query = $this->db->get('partner');
		$row = $query->result_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$query = $this->db->get('partner');
			$val = $query->result_array();

			foreach ($val as $key => $data) {

				$partner['partner_id'] = $data['partner_id'];
				$partner['partner_name'] = $data['partner_name'];
				$partner['shop_name'] = $data['shop_name'];
				$partner['mobile_no'] = $data['mobile_no'];
				$partner['email'] = $data['email'];
				$partner['address'] = $data['address'];
				$partner['city'] = $data['city'];
				$partner['pincode'] = $data['pincode'];
				$partner['gender'] = $data['gender'];
				$partner['category'] = $data['category'];
				$partner['avail_city'] = $data['avail_city'];
				$partner['part_photos'] = $data['part_photos'];
				$profile[$key] = $partner;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $profile
			], REST_Controller::HTTP_OK);
		}
	}

	public function dashboardBanner_get()
	{
		$data = $this->db->get('banner')->result_array();

		if (!empty($data)) {

			foreach ($data as $key => $val) {

				$partner['pic_file'] = $val['pic_file'];

				$profile[$key] = $partner;
			}

			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' => $profile
			], REST_Controller::HTTP_OK);
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => FALSE,
				'Message' => 'None was found.',
				'Data' => $data
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function dashboardCoupon_get()
	{
		$this->db->where('status', 1);
		$data = $this->db->get('coupon')->result_array();

		if (!empty($data)) {

			foreach ($data as $key => $val) {

				$partner['pic_file'] = $val['pic_file'];

				$profile[$key] = $partner;
			}

			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' => $profile
			], REST_Controller::HTTP_OK);
		} else {
			// Set the response and exit
			//NOT_FOUND (404) being the HTTP response code
			$this->response([
				'Status' => FALSE,
				'Message' => 'None was found.',
				'Data' => $data
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function getVendorShop_post()
	{
		$partner_id = strip_tags($this->post('partner_id'));
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();

			$this->db->where('partner_id', $partner_id);
			$review = $this->db->count_all_results('review');

			$partner['shop_name'] = $data['shop_name'];
			$partner['city'] = $data['city'];
			$partner['rating'] = $data['rating'];
			$partner['total_review'] = $review;
			$partner['part_latitude'] = $data['part_latitude'];
			$partner['part_longitude'] = $data['part_longitude'];


			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $partner
			], REST_Controller::HTTP_OK);
		}
	}

	public function vendorPhotos_post()
	{
		$partner_id = strip_tags($this->post('partner_id'));
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();
			$c = explode(",", $data['part_photos']);
			$partner = $data['part_photos'];

			$i = '0';
			foreach ($c as $key => $val) {

				$pic[$i]['part_photos'] = $val;
				$i++;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $pic
			], REST_Controller::HTTP_OK);
		}
	}

	public function addUser_post()
	{
		// Get the post data
		$user_name = strip_tags($this->post('user_name'));
		$mobile_no = strip_tags($this->post('mobile_no'));
		$email = strip_tags($this->post('email'));
		$address = strip_tags($this->post('address'));
		$gender = strip_tags($this->post('gender'));

		$usernumrow = $this->db->get('user')->num_rows();
		$user_num_row = $usernumrow + 1;
		$user_id = "USER00" . $user_num_row;

		// Validate the post data
		if (!empty($email) && !empty($mobile_no)) {

			// Check if the given user_name already exists
			$con['returnType'] = 'count';
			$con['conditions'] = array(
				'email' => $email,
			);

			$userCount = $this->Photo_model->getRows($con);

			if ($userCount > 0) {
				// Set the response and exit
				$this->response("The given Email already exists.", REST_Controller::HTTP_BAD_REQUEST);
			} else {
				// Insert user data
				$userData = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'mobile_no' => $mobile_no,
					'email' => $email,
					'address' => $address,
					'gender' => $gender,
				);
				$insert = $this->Photo_model->insert($userData);

				// Check if the user data is inserted
				if ($insert) {
					// Set the response and exit
					$this->response([
						'Status' => TRUE,
						'Message' => 'The user has been added successfully.',
						'Data' => $userData
					], REST_Controller::HTTP_OK);
				} else {
					// Set the response and exit
					$this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} else {
			// Set the response and exit
			$this->response("Provide complete user info to add.", REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function searchVendorByCity_post()
	{
		$city = strip_tags($this->post('city'));

		if (empty($city)) {
			$this->response([
				'Status' => FALSE,
				'Message' => 'Fields are empty'
			], REST_Controller::HTTP_OK);
		} else {


			$this->db->where('city', $city);


			$data = $this->db->get_where('partner')->result_array();

			foreach ($data as $key => $val) {

				$partner['partner_id'] = $val['partner_id'];
				$partner['partner_name'] = $val['partner_name'];
				$partner['shop_name'] = $val['shop_name'];
				$partner['mobile_no'] = $val['mobile_no'];
				$partner['email'] = $val['email'];
				$partner['address'] = $val['address'];
				$partner['city'] = $val['city'];
				$partner['pincode'] = $val['pincode'];
				$partner['gender'] = $val['gender'];
				$partner['rating'] = $val['rating'];
				$partner['profile_pic'] = $val['profile_pic'];
				$partner['id_proof'] = $val['id_proof'];
				$partner['orig_price'] = $val['orig_price'];
				$partner['idproof_status'] = $val['idproof_status'];
				$partner['passbook'] = $val['passbook'];
				$partner['passbook_status'] = $val['passbook_status'];
				$partner['address_proof'] = $val['address_proof'];
				$partner['addressproof_status'] = $val['addressproof_status'];
				$partner['partner_status'] = $val['partner_status'];
				$partner['avail_city'] = $val['avail_city'];
				$partner['part_latitude'] = $val['part_latitude'];
				$partner['part_longitude'] = $val['part_longitude'];
				$partner['part_photos'] = $val['part_photos'];

				$this->db->where('categ_id', $val['category']);
				$cat = $this->db->get('category')->row_array();
				$partner['category'] = $cat['cate_name'];

				$profile[$key] = $partner;
			}

			if (!empty($data)) {
				$this->response([
					'Status' => TRUE,
					'Message' => 'Data Availaible.',
					'Data' =>  $profile
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => FALSE,
					'Message' => 'Nothing was found.',
					'Data' => $data
				], REST_Controller::HTTP_NOT_FOUND);
			}
		}
	}

	public function priceLowToHigh_get()
	{
		$this->db->order_by('orig_price', 'asc');
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->order_by('orig_price', 'asc');
			$query = $this->db->get('partner');
			$data = $query->result_array();

			foreach ($data as $value => $val) {
				$partner['partner_id'] = $val['partner_id'];
				$partner['partner_name'] = $val['partner_name'];
				$partner['shop_name'] = $val['shop_name'];
				$partner['mobile_no'] = $val['mobile_no'];
				$partner['orig_price'] = $val['orig_price'];
				$partner['email'] = $val['email'];
				$partner['address'] = $val['address'];
				$partner['city'] = $val['city'];
				$partner['pincode'] = $val['pincode'];
				$partner['gender'] = $val['gender'];
				$partner['id_proof'] = $val['id_proof'];
				$partner['rating'] = $val['rating'];
				$partner['idproof_status'] = $val['idproof_status'];
				$partner['passbook'] = $val['passbook'];
				$partner['passbook_status'] = $val['passbook_status'];
				$partner['address_proof'] = $val['address_proof'];
				$partner['addressproof_status'] = $val['addressproof_status'];
				$partner['partner_status'] = $val['partner_status'];
				$partner['avail_city'] = $val['avail_city'];
				$partner['part_latitude'] = $val['part_latitude'];
				$partner['part_longitude'] = $val['part_longitude'];
				$partner['part_photos'] = $val['part_photos'];

				$this->db->where('categ_id', $val['category']);
				$cat = $this->db->get('category')->row_array();
				$partner['category'] = $cat['cate_name'];

				$profile[$value] = $partner;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $profile
			], REST_Controller::HTTP_OK);
		}
	}

	public function priceHighToLow_get()
	{
		$this->db->order_by('orig_price', 'desc');
		$row = $this->db->get('partner')->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->order_by('orig_price', 'desc');
			$query = $this->db->get('partner');
			$data = $query->result_array();

			foreach ($data as $value => $val) {
				$partner['partner_id'] = $val['partner_id'];
				$partner['partner_name'] = $val['partner_name'];
				$partner['shop_name'] = $val['shop_name'];
				$partner['mobile_no'] = $val['mobile_no'];
				$partner['orig_price'] = $val['orig_price'];
				$partner['email'] = $val['email'];
				$partner['address'] = $val['address'];
				$partner['city'] = $val['city'];
				$partner['pincode'] = $val['pincode'];
				$partner['gender'] = $val['gender'];
				$partner['id_proof'] = $val['id_proof'];
				$partner['rating'] = $val['rating'];
				$partner['idproof_status'] = $val['idproof_status'];
				$partner['passbook'] = $val['passbook'];
				$partner['passbook_status'] = $val['passbook_status'];
				$partner['address_proof'] = $val['address_proof'];
				$partner['addressproof_status'] = $val['addressproof_status'];
				$partner['partner_status'] = $val['partner_status'];
				$partner['avail_city'] = $val['avail_city'];
				$partner['part_latitude'] = $val['part_latitude'];
				$partner['part_longitude'] = $val['part_longitude'];
				$partner['part_photos'] = $val['part_photos'];

				$this->db->where('categ_id', $val['category']);
				$cat = $this->db->get('category')->row_array();
				$partner['category'] = $cat['cate_name'];

				$profile[$value] = $partner;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $profile
			], REST_Controller::HTTP_OK);
		}
	}

	public function sortVendorByRating_get()
	{
		$this->db->order_by('rating', 'desc');
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->order_by('rating', 'desc');
			$query = $this->db->get('partner');
			$data = $query->result_array();

			foreach ($data as $value => $val) {
				$partner['partner_id'] = $val['partner_id'];
				$partner['partner_name'] = $val['partner_name'];
				$partner['shop_name'] = $val['shop_name'];
				$partner['mobile_no'] = $val['mobile_no'];
				$partner['rating'] = $val['rating'];
				$partner['orig_price'] = $val['orig_price'];
				$partner['email'] = $val['email'];
				$partner['address'] = $val['address'];
				$partner['city'] = $val['city'];
				$partner['pincode'] = $val['pincode'];
				$partner['gender'] = $val['gender'];
				$partner['id_proof'] = $val['id_proof'];
				$partner['idproof_status'] = $val['idproof_status'];
				$partner['passbook'] = $val['passbook'];
				$partner['passbook_status'] = $val['passbook_status'];
				$partner['address_proof'] = $val['address_proof'];
				$partner['addressproof_status'] = $val['addressproof_status'];
				$partner['partner_status'] = $val['partner_status'];
				$partner['avail_city'] = $val['avail_city'];
				$partner['part_latitude'] = $val['part_latitude'];
				$partner['part_longitude'] = $val['part_longitude'];
				$partner['part_photos'] = $val['part_photos'];

				$this->db->where('categ_id', $val['category']);
				$cat = $this->db->get('category')->row_array();
				$partner['category'] = $cat['cate_name'];

				$profile[$value] = $partner;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $profile
			], REST_Controller::HTTP_OK);
		}
	}

	public function vendorVideos_post()
	{
		$partner_id = strip_tags($this->post('partner_id'));
		$this->db->where('partner_id', $partner_id);
		$query = $this->db->get('partner');
		$row = $query->row_array();
		if (!$row) {
			$data = array();
			$data1 = array();
			$data1['Status'] = "False";
			$data1['Message'] = "No Partner data Found";
			$data = $data1;
			$this->response($data, REST_Controller::HTTP_OK);
		} else {
			$this->db->where('partner_id', $partner_id);
			$query = $this->db->get('partner');
			$data = $query->row_array();
			$c = explode(",", $data['part_videos']);
			$partner = $data['part_videos'];

			$i = '0';
			foreach ($c as $key => $val) {

				$pic[$i]['part_videos'] = $val;
				$i++;
			}

			$this->response([
				"Status" => "TRUE",
				"Message" => "Partner data Found",
				"Data" => $pic
			], REST_Controller::HTTP_OK);
		}
	}

	public function resend_partner_otp_post()
	{
		$dateid    = date("d/m/Y");
		$mobile = $this->post('mobile_no');
		$query = $this->db->get_where('partner', array('mobile_no' => $this->post('mobile_no')));
		$row = $query->num_rows();
		if ($row > 0) {
			$data = array();
			$data1 = array();
			$ran = mt_rand('1000', '3000');
			$otp = "$ran";
			$data1['Status'] = "False";
			$data = $this->db->get_where('partner', array('mobile_no' => $this->post('mobile_no')))->row_array();
			$prtd = $data['partner_id'];

			$partner['partner_id'] = $data['partner_id'];
			$partner['partner_name'] = $data['partner_name'];
			$partner['shop_name'] = $data['shop_name'];
			$partner['mobile_no'] = $data['mobile_no'];
			$partner['email'] = $data['email'];
			$partner['address'] = $data['address'];
			$partner['city'] = $data['city'];
			$partner['pincode'] = $data['pincode'];
			$partner['gender'] = $data['gender'];
			$partner['category'] = $data['category'];
			$partner['id_proof'] = $data['id_proof'];
			$partner['idproof_status'] = $data['idproof_status'];
			$partner['passbook'] = $data['passbook'];
			$partner['passbook_status'] = $data['passbook_status'];
			$partner['address_proof'] = $data['address_proof'];
			$partner['addressproof_status'] = $data['addressproof_status'];
			$partner['partner_status'] = $data['partner_status'];
			$partner['avail_city'] = $data['avail_city'];
			$partner['part_latitude'] = $data['part_latitude'];
			$partner['part_longitude'] = $data['part_longitude'];
			$partner['part_photos'] = $data['part_photos'];

			$this->response(['Status' => 'TRUE', 'Message' => 'Data Exist! OTP Resent', 'OTP' => $otp, 'Partner ID' => $prtd, 'Data' => $partner], REST_Controller::HTTP_OK);
			$username = 'Saatvik';
			$password = 'saatvik@100';
			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		} else {
			$query = $this->db->get_where('partner', array('mobile_no' => $this->post('mobile_no')));
			$data = $query->result_array();
			$val = $data;
			$ran = mt_rand('1000', '3000');
			$otp = "$ran";
			$data['Status'] = "TRUE";
			$data['OTP'] = $otp;
			$this->response(['Status' => 'TRUE', 'Message' => 'Data not Exist! OTP Resent', 'OTP' => $otp, 'Partner ID' => 'Null', 'Data' => $val], REST_Controller::HTTP_OK);
			$username = 'Saatvik';
			$password = 'saatvik@100';
			$mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
			$url  = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
		}
	}

	public function vendorRegCategory_get()
	{
		$categories = $this->Photo_model->all_category();

		foreach ($categories as $key => $val) {
			$cat['cat_id'] = $val['categ_id'];
			$cat['category'] = $val['cate_name'];
			$cate[$key] = $cat;
		}

		if (!empty($categories)) {
			$this->response([
				'Status' => TRUE,
				'Message' => 'Categories Data Availaible.',
				'Data' => $cate
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No category was found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function vendorRegister_post()
	{
		// Get the post data
		$partner_name = strip_tags($this->post('name'));
		$mobile_no = strip_tags($this->post('mobile'));
		$address = strip_tags($this->post('address'));
		$city = strip_tags($this->post('city'));
		$gender = strip_tags($this->post('gender'));
		$category = strip_tags($this->post('cat_type'));

		$partnernumrow = $this->db->get('partner')->num_rows();
		$partner_num_row = $partnernumrow + 1;
		$partner_id = "PART00" . $partner_num_row;

		// Validate the post data
		if (!empty($mobile_no)) {

			// Check if the given partner_name already exists
			$con['returnType'] = 'count';
			$con['conditions'] = array(
				'mobile_no' => $mobile_no,
			);

			$partnerCount = $this->Photo_model->getRowsPartner($con);

			if ($partnerCount > 0) {
				// Set the response and exit
				$this->response([
					'Status' => FALSE,
					'Message' => 'Mobile Already Exists',
					'Data' => ""
				], REST_Controller::HTTP_OK);
			} else {
				// Insert partner data
				$partnerData = array(
					'partner_id' => $partner_id,
					'partner_name' => $partner_name,
					'mobile_no' => $mobile_no,
					'address' => $address,
					'city' => $city,
					'gender' => $gender,
					'category' => $category,
				);
				$insert = $this->Photo_model->insertPart($partnerData);

				// Check if the partner data is inserted
				if ($insert) {

					$part_id = $partner_id;
					$partid['Partner'] = $part_id;
					// Set the response and exit
					$this->response([
						'Status' => TRUE,
						'Message' => 'The partner has been added successfully.',
						'Data' => $partnerData
					], REST_Controller::HTTP_OK);
				} else {
					// Set the response and exit
					$this->response([
						'Status' => FALSE,
						'Message' => 'Some problems occurred, please try again.'
					], REST_Controller::HTTP_OK);
				}
			}
		} else {
			// Set the response and exit
			$this->response([
				'Status' => FALSE,
				'Message' => 'Provide complete partner info to add.',
				'Data' => ""
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function addReview_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['partner_id'] = $this->post('partner_id');
		$field['rating'] = $this->post('rating');
		$field['title'] = $this->post('title');
		$field['review'] = $this->post('review');



		if (!empty($field)) {
			$insert = $this->db->insert('review', $field);
			if ($insert > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Review Has been Added',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Empty Fields Passed'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function addEventPayment_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['part_id'] = $this->post('partner_id');
		$field['subtotal'] = $this->post('subtotal');
		$field['gst'] = $this->post('gst');
		$field['discount'] = $this->post('discount');
		$field['tot_amount'] = $this->post('total');
		$field['pay_method'] = $this->post('pay_method');

		$usernumrow = $this->db->get('order_details')->num_rows();
		$user_num_row = $usernumrow + 1;
		$field['order_id'] = "ORD00" . $user_num_row;



		if (!empty($field)) {
			$insert = $this->db->insert('order_details', $field);
			if ($insert > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Event Order Has been Added',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Empty Fields Passed'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function addUserWallet_post()
	{
		$user_id = $this->post('user_id');
		$amount = $this->post('amount');

		$data = $this->db->get_where('user_wallet', array('user_id' => $user_id))->row_array();

		if (!empty($data)) {

			$field['amount'] = $data['amount'] + $amount;

			$this->db->where('user_id', $user_id);
			$update = $this->db->update('user_wallet', $field);


			$wal['user_id'] = $user_id;
			$wal['add_amnt'] = $amount;
			$wal['total_amount'] = $field['amount'];


			if ($update > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Amount Has been Updated To Wallet',
					'Data' => $wal
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else if (empty($data)) {
			$field['user_id'] = $user_id;
			$field['amount'] = $amount;

			$update = $this->db->insert('user_wallet', $field);

			if ($update > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Amount Has been Added To Wallet',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Empty Fields Passed'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function userWallet_post()
	{
		$user_id = $this->post('user_id');

		$totalAmnt = $this->db->get_where('user_wallet', array('user_id' => $user_id))->row_array();

		$amnt['total_amount'] = $totalAmnt['amount'];

		if (!empty($totalAmnt)) {
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' =>   $amnt
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function partnerCategoryPrice_post()
	{
		$partner_id = $this->post('partner_id');
		$data = $this->db->get_where('partnerCatPrice', array('partner_id' => $partner_id))->result_array();
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$partner['partner_id'] = $val['partner_id'];
				$partner['category_id'] = $val['category_id'];
				$this->db->where('categ_id', $val['category_id']);
				$cat = $this->db->get('category')->row_array();
				$partner['category'] = $cat['cate_name'];
				$partner['amount'] = $val['amount'];
				$profile[$key] = $partner;
			}
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' =>   $profile
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function addBookingDetails_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['part_id'] = $this->post('partner_id');
		$field['cate_id'] = $this->post('category_id');
		$field['tot_amount'] = $this->post('amount');
		$field['address'] = $this->post('address');
		$field['from_date'] = $this->post('from_date');
		$field['to_date'] = $this->post('to_date');
		$field['from_time'] = $this->post('from_time');
		$field['to_time'] = $this->post('to_time');
		$field['user_mobile'] = $this->post('user_mobile');
		$field['user_name'] = $this->post('user_name');
		$field['city'] = $this->post('city');
		$field['remarks'] = $this->post('remarks');

		date_default_timezone_set('Asia/Kolkata');
		$field['created_at'] = date('Y-m-d');

		$usernumrow = $this->db->get('order_details')->num_rows();
		$user_num_row = $usernumrow + 1;
		$field['order_id'] = "ORD00" . $user_num_row;



		if (!empty($field)) {
			$insert = $this->db->insert('order_details', $field);
			if ($insert > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Event Order Has been Added',
					'Data' => [$field]
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_OK);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Empty Fields Passed'
			], REST_Controller::HTTP_OK);
		}
	}

	public function onGoingBooking_post()
	{
		$user_id = $this->post('user_id');
		$date = $this->post('date');

		$this->db->where('to_date >=', $date);
		$data = $this->db->get_where('order_details', array('user_id' => $user_id))->result_array();

		if (!empty($data)) {

			foreach ($data as $key => $val) {
				$book['order_id'] = $val['order_id'];
				$book['user_id'] = $val['user_id'];
				$book['part_id'] = $val['part_id'];
				$book['cate_id'] = $val['cate_id'];
				$book['address'] = $val['address'];
				$book['from_date'] = $val['from_date'];
				$book['to_date'] = $val['to_date'];
				$book['created_at'] = $val['created_at'];

				$this->db->where('categ_id', $val['cate_id']);
				$cat = $this->db->get('category')->row_array();
				$book['category'] = $cat['cate_name'];

				$booking[$key] = $book;
			}
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' =>   $booking
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function historyBooking_post()
	{
		$user_id = $this->post('user_id');
		$date = $this->post('date');

		$this->db->where('to_date <', $date);
		$data = $this->db->get_where('order_details', array('user_id' => $user_id))->result_array();

		if (!empty($data)) {

			foreach ($data as $key => $val) {
				$book['order_id'] = $val['order_id'];
				$book['user_id'] = $val['user_id'];
				$book['part_id'] = $val['part_id'];
				$book['cate_id'] = $val['cate_id'];
				$book['address'] = $val['address'];
				$book['from_date'] = $val['from_date'];
				$book['to_date'] = $val['to_date'];
				$book['created_at'] = $val['created_at'];

				$this->db->where('categ_id', $val['cate_id']);
				$cat = $this->db->get('category')->row_array();
				$book['category'] = $cat['cate_name'];

				$booking[$key] = $book;
			}
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' =>   $booking
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}



	function uploadPartnerDoc_post()
	{
		$partner_id = $this->input->post('partner_id');
		$base64_idproof = $this->input->post('id_proof');
		$base64_profile = $this->input->post('profile_pic');
		$base64_passbook = $this->input->post('passbook');
		$base64 = $this->input->post('address_proof');

		$data = $this->db->get_where('partner', array('partner_id' => $partner_id))->row_array();

		if (!empty($data)) {

			//ID Proof
			$IDProof = "ID_Proof_" . time();
			$PROFILE_DIRECTORY_ID = './uploads/partner_doc/';
			$id = @imagecreatefromstring(base64_decode($base64_idproof));
			$idproof  = ($IDProof != '') ? $IDProof . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
			$pathidprood = $PROFILE_DIRECTORY_ID . $idproof;

			$PROfile = "Profile_" . time();
			$PROFILE_DIRECTORY_PROFILE = './uploads/partner_doc/';
			$pro = @imagecreatefromstring(base64_decode($base64_profile));
			$profile  = ($PROfile != '') ? $PROfile . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
			$pathprofile = $PROFILE_DIRECTORY_PROFILE . $profile;

			$PASSbook = "Passbook_" . time();
			$PROFILE_DIRECTORY_PASSBOOK = './uploads/partner_doc/';
			$pass = @imagecreatefromstring(base64_decode($base64_passbook));
			$passbook  = ($PASSbook != '') ? $PASSbook . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
			$pathpassbook = $PROFILE_DIRECTORY_PASSBOOK . $passbook;

			$ADDproof = "Address_proof_" . time();
			$PROFILE_DIRECTORY_ADDPROOF = './uploads/partner_doc/';
			$addprf = @imagecreatefromstring(base64_decode($base64));
			$addproof  = ($ADDproof != '') ? $ADDproof . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
			$pathaddproof = $PROFILE_DIRECTORY_ADDPROOF . $addproof;


			$data = array('id_proof' => $idproof, 'idproof_status' => '1', 'profile_pic' => $profile, 'passbook' => $passbook, 'passbook_status' => '1', 'address_proof' => $addproof, 'addressproof_status' => '1');



			$this->db->where('partner_id', $partner_id);
			$this->db->update('partner', $data);
			$this->response([
				"Status" => "TRUE",
				"Message" => "Documents Uploaded successfully"
			], REST_Controller::HTTP_OK);
			/* if (imagejpeg($addprf, $pathaddproof)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "ID Proof Uploaded successfully"
                    ], REST_Controller::HTTP_OK);
                } 
                else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }*/
		} else {
			$Message = array('Message' => 'Data NOT EXISTS');
			echo json_encode($Message);
		}
	}

	public function addToCart_post()
	{
		$field['user_id'] = $this->post('user_id');
		$field['category_id'] = $this->post('category_id');
		$field['partner_id'] = $this->post('partner_id');
		$field['total_item'] = $this->post('total_item');
		$field['amount'] = $this->post('amount');

		$usernumrow = $this->db->get('cart')->num_rows();
		$user_num_row = $usernumrow + 1;
		$field['cart_id'] = "CART00" . $user_num_row;



		if (!empty($field)) {
			$insert = $this->db->insert('cart', $field);
			if ($insert > 0) {
				$this->response([
					'Status' => "TRUE",
					'Message' => 'Added To Cart',
					'Data' => $field
				], REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'Status' => "FALSE",
					'Message' => 'Some Error Occured! Please Try Again'
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'Status' => "FALSE",
				'Message' => 'Empty Fields Passed'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function showCart_post()
	{
		$user_id = $this->post('user_id');

		$this->db->where('user_id', $user_id);
		$data = $this->db->get('cart')->result_array();


		foreach ($data as $key => $val) {

			$cart['cart_id'] = $val['cart_id'];
			$cart['user_id'] = $val['user_id'];
			$cart['category_id'] = $val['category_id'];
			$cart['partner_id'] = $val['partner_id'];
			$cart['amount'] = $val['amount'];
			$cart['total_item'] = $val['total_item'];
			$this->db->where('categ_id', $val['category_id']);
			$cat = $this->db->get('category')->row_array();
			$cart['category'] = $cat['cate_name'];



			$showCart[$key] = $cart;
		}
		$total_amnt = 0;
		$tot_item = 0;
		foreach ($data as $a => $amnt) {

			$tot_item += $amnt['total_item'];
			$total_amnt += $amnt['amount'];
		}





		if (!empty($data)) {
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'total_amount' => $total_amnt,
				'total_item' => $tot_item,
				'Data' => $showCart
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function deleteCart_post()
	{
		$field['cart_id'] = $this->post('cart_id');
		if (!empty($field)) {
			$this->db->where('cart_id', $this->post('cart_id'));
			$query = $this->db->get('cart');
			$data = $query->num_rows();
			if ($data) {
				$this->db->where('cart_id', $this->post('cart_id'));
				$delete = $this->db->delete('cart');
				$this->response(['Status' => "TRUE", 'Message' => 'Deleted successfully.', 'Data' => $field], REST_Controller::HTTP_OK);
			} else {
				$this->response(['Status' => "FALSE", 'Message' => "Data Not found."], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(['Status' => "FALSE", 'message' => "Provide complete information."], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function partnerOrderDetails_post()
	{
		$partner_id = $this->post('partner_id');

		$data = $this->db->get_where('order_details', array('part_id' => $partner_id))->result_array();

		if (!empty($data)) {

			foreach ($data as $key => $val) {
				$book['order_id'] = $val['order_id'];
				$book['user_id'] = $val['user_id'];
				$book['part_id'] = $val['part_id'];
				$book['cate_id'] = $val['cate_id'];
				$book['address'] = $val['address'];
				$book['from_date'] = $val['from_date'];
				$book['to_date'] = $val['to_date'];
				$book['created_at'] = $val['created_at'];

				$this->db->where('categ_id', $val['cate_id']);
				$cat = $this->db->get('category')->row_array();
				$book['category'] = $cat['cate_name'];

				$booking[$key] = $book;
			}
			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' =>   $booking
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function partnerReview_post()
	{
		$partner_id = $this->post('partner_id');

		$data = $this->db->get_where('review', array('partner_id' => $partner_id))->result_array();

		if (!empty($data)) {


			$this->response([
				'Status' => TRUE,
				'Message' => 'Data Availaible.',
				'Data' =>   $data
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'Status' => FALSE,
				'Message' => 'No Data Found.'
			], REST_Controller::HTTP_NOT_FOUND);
		}
	}
}
