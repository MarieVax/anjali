<?php

/**
 * Template pour le contenu d'une carte vidéo
 * 
 * @package Asana Child
 */

// Récupérer les champs ACF
$cours_url = get_field('cours_url');
$duree = get_field('duree');
?>



<article id="post-<?php the_ID(); ?>" <?php post_class('video-card'); ?>>
  <?php if ($cours_url) : ?>
    <a href="<?php echo esc_url($cours_url); ?>" target="_blank" class="video-card-link">
    <?php endif; ?>
    <!-- Image à la une -->
    <div class="video-thumbnail">
      <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail('medium'); ?>
        <div class="video-play-overlay">
          <span class="play-icon">▶</span>
        </div>
      <?php else : ?>
        <div class="video-placeholder">
          <span class="play-icon">▶</span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Contenu de la carte -->
    <div class="video-content">

      <!-- Titre -->
      <h3 class="video-title">
        <?php the_title(); ?>
      </h3>

      <!-- Durée -->
      <?php if ($duree) : ?>
        <div class="video-duration">
          <span class="duration-icon">⏱</span>
          <span class="duration-text"><?php echo esc_html($duree); ?></span>
        </div>
      <?php endif; ?>

      <!-- Extrait -->
      <?php if (get_the_content()) : ?>
        <div class="video-content">
          <?php the_content(); ?>
        </div>
      <?php endif; ?>


    </div>
    <?php if ($cours_url) : ?>
    </a>
  <?php endif; ?>
</article>