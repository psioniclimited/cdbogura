var init_select = function(parameters){
    var placeholder_text = 'Enter ';
    parameters.selector_id.select2({
        allowClear: true,
        placeholder: placeholder_text + parameters.placeholder,
        ajax: {
            dataType: 'json',
            url: parameters.url,
            delay: 250,
            data: function(params) {
                return {
                    term: params.term,
                    value_term: parameters.value_id.val(),
                    page: params.page
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        }
    });
}  
