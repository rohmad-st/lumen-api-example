<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Google Map</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries --><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
                <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Google Map</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">
    <div class="starter-template">
        <div class="row">
            <form method="post">
                <div class="form-group">
                    <input type="text" class="form-control" id="inputPlace" placeholder="Search place">
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-7">
                <h4>Google Map API</h4>
                <div id="appendOriginal" style="text-align: left !important;"></div>
            </div>
            <div class="col-md-5">
                <h4>Custom API</h4>
                <div id="appendCustom" style="text-align: left !important;"></div>
            </div>
        </div>
        <div class="row">
            <div id="notifSource"></div>
        </div>
    </div>

</div><!-- /.container -->

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script>
    $(function () {
        $("#inputPlace").autocomplete({
            source: function (request, response) {
                $.getJSON("/api/map/find-place?term=" + request.term, function (data) {
                    response($.map(data, function (val) {
                        return {
                            label: val.name,
                            value: val.name,
                            map_id: val.map_id,
                            source: val.source
                        };
                    }));
                });
            },
            minLength: 3,
            select: function (event, ui) {
                var label = ui.item.label,
                        value = ui.item.value,
                        map_id = ui.item.map_id,
                        source = ui.item.source;

                // show describe if source from google
                if (source == 1) {
                    document.getElementById('notifSource').innerHTML = '';
                    $('#notifSource').append('<h4>Source berasal dari Google Map Api</h4>');

                    // get detail
                    getDetailOriginal(map_id);
                    getDetailCustom(map_id);

                } else {
                    document.getElementById('notifSource').innerHTML = '';
                    $('#notifSource').append('<h4>Source berasal dari Api Local</h4>');
                }
            }
            //delay: 100
        });

        function getDetailOriginal(placeId) {
            document.getElementById('appendOriginal').innerHTML = '';

            $.ajax({
                type: "GET",
                url: "/api/map/place-detail/" + placeId + '?is_original=1',
                dataType: "json",
                success: function (data) {
                    var str = JSON.stringify(data, undefined, 4);
                    $('#appendOriginal').append('<pre>' + syntaxHighlight(str) + '</pre>');
                }
            });
        }

        function getDetailCustom(placeId) {
            document.getElementById('appendCustom').innerHTML = '';

            $.ajax({
                type: "GET",
                url: "/api/map/place-detail/" + placeId,
                dataType: "json",
                success: function (data) {
                    var str = JSON.stringify(data, undefined, 4);
                    $('#appendCustom').append('<pre>' + syntaxHighlight(str) + '</pre>');
                }
            });
        }

        function syntaxHighlight(json) {
            if (typeof json != 'string') {
                json = JSON.stringify(json, undefined, 2);
            }
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = 'number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }
    });

</script>
</body>
</html>