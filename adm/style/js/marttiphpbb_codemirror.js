;(function($, window, document) {
	$('document').ready(function () {
        const storagePrefix = 'marttiphpb_codemirror_';
        var $textarea = $('textarea[data-marttiphpbb-codemirror]')[0];
        var content = $($textarea).val();
        if ($textarea){
            var data = $($textarea).data('marttiphpbb-codemirror');
            var codeMirror = CodeMirror.fromTextArea($textarea, data.config);           
            var $form = $textarea.closest('form');
            if ($form && data.historyId){
                function hash32(str){
                    var i, l, h = 0x49e335be;
                    for (i = 0, l = str.length; i < l; i++){
                        h ^= str.charCodeAt(i);
                        h += (h << 1) + (h << 4) + (h << 7) + (h << 8) + (h << 24);
                    }
                    return ('0000000' + (h >>> 0).toString(16)).substr(-8);
                }
                var hash = hash32($($textarea).val());
                console.log('marttiphpbb/codemirror hash: '+hash);
                var storageKey = storagePrefix+hash+'_'+data.historyId;
                var history = sessionStorage.getItem(storageKey);
                if (history){
                    codeMirror.setHistory(JSON.parse(history));
                }
                $($form).submit(function(){   
                    history = JSON.stringify(codeMirror.getHistory());
                    hash = hash32(codeMirror.getValue());
                    var newKey = storagePrefix+hash+'_'+historyId;
                    if (newKey !== storageKey){
                        sessionStorage.setItem(newKey, history);
//                        sessionStorage.removeItem(storageKey);
                    }
                });
            }    
            $('select[data-marttiphpbb-codemirror-theme]').change(function(){
                codeMirror.setOption('theme', $(this).find('option:selected').text());
            });
            $('select[data-marttiphpbb-codemirror-mode]').change(function(){
                codeMirror.setOption('mode', $(this).find('option:selected').text());
            });
            $('select[data-marttiphpbb-codemirror-keymap]').change(function(){
                codeMirror.setOption('keyMap', $(this).find('option:selected').text());
            });
            $('input[data-marttiphpbb-codemirror-border]').change(function(){
                if ($(this).val() == '1'){
                    codeMirror.setOption('marttiphpbbBorderEnabled', true);
                } else {
                    codeMirror.setOption('marttiphpbbBorderEnabled', false);
                }
            });
            $('#reset').click(function(){
                codeMirror.setValue(content);
            });
            if (data.defaultContent){
                $('#restore').click(function(){
                    codeMirror.setValue(data.defaultContent);
                }); 
            }
            window.marttiphpbbCodeMirror = codeMirror;
        }
	});
})(jQuery, window, document);
