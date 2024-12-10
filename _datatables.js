function define_custom_search_function_for_input_table_elements() {
    $.fn.dataTable.ext.order['dom-text-numeric'] = function (settings, col) {
        return this.api().column(col, {order: 'index'}).nodes().map(function (td, i) {
            return $('input', td).val() * 1; // Multiply by 1 to convert to numeric
        });
    };
}