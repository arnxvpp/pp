# PremierPlug Database Architecture

**Version:** 1.0.0
**Date:** 2025-10-17

---

## Overview

Complete database architecture for PremierPlug WordPress platform with dual-database system:
- **WordPress MySQL** - Content management, posts, pages, users
- **Supabase PostgreSQL** - Talent management, analytics, real-time features

---

## Database Systems

### 1. WordPress MySQL Database

**Purpose:** Core CMS functionality

**Tables (Standard + Custom):**
```
wp_posts                - Posts, pages, talents
wp_postmeta             - Post metadata
wp_users                - User accounts
wp_usermeta             - User metadata
wp_terms                - Taxonomy terms
wp_term_taxonomy        - Taxonomy definitions
wp_term_relationships   - Post-term relationships
wp_comments             - Comments
wp_commentmeta          - Comment metadata
wp_options              - Site settings
```

**Custom Post Types:**
- `talent` - Talent profiles

**Custom Taxonomies:**
- `talent_category` - Talent categories
- `talent_skill` - Skills and expertise
- `talent_segment` - Business segments

---

### 2. Supabase PostgreSQL Database

**Purpose:** Extended talent management, real-time sync, analytics

**Schema: talent_management**

#### Table 1: talent_profiles
```sql
CREATE TABLE talent_profiles (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    wp_post_id integer UNIQUE,
    name text NOT NULL,
    email text UNIQUE NOT NULL,
    phone text,
    bio text,
    rate_type text CHECK (rate_type IN ('hourly', 'daily', 'project')),
    rate_amount numeric(10,2),
    status text DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'pending')),
    featured boolean DEFAULT false,
    metadata jsonb DEFAULT '{}',
    created_at timestamptz DEFAULT now(),
    updated_at timestamptz DEFAULT now()
);
```

**Indexes:**
- PRIMARY KEY on `id`
- UNIQUE INDEX on `wp_post_id`
- UNIQUE INDEX on `email`
- INDEX on `status`
- INDEX on `featured`

**RLS Policies:**
```sql
-- Public can view active talents
CREATE POLICY "Public view active" ON talent_profiles
    FOR SELECT USING (status = 'active');

-- Authenticated users full access
CREATE POLICY "Auth full access" ON talent_profiles
    FOR ALL TO authenticated USING (true) WITH CHECK (true);
```

---

#### Table 2: talent_categories
```sql
CREATE TABLE talent_categories (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id) ON DELETE CASCADE,
    wp_term_id integer,
    category_name text NOT NULL,
    created_at timestamptz DEFAULT now()
);
```

---

#### Table 3: talent_skills
```sql
CREATE TABLE talent_skills (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id) ON DELETE CASCADE,
    wp_term_id integer,
    skill_name text NOT NULL,
    proficiency text CHECK (proficiency IN ('beginner', 'intermediate', 'advanced', 'expert')),
    created_at timestamptz DEFAULT now()
);
```

---

#### Table 4: talent_segments
```sql
CREATE TABLE talent_segments (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id) ON DELETE CASCADE,
    segment_name text NOT NULL,
    is_primary boolean DEFAULT false,
    created_at timestamptz DEFAULT now()
);
```

---

#### Table 5: talent_availability
```sql
CREATE TABLE talent_availability (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id) ON DELETE CASCADE,
    date date NOT NULL,
    status text DEFAULT 'available' CHECK (status IN ('available', 'booked', 'tentative', 'unavailable')),
    notes text,
    created_at timestamptz DEFAULT now(),
    UNIQUE(talent_id, date)
);
```

---

#### Table 6: talent_media
```sql
CREATE TABLE talent_media (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id) ON DELETE CASCADE,
    wp_attachment_id integer,
    media_type text CHECK (media_type IN ('photo', 'video', 'document', 'audio')),
    media_url text NOT NULL,
    title text,
    description text,
    display_order integer DEFAULT 0,
    is_featured boolean DEFAULT false,
    created_at timestamptz DEFAULT now()
);
```

---

#### Table 7: talent_testimonials
```sql
CREATE TABLE talent_testimonials (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id) ON DELETE CASCADE,
    client_name text NOT NULL,
    client_company text,
    testimonial text NOT NULL,
    rating integer CHECK (rating >= 1 AND rating <= 5),
    is_featured boolean DEFAULT false,
    status text DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    created_at timestamptz DEFAULT now()
);
```

---

#### Table 8: talent_inquiries
```sql
CREATE TABLE talent_inquiries (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id),
    wp_talent_id integer,
    name text NOT NULL,
    email text NOT NULL,
    phone text,
    company text,
    message text NOT NULL,
    status text DEFAULT 'new' CHECK (status IN ('new', 'contacted', 'qualified', 'converted', 'closed')),
    ip_address text,
    user_agent text,
    created_at timestamptz DEFAULT now(),
    updated_at timestamptz DEFAULT now()
);
```

---

#### Table 9: talent_sync_log
```sql
CREATE TABLE talent_sync_log (
    id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    talent_id uuid REFERENCES talent_profiles(id),
    wp_post_id integer,
    action text NOT NULL CHECK (action IN ('create', 'update', 'delete')),
    status text NOT NULL CHECK (status IN ('success', 'failed')),
    error_message text,
    synced_data jsonb,
    created_at timestamptz DEFAULT now()
);
```

---

## Entity Relationship Diagram

