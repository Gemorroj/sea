var Apanel = {
    files: function (type) {
        var tpl = document.getElementById("tpl").cloneNode(true), fl = document.getElementById("fl");
        var div = fl.getElementsByTagName("div");

        tpl.style.display = "block";
        tpl.removeAttribute("id");
        tpl.childNodes[0].name = "userfile[" + div.length + "]";

        if (type == 1) {
            fl.insertBefore(tpl, null);
        } else {
            if (div.length > 0) {
                var el = div[div.length - 1];
                el.parentNode.removeChild(el);
            }
        }
    },


    filesAttach: function  (data, type) {
        var tpl = document.getElementById("tplAttach").cloneNode(true), fl = data.parentNode;
        var span = fl.getElementsByTagName("span");

        tpl.style.display = "inline";
        tpl.removeAttribute("id");
        tpl.childNodes[0].name = "attach_" + fl.childNodes[0].name + "[]";

        if (type == 1) {
            fl.insertBefore(tpl, null);
        } else {
            if (span.length > 0) {
                var el = span[span.length - 1];
                el.parentNode.removeChild(el);
            }
        }
    }
};