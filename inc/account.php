<?php // Ajouter un lien "Mon compte" au menu primaire
add_filter('wp_nav_menu_items', 'add_account_link_to_menu', 10, 2);
function add_account_link_to_menu($items, $args)
{
  // Vérifier si c'est le menu primaire
  if ($args->theme_location == 'primary') {
    // Créer le lien "Mon compte"
    $account_link = '<li class="menu-item menu-item-account"><a href="' . wc_get_page_permalink('myaccount') . '" class="account-link">👤 Mon compte</a></li>';

    // Ajouter le lien à la fin du menu
    $items .= $account_link;
  }

  return $items;
}

// Ajouter un bandeau à la page "Mon compte" pour rediriger vers les cours en ligne
add_action('woocommerce_account_navigation', 'add_courses_banner_to_myaccount', 5);
function add_courses_banner_to_myaccount()
{
  // Vérifier si l'utilisateur est connecté
  if (is_user_logged_in()) {


    // Afficher la bannière seulement si l'utilisateur a une commande avec un cours en ligne et une inscription active
    if (((user_has_online_course_order() && is_subscription_active())) || current_user_can('administrator')) {

      echo '
<a href="/cours-en-ligne" class="courses-banner">
  <h3>Bibliothèque de cours en ligne</h3>
  <p>Accédez à tous vos cours et à la bibliothèque en ligne</p>
  <div class="courses-banner-btn" style="display: inline-block;">
    Accéder aux cours
  </div>
  <div class="overlay"></div>
</a>';
    }
  }
}

// Ajouter l'endpoint "courses" à WooCommerce
add_action('init', 'add_courses_endpoint');
function add_courses_endpoint()
{
  add_rewrite_endpoint('courses', EP_ROOT | EP_PAGES);
}

// Flusher les règles de réécriture après ajout de l'endpoint
add_action('init', 'flush_rewrite_rules_once');
function flush_rewrite_rules_once()
{
  if (get_option('courses_endpoint_flushed') != 'yes') {
    flush_rewrite_rules();
    update_option('courses_endpoint_flushed', 'yes');
  }
}
