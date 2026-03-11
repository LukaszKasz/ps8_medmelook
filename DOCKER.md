# MedMeLook Docker

## Cel

Ten plik opisuje kompletne środowisko developerskie dla sklepu `PrestaShop 8.1.2` uruchamianego w Dockerze razem z bazą danych i `phpMyAdmin`.

Celem tej konfiguracji jest to, żeby przy kolejnym stawianiu środowiska nie trzeba było od nowa ustalać:

- jaką wersję PHP i MariaDB dobrać
- jak zaimportować dump
- jak ustawić domenę sklepu po imporcie
- dlaczego obrazki produktów nie działały lokalnie
- jak dodać `ionCube Loader`
- jak uruchomić edytowalny kod bez kopiowania go do obrazu

## Co jest w repo

### Aplikacja

- kod PrestaShop jest w katalogu projektu i jest montowany do kontenera jako bind mount
- dzięki temu pliki edytujesz normalnie na dysku hosta, a nie wewnątrz obrazu Dockera

### Dump bazy

- plik dumpa: `medmelook_2026-03-11_14-55.sql.gz`
- dump pochodzi z `MariaDB 10.11`
- baza docelowa: `lkasztel_medmelook`

### Docker

Najważniejsze pliki:

- `docker-compose.yml`
- `docker/php/Dockerfile`
- `docker/php/php.ini`
- `docker/php/000-default.conf`
- `docker/db/init/20-shop-url.sql`
- `.env`

## Architektura kontenerów

### 1. `web`

Kontener aplikacji:

- obraz własny zbudowany z `php:8.1-apache`
- działa na `Apache + PHP 8.1`
- ma doinstalowane rozszerzenia potrzebne Preście:
  - `bcmath`
  - `gd`
  - `intl`
  - `mysqli`
  - `opcache`
  - `pdo_mysql`
  - `soap`
  - `xsl`
  - `zip`
- ma doinstalowany `ionCube Loader`
- kod sklepu jest montowany z hosta do `/var/www/html`

### 2. `db`

Kontener bazy:

- obraz `mariadb:10.11`
- trwałe dane są trzymane w wolumenie `db_data`
- przy pierwszym starcie wykonuje:
  1. utworzenie bazy
  2. utworzenie użytkownika
  3. import dumpa `.sql.gz`
  4. aktualizację domeny sklepu i ustawień SSL przez `20-shop-url.sql`

### 3. `phpmyadmin`

Kontener do obsługi bazy przez GUI:

- obraz `phpmyadmin:5-apache`
- łączy się do kontenera `db`
- wystawiony na porcie `8081`

## Adresy usług

Przy obecnych ustawieniach:

- sklep: `http://192.168.30.100:8080`
- phpMyAdmin: `http://192.168.30.100:8081`

Jeśli host ma inny adres IP, usługi nadal działają na tych portach hosta:

- sklep: `http://HOST:8080`
- phpMyAdmin: `http://HOST:8081`

## Dane dostępowe

### MariaDB

- baza: `lkasztel_medmelook`
- użytkownik: `lkasztel_medmelook`
- hasło: `Olunia240120)^`
- root password: `Olunia240120)^`

### phpMyAdmin

Możesz logować się:

- użytkownik: `lkasztel_medmelook`
- hasło: `Olunia240120)^`

albo rootem:

- użytkownik: `root`
- hasło: `Olunia240120)^`

## Dlaczego takie wersje

### PrestaShop

Projekt to:

- `PrestaShop 8.1.2`

Wersja została potwierdzona w:

- `src/Core/Version.php`

### PHP

Dla tego środowiska użyte zostało:

- `PHP 8.1`

Powody:

- zgodność z PrestaShop 8.1
- dostępność `ionCube Loader` dla PHP 8.1
- bezpieczny kompromis między kompatybilnością a nowością

### MariaDB

Użyta została:

- `MariaDB 10.11`

Powód:

- dump został wykonany z `MariaDB 10.11.16`
- minimalizuje to ryzyko różnic w składni i zachowaniu silnika

## Co zostało zmienione w aplikacji

### 1. Host bazy w Preście

W pliku:

- `app/config/parameters.php`

zmieniono:

