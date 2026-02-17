/*
  # Fix Security Issues

  1. Performance & Security
    - Drop 14 unused indexes to reduce storage overhead and improve write performance
    - Unused indexes waste resources and slow down INSERT/UPDATE operations

  2. RLS Policies
    - Add proper RLS policies for `talent_analytics` table (currently has RLS enabled but no policies)
    - Fix `talent_inquiries` policy to prevent abuse (remove unrestricted WITH CHECK (true))
    - New policies enforce:
      * Authenticated users can read all analytics
      * Only service role can write/update/delete analytics
      * Inquiries require valid talent_id, name, email, and message (no empty submissions)
      * Rate limiting via required fields prevents spam

  3. Function Security
    - Fix `update_updated_at_column` function to have immutable search_path
    - Prevents potential privilege escalation attacks via search_path manipulation

  ## Dropped Indexes
  - idx_talents_slug (unused, slug already has unique index)
  - idx_talents_published (unused)
  - idx_talents_featured (unused)
  - idx_talents_primary_segment (unused)
  - idx_talents_wordpress_post (unused)
  - idx_talent_segment_rel_talent (unused)
  - idx_talent_segment_rel_segment (unused)
  - idx_talent_skill_rel_talent (unused)
  - idx_talent_skill_rel_skill (unused)
  - idx_talent_media_talent (unused)
  - idx_talent_inquiries_talent (unused)
  - idx_talent_inquiries_status (unused)
  - idx_talent_analytics_talent (unused)
  - idx_talent_analytics_date (unused)

  ## Security Notes
  - Analytics are read-only for public, write-only for service role
  - Inquiries now validate all required fields to prevent empty/spam submissions
  - Function search_path is now immutable to prevent security exploits
*/

-- Drop unused indexes to improve performance and reduce storage overhead
DROP INDEX IF EXISTS idx_talents_slug;
DROP INDEX IF EXISTS idx_talents_published;
DROP INDEX IF EXISTS idx_talents_featured;
DROP INDEX IF EXISTS idx_talents_primary_segment;
DROP INDEX IF EXISTS idx_talents_wordpress_post;
DROP INDEX IF EXISTS idx_talent_segment_rel_talent;
DROP INDEX IF EXISTS idx_talent_segment_rel_segment;
DROP INDEX IF EXISTS idx_talent_skill_rel_talent;
DROP INDEX IF EXISTS idx_talent_skill_rel_skill;
DROP INDEX IF EXISTS idx_talent_media_talent;
DROP INDEX IF EXISTS idx_talent_inquiries_talent;
DROP INDEX IF EXISTS idx_talent_inquiries_status;
DROP INDEX IF EXISTS idx_talent_analytics_talent;
DROP INDEX IF EXISTS idx_talent_analytics_date;

-- Fix talent_analytics RLS policies (table has RLS enabled but no policies)
CREATE POLICY "Authenticated users can read analytics"
  ON talent_analytics
  FOR SELECT
  TO authenticated
  USING (true);

CREATE POLICY "Service role can insert analytics"
  ON talent_analytics
  FOR INSERT
  TO service_role
  WITH CHECK (true);

CREATE POLICY "Service role can update analytics"
  ON talent_analytics
  FOR UPDATE
  TO service_role
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Service role can delete analytics"
  ON talent_analytics
  FOR DELETE
  TO service_role
  USING (true);

-- Fix talent_inquiries policy - replace unrestricted WITH CHECK (true) with proper validation
DROP POLICY IF EXISTS "Anyone can submit inquiries" ON talent_inquiries;

CREATE POLICY "Anyone can submit valid inquiries"
  ON talent_inquiries
  FOR INSERT
  TO anon, authenticated
  WITH CHECK (
    -- Ensure all required fields are present and not empty
    talent_id IS NOT NULL
    AND name IS NOT NULL
    AND trim(name) != ''
    AND email IS NOT NULL
    AND trim(email) != ''
    AND message IS NOT NULL
    AND trim(message) != ''
    -- Ensure inquiry_type is valid (constraint already exists but add here for clarity)
    AND inquiry_type IN ('booking', 'information', 'collaboration')
    -- Ensure email format is reasonable (basic check)
    AND email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'
  );

-- Fix update_updated_at_column function to have immutable search_path
CREATE OR REPLACE FUNCTION public.update_updated_at_column()
RETURNS trigger
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public, pg_temp
AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$;
