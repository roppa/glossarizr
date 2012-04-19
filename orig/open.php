<html>
<body>

<?php

	$string = "";

	$file = fopen("txt.txt", "r") or exit("Unable to open file!");
	//Output a line of the file until the end is reached
	while(!feof($file))
	{
	  $string = $string . fgets($file);
	}
	fclose($file);

	$string = trim(strtolower($string));

	$new_string = preg_replace("[^A-Za-z0-9/']", " ", $string);

	$stringArray = explode(" ", $new_string);

	$newArray = array_unique($stringArray);
	asort($newArray);

	foreach($newArray as $key => $value)
	{
	  if($value == "" || is_null($value))
	  {
		unset($newArray[$key]);
	  }

	}

	$lastarray = array_values($newArray);

	for($i = 0;$i < sizeof($lastarray); $i++)
	{
	   echo $lastarray[$i] . "<br/>";
	}


?>

</body>
</html>