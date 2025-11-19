<div class="nav-overlay">
    <div class="menu-container">
        <?php
        if (has_nav_menu('primary')) {
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => 'nav',
                'container_class'=> 'global-nav',
                'menu_class'     => '',
                'walker'         => new PremierPlug_Walker_Nav_Menu(),
            ));
        } else {
            ?>
            <nav class="global-nav">
                <ul>
                    <li><a href="javascript:void(0);" title="Research">Research</a>
                        <ul>
                            <li><a href="<?php echo home_url('/social-research'); ?>" class="linkTo">Social Research</a></li>
                            <li><a href="<?php echo home_url('/market-research'); ?>" class="linkTo">Market Research</a></li>
                            <li><a href="<?php echo home_url('/data-analysis'); ?>" class="linkTo">Data Analysis</a></li>
                        </ul>
                    </li>
                    <li><a href="javascript:void(0);" title="For Talents">For Talents</a>
                        <ul>
                            <li><a href="<?php echo home_url('/motion-pictures'); ?>" class="linkTo">Motion Pictures</a></li>
                            <li><a href="<?php echo home_url('/digital-media'); ?>" class="linkTo">Digital Media</a></li>
                            <li><a href="<?php echo home_url('/speakers'); ?>" class="linkTo">Speakers</a></li>
                            <li><a href="<?php echo home_url('/television'); ?>" class="linkTo">Television</a></li>
                            <li><a href="<?php echo home_url('/voiceovers'); ?>" class="linkTo">Voiceovers</a></li>
                            <li><a href="<?php echo home_url('/publishing'); ?>" class="linkTo">Publishing</a></li>
                            <li><a href="<?php echo home_url('/music-brand-partnerships'); ?>" class="linkTo long-link">Merchandising</a></li>
                        </ul>
                    </li>
                    <li><a href="javascript:void(0);" title="Brand Solutions">Brand Solutions</a>
                        <ul>
                            <li><a href="<?php echo home_url('/brand-consulting'); ?>" class="linkTo">Brand Consulting</a></li>
                            <li><a href="<?php echo home_url('/brand-management'); ?>" class="linkTo">Brand Management</a></li>
                            <li><a href="<?php echo home_url('/brand-studio'); ?>" class="linkTo">Brand Studio</a></li>
                            <li><a href="<?php echo home_url('/motion-pictures'); ?>" class="linkTo">Production Studio</a></li>
                            <li><a href="<?php echo home_url('/marketing-it'); ?>" class="linkTo">Marketing & IT</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <?php
        }
        ?>

        <div class="overlay-footer">
            <div class="homepage-ft">
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container'      => 'nav',
                        'menu_class'     => '',
                    ));
                } else {
                    ?>
                    <nav>
                        <ul>
                            <li><a href="<?php echo home_url('/about-us'); ?>">About</a></li>
                            <li><a href="<?php echo home_url('/careers'); ?>">Careers</a></li>
                            <li><a href="<?php echo home_url('/contact'); ?>">Contact</a></li>
                        </ul>
                    </nav>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
