<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 21.11.2019
 * Time: 21:31
 */

$states = array("", "in progress", "solved", "pending", "canceled", "refused", "retired");
$statesNoEmpty = array("in progress", "solved", "pending", "canceled", "refused", "retired");
$roles = array("", "customer", "worker", "manager", "senior manager", "admin");
$rolesNoEmpty = array("customer", "worker", "manager", "senior manager", "admin");
$taskStatesNoEmpty = array("pending", "in progress", "solved", "cancelled", "refused");

$logoutSeconds = 20;

/**
 * @param $values array of values (IDs etc) of showed items (LENGTH MUST BE THE SAME AS SHOWEDLABELS)
 * @param $showedLabels array of strings of showed items
 * @param $default string - value of item which is supposed to be default
 * @param $label string - label for the select field
 * @param $name string - name of the select element
 */
function ShowSelectElement($values, $showedLabels, $default, $label, $name)
{
    echo "<label for=\"$name\">$label:</label><select name=\"$name\" size=\"1\">";
    for ($i = 0; $i < count($values); $i++)
    {
        $value = $values[$i];
        $showedLabel = $showedLabels[$i];
        if($value == $default)
            echo "<option value=\"$value\" selected>$showedLabel";
        else
            echo "<option value=\"$value\">$showedLabel";
    }
    echo "</select>";
}

function redirect($url)
{
   $string = '<script type="text/javascript">';
   $string .= 'window.location = "' . $url . '"';
   $string .= '</script>';
 
  echo $string;
}                 


function ShowHeader()
{
    echo "<nav>";
    echo "<a href=\"index.php\"><img src=\"BigDuckBugBlack.png\" alt=\"LOGO\" class=\"logo\"></a>";
    echo "<a href=\"search.php\"><li>Search ticket</li></a>";
    echo "<a href=\"searchproduct.php\"><li class=\"searchproduct\" >Search product</li></a>";
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        echo "<a href=\"ticket.php?action=new\"><li>Create ticket</li></a>";
        if ($_SESSION["role"] == "admin") {
            echo "<a href=\"profile.php?action=new\"><li class=\"register\">Add new user</li></a>";
            echo "<a href=\"searchuser.php\"><li class=\"searchuser\" >Search user</li></a>";
        }
        if ($_SESSION["role"] == "senior manager" || $_SESSION["role"] == "admin") {
            echo "<a href=\"product.php?action=new\"><li class=\"newproduct\">New product</li></a>";
        }

        if ($_SESSION["role"]!="customer"){
                echo "<a href=\"tasks.php\"><li class=\"logout\">All tasks</li></a>";          
            if ($_SESSION["role"]!= "admin"){
                echo "<a href=\"tasks.php?assignee="; echo ($_SESSION["id"]);echo "\"><li class=\"logout\">My tasks</li></a>";
            }
        }
        echo "</nav>";
        echo "<personal>";
        $id = $_SESSION["id"];
        echo "<a href=\"profile.php?id=$id\"><li class=\"profile\" >Profile</li></a>";
        echo "<a href=\"logout.php?page=index.php\"><li class=\"logout\">Log out</li></a>";
    } else {
        echo "</nav>";
        echo "<personal>";
        echo "<li class=\"login\" onclick=\"document.getElementById('id01').style.display='block'\">Log in</li>";
        echo "<a href=\"profile.php?action=new\"><li class=\"register\">Register</li></a>";
    }
    echo " </personal> ";
}

function RemoveAlreadyAssigned($all,$assigned){
    $return = array();
    $i=0;
    foreach ($all as $person){
        if (!in_array($person, $assigned)){
            $return[$i]= $person;
            $i++;
        }
    }
    return $return;
}

/**
 * returns prepared arrays for passing to ShowSelectElement
 * @param Database $db
 * @param $noParentProductChoice bool true if you want to generate a "no parent product" to the list
 * @return array
 */
function prepareProducts(Database $db, $noParentProductChoice)
{
    $allProducts = \model\Product::getAll($db->connection);
    $productIDs = array();
    $productLabels = array();
    if (!empty($allProducts))
    {
        if($noParentProductChoice)
        {
            array_push($productIDs, "");
            array_push($productLabels, "no parent product");
        }
        foreach ($allProducts as $product) {
            array_push($productIDs, $product->id);
            array_push($productLabels, $product->name);
        }
    }
    return array($productIDs, $productLabels);
}

