<?php
include_once('./_common.php');
include_once(GML_LIB_PATH.'/json.lib.php');

if(!$bo_table)
   die(json_encode(array('error'=>__('The bulletin board information is not valid.'), 'url'=>GML_URL)));

set_session('ss_write_'.$bo_table.'_token', '');

$token = get_write_token($bo_table);

die(json_encode(array('error'=>'', 'token'=>$token, 'url'=>'')));
?>