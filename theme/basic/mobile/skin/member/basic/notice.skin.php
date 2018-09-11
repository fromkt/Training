<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if (count($list) == 0) {
    $no_notices = '<li class="no_arm">'.__('No notices found').'</li>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="alarm">
    <ul class="arm_cate">
        <li><a href="./notice.php" <?php echo $read_all; ?>><?php e__('View all') ?></a></li>
		<li><a href="./notice.php?read=y" <?php echo $read_y; ?>><?php e__('Readed') ?></a></li>
		<li><a href="./notice.php?read=n" <?php echo $read_n; ?>><?php e__('Unreaded') ?></a></li>
    </ul>
    <h2><?php e__('Notice list') ?></h2>
    <p><?php e__('Total') ?><span><?php echo $total_count; ?> <?php e__('Cases') ?></span></p>

    <form name="fnoticelist" id="fnoticelist" action="<?php echo GML_BBS_URL ?>/notice_list_update.php" onsubmit="return fnoticelist_submit(this);" method="post">
    <div class="arm_btn">
       <button type="button" class="all_chk"><?php e__('Select All') ?></button>
       <button type="submit" name="btn_submit" value="delete_selection" onclick="document.pressed=this.value"><?php e__('Delete Selection') ?></button>
       <button type="submit" name="btn_submit" value="mark_as_read" onclick="document.pressed=this.value"><?php e__('Mark as read') ?></button>
       <button type="submit" class="all_arm_del" name="btn_submit" value="delete_all_notice" onclick="document.pressed=this.value"><?php e__('Delete all notice') ?></button>
    </div>

    <ul class="arm_list">
       <?php for($i=0; $i<count($list); $i++) { ?>
       <li>
           <div class="arl_li_hd">
               <span class="bo_chk li_chk">
                   <label for="chk_no_id_<?php echo $i ?>"><span class="sound_only"><?php e__('Select notice') ?></span></label>
                   <input type="checkbox" name="chk_no_id[]" value="<?php echo $list[$i]['no_id'] ?>" id="chk_no_id_<?php echo $i ?>" />
               </span>
               <span class="li_stat <?php echo $list[$i]['read_class'] ?>"><i class="fa fa-circle"></i> <?php echo $list[$i]['status'] ?></span>
           </div>
           <span class="td_tit"><?php echo $list[$i]['subject'] ?></span>
           <div class="arl_li_bt">
               <span class="li_time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['notice_datetime']; ?></span>
               <span class="li_del"><button class="list_del"><span class="sound_only"><?php e__('Delete') ?></span><i class="fa fa-times" aria-hidden="true"></i></button></span>
           </div>
       </li>
       <?php } ?>
       <?php echo $no_notices; // No notices found ?>
   </table>
   <div class="arm_btn">
       <button type="button" class="all_chk"><?php e__('Select All') ?></button>
       <button type="submit" name="btn_submit" value="delete_selection" onclick="document.pressed=this.value"><?php e__('Delete Selection') ?></button>
       <button type="submit" name="btn_submit" value="mark_as_read" onclick="document.pressed=this.value"><?php e__('Mark as read') ?></button>
       <button type="submit" class="all_arm_del" name="btn_submit" value="delete_selection" onclick="document.pressed=this.value"><?php e__('Delete all notice') ?></button>
    </div>
   </form>
</div>

<!-- pagination -->
<?php echo $write_pages; ?>

<?php
get_localize_script('notice_skin',
array(
'check_msg'=>__('Please select at least one item to %s.'),  // %s 할 게시물을 하나 이상 선택하세요.
'delete_msg'=>__('Are you sure you want to delete it?'),    // 선택한 게시물을 정말 삭제 하시겠습니까?
'delete2_msg'=>__('Once deleted, the data can not be recovered.'),   // 한번 삭제한 자료는 복구할 수 없습니다.
),
true);
?>

<script>
function fnoticelist_submit(f) {
   var chk_count = 0;

   for (var i=0; i<f.length; i++) {
       if (f.elements[i].name == "chk_no_id[]" && f.elements[i].checked)
           chk_count++;
   }

   var todo = '';

   if(document.pressed == 'delete_selection') {
       todo = '<?php e__('Delete Selection') ?>';
   } else if(document.pressed == 'mark_as_read') {
       todo = '<?php e__('Mark as read') ?>';
   }

   if (!chk_count) {
       alert( js_sprintf(notice_skin.check_msg, todo) );
       return false;
   }

   if(document.pressed == 'delete_selection') {
        if (!confirm(notice_skin.delete_msg + "\n\n" + notice_skin.delete2_msg)) {
            $("input[type=checkbox]").prop('checked', false);
            return false;
        }
   }

   return true;
}

$(document).ready(function(){
   // delete all notice
   $(".all_arm_del").click(function(){
       $("input[type=checkbox]").prop('checked', true);
   });

   // change to read
   $(".td_tit a").click(function(event){
       event.preventDefault();

       var href = this.href;
       // only unread status changes to read.
       var read_class = $(this).parents("li").find(".read_arm").text();
       var onclick = $(this).attr('onclick');
       if($.trim(read_class).length > 0) {
           var no_id = $(this).parents("li").find("input[type=checkbox]").val();
           var data = { chk_no_id : no_id };

           $.ajax({
               url: gml_bbs_url + "/ajax.notice_read.php",
               type: "post",
               data: data,
               dataType: "json",
               async: false,
               cache: false,
               success: function(data) {}
           });
       }

       if(onclick) {
           window[onclick]();
       } else {
           document.location.href = href;
       }
   });

   // delete individual
   $(".list_del").click(function(){
       if (!confirm(notice_skin.delete_msg + "\n\n" + notice_skin.delete2_msg))
           return false;
       $("input[type=checkbox]").prop('checked', false);
       var $parent = $(this).parents("tr");
       $parent.find("input[type=checkbox]").prop('checked', true);
       $("#fnoticelist").append("<input type='hidden' name='btn_submit' value='"+"<?php e__('Delete Selection') ?>"+"'/>").submit();
   });

   // select all
   $(".all_chk").click(function(){
       var is_all = 1;
       $(".li_chk label").each(function(){
           if(!$(this).hasClass("click_on")) {
               is_all = 0;
           }
       });

       if(!is_all) {
           $(".li_chk input[type=checkbox]").prop('checked', true);
           $(".li_chk label").addClass("click_on");
       } else {
           $(".li_chk input[type=checkbox]").prop('checked', false);
           $(".li_chk label").removeClass("click_on");
       }
   });

   // select individual
   $(".li_chk label").click(function(){
       if($(this).hasClass("click_on")) {
           $(this).addClass("click_on");
           $(this).parent().find("input[type=checkbox]").prop('checked', false);
       } else {
           $(this).removeClass("click_on");
           $(this).parent().find("input[type=checkbox]").prop('checked', true);
       }
   });
});
</script>
