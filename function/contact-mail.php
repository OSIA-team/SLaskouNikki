<?php
/**
 * Created by PhpStorm.
 * User: krystofkosut
 * Date: 16.07.18
 * Time: 14:59
 */

if (isset($_POST['contactMe'])){
    $mail       = $_POST['email'];
    $name       = $_POST['jmeno'];
    $phone      = $_POST['phone'];
    $content    = $_POST['message'];

    $content = "Od: $name \n
                $mail \n
                $phone \r\n
                $content
    ";

    mail('info@slaskouniki.cz', 'Dotaz z webu', $content);
    mail('Nicol.Villimova@seznam.cz', 'Dotaz z webu', $content);
}