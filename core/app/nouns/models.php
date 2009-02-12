<?php $page=next($this->uri); if (file_exists('core/app/nouns/models/'.$page.'.php')) include('core/app/nouns/models/'.$page.'.php');
else include('core/app/nouns/models/list.php'); ?>