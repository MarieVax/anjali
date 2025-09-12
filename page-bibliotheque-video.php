<?php

/**
 * Template Name: Bibliothèque Vidéo
 * 
 * Template pour afficher la bibliothèque de vidéos
 */

get_header();


?>

<div class="page-bibliotheque-video uni-container">
  <?php
  $page_title = get_the_title();
  ?>

  <h1 class="singleTitle"><?php echo $page_title; ?></h1>

  <?php
  // Vérifier l'accès avant d'afficher le contenu
  if ((!user_has_online_course_order() || !is_subscription_active()) && !current_user_can('administrator')) :

  ?>
    <div class="access-denied-message" style="text-align: center; padding: 40px 20px; background-color: #f7f6f7; border-left: 4px solid #389ddf; margin: 20px 0;">
      <h2 style="color: #389ddf; margin-bottom: 20px;">Accès restreint</h2>
      <p style="font-size: 18px; line-height: 1.6; margin-bottom: 20px;">
        Vous n'avez pas accès à la bibliothèque de cours en ligne.
        <a href="/product/cours-en-ligne/" style="color: #389ddf; text-decoration: underline; font-weight: bold;">Souscrivez un abonnement</a>
        pour débloquer l'accès.
      </p>
      <a href="/product/cours-en-ligne/" class="button viewClasses" style="color: #389ddf; border-color: #389ddf; font-weight: bold;">
        Voir l'abonnement
      </a>
    </div>
  <?php else : ?>
    <!-- Bloc flex avec deux cartes -->
    <div class="container">
      <div class="featured-videos-flex">

        <!-- Carte de gauche - Options du CPT Video -->
        <div class="featured-video-card featured-options">
          <?php
          // Récupérer les champs ACF des options du CPT Video
          $options_lien = get_field('lien_du_cours_hedbo_sur_zoom', 'option');
          $options_image = get_field('image', 'option');
          $options_duree = get_field('duree', 'option');
          $options_horaire = get_field('horaire', 'option');
          ?>

          <?php if ($options_lien) : ?>
            <a href="<?php echo esc_url($options_lien); ?>" target="_blank" class="featured-video-link">
            <?php endif; ?>

            <div class="featured-video-content">
              <div class="featured-video-text">
                <h3 class="featured-video-title">Cours en direct</h3>
                <?php if ($options_duree) : ?>
                  <div class="featured-video-duration">
                    <span class="duration-icon">⏱</span>
                    <span class="duration-text"><?php echo esc_html($options_duree); ?></span>
                  </div>
                <?php endif; ?>
                <?php if ($options_horaire) : ?>
                  <div class="featured-video-schedule">
                    <span class="schedule-icon">🕐</span>
                    <span class="schedule-text"><?php echo esc_html($options_horaire); ?></span>
                  </div>
                <?php endif; ?>
              </div>

              <div class="featured-video-image">
                <?php if ($options_image) : ?>
                  <img src="<?php echo esc_url($options_image['url']); ?>" alt="<?php echo esc_attr($options_image['alt']); ?>">
                  <div class="video-play-overlay">
                    <span class="play-icon">▶</span>
                  </div>
                <?php else : ?>
                  <div class="featured-video-placeholder">
                    <span class="play-icon">▶</span>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($options_lien) : ?>
            </a>
          <?php endif; ?>
        </div>

        <!-- Carte de droite - Dernier CPT Video de cours-hebdo -->

        <?php
        // Récupérer le dernier CPT Video de la catégorie cours-hebdo
        $latest_video_query = new WP_Query(array(
          'post_type' => 'video',
          'posts_per_page' => 1,
          'post_status' => 'publish',
          'orderby' => 'date',
          'order' => 'DESC',
          'tax_query' => array(
            array(
              'taxonomy' => 'video_category',
              'field' => 'slug',
              'terms' => 'cours-hebdos',
              'operator' => 'IN'
            )
          )
        ));

        if ($latest_video_query->have_posts()) :
          while ($latest_video_query->have_posts()) : $latest_video_query->the_post();
            $latest_duree = get_field('duree');
            $latest_cours_url = get_field('cours_url');
        ?>

            <?php if ($latest_cours_url) : ?>
              <div class="featured-video-card featured-latest">
                <a href="<?php echo esc_url($latest_cours_url); ?>" target="_blank" class="featured-video-link">
                <?php endif; ?>

                <div class="featured-video-content">
                  <div class="featured-video-text">
                    <div class="featured-video-title-new">Nouveau cours disponible</div>
                    <h3 class="featured-video-title"><?php the_title(); ?></h3>
                    <?php if ($latest_duree) : ?>
                      <div class="featured-video-duration">
                        <span class="duration-icon">⏱</span>
                        <span class="duration-text"><?php echo esc_html($latest_duree); ?></span>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="featured-video-image">
                    <?php if (has_post_thumbnail()) : ?>
                      <?php the_post_thumbnail('medium'); ?>
                      <div class="video-play-overlay">
                        <span class="play-icon">▶</span>
                      </div>
                    <?php else : ?>
                      <div class="featured-video-placeholder">
                        <span class="play-icon">▶</span>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if ($latest_cours_url) : ?>
                </a>
              </div>
            <?php endif; ?>

        <?php
          endwhile;
          wp_reset_postdata();
        endif;
        ?>


      </div>
    </div>

    <?php
    // Récupérer toutes les catégories du CPT vidéo
    $video_categories_terms = get_terms(array(
      'taxonomy' => 'video_category',
      'hide_empty' => true, // Ne récupérer que les catégories qui ont des vidéos
      'orderby' => 'name',
      'order' => 'ASC'
    ));

    // Créer le tableau des catégories
    $video_categories = array();
    if (!is_wp_error($video_categories_terms) && !empty($video_categories_terms)) {
      foreach ($video_categories_terms as $term) {
        $video_categories[$term->slug] = $term->name;
      }
    }

    // Boucle sur chaque catégorie
    foreach ($video_categories as $category_slug => $category_title) :

      // Query pour récupérer les vidéos de cette catégorie
      $video_query = new WP_Query(array(
        'post_type' => 'video',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => array(
          array(
            'taxonomy' => 'video_category',
            'field' => 'slug',
            'terms' => $category_slug,
            'operator' => 'IN'
          )
        )
      ));

      // Vérifier s'il y a des vidéos dans cette catégorie
      if ($video_query->have_posts()) :
    ?>
        <div class="container video-category-section">
          <div class="category-header">
            <h2 class="category-title"><?php echo esc_html($category_title); ?></h2>
          </div>

          <?php
          // Sauvegarder la query principale
          $temp_query = $wp_query;
          $wp_query = $video_query;

          // Afficher les vidéos avec le template video-cards
          get_template_part('template-parts/video', 'cards');

          // Restaurer la query principale
          $wp_query = $temp_query;
          wp_reset_postdata();
          ?>
        </div>
    <?php
      endif;
    endforeach;
    ?>
  <?php endif; ?>
</div>

<?php get_footer(); ?>