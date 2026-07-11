-- Migration: add per-round notes to entregas (bug: round-notes-not-saved)
-- Fresh installs get this via sql/schema.sql. Existing DBs must run this once.
-- MySQL has no ADD COLUMN IF NOT EXISTS; run only if the column is missing.
ALTER TABLE `entregas` ADD COLUMN `notas` TEXT NULL AFTER `fecha_entrega`;
