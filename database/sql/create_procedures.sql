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

/

CREATE OR REPLACE PROCEDURE SEM_NASTAV_ZPUS_VYUKY(p_id IN INTEGER, p_pocet_hodin IN INTEGER, p_kapacita IN INTEGER, p_zpusob_vyuky_id IN INTEGER, p_predm_plan_id IN INTEGER) IS
  v_pocet_studentu SEM_PREDM_PLAN.POCET_STUDENTU%TYPE;
  v_finalni_kapacita INTEGER := p_kapacita;
BEGIN
  SELECT pocet_studentu INTO v_pocet_studentu
  FROM SEM_PREDM_PLAN
  WHERE ID = p_predm_plan_id;

  IF p_kapacita > v_pocet_studentu THEN
    v_finalni_kapacita := v_pocet_studentu;
  END IF;


  IF p_id IS NULL THEN
    INSERT INTO SEM_ZPUS_PREDM (id, pocet_hodin, kapacita, zpusob_vyuky_id, predm_plan_id) VALUES (SEM_ZPUS_PREDM_SEQ.NEXTVAL, p_pocet_hodin, v_finalni_kapacita, p_zpusob_vyuky_id, p_predm_plan_id);
  ELSE
    UPDATE SEM_ZPUS_PREDM SET pocet_hodin=p_pocet_hodin, kapacita=v_finalni_kapacita, zpusob_vyuky_id=p_zpusob_vyuky_id, predm_plan_id=p_predm_plan_id WHERE ID = p_id;
  END IF;
END;

/

CREATE OR REPLACE PROCEDURE SEM_VYMAZ_MISTNOST(p_id INTEGER) IS
BEGIN
  DELETE FROM SEM_ROZVRH WHERE MISTNOST_ID = p_id;
  DELETE FROM SEM_MISTNOST WHERE ID = p_id;
END;