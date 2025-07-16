-- ALTER TABLE para renomear campos de usuários do português para inglês
ALTER TABLE `users`
  CHANGE COLUMN `nome` `name` VARCHAR(255) NOT NULL,
  CHANGE COLUMN `senha` `password` VARCHAR(255) NOT NULL,
  CHANGE COLUMN `tipo` `role` ENUM('admin', 'user', 'moderator') NOT NULL DEFAULT 'user',
  CHANGE COLUMN `ativo` `active` TINYINT(1) DEFAULT 1;
-- Se necessário, ajuste índices e constraints conforme o novo nome dos campos
