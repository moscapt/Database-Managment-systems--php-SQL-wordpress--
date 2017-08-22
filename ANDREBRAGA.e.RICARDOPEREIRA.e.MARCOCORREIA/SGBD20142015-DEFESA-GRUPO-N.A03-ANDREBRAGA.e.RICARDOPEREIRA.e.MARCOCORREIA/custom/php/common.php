<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/anytime.js"></script>

<?php

	//Function to Return All Possible ENUM Values for a Field
	function getEnumValues($table, $field)
	{
	$enum_array = array();
	$query = 'SHOW COLUMNS FROM `' . $table . '` LIKE "' . $field . '"';
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	preg_match_all('/\'(.*?)\'/', $row[1], $enum_array);
	if(!empty($enum_array[1]))
	{
		//Shift array keys to match original enumerated index in MySQL (allows for use of index values instead of strings)
		foreach($enum_array[1] as $mkey => $mval) $enum_fields[$mkey+1] = $mval;
		return $enum_fields;
	}
	else
		return array(); // Return an empty array to avoid possible errors/warnings if array is passed to foreach() without first being checked with !empty().
	}
	
	
	
	function back()
	{	
		echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
		<noscript>
		<a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
		</noscript>";
	}	
?>
	
	
