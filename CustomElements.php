<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 21.11.2019
 * Time: 21:31
 */

$states = array("", "in progress", "solved", "pending", "canceled", "refused", "retired");
$statesNoEmpty = array("in progress", "solved", "pending", "canceled", "refused", "retired");

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

function ShowHeader()
{
    echo "<nav>";
    echo "<a href=\"index.php\"><img src=\"BigDuckBugYellow.png\" alt=\"LOGO\" class=\"logo\"></a>";
    echo "<a href=\"search.php\"><li>Search</li></a>";
    session_start();
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        echo "<a href=\"ticket.php?action=new\"><li>Create ticket</li></a>";
        if ($_SESSION["role"] == "admin") {
            echo "<a href=\"profile.php?action=new\"><li class=\"register\">Add new user</li></a>";
            echo "<a href=\"searchuser.php\"><li class=\"searchuser\" >Search user</li></a>";
        }
        if($_SESSION["role"] == "senior manager" || $_SESSION["role"] == "admin")
        {
            echo "<a href=\"product.php?action=new\"><li class=\"newproduct\">New product</li></a>";
            echo "<a href=\"searchproduct.php\"><li class=\"searchproduct\" >Search product</li></a>";
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