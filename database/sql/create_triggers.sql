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