<?php
include_once("./_common.php");
include_once(GML_LIB_PATH."/register.lib.php");

$mb_recommend = trim($_POST["reg_mb_recommend"]);

if ($msg = valid_mb_id($mb_recommend)) {
    die(__('Please enter only letters, numbers, and _. for the Recommend-ID'));
}
if (!($msg = exist_mb_id($mb_recommend))) {
    die(__('The recommender you entered does not exist.'));
}
?>