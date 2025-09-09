CREATE DATABASE IF NOT EXISTS myfoods;
USE myfoods;

CREATE TABLE `user` (
  userId INT(11) NOT NULL AUTO_INCREMENT,
  userName VARCHAR(255) NOT NULL,
  userPassword VARCHAR(255) NOT NULL,
  userEmail VARCHAR(255) NOT NULL,
  PRIMARY KEY (userId),
  UNIQUE KEY userName (userName),
  UNIQUE KEY userEmail (userEmail)
);

CREATE TABLE post (
  postId INT(11) NOT NULL AUTO_INCREMENT,
  userId INT(11) NOT NULL,
  title VARCHAR(255) NOT NULL,
  postDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (postId),
  KEY fk_post_userId (userId),
  CONSTRAINT fk_post_userId FOREIGN KEY (userId) REFERENCES user (userId)
);

CREATE TABLE comment (
  commentId INT(11) NOT NULL AUTO_INCREMENT,
  userId INT(11) NOT NULL,
  postId INT(11) NOT NULL,
  content VARCHAR(255) NOT NULL,
  PRIMARY KEY (commentId),
  KEY fk_comment_postId (postId),
  KEY fk_comment_userId (userId),
  CONSTRAINT fk_comment_postId FOREIGN KEY (postId) REFERENCES post (postId),
  CONSTRAINT fk_comment_userId FOREIGN KEY (userId) REFERENCES user (userId)
);

CREATE TABLE ingredientRecipe (
  ingredientId INT(11) NOT NULL AUTO_INCREMENT,
  postId INT(11) NOT NULL,
  ingredient VARCHAR(255) NOT NULL,
  PRIMARY KEY (ingredientId),
  KEY fk_ingredientRecipe_postId (postId),
  CONSTRAINT fk_ingredientRecipe_postId FOREIGN KEY (postId) REFERENCES post (postId)
);

CREATE TABLE likes (
  likeId INT(11) NOT NULL AUTO_INCREMENT,
  postId INT(11) NOT NULL,
  userId INT(11) NOT NULL,
  PRIMARY KEY (likeId),
  KEY fk_like_postId (postId),
  KEY fk_like_userId (userId),
  CONSTRAINT fk_like_postId FOREIGN KEY (postId) REFERENCES post (postId),
  CONSTRAINT fk_like_userId FOREIGN KEY (userId) REFERENCES user (userId)
);

CREATE TABLE likesCounter (
  likesCounterId INT(11) NOT NULL AUTO_INCREMENT,
  likeId INT(11) NOT NULL,
  likesAmount INT(11) NOT NULL,
  PRIMARY KEY (likesCounterId),
  KEY fk_likesCounter_likeId (likeId),
  CONSTRAINT fk_likesCounter_likeId FOREIGN KEY (likeId) REFERENCES likes (likeId)
);

CREATE TABLE recipeStep (
  recipeStepId INT(11) NOT NULL AUTO_INCREMENT,
  postId INT(11) NOT NULL,
  step VARCHAR(255) NOT NULL,
  PRIMARY KEY (recipeStepId),
  KEY fk_recipeStep_postId (postId),
  CONSTRAINT fk_recipeStep_postId FOREIGN KEY (postId) REFERENCES post (postId)
);