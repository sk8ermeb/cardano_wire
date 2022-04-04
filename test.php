<?php
$arr = [];
$st = ['a'=>'1', 'b'=>'2', 'c', '3'];
array_push($arr, $st);

//print_r($arr);

function test($a=1, $b=2, $c=3)
{
	print("a=$a b=$b c=$c");
}
//test($arr=5);
$a = "marvin, byrd,cool,  is, ";
$b = explode(',', $a);
$size = count($b);
$tago = [];
for ($x = 0; $x < $size; $x++) {
	$tag  = trim($b[$x], $characters = " \n\r\t\v\x00");
	if(strlen($tag) > 0){
		array_push($tago, $tag);
	}
}
//print_r($tago);

$mintdate = date('Y-m-d H:i:s');
$dt = new DateTime($mintdate);
//print("==== $mintdate " );
//print($dt);
//$var = new DateTime("2010-05-15 16:00:00");
print(("asdd"!=="asdd"));

?>
