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
            "search"        : "Find (almost) anything ",
            "info"          : "Showing _START_ to _END_ of _TOTAL_ songs",
            "lengthMenu"    : "Show _MENU_ songs",
            "zeroRecords"   : "Yeah, I'm not finding that one, sorry.",
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

    // Get the modal object
    var modal = document.getElementById('myModal');
    // Get the <span> element that closes the modal
    var fclose = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    fclose.onclick = function() {

        $('.audioplayer-playpause a').click(); // stop player if playing
        $('.content-container').empty();
        modal.style.display = "none";

    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            $('.audioplayer-playpause a').click(); // stop player if playing
            $('.content-container').empty();
            modal.style.display = "none";

        }
    }

    // ---------------------------------------------------
    // Get just the artist excerpt from Wikipedia's excerpt
    // ---------------------------------------------------

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
    // and make a few calls back to the server for other content
    // which will be documented later.

    $(document).on('click', '.modalLink', function () {

        $('.content-container').empty(); // flush the toilet!

        var data        = $(this).attr('id');
        var album       = $(this).closest("tr").find(".album").text();
        var songTitle   = $(this).closest("tr").find(".title").text();
        var label       = $(this).closest("tr").find(".label").text();
        var MasterUrl   = 'getMaster.php?id='+data;

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

                $('.content-container').append('<h2 id=artist_name>'+content.artist.name+'</h2>');
                if(content.metadata.coverImg) $('.content-container').append(
                    '<div class="modalImg"><img src="' + content.metadata.coverImg + '"></div>'
                );
                $('.content-container').append('<div class="wiki">Title: <b><i>' + songTitle + '</i></b></div>');

                // Get The album
                if(album) $('.content-container').append('<div class="wiki">Album: <b><i>' + album + '</i></b></div>');
                // Label
                if(label) $('.content-container').append('<div class="wiki">Label: ' +label+ '</div>'  );
                // get Release Year
                if(content.year_released) $('.content-container').append('<div class="wiki">Released: ' + content.year_released + '</div>');

                // Styles
                if(content.styles) $('.content-container').append('<div class="wiki style">Style: '+content.styles+'</div>');
                // Genres
                if(content.genres) $('.content-container').append('<div class="wiki genre">Genre: '+content.genres+'</div>');

                // formats
                if(content.metadata.formats)$('.content-container').append('<div class="wiki formats">Media: '+content.metadata.formats+'</div>');

                // station name stats
                if(content.core.stationName) {
                    $('.content-container').append('<div class="wiki">Pandora Station: <b>' + content.core.stationName + '</b></div>');
                }

                if(content.core.num_plays){
                    $('.content-container').append('<div class="PstatsHeader">'+ content.artist.name +' nerdly statistics</div>')

                    // first appeared date
                    if(content.core.first_played) $('.content-container').append('<div class="wiki Pstats"><i>' + songTitle +
                        '</i> first appeared: ' + content.core.first_played + '</div>');

                    // total plays by pianobar
                    if(content.core.num_plays) {
                        $('.content-container').append('<div class="wiki Pstats"><i>' +songTitle+ '</i> appearances: '+ content.core.num_plays + '</div>');
                        // last played date
                        if(content.core.num_plays > 1){
                            $('.content-container').append('<div class="wiki Pstats">Last appeared: ' + content.core.last_played + '</div>');
                        }
                    }

                    // plays per artist
                    var playresults = getPlays(content.artist.name);
                    if(playresults.plays){
                        $('.content-container').append('<div class="wiki Pstats"><i>' + content.artist.name +
                            '</i> appearances to date: ' + playresults.plays + ' (' + playresults.rating +'%)</div>');
                    }

                } // if content.core.num_plays

                // show lyrics
                if(content.lyrics.length > 50) {
                    $('.content-container').append(
                        '<div class="wrap-collabsible">' +
                         '<input id="toggle" type="checkbox" class="toggle">' +
                            '<label for="toggle">'+ songTitle +' lyrics</label>' +
                              '<div class="expand">' +
                                '<section><p>'+content.lyrics +'</p></section>' +
                         '</div></div>'
                    );
                }

                // display band extract
                if(content.wiki.extract){
                    $('.content-container').append('<div class="wrap-collabsible info-collabsible"><input id="toggle-info" type="checkbox" class="toggle">' +
                        '<label for="toggle-info">'+ content.artist.name + ' Information</label>' +
                        '<div class="expand">' +
                        '<section>'+ content.wiki.extract +'</section>' +
                        '</div></div>');
                }

                // Band members list (if available)
                if(content.artist.members.length > 0){
                    var members='';
                    var members_content = '';
                    $.each(content.artist.members, function( k, v){
                        members_content += '<div class=memberName excerpt>'+ v.member_name + '</div>';
                        v.member_content != false ? members_content += '<div class=excerpt>' + v.member_content + '</div>':'';
                    });

                    members = '<div class="wrap-collabsible members-collabsible"><input id="toggle-members" type="checkbox" class="toggle">' +
                              '<label for="toggle-members">'+ content.artist.name + ' members</label>' +
                              '<div class="expand">' +
                              '<section>'+ members_content +'</section>' +
                              '</div></div>';
                    $('.content-container').append(members);
                }

                getVideo(content.artist.name, songTitle); // call the video ajax here

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
        function getVideo(artist, title){

            $.ajax({
                type: 'POST',
                url: 'youtube.php',
                data: {'q': artist + ' ' + title, 'maxResults': 1 },
                success: function(results){

                    var arr = $.parseJSON(results);
                    var videos = '<h3 class="ytHeader">'+ title + '</i></h3><div class=vcontainer>';

                    $.each(arr, function(k,v){
                       videos += '<div class="vThumb"><img src="'+ v.thumb + '"></div>';
                       videos += '<div class="youtubeLink" id="' + v.videoId + '"><i class="fab fa-youtube"></i> Listen to <i>' +title + '</div>';
                       videos += '<span class="wait">Fetching <b>'+ title +'</b> to extract mp3 audio...</span>';
                       videos += '<div class="vdescription"></div>';
                       videos += '<div class="postDate">Posted ' + v.postDate + '</div><br clear="all"/>';
                    });

                    videos += '</div><!-- vcontainer -->';
                    $('.content-container').append(videos).fadeIn('slow');
                }
            });

        } // getVideo()

        /**
         * @param artist
         * @returns {*}
         * @desc gets the play statisttics for the artist selected
         */
        function getPlays(artist) {
            var result;
            $.ajax({
                url:"stats.php?artist="+artist,
                async: false,
                success:function(data) {
                    result = $.parseJSON(data);
                }
            });
            return result;
        }


    });

    // Youtube player and download function
    $(document).on('click', '.youtubeLink', function () {
        var id = $(this).attr('id');
        $('#' + id).html('').removeClass('youtubeLink').addClass('loader');
        $('.wait').fadeIn('slow').addClass('animate-flicker');
       $.ajax({
            type: 'GET',
            url: 'youtub-dl.php?v=' + id ,
            success: function(results){
                $('.wait').hide();
                $('#'+id ).html(results).removeClass('loader').addClass('player-container');
            }
        }).fail(function() {
                alert( "Oh Fuck, something went BOOM!" );
        });


    });



    });

