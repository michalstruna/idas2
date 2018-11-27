CREATE OR REPLACE VIEW sem_p_katedra AS
SELECT sem_katedra.*, sem_fakulta.zkratka as "fakulta"
FROM sem_katedra
JOIN sem_fakulta
ON sem_katedra.fakulta_id = sem_fakulta.id;

CREATE OR REPLACE VIEW SEM_P_UCITEL AS
SELECT sem_ucitel.*, (titul_pred || ' ' || jmeno || ' ' || prijmeni || ' ' || titul_za) as "dlouhe_jmeno", sem_katedra.zkratka as "katedra"
FROM sem_ucitel
JOIN SEM_KATEDRA ON sem_katedra.id = SEM_UCITEL.katedra_id;

CREATE OR REPLACE VIEW SEM_P_PREDMET AS
SELECT sem_predmet.*, sem_zpus_zak.nazev as "zpusob_zakonceni", sem_forma_vyuky.nazev as "forma_vyuky"
FROM SEM_PREDMET
JOIN sem_zpus_zak ON sem_zpus_zak.ID = sem_predmet.zpusob_zakonceni_id
JOIN sem_forma_vyuky ON SEM_FORMA_VYUKY.id = sem_predmet.forma_vyuky_id;

CREATE OR REPLACE VIEW SEM_P_PREDM_OBOR AS
SELECT SEM_PREDM_OBOR.*, sem_kategorie.nazev as "kategorie", sem_obor.nazev as "obor", sem_predmet.nazev as "predmet"
FROM SEM_PREDM_OBOR
JOIN SEM_KATEGORIE ON SEM_KATEGORIE.ID = SEM_PREDM_OBOR.KATEGORIE_ID
JOIN sem_obor ON sem_obor.ID = SEM_PREDM_OBOR.obor_id
JOIN sem_predmet ON sem_predmet.ID = SEM_PREDM_OBOR.predmet_id
ORDER BY sem_obor.nazev, sem_predmet.nazev, sem_kategorie.nazev, rocnik;

CREATE OR REPLACE VIEW SEM_P_ZPUS_PREDM AS
SELECT SEM_ZPUS_PREDM.*, SEM_ZPUS_VYUKY.nazev as "zpusob_vyuky", (SEM_OBOR.NAZEV || ' ' || SEM_PREDMET.NAZEV) as "predm_obor"
FROM SEM_ZPUS_PREDM
JOIN SEM_ZPUS_VYUKY ON SEM_ZPUS_VYUKY.id = SEM_ZPUS_PREDM.zpusob_vyuky_id
JOIN SEM_PREDM_OBOR ON SEM_PREDM_OBOR.id = SEM_ZPUS_PREDM.predm_obor_id
JOIN SEM_OBOR ON SEM_OBOR.id = SEM_PREDM_OBOR.obor_id
JOIN SEM_PREDMET ON SEM_PREDMET.id = SEM_PREDM_OBOR.predmet_id
ORDER BY sem_obor.nazev, sem_predmet.nazev, sem_zpus_vyuky.nazev;

CREATE OR REPLACE VIEW SEM_P_UCI AS
SELECT SEM_UCI.*, (SEM_UCITEL.JMENO || ' ' || SEM_UCITEL.PRIJMENI) as "ucitel", SEM_ROLE.NAZEV as "role", (SEM_OBOR.NAZEV || ' - ' || SEM_PREDMET.NAZEV) as "predmet"
FROM SEM_UCI
JOIN SEM_UCITEL ON SEM_UCITEL.ID = SEM_UCI.UCITEL_ID
JOIN SEM_ROLE ON SEM_ROLE.ID = SEM_UCI.ROLE_ID
JOIN SEM_PREDM_OBOR ON SEM_PREDM_OBOR.ID = SEM_UCI.PREDM_OBOR_ID
JOIN SEM_PREDMET ON SEM_PREDMET.ID = SEM_PREDM_OBOR.PREDMET_ID
JOIN SEM_OBOR ON SEM_OBOR.ID = SEM_PREDM_OBOR.OBOR_ID;

CREATE OR REPLACE VIEW SEM_P_UZIVATEL AS
SELECT SEM_UZIVATEL.ID as "id", email, admin, (SEM_UCITEL.JMENO || ' ' || SEM_UCITEL.PRIJMENI) as "ucitel"
FROM SEM_UZIVATEL
JOIN SEM_UCITEL ON SEM_UCITEL.ID = SEM_UZIVATEL.UCITEL_ID
ORDER BY email;