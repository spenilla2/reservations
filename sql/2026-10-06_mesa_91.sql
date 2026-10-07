-- La mesa 91 existe en el local y esta dibujada en el tablero, pero faltaba en tblmesas.
-- Se crea igual que su pareja en el plano, la mesa 90 (VIP, 8-15 personas).
INSERT INTO tblmesas (STRMESA, NUMCAPACIDAD, STRTIPOMESA1, STRTIPOMESA2)
SELECT '91', NUMCAPACIDAD, STRTIPOMESA1, STRTIPOMESA2
  FROM tblmesas
 WHERE STRMESA = '90'
   AND NOT EXISTS (SELECT 1 FROM tblmesas WHERE STRMESA = '91');
