<script>
// Click the tab to see a list of recent posts.
$(document).ready(function(){
    $(".pic_tab_wrap").each(function() {
        opentab($(this).find(".tablinks").first());
    });
});

$(document).on('click', ".tablinks", function() {
    opentab($(this));
});

function opentab($this) {
    var tab_idx = $this.parent(".pic_tab_heading").find(".tablinks").index($this);
    var $tab_views = $this.parent(".pic_tab_heading").siblings(".tab_cnt");
    var $tab_links = $this.parent(".pic_tab_heading").find(".tablinks");
    var $see_more_btn = $this.parent(".pic_tab_heading").siblings(".pic_tab_more");

    $tab_views.css('display', 'none');
    $tab_links.find('a').removeClass('active');
    $see_more_btn.css('display', 'none');

    $tab_views.eq(tab_idx).css('display', 'block');
    $tab_links.eq(tab_idx).find('a').addClass('active');
    $see_more_btn.eq(tab_idx).css('display', 'block');
}
</script>
