<?php
/**
 * The template for displaying "doctors" CPT archive page.
 * Requirement 4: Grid layout and Pagination
 * Requirement 5: Filtering Interface
 */

get_header(); ?>

<main id="primary" class="site-main container" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <header class="page-header" style="margin-bottom: 40px;">
        <h1 class="page-title" style="font-size: 2.5rem; color: #333;">Наши специалисты</h1>
    </header>

    <!-- ТРЕБОВАНИЕ 5: Интерфейс фильтрации -->
    <section class="filters-section" style="background: #f8f9fa; padding: 25px; margin-bottom: 40px; border-radius: 12px; border: 1px solid #eee;">
        <form method="GET" action="<?php echo esc_url(get_post_type_archive_link('doctors')); ?>" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <!-- Фильтр по специализации -->
            <div class="filter-group" style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Специализация</label>
                <select name="specialization" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                    <option value="">Все специализации</option>
                    <?php
                    $specializations = get_terms(['taxonomy' => 'specialization', 'hide_empty' => false]);
                    foreach ($specializations as $spec) : ?>
                        <option value="<?php echo esc_attr($spec->slug); ?>" <?php selected($_GET['specialization'] ?? '', $spec->slug); ?>>
                            <?php echo esc_html($spec->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Фильтр по городу -->
            <div class="filter-group" style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Город</label>
                <select name="city" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                    <option value="">Все города</option>
                    <?php
                    $cities = get_terms(['taxonomy' => 'city', 'hide_empty' => false]);
                    foreach ($cities as $city) : ?>
                        <option value="<?php echo esc_attr($city->slug); ?>" <?php selected($_GET['city'] ?? '', $city->slug); ?>>
                            <?php echo esc_html($city->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Сортировка -->
            <div class="filter-group" style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Сортировать по</label>
                <select name="sort" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                    <option value="">По умолчанию</option>
                    <option value="rating_desc" <?php selected($_GET['sort'] ?? '', 'rating_desc'); ?>>Рейтингу (убыв.)</option>
                    <option value="price_asc" <?php selected($_GET['sort'] ?? '', 'price_asc'); ?>>Цене (возр.)</option>
                    <option value="exp_desc" <?php selected($_GET['sort'] ?? '', 'exp_desc'); ?>>Стажу (убыв.)</option>
                </select>
            </div>

            <div class="filter-actions" style="display: flex; gap: 10px;">
                <button type="submit" style="background: #0073aa; color: #fff; border: none; padding: 11px 25px; cursor: pointer; border-radius: 6px; font-weight: bold;">Применить</button>
                <a href="<?php echo esc_url(get_post_type_archive_link('doctors')); ?>" style="text-decoration: none; padding: 10px; color: #666; font-size: 14px; align-self: center;">Сбросить</a>
            </div>
        </form>
    </section>

    <!-- ТРЕБОВАНИЕ 4: Сетка карточек врачей -->
    <div class="doctors-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); 
                // Получаем мета-данные ACF (Requirement 4)
                $experience = get_field('experience');
                $price      = get_field('price_from');
                $rating     = get_field('rating');
                $specialization_list = get_the_term_list(get_the_ID(), 'specialization', '', ', ');
            ?>
                <article class="doctor-card" style="background: #fff; border: 1px solid #eee; border-radius: 15px; overflow: hidden; transition: transform 0.2s; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                    <div class="doctor-thumb" style="height: 250px; background: #f0f0f0;">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large', ['style' => 'width: 100%; height: 100%; object-fit: cover;']); ?>
                        <?php else: ?>
                            <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc;">Нет фото</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="doctor-content" style="padding: 20px; flex-grow: 1;">
                        <h3 style="margin: 0 0 10px; font-size: 1.25rem;">
                            <a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;"><?php the_title(); ?></a>
                        </h3>
                        <div style="font-size: 0.9rem; color: #0073aa; margin-bottom: 15px; font-weight: 500;">
                            <?php echo $specialization_list ?: 'Общий профиль'; ?>
                        </div>
                        <div class="doctor-stats" style="font-size: 0.95rem; color: #555;">
                            <div style="margin-bottom: 5px;">📅 Стаж: <strong><?php echo esc_html($experience); ?> лет</strong></div>
                            <div style="margin-bottom: 5px;">💰 Цена: <strong>от <?php echo esc_html($price); ?> ₽</strong></div>
                            <div style="margin-bottom: 5px;">⭐ Рейтинг: <strong><?php echo esc_html($rating); ?> / 5</strong></div>
                        </div>
                    </div>
                    
                    <div style="padding: 20px; border-top: 1px solid #f5f5f5;">
                        <a href="<?php the_permalink(); ?>" style="display: block; text-align: center; background: #f0f7ff; color: #0073aa; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.2s;">Подробнее</a>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: #f9f9f9; border-radius: 12px;">
                <h3>Врачи не найдены</h3>
                <p>Попробуйте изменить параметры фильтрации.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ПАГИНАЦИЯ (Requirement 4) -->
    <div class="pagination" style="margin-top: 50px; text-align: center;">
        <?php 
            echo paginate_links([
                'prev_text' => '« Назад',
                'next_text' => 'Вперед »',
                'type'      => 'plain',
            ]); 
        ?>
    </div>
</main>

<?php get_footer(); ?>