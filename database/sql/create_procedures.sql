CREATE OR REPLACE PACKAGE SEM_IMPORT
IS

PROCEDURE IMPORT_UCITELE(jmeno IN VARCHAR2, prijmeni IN VARCHAR2, titul_pred IN VARCHAR2, titul_za IN VARCHAR2,
telefon IN VARCHAR2, mobil IN VARCHAR2, email IN VARCHAR2, katedra IN VARCHAR2);

END SEM_IMPORT;



CREATE OR REPLACE PACKAGE BODY SEM_IMPORT
IS

PROCEDURE IMPORT_UCITELE(jmeno IN VARCHAR2, prijmeni IN VARCHAR2, titul_pred IN VARCHAR2, titul_za IN VARCHAR2,
telefon IN VARCHAR2, mobil IN VARCHAR2, email IN VARCHAR2, katedra IN VARCHAR2) IS
BEGIN
  INSERT INTO SEM_UCITEL(id, jmeno, prijmeni, titul_pred, titul_za, telefon, mobil, kontaktni_email, katedra_id)
  VALUES (SEM_UCITEL_SEQ.NEXTVAL, jmeno, prijmeni, titul_pred, titul_za, telefon, mobil, email, katedra);
END;

END SEM_IMPORT;