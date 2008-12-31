<?php $page=next($this->uri); if (file_exists('core/app/elements/users/'.$page.'.php')) include('core/app/elements/users/'.$page.'.php');
else include('core/app/elements/users/list.php'); ?>