<?php
/**
 * Front Page Template (Homepage)
 *
 * @package PremierPlug
 * @since 1.0.0
 */

get_header();

$enable_animation = get_theme_mod('premierplug_enable_intro_animation', true);
$slogan = get_theme_mod('premierplug_slogan', 'Plugged It Premier.');
?>

<?php if ($enable_animation) : ?>
    <div class="animation-intro">
        <div class="site-header">
            <div class="site-header-container">
            </div>
        </div>
        <section class="hero-container full_vh bg-white">
            <div class="hero-text-container vertical-align">
                <div class="gutter-container">
                    <div class="scene-1 animated-scene">
                        <div class="-logo">
                            <span class="circle-fade circle-1"></span>
                            <span class="circle-fade circle-2"></span>
                            <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                                width="500.000000pt" height="500.000000pt" viewBox="0 0 500.000000 500.000000"
                                preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,500.000000) scale(0.100000,-0.100000)"
                                    fill="#000000" stroke="none">
                                    <path id="path2830" class="cls-1"
                                        d="M2230 3393 c-126 -20 -380 -87 -368 -98 1 -1 42 5 90 15 192 40 362 34 537 -16 162 -47 324 -152 405 -262 89 -121 149 -312 149 -472 0 -176 -44 -283 -162 -400 -90 -89 -174 -134 -294 -158 -327 -64 -666 233 -645 565 15 226 244 420 499 424 l64 1 -52 24 c-84 40 -183 56 -313 52 -104 -4 -122 -7 -183 -35 -75 -35 -166 -116 -202 -178 -40 -68 -66 -179 -71 -305 -3 -63 -1 -298 3 -521 l8 -406 104 138 c91 120 107 136 125 129 12 -4 71 -29 131 -55 198 -85 255 -102 390 -111 238 -17 444 65 630 251 83 82 109 116 147 191 73 140 93 222 93 384 0 153 -14 218 -70 337 -118 251 -339 424 -622 488 -87 20 -133 25 -238 24 -71 -1 -141 -4 -155 -6z"
                                        transform="translate(-70 -159.64)" />
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="scene-2 animated-scene">
                    <div class="slogan-container">
                        <div class="slogan-line"><?php echo esc_html($slogan); ?></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php else : ?>
    <main class="site-content">
        <div class="gutter-container">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>
    </main>
<?php endif; ?>

<?php get_footer(); ?>
