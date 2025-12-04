<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get Taxonomies for Dropdowns
$cidades = get_terms( array(
    'taxonomy' => 'cidade',
    'hide_empty' => false,
) );

$bairros = get_terms( array(
    'taxonomy' => 'bairro',
    'hide_empty' => false,
) );

$tipos_imovel = get_terms( array(
    'taxonomy' => 'tipo_imovel',
    'hide_empty' => false,
) );

// Pretensoes (Buy/Rent)
$pretensoes = get_terms( array(
    'taxonomy' => 'pretensao',
    'hide_empty' => false,
) );

?>

<div id="apaf-filter-wrapper">

    <!-- STICKY BAR (V10 Design) -->
    <div class="apaf-sticky-bar">
        <div class="apaf-sticky-container">

            <!-- Toggle (Comprar/Alugar) -->
            <div class="apaf-toggle-group">
                <select id="apaf-sticky-pretensao" class="apaf-select2-basic">
                    <option value="">Comprar / Alugar</option>
                    <?php if ( ! is_wp_error( $pretensoes ) && ! empty( $pretensoes ) ) : ?>
                        <?php foreach ( $pretensoes as $term ) : ?>
                            <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- City -->
            <div class="apaf-sticky-field">
                 <select id="apaf-sticky-cidade" class="apaf-select2">
                    <option value="">Cidade</option>
                    <?php if ( ! is_wp_error( $cidades ) && ! empty( $cidades ) ) : ?>
                        <?php foreach ( $cidades as $term ) : ?>
                            <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Neighborhood -->
            <div class="apaf-sticky-field">
                 <select id="apaf-sticky-bairro" class="apaf-select2">
                    <option value="">Bairro</option>
                    <?php if ( ! is_wp_error( $bairros ) && ! empty( $bairros ) ) : ?>
                        <?php foreach ( $bairros as $term ) : ?>
                            <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Filtros Avançados Link -->
            <div class="apaf-advanced-link">
                <a href="#" id="apaf-open-modal">Filtros Avançados</a>
            </div>

            <!-- Search Button -->
            <div class="apaf-search-btn-wrapper">
                <button type="button" id="apaf-sticky-search-btn">Buscar</button>
            </div>
        </div>
    </div>

    <!-- ADVANCED MODAL (V11 Design) -->
    <div id="apaf-modal" class="apaf-modal">
        <div class="apaf-modal-content">
            <span class="apaf-close-modal">&times;</span>

            <!-- Header -->
            <div class="apaf-modal-header">
                <h2>Filtros</h2>
                <p>Selecione as opções que desejar e clique em buscar</p>
            </div>

            <form id="apaf-advanced-form">

                <!-- Row 1: Location -->
                <div class="apaf-row apaf-row-location">
                    <div class="apaf-col">
                        <label>Cidade</label>
                        <select name="cidade" id="apaf-modal-cidade" class="apaf-select2-modal" style="width: 100%;">
                            <option value="">Pesquise pela cidade</option>
                             <?php if ( ! is_wp_error( $cidades ) && ! empty( $cidades ) ) : ?>
                                <?php foreach ( $cidades as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="apaf-col">
                        <label>Bairro</label>
                        <select name="bairro" id="apaf-modal-bairro" class="apaf-select2-modal" style="width: 100%;">
                            <option value="">Pesquise pelo bairro</option>
                             <?php if ( ! is_wp_error( $bairros ) && ! empty( $bairros ) ) : ?>
                                <?php foreach ( $bairros as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="apaf-col">
                        <label>Rua</label>
                        <input type="text" name="rua" id="apaf-modal-rua" placeholder="Pesquise pela rua">
                    </div>
                </div>

                <!-- Row 2: Property Type -->
                <div class="apaf-row apaf-row-type">
                    <label class="apaf-section-label">Tipo do imóvel</label>
                    <div class="apaf-checkbox-grid">
                        <?php if ( ! is_wp_error( $tipos_imovel ) && ! empty( $tipos_imovel ) ) : ?>
                            <?php foreach ( $tipos_imovel as $term ) : ?>
                                <label class="apaf-checkbox-label">
                                    <input type="checkbox" name="tipo_imovel[]" value="<?php echo esc_attr( $term->slug ); ?>">
                                    <span class="apaf-custom-checkbox"><?php echo esc_html( $term->name ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Row 3: Price -->
                <div class="apaf-row apaf-row-price">
                    <label class="apaf-section-label">Valor do imóvel</label>
                    <div class="apaf-price-slider-container">
                        <div id="apaf-price-slider"></div>
                        <div class="apaf-price-inputs">
                            <input type="text" id="apaf-price-min" name="price_min" readonly>
                            <input type="text" id="apaf-price-max" name="price_max" readonly>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Bedrooms -->
                <div class="apaf-row apaf-row-bedrooms">
                    <label class="apaf-section-label">Dormitórios</label>
                    <div class="apaf-number-buttons" data-input="quartos">
                        <input type="hidden" name="quartos" value="0">
                        <?php for($i=0; $i<=10; $i++): ?>
                            <button type="button" class="apaf-num-btn <?php echo ($i===0) ? 'active' : ''; ?>" data-value="<?php echo $i; ?>">
                                <?php echo ($i===10) ? '10+' : $i; ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Row 5: Bathrooms -->
                <div class="apaf-row apaf-row-bathrooms">
                    <label class="apaf-section-label">Quantidade de Banheiros</label>
                    <div class="apaf-number-buttons" data-input="banheiros">
                         <input type="hidden" name="banheiros" value="0">
                        <?php for($i=0; $i<=10; $i++): ?>
                            <button type="button" class="apaf-num-btn <?php echo ($i===0) ? 'active' : ''; ?>" data-value="<?php echo $i; ?>">
                                <?php echo ($i===10) ? '10+' : $i; ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Row 6: Garages -->
                <div class="apaf-row apaf-row-garages">
                    <label class="apaf-section-label">Quantidade de vagas na garagem</label>
                    <div class="apaf-number-buttons" data-input="vagas_de_garagem">
                         <input type="hidden" name="vagas_de_garagem" value="0">
                        <?php for($i=0; $i<=10; $i++): ?>
                            <button type="button" class="apaf-num-btn <?php echo ($i===0) ? 'active' : ''; ?>" data-value="<?php echo $i; ?>">
                                <?php echo ($i===10) ? '10' : $i; // Requirement says 0...10, not 10+ for garages explicitly, but usually implies same pattern. I'll stick to 10 as per prompt "Row 6... [10]" ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="apaf-modal-actions">
                    <button type="button" id="apaf-modal-search-btn">Buscar Imóveis</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Results Container -->
    <div id="apaf-results">
        <!-- AJAX Results will appear here -->
    </div>

</div>
