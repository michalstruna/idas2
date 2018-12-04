CREATE OR REPLACE VIEW sem_p_katedra AS
SELECT sem_katedra.*, sem_fakulta.zkratka as "fakulta"
FROM sem_katedra
JOIN sem_fakulta ON sem_katedra.fakulta_id = sem_fakulta.id
ORDER BY sem_katedra.nazev;

CREATE OR REPLACE VIEW SEM_P_UCITEL AS
SELECT sem_ucitel.*, (titul_pred || ' ' || jmeno || ' ' || prijmeni || ' ' || titul_za) as "dlouhe_jmeno", sem_katedra.zkratka as "katedra"
FROM sem_ucitel
JOIN SEM_KATEDRA ON sem_katedra.id = SEM_UCITEL.katedra_id
ORDER BY sem_ucitel.prijmeni, sem_ucitel.jmeno;

CREATE OR REPLACE VIEW SEM_P_PREDMET AS
SELECT sem_predmet.*, sem_zpus_zak.nazev as "zpusob_zakonceni", sem_forma_vyuky.nazev as "forma_vyuky"
FROM SEM_PREDMET
JOIN sem_zpus_zak ON sem_zpus_zak.ID = sem_predmet.zpusob_zakonceni_id
JOIN sem_forma_vyuky ON SEM_FORMA_VYUKY.id = sem_predmet.forma_vyuky_id
ORDER BY sem_predmet.nazev;

CREATE OR REPLACE VIEW SEM_P_PREDM_PLAN AS
SELECT SEM_PREDM_PLAN.*, sem_kategorie.nazev as "kategorie", SEM_STUD_PLAN.nazev as "plan", sem_predmet.nazev as "predmet", sem_semestr.nazev as "semestr"
FROM SEM_PREDM_PLAN
JOIN SEM_KATEGORIE ON SEM_KATEGORIE.ID = SEM_PREDM_PLAN.KATEGORIE_ID
JOIN SEM_STUD_PLAN ON SEM_STUD_PLAN.ID = SEM_PREDM_PLAN.STUDIJNI_PLAN_ID
JOIN SEM_SEMESTR ON SEM_SEMESTR.ID = SEM_PREDM_PLAN.SEMESTR_ID
JOIN sem_predmet ON sem_predmet.ID = SEM_PREDM_PLAN.predmet_id
ORDER BY SEM_STUD_PLAN.nazev, sem_predmet.nazev, sem_kategorie.nazev, rocnik;

CREATE OR REPLACE VIEW SEM_P_ZPUS_PREDM AS
SELECT SEM_ZPUS_PREDM.*, SEM_ZPUS_VYUKY.nazev as "zpusob_vyuky", (SEM_STUD_PLAN.NAZEV || ' ' || SEM_PREDMET.NAZEV) as "PREDM_PLAN"
FROM SEM_ZPUS_PREDM
JOIN SEM_ZPUS_VYUKY ON SEM_ZPUS_VYUKY.id = SEM_ZPUS_PREDM.zpusob_vyuky_id
JOIN SEM_PREDM_PLAN ON SEM_PREDM_PLAN.id = SEM_ZPUS_PREDM.PREDM_PLAN_id
JOIN SEM_STUD_PLAN ON SEM_STUD_PLAN.id = SEM_PREDM_PLAN.STUDIJNI_PLAN_ID
JOIN SEM_PREDMET ON SEM_PREDMET.id = SEM_PREDM_PLAN.predmet_id
ORDER BY SEM_STUD_PLAN.nazev, sem_predmet.nazev, sem_zpus_vyuky.nazev;

CREATE OR REPLACE VIEW SEM_P_UCI AS
SELECT SEM_UCI.*, (SEM_UCITEL.JMENO || ' ' || SEM_UCITEL.PRIJMENI) as "ucitel", SEM_ROLE.NAZEV as "role", (SEM_STUD_PLAN.NAZEV || ' - ' || SEM_PREDMET.NAZEV) as "predmet"
FROM SEM_UCI
JOIN SEM_UCITEL ON SEM_UCITEL.ID = SEM_UCI.UCITEL_ID
JOIN SEM_ROLE ON SEM_ROLE.ID = SEM_UCI.ROLE_ID
JOIN SEM_PREDM_PLAN ON SEM_PREDM_PLAN.ID = SEM_UCI.PREDM_PLAN_ID
JOIN SEM_PREDMET ON SEM_PREDMET.ID = SEM_PREDM_PLAN.PREDMET_ID
JOIN SEM_STUD_PLAN ON SEM_STUD_PLAN.ID = SEM_PREDM_PLAN.STUDIJNI_PLAN_ID
ORDER BY "predmet", "ucitel", "role";

