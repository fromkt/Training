<?php
include_once("./_common.php");
?>
<!DOCTYPE HTML>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<title><?php e__('Attach Photos'); ?> :: SmartEditor2</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Generic page styles -->
<link rel="stylesheet" href="css/style.css?v=140715">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="css/jquery.fileupload.css">
</head>
<body>
<div class="container pop_container">
	<!-- header -->
    <div id="pop_header">
        <h1><?php e__('Attach Photos'); ?></h1>
    </div>
    <!-- //header -->
    <div class="content_container">
        <div class="drag_explain">
            <p><?php e__('Change the order by dragging with the mouse.'); ?></p>
            <div class="file_selet_group">
            <span class="btn btn-success fileinput-button">
                <span><?php e__('Select File'); ?></span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="fileupload" type="file" name="files[]" multiple accept="image/*">
            </span>
            <button type="button" class="btn btn-danger delete" id="all_remove_btn">
                <span><?php e__('Delete All'); ?></span>
            </button>
            </div>
        </div>
        <div class="drag_area" id="drag_area">
            <ul class="sortable" id="sortable">
            </ul>
            <em class="blind"><?php e__('Add images by dragging with the mouse.'); ?></em><span id="guide_text" class="bg hidebg"></span>
        </div>
        <div class="seletion_explain"><?php e__('Up to 10 images can be selected at a time.'); ?></div>
        <div class="btn_group">
            <button type="button" class="btn" id="img_upload_submit">
                <span><?php e__('Add'); ?></span>
            </button>
            <button type="button" class="btn" id="close_w_btn" >
                <span><?php e__('Cancel'); ?></span>
            </button>
        </div>
    </div>
</div>

<?php
get_localize_script('supload',
array(
'allow_img_msg'=>__('Allow images only.'),  // 이미지만 허용합니다.
'select_file_msg'=>__('Please select no more than %s files.'),    // 파일을 %s 개 이하로 선택해주세요.
'is_ie_msg' => __('Browser does not support drag and drop.'),  //브라우저가 드래그 앤 드랍을 지원하지 않습니다.
'delete_msg'=>__('An image has been added. Are you sure you want to delete it?'),  //추가한 이미지가 있습니다.정말 삭제 하시겠습니까?
),
true);
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="./js/jquery.iframe-transport.js"></script>

<script type="text/javascript" src="./swfupload/swfupload.js"></script>
<script type="text/javascript" src="./swfupload/jquery.swfupload.js"></script>

<!-- The basic File Upload plugin -->
<script src="./js/jquery.fileupload.js?v=140715"></script>

<script src="./js/basic.js?v3"></script>
</body> 
</html>