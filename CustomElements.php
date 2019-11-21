<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 21.11.2019
 * Time: 21:31
 */

$states = ["", "in progress", "solved", "pending", "canceled", "refused", "retired"];

function ShowSelectStatus($values, $default, $label, $name)
{
    echo "<li><label for=\"$name\">$label:</label><select name=\"$name\" size=\"1\">";
    foreach ($values as $value)
    {
        if($value == $default)
            echo "<option value=\"$value\" selected>$value";
        else
            echo "<option value=\"$value\">$value";

    }
    echo "</select></li>";
}