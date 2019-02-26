<!DOCTYPE>
<html>
<head>
    <title>Pandora Reporter</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"/>
    <script type="text/javascript" src="js/history.js"></script>

</head>
<body>

<!-- modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="lds-heart">
            <div></div>
        </div><!-- lds-heart-->
        <div class="content-container"></div>
    </div><!-- modal-content -->
</div><!--// modal -->

<!-- container -->
<div class="container">
    <h1>Pandora Reporter</h1>

      <header>
        <div class="flex-list">
            <ul>
                <li><a class="aboutLink">About</a></li>
                <li><a class="topLink">Top Artist Plays</a></li>
                <li><a class="genreLink">Plays By Genre</a></li>
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
            <th>Played</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan=6><div class="stats-container"></div></td>
        </tr>
        </tfoot>
    </table>
</div><!--// container -->
<div class="footer"></div><!-- // footer -->

</body>
</html>