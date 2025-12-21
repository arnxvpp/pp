<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Settings {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_page'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_settings_assets'));
    }

    public static function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=talent',
            'Growth & Monetization Settings',
            'Growth Settings',
            'manage_options',
            'pptm-settings',
            array(__CLASS__, 'render_settings_page')
        );
    }

    public static function enqueue_settings_assets($hook) {
        if ($hook !== 'talent_page_pptm-settings') {
            return;
        }

        wp_enqueue_style('pptm-settings', PPTM_PLUGIN_URL . 'assets/css/settings.css', array(), PPTM_VERSION);
        wp_enqueue_script('pptm-settings', PPTM_PLUGIN_URL . 'assets/js/settings.js', array('jquery'), PPTM_VERSION, true);
        wp_enqueue_code_editor(array('type' => 'text/html'));
    }

    public static function register_settings() {
        $settings_groups = array(
            'pptm_seo_settings' => array(
                'pptm_twitter_handle',
                'pptm_twitter_url',
                'pptm_facebook_url',
                'pptm_linkedin_url',
                'pptm_instagram_url',
            ),
            'pptm_monetization_settings' => array(
                'pptm_header_ad_code',
                'pptm_footer_ad_code',
                'pptm_sidebar_ad_code',
                'pptm_in_content_ad_code',
                'pptm_in_content_ad_position',
                'pptm_mobile_sticky_ad_code',
            ),
            'pptm_social_settings' => array(
                'pptm_share_buttons_position',
                'pptm_share_buttons_text',
                'pptm_share_buttons_networks',
            ),
            'pptm_related_settings' => array(
                'pptm_show_related_articles',
                'pptm_related_articles_title',
                'pptm_related_articles_count',
            ),
            'pptm_analytics_settings' => array(
                'pptm_ga4_measurement_id',
                'pptm_analytics_exclude_admin',
                'pptm_analytics_anonymize_ip',
            ),
            'pptm_email_settings' => array(
                'pptm_popup_enabled',
                'pptm_popup_trigger',
                'pptm_popup_trigger_value',
                'pptm_popup_frequency',
                'pptm_popup_title',
                'pptm_popup_subtitle',
                'pptm_popup_button_text',
                'pptm_popup_privacy_text',
                'pptm_popup_custom_form',
                'pptm_popup_hide_admin',
                'pptm_send_welcome_email',
                'pptm_welcome_email_subject',
                'pptm_welcome_email_message',
            ),
            'pptm_speed_settings' => array(
                'pptm_lazy_load_images',
                'pptm_defer_javascript',
                'pptm_async_css',
                'pptm_preload_resources',
                'pptm_browser_caching',
                'pptm_minify_html',
                'pptm_webp_support',
            ),
        );

        foreach ($settings_groups as $group => $settings) {
            foreach ($settings as $setting) {
                register_setting($group, $setting);
            }
        }
    }

    public static function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'seo';
        ?>
        <div class="wrap pptm-settings-wrap">
            <h1>Growth & Monetization Settings</h1>
            <p class="description">Optimize your site for traffic, engagement, and revenue.</p>

            <nav class="nav-tab-wrapper">
                <a href="?post_type=talent&page=pptm-settings&tab=seo" class="nav-tab <?php echo $active_tab === 'seo' ? 'nav-tab-active' : ''; ?>">SEO & Social</a>
                <a href="?post_type=talent&page=pptm-settings&tab=monetization" class="nav-tab <?php echo $active_tab === 'monetization' ? 'nav-tab-active' : ''; ?>">Monetization</a>
                <a href="?post_type=talent&page=pptm-settings&tab=sharing" class="nav-tab <?php echo $active_tab === 'sharing' ? 'nav-tab-active' : ''; ?>">Social Sharing</a>
                <a href="?post_type=talent&page=pptm-settings&tab=related" class="nav-tab <?php echo $active_tab === 'related' ? 'nav-tab-active' : ''; ?>">Related Articles</a>
                <a href="?post_type=talent&page=pptm-settings&tab=analytics" class="nav-tab <?php echo $active_tab === 'analytics' ? 'nav-tab-active' : ''; ?>">Analytics</a>
                <a href="?post_type=talent&page=pptm-settings&tab=email" class="nav-tab <?php echo $active_tab === 'email' ? 'nav-tab-active' : ''; ?>">Email Capture</a>
                <a href="?post_type=talent&page=pptm-settings&tab=performance" class="nav-tab <?php echo $active_tab === 'performance' ? 'nav-tab-active' : ''; ?>">Performance</a>
            </nav>

            <div class="pptm-settings-content">
                <?php
                switch ($active_tab) {
                    case 'seo':
                        self::render_seo_tab();
                        break;
                    case 'monetization':
                        self::render_monetization_tab();
                        break;
                    case 'sharing':
                        self::render_sharing_tab();
                        break;
                    case 'related':
                        self::render_related_tab();
                        break;
                    case 'analytics':
                        self::render_analytics_tab();
                        break;
                    case 'email':
                        self::render_email_tab();
                        break;
                    case 'performance':
                        self::render_performance_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }

    private static function render_seo_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_seo_settings'); ?>
            <h2>SEO & Social Media</h2>
            <p class="description">Configure SEO meta tags, Schema markup, and social media integration.</p>

            <table class="form-table">
                <tr>
                    <th scope="row">Twitter Handle</th>
                    <td>
                        <input type="text" name="pptm_twitter_handle" value="<?php echo esc_attr(get_option('pptm_twitter_handle', '')); ?>" class="regular-text" placeholder="@yourusername">
                        <p class="description">Used in Twitter Card meta tags (without @)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Social Media URLs</th>
                    <td>
                        <p><input type="url" name="pptm_twitter_url" value="<?php echo esc_attr(get_option('pptm_twitter_url', '')); ?>" class="regular-text" placeholder="https://twitter.com/yourusername"></p>
                        <p><input type="url" name="pptm_facebook_url" value="<?php echo esc_attr(get_option('pptm_facebook_url', '')); ?>" class="regular-text" placeholder="https://facebook.com/yourpage"></p>
                        <p><input type="url" name="pptm_linkedin_url" value="<?php echo esc_attr(get_option('pptm_linkedin_url', '')); ?>" class="regular-text" placeholder="https://linkedin.com/company/yourcompany"></p>
                        <p><input type="url" name="pptm_instagram_url" value="<?php echo esc_attr(get_option('pptm_instagram_url', '')); ?>" class="regular-text" placeholder="https://instagram.com/yourusername"></p>
                        <p class="description">Used in Schema.org Organization markup</p>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Automatically Added:</h4>
                <ul>
                    <li>✓ Open Graph meta tags (Facebook, LinkedIn)</li>
                    <li>✓ Twitter Card meta tags</li>
                    <li>✓ Schema.org markup (Article, Person, Organization)</li>
                    <li>✓ Meta descriptions (auto-generated from content)</li>
                    <li>✓ Canonical URLs</li>
                </ul>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function render_monetization_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_monetization_settings'); ?>
            <h2>Ad Zone Management</h2>
            <p class="description">Paste your ad codes (Google AdSense, Media.net, etc.) below. Ads will automatically display on articles.</p>

            <table class="form-table">
                <tr>
                    <th scope="row">Header Ad (728x90 or 970x90)</th>
                    <td>
                        <textarea name="pptm_header_ad_code" rows="5" class="large-text code"><?php echo esc_textarea(get_option('pptm_header_ad_code', '')); ?></textarea>
                        <p class="description">Appears below header. Desktop only. Use for leaderboard ads.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Sidebar Ad (300x250 or 300x600)</th>
                    <td>
                        <textarea name="pptm_sidebar_ad_code" rows="5" class="large-text code"><?php echo esc_textarea(get_option('pptm_sidebar_ad_code', '')); ?></textarea>
                        <p class="description">Appears in sidebar. Use for rectangle or skyscraper ads.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">In-Content Ad (Native or Display)</th>
                    <td>
                        <textarea name="pptm_in_content_ad_code" rows="5" class="large-text code"><?php echo esc_textarea(get_option('pptm_in_content_ad_code', '')); ?></textarea>
                        <p><label>Insert after paragraph #: <input type="number" name="pptm_in_content_ad_position" value="<?php echo esc_attr(get_option('pptm_in_content_ad_position', '3')); ?>" min="1" max="20" style="width:60px;"></label></p>
                        <p class="description">Automatically inserted within article content.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Footer Ad</th>
                    <td>
                        <textarea name="pptm_footer_ad_code" rows="5" class="large-text code"><?php echo esc_textarea(get_option('pptm_footer_ad_code', '')); ?></textarea>
                        <p class="description">Appears at bottom of articles.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mobile Sticky Ad (320x50)</th>
                    <td>
                        <textarea name="pptm_mobile_sticky_ad_code" rows="5" class="large-text code"><?php echo esc_textarea(get_option('pptm_mobile_sticky_ad_code', '')); ?></textarea>
                        <p class="description">Sticky bottom banner on mobile devices. Mobile only.</p>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Shortcode for Manual Placement:</h4>
                <p><code>[pptm_ad id="my-custom-ad"]</code></p>
                <p class="description">Create custom ad zones in Settings > Monetization</p>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function render_sharing_tab() {
        $networks = get_option('pptm_share_buttons_networks', array('facebook', 'twitter', 'linkedin', 'whatsapp', 'email', 'copy'));
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_social_settings'); ?>
            <h2>Social Sharing Buttons</h2>
            <p class="description">Add share buttons to articles to increase viral reach.</p>

            <table class="form-table">
                <tr>
                    <th scope="row">Button Position</th>
                    <td>
                        <select name="pptm_share_buttons_position">
                            <option value="top" <?php selected(get_option('pptm_share_buttons_position', 'bottom'), 'top'); ?>>Top of content</option>
                            <option value="bottom" <?php selected(get_option('pptm_share_buttons_position', 'bottom'), 'bottom'); ?>>Bottom of content</option>
                            <option value="both" <?php selected(get_option('pptm_share_buttons_position', 'bottom'), 'both'); ?>>Both top and bottom</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Button Text</th>
                    <td>
                        <input type="text" name="pptm_share_buttons_text" value="<?php echo esc_attr(get_option('pptm_share_buttons_text', 'Share this:')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enabled Networks</th>
                    <td>
                        <fieldset>
                            <label><input type="checkbox" name="pptm_share_buttons_networks[]" value="facebook" <?php checked(in_array('facebook', $networks)); ?>> Facebook</label><br>
                            <label><input type="checkbox" name="pptm_share_buttons_networks[]" value="twitter" <?php checked(in_array('twitter', $networks)); ?>> Twitter</label><br>
                            <label><input type="checkbox" name="pptm_share_buttons_networks[]" value="linkedin" <?php checked(in_array('linkedin', $networks)); ?>> LinkedIn</label><br>
                            <label><input type="checkbox" name="pptm_share_buttons_networks[]" value="whatsapp" <?php checked(in_array('whatsapp', $networks)); ?>> WhatsApp</label><br>
                            <label><input type="checkbox" name="pptm_share_buttons_networks[]" value="email" <?php checked(in_array('email', $networks)); ?>> Email</label><br>
                            <label><input type="checkbox" name="pptm_share_buttons_networks[]" value="copy" <?php checked(in_array('copy', $networks)); ?>> Copy Link</label>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Features:</h4>
                <ul>
                    <li>✓ Click tracking (integrated with Analytics)</li>
                    <li>✓ Mobile-optimized</li>
                    <li>✓ No external dependencies</li>
                    <li>✓ Privacy-friendly</li>
                </ul>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function render_related_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_related_settings'); ?>
            <h2>Related Articles</h2>
            <p class="description">Keep visitors engaged with smart article recommendations.</p>

            <table class="form-table">
                <tr>
                    <th scope="row">Show Related Articles</th>
                    <td>
                        <label><input type="checkbox" name="pptm_show_related_articles" value="yes" <?php checked(get_option('pptm_show_related_articles', 'yes'), 'yes'); ?>> Enable related articles section</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Section Title</th>
                    <td>
                        <input type="text" name="pptm_related_articles_title" value="<?php echo esc_attr(get_option('pptm_related_articles_title', 'Related Articles')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Number of Articles</th>
                    <td>
                        <input type="number" name="pptm_related_articles_count" value="<?php echo esc_attr(get_option('pptm_related_articles_count', '3')); ?>" min="1" max="12" style="width:60px;">
                        <p class="description">How many related articles to show (1-12)</p>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Smart Algorithm:</h4>
                <ol>
                    <li>First: Articles linked to same talent</li>
                    <li>Then: Articles in same category</li>
                    <li>Then: Popular articles (30 days)</li>
                    <li>Finally: Recent articles</li>
                </ol>
                <p class="description">Shortcode: <code>[pptm_related count="3"]</code></p>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function render_analytics_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_analytics_settings'); ?>
            <h2>Google Analytics 4</h2>
            <p class="description">Track traffic, engagement, and conversions with Google Analytics 4.</p>

            <table class="form-table">
                <tr>
                    <th scope="row">GA4 Measurement ID</th>
                    <td>
                        <input type="text" name="pptm_ga4_measurement_id" value="<?php echo esc_attr(get_option('pptm_ga4_measurement_id', '')); ?>" class="regular-text" placeholder="G-XXXXXXXXXX">
                        <p class="description">Find this in Google Analytics > Admin > Data Streams</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Exclude Admins</th>
                    <td>
                        <label><input type="checkbox" name="pptm_analytics_exclude_admin" value="yes" <?php checked(get_option('pptm_analytics_exclude_admin', 'yes'), 'yes'); ?>> Don't track admin users</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Anonymize IP</th>
                    <td>
                        <label><input type="checkbox" name="pptm_analytics_anonymize_ip" value="yes" <?php checked(get_option('pptm_analytics_anonymize_ip', 'yes'), 'yes'); ?>> Anonymize visitor IP addresses (GDPR compliant)</label>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Auto-Tracked Events:</h4>
                <ul>
                    <li>✓ Page views</li>
                    <li>✓ Outbound link clicks</li>
                    <li>✓ File downloads</li>
                    <li>✓ Scroll depth (25%, 50%, 75%, 90%)</li>
                    <li>✓ Video plays</li>
                    <li>✓ Form submissions</li>
                    <li>✓ Time on page</li>
                    <li>✓ Social shares</li>
                </ul>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function render_email_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_email_settings'); ?>
            <h2>Email List Building</h2>
            <p class="description">Grow your email list with pop-ups and inline forms.</p>

            <h3>Pop-up Settings</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Pop-up</th>
                    <td>
                        <label><input type="checkbox" name="pptm_popup_enabled" value="yes" <?php checked(get_option('pptm_popup_enabled', 'no'), 'yes'); ?>> Show email capture pop-up</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Trigger Type</th>
                    <td>
                        <select name="pptm_popup_trigger" id="pptm_popup_trigger">
                            <option value="exit" <?php selected(get_option('pptm_popup_trigger', 'exit'), 'exit'); ?>>Exit Intent</option>
                            <option value="scroll" <?php selected(get_option('pptm_popup_trigger', 'exit'), 'scroll'); ?>>Scroll Percentage</option>
                            <option value="time" <?php selected(get_option('pptm_popup_trigger', 'exit'), 'time'); ?>>Time Delay</option>
                        </select>
                        <div id="pptm_trigger_value_container">
                            <input type="number" name="pptm_popup_trigger_value" value="<?php echo esc_attr(get_option('pptm_popup_trigger_value', '5')); ?>" min="1" style="width:60px;">
                            <span id="pptm_trigger_unit">seconds</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Show Frequency</th>
                    <td>
                        <input type="number" name="pptm_popup_frequency" value="<?php echo esc_attr(get_option('pptm_popup_frequency', '7')); ?>" min="1" max="90" style="width:60px;"> days
                        <p class="description">How often to show pop-up to same visitor</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pop-up Title</th>
                    <td>
                        <input type="text" name="pptm_popup_title" value="<?php echo esc_attr(get_option('pptm_popup_title', 'Stay Updated!')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pop-up Subtitle</th>
                    <td>
                        <textarea name="pptm_popup_subtitle" rows="2" class="large-text"><?php echo esc_textarea(get_option('pptm_popup_subtitle', 'Get the latest news and updates delivered to your inbox.')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Button Text</th>
                    <td>
                        <input type="text" name="pptm_popup_button_text" value="<?php echo esc_attr(get_option('pptm_popup_button_text', 'Subscribe')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Privacy Text</th>
                    <td>
                        <input type="text" name="pptm_popup_privacy_text" value="<?php echo esc_attr(get_option('pptm_popup_privacy_text', 'We respect your privacy. Unsubscribe anytime.')); ?>" class="large-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Custom Form Code</th>
                    <td>
                        <textarea name="pptm_popup_custom_form" rows="5" class="large-text code"><?php echo esc_textarea(get_option('pptm_popup_custom_form', '')); ?></textarea>
                        <p class="description">Optional: Paste Mailchimp, ConvertKit, or other form shortcode. Leave empty to use built-in form.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Hide for Admins</th>
                    <td>
                        <label><input type="checkbox" name="pptm_popup_hide_admin" value="yes" <?php checked(get_option('pptm_popup_hide_admin', 'yes'), 'yes'); ?>> Don't show pop-up to logged-in admins</label>
                    </td>
                </tr>
            </table>

            <h3>Welcome Email</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Send Welcome Email</th>
                    <td>
                        <label><input type="checkbox" name="pptm_send_welcome_email" value="yes" <?php checked(get_option('pptm_send_welcome_email', 'yes'), 'yes'); ?>> Send automatic welcome email</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Email Subject</th>
                    <td>
                        <input type="text" name="pptm_welcome_email_subject" value="<?php echo esc_attr(get_option('pptm_welcome_email_subject', 'Welcome to ' . get_bloginfo('name'))); ?>" class="large-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Email Message</th>
                    <td>
                        <textarea name="pptm_welcome_email_message" rows="5" class="large-text"><?php echo esc_textarea(get_option('pptm_welcome_email_message', 'Thank you for subscribing to our newsletter!')); ?></textarea>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Inline Form Shortcode:</h4>
                <p><code>[pptm_email_form title="Subscribe" button="Sign Up" style="default"]</code></p>
                <p><strong>Styles:</strong> default, boxed, minimal</p>
                <p><strong>Total Subscribers:</strong> <?php echo PPTM_Email_Capture::get_subscriber_count(); ?></p>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function render_performance_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('pptm_speed_settings'); ?>
            <h2>Speed & Performance</h2>
            <p class="description">Optimize your site for speed. Fast sites rank better and convert more visitors.</p>

            <table class="form-table">
                <tr>
                    <th scope="row">Lazy Load Images</th>
                    <td>
                        <label><input type="checkbox" name="pptm_lazy_load_images" value="yes" <?php checked(get_option('pptm_lazy_load_images', 'yes'), 'yes'); ?>> Enable lazy loading for images</label>
                        <p class="description">Recommended: Loads images only when visible</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Defer JavaScript</th>
                    <td>
                        <label><input type="checkbox" name="pptm_defer_javascript" value="yes" <?php checked(get_option('pptm_defer_javascript', 'no'), 'yes'); ?>> Defer non-critical JavaScript</label>
                        <p class="description">Improves initial page load speed</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Preload Resources</th>
                    <td>
                        <label><input type="checkbox" name="pptm_preload_resources" value="yes" <?php checked(get_option('pptm_preload_resources', 'no'), 'yes'); ?>> Preload critical resources</label>
                        <p class="description">Preloads logo, featured images, and fonts</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Browser Caching</th>
                    <td>
                        <label><input type="checkbox" name="pptm_browser_caching" value="yes" <?php checked(get_option('pptm_browser_caching', 'no'), 'yes'); ?>> Enable browser caching</label>
                        <p class="description">Modifies .htaccess file (Apache servers only)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Minify HTML</th>
                    <td>
                        <label><input type="checkbox" name="pptm_minify_html" value="yes" <?php checked(get_option('pptm_minify_html', 'no'), 'yes'); ?>> Minify HTML output</label>
                        <p class="description">Removes whitespace and comments</p>
                    </td>
                </tr>
            </table>

            <div class="pptm-info-box">
                <h4>Current Performance:</h4>
                <?php
                $scores = PPTM_Speed_Optimizer::get_page_speed_score();
                $total = count(array_filter($scores));
                ?>
                <p><strong><?php echo $total; ?>/5</strong> optimizations enabled</p>
                <ul>
                    <?php foreach ($scores as $name => $enabled): ?>
                        <li><?php echo $enabled ? '✓' : '✗'; ?> <?php echo ucwords(str_replace('_', ' ', $name)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }
}
