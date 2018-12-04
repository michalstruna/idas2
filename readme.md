IDAS2
=================

Požadavky
------------


- ✓ 10 tabulek
    - ...
- ✓ 2 číselníky
    - Role
    - Způsob výuky
- ✓ 3 sekvence
    - ID autoincrement (sekvence.NEXTVAL v insert na místo ID)
- ✓ 3 pohledy (logicky využité, různého typu)
- 2 funkce různého typu
    - Bool-to-string (pouzito pro vypis zda je uzivatel admin).
    - Převod datumu na den v týdnu (?)
    - Obsazenost mítnosti (při editaci místnosti, m při vytváření předmětu) (?)
- ✓ 3 procedury
    - Import (3x + package)
    - (Vymazat vyučování učitele)
- ✓ 2 triggery
    - Když se smaže učitel, smazat fotku a učení a uživatele.
    - Změna fotky učitele smaže předešlou fotku.
    - Když nastavím rozvrhovou akci na přesné datum, tak vypočítám den v týdnu.
    - Když se zmenší místnost, zmenšit cvičení (?)
- ✓ Binární obsah (speciální tabulka, info jako typ, soubor, ...)
- ✓ 2 formuláře (ošetření polí)
    - U rozvrhu
    - Registrace a úprava učitelů (fotky)