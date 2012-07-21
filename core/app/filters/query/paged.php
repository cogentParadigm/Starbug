<?php
import("pager");
efault($args['page'], $_GET['page']);
//get count
$sql = "SELECT COUNT(".((false !== strpos(strtolower($args['select']), 'distinct')) ? $args['select'] : "*").") FROM $args[from]";
if (!empty($args['where'])) $sql .= " WHERE $args[where]";
$res = $this->pdo->prepare($sql);
$res->execute($replacements);
$count = $res->fetchColumn();
efault($args['limit'], $_GET['show']);
if ($args['limit'] == "all") $args['limit'] = $count;
request()->pager = new pager($count, $args['limit'], $args['page']);
$args['limit'] = request()->pager->start.', '.$args['limit'];
?>
