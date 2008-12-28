<?php $page=next($this->uri); if (file_exists('core/app/elements/elements/'.$page.'.php')) include('core/app/elements/elements/'.$page.'.php');
else include('core/app/elements/elements/list.php'); ?>