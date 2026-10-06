-- Migracion para contrasenas con hash y recuperacion por enlace.
-- Revisar antes la definicion actual con: SHOW CREATE TABLE tblusuarios;

-- 1) password_hash() produce 60 caracteres o mas. Mientras la columna sea mas corta,
--    la aplicacion sigue guardando las claves en el formato anterior.
ALTER TABLE tblusuarios MODIFY STRPASSWORD VARCHAR(255);

-- 2) Enlaces de un solo uso para restablecer la contrasena (se guarda el SHA-256 del token).
CREATE TABLE IF NOT EXISTS tblrecuperaclave (
	STRTOKEN    CHAR(64) NOT NULL,
	NUMIDENTLIV INT      NOT NULL,
	DTMEXPIRA   DATETIME NOT NULL,
	PRIMARY KEY (STRTOKEN),
	KEY idx_recupera_usuario (NUMIDENTLIV)
);