CREATE OR REPLACE VIEW SEM_P_UZIVATEL AS
SELECT SEM_UZIVATEL.ID as "id", email, admin, (SEM_UCITEL.JMENO || ' ' || SEM_UCITEL.PRIJMENI) as "ucitel", SEM_BOOL_TO_STRING(admin) as "je_admin"
FROM SEM_UZIVATEL
LEFT JOIN SEM_UCITEL ON SEM_UCITEL.ID = SEM_UZIVATEL.UCITEL_ID
ORDER BY email;

CREATE OR REPLACE VIEW SEM_P_STUD_PLAN AS
SELECT SEM_STUD_PLAN.*, SEM_OBOR.NAZEV AS "obor"
FROM SEM_STUD_PLAN
JOIN SEM_OBOR ON SEM_OBOR.ID = SEM_STUD_PLAN.OBOR_ID
ORDER BY SEM_STUD_PLAN.NAZEV;

CREATE OR REPLACE VIEW sem_p_rozvrh AS
SELECT sem_rozvrh.*, sem_zpus_vyuky.nazev AS "zpusob_vyuky", TO_CHAR(sem_rozvrh.presne_datum, 'DD. MM. YYYY') AS "datum", sem_ucitel.id AS "ucitel_id", sem_ucitel.jmeno || ' ' || sem_ucitel.prijmeni AS "ucitel", sem_zpus_predm.pocet_hodin AS "pocet_hodin", sem_predmet.zkratka AS "predmet", sem_semestr.nazev AS "semestr", sem_predm_plan.semestr_id AS "semestr_id", sem_mistnost.nazev AS "mistnost", sem_mistnost.id AS "mistnost_id", sem_mistnost.kapacita AS "kapacita"
FROM sem_rozvrh
JOIN sem_mistnost
ON sem_rozvrh.mistnost_id = sem_mistnost.id
JOIN sem_zpus_predm
ON sem_rozvrh.zpusob_zakonceni_predmetu_id = sem_zpus_predm.id
JOIN sem_predm_plan
ON sem_zpus_predm.predm_plan_id = sem_predm_plan.id
JOIN sem_zpus_vyuky
ON sem_zpus_predm.zpusob_vyuky_id = sem_zpus_vyuky.id
JOIN sem_predmet
ON sem_predm_plan.predmet_id = sem_predmet.id
JOIN sem_uci
ON sem_rozvrh.uci_id = sem_uci.id
JOIN sem_ucitel
ON sem_uci.ucitel_id = sem_ucitel.id
JOIN sem_semestr
ON sem_semestr.id = sem_predm_plan.semestr_id;

CREATE OR REPLACE VIEW sem_p_uvazky AS
SELECT SEM_UCI.UCITEL_ID as "ucitel", SEM_PREDMET.NAZEV as "predmet", SEM_ROLE.NAZEV as "role", sum(NVL(SEM_ZPUS_PREDM.POCET_HODIN, 0)) as "hodiny"
FROM SEM_UCI
LEFT JOIN SEM_ROZVRH ON SEM_ROZVRH.UCI_ID = SEM_UCI.ID
LEFT JOIN SEM_ZPUS_PREDM ON SEM_ROZVRH.ZPUSOB_ZAKONCENI_PREDMETU_ID = SEM_ZPUS_PREDM.ID
JOIN SEM_ROLE ON SEM_ROLE.ID = SEM_UCI.ROLE_ID
JOIN SEM_PREDM_PLAN ON SEM_UCI.PREDM_PLAN_ID = SEM_PREDM_PLAN.ID
JOIN SEM_PREDMET ON SEM_PREDM_PLAN.PREDMET_ID = SEM_PREDMET.ID
GROUP BY SEM_UCI.UCITEL_ID, SEM_PREDMET.NAZEV, SEM_ROLE.NAZEV
ORDER BY "predmet", "role";