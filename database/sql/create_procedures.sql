CREATE OR REPLACE PACKAGE SEM_IMPORT
IS

PROCEDURE IMPORT_UCITELE(p_jmeno IN VARCHAR2, p_prijmeni IN VARCHAR2, p_titul_pred IN VARCHAR2, p_titul_za IN VARCHAR2,
p_telefon IN VARCHAR2, p_mobil IN VARCHAR2, p_email IN VARCHAR2, p_katedra IN VARCHAR2);

PROCEDURE IMPORT_PREDMETU(p_zkratka IN VARCHAR2, p_nazev IN VARCHAR2, p_forma_vyuky IN VARCHAR2, p_zpusob_zakonceni IN VARCHAR2);

PROCEDURE IMPORT_KATEDRY(p_zkratka IN VARCHAR2, p_nazev IN VARCHAR2, p_fakulta IN VARCHAR2);

END SEM_IMPORT;

/

CREATE OR REPLACE PACKAGE BODY SEM_IMPORT
IS

PROCEDURE IMPORT_UCITELE(p_jmeno IN VARCHAR2, p_prijmeni IN VARCHAR2, p_titul_pred IN VARCHAR2, p_titul_za IN VARCHAR2,
p_telefon IN VARCHAR2, p_mobil IN VARCHAR2, p_email IN VARCHAR2, p_katedra IN VARCHAR2) IS
BEGIN
  INSERT INTO SEM_UCITEL(id, jmeno, prijmeni, titul_pred, titul_za, telefon, mobil, kontaktni_email, katedra_id)
  VALUES (SEM_UCITEL_SEQ.NEXTVAL, p_jmeno, p_prijmeni, p_titul_pred, p_titul_za, p_telefon, p_mobil, p_email, p_katedra);
END IMPORT_UCITELE;


PROCEDURE IMPORT_PREDMETU(p_zkratka IN VARCHAR2, p_nazev IN VARCHAR2, p_forma_vyuky IN VARCHAR2, p_zpusob_zakonceni IN VARCHAR2) IS
BEGIN
  INSERT INTO SEM_PREDMET (id, zkratka, nazev, forma_vyuky_id, zpusob_zakonceni_id)
  VALUES (SEM_PREDMET_SEQ.NEXTVAL, p_zkratka, p_nazev, p_forma_vyuky, p_zpusob_zakonceni);
END IMPORT_PREDMETU;

PROCEDURE IMPORT_KATEDRY(p_zkratka IN VARCHAR2, p_nazev IN VARCHAR2, p_fakulta IN VARCHAR2) IS
BEGIN
INSERT INTO SEM_KATEDRA (id, zkratka, nazev, fakulta_id)
VALUES (SEM_PREDMET_SEQ.NEXTVAL, p_zkratka, p_nazev, p_fakulta);
END IMPORT_KATEDRY;

END SEM_IMPORT;