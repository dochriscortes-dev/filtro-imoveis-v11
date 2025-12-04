jQuery(document).ready(function($) {

    // Initialize Select2 for Modal
    $('.apaf-select2-modal').select2({
        dropdownParent: $('#apaf-modal') // Fix for Select2 inside modal
    });

    // Initialize Select2 for Sticky Bar
    $('.apaf-select2').select2({
        width: '100%'
    });
    $('.apaf-select2-basic').select2({
        width: '100%',
        minimumResultsForSearch: -1 // Disable search for basic dropdowns
    });

    // Modal Toggle
    var modal = $('#apaf-modal');
    var openBtn = $('#apaf-open-modal');
    var closeSpan = $('.apaf-close-modal');

    openBtn.on('click', function(e) {
        e.preventDefault();
        modal.show();
    });

    closeSpan.on('click', function() {
        modal.hide();
    });

    $(window).on('click', function(e) {
        if ($(e.target).is(modal)) {
            modal.hide();
        }
    });

    // Price Slider
    var priceMin = 20000;
    var priceMax = 1000000;

    $("#apaf-price-slider").slider({
        range: true,
        min: 20000,
        max: 1000000,
        values: [20000, 1000000],
        step: 1000,
        slide: function(event, ui) {
            $("#apaf-price-min").val("R$ " + ui.values[0].toLocaleString('pt-BR'));
            $("#apaf-price-max").val("R$ " + ui.values[1].toLocaleString('pt-BR'));
        }
    });

    // Initial values
    $("#apaf-price-min").val("R$ " + $("#apaf-price-slider").slider("values", 0).toLocaleString('pt-BR'));
    $("#apaf-price-max").val("R$ " + $("#apaf-price-slider").slider("values", 1).toLocaleString('pt-BR'));


    // Numeric Buttons Logic
    $('.apaf-num-btn').on('click', function() {
        var btn = $(this);
        var container = btn.parent();
        var inputName = container.data('input');
        var value = btn.data('value');

        // Update UI
        container.find('.apaf-num-btn').removeClass('active');
        btn.addClass('active');

        // Update Hidden Input
        container.find('input[name="' + inputName + '"]').val(value);
    });

    // AJAX Search Handler
    function performSearch(source) {
        var formData = {};

        if (source === 'sticky') {
            formData.pretensao = $('#apaf-sticky-pretensao').val();
            formData.cidade = $('#apaf-sticky-cidade').val();
            formData.bairro = $('#apaf-sticky-bairro').val();
            // Defaults for others
            formData.price_min = 20000;
            formData.price_max = 1000000;
            formData.quartos = 0;
            formData.banheiros = 0;
            formData.vagas_de_garagem = 0;
            formData.rua = '';
            formData.tipo_imovel = [];

        } else {
            // Modal
            // We need to sync sticky bar if modal changes? Or just take modal data?
            // Usually Advanced Modal overrides everything.

            // Get data from form
            // SerializeArray doesn't work well with our custom logic for prices/buttons if we just used that,
            // but we have inputs.
            // Let's gather manually to be precise.

            formData.cidade = $('#apaf-modal-cidade').val();
            formData.bairro = $('#apaf-modal-bairro').val();
            formData.rua = $('#apaf-modal-rua').val();

            // Checkboxes
            var tipos = [];
            $('input[name="tipo_imovel[]"]:checked').each(function() {
                tipos.push($(this).val());
            });
            formData.tipo_imovel = tipos;

            // Price
            formData.price_min = $("#apaf-price-slider").slider("values", 0);
            formData.price_max = $("#apaf-price-slider").slider("values", 1);

            // Numeric Buttons (Inputs are updated on click)
            formData.quartos = $('input[name="quartos"]').val();
            formData.banheiros = $('input[name="banheiros"]').val();
            formData.vagas_de_garagem = $('input[name="vagas_de_garagem"]').val();

            // Should we include pretensao in modal? The requirements didn't specify it in the modal layout,
            // but usually it's a primary filter. The sticky bar has it.
            // Requirement says "Row 1 (Location): 3 Columns". No mention of Pretensao.
            // Maybe it persists from Sticky Bar or is not filterable in Modal?
            // I'll grab it from the Sticky Bar as it's the global context usually.
            formData.pretensao = $('#apaf-sticky-pretensao').val();
        }

        // Send AJAX
        $.ajax({
            url: apaf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'apaf_filter_properties',
                security: apaf_ajax.nonce,
                filters: formData
            },
            beforeSend: function() {
                $('#apaf-results').html('<div style="text-align:center; padding:20px;">Carregando...</div>');
                if(source === 'modal') {
                     $('#apaf-modal').hide(); // Close modal on search
                }
            },
            success: function(response) {
                if (response.success) {
                    $('#apaf-results').html(response.data.html);
                } else {
                    $('#apaf-results').html('<div style="text-align:center; padding:20px;">' + response.data + '</div>');
                }
            },
            error: function() {
                 $('#apaf-results').html('<div style="text-align:center; padding:20px;">Erro ao buscar imóveis. Tente novamente.</div>');
            }
        });
    }

    // Trigger Search from Sticky Bar
    $('#apaf-sticky-search-btn').on('click', function() {
        performSearch('sticky');
    });

    // Trigger Search from Modal
    $('#apaf-modal-search-btn').on('click', function() {
        performSearch('modal');
    });

});
