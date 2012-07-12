<?php
import("pager");
efault($args['page'], $_GET['page']);
//get count
$sql = "SELECT COUNT(*) FROM $args[from]";
if (!empty($args['where'])) $sql .= " WHERE $args[where]";
$res = $this->pdo->prepare($sql);
$res->execute($replacements);
$count = $res->fetchColumn();
$pager = new pager($count, $args['limit'], $args['page']);
$args['limit'] = $pager->start.', '.$args['limit'];
?>
