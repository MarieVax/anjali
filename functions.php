<?php
// Include custom post type for videos
require_once dirname(__FILE__) . '/inc/cpt-video.php';
require_once dirname(__FILE__) . '/inc/account.php';

// styles of child theme
function uni_child_theme_style()
{

	wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.6.1');

	wp_register_style('bxslider-styles', get_template_directory_uri() . '/css/bxslider.css', '4.2.3');
	wp_enqueue_style('bxslider-styles');

	wp_register_style('ball-clip-rotate-styles', get_template_directory_uri() . '/css/ball-clip-rotate.css', '0.1.0');
	wp_enqueue_style('ball-clip-rotate-styles');

	wp_register_style('fancybox-styles', get_template_directory_uri() . '/css/fancybox.css', '2.1.5');
	wp_enqueue_style('fancybox-styles');

	wp_register_style('jscrollpane-styles', get_template_directory_uri() . '/css/jscrollpane.css', '2.1.5');
	wp_enqueue_style('jscrollpane-styles');

	wp_register_style('selectric-styles', get_template_directory_uri() . '/css/selectric.css', '2.1.5');
	wp_enqueue_style('selectric-styles');

	if (class_exists('WooCommerce')) {
		wp_register_style('asana-woocommerce-styles', get_template_directory_uri() . '/css/woocommerce-store.css', '1.7.7');
		wp_enqueue_style('asana-woocommerce-styles');
	}

	if (function_exists('ecwid_check_version')) {
		wp_register_style('asana-ecwid-styles', get_template_directory_uri() . '/css/ecwid-store.css', '1.7.7');
		wp_enqueue_style('asana-ecwid-styles');
	}

	wp_register_style('unitheme-styles', get_template_directory_uri() . '/style.css', array(
		'bxslider-styles',
		'ball-clip-rotate-styles',
		'fancybox-styles',
		'jscrollpane-styles',
		'selectric-styles'
	), '1.7.6', 'all');
	wp_enqueue_style('unitheme-styles');

	if (! ot_get_option('uni_color_schemes')) {
		wp_register_style(
			'unitheme-asana-scheme',
			get_template_directory_uri() . '/css/scheme-default.css',
			array('unitheme-styles'),
			'1.7.7',
			'screen'
		);
		wp_enqueue_style('unitheme-asana-scheme');
	} else {
		$sColourScheme = ot_get_option('uni_color_schemes');
		wp_register_style(
			'unitheme-asana-scheme',
			get_template_directory_uri() . '/css/scheme-' . $sColourScheme . '.css',
			array('unitheme-styles'),
			'1.7.7',
			'screen'
		);
		wp_enqueue_style('unitheme-asana-scheme');
	}

	wp_register_style(
		'unitheme-adaptive',
		get_template_directory_uri() . '/css/adaptive.css',
		array('unitheme-styles'),
		'1.7.7',
		'screen'
	);
	wp_enqueue_style('unitheme-adaptive');

	wp_register_style(
		'unitheme-asana-custom-scheme',
		get_stylesheet_directory_uri() . '/css/scheme-custom.css',
		array('unitheme-styles'),
		'1.7.7',
		'screen'
	);
	wp_enqueue_style('unitheme-asana-custom-scheme');

	wp_register_style(
		'unitheme-child-styles',
		get_stylesheet_directory_uri() . '/style.css',
		array('unitheme-styles'),
		'1.7.7',
		'screen'
	);
	wp_enqueue_style('unitheme-child-styles');
}

add_action('wp_enqueue_scripts', 'uni_child_theme_style');

// after setup of the child theme
add_action('after_setup_theme', 'uni_theme_child_theme_setup');
function uni_theme_child_theme_setup()
{

	// Enable featured image
	add_theme_support('post-thumbnails');

	// Add default posts and comments RSS feed links to head
	add_theme_support('automatic-feed-links');

	// Add html5 suppost for search form and comments list
	add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));

	// translation files for the child theme
	load_child_theme_textdomain('asana-child', get_stylesheet_directory() . '/languages');
}

