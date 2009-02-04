<?php $page=next($this->uri); if (file_exists('core/app/nouns/users/'.$page.'.php')) include('core/app/nouns/users/'.$page.'.php');
else include('core/app/nouns/users/list.php'); ?>