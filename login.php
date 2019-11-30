<?php
// Initialize the session
include_once "php/Database.php";
include_once "php/model/Person.php";
include_once "php/model/Product.php";
include_once "php/model/Task.php";
include_once "php/model/Ticket.php";
include_once "php/model/Attachment.php";
include_once "php/model/Comment.php";
include_once "CustomElements.php";


setSession();
// Check if the user is already logged in, if yes then redirect him to welcome page
function loginRedirect(){
    header("Location: ".$_GET["page"]);
    exit();
}
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    loginRedirect();
}
 
// Define variables and initialize with empty values

$db = new Database();
$db->getConnection();

$person = new \model\Person($db->connection);
$password = "";
$username_err = $password_err = "";
 
 // Check if username is empty
    if(empty(trim($_POST["uname"]))){
        $username_err = "Please enter your username.";
    } else{
        $person->username = trim($_POST["uname"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["psw"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["psw"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $users= $person->findInDb();
        foreach ($users as $user) {
            if ($user->username==$person->username){
                if (password_verify($password,$user->password)){
                    // Password is correct, so start a new session
                    session_start();
                    $_SESSION['expire'] = time() + $logoutSeconds;

                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $user->id;   
                    $_SESSION["role"] = $user->role;                    
                        
                    // Redirect user to welcome page
                    loginRedirect();
                }
                loginRedirect();
            }
        }
        loginRedirect();
    }
    loginRedirect();
?>
