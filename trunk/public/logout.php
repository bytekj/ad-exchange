<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<?php
require_once '../Classes/Session.php';
require_once '../include/global.php';
try {
    ob_start();
    new Session();

    $_SESSION = array();
    ob_flush();
    header('Location: '.PATH);
} catch (Exception $exc) {
    
}

?>
    


