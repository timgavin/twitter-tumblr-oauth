# OAuth Fun With Twitter and Tumblr

### This wee app demonstrates basic CARP* (Connect Authenticate Read Post) with the Twitter and Tumblr APIs.

## Requirements

1. PHP >= 5.4
2. MySQL
3. Composer

## Installation

Download source from GitHub and unzip in a web accessible directory. I'm using a Mac and MAMP so I put mine at `/Applications/MAMP/htdocs/twitter-tumblr-oauth/`


Next, [download Rachman Chavik's TumblrOauth class](https://github.com/rchavik/) and place the `TumblrOauth.php` file in `/Applications/MAMP/htdocs/twitter-tumblr-oauth/assets/oauth/`

Next, [download the official Tumblr PHP client](https://github.com/tumblr/tumblr.php) and place it in the `/Applications/MAMP/htdocs/twitter-tumblr-oauth/assets/oauth/` folder and unzip it.

Rename the unzipped folder to `tumblr`

The oauth directory should look like this

    assets/oauth
             |-- authenticate.php
             |-- ezSQL-master/             |-- OAuth.php             |-- OauthConnections.php             |-- tumblr/             |-- TumblrOAuth.php             |-- twitter/

Open a shell (Terminal) and `cd` to the `tumblr` directory and install the library via Composer

    cd /Applications/MAMP/htdocs/twitter-tumblr-oauth/assets/oauth/tumblr
    composer install
    
Now install [Abraham's TwitterOauth](http://github.com/abraham/twitteroauth) via Composer...
    
    cd ../twitter
    composer install

We'll be caching our Twitter and Tumblr feeds and uploading images, so we need to set permissions on our `cache` and `uploads` directories
    
    chmod 0777 ../../../cache
    chmod 0777 ../../../uploads


## Service APIs

Now we need to set up our Twitter and Tumblr applications. If you already have an app set up on each service, skip this part.

1. Set up a [Twitter API application](https://apps.twitter.com/app/).
2. Set up a [Tumblr API application](http://www.tumblr.com/oauth/apps).

## MySQL

Create a new database and name it `oauth`. Run the following query against the `oauth` db.
```
CREATE TABLE `users_oauth` (
  `uo_usr_id` int(11) unsigned NOT NULL COMMENT 'The user ID from your users table',
  `service` varchar(30) NOT NULL DEFAULT '' COMMENT 'The provider: Twitter, Tumblr, LinkedIn, etc.',
  `service_uid` varchar(99) NOT NULL COMMENT 'The user''s ID from the provider',
  `service_username` varchar(99) DEFAULT NULL COMMENT 'The user''s username from the provider',
  `service_userurl` varchar(99) DEFAULT NULL COMMENT 'The user''s url from the provider',
  `oauth_token` varchar(99) NOT NULL,
  `oauth_secret` varchar(99) NOT NULL,
  PRIMARY KEY (`uo_usr_id`,`service`),
  KEY `service_username` (`service_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

## Config

Open `assets/config.php` and enter the URL to this app, plus your Twitter/Tumblr API and MySQL credentials.

## Play!

Visit `index.php` in a browser and start playing with the app.


---

## Coda

I originally built this as a *very* small procedural example to show how to connect to the Tumblr API, then decided to include posting to Tumblr, then decided to retrieve the user's Tumblr posts, then thought "Why not include Twitter too?..."

This app blew way, way past what I was originally planning. I had some serious thoughts about going back and redoing it as objects, but didn't for two reasons.

1. I wanted to keep it simple so that **anybody** could follow the code.
2. I plum ran out of time. 

So not everything in here is as it should be. There are a few shortcuts and several things I would have done differently if I had done a better job of planning this out. Hopefully this will at least give you the tools to figure out the rest and do a much better job than I did. :)

I also didn't include a Facebook example because Facebook makes you jump through a whole bunch of stupid hoops which delays your development by days - or weeks. It's just not worth the headache for either of us. Oh, and because Facebook totally sucks.

*Did I make this up? I don't know and I'm too lazy to look. Also, CRAP (Connect Read Authenticate Post) was funnier but the order of things didn't jive. :(