<?php
/**
* FILE: script/_generate/crud.php
* PURPOSE: generates crud
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
$base = dirname(__FILE__)."/../../app/nouns/";

// 0.) GENERATE URI GATE AND FOLDER
$gateway = "<?php \$page=next(\$this->uri); if (file_exists(\"app/nouns/$argv[2]/\$page.php\")) include(\"app/nouns/$argv[2]/\$page.php\");
else include(\"app/nouns/$argv[2]/list.php\"); ?>";
$file = fopen($base.$argv[2].".php", "wb");
fwrite($file, $gateway);
fclose($file);
if (!file_exists($base.$argv[2])) mkdir($base.$argv[2]);

// 1.) GENERATE FORM
include(dirname(__FILE__)."/form.php");
$label_column = $args->flag('l');

// 2.) GENERATE CREATE
$create = "<h2>Create $argv[2]</h2>\n<p>Create a new $argv[2]</p>\n<?php \$action = \"create\"; \$submit_to = uri(\"$argv[2]/show\"); include(\"app/nouns/$argv[2]/$argv[2]_form.php\"); ?>";
$file = fopen($base.$argv[2]."/create.php", "wb");
fwrite($file, $create);
fclose($file);

// 3.) GENERATE SHOW
$show = "<?php \$id = next(\$this->uri);\n";
$show .= "\tif (!empty(\$this->errors['$argv[2]'])) include(\"app/nouns/$argv[2]/\".((\$id)?\"update\":\"create\").\".php\");\n";
$show .= "\telse {\n";
$show .= "\t\tif (!\$id) \$entry = \$this->get(\"$argv[2]\")->find(\"*\", \"\", \"LIMIT 1\")->fields();\n";
$show .= "\t\telse \$entry = \$this->get(\"$argv[2]\")->find(\"*\", \"id='\".\$id.\"'\")->fields();\n?>\n";
$show .= "<h2><?php echo \$entry['$label_column']; ?></h2>\n<dl>\n";
foreach ($fields as $k => $v) if ($k != $label_column) $show .= "\t<dt>".ucwords($k)."</dt><dd><?php echo \$entry['$k']; ?></dd>\n";
$show .= "\t<dt>Options</dt>\n\t<dd>\n";
$show .= "\t\t<a class=\"button\" href=\"<?php echo uri(\"$argv[2]/update/\$entry[id]\"); ?>\" style=\"float:left\">Edit</a>\n";
$show .= "\t\t<form id=\"del_form\" action=\"<?php echo uri(\"$argv[2]/list\"); ?>\" method=\"post\">\n";
$show .= "\t\t\t<input name=\"action[$argv[2]]\" type=\"hidden\" value=\"delete\"/>";
$show .= "\t\t\t<input type=\"hidden\" name=\"$argv[2]"."[id]\" value=\"<?php echo \$entry['id']; ?>\"/>\n";
$show .= "\t\t\t<input class=\"button\" type=\"submit\" onclick=\"return confirm('Are you sure you want to delete?')\" value=\"Delete\"/>\n";
$show .= "\t\t</form>\n\t</dd>\n</dl>\n<?php } ?>\n";
$file = fopen($base.$argv[2]."/show.php", "wb");
fwrite($file, $show);
fclose($file);

// 4.) GENERATE UPDATE
$update = "<?php \$id = next(\$this->uri); \$_POST['$argv[2]'] = \$this->get(\"$argv[2]\")->find(\"*\", \"id='\$id'\")->fields(); ?>\n";
$update .= "<h2>Update $argv[2]</h2>";
$update .= "<?php \$formid = \"edit_$argv[2]_form\"; \$action = \"create\"; \$submit_to = uri(\"$arv[2]/show/\").\$id; include(\"app/nouns/$argv[2]/$argv[2]_form.php\"); ?>\n";
$file = fopen($base.$argv[2]."/update.php", "wb");
fwrite($file, $update);
fclose($file);

// 5.) GENERATE LIST
$list = "<?php\n\$$argv[2] = \$this->get(\"$argv[2]\");\n\$page = next(\$this->uri);\nempty_nan(\$page, 0);\n\$all = \$$argv[2]->afind(\"*\");\n\$total = \$$argv[2]->recordCount;\n\$list = \$$argv[2]->afind(\"*\", \"\", \"ORDER BY id DESC LIMIT \".(\$page*25).\", 25\");\n\$shown = \$$argv[2]->recordCount;\n?>\n";
$list .= "<script type=\"text/javascript\">\n";
$list .= "\tfunction showhide(item) {\n";
$list .= "\t\tvar node = dojo.byId(item);\n";
$list .= "\t\tvar display = node.getAttribute('class');\n";
$list .= "\t\tif (display == 'hidden') display = '';\n";
$list .= "\t\telse display = 'hidden';\n";
$list .= "\t\tnode.setAttribute('class', display);\n";
$list .= "\t}\n</script>\n<?php include(\"public/js/$argv[2].php\"); ?>\n";
$list .= "<h2>$argv[2] list</h2>\n";
$list .= "<?php if (\$total > 25) { ?>\n";
$list .= "<ul class=\"pages\">\n";
$list .= "\t<?php if (\$page > 0) { ?>\n";
$list .= "\t<li class=\"back\"><a href=\"$argv[2]/list/<?php echo \$page-1; ?>\">Back</a></li>\n";
$list .= "\t<?php } for(\$i=0;\$i<ceil(\$total/25);\$i++) { ?>\n";
$list .= "\t<li><a<?php if(\$page == \$i) { ?> class=\"active\"<?php } ?> href=\"$argv[2]/list/<?php echo \$i; ?>\"><?php echo \$i+1; ?></a></li>\n";
$list .= "\t<?php } if(\$page < ceil(\$total/25)-1) { ?>\n";
$list .= "\t<li class=\"next\"><a href=\"$argv[2]/list/<?php echo \$page+1; ?>\">Next</a></li>\n";
$list .= "\t<?php } ?>\n";
$list .= "</ul>\n<?php } ?>\n<ul id=\"$argv[2]_list\" class=\"lidls\">\n";
$list .= "<?php foreach(\$list as \$entry) { ?>\n";
$list .= "\t<li id =\"$argv[2]_<?php echo \$entry['id']; ?>\">\n";
$list .= "\t\t<h3>\n";
$list .= "\t\t\t<form id=\"del_form\" action=\"<?php echo htmlentities(\$_SERVER['REQUEST_URI']); ?>\" method=\"post\">\n";
$list .= "\t\t\t\t<input id=\"action[$argv[2]]\" name=\"action[$argv[2]]\" type=\"hidden\" value=\"delete\"/>\n";
$list .= "\t\t\t\t<input type=\"hidden\" name=\"$argv[2]"."[id]\" value=\"<?php echo \$entry['id']; ?>\"/>\n";
$list .= "\t\t\t\t<input class=\"button\" type=\"submit\" onclick=\"return confirm('Are you sure you want to delete?');\" value=\"[X]\"/>\n";
$list .= "\t\t\t</form>\n";
$list .= "\t\t\t<a href=\"<?php echo uri(\"$argv[2]/update/\$entry[id]\"); ?>\">[edit]</a>\n";
$list .= "\t\t\t<a class=\"title\" href=\"#\" onclick=\"showhide('$argv[2]_<?php echo \$entry['id']; ?>_list');return false;\"><?php echo \$entry['$label_column']; ?></a>\n";
$list .= "\t\t</h3>\n";
$list .= "\t\t<dl id=\"$argv[2]_<?php echo \$entry['id']; ?>_list\" style=\"padding:5px\" class=\"hidden\">\n";
foreach ($fields as $k => $v) if ($k != $label_column) $list .= "\t\t\t<dt>".ucwords($k)."</dt><dd><?php echo \$entry['$k']; ?></dd>\n";
$list .= "\t\t</dl>\n\t</li>\n<?php } ?>\n</ul>\n";
$list .= "<a id=\"add_$argv[2]\" class=\"button\" href=\"<?php echo uri(\"$argv[2]/create\"); ?>\" onclick=\"new_$argv[2]();return false;\">new $argv[2]</a>\n";
$file = fopen($base.$argv[2]."/list.php", "wb");
fwrite($file, $list);
fclose($file);

// 6.) INSERT URI
if (!$args->flag('u')) {
	include(dirname(__FILE__)."/../../etc/init.php");
	include(dirname(__FILE__)."/../../core/db/Schemer.php");
	$schemer = new Schemer($db);
	$template = ($args->flag('t')) ? $args->flag('t') : Etc::DEFAULT_TEMPLATE;
	$access = ($args->flag('a')) ? $args->flag('a') : Etc::DEFAULT_SECURITY;
	$schemer->insert("uris", "path, template, security", "'$argv[2]', '$template', '$access'");
}
