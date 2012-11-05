<?php if(!function_exists('register_field_group')) { ?>
<?php get_template_part( 'setup' ); ?>
<?php } else { ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php bloginfo('name'); ?></title>
        <meta name="description" content="<?php bloginfo('description'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
        <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300' rel='stylesheet' type='text/css'>
        <?php if(get_field('sf_favicon_image', 'option')) { ?>
        <link rel="shortcut icon" href="<?php the_field('sf_favicon_image', 'option'); ?>">
        <?php } ?>
        <?php wp_head(); ?>
    </head>
    
    <body class="<?php the_field('sf_background_color', 'option'); ?>">
        <?php if(get_field('sf_logo_image', 'option')) { ?>
        <header>
            <img src="<?php the_field('sf_logo_image', 'option'); ?>" alt="Logo" />
        </header>
        <?php } ?>
        
        <?php if(get_field('sf_enable_intro_text', 'option')) { ?>
        <section class="<?php the_field('sf_intro_text_color', 'option'); ?><?php if(get_field('sf_text_translucency', 'option')) { ?> translucent<?php } ?><?php if(get_field('sf_text_shadow', 'option')) { ?> shadow<?php } ?>">
            <h2><?php the_field('sf_intro_title_text', 'option'); ?></h2>
            <?php the_field('sf_intro_paragraph_text', 'option'); ?>
        </section>
        <?php } ?>
        
        <?php if(get_field('sf_enable_mailchimp', 'option')) { ?>
        <section>   
            <?php if( !$_POST['email'] ){ ?>
            <form method="post">
                <input id="email" class="<?php if(get_field('sf_form_field_shadow', 'option')) { ?>shadow<?php } ?>" name="email" type="email" placeholder="<?php the_field('sf_mailchimp_placeholder_text', 'option'); ?>" required validate>  
            </form>
            <?php } else { ?>
            <p class="success"><?php the_field('sf_mailchimp_success_message', 'option'); ?></p>
            <?php } 
            
                // The Email Address
                $email_address = $_POST['email'];
                
                // Get the MailChimp Class
                require_once("mailchimp/MCAPI.class.php");
                
                // MailChimp API Key & List ID
                $api_key = get_field('sf_mailchimp_api_key', 'option');
                $list_id = get_field('sf_mailchimp_list_id', 'option');
                
                // Get the MailChimp Class
                $mc_api = new MCAPI($api_key);
                
                // Do Work!
                if ($mc_api->listSubscribe($list_id, $email_address, '', '', false, true, false, false)){
                    $status = true;
                } else { 
                    $status = false; 
                }
            
            ?>
        </section>
        <?php } ?>
        
        <?php if(get_field('sf_twitter_id', 'option')) { ?>
        <section>   
            <a class="twitter" href="http://twitter.com/<?php the_field('sf_twitter_id', 'option'); ?>"></a>
        </section>
        <?php } ?>
        
        <?php if(get_field('sf_enable_text_panels', 'option')) { ?>
        <nav class="<?php the_field('sf_panel_nav_text_color', 'option'); ?><?php if(get_field('sf_text_translucency', 'option')) { ?> translucent<?php } ?><?php if(get_field('sf_text_shadow', 'option')) { ?> shadow<?php } ?>">
            <ul>
                <?php if(get_field('sf_text_panels', 'option')): while(has_sub_field('sf_text_panels', 'option')): ?>
                <li><a href="" class="slide"><?php the_sub_field('sf_panel_title', 'option'); ?></a></li>
                <?php endwhile; endif; ?>
                
                <?php if(get_field('sf_enable_profiles_panel', 'option')) { ?>
                <li><a href="" class="slide"><?php the_field('sf_profiles_panel_name', 'option'); ?></a></li>
                <?php } ?>
            </ul>
        </nav>
        <?php } ?>
        
        <?php if(get_field('sf_text_panels', 'option')): while(has_sub_field('sf_text_panels', 'option')): ?>
        <div class="panel">
            <?php the_sub_field('sf_panel_text', 'option'); ?>
        </div>
        <?php endwhile; endif; ?>
        
        <?php if(get_field('sf_enable_profiles_panel', 'option')) { ?>
        <div class="panel">
            <?php if(get_field('sf_profiles', 'option')): while(has_sub_field('sf_profiles', 'option')): ?>
            <div class="profile">
                <a href="<?php the_sub_field('sf_profile_link', 'option'); ?>">
                    <img src="<?php the_sub_field('sf_profile_image', 'option'); ?>" alt="<?php the_sub_field('sf_profile_name', 'option'); ?>" />
                </a>
                
                <span><?php the_sub_field('sf_profile_name', 'option'); ?></span>
            </div>
            <?php endwhile; endif; ?>
        </div>
        <?php } ?>
        
        <?php wp_footer(); ?>
        
        <?php if(get_field('sf_background_photo', 'option')) { ?>
        <script>
            jQuery(document).ready(function($) {
            	$.backstretch(["<?php the_field('sf_background_photo', 'option'); ?>"],{duration:3000,fade:750});
            });
        </script>
        <?php } ?>
        
        <?php the_field('sf_analytics_tracking_code', 'option'); ?>
    </body>
</html>
<?php } ?>