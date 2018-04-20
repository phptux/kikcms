##Setting up dev environment

###Docker 
Install Docker CE: https://www.docker.com/community-edition

Make sure MySQL and Log dirs are created:

`mkdir ~/.docker-kikdev && mkdir ~/.docker-kikdev/mysql && mkdir ~/.docker-kikdev/logs`

and a network is started:

`docker network create kikdev`

####Complete stack 

To set up a complete environment, with:

* The website available on port 443 (https://localhost)
* MySQL available on 3306
* MailHog (to test emails) GUI available on port 8025

Use this command (from the website root):

`docker-compose -f vendor/kiksaus/kikcms/docker/docker-compose.yml up -d`

####Multiple websites 
To run multiple websites, with a user specified port per website:

Run (from the website root): 

`docker-compose -f vendor/kiksaus/kikcms/docker/docker-compose-services.yml up -d`

once, and:

`SITE_PORT=[PORT] docker-compose -f vendor/kiksaus/kikcms/docker/docker-compose-site.yml -p [NAME] up -d`

per site, where you replace [PORT] with the desired port and [NAME] with a unique name.

####Down 
To take an environment down, run the same command, but replace `up -d` with `down`

####Database

Make sure you have this in your env/config.ini file, replacing [DB_NAME] with your database name:

```
[application]
env = dev
sendmailCommand = /usr/bin/mhsendmail -t --smtp-addr mail:1025
 
[database]
username = root
password = adminkik12
dbname = [DB_NAME] 
host = mysql
```

###Alternatives
While Docker is recommended, you can also set-up a environment using MAMP or even on 
MacOS itself. Make sure to install the following plugins:

* Phalcon
* ImageMagick
* APCu
* XDebug (optional)

And set up:
* MySQL
* MailHog