// ============================================================================
// FONCTIONS POUR CONTRÔLE DES PRODUITS "COURS EN LIGNE"
// ============================================================================

/**
 * Vérifie si un produit appartient à la catégorie "cours en ligne"
 */
function is_online_course_product($product_id)
{
	$product = wc_get_product($product_id);
	if (!$product) {
		return false;
	}

	// Vérifier si le produit appartient à la catégorie "cours en ligne"
	$categories = get_the_terms($product_id, 'product_cat');
	if ($categories && !is_wp_error($categories)) {
		foreach ($categories as $category) {
			if (
				strtolower($category->name) === 'cours en ligne' ||
				strtolower($category->slug) === 'cours-en-ligne'
			) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Empêche l'ajout au panier si l'utilisateur n'est pas connecté et que le produit est un cours en ligne
 */
function prevent_online_course_add_to_cart_for_guests()
{
	if (!is_user_logged_in()) {
		// Vérifier si le produit ajouté est un cours en ligne
		if (isset($_POST['add-to-cart']) && is_online_course_product($_POST['add-to-cart'])) {
			wp_die(
				'<h2>Connexion requise</h2>' .
					'<p>Vous devez être connecté pour ajouter un cours en ligne à votre panier.</p>' .
					'<p><a href="/mon-compte">Se connecter</a> | ' .
					'<a href="/mon-compte">Créer un compte</a></p>',
				'Connexion requise',
				array('response' => 403)
			);
		}
	}
}
add_action('wp_loaded', 'prevent_online_course_add_to_cart_for_guests');

/**
 * Empêche l'ajout via AJAX pour les cours en ligne
 */
function prevent_ajax_add_to_cart_for_online_courses()
{
	if (!is_user_logged_in()) {
		$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));

		if (is_online_course_product($product_id)) {
			wp_send_json_error(array(
				'error' => true,
				'message' => 'Vous devez être connecté pour ajouter un cours en ligne à votre panier.',
				'redirect' => wp_login_url(wc_get_cart_url())
			));
		}
	}
}
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'prevent_ajax_add_to_cart_for_online_courses');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart', 'prevent_ajax_add_to_cart_for_online_courses');

/**
 * Affiche un message d'alerte sur les pages de produits cours en ligne pour les utilisateurs non connectés
 */
function display_login_required_message_for_online_courses()
{
	if (!is_user_logged_in() && is_product() && is_online_course_product(get_the_ID())) {
		echo '<div class="woocommerce-message woocommerce-message--login-required woocommerce-message--login-custom" style="background-color: #f7f6f7; border-left: 4px solid #a46497; padding: 12px; margin-bottom: 20px;">';
		echo '<p class="custom-p"><strong>Connexion requise :</strong> Vous devez être connecté pour acheter ce cours en ligne.</p>';
		echo '<p><a href="/mon-compte" class="custom-btn">Se connecter</a> | ' .
			'<a href="/mon-compte" class="custom-btn">Créer un compte</a></p>';

		echo '</div>';
	}
}
add_action('woocommerce_before_single_product', 'display_login_required_message_for_online_courses');

/**
 * Désactive le bouton "Ajouter au panier" pour les cours en ligne si l'utilisateur n'est pas connecté
 * 
 */
function disable_add_to_cart_button_for_guests()
{
	if (is_product() && is_online_course_product(get_the_ID())) {
		$product_id = get_the_ID();

		// Si l'utilisateur n'est pas connecté
		if (!is_user_logged_in()) {
			remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

			// Ajouter un bouton de connexion à la place
			add_action('woocommerce_after_shop_loop_item', 'display_login_button_instead_of_add_to_cart', 10);
			add_action('woocommerce_single_product_summary', 'display_login_button_instead_of_add_to_cart', 30);
		}
	}
}
add_action('wp', 'disable_add_to_cart_button_for_guests');

