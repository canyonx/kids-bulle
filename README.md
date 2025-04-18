# Planning Kid's Bulle 

Application d'inscription des enfants aux activités du club de ski Kid's Bulle de Peyragudes


## Installer 
* Installer et créer les containers Docker
`docker compose up -d --build`

* Se connecter au container app
`docker exec -it kids-bulle-app-1 bash`

* Installer les dépendances php
`composer install`

* Installer et compiler le front
`npm install`
`npm run build`


## Accéder à la base de donnée Mysql
`docker exec -it kids-bulle-app-1 bash`

`mysql -u symfony -p`
MDP: symfony

```
USE kids-bulle;
SHOW tables;
SELECT * FROM user;
```