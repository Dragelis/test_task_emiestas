# Testinės užduoties sprendimas.

## Reikalavimai
Reikalingi NodeJS (>=11.15.0), Yarn (>=1.17.3), Docker ir docker-compose programiniai paketai.

## Diegimo instrukcija

1. Pakoreguoti `.env` faile esantį kelią iki šio projekto `SYMFONY_APP_PATH`.

2. Sugeneruoti ir paleisti Docker konteinerius:

    ```bash
    $ cd SYMFONY_APP_PATH
    $ docker-compose build
    $ docker-compose up -d
    ```

3. Pakoreguoti savo operacinės sistemos `hosts` failą:

    ```bash
    # UNIX only: get containers IP address and update host (replace IP according to your configuration) (on Windows, edit C:\Windows\System32\drivers\etc\hosts)
    $ sudo echo $(docker network inspect bridge | grep Gateway | grep -o -E '([0-9]{1,3}\.){3}[0-9]{1,3}') "symfony.local" >> /etc/hosts
    ```

    **Pastaba:** Daugiau informacijos **OS X** operacinei sistemai rasite [čia](https://docs.docker.com/docker-for-mac/networking/), o **Windows** operacinei sistemai [čia](https://docs.docker.com/docker-for-windows/#/step-4-explore-the-application-and-run-examples).

4. Įdiegti reikalingas projekto PHP bibliotekas ir paruošti sistemą paleidimui:

    ```bash
    $ docker-compose exec php bash
    $ composer install
    $ sf4 doctrine:schema:update --force
    $ composer dump-env prod
    $ exit
    ```

5. Įdiegti reikalingas projekto JS bibliotekas:

    ```bash
    $ cd SYMFONY_APP_PATH
    $ yarn install
    ```

6. Sugeneruoti projektui galutinius JS/CSS failus:

    ```bash
    $ cd SYMFONY_APP_PATH
    $ yarn encore production
    ```

7. Apsilankyti [symfony.local](http://symfony.local) puslapyje.