/**
 * Affiche un bouton de connexion au lieu du bouton "Ajouter au panier"
 */
function display_login_button_instead_of_add_to_cart()
{
	echo '<div class="woocommerce-login-required">';
	echo '<a href="/mon-compte/" class="single_add_to_cart_button button alt">Se connecter pour acheter</a>';
	echo '</div>';
}


/**
 * Force la création de compte au checkout si le panier contient des cours en ligne
 */
function force_account_creation_for_online_courses()
{
	if (is_checkout() && !is_user_logged_in()) {
		$cart = WC()->cart;
		$has_online_course = false;

		if ($cart && !$cart->is_empty()) {
			foreach ($cart->get_cart() as $cart_item) {
				if (is_online_course_product($cart_item['product_id'])) {
					$has_online_course = true;
					break;
				}
			}
		}

		if ($has_online_course) {
			// Rediriger vers la page de connexion avec un message
			wp_redirect(wp_login_url(wc_get_checkout_url()) . '&online_course=1');
			exit;
		}
	}
}
add_action('template_redirect', 'force_account_creation_for_online_courses');

/**
 * Affiche un message spécial sur la page de connexion si l'utilisateur vient du checkout avec des cours en ligne
 */
function display_online_course_login_message()
{
	if (isset($_GET['online_course']) && $_GET['online_course'] == '1') {
		echo '<div class="woocommerce-message woocommerce-message--online-course" style="background-color: #f7f6f7; border-left: 4px solid #a46497; padding: 12px; margin-bottom: 20px;">';
		echo '<p><strong>Compte requis :</strong> Votre panier contient des cours en ligne. Vous devez créer un compte ou vous connecter pour continuer.</p>';
		echo '</div>';
	}
}
add_action('login_form', 'display_online_course_login_message');

/**
 * Empêche le checkout guest si le panier contient des cours en ligne
 */
function prevent_guest_checkout_for_online_courses()
{
	if (!is_user_logged_in() && is_checkout()) {
		$cart = WC()->cart;
		$has_online_course = false;

		if ($cart && !$cart->is_empty()) {
			foreach ($cart->get_cart() as $cart_item) {
				if (is_online_course_product($cart_item['product_id'])) {
					$has_online_course = true;
					break;
				}
			}
		}

		if ($has_online_course) {
			// Désactiver le checkout guest
			add_filter('woocommerce_checkout_registration_required', '__return_true');

			// Afficher un message
			add_action('woocommerce_before_checkout_form', function () {
				echo '<div class="woocommerce-message woocommerce-message--account-required" style="background-color: #f7f6f7; border-left: 4px solid #a46497; padding: 12px; margin-bottom: 20px;">';
				echo '<p><strong>Compte requis :</strong> Votre panier contient des cours en ligne. Vous devez créer un compte ou vous connecter pour continuer.</p>';
				echo '</div>';
			});
		}
	}
}
add_action('wp', 'prevent_guest_checkout_for_online_courses');

/**
 * Vérifie et nettoie le panier si un utilisateur se déconnecte avec des cours en ligne
 */
function check_cart_on_logout()
{
	if (!is_user_logged_in()) {
		$cart = WC()->cart;
		$items_to_remove = array();

		if ($cart && !$cart->is_empty()) {
			foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
				if (is_online_course_product($cart_item['product_id'])) {
					$items_to_remove[] = $cart_item_key;
				}
			}

			// Supprimer les cours en ligne du panier
			foreach ($items_to_remove as $cart_item_key) {
				$cart->remove_cart_item($cart_item_key);
			}

			if (!empty($items_to_remove)) {
				wc_add_notice('Les cours en ligne ont été retirés de votre panier car vous n\'êtes plus connecté.', 'notice');
			}
		}
	}
}
add_action('wp_loaded', 'check_cart_on_logout');

