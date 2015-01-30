# community-site
Inexor's community site built upon [CodeIgniter 2x](http://www.codeigniter.com/user_guide/) series.

## Features planned
The site is going to be structured as a simple infinite-scrolling-based page offering *"streams"* with different tabs of content.
The main tabs are listed below:

- News

This tab shows a collection of aggregated news all over the cubeengine world. 
Items can be starred, tagged, mentioned, commented and liked (*"karma"*). A extensive search algorithm should allow the user to browse specific categories or types of news (eg: forum), as well as it should power a *"favorite"* tab.
Administrators can feed the news tab with RSS, XML and HTML-based Blogs, Forums (...) -> items will be linked & previewed (a few lines of description).
- Content

The content spare allows people to upload any kind of cube-related material (screenshots, maps, videos ...).
Tagging, commenting and repost/like should be available.
As an ultimative goal resources uploaded within the content tab should be accessable for the user within the Inexor-client.
- Discuss

[bbPress-alike](https://bbpress.org/) forum
- Hot spot

The hot spot displays a live-view of currently running games. Furthermore the hot spot collects statistics from servers which have registered using #JSON/Webhook and display a ranking.
Additionaly we could offer league-folks to postpone league results within this tab as well!
- Market

The market offers players to create clans, join/leave them and to post "clan-applications" (eg. "I am looking for an eCTF clan").
In later steps we should consider this tab as well to share custom-made items within Inexor!

## Deployment & Requirements
This site is built using CodeIgniter 2.1++ and will therefore require PHP 5.4
Using PHP >= 5.4 is highly appreciated because we make use of clojures and lamda-functions, which are not supported in lower versions of PHP.
The following depencies are needed to get this site up and running:
- php5-mysql //This could as well work with other PDO drivers as we use ARP, but I haven't explicitely tested any other than mySQL
- php5-json
- php5-xml
- php5-curl

Once you managed to install the depencies & clone the site make sure to modify the following config files:
- application/config/config.php

```php
$config['encryption_key'] = '';
```
- application/config/migration.php

```php
$config['migration_enabled'] = TRUE; // This should be enabled on initialisation
```

- application/config/database.php

Please make sure to use a table prefix because some parts of the code specifically requires them.

##Development
If you'd like to start over and develop a few lines of code, you should be aware of a few things:
- jQuery, Bootstrap with Normalize (responsive), TinyMCE, Fontawesome are already there. Dont' use any more depencies if not **really** needed.
- There is no *"real"* multilingual-functioning at the moment, all you have is a customized version of the (language helper)[http://www.codeigniter.com/user_guide/helpers/language_helper.html].

When starting with your own controller you can extend MY_Controller, which offers ``` $this->display($view, $data)```
Mentioned method will render the full page, including header, footer and resources for you, so that work is already done.
During runtime you can modify the view properties like:
```php
$this->title = "Some new title";
$this->meta->author = "Fohlen";
```
