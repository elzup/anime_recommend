<?php
/* @var $title Title */
?>
<div id="wrapper">
	<section class="title-box">
		<div class="tops">
			<h2><?= $title->name ?></h2>
			<img src="<?= $title->imgurl ?>" alt="<?= $title->name ?>のサムネ画像">
		</div>
		<div class="right-columns">
			<p class="description">
				アニメの詳細な情報
			</p>
			<section>
				<h3>未視聴</h3>
				<input type="button" value="面白そう" case="1"></input>
				<input type="button" value="興味ない" case="2"></input>
			</section>
			<section>
				<h3>視聴済み</h3>
				<input type="button" value="面白かった！" case="4"></input>
				<input type="button" value="普通" case="5"></input>
				<input type="button" value="嫌い" case="6"></input>
			</section>
		</div>
	</section>
</div>
