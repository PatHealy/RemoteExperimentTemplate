<?php
	include('library.php');
	$db = new Database();
	$db->connect();
	echo $db->getnumberfinishedsubjects();
	// This page exists for researchers to quickly and easily check how many subjects have completely finished all research activities. This page is completely public.
?>