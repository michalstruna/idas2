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