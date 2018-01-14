(function(yourcode) {
    yourcode(window.jQuery, window, document);
}(function($, window, document) {
    $(function() {

        var form = $('#form');
        var results = $('#results');

        form.on('submit', function (e) {
            e.preventDefault();

            var data = $( this ).serialize();

            $.ajax({
                url: "/",
                type: "get",
                data: data
            }).done(function(result) {
                results.html(result);
                window.history.pushState(null, null, "/?" + data);
            });
        })

    });
}));