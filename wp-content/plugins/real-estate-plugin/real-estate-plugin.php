<?php

/**
 * Plugin Name: Об'єкти нерухомості
 * Description: Плагін, що додає новий тип запису "Об'єкт нерухомості" та таксономію "Район".
 * Version: 1.0
 * Author: Volodjka
 */
// Ініціалізуємо новий тип запису "Об'єкт нерухомості"
?>
<style>
#real-estate-filter {
    margin-bottom: 20px;
}

#real-estate-filter label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

#real-estate-filter input[type="text"],
#real-estate-filter select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 10px;
}

#real-estate-filter input[type="radio"] {
    margin-right: 5px;
}

#real-estate-filter button {
    padding: 8px 16px;
    background-color: #4caf50;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

#real-estate-filter button:hover {
    background-color: #45a049;
}
</style>
<?php
function custom_post_type()
{
    $labels = array(
        'name'               => 'Об\'єкти нерухомості',
        'singular_name'      => 'Об\'єкт нерухомості',
        'add_new'            => 'Додати новий',
        'add_new_item'       => 'Додати новий об\'єкт нерухомості',
        'edit_item'          => 'Редагувати об\'єкт нерухомості',
        'new_item'           => 'Новий об\'єкт нерухомості',
        'all_items'          => 'Всі об\'єкти нерухомості',
        'view_item'          => 'Переглянути об\'єкт нерухомості',
        'search_items'       => 'Шукати об\'єкти нерухомості',
        'not_found'          => 'Об\'єкти нерухомості не знайдено',
        'not_found_in_trash' => 'Об\'єкти нерухомості не знайдено у кошику',
        'menu_name'          => 'Об\'єкти нерухомості'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'query_var'           => true,
        'rewrite'             => true,
        'capability_type'     => 'post',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'taxonomies'          => array('district'),
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-building',
    );

    register_post_type('real_estate', $args);
}
add_action('init', 'custom_post_type');

// Ініціалізуємо нову таксономію "Район"
function custom_taxonomy()
{
    $labels = array(
        'name'                       => 'Райони',
        'singular_name'              => 'Район',
        'search_items'               => 'Шукати райони',
        'popular_items'              => 'Популярні райони',
        'all_items'                  => 'Всі райони',
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => 'Редагувати район',
        'update_item'                => 'Оновити район',
        'add_new_item'               => 'Додати новий район',
        'new_item_name'              => 'Назва нового району',
        'separate_items_with_commas' => 'Відокремлюйте райони комами',
        'add_or_remove_items'        => 'Додати або видалити райони',
        'choose_from_most_used'      => 'Вибрати з найбільш популярних районів',
        'menu_name'                  => 'Райони',
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'district'),
    );

    register_taxonomy('district', 'real_estate', $args);
}
add_action('init', 'custom_taxonomy');

function enqueue_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('real-estate-ajax', plugin_dir_url(__FILE__) . 'real-estate-ajax.js', array('jquery'), '1.0', true);
    wp_localize_script('real-estate-ajax', 'realEstateAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');


// Шорткод для відображення блоку фільтра на фронтенді
function real_estate_filter_shortcode()
{
    ob_start();
?>
<div id="real-estate-filter">
    <label for="district-filter">Район:</label>
    <input type="text" id="district-filter" name="district" placeholder="Введіть назву району">

    <label for="house-name-filter">Назва будинку:</label>
    <input type="text" id="house-name-filter" name="house_name" placeholder="Введіть назву будинку">

    <label for="coordinates-filter">Координати:</label>
    <input type="text" id="coordinates-filter" name="coordinates" placeholder="Введіть координати">

    <label for="floor-count-filter">Кількість поверхів:</label>
    <select id="floor-count-filter" name="floor_count">
        <option value="">Виберіть кількість поверхів</option>
        <?php for ($i = 1; $i <= 20; $i++) : ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>

    <label>Тип будівлі:</label>
    <input type="radio" id="building-type-filter1" name="building_type" value="residential">
    <label for="building-type-filter1">панель</label>

    <input type="radio" id="building-type-filter2" name="building_type" value="commercial">
    <label for="building-type-filter2">цегла</label>

    <input type="radio" id="building-type-filter2" name="building_type" value="commercial">
    <label for="building-type-filter2">піноблок</label>

    <!-- Додайте інші типи будівель за необхідністю -->

    <button id="filter-button">Фільтрувати</button>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('real_estate_filter', 'real_estate_filter_shortcode');


// Віджет для відображення блоку фільтра на фронтенді
class RealEstateFilterWidget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'real_estate_filter_widget',
            'description' => 'Блок фільтра для об\'єктів нерухомості',
        );
        parent::__construct('real_estate_filter_widget', 'Фільтр нерухомості', $widget_ops);
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo do_shortcode('[real_estate_filter]');
        echo $args['after_widget'];
    }
}
function register_real_estate_filter_widget()
{
    register_widget('RealEstateFilterWidget');
}
add_action('widgets_init', 'register_real_estate_filter_widget');

// Ajax обробник для пошуку об'єктів нерухомості
function real_estate_search_ajax_handler()
{
    $args = array(
        'post_type'      => 'real_estate',
        'posts_per_page' => 3,
        'paged'          => $_POST['page'],
    );

    // Додайте будь-які додаткові параметри для пошуку, використовуючи $_POST['filter']
    if (isset($_POST['filter'])) {
        $meta_query = array();

        // Фільтр по house_name
        if (!empty($_POST['filter']['house_name'])) {
            $meta_query[] = array(
                'key'     => 'house_name',
                'value'   => $_POST['filter']['house_name'],
                'compare' => 'LIKE',
            );
        }

        // Фільтр по coordinates
        if (!empty($_POST['filter']['coordinates'])) {
            $meta_query[] = array(
                'key'     => 'coordinates',
                'value'   => $_POST['filter']['coordinates'],
                'compare' => 'LIKE',
            );
        }

        // Фільтр по floor_count
        if (!empty($_POST['filter']['floor_count'])) {
            $meta_query[] = array(
                'key'     => 'floor_count',
                'value'   => $_POST['filter']['floor_count'],
                'compare' => '=',
            );
        }

        // Фільтр по building_type
        if (!empty($_POST['filter']['building_type'])) {
            $meta_query[] = array(
                'key'     => 'building_type',
                'value'   => $_POST['filter']['building_type'],
                'compare' => '=',
            );
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Виведіть необхідну інформацію про об'єкт нерухомості (назва району, атрибути і т.д.)
            echo '<h2>' . get_the_title() . '</h2>';

            // Отримати значення поля house_name за допомогою ACF
            $house_name = get_field('house_name');
            echo '<p>House Name: ' . $house_name . '</p>';

            // Отримати значення поля coordinates за допомогою ACF
            $coordinates = get_field('coordinates');
            echo '<p>Coordinates: ' . $coordinates . '</p>';

            // Отримати значення поля floor_count за допомогою ACF
            $floor_count = get_field('floor_count');
            echo '<p>Floor Count: ' . $floor_count . '</p>';

            // Отримати значення поля building_type за допомогою ACF
            $building_type = get_field('building_type');
            echo '<p>Building Type: ' . $building_type . '</p>';
        }
    } else {
        echo '<p>Об\'єкти нерухомості не знайдені.</p>';
    }

    wp_reset_postdata();

    die();
}



add_action('wp_ajax_real_estate_search', 'real_estate_search_ajax_handler');
add_action('wp_ajax_nopriv_real_estate_search', 'real_estate_search_ajax_handler');
add_filter('widget_text', 'do_shortcode');