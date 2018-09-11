function check_all(f)
{
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++)
        chk[i].checked = f.chkall.checked;
}

function btn_check(f, act)
{
    if (act == "update") // Modify Selection
    {
        f.action = list_update_php;
        str = admin_js_l10n.edit_text;
    }
    else if (act == "delete") // Delete Selection
    {
        f.action = list_delete_php;
        str = admin_js_l10n.delete_text;
    }
    else
        return;

    var chk = document.getElementsByName("chk[]");
    var bchk = false;

    for (i=0; i<chk.length; i++)
    {
        if (chk[i].checked)
            bchk = true;
    }

    if (!bchk)
    {
        alert( js_sprintf(admin_js_l10n.select_s_text, str) );
        return;
    }

    if (act == "delete")
    {
        if (!confirm( admin_js_l10n.confirm_delete_text ))
            return;
    }

    f.submit();
}

function is_checked(elements_name)
{
    var checked = false;
    var chk = document.getElementsByName(elements_name);
    for (var i=0; i<chk.length; i++) {
        if (chk[i].checked) {
            checked = true;
        }
    }
    return checked;
}

function delete_confirm(el)
{
    if(confirm( admin_js_l10n.confirm_delete_text )) {
        var token = get_ajax_token();
        var href = el.href.replace(/&token=.+$/g, "");
        if(!token) {
            alert( admin_js_l10n.token_wrong_text );
            return false;
        }
        el.href = href+"&token="+token;
        return true;
    } else {
        return false;
    }
}

function delete_confirm2(msg)
{
    if(confirm(msg))
        return true;
    else
        return false;
}

function get_ajax_token()
{
    var token = "";

    jQuery.ajax({
        type: "POST",
        url: gml_admin_url+"/ajax.token.php",
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {
            if(data.error) {
                alert(data.error);
                if(data.url)
                    document.location.href = data.url;

                return false;
            }

            token = data.token;
        }
    });

    return token;
}

jQuery(function() {
    $(document).on("click", "form input:submit, form button:submit", function() {
        var f = this.form;
        var token = get_ajax_token();

        if(!token) {
            alert(admin_js_l10n.token_wrong_text);
            return false;
        }

        var $f = $(f);

        if(typeof f.token === "undefined")
            $f.prepend('<input type="hidden" name="token" value="">');

        $f.find("input[name=token]").val(token);

        return true;
    });
});