```
talent_profiles (1) ─────< (N) talent_categories
       │
       ├─────< (N) talent_skills
       │
       ├─────< (N) talent_segments
       │
       ├─────< (N) talent_availability
       │
       ├─────< (N) talent_media
       │
       ├─────< (N) talent_testimonials
       │
       ├─────< (N) talent_inquiries
       │
       └─────< (N) talent_sync_log
```

---

## Data Flow

### WordPress → Supabase Sync

```
1. User creates/updates talent in WordPress
2. Post save hook triggers
3. Plugin validates data
4. Data sent to Supabase via API
5. Supabase stores with uuid
6. wp_post_id stored for reference
7. Sync logged in talent_sync_log
```

### Supabase → WordPress Sync

```
1. Real-time change detected in Supabase
2. Webhook triggered
3. WordPress receives update
4. Data validated
5. Post updated in WordPress
6. Sync logged
```

---

## Backup Strategy

### WordPress MySQL Backup

**Frequency:** Daily
**Method:** UpdraftPlus plugin
**Storage:** Cloud (Google Drive / Dropbox)
**Retention:** 30 days

**Manual Backup:**
```bash
mysqldump -u username -p database_name > backup-$(date +%Y%m%d).sql
```

### Supabase Backup

**Frequency:** Automatic (Supabase manages)
**Point-in-time Recovery:** 7 days (free tier), 30 days (pro)
**Manual Export:**
```bash
# Via Supabase CLI
supabase db dump > supabase-backup-$(date +%Y%m%d).sql
```

---

## Performance Optimization

### WordPress MySQL

**Indexes:**
```sql
-- Add indexes for custom queries
ALTER TABLE wp_postmeta ADD INDEX idx_meta_key_value (meta_key, meta_value(50));
ALTER TABLE wp_posts ADD INDEX idx_type_status (post_type, post_status);
```

**Query Caching:**
- W3 Total Cache (Database Cache enabled)
- Object caching with Redis/Memcached (optional)

### Supabase PostgreSQL

**Indexes Created:**
- All primary keys (automatic)
- Foreign keys (automatic)
- Custom indexes on frequently queried fields

**Connection Pooling:**
- Managed by Supabase (automatic)
- Max connections: 100 (adjustable)

---

## Security

### WordPress MySQL

**Protection:**
- Strong database password
- Firewall rules (only allow WordPress server)
- Regular security scans (Wordfence)
- SQL injection prevention (prepared statements)

**User Permissions:**
```sql
GRANT SELECT, INSERT, UPDATE, DELETE ON database_name.* TO 'wp_user'@'localhost';
```

### Supabase PostgreSQL

**Row Level Security (RLS):**
- Enabled on all tables
- Public can only view active talents
- Authenticated users have full access
- Service role for admin operations

**API Security:**
- Anonymous key for public read
- Service key for admin operations
- JWT authentication for users
- Rate limiting enabled

---

## Migration Procedures

### From MySQL to Supabase

```php
// WordPress function to sync all talents
function sync_all_talents_to_supabase() {
    $talents = get_posts(array(
        'post_type' => 'talent',
        'posts_per_page' => -1
    ));

    foreach ($talents as $talent) {
        // Sync logic in class-talent-sync.php
        PremierPlug_Talent_Sync::sync_to_supabase($talent->ID);
    }
}
```

### From Supabase to MySQL

```sql
-- Export Supabase data
COPY (SELECT * FROM talent_profiles) TO '/tmp/talents.csv' CSV HEADER;

-- Import to WordPress via CSV import feature
-- Tools → Import/Export → Import CSV
```

---

## Monitoring

### WordPress MySQL

**Tools:**
- phpMyAdmin - Database management
- WordPress Query Monitor - Query analysis
- New Relic - Performance monitoring (optional)

**Key Metrics:**
- Query execution time
- Slow query log
- Database size
- Connection count

### Supabase PostgreSQL

**Tools:**
- Supabase Dashboard - Real-time metrics
- Built-in query analyzer
- Connection pool monitor

**Key Metrics:**
- API response time
- Database size
- Active connections
- RLS policy performance

---

## Troubleshooting

### Common Issues

**Issue:** WordPress-Supabase sync failing
**Solution:**
```php
// Check sync log
SELECT * FROM talent_sync_log
WHERE status = 'failed'
ORDER BY created_at DESC
LIMIT 10;

// Manually re-sync
wp_talent_resync --post-id=123
```

**Issue:** Slow queries
**Solution:**
```sql
-- Analyze query performance
EXPLAIN ANALYZE SELECT * FROM talent_profiles WHERE status = 'active';

-- Add missing indexes
CREATE INDEX IF NOT EXISTS idx_status ON talent_profiles(status);
```

**Issue:** Connection limit exceeded
**Solution:**
- Enable connection pooling
- Optimize query efficiency
- Upgrade Supabase plan

---

## Schema Version History

**v1.0.0** (2025-10-17)
- Initial schema creation
- 9 tables implemented
- RLS policies configured
- Indexes optimized

---

## Maintenance Schedule

**Daily:**
- Automated backups
- Sync status monitoring

**Weekly:**
- Query performance review
- Index usage analysis
- Slow query optimization

**Monthly:**
- Full database audit
- Backup restoration test
- Security review
- Capacity planning

---

**Document Version:** 1.0.0
**Last Updated:** 2025-10-17
**Maintained By:** PremierPlug Development Team
