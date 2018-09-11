$(function() {
    $(".theme_active").on("click", function() {
        var theme = $(this).data("theme");
        var name  = $(this).data("name");

        if(!confirm(js_sprintf(theme_js_l10n.theme_apply_text, name)))
            return false;

        var set_default_skin = 0;
        if($(this).data("set_default_skin") == true) {
            if(confirm(theme_js_l10n.theme_setting_text+"\n\n"+theme_js_l10n.theme_skin_text))
                set_default_skin = 1;
        }

        $.ajax({
            type: "POST",
            url: "./theme_update.php",
            data: {
                "theme": theme,
                "set_default_skin": set_default_skin
            },
            cache: false,
            async: false,
            success: function(data) {
                if(data) {
                    alert(data);
                    return false;
                }

                document.location.reload();
            }
        });
    });

    $(".theme_deactive").on("click", function() {
        var theme = $(this).data("theme");
        var name  = $(this).data("name");

        if(!confirm(js_sprintf(theme_js_l10n.theme_disable_text, name)+"\n\n"+theme_js_l10n.theme_disable_text2))
            return false;

        $.ajax({
            type: "POST",
            url: "./theme_update.php",
            data: {
                "theme": theme,
                "type": "reset"
            },
            cache: false,
            async: false,
            success: function(data) {
                if(data) {
                    alert(data);
                    return false;
                }

                document.location.reload();
            }
        });
    });

    $(".theme_preview").on("click", function() {
        var theme = $(this).data("theme");

        $("#theme_detail").remove();

        $.ajax({
            type: "POST",
            url: "./theme_detail.php",
            data: {
                "theme": theme
            },
            cache: false,
            async: false,
            success: function(data) {
                $("#theme_list").after(data);
            }
        });
    });
});