/**
 * Ajoute des styles CSS pour les messages d'alerte
 */
function add_online_course_styles()
{
	if (class_exists('WooCommerce')) {
		echo '<style>
        .woocommerce-message--login-required,
        .woocommerce-message--online-course,
        .woocommerce-message--account-required {
            background-color: #f7f6f7 !important;
            border-left: 4px solid #a46497 !important;
            padding: 12px !important;
            margin-bottom: 20px !important;
        }
        .woocommerce-login-required .button {
            background-color: #a46497 !important;
            color: white !important;
            padding: 10px 20px !important;
            text-decoration: none !important;
            display: inline-block !important;
            border-radius: 3px !important;
        }
        .woocommerce-login-required .button:hover {
            background-color: #8a4a7d !important;
        }
        </style>';
	}
}
add_action('wp_head', 'add_online_course_styles');

/**
 * Supprime l'option "1 mois d'essai offert" du dropdown des variations si l'utilisateur l'a déjà commandée
 */
function remove_trial_variation_option($args, $product, $variation)
{
	// Vérifier si c'est un produit de cours en ligne et si l'utilisateur est connecté
	if (is_online_course_product($product->get_id()) && is_user_logged_in()) {

		// Vérifier si l'utilisateur a déjà commandé la variation "1 mois d'essai offert"
		if (user_has_ordered_one_month_trial_variation($product->get_id())) {

			// Si c'est l'attribut "duree", supprimer l'option "1 mois d'essai offert"
			if (isset($args['attribute']) && $args['attribute'] === 'duree') {
				$options = $args['options'];

				// Supprimer l'option "1 mois d'essai offert" si elle existe
				if (isset($options['1 mois d\'essai offert'])) {
					unset($options['1 mois d\'essai offert']);
					$args['options'] = $options;
				}
			}
		}
	}

	return $args;
}
add_filter('woocommerce_dropdown_variation_attribute_options_args', 'remove_trial_variation_option', 10, 3);

/**
 * Alternative: Supprime l'option via JavaScript si le filtre ne fonctionne pas
 */
function add_trial_variation_removal_script()
{
	if (is_product() && is_online_course_product(get_the_ID()) && is_user_logged_in()) {
		if (user_has_ordered_one_month_trial_variation(get_the_ID())) {
?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					// Supprimer l'option "1 mois d'essai offert" du dropdown
					$('select[name="attribute_duree"] option[value="1 mois d\'essai offert"]').remove();
				});
			</script>
<?php
		}
	}
}
add_action('wp_footer', 'add_trial_variation_removal_script');

/**
 * Empêche l'ajout au panier de la variation "1 mois d'essai offert" si l'utilisateur l'a déjà commandée
 */
function prevent_trial_variation_add_to_cart($passed, $product_id, $quantity, $variation_id = 0, $variations = array())
{
	// Vérifier si c'est un produit de cours en ligne et si l'utilisateur est connecté
	if (is_online_course_product($product_id) && is_user_logged_in()) {

		// Vérifier si l'utilisateur a déjà commandé la variation "1 mois d'essai offert"
		if (user_has_ordered_one_month_trial_variation($product_id)) {

			// Si une variation est sélectionnée, vérifier si c'est "1 mois d'essai offert"
			if ($variation_id > 0) {
				$variation = wc_get_product($variation_id);
				if ($variation) {
					$variation_attributes = $variation->get_variation_attributes();
					if (isset($variation_attributes['attribute_duree']) && $variation_attributes['attribute_duree'] === '1 mois d\'essai offert') {
						wc_add_notice('Vous avez déjà bénéficié de l\'offre d\'essai de 1 mois offert.', 'error');
						return false;
					}
				}
			}

			// Vérifier aussi dans les variations passées en paramètre
			if (isset($variations['attribute_duree']) && $variations['attribute_duree'] === '1 mois d\'essai offert') {
				wc_add_notice('Vous avez déjà bénéficié de l\'offre d\'essai de 1 mois offert.', 'error');
				return false;
			}
		}
	}

	return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'prevent_trial_variation_add_to_cart', 10, 5);

