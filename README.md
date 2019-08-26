# LZZ
LZZ is a URL shortener that allows changing target URL's after they have been created.
# Installation
* Create a MySQL database with `utf8_general_ci` collation.
* Specify your database credentials in `dbconfig.php`.
* Create a Google Recaptcha account and update the keys in `account/login.php` and `account/register.php`.
  Replace `<google recaptcha public key>` with the public key.
  Replace `<google recaptcha secret key>` with the secret key.
* Upload the source code to the host directory with the ui. Delete `link.php`.
* Upload `link.php` to the short url host directory.
* Adjust locations in `link.php`.
* Generate a 128-byte key and replace `<128-byte key>` in `includes/classes/Account.php` with it.
# Web server configuration
## nginx
Main website example configuration.
```
server {
	listen 80;
	listen [::]:80;

	root /var/www/<base_url>/html;
	index index.php index.html index.htm;
	server_name <base_url>;

	location / {
			try_files $uri $uri.html $uri/ @extless-php;
	}

	location @extless-php {
			rewrite ^(.*)$ $1.php last;
	}

	location ~ \.php$ {
			include snippets/fastcgi-php.conf;

			fastcgi_split_path_info ^(.+\.php)(/.*)$;
			fastcgi_pass unix:/run/php/php7.0-fpm.sock;
	}

	location ~* \.(pl|cgi|py|sh|lua)\$ {
			return 403;
	}

	location ~* (roundcube|webdav|smtp|http\:|soap|w00tw00t) {
			return 403;
	}
}
```
Short url website example configuration:
```
server {
	listen 80;
	listen [::]:80;

	root /var/www/<short_url>/html;
	index index.php index.html index.htm;
	server_name <short_url>;

	rewrite ^/(.*)$ /link?id=$1 last;

	location / {
			try_files $uri $uri.html $uri/ @extless-php;
	}

	location @extless-php {
			rewrite ^(.*)$ $1.php last;
	}

	location ~ \.php$ {
			include snippets/fastcgi-php.conf;

			# fastcgi_pass 127.0.0.1:9000;
			fastcgi_split_path_info ^(.+\.php)(/.*)$;
			fastcgi_pass unix:/run/php/php7.0-fpm.sock;
	}

	location ~* \.(pl|cgi|py|sh|lua)\$ {
			return 403;
	}

	location ~* (roundcube|webdav|smtp|http\:|soap|w00tw00t) {
			return 403;
	}
}
```
# Project status
**This open source version of LZZ url shortener is not maintained anymore.**
Only security fixes will be commited.
Feel free to fork this repo.
# License
Copyright (c) 2017-2019 Alexander Mayorov. All rights reserved.

This project is licensed under the MIT license.
If you are using the code in this repository, please leave a copyright notice.
See the LICENCE file for more details.
