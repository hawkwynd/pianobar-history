<?php
/**
 * Date: 12/14/18
 * Time: 2:11 PM
 * scottybox - sfleming
 * The about content display functions
 */

?>
<h2>About Pandora Reporter</h2>

<div class="selfie-container">
    <img src="images/IMG_2161.jpg"/>
</div>
<p>This is a project I started out of sheer curiosity for both programming and data collection functions coupled with my ever-increasing yearn for music and the history behind the artists. Pandora is an excellent music streaming service, which provides a wide variety of music for millions of users across the world. It is the base for my quest to where I'm at now in this project.
Even this documentation is a living object in the project - I am constantly adding to it to help provide you with as much information as possible about it's workings and the adventures I experienced while building it.
</p>
<p>
    The main drive for this project came when I discovered a command-line version of the client known as <a href="https://github.com/PromyLOPh/pianobar" target="_blank">Pianobar</a>. Pianobar provides access to the Pandora library without advertisements, and a great set of options that extend the functionality of Pandora's main features. Again, without ever hearing a single advertisement.
</p>
<div class="wrap-collabsible members-collabsible">
    <input id="toggle-members" type="checkbox" class="toggle">
    <label for="toggle-members">Under The Hood</label>
    <div class="expand">
        <section>
            <div class="memberName" excerpt="">API's</div>
            <div class="excerpt">
                Several API's are implemented in Pandora Reporter. It began with a simple mongodb interface to see if I could capture the song title and artist. Then I wanted to know how many times that song was being played by Pandora. Then, I wanted to know more about the artist (wikipedia), and the members of the band. Then, I wanted to know as much information about the song as I could find, so I added a lyrics api. Discogs was the first api used to pull the details about the song, and it kept growing from there. Here's a list of the API's..
            </div>
            <div class="memberName" excerpt="">MongoDB</div>
            <div class="excerpt">
                <a target="_blank" href="https://github.com/mongodb/mongo-php-driver">MongoDB</a> PHP Driver library provides a high-level abstraction around the lower-level. While the extension provides a limited API for executing commands, queries, and
                write operations, this library implements an API similar to that of the legacy PHP driver. It contains abstractions for client, database, and collection objects, and provides methods for CRUD operations and common commands (e.g. index and collection management). It's what holds the love together in the project, and I love it!!!
            </div>
            <div class="memberName" excerpt="">Google APIclient</div>
            <div class="excerpt">
                The Google API Client Library work with Google APIs such as Google+, Drive, or YouTube.
                Specifically, the API provides access to display the YouTube link to the corresponding song being queried and is passed to the youtube-dl functions for extracting the audio from the Youtube video as an mp3 file to be downloaded or played through the AudioPlayer (javascript) functions. It is a powerful api capable of finding even the most rare of music titles and/or artists.
            </div>
            <div class="memberName">youtube-dl</div>
            <div class="excerpt">
                youtube-dl is a command-line program to download videos from YouTube.com and a few more sites. It requires the Python interpreter, version 2.6, 2.7, or 3.2+, and it is not platform specific. It should work in your Unix box, in Windows or in Mac OS X. It is released to the public domain, which means you can modify it, redistribute it or use it however you like. The project is currently being developed at <a target="_blank" href="https://github.com/rg3/youtube-dl/">GitHub</a>. It's what I like to call the "GOAT" of getting music without having to pull down the video and do the extraction to mp3, ogg, or even flac format! I really love this program!
            </div>

            <div class="memberName">Discogs</div>
            <div class="excerpt">
                The <a target="_blank" href="https://www.discogs.com/developers">Discogs API</a> v2.0 is a RESTful interface to Discogs data. You can access JSON-formatted information about Database objects such as Artists, Releases, and Labels.
            </div>

            <div class="memberName">Wikipedia</div>
            <div class="excerpt">
                Utilizing the <a target="_blank" href="https://en.wikipedia.org">Wikipedia API</a>, this application can quickly obtain data from the Wikipedia treasure trove of information. Artist members information, bands, and more are culled from it and used to populate the output in the `info` window for each record. It serves as an educational, and entertaining way to learn about a Band's history, it's members (past and present) as well as cool little party `ice-breakers` content about artists and music.
            </div>
        </section>
    </div>
</div>

<!-- pianobar shares -->
<div class="wrap-collabsible pianobar-collabsible">
    <input id="toggle-pianobar" type="checkbox" class="toggle">
    <label for="toggle-pianobar">The Pianobar Modifications</label>
    <div class="expand">
        <section>
            <div class="memberName" excerpt="">eventcmd</div>
            <div class="excerpt">
                The pianobar application runs on a small linux based computer in my home with it's sole job of just playing the music. It logs in to my Pandora account automatically, and begins playing the last played station. To date, it has over 25 different stations which I can change whenever I am in the mood for something different. Pianobar has a TON of additional functions, which I will not mention here to save time. <br/>
                The `eventcmd` script sends song information from pianobar to the mongodb server as a _POST command, and contains the Title, Artist data as well as some other important information which is inserted (upserted) into the pianobar database. If mongo finds a matching song title and artist already exists, it performs an incremental uptick by one to the `num_played` field in the record, otherwise it inserts a new row into the table. Each time a new song is triggered within pianobar, it sends the data to the server for processing.
            </div>
        </section>
    <div>
</div>