/**
 * Rediriger vers la page "Mon compte" après le checkout pour les utilisateurs connectés
 */
function redirect_to_my_account_after_checkout($order_id)
{
	// Vérifier si l'utilisateur est connecté
	if (is_user_logged_in()) {
		// Récupérer l'URL de la page "Mon compte"
		$my_account_url = wc_get_page_permalink('myaccount');

		// Rediriger vers la page "Mon compte"
		wp_redirect($my_account_url);
		exit;
	}
}
add_action('woocommerce_thankyou', 'redirect_to_my_account_after_checkout');

/**
 * Ajouter un bouton "Accéder à vos cours en ligne" dans l'email de confirmation de commande
 */
function add_online_courses_button_to_order_email($order, $sent_to_admin, $plain_text, $email)
{
	// Vérifier si c'est l'email de confirmation de commande et si l'utilisateur est connecté
	if (!$sent_to_admin && is_user_logged_in() && $email->id === 'customer_completed_order') {

		// Vérifier si la commande contient des cours en ligne
		$has_online_course = false;
		foreach ($order->get_items() as $item) {
			if (has_term('cours-en-ligne', 'product_cat', $item->get_product_id())) {
				$has_online_course = true;
				break;
			}
		}

		// Si la commande contient des cours en ligne, ajouter le bouton
		if ($has_online_course) {
			if ($plain_text) {
				// Version texte brut
				echo "\n\n" . str_repeat('=', 50) . "\n";
				echo "ACCÉDER À VOS COURS EN LIGNE\n";
				echo str_repeat('=', 50) . "\n";
				echo "Cliquez sur le lien suivant pour accéder à votre bibliothèque de cours :\n";
				echo "https://www.anjali-eau-bien-etre.com/cours-en-ligne/\n";
				echo str_repeat('=', 50) . "\n\n";
			} else {
				// Version HTML
				echo '<div style="margin: 30px 0; text-align: center;">';
				echo '<div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0;">';
				echo '<h3 style="color: #389ddf; margin: 0 0 15px 0; font-size: 18px;">🎥 Vos cours en ligne sont disponibles !</h3>';
				echo '<p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">Accédez à votre bibliothèque de cours en ligne dès maintenant.</p>';
				echo '<a href="https://www.anjali-eau-bien-etre.com/cours-en-ligne/" style="display: inline-block; background-color: #389ddf; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; transition: background-color 0.3s ease;">Accéder à vos cours en ligne</a>';
				echo '</div>';
				echo '</div>';
			}
		}
	}
}
//add_action('woocommerce_email_order_details', 'add_online_courses_button_to_order_email', 20, 4);


// Vérifier si l'utilisateur a une commande avec un produit de la catégorie "Cours en ligne"
function user_has_online_course_order()
{
	// Vérifier si l'utilisateur est connecté
	if (!is_user_logged_in()) {
		return false;
	}

	$user_id = get_current_user_id();

	// Récupérer toutes les commandes de l'utilisateur
	$customer_orders = wc_get_orders(array(
		'customer_id' => $user_id,
		'status' => array('completed', 'processing', 'on-hold'),
		'limit' => -1
	));


	// Vérifier chaque commande pour des produits de la catégorie "Cours en ligne"
	foreach ($customer_orders as $order) {
		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
			// Vérifier si le produit appartient à la catégorie "Cours en ligne"
			if (has_term('cours-en-ligne', 'product_cat', $product_id)) {
				return true;
			}
		}
	}
	return false;
}

