<?php

$routes[] = route('^$', 'MyBlogApp_Actions_Articles_ListAction');

$routes[] = route('^articles/(?P<id>\w+)$', 'MyBlogApp_Actions_Articles_ViewAction');

?>
