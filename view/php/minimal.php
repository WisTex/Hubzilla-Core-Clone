<?php
/**
 *   * Name: full
 *   * Description: A single column full width layout
 *   * Version: 1
 *   * Author: None
 *   * Maintainer: None
 *   * ContentRegion: region_1_minimal
 */
?>
<!DOCTYPE html >
<html prefix="og: http://ogp.me/ns#">
<head>
  <title><?php if(x($page,'title')) echo $page['title'] ?></title>
  <script>var baseurl="<?php echo z_root() ?>";</script>
  <?php if(x($page,'htmlhead')) echo $page['htmlhead'] ?>
</head>
<body>
	<section id="region_1_minimal">
		<?php if(x($page,'content')) echo $page['content']; ?>
		<div id="page-footer"></div>
	</section>
</body>
</html>

