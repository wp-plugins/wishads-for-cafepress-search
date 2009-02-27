/**
 * Handle: wpCPGridAdmin
 * Version: 0.0.1
 * Deps: jquery
 * Enqueue: true
 */

var wpCPGridAdmin = function () {}

wpCPGridAdmin.prototype = {
    options           : {},
    generateShortCode : function() {
        var content = this['options']['content'];
        delete this['options']['content'];

        var attrs = '';
		var content = document.getElementById('wpCPGrid_search').value;
		var prodtypes = document.getElementById('wpCPGrid_prodtypes').value;
			if (prodtypes != '') {
                attrs += ' prodtypes="' + prodtypes + '"';
            }
		var returnnum = document.getElementById('wpCPGrid_return').value;
			if (returnnum != '') {
                attrs += ' return="' + returnnum + '"';
            }
		var previewnum = document.getElementById('wpCPGrid_preview').value;
			if (returnnum != '') {
                attrs += ' preview="' + previewnum + '"';
            }
		return '[cpgrid' + attrs + ']' + content + '[/cpgrid]'
    },
    sendToEditor      : function(f) {
        var collection = jQuery(f).find("input[id^=wpCPGridName]:not(input:checkbox),input[id^=wpCPGridName]:checkbox:checked");
        var $this = this;
        collection.each(function () {
            var name = this.name.substring(13, this.name.length-1);
            $this['options'][name] = this.value;
        });
        send_to_editor(this.generateShortCode());
        return false;
    }
}

var this_wpCPGridAdmin = new wpCPGridAdmin();