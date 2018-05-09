;(function($, window, document) {
	$('document').ready(function () {
        var $textarea = $('textarea[data-marttiphpbb-codemirror]')[0];
        if ($textarea){
            var config = $($textarea).data('marttiphpbb-codemirror');
            window.marttiphpbbCodeMirror = CodeMirror.fromTextArea($textarea, config); 
            var $form = $textarea.closest('form');
            var historyId = $($textarea).data('marttiphpbb-codemirror-history-id');
            if ($form && historyId){
                String.prototype.marttiphpbbCodeMirrorHash = function(){
                    var i, l, h = 0x49e335be;
                    for (i = 0, l = this.length; i < l; i++) {
                        h ^= this.charCodeAt(i);
                        h += (h << 1) + (h << 4) + (h << 7) + (h << 8) + (h << 24);
                    }
                    return ('0000000' + (h >>> 0).toString(16)).substr(-8);
                }
                console.log($($textarea).val());
                console.log($($textarea).val().marttiphpbbCodeMirrorHash());

                var history = sessionStorage.getItem('marttiphpbb_codemirror_history_'+historyId);
                if (history){
                    windowphpbbCodeMirror.setHistory(JSON.parse(history));
                }
                $($form).submit(function(){   
                    history = window.marttiphpbbCodeMirror.getHistory();
                    sessionStorage.setItem('marttiphpbb_codemirror_history_'+historyId, JSON.stringify(history));
                });
            } 
        }
	});
})(jQuery, window, document);
