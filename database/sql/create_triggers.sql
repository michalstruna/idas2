create or replace TRIGGER SEM_SMAZ_OBRAZEK
AFTER UPDATE of obrazek_id ON sem_ucitel
for each row
BEGIN
DELETE FROM SEM_OBRAZEK WHERE ID = :old.obrazek_id;
END;

/

create or replace TRIGGER SEM_SMAZ_OBRAZEK_DELETE
AFTER DELETE ON sem_ucitel
for each row
BEGIN
DELETE FROM SEM_OBRAZEK WHERE ID = :old.obrazek_id;
END;

/

-- TODO
CREATE OR REPLACE TRIGGER sem_nastav_den_v_tydnu
BEFORE UPDATE OR INSERT ON sem_rozvrh
FOR EACH ROW
BEGIN
  DBMS_OUTPUT.PUT_LINE(:NEW.presne_datum);
  DBMS_OUTPUT.PUT_LINE(TO_CHAR(TO_DATE(:NEW.presne_datum, 'YYYY-MM-DD'), 'D') - 1);

  DBMS_OUTPUT.PUT_LINE(TO_DATE('2018-12-04', 'YYYY-MM-DD'));
  DBMS_OUTPUT.PUT_LINE(TO_CHAR(TO_DATE('2018-12-04', 'YYYY-MM-DD'), 'D') - 1);

  --IF :OLD.presne_datum IS NOT NULL THEN
    --:NEW.den_v_tydnu := TO_CHAR(:OLD.presne_datum, 'D') - 1;
  --END IF;
END;