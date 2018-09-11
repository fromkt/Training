<?php
include_once('./_common.php');

// notice preview list
$sql = " select * from {$gml['notice_table']}
            where rel_mb_id = '{$member['mb_id']}'
            and no_read_datetime = '0000-00-00 00:00:00'
            order by no_id desc limit 0, 10 ";
$result = sql_query($sql);

$list = array();
$str = '';
for($i=0; $row=sql_fetch_array($result); $i++) {
    $no_id = $row['no_id'];
    // create notice message
    $subject = get_notice_subject($row);
    // display relative time
    $datetime = time2str($row['no_notice_datetime']);

    $str .=
        "<li>
    		{$subject}
    		<span class=\"list_time\">{$datetime}</span>
    		<button class=\"list_del\" id=\"{$no_id}\"><span class=\"sound_only\">닫기</span><i class=\"fa fa-times\" aria-hidden=\"true\"></i></button>
    	</li>";
}
if ($i == 0) {
    $str =
        "<li>
			".__('No new notices found')."
		</li>";
}
echo $str;
?>
