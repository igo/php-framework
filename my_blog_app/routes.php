<?php

$routes[] = route('^$', 'MyBlogApp_Actions_Articles_ListAction');

$routes[] = route('^articles/(?P<id>\d+)$', 'MyBlogApp_Actions_Articles_ViewAction');

$routes[] = route('^articles/add$', 'MyBlogApp_Actions_Articles_AddAction');

?>