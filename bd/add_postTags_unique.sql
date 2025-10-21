-- Agrega constraint UNIQUE a la tabla postTags para evitar duplicados
-- Ejecutar en la base de datos `myfoods`

ALTER TABLE postTags
ADD CONSTRAINT unique_post_tag UNIQUE (postId, tagId);

-- Si la tabla postTags no existe, aquí hay una creación segura:
-- CREATE TABLE IF NOT EXISTS postTags (
--   postId INT NOT NULL,
--   tagId INT NOT NULL,
--   PRIMARY KEY (postId, tagId),
--   FOREIGN KEY (postId) REFERENCES post(postId) ON DELETE CASCADE,
--   FOREIGN KEY (tagId) REFERENCES tags(tagId) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
