# Card catalog

```
Symfony Card catalog API with Vue.js front end
Comes with two versions:
    - with Api Platform 2.6 library
    - without Api Platform 2.6 library
```

## clone repository
```
git clone git@github.com:danielwitas/catalog.git
cd catalog/
```
## select version from two branches
```
git checkout api-platform
```
## or
```
git checkout no-api-platform
```
## Init docker setup
```
sh install.sh
```
## Build
```
docker-compose up
```
### Runs tests
```
docker-compose run --rm php74-service php vendor/bin/phpunit
```

### Catalog webpage
See [http://localhost:8081](http://localhost:8081)

### Api-platform version docs
See [http://localhost:8080/api/docs](http://localhost:8080/api/docs)

### Non API Platform version docs html
See [http://localhost:8080/api/docs](http://localhost:8080/api/docs)

### Non API Platform version docs json
```
/app/docs/docs.json
```
### Non API Platform version docs yaml
```
/app/docs/docs.yaml
```

### Troubleshooting
```
If for some reason automatic setup installation fails
here are some handy commands to run:

To run composer install manually:
docker-compose run --rm php74-service composer install

To create database manually:
docker-compose run --rm php74-service php bin/console doctrine:database:create

To create database schema manually:
docker-compose run --rm php74-service php bin/console doctrine:schema:update --force

To load application fixtures manually:
docker-compose run --rm php74-service php bin/console doctrine:fixtures:load -q

To create database manually for test envirnoment:
docker-compose run --rm php74-service php bin/console doctrine:database:create --env=test

To create database schema manually for test envirnoment:
docker-compose run --rm php74-service php bin/console doctrine:schema:update --force --env=test

To run tests manually
docker-compose run --rm php74-service php vendor/phpunit

If all fails. You can try to remove all unused containers, networks,
images (both dangling and unreferenced), and optionally, volumes. Run:
docker system prune
And try again... or just go and watch some netflix :).
```
