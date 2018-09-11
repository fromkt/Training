<?php
include_once(dirname(__FILE__). "/common.php");

// 게시판 리스트
$sql = " SELECT * FROM {$gml['board_table']} WHERE bo_read_level = 1 order by bo_order ";
$result = sql_query($sql);

while ($row = sql_fetch_array($result)) {
	$boards[] = $row;
}

header('Content-type: text/xml');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

echo '<?xml version="1.0" encoding="UTF-8"?>';
// 파일 작성 시작
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($boards as $board) { ?>
	<url>
		<loc><?php echo GML_URL."/{$board['bo_table']}"; ?></loc>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
<?php } ?>
</urlset>
