<html>
<head>
<title>My Blog<?php if (isset($title)) echo ': ' . $html->escape($title); ?></title>
</head>
<body>
<?php echo $html->link('Home', "/"); ?>
<hr />
<?php echo $content; ?>
</body>
</html>
