<p align="center">
    <a href="https://legacy-roleplay.com" target="blank">
        <img src="https://github.com/coalaura/opfw-admin/raw/master/.github/opfw-logo.png" height="150px" width="150px" alt="OP-FW Logo" />
    </a>
</p>

<h1 align="center">
    opfw-admin
</h1>

<strong>100% FREE + OPEN SOURCE.</strong>

> A web interface to help with administrative duties at the FiveM server: Legacy Roleplay. Written in PHP using [Laravel Framework](https://laravel.com/) and
> [Tailwindcss](https://tailwindcss.com) for the frontend.

See [#contributing](#Contributing) for more details on how you can help shape **opfw-admin**. We're always down to improve and receive feedback.

## Note

It is recommended that you use Firefox, Chrome or Edge as your web browser for the best experience.

## Features
* See and search server logs.
* See and search players.
* Warn and ban players, also temporarily.
* See and edit characters.
* And many more detailed features...
* [*... open issue to request a feature.*](https://github.com/coalaura/opfw-admin/issues/new/choose)

## License
Please refer to [LICENSE.md](https://github.com/coalaura/opfw-admin/blob/master/LICENSE.md) for this project's license.

## Contributors
This list only contains some of the most notable contributors. For the full list, refer to [GitHub's contributors graph](https://github.com/coalaura/opfw-admin/graphs/contributors).
* [ExpDev07](https://github.com/ExpDev07) (Marius) - Original creator.
* [coalaura](https://github.com/coalaura) (Laura) - Maintainer of both frontend and backend.

## Pictures

### Logging in
![Logging in](.github/screenshots/logging_in.PNG)

### Dashboard
![Dashboard](.github/screenshots/dashboard.PNG)

### Players
![Players](.github/screenshots/players.PNG)

### Viewing player
![View Player](.github/screenshots/player.PNG)

### Characters
![Characters](.github/screenshots/characters.PNG)

### Viewing character
![View Character](.github/screenshots/character.PNG)

### Server Logs
![Logs](.github/screenshots/logs.PNG)

### Panel Logs
![Panel Logs](.github/screenshots/panel_logs.PNG)

### Server List
![Server List](.github/screenshots/servers.PNG)

### Live-Map
![Live-Map](.github/screenshots/livemap.png)

### New Players
![New Players](.github/screenshots/new_players.png)

## Contributing
This section describes how you can help contribute.

### Prerequisites
* An OPFW-Server.
* PHP 8.0+.
* Composer.
* Node (and npm).
* MySQL/MariaDB (database).
* Optimally nginx

### Setting up project
Grab yourself a copy of this repository:
```bash
$ git clone https://github.com/coalaura/opfw-admin.git
```

Install all the required dependencies (we use both npm and composer):
```bash
$ composer install
$ npm install
```

Create a new file called ``envs/c1/.env`` and copy the contents from ``.env.example`` over to it, then apply your configurations. (Replace "c1" with your cluster)
```bash
$ cp .env.example envs/c1/.env
```

Create a private and unique application key:
```bash
$ php artisan key:generate
```

Run database migrations so that we can store things:
```bash
$ php artisan migrate
```

Create a symbolic link at **public/storage** so that it points to **storage/app/public**:
```bash
$ php artisan storage:link
```

Install, configure and run the socket server from [admin-panel-socket](https://github.com/coalaura/admin-panel-socket)

Compile frontend assets (use "dev" for development and "prod" for production):
```bash
$ npm run dev/prod
```

Finally, set up nginx:
```nginx
server {
    # Replace this with your domain, important is that the subdomain is the exact same as your cluster.
    # The panel uses the subdomain to determine what .env file should be used.
	server_name c1.legacy-roleplay.com;

    # Change this to point to the "public" folder inside the opfw-admin repository
	root /path/to/opfw-admin/public;

	index index.php index.html index.htm;

	location / {
		try_files $uri $uri/ /index.php$is_args$args;
	}

    # This would be the socket server's configuration.
    # If you are not running it on the standard port you will have to change it here.
	location ~ ^/(historic|timestamp|socket\.io) {
		proxy_pass http://127.0.0.1:9999;
		proxy_http_version 1.1;
		proxy_set_header Upgrade $http_upgrade;
		proxy_set_header Connection "Upgrade";
		proxy_set_header Host $host;
	}

    # If you are not using php8.2 you need to replace "php8.2-fpm" with the correct version
	location ~ \.php$ {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass unix:/run/php/php8.2-fpm.sock;
		fastcgi_index index.php;
		include fastcgi.conf;
	}

    # This is depending on how you are setting up ssl certificates. This example would be using lets-encrypt
	listen 443 ssl;

	ssl_certificate /etc/letsencrypt/live/c1.legacy-roleplay.com/fullchain.pem;
	ssl_certificate_key /etc/letsencrypt/live/c1.legacy-roleplay.com/privkey.pem;
	include /etc/letsencrypt/options-ssl-nginx.conf;
	ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}
```
