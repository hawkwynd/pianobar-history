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


### SET UP STEPS FOR PIANOBAR application #########


How to install Mongodb PHP extension in Ubuntu 16.04 LTS with php5.x

`sudo apt-get install php-mongodb`


########## FOR php7:
$ sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927

$ echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.2.list

$ sudo apt-get update

$ sudo apt-get install -y mongodb-org

Create file mongodb.service in /etc/systemd/system/ by entering the command:

$ sudo nano /etc/systemd/system/mongodb.service

Paste the following contents in it:

[Unit]
Description=High-performance, schema-free document-oriented database
After=network.target

[Service]
User=mongodb
ExecStart=/usr/bin/mongod --quiet --config /etc/mongod.conf

[Install]
WantedBy=multi-user.target

Then enter the following commands:

$ sudo systemctl start mongodb

$ sudo systemctl enable mongodb

####### Installing the mongo-php driver:

$ sudo pecl install mongodb

Also you might receive error: phpize not found. Phpize is a command which is used to create a build environment. This error could appear at the time of installation of any pecl extension. To solve this problem of the phpize command not found, the user has to install the php5-dev package. To install it enter the command:

 $ sudo apt-get install php7.0-dev
Then in the php.ini file which is in /etc/php/7.0/apache2 directory, add the mongo db extension:

$ sudo nano /etc/php/7.0/apache2/php.ini
Add the following line in the file:

extension = mongo.so;

alternatively: `sudo apt-get install php7.0-mongodb`

############# Install Google-api-client #######################

in composer.json:

composer require google/apiclient:"^2.0"