- `database_host` z `localhost` na `db`
- `database_port` na `3306`

To jest konieczne, bo w Dockerze Presta łączy się do bazy po nazwie usługi, a nie przez lokalny socket.

### 2. Ustawienie domeny po imporcie

Po imporcie dumpa wykonywany jest:

- `docker/db/init/20-shop-url.sql`

Skrypt ustawia:

- `ps_shop_url.domain`
- `ps_shop_url.domain_ssl`
- `PS_SHOP_DOMAIN`
- `PS_SHOP_DOMAIN_SSL`

na:

- `192.168.30.100:8080`

### 3. Wyłączenie SSL lokalnie

W tym samym skrypcie ustawiane jest:

- `PS_SSL_ENABLED = 0`
- `PS_SSL_ENABLED_EVERYWHERE = 0`

Powód:

- po imporcie sklep próbował wymuszać `https://192.168.30.100:8080`
- lokalnie nie było skonfigurowanego certyfikatu i to utrudniało uruchomienie

### 4. Naprawa `virtual_uri`

Po imporcie `ps_shop_url.virtual_uri` powodowało błędne końcówki typu `//`.

Zostało ustawione na pusty string:

- `virtual_uri = ''`

To naprawiło błędne przekierowania na adresy kończące się `//`.

### 5. Naprawa `.htaccess` dla obrazów produktów

To była jedna z ważniejszych pułapek.

#### Problem

Obrazy produktów nie działały lokalnie, mimo że pliki były fizycznie obecne w:

- `img/p/...`

Przykład istniejących plików:

- `img/p/1/7/0/7/1707-home_default.jpg`
- `img/p/1/8/0/3/1803-home_default.jpg`

#### Przyczyna

W `.htaccess` reguły rewrite dla zdjęć działały tylko dla:

- `medmelook.pl`

czyli miały warunek:

- `RewriteCond %{HTTP_HOST} ^medmelook.pl$`

Przy wejściu przez:

- `192.168.30.100:8080`

reguły nie uruchamiały się, więc przyjazne URL obrazków trafiały na `404`.

#### Naprawa

Warunki `RewriteCond` zostały rozszerzone tak, aby akceptowały też:

- `medmelook.pl`
- `www.medmelook.pl`
- `192.168.30.100`
- `192.168.30.100:8080`
- `localhost`
- `localhost:8080`
- `127.0.0.1`
- `127.0.0.1:8080`

To pozwala używać friendly image URL zarówno lokalnie, jak i produkcyjnie.

### 6. Logowanie błędów PHP

W pliku:

- `docker/php/php.ini`

ustawiono:

- `display_errors = Off`
- `log_errors = On`
- `error_log = /proc/self/fd/2`

Powód:

- błędy PHP trafiają bezpośrednio do `docker compose logs web`
- przy kolejnych awariach nie trzeba zgadywać, co padło

## ionCube Loader

`ionCube Loader` został dodany do obrazu PHP.

Instalacja odbywa się w:

- `docker/php/Dockerfile`

Proces:

1. pobranie archiwum z oficjalnej strony ionCube
2. wypakowanie loadera dla `PHP 8.1`
3. skopiowanie pliku `.so` do katalogu rozszerzeń PHP
4. utworzenie `00-ioncube.ini` z `zend_extension=...`

Loader został zweryfikowany przez:

- `php -v`

## phpMyAdmin

`phpMyAdmin` został dodany jako osobny serwis do `docker-compose.yml`.

### Połączenie

- host bazy: `db`
- port bazy: `3306`

### Adres

- `http://192.168.30.100:8081`

### Zastosowanie

Do szybkiej pracy z bazą bez ręcznego używania klienta MariaDB.

## Jak uruchomić środowisko od zera

### Pierwsze uruchomienie lub pełne odtworzenie

```bash
docker compose down -v
docker compose up -d --build
```

To zrobi:

1. usunięcie kontenerów
2. usunięcie wolumenu bazy
3. przebudowę obrazu `web`
4. uruchomienie `db`
5. import dumpa
6. uruchomienie `web`
7. uruchomienie `phpmyadmin`

## Jak uruchomić bez kasowania bazy

