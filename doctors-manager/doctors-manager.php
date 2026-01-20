<?php
/**
 * Plugin Name: Doctors Manager
 * Description: Custom Post Type "Doctors", Taxonomies and Filtering Logic for Test Task.
 * Version: 1.1
 * Author: Reflejo16
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 1. РЕГИСТРАЦИЯ ТИПА ЗАПИСИ "ДОКТОР" (Requirement 1)
 */
function dm_register_doctors_cpt() {
    $labels = array(
        'name'               => 'Доктора',
        'singular_name'      => 'Доктор',
        'add_new'            => 'Добавить доктора',
        'add_new_item'       => 'Добавить нового доктора',
        'edit_item'          => 'Редактировать доктора',
        'all_items'          => 'Все доктора',
        'view_item'          => 'Посмотреть доктора',
        'search_items'       => 'Искать докторов',
        'not_found'          => 'Доктора не найдены',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'doctors' ),
        'menu_icon'          => 'dashicons-businessman',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true, // Поддержка Gutenberg и REST API
    );

    register_post_type( 'doctors', $args );
}
add_action( 'init', 'dm_register_doctors_cpt' );

/**
 * 2. РЕГИСТРАЦИЯ ТАКСОНОМИЙ (Requirement 2)
 */
function dm_register_taxonomies() {
    // Специализация (Иерархическая)
    register_taxonomy( 'specialization', 'doctors', array(
        'label'             => 'Специализации',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ));

    // Город (Иерархическая - выбрана для удобства группировки по регионам в будущем)
    register_taxonomy( 'city', 'doctors', array(
        'label'             => 'Города',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ));
}
add_action( 'init', 'dm_register_taxonomies' );

/**
 * 3. ЛОГИКА ФИЛЬТРАЦИИ, СОРТИРОВКИ И ПАГИНАЦИИ (Requirement 4, 5, 6)
 * Используем хук pre_get_posts для модификации основного запроса архива.
 */
function dm_filter_doctors_archive($query) {
    // Выполняем только для фронтенда, основного запроса и архива врачей
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('doctors')) {
        
        // Устанавливаем количество записей
        $query->set('posts_per_page', 6);

        // --- ФИЛЬТР ПО ТАКСОНОМИЯМ ---
        $tax_query = array('relation' => 'AND');

        // Санитизируем входящие параметры перед использованием
        if (!empty($_GET['specialization'])) {
            $tax_query[] = array(
                'taxonomy' => 'specialization',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['specialization']),
            );
        }

        if (!empty($_GET['city'])) {
            $tax_query[] = array(
                'taxonomy' => 'city',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['city']),
            );
        }

        if (count($tax_query) > 1) {
            $query->set('tax_query', $tax_query);
        }

        // --- СОРТИРОВКА ПО ПОЛЯМ ACF ---
        if (!empty($_GET['sort'])) {
            $sort = sanitize_text_field($_GET['sort']);
            
            switch ($sort) {
                case 'rating_desc':
                    $query->set('meta_key', 'rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                case 'price_asc':
                    $query->set('meta_key', 'price_from');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
                    break;
                case 'exp_desc':
                    $query->set('meta_key', 'experience');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
            }
        }
    }
}
add_action('pre_get_posts', 'dm_filter_doctors_archive');