create or replace TRIGGER SEM_SMAZ_OBRAZEK
AFTER UPDATE of obrazek_id OR DELETE ON sem_ucitel
for each row
BEGIN
  DELETE FROM SEM_OBRAZEK WHERE ID = :old.obrazek_id;
END;

/

CREATE OR REPLACE TRIGGER sem_nastav_den_v_tydnu
BEFORE INSERT OR UPDATE ON sem_rozvrh
FOR EACH ROW
BEGIN
  IF :NEW.presne_datum IS NOT NULL THEN
    :NEW.den_v_tydnu := TRUNC(TO_DATE(:NEW.presne_datum, 'YYYY-MM-DD')) - TRUNC (TO_DATE(:NEW.presne_datum, 'YYYY-MM-DD'), 'IW');
  END IF;
END;