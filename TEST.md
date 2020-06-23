# Vaizdo kodekas.

## Reikalavimai
Reikalingi „NodeJS“ (>=11.4.0), „Npm“ (>=6.4.1), [„FFmpeg“] (>=4.2.2) programiniai paketai. Projektui paleisti rekomenduojamos „**Windows 10**“ arba „**macOS**“ operacinės sistemos.

## Paleidimo instrukcija

1. Įdiegti reikalingas projekto „NodeJS“ bibliotekas:

    ```bash
    $ cd SRC_PATH # Kelias iki išeities kodo aplanko.
    $ npm i
    $ npm install --only=dev
    ```

2. Atlikti vaizdo kodeko automatinius testus:

    ```bash
    $ npm run test
    ```

3. Įsitikinti ar vaizdo kodekas grąžina bakalauro baigiamajame darbe aprašytas komandas:

    ```bash
    $ node codec.js --help
    ```

4. Vaizdo kodeko glaudinimo etape reikalingas sukonvertuotas Y′UV 4:2:0 tipo vaizdo įrašas. Vaizdo įrašą galite susirasti „tests“ aplanke arba [internete](http://ultravideo.cs.tut.fi).
Susiradus vaizdo įrašą jums reikia jį sukonvertuoti taip, kad vienas aplanko failas būtų sudarytas iš vieno vaizdo kadro. Šio reikalavimo įgyvendinimui galite pasinaudoti „convert_yuv_file_to_frames.bat“ faile esančia komanda.
Šiai komandai atlikti reikalingas „FFmpeg“ programinis paketas.
