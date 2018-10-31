<!DOCTYPE >
<html>
<head>
    <title>Pianobar Loved Songs</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="css/style.css"/>
    <script>
        $(document).ready(function() {
            $('#deviceTable').dataTable({
                "lengthMenu": [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
                pageLength: 10,
                order: [[7, "desc"]],
                ajax: "mongod.php?table=lovedSongs",
                dataSrc: 'data',
                columns : [
                    {data : "stationName"},
                    {data : "artist"},
                    {data : "title"},
                    {data : "album"},
                    {data : "genre"},
                    {data : "year"},
                    {data : "label"},
                    {data: "loveDate",
                        "render": function (data) {
                            var date = new Date(data);
                            var month = date.getMonth() + 1;
                            return (month.length > 1 ? month : month) + "/" + date.getDate() + "/" + date.getFullYear()+ "&nbsp;" +(date.getHours() < 10 ? ("0"+date.getHours()) : date.getHours())+ ":"+(date.getMinutes() < 10 ? ("0"+date.getMinutes()) : date.getMinutes()) ;
                        }},

                    {data: "coverImg",
                        "render" : function(data) {
                            if(data){
                                return '<div class="zoom"><img src=' + data + '></div>';
                            }else{
                                return '';
                            }

                        }
                    }
                ]
            });
        });

    </script>
</head>
<body>
<div class="container">
    <h2>Loved Pianobar Songs</h2>
    <table id="deviceTable" class="display" style="width:100%">
        <thead>
        <tr>
            <th>Station</th>
            <th>Artist</th>
            <th>Title</th>
            <th>Album</th>
            <th>Genre</th>
            <th>Year</th>
            <th>Label</th>
            <th>added</th>
            <th>coverArt</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan=9>Pianobar Loved Songs</td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="footer">
    <a href="./">Pianobar History</a>
</div>
</body>
</html>