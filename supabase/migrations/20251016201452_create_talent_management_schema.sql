/*
  # Talent Management System - Initial Schema

  ## Overview
  Complete database schema for PremierPlug talent management system supporting
  Digital Media, Television, Voiceovers, Speakers, and Motion Pictures segments.

  ## New Tables Created

  ### 1. `talent_segments`
  Predefined talent categories matching site navigation structure
  - `id` (uuid, primary key)
  - `name` (text) - Segment name: Digital Media, Television, etc.
  - `slug` (text) - URL-friendly identifier
  - `description` (text) - Segment description
  - `display_order` (integer) - Sort order for display
  - `created_at` (timestamptz)

  ### 2. `talents`
  Core talent profile information
  - `id` (uuid, primary key)
  - `name` (text) - Talent full name
  - `slug` (text, unique) - URL-friendly identifier
  - `headline` (text) - Short tagline/title
  - `bio` (text) - Full biography
  - `primary_segment_id` (uuid) - Main talent category
  - `featured` (boolean) - Featured on homepage
  - `availability_status` (text) - available, booked, unavailable
  - `experience_years` (integer) - Years of experience
  - `profile_image_url` (text) - Main profile photo
  - `published` (boolean) - Visibility status
  - `wordpress_post_id` (bigint) - Link to WordPress post
  - `view_count` (integer) - Profile view tracking
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ### 3. `talent_segment_relationships`
  Many-to-many relationship between talents and segments
  - `id` (uuid, primary key)
  - `talent_id` (uuid) - References talents table
  - `segment_id` (uuid) - References talent_segments table
  - `created_at` (timestamptz)

  ### 4. `talent_skills`
  Skills and specializations for filtering
  - `id` (uuid, primary key)
  - `name` (text, unique) - Skill name
  - `slug` (text, unique) - URL-friendly identifier
  - `created_at` (timestamptz)

  ### 5. `talent_skill_relationships`
  Many-to-many relationship between talents and skills
  - `id` (uuid, primary key)
  - `talent_id` (uuid) - References talents table
  - `skill_id` (uuid) - References talent_skills table
  - `created_at` (timestamptz)

  ### 6. `talent_media`
  Portfolio items: images, videos, audio, documents
  - `id` (uuid, primary key)
  - `talent_id` (uuid) - References talents table
  - `media_type` (text) - image, video, audio, document
  - `file_url` (text) - Media file location
  - `thumbnail_url` (text) - Preview image
  - `title` (text) - Media title
  - `description` (text) - Media description
  - `order_position` (integer) - Display order
  - `created_at` (timestamptz)

  ### 7. `talent_contacts`
  Contact information and preferences
  - `id` (uuid, primary key)
  - `talent_id` (uuid) - References talents table
  - `email` (text) - Contact email
  - `phone` (text) - Contact phone
  - `website` (text) - Personal website
  - `social_instagram` (text) - Instagram URL
  - `social_linkedin` (text) - LinkedIn URL
  - `social_twitter` (text) - Twitter URL
  - `social_youtube` (text) - YouTube URL
  - `preferred_contact_method` (text) - Email, phone, etc.
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ### 8. `talent_inquiries`
  Form submissions and booking requests
  - `id` (uuid, primary key)
  - `talent_id` (uuid) - References talents table
  - `name` (text) - Inquirer name
  - `email` (text) - Inquirer email
  - `phone` (text) - Inquirer phone
  - `organization` (text) - Company/organization
  - `country` (text) - Country of origin
  - `message` (text) - Inquiry details
  - `inquiry_type` (text) - booking, information, collaboration
  - `status` (text) - new, contacted, closed
  - `ip_address` (text) - Submitter IP
  - `user_agent` (text) - Browser info
  - `created_at` (timestamptz)

  ### 9. `talent_analytics`
  Profile view tracking and engagement metrics
  - `id` (uuid, primary key)
  - `talent_id` (uuid) - References talents table
  - `event_type` (text) - view, inquiry, portfolio_click
  - `event_date` (date) - Date of event
  - `count` (integer) - Number of events
  - `created_at` (timestamptz)

  ## Security
  - Enable RLS on all tables
  - Public read access for published talents only
  - Admin-only write access
  - Secure inquiry data with restricted access

  ## Notes
  - All timestamps use timestamptz for timezone awareness
  - UUID primary keys for security and scalability
  - Indexes on frequently queried fields for performance
*/

