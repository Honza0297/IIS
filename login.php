<?php
include_once "CustomElements.php";
include_once "php/Database.php";
include_once "php/model/Person.php";
include_once "php/model/Product.php";
include_once "php/model/Task.php";
include_once "php/model/Ticket.php";
include_once "php/model/Attachment.php";
include_once "php/model/Comment.php";
include_once "CustomElements.php";
setSession();
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="robots" content="noindex; nofollow">
    <link type="text/css" rel="stylesheet" href="style.css">
    <title>ITS</title>
    <link rel="icon" href="BigDuckBugBlack.png">
    <!--<link rel="icon" href="imgs/icon.png">-->

</head>
<body>
    <header>
        <ul>
                <?php
                ShowHeader();
                ?>
        </ul>
    </header>
    <br>
        <div class="main">      
<?php
function loginRedirect(){
    $page = rtrim($_GET["page"],'?');
    redirect($page);
    exit();
}
// Check if the user is already logged in, if yes then redirect him to welcome page
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

                    // Store data in session variables
                    $_SESSION['expire'] = time() + $logoutSeconds;
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $user->id;   
                    $_SESSION["role"] = $user->role;                    
                        
                    // Redirect user to welcome page
                    loginRedirect();
                }
            }
        }
    }
    echo "Log in wasn't succesful, please log in";
?>
</div>
</body>

<?php 
    echo "<div id=\"id01\" class=\"modal\">";
        echo "<form class=\"modal-content animate\" action=\"login.php?page=".$_GET["page"]."\"method=\"post\">";
            echo "<div class=\"container\">";
                echo "<label for=\"uname\"><b>Username</b></label>";
                echo "<input type=\"text\" class=\"UsPa\" placeholder=\"Enter Username\" name=\"uname\" required>";
                echo "<label for=\"psw\"><b>Password</b></label>";
                echo "<input type=\"password\" class=\"UsPa\" placeholder=\"Enter Password\" name=\"psw\" required>";

                echo "<button type=\"submit\">Login</button>";
            echo "</div>";

            echo "<div class=\"container\" style=\"background-color:#f1f1f1\">";
                echo "<button type=\"button\" onclick=\"document.getElementById('id01').style.display='none'\" class=\"cancelbtn\">Cancel</button>";
            echo "</div>";
        echo "</form>";
    echo "</div>";

    echo "<script>";
        // Get the modal
        echo "var modal = document.getElementById('id01');";

        // When the user clicks anywhere outside of the modal, close it
        echo "window.onclick = function (event) {";
            echo "if (event.target == modal) {";
                echo "modal.style.display = \"none\";";
            echo "}}";
    echo "</script>";
?>

</html>
