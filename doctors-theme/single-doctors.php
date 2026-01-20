<?php
/**
 * The template for displaying single "doctors" posts.
 * Layout: Image Left / Info & Description Right (Aligned Top)
 */

get_header(); ?>

<main id="primary" class="site-main container" style="max-width: 1100px; margin: 50px auto; padding: 0 20px;">
    <?php
    while (have_posts()) :
        the_post();

        // Данные ACF и таксономии
        $experience = get_field('experience');
        $price      = get_field('price_from');
        $rating     = get_field('rating');
        $specializations = get_the_term_list(get_the_ID(), 'specialization', '', ', ');
        $cities          = get_the_term_list(get_the_ID(), 'city', '', ', ');
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <!-- Заголовок имени по центру -->
            <header class="entry-header" style="margin-bottom: 40px; text-align: center;">
                <?php the_title('<h1 class="entry-title" style="font-size: 2.8rem; margin: 0; color: #333;">', '</h1>'); ?>
            </header>

            <!-- Основной контейнер: Фото слева, Весь текст справа -->
            <div class="doctor-main-flex" style="display: flex; gap: 40px; align-items: flex-start; justify-content: center; flex-wrap: wrap;">
                
                <!-- Левая колонка: Фото (фиксированная ширина) -->
                <div class="doctor-photo-col" style="flex: 0 0 350px; max-width: 350px;">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large', [
                            'style' => 'width: 100%; height: auto; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); display: block;'
                        ]); ?>
                    <?php endif; ?>
                </div>

                <!-- Правая колонка: Информация + Описание (начинаются с самого верха) -->
                <div class="doctor-content-col" style="flex: 1; min-width: 300px; margin-top: 0;">
                    
                    <!-- Блок характеристик (Requirement 3) -->
                    <div class="info-details-box" style="background: #d3d9e3; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; margin-bottom: 25px;">
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 1.1rem; line-height: 1.8;">
                            <li><strong style="color: #666;">Специализация:</strong> <?php echo $specializations ?: '—'; ?></li>
                            <li><strong style="color: #666;">Город:</strong> <?php echo $cities ?: '—'; ?></li>
                            <li><strong style="color: #666;">Стаж:</strong> <?php echo esc_html($experience); ?> лет</li>
                            <li><strong style="color: #666;">Стоимость:</strong> от <?php echo esc_html($price); ?> руб.</li>
                            <li><strong style="color: #666;">Рейтинг:</strong> <span style="color: #f39c12;"><?php echo esc_html($rating); ?> / 5 ⭐</span></li>
                        </ul>
                    </div>

                    <!-- Текстовое описание (без заголовка, на одном уровне с фото) -->
                    <div class="entry-content" style="color: #444; font-size: 1.1rem; line-height: 1.7;">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

        </article>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
