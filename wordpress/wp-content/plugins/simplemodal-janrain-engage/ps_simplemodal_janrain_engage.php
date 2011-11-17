<?php
/*
Plugin Name: SimpleModal Janrain Engage
Plugin URI: http://soderlind.no/archives/2010/12/03/simplemodal-janrain-engage/
Description: Adds Janrain Engage (rpx) to SimpleModal Login. The Janrain Engage and SimpleModal Login plugins must be installed and working.
Version: 1.2.9
Author: PerS
Author URI: http://soderlind.no
*/
/*

Changelog:
v1.2.9 Bugfix, fixed bad path to language file. Many thanks to vinoowijn1 for pointing out this bug.
v1.2.8 Bugfix (removed the spinner/loading icon)
v1.2.7 Fixed bug that prevented using LinkedIn and Twitter as a identity provider. My bad, many thanks to mattp and Robert for pointing out this bug.
v1.2.5 Added "set modal width" in the settings page + minor bug fixes
v1.2.0 I should have read the Janrain Engage doc a litle better, discovered a paramenter for the inline widget and "had" to rewrite the plugin. Now you can change the heading above the Janrain Engage widget using the ps_simplemodal_janrain_engage.pot file
v1.1.1 Minor style adjustment
v1.1: Added language support for the Janrain Engange embedded widget and updated the ps_simplemodal_janrain_engage.pot file
v1.0: Initial release

*/
/*
Credits: 
	This template is based on the template at http://pressography.com/plugins/wordpress-plugin-template/ 
	My changes are documented at http://soderlind.no/archives/2010/03/04/wordpress-plugin-template/
*/

