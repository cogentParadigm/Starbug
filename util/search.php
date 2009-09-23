<?php
$sb->provide("util/search");
function keywordClause($text, $fields) {
	$text = strtolower(trim(str_replace("\\\"","&quot;",$text)));
	//tokenize the text
	$output = array();
	$output2 = array();
	$arr = explode("&quot;",$text);
	for ($i=0;$i<count($arr);$i++){
		if ($i%2==0) $output=array_merge($output,explode(" ",$arr[$i]));
		else $output[] = $arr[$i];
	}
	foreach($output as $token) if (trim($token)!="") $words[]=$token;
	//generate condition string
	$conditions = "(";
	for($word=0;$word<count($words);$word++) {
		$w = $words[$word];
		if ($w!="") {
			if ($w!="and" && $w!="or") {
				$conditions .= "(";
				for($field=0;$field<count($fields);$field++) {
					$conditions .= $fields[$field]." LIKE '%".$w."%'";
					if ($field<(count($fields)-1)) {
						$conditions .= " OR ";
					} else {
						$conditions .= ")";
					}
				}
				if ($word<(count($words)-1)) {
					if ($words[$word+1]=="and" || $words[$word+1]=="or") {
						$conditions .= " ".$words[$word+1]." ";
					} else {
						$conditions .= " AND ";
					}
				}
			}
		}
	}
	$conditions .= ")";
	return $conditions;
}
?>
