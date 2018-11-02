/**
 * @author Scott Fleming
 * @email scott.fleming@gmail.com
 * Date: 10/26/18
 *
 *
 */
$(document).ready(function() {

    var info = false;

    $('#deviceTable').dataTable({
        "language": {
            "search"        : "Find anything ",
            "info"          : "Showing _START_ to _END_ of _TOTAL_ songs",
            "lengthMenu"    : "Show _MENU_ songs",
            "zeroRecords"   : "No matching songs found",
            "loadingRecords": "Loading songs...",
            "processing"    : "Processing...",
            "infoFiltered"  : "(filtered from _MAX_ total songs)",
            "paginate": {
                "first":      "First",
                "last":       "Last",
                "next":       "Next",
                "previous":   "Previous"
            }
        },

        "lengthMenu": [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
        pageLength: 10,
        order: [[7, "desc"]],
        ajax: "mongod.php?table=pianobar",
        dataSrc: 'data',
        columns : [
            {data: "masterId",
                "render" : function(data){
                    if(data){
                        info = false;
                        return '<a href="#" class="modalLink" id='+data+' >Info</a>';
                    } else{
                        info = true;
                        return '';
                    }
                }
            },
            {data : "artist",
                "render" : function(data){
                    if(info) {
                        return '<a href="#" class="artistLink" id="' + data + '">' + data + '</a>';
                    }else{
                        return data;
                    }
                }
            },
            {className: "title" ,  data : "title"},
            {className : "album" , data : "album"},
            {data : "genre"},
            {data : "year"},
            {className: "label", data : "label"},
            {data: "loveDate",
                "render": function (data) {
                    var date = new Date(data);
                    var month = date.getMonth() + 1;
                    return (month.length > 1 ? month : month) + "/" + date.getDate() + "/" + date.getFullYear()+ "&nbsp;" +(date.getHours() < 10 ? ("0"+date.getHours()) : date.getHours())+ ":"+(date.getMinutes() < 10 ? ("0"+date.getMinutes()) : date.getMinutes()) ;
                }}


        ]
    });

    // Get the modal
    var modal = document.getElementById('myModal');
    // Get the <span> element that closes the modal
    var fclose = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    fclose.onclick = function() {
        modal.style.display = "none";
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // ------------------------------------------
    // Get just the artist excerpt from Wikipedia
    // ------------------------------------------

    $(document).on('click', '.artistLink', function() {

        var data = encodeURIComponent($(this).attr('id'));
        var url  = 'getArtist.php?artist=' + data;

        $.ajax({
            type: 'GET',
            url : url,
            success: function( response ){
                var content = $.parseJSON( response );
                $.each(content.query.pages, function(idx, v){
                    output = v.extract;
                });
                modal.style.display = "block";
                $('.content-container').html(  output );
            },
            error: function( response ){
                alert("fail");
            }
        });

    });


    // The magic happens here to create a modal and populate it from
    // the json data returned by getMaster.php
    $(document).on('click', '.modalLink', function () {

        $('.content-container').empty(); // flush the toilet!

        var data        = $(this).attr('id');
        var album       = $(this).closest("tr").find(".album").text();
        var songTitle   = $(this).closest("tr").find(".title").text();
        var label       = $(this).closest("tr").find(".label").text();
        var MasterUrl         = 'getMaster.php?id='+data;

        modal.style.display = "block";
        $('.lds-heart').show();
        $('.content-container').hide();

        getMaster();

        function getMaster(){
        $.ajax({
            type: 'GET',
            url: MasterUrl,
            success: function (output) {

                $('.lds-heart').hide();

                var content = $.parseJSON(output);

                console.log(content);

                $('.content-container').append('<h2>'+content.artist.name+'</h2>');
                if(content.metadata.coverImg) $('.content-container').append(
                    '<div class="modalImg"><img src="' + content.metadata.coverImg + '"></div>'
                );
                $('.content-container').append('<div class="songTitle">Title: ' + songTitle + '</div>');

                // Get The album
                if(album) $('.content-container').append('<div class="albumTitle">Album: ' + album + '</div>');
                // get Release Year
                if(content.year_released) $('.content-container').append('<div class="albumTitle">Released: ' + content.year_released + '</div>');

                if(label) $('.content-container').append('<div class="albumTitle">Label: ' +label+ '</div>'  );

                // Styles
                if(content.styles) $('.content-container').append('<div class="wiki style">Style: '+content.styles+'</div>');
                // Genres
                if(content.genres) $('.content-container').append('<div class="wiki genre">Genre: '+content.genres+'</div>');

                // formats
                if(content.metadata.formats)$('.content-container').append('<div class="wiki formats">Formats: '+content.metadata.formats+'</div>');

                // station name
                if(content.core.stationName) {
                    $('.content-container').append('<div class="wiki albumTitle">Pandora station: ' + content.core.stationName + '</div>');
                }

                // first played date
                if(content.core.first_played) $('.content-container').append('<div class="wiki first_played">First Played: ' + content.core.first_played + '</div>');

                // total plays by pianobar
                if(content.core.num_plays) {
                    var playTxt = content.core.num_plays > 1 ? ' times' : ' time';
                    $('.content-container').append('<div class="wiki num_plays">Pandora has played this song '+ content.core.num_plays + playTxt +'.</div>');
                    // last played date
                    if(content.core.num_plays > 1){
                        $('.content-container').append('<div class="wiki last_played">Last Played: ' + content.core.last_played + '</div>');
                    }

                }



                // show lyrics
                if(content.lyrics.length > 50) {
                    $('.content-container').append('<div class="wrap-collabsible">'+
                        '<input id="collapsible" class="toggle" type="checkbox">'+
                        '<label for="collapsible" class="lbl-toggle">Lyrics</label>' +
                        '<div class="collapsible-content">'+
                        '<div class="content-inner"">'+content.lyrics +'<br/></div></div></div>');
                }

                // display band extract
                if(content.wiki.extract){
                    $('.content-container').append('<h2>Band information</h2><div class=wiki>'+content.wiki.extract+'</div>');
                }

                // Band members list (if available)
                if(content.artist.members.length > 0){
                    var members = '<h3>Members</h3>';

                    $.each(content.artist.members, function( k, v){
                        members += '<div class=memberName excerpt>'+ v.member_name + '</div>';
                        members += '<div class=excerpt>' + v.member_content + '</div>';
                    });

                    $('.content-container').append(members);
                }

                getVideo(content.artist.name + ' ' + songTitle); // call the video ajax here

            },
            error: function(output){
                alert("fail");
            }
        });

    } // getMaster()

        /**
         * @function getVideo
         * @desc ajax call to retrieve 3 youtube objects
         * @param string
         */
        function getVideo(query){

            $.ajax({
                type: 'POST',
                url: 'youtube.php',
                data: {'q': query, 'maxResults': 3 },
                success: function(results){

                    var arr = $.parseJSON(results);
                    var videos = '<h3 class="ytHeader">Listen</h3><div class=vcontainer>';

                    $.each(arr, function(k,v){
                       videos += '<div class="vThumb"><img src="'+ v.thumb + '"></div>';
                       videos += '<div class="youtubeLink"><a href="https://music.youtube.com/watch?v=' + v.videoId + '" target="_blank">' + v.title + '</a><br/>' + v.description;
                       videos += '<div class="postDate">Posted ' + v.postDate + '</div><br clear="all"/>';
                    });

                    videos += '</div><!-- vcontainer -->';
                    $('.content-container').append(videos).fadeIn('slow');
                }
            });

        } // getVideo()


    });



});

