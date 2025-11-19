<div class="nav-overlay">
    <div class="menu-container">
        <nav class="global-nav">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => '',
                    'walker'         => new PremierPlug_Walker_Nav_Menu(),
                ));
            } else {
                ?>
                <ul>
                    <li><a href="javascript:void(0);" title="Research" target="_self" class="">Research</a>
                        <ul>
                            <li><a href="<?php echo home_url('/social-research'); ?>" title="Social Research" target="_self" class="linkTo">Social Research</a></li>
                            <li><a href="<?php echo home_url('/market-research'); ?>" title="Market Research" target="_self" class="linkTo">Market Research</a></li>
                            <li><a href="<?php echo home_url('/data-analysis'); ?>" title="Data Analysis" target="_self" class="linkTo">Data Analysis</a></li>
                        </ul>
                    </li>

                    <li><a href="javascript:void(0);" title="For Talents" target="_self" class="">For Talents</a>
                        <ul>
                            <li><a href="<?php echo home_url('/motion-pictures'); ?>" title="Motion Pictures" target="_self" class="linkTo">Motion Pictures</a></li>
                            <li><a href="<?php echo home_url('/digital-media'); ?>" title="Digital Media" target="_self" class="linkTo">Digital Media</a></li>
                            <li><a href="<?php echo home_url('/speakers'); ?>" title="Speakers" target="_self" class="linkTo">Speakers</a></li>
                            <li><a href="<?php echo home_url('/television'); ?>" title="Television" target="_self" class="linkTo">Television</a></li>
                            <li><a href="<?php echo home_url('/voiceovers'); ?>" title="Voiceovers" target="_self" class="linkTo long-link">Voiceovers</a></li>
                        </ul>
                    </li>

                    <li><a href="javascript:void(0);" title="For Enterprise" target="_self" class="">For Enterprise</a>
                        <ul>
                            <li><a href="javascript:void(0);" title="Partnership Sales" target="_self" class="">Partnership Sales</a>
                                <ul>
                                    <li><a href="<?php echo home_url('/music-brand-partnerships'); ?>" title="Music Brand Partnerships" target="_self" class="linkTo long-link">Music Brand Partnerships</a></li>
                                    <li><a href="<?php echo home_url('/publishing'); ?>" title="Publishing" target="_self" class="linkTo">Publishing</a></li>
                                    <li><a href="#" title="Licensing" target="_self" class="linkTo">Licensing</a></li>
                                    <li><a href="<?php echo home_url('/touring'); ?>" title="Music and Comedy Touring" target="_self" class="linkTo long-link">Music & Comedy Touring</a></li>
                                    <li><a href="<?php echo home_url('/merchandising'); ?>" title="Entertainment Endorsements & Voiceovers" target="_self" class="linkTo long-link">Merchandising</a></li>
                                </ul>
                            </li>
                            <li><a href="javascript:void(0);" title="Brand Solutions" target="_self" class="">Brand Solutions</a>
                                <ul>
                                    <li><a href="<?php echo home_url('/brand-consulting'); ?>" title="Brand Consulting" target="_self" class="linkTo">Brand Consulting</a></li>
                                    <li><a href="<?php echo home_url('/brand-management'); ?>" title="Brand Management" target="_self" class="linkTo">Brand Management</a></li>
                                    <li><a href="<?php echo home_url('/brand-studio'); ?>" title="Brand Studio" target="_self" class="linkTo">Brand Studio</a></li>
                                    <li><a href="<?php echo home_url('/motion-pictures'); ?>" title="Motion Pictures" target="_self" class="linkTo">Production Studio</a></li>
                                    <li><a href="<?php echo home_url('/marketing-it'); ?>" title="Marketing & IT" target="_self" class="linkTo">Marketing & IT</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
                <?php
            }
            ?>
        </nav>

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
                            <li><a href="<?php echo home_url('/about-us'); ?>" title="about-us.html">About</a></li>
                            <li><a href="<?php echo home_url('/careers'); ?>" title="Careers">Careers</a></li>
                            <li><a href="<?php echo home_url('/contact'); ?>" title="Contact">Contact</a></li>
                        </ul>
                    </nav>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <div class="overlay-footer">
    </div>
</div>
