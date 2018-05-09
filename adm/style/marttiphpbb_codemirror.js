;(function($, window, document) {
	$('document').ready(function () {
        var $textarea = $('textarea[data-marttiphpbb-codemirror]')[0];
        if ($textarea){
            var config = $($textarea).data('marttiphpbb-codemirror');
            window.marttiphpbbCodeMirror = CodeMirror.fromTextArea($textarea, config); 
            var $form = $textarea.closest('form');
            var historyId = $($textarea).data('marttiphpbb-codemirror-history-id');
            if ($form && historyId){
                var history = localStorage.get('marttiphpbb_codemirror_history_'+historyId);
                if (history){
                    windowphpbbCodeMirror.setHistory(JSON.parse(history));
                }
                $form.submit(function(){   
                    history = window.marttiphpbbCodeMirror.getHistory();
                    localStorage.set('marttiphpbb_codemirror_history_'+historyId, JSON.stringify(history));
                });
            } 
        }
	});
})(jQuery, window, document);
