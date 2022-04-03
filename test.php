<?php
$arr = [];
$st = ['a'=>'1', 'b'=>'2', 'c', '3'];
array_push($arr, $st);

//print_r($arr);

function test($arr = 3)
{
	print($arr);
}
test($arr=5);
$a = "marvin, byrd,cool,  is  ,";
$b = explode(',', $a);
$size = count($b);
for ($x = 0; $x <= $size; $x++) {
	$b[$x] = trim($b[$x], $characters = " \n\r\t\v\x00");
}
print_r($b);
?>