```bash
docker compose up -d
```

## Jak przebudować tylko PHP/Apache

```bash
docker compose up -d --build web
```

Przydaje się po zmianach w:

- `docker/php/Dockerfile`
- `docker/php/php.ini`
- `docker/php/000-default.conf`

## Jak podejrzeć logi

### Web

```bash
docker compose logs -f web
```

### Baza

```bash
docker compose logs -f db
```

### phpMyAdmin

```bash
docker compose logs -f phpmyadmin
```

## Jak sprawdzić status kontenerów

```bash
docker compose ps
```

## Jak wejść do kontenera

### Web

```bash
docker compose exec web bash
```

### DB

```bash
docker compose exec db bash
```

## Jak połączyć się do bazy z CLI

```bash
docker compose exec db mariadb -u root -p'Olunia240120)^' lkasztel_medmelook
```

## Jak ręcznie wykonać SQL po imporcie

Przykład:

```bash
docker compose exec db mariadb -u root -p'Olunia240120)^' lkasztel_medmelook -e "SELECT NOW();"
```

## Jak odświeżyć cache Presty

Jeżeli po zmianach w konfiguracji coś zachowuje się dziwnie:

```bash
docker compose exec web sh -lc 'rm -rf var/cache/* cache/*'
```

## Co było problematyczne przy stawianiu tego środowiska

### 1. `database_host=localhost`

Nie działa w Dockerze.

Musi być:

- `database_host=db`

### 2. Wymuszenie HTTPS po imporcie

Dump miał ustawienia domeny/SSL, które nie pasowały do lokalnego środowiska.

Bez poprawki sklep przekierowywał błędnie.

### 3. `virtual_uri`

Powodowało URL z podwójnym slashem `//`.

### 4. Obrazy produktów

Friendly URL obrazów były uzależnione od hosta `medmelook.pl` w `.htaccess`.

Na lokalnym IP nie działały.

### 5. Bindowanie portu tylko do jednego IP

Docker na tej maszynie nie chciał wystawić kontenera wyłącznie na:

- `192.168.30.100:8080`

Dlatego port został wystawiony na wszystkich interfejsach hosta:

- `0.0.0.0:8080`
- `0.0.0.0:8081`

Jeśli host ma adres `192.168.30.100`, usługi i tak są osiągalne pod:

- `http://192.168.30.100:8080`
- `http://192.168.30.100:8081`

## Najważniejsze komendy robocze

### Start

```bash
docker compose up -d --build
```

### Restart tylko aplikacji

```bash
docker compose restart web
```

### Restart wszystkiego

```bash
docker compose restart
```

### Pełne odtworzenie bazy z dumpa

```bash
docker compose down -v
docker compose up -d --build
```

### Podgląd logów web

```bash
docker compose logs -f web
```

### Podgląd logów db

```bash
docker compose logs -f db
```

### Sprawdzenie `ionCube`

```bash
docker compose exec web php -v
```

### Sprawdzenie statusu usług

```bash
docker compose ps
```

## Na co uważać w przyszłości

### Regenerowanie `.htaccess` przez Prestę

Presta potrafi nadpisać `.htaccess`.

Jeżeli po jakiejś operacji z panelu admina znowu przestaną działać obrazki produktów lokalnie, sprawdź czy w sekcji `# Images` nie wróciły warunki tylko dla:

- `medmelook.pl`

Jeśli wrócą, trzeba ponownie rozszerzyć `RewriteCond` dla hostów lokalnych.

### Reset wolumenu bazy

Jeżeli zrobisz:

```bash
docker compose down -v
```

to stracisz bieżący stan bazy z kontenera i przy kolejnym starcie znowu zaimportuje się dump startowy.

## Podsumowanie

Stan docelowy środowiska:

- PrestaShop 8.1.2
- PHP 8.1 + Apache
- ionCube Loader aktywny
- MariaDB 10.11
- phpMyAdmin na porcie `8081`
- sklep na porcie `8080`
- kod montowany z hosta
- baza importowana automatycznie z dumpa przy pierwszym starcie
- lokalna domena ustawiona na `192.168.30.100:8080`
- obrazki produktów działają lokalnie po poprawce `.htaccess`
