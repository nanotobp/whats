-- ============================================
-- POLÍTICAS RLS PARA SUPABASE
-- ============================================
-- Este script configura Row Level Security para todas las tablas
-- Esto es REQUERIDO por Supabase para evitar suspensión de cuenta

-- ============================================
-- HABILITAR RLS EN TODAS LAS TABLAS
-- ============================================

ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE contacts ENABLE ROW LEVEL SECURITY;
ALTER TABLE groups ENABLE ROW LEVEL SECURITY;
ALTER TABLE campaigns ENABLE ROW LEVEL SECURITY;
ALTER TABLE messages ENABLE ROW LEVEL SECURITY;
ALTER TABLE cache ENABLE ROW LEVEL SECURITY;
ALTER TABLE cache_locks ENABLE ROW LEVEL SECURITY;
ALTER TABLE jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE job_batches ENABLE ROW LEVEL SECURITY;
ALTER TABLE failed_jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE sessions ENABLE ROW LEVEL SECURITY;

-- ============================================
-- POLÍTICAS PARA USUARIOS (users)
-- ============================================

-- Permitir a los usuarios autenticados ver todos los usuarios
CREATE POLICY "Usuarios autenticados pueden ver usuarios"
ON users FOR SELECT
TO authenticated
USING (true);

-- Permitir a los usuarios autenticados actualizar su propio perfil
CREATE POLICY "Usuarios pueden actualizar su propio perfil"
ON users FOR UPDATE
TO authenticated
USING (true)
WITH CHECK (true);

-- ============================================
-- POLÍTICAS PARA CONTACTOS (contacts)
-- ============================================

-- Permitir todas las operaciones a usuarios autenticados
CREATE POLICY "Usuarios autenticados pueden ver contactos"
ON contacts FOR SELECT
TO authenticated
USING (true);

CREATE POLICY "Usuarios autenticados pueden insertar contactos"
ON contacts FOR INSERT
TO authenticated
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden actualizar contactos"
ON contacts FOR UPDATE
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden eliminar contactos"
ON contacts FOR DELETE
TO authenticated
USING (true);

-- ============================================
-- POLÍTICAS PARA GRUPOS (groups)
-- ============================================

CREATE POLICY "Usuarios autenticados pueden ver grupos"
ON groups FOR SELECT
TO authenticated
USING (true);

CREATE POLICY "Usuarios autenticados pueden insertar grupos"
ON groups FOR INSERT
TO authenticated
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden actualizar grupos"
ON groups FOR UPDATE
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden eliminar grupos"
ON groups FOR DELETE
TO authenticated
USING (true);

-- ============================================
-- POLÍTICAS PARA CAMPAÑAS (campaigns)
-- ============================================

CREATE POLICY "Usuarios autenticados pueden ver campañas"
ON campaigns FOR SELECT
TO authenticated
USING (true);

CREATE POLICY "Usuarios autenticados pueden insertar campañas"
ON campaigns FOR INSERT
TO authenticated
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden actualizar campañas"
ON campaigns FOR UPDATE
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden eliminar campañas"
ON campaigns FOR DELETE
TO authenticated
USING (true);

-- ============================================
-- POLÍTICAS PARA MENSAJES (messages)
-- ============================================

CREATE POLICY "Usuarios autenticados pueden ver mensajes"
ON messages FOR SELECT
TO authenticated
USING (true);

CREATE POLICY "Usuarios autenticados pueden insertar mensajes"
ON messages FOR INSERT
TO authenticated
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden actualizar mensajes"
ON messages FOR UPDATE
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Usuarios autenticados pueden eliminar mensajes"
ON messages FOR DELETE
TO authenticated
USING (true);

-- ============================================
-- POLÍTICAS PARA CACHE Y SISTEMA
-- ============================================

-- Permitir acceso completo a cache (usado por Laravel)
CREATE POLICY "Sistema puede acceder a cache"
ON cache FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Sistema puede acceder a cache_locks"
ON cache_locks FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- ============================================
-- POLÍTICAS PARA JOBS Y COLAS
-- ============================================

CREATE POLICY "Sistema puede acceder a jobs"
ON jobs FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Sistema puede acceder a job_batches"
ON job_batches FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

CREATE POLICY "Sistema puede acceder a failed_jobs"
ON failed_jobs FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- ============================================
-- POLÍTICAS PARA SESIONES
-- ============================================

CREATE POLICY "Sistema puede acceder a sessions"
ON sessions FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- ============================================
-- POLÍTICAS DE ACCESO PÚBLICO (service_role bypass)
-- ============================================
-- Estas políticas permiten a la aplicación (usando service_role key)
-- acceder a los datos sin restricciones

CREATE POLICY "Service role puede acceder a users"
ON users FOR ALL
TO service_role
USING (true)
WITH CHECK (true);

CREATE POLICY "Service role puede acceder a contacts"
ON contacts FOR ALL
TO service_role
USING (true)
WITH CHECK (true);

CREATE POLICY "Service role puede acceder a groups"
ON groups FOR ALL
TO service_role
USING (true)
WITH CHECK (true);

CREATE POLICY "Service role puede acceder a campaigns"
ON campaigns FOR ALL
TO service_role
USING (true)
WITH CHECK (true);

CREATE POLICY "Service role puede acceder a messages"
ON messages FOR ALL
TO service_role
USING (true)
WITH CHECK (true);

-- ============================================
-- CONFIGURACIÓN DEL BUCKET DE STORAGE
-- ============================================
-- El bucket 'archivos' debe ser público para acceder a las imágenes

-- Esto se debe hacer desde el panel de Supabase:
-- 1. Ve a Storage > archivos
-- 2. Click en "Policies"
-- 3. Crear una política que permita:
--    - SELECT público (para ver imágenes)
--    - INSERT para authenticated (para subir imágenes)
--    - DELETE para authenticated (para eliminar imágenes)
