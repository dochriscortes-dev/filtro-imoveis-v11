<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_apaf_filter_properties', 'apaf_filter_properties_handler' );
add_action( 'wp_ajax_nopriv_apaf_filter_properties', 'apaf_filter_properties_handler' );

function apaf_filter_properties_handler() {
    check_ajax_referer( 'apaf_filter_nonce', 'security' );

    $filters = isset( $_POST['filters'] ) ? $_POST['filters'] : array();

    // Base Args
    $args = array(
        'post_type'      => 'imovel',
        'post_status'    => 'publish',
        'posts_per_page' => -1, // Or paginate
        'tax_query'      => array( 'relation' => 'AND' ),
        'meta_query'     => array( 'relation' => 'AND' ),
    );

    // Taxonomies
    // 1. Pretensao
    if ( ! empty( $filters['pretensao'] ) ) {
        $args['tax_query'][] = array(
            'taxonomy' => 'pretensao',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $filters['pretensao'] ),
        );
    }

    // 2. Cidade
    if ( ! empty( $filters['cidade'] ) ) {
        $args['tax_query'][] = array(
            'taxonomy' => 'cidade',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $filters['cidade'] ),
        );
    }

    // 3. Bairro
    if ( ! empty( $filters['bairro'] ) ) {
        $args['tax_query'][] = array(
            'taxonomy' => 'bairro',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $filters['bairro'] ),
        );
    }

    // 4. Tipo do Imovel (Array)
    if ( ! empty( $filters['tipo_imovel'] ) && is_array( $filters['tipo_imovel'] ) ) {
        $args['tax_query'][] = array(
            'taxonomy' => 'tipo_imovel',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_text_field', $filters['tipo_imovel'] ),
        );
    }


    // Meta Query

    // 1. Rua (LIKE comparison)
    if ( ! empty( $filters['rua'] ) ) {
        $args['meta_query'][] = array(
            'key'     => 'rua',
            'value'   => sanitize_text_field( $filters['rua'] ),
            'compare' => 'LIKE',
        );
    }

    // 2. Price (Range)
    $min_price = isset( $filters['price_min'] ) ? intval( $filters['price_min'] ) : 0;
    $max_price = isset( $filters['price_max'] ) ? intval( $filters['price_max'] ) : 0;

    // Only filter if not default full range (optional, but good practice).
    // Defaults were 20k to 1m.
    if ( $min_price > 0 || $max_price > 0 ) {
         // Assuming 'preco_venda' stores numeric value
         $args['meta_query'][] = array(
            'key'     => 'preco_venda',
            'value'   => array( $min_price, $max_price ),
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
    }

    // 3. Quartos
    // Logic: "0" implies "Any". Buttons 0-10+
    $quartos = isset( $filters['quartos'] ) ? intval( $filters['quartos'] ) : 0;
    if ( $quartos > 0 ) {
        if ( $quartos >= 10 ) {
            $args['meta_query'][] = array(
                'key'     => 'quartos',
                'value'   => 10,
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        } else {
            $args['meta_query'][] = array(
                'key'     => 'quartos',
                'value'   => $quartos,
                'type'    => 'NUMERIC',
                'compare' => '=', // Precise match or >=? Requirement says [0] [1]... usually precise.
                              // But if I select 2, do I want exactly 2? Yes.
            );
        }
    }

    // 4. Banheiros
    $banheiros = isset( $filters['banheiros'] ) ? intval( $filters['banheiros'] ) : 0;
    if ( $banheiros > 0 ) {
         if ( $banheiros >= 10 ) {
            $args['meta_query'][] = array(
                'key'     => 'banheiros',
                'value'   => 10,
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        } else {
            $args['meta_query'][] = array(
                'key'     => 'banheiros',
                'value'   => $banheiros,
                'type'    => 'NUMERIC',
                'compare' => '=',
            );
        }
    }

    // 5. Vagas
    $vagas = isset( $filters['vagas_de_garagem'] ) ? intval( $filters['vagas_de_garagem'] ) : 0;
    if ( $vagas > 0 ) {
         // The requirement for Vagas row 6 was Buttons [0]...[10]. Not 10+.
         // Assuming same logic: 0 = any.
         $args['meta_query'][] = array(
            'key'     => 'vagas_de_garagem',
            'value'   => $vagas,
            'type'    => 'NUMERIC',
            'compare' => '=',
        );
    }

    // Aceita Financiamento (Not in UI requirements but listed in DB Mapping?
    // Requirement says "Keep previous working keys".
    // But UI didn't specify a field for it. I will leave it out of UI unless implicitly needed.
    // I won't add it to query if it's not in the filters.)

    // Run Query
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        ob_start();
        echo '<div class="apaf-results-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">';
        while ( $query->have_posts() ) {
            $query->the_post();

            // Basic Result Card Template (Placeholder)
            // In a real scenario, this would be a separate template part
            ?>
            <div class="apaf-card" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="apaf-card-img" style="margin-bottom: 10px;">
                        <?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:auto;' ) ); ?>
                    </div>
                <?php endif; ?>
                <h3 style="margin: 0 0 10px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p><strong>Preço:</strong> R$ <?php echo number_format( (float) get_post_meta( get_the_ID(), 'preco_venda', true ), 2, ',', '.' ); ?></p>
                <p>
                    <?php echo get_post_meta( get_the_ID(), 'quartos', true ); ?> Quartos |
                    <?php echo get_post_meta( get_the_ID(), 'banheiros', true ); ?> Banheiros |
                    <?php echo get_post_meta( get_the_ID(), 'vagas_de_garagem', true ); ?> Vagas
                </p>
                <p><?php echo get_the_term_list( get_the_ID(), 'bairro', '', ', ' ); ?>, <?php echo get_the_term_list( get_the_ID(), 'cidade', '', ', ' ); ?></p>
            </div>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();
        $html = ob_get_clean();
        wp_send_json_success( array( 'html' => $html ) );
    } else {
        wp_send_json_success( array( 'html' => '<p>Nenhum imóvel encontrado com os filtros selecionados.</p>' ) );
    }

    wp_die();
}
