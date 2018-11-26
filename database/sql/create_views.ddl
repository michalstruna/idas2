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