function is_subscription_active()
{
	$user_id = get_current_user_id();

	// Récupérer toutes les commandes de l'utilisateur
	$customer_orders = wc_get_orders(array(
		'customer_id' => $user_id,
		'status' => array('completed', 'processing', 'on-hold'),
		'limit' => -1
	));

	$variation_id = null;
	$order_date = null;

	// Vérifier chaque commande pour des produits de la catégorie "Cours en ligne"
	foreach ($customer_orders as $order) {
		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
			// Vérifier si le produit appartient à la catégorie "Cours en ligne"
			if (has_term('cours-en-ligne', 'product_cat', $product_id)) {
				$variation_id = $item->get_variation_id();
				$order_date = $order->get_date_created();
				break 2;
			}
		}
	}

	// Si aucune commande avec cours en ligne n'est trouvée, retourner false
	if ($variation_id === null || $order_date === null) {
		return false;
	}

	$formatted_date = $order_date->format('Y-m-d H:i:s');

	// Comparer avec la date d'aujourd'hui
	$today = new DateTime();
	$order_datetime = new DateTime($formatted_date);
	$diff = $today->diff($order_datetime);

	// Récupérer le premier produit de la catégorie "cours-en-ligne"
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => 1,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => 'cours-en-ligne',
				'operator' => 'IN'
			)
		)
	);

	$products = get_posts($args);
	$online_course_product = !empty($products) ? $products[0] : null;
	if ($online_course_product) {
		$product = wc_get_product($online_course_product->ID);
		if ($product && $product->is_type('variable')) {
			$variations = $product->get_available_variations();
			foreach ($variations as $variation) {

				if ($variation['variation_id'] === $variation_id) {
					$subscription_duration = $variation['attributes']['attribute_duree'];
					break;
				}
			}
			// Comparer la durée de l'abonnement avec la différence en jours
			$duration_days = 0;

			if (strpos($subscription_duration, 'mois') !== false) {
				$duration_days = (int) $subscription_duration * 31;
			} elseif (strpos($subscription_duration, 'an') !== false) {
				$duration_days = (int) $subscription_duration * 365;
			}

			$is_active = ($diff->days < $duration_days) ? true : false;
			return $is_active;
		}
	}

	return false;
}

/**
 * Vérifie si l'utilisateur a déjà commandé le produit avec la variation "1 mois d'essai offert"
 */
function user_has_ordered_one_month_trial_variation($product_id)
{
	// Vérifier si l'utilisateur est connecté
	if (!is_user_logged_in()) {
		return false;
	}

	$user_id = get_current_user_id();

	// Récupérer toutes les commandes de l'utilisateur
	$customer_orders = wc_get_orders(array(
		'customer_id' => $user_id,
		'status' => array('completed', 'processing', 'on-hold'),
		'limit' => -1
	));

	// Vérifier chaque commande pour des produits de la catégorie "Cours en ligne" avec variation "1 mois d'essai offert"
	foreach ($customer_orders as $order) {
		foreach ($order->get_items() as $item) {
			$item_product_id = $item->get_product_id();
			$variation_id = $item->get_variation_id();

			// Vérifier si c'est le même produit et s'il appartient à la catégorie "Cours en ligne"
			if ($item_product_id == $product_id && has_term('cours-en-ligne', 'product_cat', $item_product_id)) {

				// Si c'est une variation, vérifier l'attribut durée
				if ($variation_id > 0) {
					$variation = wc_get_product($variation_id);
					if ($variation) {
						$variation_attributes = $variation->get_variation_attributes();

						// Debug : afficher les attributs pour vérifier
						// error_log('Variation attributes: ' . print_r($variation_attributes, true));

						if (isset($variation_attributes['attribute_duree'])) {
							$duree_value = $variation_attributes['attribute_duree'];
							// Debug : afficher la valeur de durée
							// error_log('Durée trouvée: "' . $duree_value . '"');

							if ($duree_value === '1 mois d\'essai offert') {
								return true;
							}
						}
					}
				}
			}
		}
	}

	return false;
}
