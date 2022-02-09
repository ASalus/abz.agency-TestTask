const myModalEl = document.getElementById('edit-modal');
myModalEl.addEventListener('shown.bs.modal', (e) => {
    $("#head").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "{{url('autocomplete')}}",
                data: {
                    term: request.term
                },
                dataType: "json",
                success: function (data) {
                    var resp = $.map(data, function (obj) {
                        //console.log(obj.name);
                        return obj.name;
                    })
                    response(resp)
                }
            });
        },
        minLength: 3,
        appendTo: "#create-modal"
    });

    $("#position_id").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "{{url('autoc-position')}}",
                data: {
                    term: request.term
                },
                dataType: "json",
                success: function (data) {
                    var resp = $.map(data, function (obj) {
                        //console.log(obj.position_name);
                        return obj.position_name;
                    })
                    response(resp)
                }
            });
        },
        minLength: 3,
        appendTo: "#create-modal"
    });
});