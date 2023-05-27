h<?php
session_start();
if(session_destroy())
{
	unset($_COOKIE['member_login']);
    unset($_COOKIE['member_password']);
    unset($_COOKIE['member_id']);
    setcookie('member_id', null, -1, '/');
    setcookie('member_password', null, -1, '/');
    setcookie('member_login', null, -1, '/');

	header("Location: index.php");
}
?>