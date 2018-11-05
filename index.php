<!DOCTYPE>
<html>
<head>
    <title>Pianobar History</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"/>
    <script type="text/javascript" src="js/history.js"></script>
</head>
<body>

<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="lds-heart">
            <div></div>
        </div>

        <div class="content-container"></div>

    </div>
</div>

<!-- the container -->
<div class="container">
    <h1>Pianobar Music History</h1>
    <header>
        <div class="menu">
            <ul>
                <li><a href="loved.php">Loved songs</a></li>
                <li><a href="">Menu 2</a></li>
            </ul>

        </div>
    </header>
    <table id="pianobarTable" class="display" style="width:100%">
        <thead>
        <tr>
            <th>Info</th>
            <th>Artist</th>
            <th>Title</th>
            <th>Album</th>
            <th>Genre</th>
            <th>Released</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan=6>2018 Pianobar Data Tracker by <a href="mailto:scott.s.fleming@gmail.com">Scott Fleming</a></td>
        </tr>
        </tfoot>
    </table>
</div><!-- // the container -->

<div class="footer">
    <a href="loved.php">View Loved Songs</a>
</div>
</body>
</html>