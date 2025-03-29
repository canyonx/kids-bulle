# kids-bulle

* Installer et créer les containers Docker
`docker compose up -d --build`

* Se connecter au container app
`docker exec -it kids-bulle-app-1 bash`

* Installer les dépendances php
`composer install`

* Installer et compiler le front
`npm install`
`npm run build`

* Lancer le serveur web depuis le conteneur kids-bulle-app-1 
`php -S 0.0.0.0:8000 -t public > /dev/null 2>&1 &`


## Accéder à la base de donnée Mysql
`docker exec -it kids-bulle-app-1 bash`

`mysql -u symfony -p`
MDP: symfony

```
USE kids-bulle;
SHOW tables;
SELECT * FROM user;
```