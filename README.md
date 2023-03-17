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

It is recommended that you use Chrome or Edge as your web browser for the best experience.

## Features
* See and search server logs.
* See and search players.
* Warn and ban players, also temporarily.
* See and edit characters.
* [*... open issue to request a feature.*](https://github.com/coalaura/opfw-admin/issues/new/choose)

## Improvements from v1
1. Re-done to give a more minimalistic and "simple" look.
2. Backend improvements that encourage scaling and maintainability.
3. More.

## License
Please refer to [LICENSE.md](https://github.com/coalaura/opfw-admin/blob/master/LICENSE.md) for this project's license.

## Contributors
This list only contains some of the most notable contributors. For the full list, refer to [GitHub's contributors graph](https://github.com/coalaura/opfw-admin/graphs/contributors).
* [ExpDev07](https://github.com/ExpDev07) (Marius) - Original creator.
* [twooot](https://github.com/coalaura) (Laura) - Maintainer of both frontend and backend.

## Pictures
(more will be posted soon once some design decisions are finalized).

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

### Advanced
![Advanced](.github/screenshots/advanced.PNG)

### Suspicious Search
![Suspicious Search](.github/screenshots/suspicious.PNG)

## Contributing
This section describes how you can help contribute.

### Prerequisites
* PHP 7.4+.
* Composer.
* Node (and npm).
* SQL (database).

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

Create a new file called ``envs/.env`` and copy the contents from ``.env.example`` over to it, then apply your configurations.
```bash
$ cp .env.example envs/.env
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

Finally, boot the server up:
```bash
$ php artisan serve
```
