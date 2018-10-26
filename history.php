<!DOCTYPE>
<html>
<head>
    <title>Pianobar History</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="css/style.css"/>
    <script type="text/javascript" src="js/history.js"></script>
</head>
<body>

<!-- Trigger/Open The Modal -->
<!-- <button id="myBtn">Open Modal</button> -->


<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="content-container"></div>
        <span class="close">&times;</span>
    </div>

</div>

<div class="container">
    <h2>Pianobar Historical</h2>
    <table id="deviceTable" class="display" style="width:100%">
        <thead>
        <tr>
            <th>Info</th>
            <th>Artist</th>
            <th>Title</th>
            <th>Album</th>
            <th>Genre</th>
            <th>Year</th>
            <th>Label</th>
            <th>Played</th>
            <th>Cover</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan=6>The Pianobar History of Played Music Collection by Scott Fleming</td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="footer">
    <a href="/pianobar">View Loved Songs</a>
</div>
</body>
</html>