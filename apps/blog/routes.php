<?php

$routes[] = route('^$', 'Blog_Actions_Articles_ListAction');

$routes[] = route('^articles/(?P<id>\d+)$', 'Blog_Actions_Articles_ViewAction');

$routes[] = route('^articles/add$', 'Blog_Actions_Articles_AddAction');

?>