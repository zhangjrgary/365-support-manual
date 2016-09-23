<?php
/*
Template Name: Manual template
*/
?>
<?php get_header(); ?>
	<head>
        <link rel="stylesheet" href="/wp-content/themes/fortuna/res/style.css" >
        <link rel="stylesheet" href="/wp-content/themes/fortuna/res/prism.css" media="screen">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">

        <script src="/wp-content/themes/fortuna/res/jquery.js"></script>
        <script src="/wp-content/themes/fortuna/res/prism.js"></script>
        <script src="/wp-content/themes/fortuna/res/jquery.scrollTo.min.js"></script>
        <script src="/wp-content/themes/fortuna/res/common.js"></script>
		<script type="text/javascript">
            jQuery( document ).ready(function() {
                jQuery(".my_manual").on("click", function(){

                    request($(this).attr("category"));
                    jQuery(".resp-tab-active").removeClass("resp-tab-active");
                    jQuery(this).addClass("resp-tab-active");
                })


            });
			function request(post_id){
				jQuery.ajax({
					type:'post',
					url:'/wp-content/themes/fortuna/transfer.php',
					data:'id=' +post_id,//form data ; url echo
					success:function(data) {
						jQuery("#my_container").html(data);
					}
				});
			}

		</script>
        <style type="text/css">
            .full_container_page_title {
                display:none;
            }
            pre {
                background: white;
                border:none;
            }
        </style>
	</head>



<?php //get_search_form(); ?>

<?php
$con = mysql_connect("localhost:3306","root","1234");
mysql_select_db("pro_website", $con);

$term_id=array();
$term_id_q = mysql_query("select term_id from pro_wp_terms where name=\"support\""); // category
while($term_id_qq=mysql_fetch_array($term_id_q)) {
	array_push($term_id, $term_id_qq['term_id']);			// term_id[0] is the id of support category
}

echo "<div class='content'>";
echo "
		<div class=\"minimal_style resp-vtabs\">          
			<ul class=\"resp-tabs-list\">
";

$parent_id_q=mysql_query("select term_taxonomy_id from pro_wp_term_taxonomy where parent = ".$term_id[0]);
$parent_id=array();
while($parent_id_qq=mysql_fetch_array($parent_id_q)) {
	array_push($parent_id, $parent_id_qq['term_taxonomy_id']);  // parent_id is a array of all parent title id, sub category
}

$arrlength=count($parent_id);
$my123=my_sort($parent_id);
for($x=0;$x<$arrlength;$x++) {
	$temp = mysql_query("select name from pro_wp_terms a inner join pro_wp_term_taxonomy b on a.term_id=b.term_id where a.term_id=" . $my123[$x]);
	$temp_q=mysql_fetch_array($temp);
    $ttt=explode('=+=', $temp_q['name'])[1];
	echo "<li class='parent-tab'>" . $ttt ."</li>";
	$object_id_q=mysql_query("select p.*, tr.* from pro_wp_posts p left join pro_wp_term_relationships tr on tr.object_id = p.ID WHERE term_taxonomy_id =". $my123[$x] . " AND post_type != \"revision\"");
    $post_array=array();
    $lll=0;
	while($row=mysql_fetch_array($object_id_q)) {  // noticed use dot to connect sql and php and JS (print directly vs. generate).
        $num=explode('=+=', $row['post_title']);
        $row['index']=$num[0];
        array_push($post_array, $row);
        //echo "<li class='my_manual' category='".$row['ID']."'>".$row['post_title']."</li>";
	}
    $temp_array=array();
    foreach($post_array as $k => $v){
        $temp_array[$k]=$v['index'];
    }
    array_multisort($temp_array, SORT_ASC, $post_array);
    for ($i=0;$i<count($post_array);$i++) {
        $str=explode('=+=', $post_array[$i]['post_title']);
        echo "<li class='my_manual' category='".$post_array[$i]['ID']."'>".$str[1]."</li>";
    }
}
echo "</ul>";
echo "<div class=\"resp-tabs-container\">";
echo "<div class='container' id='my_container' style='padding-left: 65px;' ></div>";
echo "</div>";

echo "</div></div>";
mysql_close($con);

function my_sort($parent_id) {
    $arrlength=count($parent_id);

// post name and sub-category name use index=+=Name please
    $parent_name=array();
    for($x=0;$x<$arrlength;$x++) {
        $temp = mysql_query("select name from pro_wp_terms a inner join pro_wp_term_taxonomy b on a.term_id=b.term_id where a.term_id=" . $parent_id[$x]);
        while($temp_q = mysql_fetch_array($temp)) {
            $ttt=$temp_q['name'];           // WHY
            array_push($parent_name, $ttt);
        }
    }

    $a_parent_name=array();
    $r_parent_name=array();
    for($x=0;$x<$arrlength;$x++) {
        list($num, $str) = explode('=+=', $parent_name[$x]);
        $a_parent_name[$str] = $num;
    }

    asort($a_parent_name, 0);
    foreach($a_parent_name as $x=>$x_value) {
        array_push($r_parent_name, $x_value."=+=".$x);
    }

    $sub_category_id=array();
    for($x=0;$x<$arrlength;$x++) {
        $temp = mysql_query("select term_id from pro_wp_terms where name ='" . $r_parent_name[$x]."'");
        $temp_q = mysql_fetch_array($temp);
        array_push($sub_category_id, $temp_q['term_id']);
    }
    return $sub_category_id;
}
?>

<?php get_footer(); ?>