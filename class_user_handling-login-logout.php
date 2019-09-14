<?
/* -----------------------------------------------------------------------------------------
PHP class "User Handling"
Logging in, Logging out, Session Handling and Basic Operations

The class at hand has the followig purposes:
- Provide basic user handling functionality for developing and testing web applications
- Provide functionality for starting and ending sessions based on username and password
- Provide functionality for basic profile operations - such as getUsername, getFullName 

The class can be used by adding a require command after session_start() in file head.

Please note that this class/ code is not meant to be used in production!
It has not been, by any means, hardend for using it in production systems.
It was written in order to have a very quick and simple solution for dev.
----------------------------------------------------------------------------------------- */


// -----------------------------------------------------------------------------------------
// Definition of the class "user"
// -----------------------------------------------------------------------------------------
final class user
{
	// Basic user variables
	private $username;									// String; used for login credentials
	private $password;									// String; used for login credentials
	private $user_token;								// String; used for internal handling
	
	// Additional user information
	private $user_info;									// Array; can contain various information
	private $firstname;									// String; can be used for displaying user information
	private $surname;										// String; can be used for displaying user information
	
	// Directory to the user's picture
	private $profile_pic_directory = "asset/user/profile_pic/";
	private $profile_pic;								// String; will be used for dirname and filename
	
	// Constructor method
	public function __construct($username, $password, $profile_pic, $user_info)
	{
		$this->username				= $username;
		$this->password				= $password;
		
		$this->user_token			=	$user_info['user_token'];
		$this->firstname			= $user_info['firstname'];
		$this->surname				= $user_info['surname'];
		$this->profile_pic		= $profile_pic_directory.$profile_pic;
	}
		
	// Get current Username from user object
	function getUsername()
	{
		return $this->username;
	}
	
	// Set Username to new value
	function setUsername($username)
	{
		$this->username = $username;
	}
	
	// Get current Password from user object
	function getPassword()
	{
		return $this->password;
	}
	
	// Set Password to new value
	function setPassword($password)
	{
		$this->password = $password;
	}
	
	// Get current UserToken
	function getUserToken()
	{
		return $this->user_token;
	}
	
	// Get the user's full name by combining firstname and surname
	function getFullName()
	{
		return $this->firstname." ".$this->surname;
	}
	
	// Get the path to the user's profile picture
	function getProfilePic()
	{
		return $this->profile_pic;
	}
	
	// Set new path to the user's profile picture
	function setProfilePic($profile_pic)
	{
		$this->profile_pic = $profile_pic_directory.$profile_pic;
	}
}
// -----------------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------------
// Test Users: Create new user objects
// -----------------------------------------------------------------------------------------
$user    = array();
$user[0] = new user("john@doe.com", password_hash("mySecretPassword", PASSWORD_BCRYPT), "user_profile_pic_XYZ.png", ["firstname" => "John", "surname" => "Doe", "user_token" => "k4Vm9La24zUC"]);
$user[1] = new user("lucy@whatever.com", password_hash("mySuperSecretPW", PASSWORD_BCRYPT), "user_profile_pic_ABC.png", ["firstname" => "Lucy", "surname" => "Smith", "user_token" => "DP3xFe9bf2Sj"]);
// -----------------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------------
// Handling logic: Check credentials and handle data comming from POST method
// -----------------------------------------------------------------------------------------
if (
		(!isset($_SESSION['user_logged_in']) OR $_SESSION['user_logged_in'] == 0)
		AND (isset($_POST['username']) AND isset($_POST['password']))
		AND (!empty($_POST['username']) AND !empty($_POST['password']))
		AND strpos($_POST['username'], "@") != FALSE
		)
{
	// ---------------------------------------------------------------------------------------
	// Walk through test user array and check each user against the data submitted by POST
	// ---------------------------------------------------------------------------------------
	for ($i=0; $i < count($user); $i++)
	{
		// -------------------------------------------------------------------------------------
		// Check combination of username and password and create or destroy SESSION data
		// -------------------------------------------------------------------------------------
		if (strcmp($_POST['username'], $user[$i]->getUsername()) == 0)
		{
			if (password_verify($_POST['password'], $user[$i]->getPassword()))
			{
				$_SESSION['user_logged_in']				= 1;
				$_SESSION['username']							= $user[$i]->getUsername();
				$_SESSION['fullname']							= $user[$i]->getFullName();
				$_SESSION['user_token']						= $user[$i]->getUserToken();
				$_SESSION['profile_pic']					= $user[$i]->getProfilePic();
			}
			else
			{
				$_POST = array();
				session_start();
				session_destroy();
			}	
		}
		// -------------------------------------------------------------------------------------
	}
	// ---------------------------------------------------------------------------------------
}
elseif (isset($_SESSION['user_logged_in']) AND $_SESSION['user_logged_in'] == 1 AND (strcmp($_POST['Logout'], "Logout") == 0))
{
	unset($_POST);
	unset($_SESSION["user_logged_in"]);
	session_destroy();
}
// -----------------------------------------------------------------------------------------

?>