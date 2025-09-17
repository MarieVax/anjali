<?php

/**
 * Fonctions pour la gestion de la liste des clients des cours en ligne
 * 
 * @package Asana Child
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Ajoute un sous-menu dans l'admin WordPress pour afficher les clients des cours en ligne
 */
function add_online_course_customers_menu()
{
  add_submenu_page(
    'woocommerce', // Menu parent (WooCommerce)
    'Clients Cours en Ligne', // Titre de la page
    'Clients Cours en Ligne', // Titre du menu
    'manage_woocommerce', // Capacité requise
    'online-course-customers', // Slug de la page
    'display_online_course_customers_page' // Fonction de callback
  );
}
add_action('admin_menu', 'add_online_course_customers_menu');

/**
 * Affiche la page des clients des cours en ligne
 */
function display_online_course_customers_page()
{
  // Récupérer tous les clients qui ont commandé des cours en ligne
  $customers = get_online_course_customers();

?>
  <div class="wrap">
    <h1>Clients ayant commandé des cours en ligne</h1>

    <?php if (!empty($customers)) : ?>
      <div class="tablenav top">
        <div class="alignleft actions">
          <p><strong><?php echo count($customers); ?></strong> client(s) trouvé(s)</p>
        </div>
      </div>

      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th scope="col">Nom</th>
            <th scope="col">Prénom</th>
            <th scope="col">Email</th>
            <th scope="col">Téléphone</th>
            <th scope="col">Date de commande</th>
            <th scope="col">Produit commandé</th>
            <th scope="col">Statut</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($customers as $customer) : ?>
            <tr>
              <td><strong><?php echo esc_html($customer['last_name']); ?></strong></td>
              <td><?php echo esc_html($customer['first_name']); ?></td>
              <td>
                <a href="mailto:<?php echo esc_attr($customer['email']); ?>">
                  <?php echo esc_html($customer['email']); ?>
                </a>
              </td>
              <td><?php echo esc_html($customer['phone']); ?></td>
              <td><?php echo esc_html($customer['order_date']); ?></td>
              <td><?php echo esc_html($customer['product_name']); ?></td>
              <td>
                <span class="order-status status-<?php echo esc_attr($customer['order_status']); ?>">
                  <?php echo esc_html($customer['order_status']); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else : ?>
      <p>Aucun client n'a encore commandé de cours en ligne.</p>
    <?php endif; ?>
  </div>

  <style>
    .order-status {
      padding: 4px 8px;
      border-radius: 3px;
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .status-completed {
      background-color: #c6e1c6;
      color: #5b841b;
    }

    .status-processing {
      background-color: #f8dda7;
      color: #94660c;
    }

    .status-on-hold {
      background-color: #f1f1f1;
      color: #777;
    }

    .status-pending {
      background-color: #f1f1f1;
      color: #777;
    }
  </style>
<?php
}

/**
 * Récupère tous les clients qui ont commandé des cours en ligne
 */
function get_online_course_customers()
{
  $customers = array();

  // Récupérer toutes les commandes
  $orders = wc_get_orders(array(
    'status' => array('completed', 'processing', 'on-hold', 'pending'),
    'limit' => -1
  ));

  foreach ($orders as $order) {
    $has_online_course = false;
    $online_course_products = array();

    // Vérifier si la commande contient des cours en ligne
    foreach ($order->get_items() as $item) {
      $product_id = $item->get_product_id();

      if (has_term('cours-en-ligne', 'product_cat', $product_id)) {
        $has_online_course = true;
        $online_course_products[] = $item->get_name();
      }
    }

    // Si la commande contient des cours en ligne, ajouter le client à la liste
    if ($has_online_course) {
      // Récupérer les données de facturation correctement
      $first_name = $order->get_billing_first_name();
      $last_name = $order->get_billing_last_name();
      $email = $order->get_billing_email();
      $phone = $order->get_billing_phone();

      $customers[] = array(
        'first_name' => $first_name ?: 'Non renseigné',
        'last_name' => $last_name ?: 'Non renseigné',
        'email' => $email ?: 'Non renseigné',
        'phone' => $phone ?: 'Non renseigné',
        'order_date' => $order->get_date_created()->date('d/m/Y H:i'),
        'product_name' => implode(', ', $online_course_products),
        'order_status' => $order->get_status(),
        'order_id' => $order->get_id()
      );
    }
  }

  // Trier par date de commande (plus récent en premier)
  usort($customers, function ($a, $b) {
    return strtotime($b['order_date']) - strtotime($a['order_date']);
  });

  return $customers;
}
