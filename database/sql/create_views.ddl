DROP VIEW sem_p_katedra;

CREATE OR REPLACE VIEW sem_p_katedra AS
SELECT sem_katedra.*, sem_fakulta.zkratka as "fakulta"
FROM sem_katedra
JOIN sem_fakulta
ON sem_katedra.fakulta_id = sem_fakulta.id;