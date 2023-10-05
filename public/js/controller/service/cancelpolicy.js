(function() {

    $(document).ready(function() {

        var cancelpolicyPanel = $("#cancelpolicy-panel");

        if (cancelpolicyPanel.length) {
            cancelpolicyPanel.find("img").closest("a").css("opacity", 1.0).wrap('<div class="panel">');
        }

    });

})();