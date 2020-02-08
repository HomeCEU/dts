# Setup instructions
## Using Docker
```bash
git clone git@github.com:HomeCEU/dockerapp.git dts
cd dts
./app.sh config set GIT_REPO git@github.com:HomeCEU/dts.git
./app.sh config set APP_CONTAINER dts
./app.sh init
cd .docker
docker-compose build
docker-compose up
```
http://localhost:8080

if you wish you can customize the exposed port with

```bash
./app.sh config set APP_PORT 8080
```

or just edit config yourself.