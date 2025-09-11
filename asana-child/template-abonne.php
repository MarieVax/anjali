<?php

/**
 * Template Name: Page Abonné Premium
 */

get_header();

// Vérifie si l'utilisateur est connecté
if (!is_user_logged_in()) {
  // Redirige vers la page de connexion
  wp_redirect(wp_login_url(get_permalink()));
  exit;
}

// Vérifie le rôle de l'utilisateur
$current_user = wp_get_current_user();

if (!in_array('abonne_premium', $current_user->roles)) {
  // Affiche un message d'erreur ou redirige ailleurs
  echo ('Désolé, cette page est réservée aux abonnés premium.');
} else {
?>


  <section class="uni-container">
    <?php if (have_posts()) : while (have_posts()) : the_post();
        if (has_post_thumbnail()) {
          $iAttachId = get_post_thumbnail_id($post->ID);
          $aPageHeaderImage = wp_get_attachment_image_src($iAttachId, 'full');
          $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
          $sPageHeaderImage = get_template_directory_uri() . '/images/placeholders/pageheader-classes.jpg';
        }
    ?>
        <div class="pageHeader" style="background-image: url(<?php echo esc_url($sPageHeaderImage); ?>);">
          <h1><?php the_title() ?></h1>
        </div>
        <div class="contentWrap">

          <?php the_content() ?>

        </div>
  <?php endwhile;
    endif;
  } ?>
  </section>


  <?php get_footer(); ?>