# Site Creator

A Laravel/Vue utility to convert an HTML website to a WordPress subsite.

## Requirements:
**Site Creator** was created in **Laravel version 9.x** and requires the following:

* **PHP** >= 8.1
* **Composer** >= 2.5.5
* **npm** >= 9.7.2

## Installation:
Install **Site Creator** locally with the following command:

`git clone git@github.com:mpemburn/site_creator.git`

Change to the `site_creator` directory and run:

`composer install`

...to install the PHP dependencies.

`npm install`

...to install modules needed to compile the JavaScript and CSS assets.

`npm run build`

...to do the asset compiling.

Copy `.env.example` to `.env` and make all necessary changes.

You will need to run a web server to run **Site Creator** in a browser.
I recommend [**Laravel Valet**](https://laravel.com/docs/10.x/valet), but you can do it simply by going to the project
directory and running:

`php artisan:serve`

This will launch a server on `http://127.0.0.1:8000`

### Register and Log in
To begin, you will need to create a user account. Click on the "**Register**" link
at the top right side of your browser page.

### Creating a WordPress Subsite
