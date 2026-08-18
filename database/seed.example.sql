USE iamroot_public;

INSERT INTO aplicaciones (code, nombre, descripcion)
VALUES ('demo-app', 'Application de démonstration', 'Données publiques de démonstration.');

INSERT INTO roles (nombre, descripcion)
VALUES ('reader', 'Lecture uniquement'), ('editor', 'Lecture et modification');

INSERT INTO permisos (code, nombre)
VALUES ('app.read', 'Lire'), ('app.write', 'Modifier');

INSERT INTO rol_permisos (id_rol, id_permiso)
SELECT r.id, p.id FROM roles r JOIN permisos p
WHERE r.nombre = 'reader' AND p.code = 'app.read';

INSERT INTO rol_permisos (id_rol, id_permiso)
SELECT r.id, p.id FROM roles r JOIN permisos p
WHERE r.nombre = 'editor' AND p.code IN ('app.read', 'app.write');

-- Créer un utilisateur seulement après avoir généré un hash avec :
-- php scripts/hash_password.php 'mot-de-passe'
