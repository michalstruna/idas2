IDAS2
=================

Dokumentace v /database/
- idas2 documentation.docx - výstupní zpráva
- model/ - databázový model v OracleDataModeler
- sql/ - SQL potřebné k vytvoření databáze, naplnění daty a dalších věcí (views, PL/SQL)

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
- ✓ 2 funkce různého typu
    - Bool-to-string (pouzito pro vypis zda je uzivatel admin).
    - Převod datumu na den v týdnu
    - Kontrola volne mistnosti, zaneprazdnenosti studijniho planu.
- ✓ 3 procedury
    - Import (3x + package)
    - SEM_NASTAV_ZPUS_VYUKY
    - SEM_VYMAZ_MISTNOST
- ✓ 2 triggery
    - Když se smaže učitel, smazat fotku.
    - Změna fotky učitele smaže předešlou fotku.
    - Když nastavím rozvrhovou akci na přesné datum, tak vypočítám den v týdnu.
- ✓ Binární obsah (speciální tabulka, info jako typ, soubor, ...)
- ✓ 2 formuláře (ošetření polí)
    - U rozvrhu
    - Registrace a úprava učitelů (fotky)

IWWW
=================
Dokumentace v /iwww