-- Create talent_segments table
CREATE TABLE IF NOT EXISTS talent_segments (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL UNIQUE,
  slug text NOT NULL UNIQUE,
  description text,
  display_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now()
);

-- Create talents table
CREATE TABLE IF NOT EXISTS talents (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  slug text NOT NULL UNIQUE,
  headline text,
  bio text,
  primary_segment_id uuid REFERENCES talent_segments(id) ON DELETE SET NULL,
  featured boolean DEFAULT false,
  availability_status text DEFAULT 'available' CHECK (availability_status IN ('available', 'booked', 'unavailable')),
  experience_years integer DEFAULT 0,
  profile_image_url text,
  published boolean DEFAULT true,
  wordpress_post_id bigint,
  view_count integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Create talent_segment_relationships table
CREATE TABLE IF NOT EXISTS talent_segment_relationships (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  talent_id uuid REFERENCES talents(id) ON DELETE CASCADE,
  segment_id uuid REFERENCES talent_segments(id) ON DELETE CASCADE,
  created_at timestamptz DEFAULT now(),
  UNIQUE(talent_id, segment_id)
);

-- Create talent_skills table
CREATE TABLE IF NOT EXISTS talent_skills (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL UNIQUE,
  slug text NOT NULL UNIQUE,
  created_at timestamptz DEFAULT now()
);

-- Create talent_skill_relationships table
CREATE TABLE IF NOT EXISTS talent_skill_relationships (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  talent_id uuid REFERENCES talents(id) ON DELETE CASCADE,
  skill_id uuid REFERENCES talent_skills(id) ON DELETE CASCADE,
  created_at timestamptz DEFAULT now(),
  UNIQUE(talent_id, skill_id)
);

-- Create talent_media table
CREATE TABLE IF NOT EXISTS talent_media (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  talent_id uuid REFERENCES talents(id) ON DELETE CASCADE,
  media_type text NOT NULL CHECK (media_type IN ('image', 'video', 'audio', 'document')),
  file_url text NOT NULL,
  thumbnail_url text,
  title text,
  description text,
  order_position integer DEFAULT 0,
  created_at timestamptz DEFAULT now()
);

-- Create talent_contacts table
CREATE TABLE IF NOT EXISTS talent_contacts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  talent_id uuid REFERENCES talents(id) ON DELETE CASCADE UNIQUE,
  email text,
  phone text,
  website text,
  social_instagram text,
  social_linkedin text,
  social_twitter text,
  social_youtube text,
  preferred_contact_method text DEFAULT 'email',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Create talent_inquiries table
CREATE TABLE IF NOT EXISTS talent_inquiries (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  talent_id uuid REFERENCES talents(id) ON DELETE SET NULL,
  name text NOT NULL,
  email text NOT NULL,
  phone text,
  organization text,
  country text,
  message text NOT NULL,
  inquiry_type text DEFAULT 'information' CHECK (inquiry_type IN ('booking', 'information', 'collaboration')),
  status text DEFAULT 'new' CHECK (status IN ('new', 'contacted', 'closed')),
  ip_address text,
  user_agent text,
  created_at timestamptz DEFAULT now()
);

-- Create talent_analytics table
CREATE TABLE IF NOT EXISTS talent_analytics (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  talent_id uuid REFERENCES talents(id) ON DELETE CASCADE,
  event_type text NOT NULL CHECK (event_type IN ('view', 'inquiry', 'portfolio_click', 'contact_click')),
  event_date date NOT NULL DEFAULT CURRENT_DATE,
  count integer DEFAULT 1,
  created_at timestamptz DEFAULT now(),
  UNIQUE(talent_id, event_type, event_date)
);

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_talents_slug ON talents(slug);
CREATE INDEX IF NOT EXISTS idx_talents_published ON talents(published);
CREATE INDEX IF NOT EXISTS idx_talents_featured ON talents(featured);
CREATE INDEX IF NOT EXISTS idx_talents_primary_segment ON talents(primary_segment_id);
CREATE INDEX IF NOT EXISTS idx_talents_wordpress_post ON talents(wordpress_post_id);
CREATE INDEX IF NOT EXISTS idx_talent_segment_rel_talent ON talent_segment_relationships(talent_id);
CREATE INDEX IF NOT EXISTS idx_talent_segment_rel_segment ON talent_segment_relationships(segment_id);
CREATE INDEX IF NOT EXISTS idx_talent_skill_rel_talent ON talent_skill_relationships(talent_id);
CREATE INDEX IF NOT EXISTS idx_talent_skill_rel_skill ON talent_skill_relationships(skill_id);
CREATE INDEX IF NOT EXISTS idx_talent_media_talent ON talent_media(talent_id);
CREATE INDEX IF NOT EXISTS idx_talent_inquiries_talent ON talent_inquiries(talent_id);
CREATE INDEX IF NOT EXISTS idx_talent_inquiries_status ON talent_inquiries(status);
CREATE INDEX IF NOT EXISTS idx_talent_analytics_talent ON talent_analytics(talent_id);
CREATE INDEX IF NOT EXISTS idx_talent_analytics_date ON talent_analytics(event_date);

-- Enable Row Level Security
ALTER TABLE talent_segments ENABLE ROW LEVEL SECURITY;
ALTER TABLE talents ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_segment_relationships ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_skills ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_skill_relationships ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_media ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_contacts ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_inquiries ENABLE ROW LEVEL SECURITY;
ALTER TABLE talent_analytics ENABLE ROW LEVEL SECURITY;

-- RLS Policies for public read access to published talents
CREATE POLICY "Public can view published talent segments"
  ON talent_segments FOR SELECT
  TO anon, authenticated
  USING (true);

CREATE POLICY "Public can view published talents"
  ON talents FOR SELECT
  TO anon, authenticated
  USING (published = true);

CREATE POLICY "Public can view segment relationships for published talents"
  ON talent_segment_relationships FOR SELECT
  TO anon, authenticated
  USING (
    EXISTS (
      SELECT 1 FROM talents
      WHERE talents.id = talent_segment_relationships.talent_id
      AND talents.published = true
    )
  );

CREATE POLICY "Public can view skills"
  ON talent_skills FOR SELECT
  TO anon, authenticated
  USING (true);

CREATE POLICY "Public can view skill relationships for published talents"
  ON talent_skill_relationships FOR SELECT
  TO anon, authenticated
  USING (
    EXISTS (
      SELECT 1 FROM talents
      WHERE talents.id = talent_skill_relationships.talent_id
      AND talents.published = true
    )
  );

CREATE POLICY "Public can view media for published talents"
  ON talent_media FOR SELECT
  TO anon, authenticated
  USING (
    EXISTS (
      SELECT 1 FROM talents
      WHERE talents.id = talent_media.talent_id
      AND talents.published = true
    )
  );

CREATE POLICY "Public can view contacts for published talents"
  ON talent_contacts FOR SELECT
  TO anon, authenticated
  USING (
    EXISTS (
      SELECT 1 FROM talents
      WHERE talents.id = talent_contacts.talent_id
      AND talents.published = true
    )
  );

CREATE POLICY "Anyone can submit inquiries"
  ON talent_inquiries FOR INSERT
  TO anon, authenticated
  WITH CHECK (true);

-- Insert default talent segments matching site navigation
INSERT INTO talent_segments (name, slug, description, display_order) VALUES
  ('Digital Media', 'digital-media', 'Content creators, influencers, and digital media professionals', 1),
  ('Television', 'television', 'TV personalities, hosts, and television professionals', 2),
  ('Voiceovers', 'voiceovers', 'Voice actors, dubbing artists, and voiceover professionals', 3),
  ('Speakers', 'speakers', 'Public speakers, motivational speakers, and event presenters', 4),
  ('Motion Pictures', 'motion-pictures', 'Film actors, directors, and motion picture professionals', 5)
ON CONFLICT (slug) DO NOTHING;

-- Create function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create triggers for updated_at
CREATE TRIGGER update_talents_updated_at
  BEFORE UPDATE ON talents
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_talent_contacts_updated_at
  BEFORE UPDATE ON talent_contacts
  FOR EACH ROW
  EXECUTE FUNCTION update_updated_at_column();
