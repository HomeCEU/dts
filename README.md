[![GitHub release (latest by date)](https://img.shields.io/github/v/release/HomeCEU/dts.svg?include_prereleases)](https://github.com/HomeCEU/dts/releases)
[![Travis CI](https://img.shields.io/travis/homeceu/dts/master.svg)](https://travis-ci.com/github/HomeCEU/dts)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/logikostech/util/master/LICENSE)


# Setup instructions
## Using Docker
```shell script
git clone git@github.com:HomeCEU/dockerapp.git dts
cd dts
git checkout dts
./app.sh config set GIT_REPO git@github.com:HomeCEU/dts.git
./app.sh config set APP_CONTAINER dts
./app.sh init
cd .docker
docker-compose build
docker-compose up
cd ..
./app.sh exec phinx migrate
```

If you want a sample template and data, run
```shell script
./app.sh exec phinx seed:run -s SampleSeeder
```
http://localhost:8080

if you wish you can customize the exposed port with

```bash
./app.sh config set APP_PORT 8080
```

or just edit config yourself.

## command exec
You can execute commands in the container from the outside

```bash
./app.sh composer update
```

also vendor/bin is in `$PATH` so you can

```bash
# run phpunit
./app.sh exec phpunit

# create a migration
./app.sh exec phinx create MyMigration
````