if (!class_exists('ps_simplemodal_janrain_engage')) {
    class ps_simplemodal_janrain_engage {
		/**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'ps_simplemodal_janrain_engage_options';
 
        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();
	
		/**
		* @var string $localizationDomain Domain used for localization
		*/
		var $localizationDomain = "ps_simplemodal_janrain_engage";
		var $wordpress_janrain_locales = array(	'bg_BG' => 'bg',
												'cs_CZ' => 'cs',
												'da_DK' => 'da',
												'de_DE' => 'de',
												'el'    => 'el',
												'en'    => 'en',
												'es_ES' => 'es',
												'fi'    => 'fi',
												'fr_FR' => 'fr',
												'he_IL' => 'he',
												'hr'    => 'hr',
												'hu_HU' => 'hu',
												'id_ID' => 'id',
												'it_IT' => 'it',
												'ja'    => 'ja',
												'lt_LT' => 'lt',
												'nb_NO' => 'nb-NO',
												'nl_NL' => 'nl',
												'pl_PL' => 'pl',
												'pt_PT' => 'pt',
												'pt_BR' => 'pt-BR',
												'ro_RO' => 'ro',
												'ru_RU' => 'ru',
												'sk_SK' => 'sk',
												'sl_SI' => 'sl',
												'sv_SE' => 'sv-SE',
												'th'    => 'th',
												'zh_CN' => 'zh');
												
		
		/**
		* @var string $url The url to this plugin
		*/ 
		var $url = '';
		/**
		* @var string $urlpath The path to this plugin
		*/
		var $urlpath = '';

		//Class Functions
		/**
		* PHP 4 Compatible Constructor
		*/
		function ps_simplemodal_janrain_engage(){$this->__construct();}

		/**
		* PHP 5 Constructor
		*/        
		function __construct(){
		    //Language Setup
		    $locale = get_locale();
			$mo = sprintf("%s/languages/%s-%s.mo",dirname(__FILE__),$this->localizationDomain,$locale);	
		    load_textdomain($this->localizationDomain, $mo);
		    //"Constants" setup
			$this->url = plugins_url(basename(__FILE__), __FILE__);
			$this->urlpath = plugins_url('', __FILE__);
			//Initialize the options
			$this->getOptions();
			//Admin menu
			add_action("admin_menu", array(&$this,"admin_menu_link"));
			//Actions
			add_action("init", array(&$this,"ps_simplemodal_janrain_engage_init"));
			add_action('wp_print_scripts', array(&$this,'ps_simplemodal_janrain_engage_script'));
			add_action('wp_print_styles', array(&$this,'ps_simplemodal_janrain_engage_style'));
			//Filters
			add_filter('simplemodal_login_form', array(&$this,'ps_simplemodal_janrain_engage_login_form'));
			add_filter('simplemodal_registration_form', array(&$this,'ps_simplemodal_janrain_engage_registration_form'));
			add_filter('simplemodal_reset_form', array(&$this,'ps_simplemodal_janrain_engage_reset_form'));		    
		}
            
 		
		function ps_simplemodal_janrain_engage_init() {
			// remove Janrain Engage default login and register form buttons
			remove_action('login_head',   'rpx_login_head');
		    remove_action('login_form',    'rpx_login_form');
		    remove_action('wp_head', 'rpx_login_head');
			remove_action('wp_footer', 'rpx_wp_footer');
		    remove_action('register_form', 'rpx_login_form');
	    }


        function ps_simplemodal_janrain_engage_script() {      
			if (is_admin()) { // only run when not in wp-admin, other conditional tags at http://codex.wordpress.org/Conditional_Tags
				wp_enqueue_script('jquery'); // other scripts included with Wordpress: http://tinyurl.com/y875age
                wp_enqueue_script('jquery-validate', 'http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js', array('jquery'));
				wp_enqueue_script('ps_simplemodal_janrain_engage_script', $this->url.'?ps_simplemodal_janrain_engage_javascript'); // embed javascript, see end of this file
                wp_localize_script( 'ps_simplemodal_janrain_engage_script', 'ps_simplemodal_janrain_engage_lang', array(
                    'required' => __('Please enter a number.', $this->localizationDomain),
                    'number'   => __('Please enter a number.', $this->localizationDomain)
                ));
            }
        }

		function ps_simplemodal_janrain_engage_style() {
			if( !is_admin() ) {	
				wp_enqueue_style('ps_simplemodal_janrain_engage_style',  $this->url . "?ps_simplemodal_janrain_engage_style&ps_simplemodal_janrain_engage_modal_width=" . $this->options['ps_simplemodal_janrain_engage_option_width']);				
				wp_enqueue_style('jquery-loadmask-css',  $this->urlpath. '/lib/jquery.loadmask.css');				
			}
		}


		function ps_simplemodal_janrain_engange_locale() {
			$locale = get_locale();
			if (array_key_exists( $locale , $this->wordpress_janrain_locales )) {
				return  $this->wordpress_janrain_locales[$locale]; 
			} else {
				return $this->options['ps_simplemodal_janrain_engage_option_language'];
			}
		}


		function ps_simplemodal_janrain_engage_login_form($form) {
			$users_can_register = get_option('users_can_register') ? true : false;
			$options = get_option('simplemodal_login_options');
			$rpx_api_key = get_option(RPX_API_KEY_OPTION);
		  	if ($rpx_api_key == ''){ $rpx_api_key = strip_tags($_POST[RPX_API_KEY_OPTION]); }
		  	if ($rpx_api_key != ''){
		    	$rpx_rp = rpx_get_rp($rpx_api_key);
			}

			$output = sprintf('
		<form name="loginform" id="loginform" action="%s" method="post">
		<div id="modalrpx-loginform" style="float:left;padding:0;margin-right:0 auto;">
			<div class="title">%s</div>
			<div class="iframe-container">
				<iframe id="janrain_login_iframe" src="%s://%s/openid/embed?token_url=%s&language_preference=%s&flags=hide_sign_in_with" scrolling="no" frameBorder="no" allowtransparency="true" style="width:350px;height:260px;margin:0;padding:0;"></iframe>
			</div>
		</div>
		<div style="float:right;width=350px;">
			<div class="title">%s </div>
			<div class="simplemodal-login-fields">
			<p>
				<label>%s<br />
				<input type="text" name="log" class="user_login input" value="" size="20" tabindex="10" /></label>
			</p>
			<p>
				<label>%s<br />
				<input type="password" name="pwd" class="user_pass input" value="" size="20" tabindex="20" /></label>
			</p>',
				site_url('wp-login.php', 'login_post'),
				__('Use a third party account',$this->localizationDomain),
				$rpx_rp['realmScheme'],
				$rpx_rp['realm'],
				RPX_TOKEN_URL,
				$this->ps_simplemodal_janrain_engange_locale(),
				__('Or, login with your local account', $this->localizationDomain),
				__('Username', $this->localizationDomain),
				__('Password', $this->localizationDomain)
			);

			ob_start();
			do_action('login_form');
			$output .= ob_get_clean();
			$output .= sprintf('
			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" class="rememberme" value="forever" tabindex="90" />%s</label></p>
			<p class="submit">
				<input type="submit" name="wp-submit" value="%s" tabindex="100" />
				<input type="button" class="simplemodal-close" value="%s" tabindex="101" />
				<input type="hidden" name="testcookie" value="1" />
			</p>
			<p class="nav">',
				__('Remember Me', $this->localizationDomain),
				__('Log In', $this->localizationDomain),
				__('Cancel', $this->localizationDomain)
			);

			if ($users_can_register && $options['registration']) {
				$output .= sprintf('<a class="simplemodal-register" href="%s">%s</a>', 
					site_url('wp-login.php?action=register', 'login'), 
					__('Register', $this->localizationDomain)
				);
			}

			if (($users_can_register && $options['registration']) && $options['reset']) {
				$output .= ' | ';
			}

			if ($options['reset']) {
				$output .= sprintf('<a class="simplemodal-forgotpw" href="%s" title="%s">%s</a>',
					site_url('wp-login.php?action=lostpassword', 'login'),
					__('Password Lost and Found', $this->localizationDomain),
					__('Lost your password?', $this->localizationDomain)
				);
			}

			$output .= ' 
			</p>
			</div>
			<div class="simplemodal-login-activity" style="display:none;"></div>
			</div>
		</form>';

			return $output;
		}

		/*
		 * GREEN PAGES MODIFIED FUNCTION!!!
		 * 
		 * Janrain user registration doesn't seem to be multisite enabled so I've had to fix this 
		 * function.
		 * 
		 * Note 1: I wasn't able to find a solution but I think there should be a way to order plugin 
		 * execution so that we can override this function instead. That way this won't break on 
		 * plugin updates.
		 * 
		 * Note 2: See /wp-signup.php for Wordpress registration code.
		 * 
		 */
		function ps_simplemodal_janrain_engage_registration_form() {
			$users_can_register = get_option('users_can_register') ? true : false;
			$options = get_option('simplemodal_login_options');
			$rpx_api_key = get_option(RPX_API_KEY_OPTION);
		  	if ($rpx_api_key == ''){ $rpx_api_key = strip_tags($_POST[RPX_API_KEY_OPTION]); }
		  	if ($rpx_api_key != ''){
		    	$rpx_rp = rpx_get_rp($rpx_api_key);
			}
			$output .= sprintf('
		<form name="registerform" id="registerform" action="/register" method="post">
		<div id="modalrpx-registerform" style="float:left;padding:0;margin-right:0 auto;">
			<div class="title">%s</div>
			<div class="iframe-container" style="margin:0;padding:0;">
				<iframe id="janrain_register_iframe" src="%s://%s/openid/embed?token_url=%s&language_preference=%s&flags=hide_sign_in_with" scrolling="no" frameBorder="no" allowtransparency="true" style="width:350px;height:260px;margin:0;padding:0;"></iframe>
			</div>
		</div>
		<div style="float:right;width=350px;">
		
			<div class="title">%s</div>
			<div class="simplemodal-login-fields">
			<p>
				<label>%s<br />
				<input type="text" name="user_name" class="user_login input" value="" size="20" tabindex="10" /></label>
			</p>
			<p>
				<label>%s<br />
				<input type="text" name="user_email" class="user_email input" value="" size="25" tabindex="20" /></label>
			</p>',
				__('The easy way',$this->localizationDomain),
				$rpx_rp['realmScheme'],
				$rpx_rp['realm'],
				RPX_TOKEN_URL,
				$this->ps_simplemodal_janrain_engange_locale(),
				__('Or, register for a local account', $this->localizationDomain),
				__('Username', $this->localizationDomain),
				__('E-mail', $this->localizationDomain)
			);

			ob_start();
			do_action('register_form');
			do_action('signup_hidden_fields');
			$output .= ob_get_clean();			
			
			$output .= sprintf('
			<p class="reg_passmail">%s</p>
			<p class="submit">
				<input type="hidden" name="stage" value="validate-user-signup" />
				<input type="submit" name="submit" value="%s" tabindex="100" />
				<input type="button" class="simplemodal-close" value="%s" tabindex="101" />
			</p>
			<p class="nav">
				<a class="simplemodal-login" href="%s">%s</a>',
						__('A password will be e-mailed to you.', $this->localizationDomain),
						__('Register', $this->localizationDomain),
						__('Cancel', $this->localizationDomain),
						site_url('wp-login.php', 'login'),
						__('Log in', $this->localizationDomain)
					);

					if ($options['reset']) {
						$output .= sprintf(' | <a class="simplemodal-forgotpw" href="%s" title="%s">%s</a>',
							site_url('wp-login.php?action=lostpassword', 'login'),
							__('Password Lost and Found', $this->localizationDomain),
							__('Lost your password?', $this->localizationDomain)
						);
					}

					$output .= '
			</p>
			</div>
			<div class="simplemodal-login-activity" style="display:none;"></div>
			
		</div>
		</form>';

			return $output;
		}


		function ps_simplemodal_janrain_engage_reset_form() {
			$users_can_register = get_option('users_can_register') ? true : false;
			$options = get_option('simplemodal_login_options');
			$output .= sprintf('

		
		<form name="lostpasswordform" id="lostpasswordform" action="%s" method="post">
		
			<div class="title">%s</div>
			<div class="simplemodal-login-fields">
			<p>
				<label>%s<br />
				<input type="text" name="user_login" class="user_login input" value="" size="20" tabindex="10" /></label>
			</p>',

			site_url('wp-login.php?action=lostpassword', 'login_post'),
			__('Reset Password', $this->localizationDomain),
			__('Username or E-mail:', $this->localizationDomain)
			);
			
			ob_start();
			do_action('lostpassword_form');
			$output .= ob_get_clean();
			
			$output .= sprintf('
			<p class="submit">
				<input type="submit" name="wp-submit" value="%s" tabindex="100" />
				<input type="button" class="simplemodal-close" value="%s" tabindex="101" />
			</p>
			<p class="nav">
				<a class="simplemodal-login" href="%s">%s</a>',
					__('Get New Password', $this->localizationDomain),
					__('Cancel', $this->localizationDomain),
					site_url('wp-login.php', 'login'),
					__('Log in', $this->localizationDomain)
				);

				if ($users_can_register && $options['registration']) {
					$output .= sprintf('| <a class="simplemodal-register" href="%s">%s</a>', site_url('wp-login.php?action=register', 'login'), __('Register', $this->localizationDomain));
				}

				$output .= '
			</p>
			</div>
			<div class="simplemodal-login-activity" style="display:none;"></div>
		</form>';

			return $output;
		}
		
		
        /**
        * @desc Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {
			$theOptions = get_option($this->optionsName);
            if (!$theOptions || count($theOptions) < 2) {
				if (!$theOptions) {$theOptions = array();}
				$theOptions = shortcode_atts(array( // merges default values with existing if they exists, about shortcode_atts: http://codex.wordpress.org/Function_Reference/shortcode_atts
					'ps_simplemodal_janrain_engage_option_language'=> 'en',
					'ps_simplemodal_janrain_engage_option_width'=> 760,
				), $theOptions);
	
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;
        }
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }

        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            add_options_page('SimpleModal Janrain Engage', 'SimpleModal Janrain Engage', 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }

        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }

        /**
        * Adds settings/options page
        */
        function admin_options_page() { 
            if($_POST['ps_simplemodal_janrain_engage_save']){
                if (! wp_verify_nonce($_POST['_wpnonce'], 'ps_simplemodal_janrain_engage-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.');                    
				$this->options['ps_simplemodal_janrain_engage_option_language'] = $_POST['ps_simplemodal_janrain_engage_option_language'];                   
				$this->options['ps_simplemodal_janrain_engage_option_width'] = (int)$_POST['ps_simplemodal_janrain_engage_option_width'];
                $this->saveAdminOptions();

                echo '<div class="updated"><p>' . __('Success! Your changes were sucessfully saved!',$this->localizationDomain) . '</p></div>';
            }

			$janrain_locales = array(    'bg' => __('Bulgarian',$this->localizationDomain), 
										 'cs' => __('Czech', $this->localizationDomain),
										 'da' => __('Danish', $this->localizationDomain),
										 'de' => __('German', $this->localizationDomain),
										 'el' => __('Greek', $this->localizationDomain),
										 'en' => __('English', $this->localizationDomain),
										 'es' => __('Spanish', $this->localizationDomain),
										 'fi' => __('Finnish', $this->localizationDomain),
										 'fr' => __('French', $this->localizationDomain),
										 'he' => __('Hebrew', $this->localizationDomain),
										 'hr' => __('Croatian', $this->localizationDomain),
										 'hu' => __('Hungarian', $this->localizationDomain),
										 'id' => __('Indonesian', $this->localizationDomain),
										 'it' => __('Italian', $this->localizationDomain),
										 'ja' => __('Japanese', $this->localizationDomain),
										 'lt' => __('Lithuanian', $this->localizationDomain),
										 'nb-NO' => __('Norwegian', $this->localizationDomain),
										 'nl' => __('Dutch',$this->localizationDomain),
										 'pl' => __('Polish', $this->localizationDomain),
										 'pt' => __('Portuguese', $this->localizationDomain),
										 'pt-BR' => __('Brazilian Portuguese',  $this->localizationDomain),
										 'ro' => __('Romanian', $this->localizationDomain),
										 'ru' => __('Russian', $this->localizationDomain),
										 'sk' => __('Slovak', $this->localizationDomain),
										 'sl' => __('Slovenian', $this->localizationDomain),
										 'sv-SE' => __('Swedish',  $this->localizationDomain),
										 'th' => __('Thai', $this->localizationDomain),
										 'zh' => __('Chinese', $this->localizationDomain));
				asort($janrain_locales);
?>                                   
                <div class="wrap">
                <h2>SimpleModal Janrain Engage</h2>
                <p>
                <?php _e('The Janrain Engage embedded widget support several languages (see the fallback languages below). The SimpleModal Janrain Engange plugin will try to automatically set the language for the embedded Janrain Engage widget based on your <a href="http://codex.wordpress.org/WordPress_in_Your_Language">locale</a>. If the plugin doesn\'t find a match, it will use the selected fallback language below.', $this->localizationDomain); ?>
                </p>
                <form method="post" id="ps_simplemodal_janrain_engage_options">
                <?php wp_nonce_field('ps_simplemodal_janrain_engage-update-options'); ?>
                    <table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 

                        <tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Fallback language:', $this->localizationDomain); ?></th> 
                            <td>
                                <select name="ps_simplemodal_janrain_engage_option_language" type="text" id="ps_simplemodal_janrain_engage_option2">
								<?php
								foreach ($janrain_locales as $locale => $language) {
									$selected = ($locale == $this->options['ps_simplemodal_janrain_engage_option_language']) ? ' selected="selected"' : '';
									printf ('<option value="%s" %s>%s</option>',$locale,$selected,$language);
								}
								?>
								</select>
                            </td> 
                        </tr>
						<tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Modal width:', $this->localizationDomain); ?></th> 
                            <td>
                                <input name="ps_simplemodal_janrain_engage_option_width" type="text" id="ps_simplemodal_janrain_engage_option_width" size="45" value="<?php echo $this->options['ps_simplemodal_janrain_engage_option_width'] ;?>"/>
                                <br /><span class="setting-description"><?php _e('The width of the login modal in pixels (px), default is 760', $this->localizationDomain); ?>
                            </td> 
                        </tr>
                    </table>
                    <p class="submit"> 
                        <input type="submit" name="ps_simplemodal_janrain_engage_save" class="button-primary" value="<?php _e('Save Changes', $this->localizationDomain); ?>" />
                    </p>
                </form>
               	<h2><?php _e('Like To Contribute?', $this->localizationDomain);?></h2>
				<p>
				<?php _e('If you would like to contribute, the following is a list of ways you can help:', $this->localizationDomain);?>
				</p>
				<ul>
				<li>&raquo; <?php _e('Translate SimpleModal Janrain Engage into your language, the ps_simplemodal_janrain_engage.pot file is in the wp-content/plugins/simplemodal-janrain-engage/languages folder', $this->localizationDomain);?></li>
				<li>&raquo; <?php _e('Blog about or link to SimpleModal Janrain Engage so others can find out about it', $this->localizationDomain);?></li>
				<li>&raquo; <a href="http://soderlind.no/contact-me/"><?php _e('Report issues, provide feedback, request features, etc.', $this->localizationDomain);?></a></li>
				<li>&raquo; <a href="http://wordpress.org/extend/plugins/simplemodal-janrain-engage/"><?php _e('Rate SimpleModal Janrain Engage on the WordPress Plugins Page', $this->localizationDomain);?></a></li>
				<li>&raquo; <a href="http://soderlind.no/donate/"><?php _e('Make a donation', $this->localizationDomain);?></a></li>
				</ul>
                <?php
        }
		
		
		function ps_simplemodal_janrain_engage_dependency_check() {	
			$missing_plugin = "";
			$required_plugins_assoc =  array('rpx/rpx.php' => 'Janrain Engage','simplemodal-login/simplemodal-login.php' => 'SimpleModal Login');
			if((get_option(RPX_API_KEY_OPTION) != "") && (get_option('simplemodal_login_options') != "")) {
				$required_plugins = array('rpx/rpx.php','simplemodal-login/simplemodal-login.php');
				$active_plugins = get_option('active_plugins');
				foreach ($required_plugins as $required_plugin) {
				    if ( !in_array( $required_plugin , $active_plugins )) {
						$missing_plugin .= $required_plugins_assoc[$required_plugin] . " "; 
					}
				}
				if ($missing_plugin == "")
					return; // everything is ok
					
			}

		    $message = sprintf('<p>This plugin requires %s, which you do not have. Add and activate the missing plugin</p>', $missing_plugin); 

		    if( function_exists('deactivate_plugins') ) {
		        deactivate_plugins(__FILE__); 
			}
			exit($message);
		}
		
				
	} //End Class
} //End if class exists statement


if (isset($_GET['ps_simplemodal_janrain_engage_javascript'])) {
    //embed javascript
    Header("content-type: application/x-javascript");
    echo<<<ENDJS
/**
* @desc SimpleModal Janrain Engage
* @author PerS - http://soderlind.no
*/

 
jQuery(document).ready(function(){
	
    //validate plugin option form
	if (typeof(ps_simplemodal_janrain_engage_lang) !== 'undefined') {
	    jQuery("#ps_simplemodal_janrain_engage_options").validate({
	        rules: {
	            ps_simplemodal_janrain_engage_option_width: {
	                required: true,
	                number: true,
	            }
	        },
	        messages: {
	            ps_simplemodal_janrain_engage_option_width: {
	                // the ps_simplemodal_janrain_engage_lang object is defined using wp_localize_script() in function ps_simplemodal_janrain_engage_script() 
	                required: ps_simplemodal_janrain_engage_lang.required,
	                number: ps_simplemodal_janrain_engage_lang.number,
	            }
	        }
	    });
	}
});
 
ENDJS;
 
} else if (isset($_GET['ps_simplemodal_janrain_engage_style'])) {
	
	$m_width = (int)$_GET['ps_simplemodal_janrain_engage_modal_width'] . "px";
	
	Header("content-type: text/css");
	echo<<<ENDCSS
/**
* @desc modify the SimpleModal Login style
* @author PerS - http://soderlind.no
*/

.simplemodal-container, #simplemodal-login-container {width:$m_width; height:auto;}
.simplemodal-container form, #simplemodal-login-container form {overflow:auto;}
.simplemodal-login-credit {width:90%; padding-top:4px; text-align:center; bottom:0;}


ENDCSS;
} else {
	if (class_exists('ps_simplemodal_janrain_engage')) { 
		register_activation_hook(__FILE__, array('ps_simplemodal_janrain_engage','ps_simplemodal_janrain_engage_dependency_check')); 	
    	$ps_simplemodal_janrain_engage_var = new ps_simplemodal_janrain_engage();
	}
}
?>