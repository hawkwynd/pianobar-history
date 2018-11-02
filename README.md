# pianobar-history


composer.json:

{
    "require": {
        "google/apiclient": "~2.0",         "mongodb/mongodb": "^1.0.0"
    }
}

# Export Pianobar collection

mongoexport -d scottybox -c pianobar --type csv --out pianobar.csv --fields title,artist,loveDate,album,stationName,id,masterId,style,genre,country,coverImg,thumb,formats,year,catno,status,label,coverArt,lyrics,first_played,last_played,num_plays

# Import Pianobar collection

mongoimport -d scottybox -c pianobar --type csv --file filename.csv --headerline

