<?php

/**
 * Configuration WooCommerce pour les cours en ligne
 * Force l'activation de l'inscription et améliore la gestion des comptes
 */

// Forcer l'activation de l'inscription dans WooCommerce
add_filter('woocommerce_enable_myaccount_registration', '__return_true');
add_filter('woocommerce_enable_checkout_login_reminder', '__return_true');

// Activer l'inscription sur la page de checkout
add_filter('woocommerce_enable_checkout_login_reminder', '__return_true');

// Personnaliser les messages de succès d'inscription
add_filter('woocommerce_registration_success_message', 'customize_fitzen_registration_success');
function customize_fitzen_registration_success($message)
{
  return '🎉 Félicitations ! Votre compte a été créé avec succès. Vous pouvez maintenant accéder aux cours en ligne.';
}

// Personnaliser le message de bienvenue après connexion
add_filter('woocommerce_login_success_message', 'customize_fitzen_login_success');
function customize_fitzen_login_success($message)
{
  return '👋 Bienvenue ! Vous êtes maintenant connecté à votre espace client.';
}

// Rediriger vers la page appropriée après inscription
add_filter('woocommerce_registration_redirect', 'customize_fitzen_registration_redirect');
function customize_fitzen_registration_redirect($redirect)
{
  // Si l'utilisateur vient du checkout, le rediriger vers le checkout
  if (isset($_GET['checkout']) && isset($_GET['fitzen_required'])) {
    return wc_get_checkout_url();
  }

  // Sinon, redirection par défaut vers la page mon compte
  return wc_get_page_permalink('myaccount');
}

// Personnaliser les champs du formulaire d'inscription
add_filter('woocommerce_registration_fields', 'customize_fitzen_registration_fields');
function customize_fitzen_registration_fields($fields)
{
  // Ajouter un champ pour le prénom si il n'existe pas
  if (!isset($fields['first_name'])) {
    $fields['first_name'] = array(
      'label' => 'Prénom',
      'required' => true,
      'class' => array('form-row-first'),
      'priority' => 10,
    );
  }

  // Ajouter un champ pour le nom si il n'existe pas
  if (!isset($fields['last_name'])) {
    $fields['last_name'] = array(
      'label' => 'Nom',
      'required' => true,
      'class' => array('form-row-last'),
      'priority' => 20,
    );
  }

  // Personnaliser le label du champ email
  if (isset($fields['email'])) {
    $fields['email']['label'] = 'Adresse email';
    $fields['email']['description'] = 'Votre adresse email sera utilisée pour vous connecter et recevoir vos informations Fit\'Zen.';
  }

  // Personnaliser le label du champ mot de passe
  if (isset($fields['password'])) {
    $fields['password']['label'] = 'Mot de passe';
    $fields['password']['description'] = 'Choisissez un mot de passe sécurisé pour protéger votre compte.';
  }

  return $fields;
}
