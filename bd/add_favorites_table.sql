-- Script para crear la tabla favorites
-- Ejecutar en la BD `myfoods` (por ejemplo con phpMyAdmin o mysql)

CREATE TABLE IF NOT EXISTS `favorites` (
  `favoriteId` INT NOT NULL AUTO_INCREMENT,
  `postId` INT NOT NULL,
  `userId` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`favoriteId`),
  UNIQUE KEY `unique_fav` (`postId`, `userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opcional: agregar claves foráneas si la estructura lo permite
-- ALTER TABLE `favorites` ADD CONSTRAINT `fk_fav_post` FOREIGN KEY (`postId`) REFERENCES `post`(`postId`) ON DELETE CASCADE;
-- ALTER TABLE `favorites` ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON DELETE CASCADE;
