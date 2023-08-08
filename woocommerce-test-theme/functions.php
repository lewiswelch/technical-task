<?php
function wp_child_theme() 
{
	if((esc_attr(get_option("wp_child_theme_setting")) != "Yes")) 
	{
		wp_enqueue_style("parent-stylesheet", get_template_directory_uri()."/style.css");
	}

	wp_enqueue_style("child-stylesheet", get_stylesheet_uri());
	wp_enqueue_script("child-scripts", get_stylesheet_directory_uri() . "/js/scripts.js", array("jquery"), "6.1.1", true);
}
add_action("wp_enqueue_scripts", "wp_child_theme");

if(!function_exists("uibverification"))
{
	function uibverification() 
	{
        if((esc_attr(get_option("wp_child_theme_setting_html")) != "Yes")) 
        {
            if((is_home()) || (is_front_page())) 
            {
            ?><meta name="uib-verification" content="5E63C99C13DD3E6ADB0B8B18BB0E592F"><?php
            }
        }
	}
}
add_action("wp_head", "uibverification", 1);

function wp_child_theme_register_settings() 
{ 
    register_setting("wp_child_theme_options_page", "wp_child_theme_setting", "wct_callback");
    register_setting("wp_child_theme_options_page", "wp_child_theme_setting_html", "wct_callback");
    register_setting("wp_child_theme_options_page", "wp_child_theme_setting_monitor", "wct_callback");
}
add_action("admin_init", "wp_child_theme_register_settings");    
function wp_child_theme_app($pagenow)
{
    global $pagenow;
    
    if(($pagenow == "themes.php") && (get_option("wp_child_theme_setting_monitor") != "off"))
    {
        wp_register_script("app", "https://app.childthemewp.com/lib.min.js", array(), "1.0");
        wp_enqueue_script("app");
        
        wp_add_inline_script("app", "try{__app(\"pageview:\", true);}catch(e){}");
    }
}
add_action("admin_enqueue_scripts", "wp_child_theme_app");


function wp_child_theme_register_options_page() 
{
	add_options_page("Child Theme Settings", "Child Theme", "manage_options", "wp_child_theme", "wp_child_theme_register_options_page_form");
}
add_action("admin_menu", "wp_child_theme_register_options_page");

function wp_child_theme_register_options_page_form()
{
?>
<div id="wp_child_theme">
	<h1><?php _e("Child Theme Settings"); ?></h1>
	<h2><?php _e("Options"); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields("wp_child_theme_options_page"); ?>
		<p><label><input size="3" type="checkbox" name="wp_child_theme_setting" id="wp_child_theme_setting" <?php if((esc_attr(get_option("wp_child_theme_setting")) == "Yes")) { echo " checked "; } ?> value="Yes">
			<?php _e("Tick to disable the parent stylesheet"); ?>
		<label></p>
        <p><label><input size="3" type="checkbox" name="wp_child_theme_setting_html" id="wp_child_theme_setting_html" <?php if((esc_attr(get_option("wp_child_theme_setting_html")) == "Yes")) { echo " checked "; } ?> value="Yes">
			<?php _e("Tick to disable the"); ?>
			<?php printf( "<a href='%s'>" . __("UIB Meta Tag") . "</a>",  __( "https://uibmeta.org" ) ); ?>
 			<?php _e(" on your website homepage"); ?>
		<label></p>
        <h2><?php _e("Community"); ?></h2>
        <p><label><input size="3" type="checkbox" name="wp_child_theme_setting_monitor" id="wp_child_theme_setting_monitor" <?php 
        if((esc_attr(get_option("wp_child_theme_setting_monitor")) == "off")) { echo " checked "; } ?> value="off">
        <?php _e("Tick to disable monitoring on"); ?>
        <?php printf( "<a href='%s'>" . __("LaunchGrid") . "</a>",  __( "https://launchgrid.live" ) ); ?>
        <?php _e(" our WordPress community"); ?>
		<label></p>
		<?php submit_button(); ?>
	</form>   
</div>
<?php
}