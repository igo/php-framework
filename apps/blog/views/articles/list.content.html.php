<h2>Articles</h2>
<?php if (empty($articles)): ?>
	<i>No articles</i>
<?php else: ?>
	<?php foreach($articles as $article): ?>
		<h2><?php echo $article['Article']['title']; ?></h2>
		<p><?php echo $article['Article']['excerpt']; ?></p>
		<?php echo $html->link('read more...', "/articles/{$article['Article']['id']}"); ?>
	<?php endforeach; ?>
<?php endif; ?>
