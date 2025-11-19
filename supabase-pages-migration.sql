/*
  # Pages Content Management Migration

  1. New Tables
    - `pages`
      - `id` (bigint, primary key) - WordPress page ID
      - `title` (text) - Page title
      - `slug` (text, unique) - URL slug
      - `content` (text) - Page content
      - `excerpt` (text) - Meta description
      - `featured_image` (text) - Image URL
      - `parent_id` (bigint) - Parent page ID
      - `menu_order` (integer) - Display order
      - `status` (text) - publish/draft
      - `template` (text) - Page template
      - `created_at` (timestamptz)
      - `updated_at` (timestamptz)

    - `menus`
      - `id` (serial, primary key)
      - `name` (text) - Menu name
      - `location` (text) - primary/footer
      - `created_at` (timestamptz)

    - `menu_items`
      - `id` (serial, primary key)
      - `menu_id` (integer) - Foreign key to menus
      - `page_id` (bigint) - Foreign key to pages
      - `parent_id` (integer) - Parent menu item
      - `title` (text) - Menu item title
      - `url` (text) - Menu item URL
      - `order` (integer) - Display order
      - `created_at` (timestamptz)

  2. Security
    - Enable RLS on all tables
    - Public read access for published pages
    - Authenticated full access for content management
*/

-- Pages table
CREATE TABLE IF NOT EXISTS pages (
    id BIGINT PRIMARY KEY,
    title TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    content TEXT,
    excerpt TEXT,
    featured_image TEXT,
    parent_id BIGINT REFERENCES pages(id) ON DELETE SET NULL,
    menu_order INTEGER DEFAULT 0,
    status TEXT DEFAULT 'publish' CHECK (status IN ('publish', 'draft', 'private')),
    template TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_pages_slug ON pages(slug);
CREATE INDEX IF NOT EXISTS idx_pages_parent ON pages(parent_id);
CREATE INDEX IF NOT EXISTS idx_pages_status ON pages(status);

-- Menus table
CREATE TABLE IF NOT EXISTS menus (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    location TEXT NOT NULL CHECK (location IN ('primary', 'footer', 'mobile')),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_menus_location ON menus(location);

-- Menu items table
CREATE TABLE IF NOT EXISTS menu_items (
    id SERIAL PRIMARY KEY,
    menu_id INTEGER REFERENCES menus(id) ON DELETE CASCADE,
    page_id BIGINT REFERENCES pages(id) ON DELETE CASCADE,
    parent_id INTEGER REFERENCES menu_items(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    url TEXT,
    menu_order INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_menu_items_menu ON menu_items(menu_id);
CREATE INDEX IF NOT EXISTS idx_menu_items_parent ON menu_items(parent_id);
CREATE INDEX IF NOT EXISTS idx_menu_items_order ON menu_items(menu_order);

-- Enable Row Level Security
ALTER TABLE pages ENABLE ROW LEVEL SECURITY;
ALTER TABLE menus ENABLE ROW LEVEL SECURITY;
ALTER TABLE menu_items ENABLE ROW LEVEL SECURITY;

-- Pages policies
CREATE POLICY "Public can view published pages"
    ON pages FOR SELECT
    TO anon
    USING (status = 'publish');

CREATE POLICY "Authenticated can view all pages"
    ON pages FOR SELECT
    TO authenticated
    USING (true);

CREATE POLICY "Authenticated can insert pages"
    ON pages FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated can update pages"
    ON pages FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated can delete pages"
    ON pages FOR DELETE
    TO authenticated
    USING (true);

-- Menus policies
CREATE POLICY "Public can view menus"
    ON menus FOR SELECT
    TO anon, authenticated
    USING (true);

CREATE POLICY "Authenticated can manage menus"
    ON menus FOR ALL
    TO authenticated
    USING (true)
    WITH CHECK (true);

-- Menu items policies
CREATE POLICY "Public can view menu items"
    ON menu_items FOR SELECT
    TO anon, authenticated
    USING (true);

CREATE POLICY "Authenticated can manage menu items"
    ON menu_items FOR ALL
    TO authenticated
    USING (true)
    WITH CHECK (true);

-- Update timestamp trigger
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_pages_updated_at
    BEFORE UPDATE ON pages
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Helper function to get page hierarchy
CREATE OR REPLACE FUNCTION get_page_children(parent_page_id BIGINT)
RETURNS TABLE (
    id BIGINT,
    title TEXT,
    slug TEXT,
    parent_id BIGINT,
    level INTEGER
) AS $$
WITH RECURSIVE page_tree AS (
    -- Base case: direct children
    SELECT
        p.id,
        p.title,
        p.slug,
        p.parent_id,
        1 as level
    FROM pages p
    WHERE p.parent_id = parent_page_id

    UNION ALL

    -- Recursive case: children of children
    SELECT
        p.id,
        p.title,
        p.slug,
        p.parent_id,
        pt.level + 1
    FROM pages p
    INNER JOIN page_tree pt ON p.parent_id = pt.id
)
SELECT * FROM page_tree ORDER BY level, title;
$$ LANGUAGE sql STABLE;

-- Helper function to get menu structure
CREATE OR REPLACE FUNCTION get_menu_structure(menu_location TEXT)
RETURNS TABLE (
    id INTEGER,
    title TEXT,
    url TEXT,
    parent_id INTEGER,
    menu_order INTEGER,
    page_slug TEXT
) AS $$
    SELECT
        mi.id,
        mi.title,
        mi.url,
        mi.parent_id,
        mi.menu_order,
        p.slug as page_slug
    FROM menu_items mi
    LEFT JOIN menus m ON mi.menu_id = m.id
    LEFT JOIN pages p ON mi.page_id = p.id
    WHERE m.location = menu_location
    ORDER BY mi.menu_order;
$$ LANGUAGE sql STABLE;

-- Insert default menus
INSERT INTO menus (name, location)
VALUES
    ('Primary Navigation', 'primary'),
    ('Footer Navigation', 'footer')
ON CONFLICT DO NOTHING;
