<?php

/**
 * Template pour afficher les vidéos sous forme de cartes
 * 
 * @package Asana Child
 */

// Vérifier si on a des vidéos
if (have_posts()) :

?>


  <div class="video-grid">
    <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('template-parts/video', 'card'); ?>
    <?php endwhile; ?>
  </div>

<?php else : ?>
  <!-- Aucune vidéo trouvée -->
  <div class="no-videos">
    <p>Aucune vidéo trouvée.</p>
  </div>
<?php endif; ?>