/**
 * returns prepared arrays for passing to ShowSelectElement
 * @param Database $db
 * @return array
 */
function prepareManagers(Database $db)
{
    $searchPerson = new \model\Person($db->connection);
    $searchPerson->role = "manager";
    $allManagers = $searchPerson->findInDb();
    $managerIDs = array();
    $managerLabels = array();
    if (!empty($allManagers)) {
        foreach ($allManagers as $manager) {
            array_push($managerIDs, $manager->id);
            array_push($managerLabels, $manager->username . ": " . $manager->name . " " . $manager->surname);
        }
    }
    return array($managerIDs, $managerLabels);
}

/**
 * @param $tik
 */
function print_ticket_simple($tik)
{
    echo "<a href=\"ticket.php?id=$tik->id\">";
    echo "<div class=\"ticket\">";
    echo "<ul>";
    echo "<label class='title'>$tik->title [#$tik->id]</label> <br><br>";
    echo "<label class='info'>$tik->info</label><br>";
    echo "</ul>";
    echo "<hr class='line'>";
    echo "</div>";
    echo "</a>";
}

/**
 * @param $pro
 */
function print_product_basic($pro)
{
    if (!$pro->loadModels()) {
        echo "nepodarilo se nacist modely...";
    }

    echo "<a href=\"product.php?id=$pro->id\">";

    echo "<div class=\"product\">";
    echo "<ul>";
    echo "<label class='title'>$pro->name [#$pro->id]</label><br>";
    echo "<label class='info'>$pro->description </label><br>";
    $pro->loadModels();
    $name = $pro->manager->username;
    echo "<label class='info'>Manager: $name </label><br>";
    echo "<hr class=\"line\">";
    echo "</ul>";
    echo "</div>";
    if ($_SESSION["id"] == "admin" || $_SESSION["role"] == "senior manager") {
        echo "</a>";
    }
}

/**
 * @param $per
 */
function print_user_basic($per)
{
    if ($_SESSION["role"] == "admin") {
        echo "<a href=\"profile.php?id=$per->id\">";
    }
    echo "<div class=\"person\">";
    echo "<ul>";
    echo "<label class='title'>$per->name $per->surname</label><br>";
    echo"<br>";
    echo "<label class='info'> ID: $per->id </label><br>";
    echo "<label class='info'> Role: $per->role </label><br>";
    echo "<label class='info'> Username: $per->username </label> <br>";
    echo "</ul>";
    echo "</div>";
    if ($_SESSION["id"] == "admin") {
        echo "</a>";
    }
    echo "<hr class=\"line\">";
}

/**
 * @param $task
 */
function print_task($task)
{
    echo "<a href=\"task.php?id=$task->id\">";
    echo "<div class=\"task\">";
    echo "<ul>";
    $task->loadModels();
    $temp = $task->ticket;
    $temp->loadModels();
    $temp = $temp->product->name;
    echo "<label class='info'><b>Title:</b> $task->title </label> <br>";
    echo"<label class='info'><b>Status: </b> $task->state </label><br>";
    echo"<label class='info'><b>Product:</b> $temp</label><br>";

    echo"<br>";
    echo "<label class='info'> $task->description</label><br>";
    echo"<hr>";
    echo "</ul>";
    echo "</div>";
    echo "</a>";
}

function logindiv(){
    //taken from https://www.w3schools.com/howto/howto_css_login_form.asp
    echo "<div id=\"id01\" class=\"modal\">";

        echo "<form class=\"modal-content animate\" action=\"login.php?page=".$_SERVER["REQUEST_URI"]."?".$_SERVER["QUERY_STRING"]."\"method=\"post\">";
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
}

function setSession()
{
    $logoutSeconds = 20;

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if(!isset($_SESSION['expire']))
        return;

    if(time() > $_SESSION['expire'])
    {
        session_destroy();
        session_write_close();
        session_unset();
        $_SESSION = array();
        session_start();
    }
    else
    {
        $_SESSION['expire'] = time()+ $logoutSeconds;
    }
}