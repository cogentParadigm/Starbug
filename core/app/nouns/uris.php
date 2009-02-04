<?php $page=next($this->uri); if (file_exists('core/app/nouns/uris/'.$page.'.php')) include('core/app/nouns/uris/'.$page.'.php');
else include('core/app/nouns/uris/list.php'); ?>