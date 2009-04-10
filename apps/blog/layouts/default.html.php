<html>
<head>
<title>My Blog<?php if (isset($title)) echo ': ' . $html->escape($title); ?></title>
<?php echo $html->css('/css/blog.css'); ?>
</head>
<body>
<ul class="menu">
	<li><?php echo $html->link('Home', "/"); ?></li>
	<li><?php echo $html->link('Add article', "/articles/add"); ?></li>
</ul>
<hr />
<?php echo $content; ?>
</body>
</html>
