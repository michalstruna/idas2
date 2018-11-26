CREATE OR REPLACE VIEW sem_p_katedra AS
SELECT sem_katedra.*, sem_fakulta.zkratka as "fakulta"
FROM sem_katedra
JOIN sem_fakulta
ON sem_katedra.fakulta_id = sem_fakulta.id;

CREATE OR REPLACE VIEW SEM_P_UCITEL AS
SELECT sem_ucitel.*, (titul_pred || ' ' || jmeno || ' ' || prijmeni || ' ' || titul_za) as "dlouhe_jmeno", sem_katedra.zkratka as "katedra"
FROM sem_ucitel
JOIN SEM_KATEDRA ON sem_katedra.id = SEM_UCITEL.katedra_id;