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
    - Obsazenost mítnosti (při editaci místnosti, m při vytváření předmětu)
    - V jednu dobu nemůže mít učitel dva předměty
- 3 procedury
    - Vymazat vyučování učitele
    - 
- 2 triggery
    - Když se zmenší místnost, zmenšit cvičení (?)
    - Když se smaže učitel, smazat fotku a učení a uživatele.
    - Změna fotky učitele smaže předešlou fotku.
- ✓ Binární obsah (speciální tabulka, info jako typ, soubor, ...)
- 2 formuláře (ošetření polí)
    - U rozvrhu
    - Registrace a úprava uživatelů (fotky)