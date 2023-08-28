<?php 

//--------------------------------------------------------
// Fungsi untuk mendapatkan ID item menu dalam shortcode
// penjelasan : gunakan fungsi ini untuk mendapatkan id dari setiap item dalam menu
// 'menu_id' => 4, // masukan id menu sesuai dengan id menu anda
//--------------------------------------------------------


function get_menu_items_shortcode($atts) {
    // Mendapatkan atribut yang diberikan dalam shortcode, jika ada
    $atts = shortcode_atts(array(
        'menu_id' => 4, // ID menu default
    ), $atts, 'menu_items');

    // Mendapatkan item menu berdasarkan ID menu
    $menu_items = wp_get_nav_menu_items($atts['menu_id']);
    $menu = wp_get_nav_menu_object($atts['menu_id']);

    $output = '';

    // Tampilkan nama menu
    if ($menu) {
        $output .= 'Nama Menu: ' . $menu->name . '<br>';
    }

    // Loop melalui item menu untuk mendapatkan ID dan nama masing-masing
    if ($menu_items) {
        foreach ($menu_items as $menu_item) {
            $menu_item_id = $menu_item->ID;
            $menu_item_name = $menu_item->title;
            // Menggabungkan ID dan nama item menu ke dalam output
            $output .= 'ID Item Menu: ' . $menu_item_id . ', Nama Item Menu: ' . $menu_item_name . '<br>';
        }
    }

    return $output;
}

// Menambahkan shortcode untuk mendapatkan ID item menu
add_shortcode('menu_items', 'get_menu_items_shortcode');


//--------------------------------------------------------
// Fungsi untuk menambahkan custom post ke menu nav
// penjelasan : post akan otomatis tertambah ke menu
// 'menu_id' => 4, // masukan id menu sesuai dengan id menu anda
//--------------------------------------------------------


// Fungsi untuk menambahkan post dari custom post type ke menu navbar
function tambahkan_custom_post_ke_navbar($post_id) {
    // Periksa apakah post telah diterbitkan dan termasuk dalam custom post type "layanan"
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) || get_post_status($post_id) !== 'publish' || get_post_type($post_id) !== 'layanan') {
        return;
    }
    
    // Dapatkan data post yang baru ditambahkan
    $post = get_post($post_id);
    
    // Ganti 'nama-menu-navbar' dengan ID menu
    $menu_id = 4; // Ganti 4 dengan ID menu yang sesuai
    
    // Dapatkan semua item menu dalam menu dengan ID yang ditentukan
    $menu_items = wp_get_nav_menu_items($menu_id);

    //dapatkan URL dari post 
    $post_url = get_permalink($post_id);
    
    // Periksa apakah item menu dengan ID yang sama sudah ada dalam menu
    $item_exists = false;
    foreach ($menu_items as $menu_item) {
        if ($menu_item->url == $post_url) {
            $item_exists = true;
            break;
        }
    }
    
    // Jika item menu dengan ID yang sama belum ada, tambahkan item menu baru
    if (!$item_exists) {
        // Buat data menu untuk post
        $menu_data = array(
            'menu-item-title' => $post->post_title,
            'menu-item-url' => get_permalink($post_id),
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => 460, // Ganti 123 dengan ID parent menu yang sesuai
        );
        
        // Tambahkan menu item ke menu navbar
        wp_update_nav_menu_item($menu_id, 0, $menu_data);
    }
}

// Tambahkan hook untuk menjalankan fungsi tambahkan_custom_post_ke_navbar setelah post custom ditambahkan
add_action('wp_insert_post', 'tambahkan_custom_post_ke_navbar');



//------------------------------------------------------------------------
// Fungsi untuk menghapus menu nav
// penjelasan : jika post di hapus maka menu akan terhapus secara otomatis
// 'menu_id' => 4, // masukan id menu sesuai dengan id menu anda
//-------------------------------------------------------------------------

function hapus_menu_post_terkait($post_id) {
    // Periksa apakah post termasuk dalam custom post type "layanan"
    if (get_post_type($post_id) === 'layanan') {
        // Ganti 'nama-menu-navbar' dengan ID menu
        $menu_id = 4; // Ganti 4 dengan ID menu yang sesuai

        // Dapatkan semua item menu dalam menu dengan ID yang ditentukan
        $menu_items = wp_get_nav_menu_items($menu_id);

        // Dapatkan URL post yang dihapus
        $url_post_dihapus = get_permalink($post_id);

        // Loop melalui semua item menu
        foreach ($menu_items as $menu_item) {
            // Dapatkan URL item menu
            $url_item_menu = $menu_item->url;

            // Periksa kesamaan URL
            if ($url_item_menu === $url_post_dihapus) {
                // Hapus item menu dari menu
                wp_delete_post($menu_item->ID);
            }
        }
    }
}

// Tambahkan hook untuk menjalankan fungsi hapus_menu_post_terkait saat post dihapus
add_action('wp_trash_post', 'hapus_menu_post_terkait');
add_action('before_delete_post', 'hapus_menu_post_terkait');


















 ?>