-- Migracion para contrasenas con hash y recuperacion por enlace.
-- STRPASSWORD ya es VARCHAR(255) (ver data/livingni_living.sql), asi que admite password_hash() sin cambios.

-- Enlaces de un solo uso para restablecer la contrasena (se guarda el SHA-256 del token).
CREATE TABLE IF NOT EXISTS tblrecuperaclave (
	STRTOKEN    CHAR(64) NOT NULL,
	NUMIDENTLIV INT      NOT NULL,
	DTMEXPIRA   DATETIME NOT NULL,
	PRIMARY KEY (STRTOKEN),
	KEY idx_recupera_usuario (NUMIDENTLIV)
);
