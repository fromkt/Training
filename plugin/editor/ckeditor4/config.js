/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

//  모바일 체크
if(typeof(gml_is_mobile) == "undefined") gml_is_mobile = false;
// 언어체크 gml_lang
if(typeof(gml_lang) == "undefined") gml_lang = "en_US";

CKEDITOR.editorConfig = function( config ) {
	// 에디터 높이 설정
	if(typeof(editor_height) != "undefined") {
		config.height = editor_height+"px";
	}

	// 언어 설정
	switch( gml_lang ) {
		case "ko_KR":	config.language = 'ko';	break;
		case "ja_JP":	config.language = 'ja';	break;
		case "zh_CN":	config.language = 'zh-cn';	break;
		case "en_US":
		default:		config.language = 'en';	break;
	}
	
	// 글꼴관련
	switch( gml_lang ) {
		case "ko_KR":
			config.font_names = '맑은 고딕;굴림;궁서;돋움;바탕;';  // + CKEDITOR.config.font_names;
			config.font_defaultLabel = '맑은 고딕';
			//config.font_defaultLabel = 'Malgun Gothic';
		break;
	}
	// 글자크기 출력
	config.fontSize_sizes = '8pt;9pt;10pt;11pt;12pt;14pt;16pt;20pt;24pt;30pt;48pt;60pt;72pt;';

	// 툴바 기능버튼 순서
	config.toolbarGroups = [
		{ name: '1', groups: [ 'styles', 'align', 'basicstyles', 'cleanup' ] },
		{ name: '2', groups: [ 'insertImg', 'insert', 'colors', 'list', 'blocks', 'links', 'mode', 'tools', 'about' ] }
	];
	// 미노출 기능버튼
	if(gml_is_mobile) {
		//--- 모바일 ---//
		config.removeButtons = 'Print,Cut,Copy,Paste,Subscript,Superscript,Anchor,Unlink,ShowBlocks,Undo,Redo,Smiley,Font';
	} else {
		//--- PC ---//
		config.removeButtons = 'Print,Cut,Copy,Paste,Subscript,Superscript,Anchor,Unlink,ShowBlocks,Undo,Redo,Smiley';
	}

	/* 이미지 업로드 관련 소스 */
	var up_url = "/upload.php?type=Images";
	if( typeof(gml_editor_url) != "undefined" )	{
		up_url = gml_editor_url + up_url;
	} else {
		up_url = "/plugin/editor/ckeditor4" + up_url;
	}
	// 에디터 구분
	if(typeof(editor_id) != "undefined" && editor_id != "") {
		up_url += "&editor_id="+editor_id;
	}
	// 업로드 경로 - editor_uri
	if(typeof(editor_uri) != "undefined" && editor_uri != "") {
		up_url += "&editor_uri="+editor_uri;
	}
	// 업로드 이미지용 토큰
	if( typeof(editor_form_name) != "undefined" && editor_form_name != "") {
		up_url += "&editor_form_name="+editor_form_name;
	}
	// 게시판 구분
	if(typeof(editor_token) != "undefined" && editor_token != "") {
		up_url += "&ei_token="+editor_token;
	}
    
	// 업로드 페이지 URL 선언
	config.filebrowserImageUploadUrl = up_url;

	//***** 플러그인 추가 내역 ***** //
	// 유튜브 플러그인 추가설정
	config.youtube_autoplay = false;	// 자동실행 안함
	config.youtube_responsive = true;	// 반응형 너비
	config.youtube_disabled_fields = ['chkAutoplay', 'chkPrivacy'];	// 자동실행 비활성

	// 이미지 다이얼로그 수정 
	CKEDITOR.on('dialogDefinition', function (ev) {
		var dialogName = ev.data.name;
		var dialog = ev.data.definition.dialog;
		var dialogDefinition = ev.data.definition;
		if (dialogName == 'image') {
			dialog.on('show', function (obj) {
				//this.selectPage('Upload'); //업로드텝으로 시작
			});
			dialogDefinition.removeContents('advanced'); // 자세히탭 제거
			dialogDefinition.removeContents('Link'); // 링크탭 제거
			
			var infoTab = dialogDefinition.getContents('info');   
			infoTab.remove('txtHSpace');
			infoTab.remove('txtVSpace');
			infoTab.remove('htmlPreview');	// 미리보기 제거
		}
	});

	config.extraPlugins = 'youtube,uploadimage';
};
