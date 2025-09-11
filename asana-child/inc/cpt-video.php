<?php
// Créer le custom post type "Video"
add_action('init', 'create_video_post_type');
function create_video_post_type()
{
  $labels = array(
    'name' => 'Vidéos cours en ligne',
    'singular_name' => 'Vidéo',
    'menu_name' => 'Vidéos',
    'add_new' => 'Ajouter une vidéo',
    'add_new_item' => 'Ajouter une nouvelle vidéo',
    'edit_item' => 'Modifier la vidéo',
    'new_item' => 'Nouvelle vidéo',
    'view_item' => 'Voir la vidéo',
    'search_items' => 'Rechercher des vidéos',
    'not_found' => 'Aucune vidéo trouvée',
    'not_found_in_trash' => 'Aucune vidéo trouvée dans la corbeille',
    'parent_item_colon' => 'Vidéo parente :',
    'all_items' => 'Toutes les vidéos',
    'archives' => 'Archives des vidéos',
    'insert_into_item' => 'Insérer dans la vidéo',
    'uploaded_to_this_item' => 'Téléchargé pour cette vidéo',
    'featured_image' => 'Image à la une',
    'set_featured_image' => 'Définir l\'image à la une',
    'remove_featured_image' => 'Supprimer l\'image à la une',
    'use_featured_image' => 'Utiliser comme image à la une',
    'filter_items_list' => 'Filtrer la liste des vidéos',
    'items_list_navigation' => 'Navigation de la liste des vidéos',
    'items_list' => 'Liste des vidéos',
    'item_published' => 'Vidéo publiée.',
    'item_published_privately' => 'Vidéo publiée en privé.',
    'item_reverted_to_draft' => 'Vidéo remise en brouillon.',
    'item_scheduled' => 'Vidéo programmée.',
    'item_updated' => 'Vidéo mise à jour.',
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => true,
    'show_in_admin_bar' => true,
    'menu_position' => 20,
    'menu_icon' => 'dashicons-video-alt3', // Icône vidéo
    'can_export' => true,
    'has_archive' => true,
    'exclude_from_search' => false,
    'capability_type' => 'post',
    'show_in_rest' => true, // Support Gutenberg
    'supports' => array(
      'title', // Titre
      'editor', // Éditeur de contenu
      'excerpt', // Extrait
      'thumbnail', // Image à la une
      'custom-fields', // Champs personnalisés
      'revisions', // Révisions
      'page-attributes', // Attributs de page
    ),
    'rewrite' => array(
      'slug' => 'video',
      'with_front' => false,
    ),
    'taxonomies' => array('video_category'),
  );

  register_post_type('video', $args);
}

// Créer une custom taxonomy pour les vidéos
add_action('init', 'create_video_taxonomy');
function create_video_taxonomy()
{
  $labels = array(
    'name' => 'Catégories de vidéos',
    'singular_name' => 'Catégorie de vidéo',
    'search_items' => 'Rechercher des catégories',
    'all_items' => 'Toutes les catégories',
    'parent_item' => 'Catégorie parente',
    'parent_item_colon' => 'Catégorie parente :',
    'edit_item' => 'Modifier la catégorie',
    'update_item' => 'Mettre à jour la catégorie',
    'add_new_item' => 'Ajouter une nouvelle catégorie',
    'new_item_name' => 'Nom de la nouvelle catégorie',
    'menu_name' => 'Catégories',
    'view_item' => 'Voir la catégorie',
    'popular_items' => 'Catégories populaires',
    'separate_items_with_commas' => 'Séparer les catégories par des virgules',
    'add_or_remove_items' => 'Ajouter ou supprimer des catégories',
    'choose_from_most_used' => 'Choisir parmi les plus utilisées',
    'not_found' => 'Aucune catégorie trouvée',
    'no_terms' => 'Aucune catégorie',
    'filter_by_item' => 'Filtrer par catégorie',
    'items_list_navigation' => 'Navigation de la liste des catégories',
    'items_list' => 'Liste des catégories',
    'back_to_items' => '← Retour aux catégories',
    'item_link' => 'Lien de la catégorie',
    'item_link_description' => 'Un lien vers une catégorie',
  );

  $args = array(
    'labels' => $labels,
    'hierarchical' => true, // Comme les catégories (avec hiérarchie)
    'public' => true,
    'show_ui' => true,
    'show_admin_column' => true, // Afficher dans la liste des vidéos
    'show_in_nav_menus' => true,
    'show_tagcloud' => false,
    'show_in_rest' => true, // Support Gutenberg
    'rewrite' => array(
      'slug' => 'categorie-video',
      'with_front' => false,
      'hierarchical' => true,
    ),
    'capabilities' => array(
      'manage_terms' => 'manage_categories',
      'edit_terms' => 'manage_categories',
      'delete_terms' => 'manage_categories',
      'assign_terms' => 'edit_posts',
    ),
  );

  register_taxonomy('video_category', array('video'), $args);
}

// Page d'options pour le CPT Vidéo
if (function_exists('acf_add_options_page')) {
  acf_add_options_sub_page(array(
    'page_title'  => 'Options',
    'menu_title'  => 'Options',
    'parent_slug' => 'edit.php?post_type=video',
  ));
}

// Callback pour afficher la page d'options
function video_options_page_callback()
{

  // Récupérer toutes les catégories de vidéos
  $video_categories = get_terms(array(
    'taxonomy' => 'video_category',
    'hide_empty' => false,
  ));
?>
  <div class="wrap">
    <h1>Options Vidéos</h1>

  </div>
<?php
}
