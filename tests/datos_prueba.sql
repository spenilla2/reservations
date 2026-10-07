-- Datos de prueba para tests/regresion.sh (solo base local). Clave de todos: Prueba123
INSERT INTO tblusuarios VALUES
(900,'ADMIN','PRUEBA','M','1985-01-01','admin.prueba@example.com',1,1,'300',1,1,1,1,'$2y$10$7J/T53UTXipWXoug24JSAOmt1pA4JuwiVDgVnKJYdPU22nuBqfrH.','2026-01-01','F','','','','','1',''),
(901,'ANA MARIA','O\'BRIEN TEST','F','1990-05-04','cliente.prueba@example.com',1,1,'3001234567',1,1,1,1,'$2y$10$7J/T53UTXipWXoug24JSAOmt1pA4JuwiVDgVnKJYdPU22nuBqfrH.','2026-01-01','T','','','','','1',''),
(902,'SIN','ACTIVAR','M','1991-01-01','inactivo.prueba@example.com',1,1,'301',1,1,1,2,'$2y$10$7J/T53UTXipWXoug24JSAOmt1pA4JuwiVDgVnKJYdPU22nuBqfrH.','2026-01-01','F','','','','','0',''),
(903,'CLAVE','ANTERIOR','F','1992-01-01','anterior.prueba@example.com',1,1,'302',1,1,1,1,'UHJ1ZWJhMTIz','2026-01-01','F','','','','','1','');
INSERT INTO tblusuariosperfiles VALUES (900,1),(901,0),(903,0);
INSERT INTO tblclientesvip VALUES (900,'VIP DE PRUEBA','M',900,1,'3110000000','2026-01-01 10:00:00','[2026-01-01 10:00:00]: creado');
INSERT INTO tblreservas VALUES
(100,'2026-12-12',901,'FIESTA ANA',6,1,'ANA','SIN OBSERVACIONES','2026-10-01 10:00:00',1,'2026-10-01 10:00:00','',0,'N'),
(101,'2026-12-12',901,'GRADO DE LUIS',8,3,'LUIS','LLEGAN TARDE','2026-10-01 10:05:00',1,'2026-10-01 10:05:00','10',0,'N'),
(102,'2026-12-12',900,'EMPRESA XYZ',12,5,'','','2026-10-01 10:10:00',3,'2026-10-02 09:00:00','B1',0,'S'),
(103,'2026-12-12',900,'MESA BLOQUEADA',2,5,'','MANTENIMIENTO','2026-10-01 10:15:00',3,'2026-10-01 10:15:00','11',0,'N'),
(104,'2026-12-12',900,'Mesa Compartida',4,5,'','','2026-10-01 10:20:00',3,'2026-10-01 10:20:00','12',0,'N'),
(105,'2026-12-12',901,'CUMPLE DE PEDRO',10,1,'PEDRO','TORTA','2026-10-01 10:25:00',3,'2026-10-02 09:30:00','P1',0,'S'),
(106,'2026-12-12',901,'ANIVERSARIO',2,2,'','','2026-10-01 10:30:00',1,'2026-10-01 10:30:00','',0,'N'),
(107,'2026-12-12',900,'ESPECIAL VIP',5,5,'','','2026-10-01 10:35:00',1,'2026-10-01 10:35:00','13',900,'N'),
(108,'2026-12-13',901,'DOMINGO',3,5,'','','2026-10-01 10:40:00',1,'2026-10-01 10:40:00','',0,'N');
INSERT INTO tblmesareserva VALUES
(200,'2026-12-12',101,'10','2026-10-01 11:00:00','N','1'),
(201,'2026-12-12',102,'B1','2026-10-01 11:00:00','C','1'),
(202,'2026-12-12',103,'11','2026-10-01 11:00:00','B','1'),
(203,'2026-12-12',104,'12','2026-10-01 11:00:00','K','1'),
(204,'2026-12-12',105,'P1','2026-10-01 11:00:00','T','1'),
(205,'2026-12-12',107,'13','2026-10-01 11:00:00','E','1');
-- token de restablecimiento conocido: "tokendeprueba123"
INSERT INTO tblrecuperaclave VALUES (SHA2('tokendeprueba123',256),901,'2099-01-01 00:00:00');
ALTER TABLE tblusuarios AUTO_INCREMENT=1000;
ALTER TABLE tblreservas AUTO_INCREMENT=500;
ALTER TABLE tblmesareserva AUTO_INCREMENT=500;
ALTER TABLE tblclientesvip AUTO_INCREMENT=1000;
