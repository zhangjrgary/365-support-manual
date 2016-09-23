<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
        function request(post_id){
            $.ajax({
                type:'post',
                url:'transfer.php',
                data:'id=' +post_id,//form data ; url echo
                // this means the return result of this function
                success:function(data) {
                    $("#my_container").html(data);
                }
            });
        }
    </script>
</head>

<body>
    <div id="my_container">
        <h1>
            Home!
        </h1>
    </div>
</body>

<?php
$con = mysql_connect("localhost:3306","root","1234");
mysql_select_db("pro_website", $con);

$term_id_q = mysql_query("select term_id from pro_wp_terms where name=\"support\"");
while($term_id_qq=mysql_fetch_array($term_id_q)) {
    $term_id=$term_id_qq['term_id'];
}

echo "<table border='1'>
<tr>
<th>Manual</th>
</tr>";

$parent_id_q=mysql_query("select term_taxonomy_id from pro_wp_term_taxonomy where parent = ".$term_id);
$parent_id=array();
while($parent_id_qq=mysql_fetch_array($parent_id_q)) {
    array_push($parent_id, $parent_id_qq['term_taxonomy_id']);  // parent_id is a array of all parent title id, notice how to insert to array in php
}

$arrlength=count($parent_id);
$parent_str="";
for($x=0;$x<$arrlength;$x++) {
    $parent_str .= ($parent_id[$x]);
}

// sort parent_id

$object_id_q=mysql_query("select p.*, tr.* from pro_wp_posts p left join pro_wp_term_relationships tr on tr.object_id = p.ID WHERE term_taxonomy_id in (".$parent_str.") AND post_type != \"revision\"");
for($x=0;$x<$arrlength;$x++) {
    echo "<tr>";
    $temp = mysql_query("select taxonomy from pro_wp_term_taxonomy where term_taxonomy_id = " . $parent_id[$x]);
    $temp_q=mysql_fetch_array($temp);

    echo "<td>" . $temp_q['taxonomy'] . "</td>";
    echo "</tr>";
    echo "<tr>";

    while($row=mysql_fetch_array($object_id_q)) {
        if($row['term_taxonomy_id']==$parent_id[$x]) {
            echo "<tr>";
            echo "<td> <a href='#' onclick='request(".$row['ID'].")'>".$row['post_title']."</a> </td>";  // noticed use dot to connect sql and php and JS (print directly vs. generate).
            echo "</tr>";
        }
    }
}

echo "</table>";

mysql_close($con);
?>

