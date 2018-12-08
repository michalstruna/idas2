CREATE OR REPLACE FUNCTION SEM_BOOL_TO_STRING(p_bool NUMBER)
RETURN VARCHAR2 DETERMINISTIC IS
begin
    IF p_bool > 0 THEN
        RETURN 'Ano';
    ELSE
        RETURN 'Ne';
    END IF;
end;

/

CREATE OR REPLACE FUNCTION SEM_DATUM_NA_DEN(p_datum DATE)
RETURN NUMBER DETERMINISTIC IS
BEGIN
    RETURN TRUNC(p_datum) - TRUNC (p_datum, 'IW');
END;

/

/**
 * Zjistí, zda je místnost v daný den v daný časový interval volná.
 * @param p_mistnost_id ID místnosti.
 * @param p_den_v_tydnu Pořadí dne v týdnu.
 * @param p_od Počáteční hodina intervalu.
 * @param p_do Konečná hodina intervalu.
 * @param krome ID aktuální rozvrhové akce.
 * @return Místnost je volná.
 */
CREATE OR REPLACE FUNCTION sem_je_mistnost_volna (p_mistnost_id NUMBER, p_den_v_tydnu NUMBER, p_od NUMBER, p_do NUMBER, krome NUMBER)
    RETURN NUMBER AS
        v_pocet NUMBER(1);
  BEGIN
      SELECT count(*) AS pocet
      INTO v_pocet
      FROM sem_rozvrh
          JOIN sem_zpus_predm
          ON sem_zpus_predm.id = sem_rozvrh.zpusob_zakonceni_predmetu_id
      WHERE mistnost_id = p_mistnost_id
          AND den_v_tydnu = p_den_v_tydnu
          AND (sem_rozvrh.id <> krome OR krome IS NULL)
          AND schvaleno = 1
          AND (
              (zacatek <= p_od AND (zacatek + pocet_hodin) >= p_od)
                  OR (zacatek < p_do AND (zacatek + pocet_hodin) >= p_do)
                  OR (zacatek >= p_od AND (zacatek + pocet_hodin) <= p_do)
          );

      IF v_pocet > 0 THEN
          RETURN 0;
      ELSE
          RETURN 1;
      END IF;
  END;

/

CREATE OR REPLACE FUNCTION sem_skupina_zaneprazdnena(p_predm_plan_id NUMBER, p_den_v_tydnu NUMBER, p_od NUMBER, p_do NUMBER, krome NUMBER)
    RETURN NUMBER AS
        v_plan_id NUMBER;
        v_pocet NUMBER(1);
  BEGIN
      SELECT sem_predm_plan.id INTO v_plan_id
      FROM sem_zpus_predm
      JOIN sem_predm_plan ON sem_predm_plan.id = sem_zpus_predm.predm_plan_id
      WHERE sem_zpus_predm.id = p_predm_plan_id;

      SELECT count(*) AS pocet
      INTO v_pocet
      FROM sem_rozvrh
      JOIN sem_zpus_predm ON sem_zpus_predm.id = sem_rozvrh.zpusob_zakonceni_predmetu_id
      JOIN sem_predm_plan ON sem_predm_plan.id = sem_zpus_predm.predm_plan_id
      WHERE sem_predm_plan.id = v_plan_id
          AND den_v_tydnu = p_den_v_tydnu
          AND (sem_rozvrh.id <> krome OR krome IS NULL)
          AND schvaleno = 1
          AND (
              (zacatek <= p_od AND (zacatek + pocet_hodin) >= p_od)
                  OR (zacatek < p_do AND (zacatek + pocet_hodin) >= p_do)
                  OR (zacatek >= p_od AND (zacatek + pocet_hodin) <= p_do)
          );

      IF v_pocet > 0 THEN
          RETURN 0;
      ELSE
          RETURN 1;
      END IF;
  END;