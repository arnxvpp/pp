/*
  # Article Management System - Supabase Migration

  Complete database setup for article management and talent relationships.

  ## Tables Created

  1. **talent_articles** - Stores all article data (press releases, blog, awards, news, media coverage)
  2. **talent_article_relationships** - Many-to-many relationships between talents and articles

  ## Features

  - Row Level Security (RLS) enabled
  - Public read access for published articles
  - Authenticated full access for admin operations
  - Indexes for optimal query performance
  - Foreign key constraints for data integrity
  - Automatic timestamps

  ## How to Run

  1. Login to Supabase Dashboard
  2. Go to SQL Editor
  3. Copy and paste this entire file
  4. Click "Run" button
  5. Verify tables created in Table Editor

  Created: December 2024
  Version: 1.0
*/

-- ============================================================================
-- Table 1: talent_articles
-- ============================================================================

CREATE TABLE IF NOT EXISTS talent_articles (
    id BIGSERIAL PRIMARY KEY,
    wordpress_post_id BIGINT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    content TEXT,
    excerpt TEXT,
    article_type TEXT NOT NULL CHECK (article_type IN (
        'press_release',
        'blog_article',
        'award',
        'news',
        'media_coverage'
    )),
    featured_image_url TEXT,
    publication_date TIMESTAMPTZ,
    source_name TEXT,
    source_url TEXT,
    author_name TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    slug TEXT NOT NULL,
    status TEXT DEFAULT 'publish' CHECK (status IN ('publish', 'draft', 'pending', 'private')),
    view_count INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Add comment to table
COMMENT ON TABLE talent_articles IS 'Stores articles related to talents including press releases, blog posts, awards, news, and media coverage';

-- Add comments to columns
COMMENT ON COLUMN talent_articles.wordpress_post_id IS 'Unique WordPress post ID for sync reference';
COMMENT ON COLUMN talent_articles.article_type IS 'Type of article: press_release, blog_article, award, news, or media_coverage';
COMMENT ON COLUMN talent_articles.is_featured IS 'Whether this article is featured prominently on talent profiles';
COMMENT ON COLUMN talent_articles.publication_date IS 'Original publication date (may differ from created_at)';

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_articles_type ON talent_articles(article_type);
CREATE INDEX IF NOT EXISTS idx_articles_status ON talent_articles(status);
CREATE INDEX IF NOT EXISTS idx_articles_featured ON talent_articles(is_featured) WHERE is_featured = TRUE;
CREATE INDEX IF NOT EXISTS idx_articles_published ON talent_articles(publication_date DESC) WHERE status = 'publish';
CREATE INDEX IF NOT EXISTS idx_articles_wordpress_id ON talent_articles(wordpress_post_id);

-- Enable Row Level Security
ALTER TABLE talent_articles ENABLE ROW LEVEL SECURITY;

-- RLS Policy: Public read access for published articles
CREATE POLICY "Allow public read access for published articles"
    ON talent_articles
    FOR SELECT
    TO anon
    USING (status = 'publish');

-- RLS Policy: Authenticated users have full access
CREATE POLICY "Allow authenticated users full access"
    ON talent_articles
    FOR ALL
    TO authenticated
    USING (true)
    WITH CHECK (true);

-- ============================================================================
-- Table 2: talent_article_relationships
-- ============================================================================

CREATE TABLE IF NOT EXISTS talent_article_relationships (
    id BIGSERIAL PRIMARY KEY,
    talent_id BIGINT NOT NULL,
    article_id BIGINT NOT NULL REFERENCES talent_articles(id) ON DELETE CASCADE,
    is_primary_talent BOOLEAN DEFAULT FALSE,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(talent_id, article_id)
);

-- Add comment to table
COMMENT ON TABLE talent_article_relationships IS 'Many-to-many relationships between talents and articles';

-- Add comments to columns
COMMENT ON COLUMN talent_article_relationships.talent_id IS 'WordPress talent post ID';
COMMENT ON COLUMN talent_article_relationships.article_id IS 'Reference to talent_articles.id';
COMMENT ON COLUMN talent_article_relationships.is_primary_talent IS 'Whether this talent is the primary subject of the article';
COMMENT ON COLUMN talent_article_relationships.display_order IS 'Order in which talents should be displayed (0 = first)';

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_rel_talent ON talent_article_relationships(talent_id);
CREATE INDEX IF NOT EXISTS idx_rel_article ON talent_article_relationships(article_id);
CREATE INDEX IF NOT EXISTS idx_rel_primary ON talent_article_relationships(is_primary_talent) WHERE is_primary_talent = TRUE;
CREATE INDEX IF NOT EXISTS idx_rel_display_order ON talent_article_relationships(display_order);

-- Enable Row Level Security
ALTER TABLE talent_article_relationships ENABLE ROW LEVEL SECURITY;

-- RLS Policy: Public read access
CREATE POLICY "Allow public read access to relationships"
    ON talent_article_relationships
    FOR SELECT
    TO anon
    USING (true);

-- RLS Policy: Authenticated users have full access
CREATE POLICY "Allow authenticated users full access to relationships"
    ON talent_article_relationships
    FOR ALL
    TO authenticated
    USING (true)
    WITH CHECK (true);

-- ============================================================================
-- Functions and Triggers
-- ============================================================================

-- Function: Update updated_at timestamp automatically
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger: Auto-update updated_at on talent_articles
DROP TRIGGER IF EXISTS update_talent_articles_updated_at ON talent_articles;
CREATE TRIGGER update_talent_articles_updated_at
    BEFORE UPDATE ON talent_articles
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Function: Get article count by type for a talent
CREATE OR REPLACE FUNCTION get_talent_article_count(p_talent_id BIGINT, p_article_type TEXT DEFAULT NULL)
RETURNS INTEGER AS $$
DECLARE
    article_count INTEGER;
BEGIN
    IF p_article_type IS NULL THEN
        SELECT COUNT(DISTINCT r.article_id)
        INTO article_count
        FROM talent_article_relationships r
        INNER JOIN talent_articles a ON r.article_id = a.id
        WHERE r.talent_id = p_talent_id AND a.status = 'publish';
    ELSE
        SELECT COUNT(DISTINCT r.article_id)
        INTO article_count
        FROM talent_article_relationships r
        INNER JOIN talent_articles a ON r.article_id = a.id
        WHERE r.talent_id = p_talent_id
          AND a.article_type = p_article_type
          AND a.status = 'publish';
    END IF;

    RETURN COALESCE(article_count, 0);
END;
$$ LANGUAGE plpgsql;

-- Function: Get articles for a talent with pagination
CREATE OR REPLACE FUNCTION get_talent_articles(
    p_talent_id BIGINT,
    p_article_type TEXT DEFAULT NULL,
    p_limit INTEGER DEFAULT 10,
    p_offset INTEGER DEFAULT 0
)
RETURNS TABLE (
    article_id BIGINT,
    title TEXT,
    excerpt TEXT,
    article_type TEXT,
    featured_image_url TEXT,
    publication_date TIMESTAMPTZ,
    is_featured BOOLEAN,
    is_primary_talent BOOLEAN,
    source_name TEXT
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        a.id,
        a.title,
        a.excerpt,
        a.article_type,
        a.featured_image_url,
        a.publication_date,
        a.is_featured,
        r.is_primary_talent,
        a.source_name
    FROM talent_article_relationships r
    INNER JOIN talent_articles a ON r.article_id = a.id
    WHERE r.talent_id = p_talent_id
      AND a.status = 'publish'
      AND (p_article_type IS NULL OR a.article_type = p_article_type)
    ORDER BY r.is_primary_talent DESC, a.publication_date DESC
    LIMIT p_limit
    OFFSET p_offset;
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- Sample Data (Optional - Remove if not needed)
-- ============================================================================

-- Uncomment below to insert sample data for testing

/*
-- Sample Article 1: Press Release
INSERT INTO talent_articles (
    wordpress_post_id,
    title,
    content,
    excerpt,
    article_type,
    publication_date,
    source_name,
    source_url,
    author_name,
    is_featured,
    slug,
    status
) VALUES (
    9001,
    'Award-Winning Actor Joins New Film',
    'Full article content here...',
    'Exciting news as talented actor joins blockbuster production.',
    'press_release',
    NOW(),
    'Hollywood Reporter',
    'https://example.com/article',
    'Jane Smith',
    TRUE,
    'award-winning-actor-joins-new-film',
    'publish'
);

-- Sample Article 2: Award
INSERT INTO talent_articles (
    wordpress_post_id,
    title,
    content,
    excerpt,
    article_type,
    publication_date,
    source_name,
    is_featured,
    slug,
    status
) VALUES (
    9002,
    'Best Actor Award 2024',
    'Full article content here...',
    'Wins prestigious award for outstanding performance.',
    'award',
    NOW(),
    'Academy Awards',
    TRUE,
    'best-actor-award-2024',
    'publish'
);
*/

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Check tables created successfully
SELECT table_name, table_type
FROM information_schema.tables
WHERE table_schema = 'public'
  AND table_name IN ('talent_articles', 'talent_article_relationships');

-- Check indexes
SELECT tablename, indexname, indexdef
FROM pg_indexes
WHERE tablename IN ('talent_articles', 'talent_article_relationships')
ORDER BY tablename, indexname;

-- Check RLS policies
SELECT schemaname, tablename, policyname, permissive, roles, cmd, qual
FROM pg_policies
WHERE tablename IN ('talent_articles', 'talent_article_relationships')
ORDER BY tablename, policyname;

-- ============================================================================
-- Complete!
-- ============================================================================

/*
Your article management system database is ready!

Next steps:
1. Verify tables exist in Supabase Table Editor
2. Test the connection from WordPress admin
3. Create your first article and link to a talent
4. Articles will auto-sync from WordPress to Supabase

Useful queries:

-- Get all articles
SELECT * FROM talent_articles WHERE status = 'publish' ORDER BY publication_date DESC;

-- Get article count by type
SELECT article_type, COUNT(*) as count FROM talent_articles WHERE status = 'publish' GROUP BY article_type;

-- Get talents for an article
SELECT * FROM talent_article_relationships WHERE article_id = 1;

-- Get articles for a talent (using function)
SELECT * FROM get_talent_articles(123, NULL, 10, 0);

-- Get article count for a talent
SELECT get_talent_article_count(123, 'press_release');
*/
