# ViDIX

## Technical requirements

This Docker stack is based on Dunglas Symfony Docker project. The original `README.md` file was renamed to [INSTALL](INSTALL.md) (have a look if you need more details on it).

## First run of this project

After cloning this project, move into it via a Terminal console and run:
```bash
# If `make` command is available on the host machine
make start
```
or
```bash
# If you cannot use `make`
docker compose build --pull --no-cache
docker compose up
```

Open another Terminal console and run:
```bash
# If `make` command is available on the host machine
make dfl
```
or
```bash
# If you cannot use `make`
docker compose exec -ti php php bin/console doctrine:fixtures:load --no-interaction
```
This will load fake data into database to start using project quickly.

## Open a browser

To see this project, go to http://localhost/ or any other FQDN you want (pointing `127.0.0.1` in your `/etc/hosts` file)

## Shut down stack

To stop this project, go to the root folder of this project in a Terminal console of the host machine and run:
```bash
docker compose down --remove-orphans
```
