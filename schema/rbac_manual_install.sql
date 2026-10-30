-- ============================================================================
--  Ручная установка RBAC для окружений БЕЗ командной строки (Hostinger и т.п.)
--  Выполнять в phpMyAdmin → вкладка SQL, ОДИН раз на боевой базе.
--
--  Это эквивалент `php bin/migrate.php up`:
--   - baseline-миграция на существующей базе — no-op (таблицы проекта уже есть),
--     поэтому их здесь не трогаем;
--   - создаём RBAC-таблицы и колонку users.is_active;
--   - наполняем роли/права/связи и выдаём всем пользователям роль superadmin;
--   - помечаем все три миграции применёнными в schema_migrations, чтобы CLI-раннер
--     (когда/если появится SSH) их НЕ повторял.
-- ============================================================================

-- 0. Реестр миграций (обычно его создаёт раннер) -----------------------------
CREATE TABLE IF NOT EXISTS schema_migrations (
    version    VARCHAR(255) NOT NULL,
    applied_at DATETIME     NOT NULL,
    PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- 1. RBAC-таблицы (миграция 20261030_120000_create_rbac_tables) --------------
CREATE TABLE IF NOT EXISTS roles (
    id         INT          NOT NULL AUTO_INCREMENT,
    name       VARCHAR(64)  NOT NULL,
    label      VARCHAR(128) NOT NULL DEFAULT '',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS permissions (
    id         INT          NOT NULL AUTO_INCREMENT,
    name       VARCHAR(128) NOT NULL,
    label      VARCHAR(191) NOT NULL DEFAULT '',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_permissions_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id       INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    KEY idx_rp_permission (permission_id),
    CONSTRAINT fk_rp_role       FOREIGN KEY (role_id)       REFERENCES roles (id)       ON DELETE CASCADE,
    CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    KEY idx_ur_role (role_id),
    CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- users.is_active — отключение аккаунта без удаления.
-- ВНИМАНИЕ: MySQL не поддерживает ADD COLUMN IF NOT EXISTS.
-- Если колонка уже есть, phpMyAdmin выдаст ошибку на этой строке — просто
-- пропусти её и выполни остальное.
ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1;

-- 2. Сид (миграция 20261030_120500_seed_rbac) --------------------------------
INSERT INTO roles (name, label) VALUES
    ('superadmin', 'Суперадмин'),
    ('manager',    'Менеджер'),
    ('editor',     'Редактор'),
    ('viewer',     'Наблюдатель');

INSERT INTO permissions (name, label) VALUES
    ('dashboard.view',    'Просмотр дашборда'),
    ('products.view',     'Просмотр товаров'),
    ('products.edit',     'Создание и редактирование товаров'),
    ('products.delete',   'Удаление товаров'),
    ('categories.manage', 'Управление деревом категорий'),
    ('filters.manage',    'Управление фильтрами'),
    ('sborki.manage',     'Управление сборками'),
    ('filters.view',      'Просмотр фильтров'),
    ('sborki.view',       'Просмотр сборок'),
    ('users.manage',      'Управление пользователями');

-- superadmin — все права
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
 WHERE r.name = 'superadmin';

-- manager — всё, кроме управления пользователями
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
 WHERE r.name = 'manager'
   AND p.name IN ('dashboard.view','products.view','products.edit','products.delete',
                  'categories.manage','filters.manage','sborki.manage');

-- editor — правка товаров и фильтров, без удаления и категорий
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
 WHERE r.name = 'editor'
   AND p.name IN ('dashboard.view','products.view','products.edit','filters.manage');

-- viewer — только чтение
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
 WHERE r.name = 'viewer'
   AND p.name IN ('dashboard.view','products.view');

-- Права просмотра фильтров/сборок — всем ролям (кроме superadmin: ему всё
-- выдано блоком выше). Так viewer/editor/manager видят списки, но правят их
-- только те, у кого есть filters.manage / sborki.manage.
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
 WHERE p.name IN ('filters.view', 'sborki.view')
   AND r.name IN ('manager', 'editor', 'viewer');

-- все существующие пользователи → superadmin (чтобы никого не залочить)
INSERT INTO user_roles (user_id, role_id)
SELECT u.id, r.id FROM users u CROSS JOIN roles r
 WHERE r.name = 'superadmin';

-- 3. Отметить миграции применёнными ------------------------------------------
INSERT INTO schema_migrations (version, applied_at) VALUES
    ('00000000_000000_baseline',            NOW()),
    ('20261030_120000_create_rbac_tables',  NOW()),
    ('20261030_120500_seed_rbac',           NOW());
