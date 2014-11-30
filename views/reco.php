<?php
/* @var $title Title */
?>
<div id="wrapper">
	<section class="title-box">
		<header>
			<h2><?= $title->name ?></h2>
		</header>
		<div class="tops">
			<div class="img-box">
				<img class="sumnail" src="<?= $title->imgurl ?>" alt="<?= $title->name ?>のサムネ画像">
			</div>
		</div>
		<div class="right-columns">
			<section class="description">
				<h3><?= $title->name ?></h3>
				<p>
					説明文<br/>
					説明文説明文<br/>
					説明文説明文説明文<説明文説明文説明文<br/>
					説明文説明文説明文<説明文説明文説明文<br/>
					説明文説明文説明文<説明文説明文説明文<br/>
				</p>
			</section>
			<section class="not-view">
				<h3>未視聴</h3>
				<input type="button" value="面白そう" case="1"></input>
				<input type="button" value="興味ない" case="2"></input>
			</section>
			<section class="watched">
				<h3>視聴済み</h3>
				<input type="button" value="面白かった" case="4"></input>
				<input type="button" value="普通" case="5"></input>
				<input type="button" value="嫌い" case="6"></input>
			</section>
		</div>
	</section>
</div>
