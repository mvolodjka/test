<?php
get_header();

// Отримання значень полів
$house_name = get_field('house_name');
$coordinates = get_field('coordinates');
$floor_count = get_field('floor_count');
$building_type = get_field('building_type');
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php echo esc_html($house_name); ?></h1>
            </header>

            <div class="entry-content">
                <p>Координати: <?php echo esc_html($coordinates); ?></p>
                <p>Кількість поверхів: <?php echo esc_html($floor_count); ?></p>
                <p>Тип будівлі: <?php echo esc_html($building_type); ?></p>

                <?php the_content(); ?>
            </div>
        </article>
    </main>
</div>
<?php get_footer();