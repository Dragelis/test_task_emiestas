# Testinės užduoties sprendimas.

## Reikalavimai
Reikalingi NodeJS (>=11.15.0), Yarn (>=1.17.3), Docker ir docker-compose programiniai paketai. Projektui paleisti rekomenduojama **Debian** distribucija arba **OS X**.

## Diegimo instrukcija

1. Pakoreguoti `.env` faile esantį kelią iki šio projekto `SYMFONY_APP_PATH`.

2. Sugeneruoti ir paleisti Docker konteinerius:

    ```bash
    $ cd SYMFONY_APP_PATH # Kelias iki šio projekto.
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
    $ cd SYMFONY_APP_PATH # Kelias iki šio projekto.
    $ yarn install
    ```

6. Sugeneruoti projektui galutinius JS/CSS failus:

    ```bash
    $ cd SYMFONY_APP_PATH # Kelias iki šio projekto.
    $ yarn encore production
    ```

7. Apsilankyti [symfony.local](http://symfony.local) puslapyje.

8. _(Neprivalomas)_ Norėdami prie sistemos pridėti administratorių pasinaudokite šiomis komandomis:

    ```bash
    $ docker-compose exec mysql bash
    $ mysql -u symfony_test_user -p symfony_test
    $ # Įveskite "symfony_test_password" (be kabučių).
    $ UPDATE `user` SET `roles` = '["ROLE_ADMIN"]' WHERE `id` = 1; # 1 pakeiskite į norimo vartotojo ID.
    $ exit
    $ exit
    ```

  Sistema palaiko _CSV_ failų importavimą. Pavyzdinė failo struktūra:

    Participant,Opponent,Starts On,Winner
    Jonas,Jonaitis,"2019-08-15 20:24",,
    Vardenis,Pavardenis,"2019-09-04 12:25",,
    Komanda 1,Komanda 2,"2004-02-01 12:25",Komanda 1
    Komanda 3,Komanda 4,"2009-09-09 01:36",Komanda 4
