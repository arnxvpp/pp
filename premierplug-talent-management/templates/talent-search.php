<?php
if (!defined('ABSPATH')) {
    exit;
}

$categories = get_terms(array(
    'taxonomy' => 'talent_category',
    'hide_empty' => false,
));

$skills = get_terms(array(
    'taxonomy' => 'talent_skill',
    'hide_empty' => false,
));
?>

<div class="pptm-talent-search-form">
    <form id="pptm-search-form" class="pptm-search-form">
        <div class="pptm-search-row">
            <div class="pptm-search-field">
                <label for="pptm-search-keyword"><?php _e('Search:', 'premierplug-talent'); ?></label>
                <input type="text" id="pptm-search-keyword" name="search" placeholder="<?php _e('Enter name or keyword...', 'premierplug-talent'); ?>" />
            </div>

            <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                <div class="pptm-search-field">
                    <label for="pptm-search-category"><?php _e('Category:', 'premierplug-talent'); ?></label>
                    <select id="pptm-search-category" name="category">
                        <option value=""><?php _e('All Categories', 'premierplug-talent'); ?></option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->slug); ?>">
                                <?php echo esc_html($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (!empty($skills) && !is_wp_error($skills)): ?>
                <div class="pptm-search-field">
                    <label for="pptm-search-skill"><?php _e('Skill:', 'premierplug-talent'); ?></label>
                    <select id="pptm-search-skill" name="skill">
                        <option value=""><?php _e('All Skills', 'premierplug-talent'); ?></option>
                        <?php foreach ($skills as $skill): ?>
                            <option value="<?php echo esc_attr($skill->slug); ?>">
                                <?php echo esc_html($skill->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="pptm-search-field">
                <button type="submit" class="pptm-search-submit"><?php _e('Search', 'premierplug-talent'); ?></button>
            </div>
        </div>
    </form>

    <div id="pptm-search-results" class="pptm-search-results">
        <div class="pptm-talent-grid columns-3"></div>
    </div>

    <div id="pptm-search-loading" class="pptm-search-loading" style="display:none;">
        <?php _e('Searching...', 'premierplug-talent'); ?>
    </div>

    <div id="pptm-search-no-results" class="pptm-no-results" style="display:none;">
        <?php _e('No talents found matching your criteria.', 'premierplug-talent'); ?>
    </div>
</div>
