function bmsortable(sort_id, table_id) {
    var $tbody = jQuery("#" + table_id + " tbody");
    
    $tbody.sortable({
        cursor: "move",
        stop: function() {
            var sortables = [];
            var $sortables = jQuery("#" + table_id + " .bmsortables");
            
            $sortables.each(function(i, e) {
                var id = bmfieldid( jQuery(e).attr("name") );
                sortables.push(id);
            });
            
            jQuery("#" + sort_id).val(sortables);
        }
    });
    
    //jQuery("#" + id + " tbody *").disableSelection();
}

function bmfieldid(element_name) {
    var regex = /\[(\w+)\]$/;
    var matches = element_name.match(regex);
    
    return matches[1];
}