<?php
$con = mysql_connect("localhost:3306","root","1234");
mysql_select_db("pro_website", $con);
$post_content_q=mysql_query("select post_content from pro_wp_posts where ID = ".$_POST['id']);
$post_content_qq=mysql_fetch_array($post_content_q);
echo htmlspecialchars_decode($post_content_qq['post_content'],ENT_HTML5);
?>