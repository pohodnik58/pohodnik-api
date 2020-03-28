# pohodnik-api
api for pohodnik

### Настройка проекта :hourglass_flowing_sand:	
> **выполняется один раз перед запуском проекта**
> или по мере надобности 
 1. `startHosts_ONCE.bat` или добавть запись `127.0.0.1 pohodnik.tk` в hosts, чтобы pohodnik.tk открывался локальным
 1. `startCerts_ONCE.bat` для генерации SSH ключей (для работы https)

### Запуск проекта :rocket:
```bash
docker-compose up
```
или `start.bat`

после старта доступны:
* http://pohodnik.tk - http версия сайта
* https://pohodnik.tk - https версия сайта
* http://pohodnik.tk:8001 - phpMyAdmin (MySql database)

phpmyadmin pohodnik.tk:8001
