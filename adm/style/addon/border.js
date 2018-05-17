(function(mod) {
    mod(CodeMirror, $);
})(function(CodeMirror, $) {
  "use strict";

  CodeMirror.defineOption("marttiphpbbBorderEnabled", false, function(cm, val, old) {
    if (old == CodeMirror.Init){
      old = false;      
    }
    if (!old == !val){
      return;
    }
    var wrapper = cm.getWrapperElement();
    if (val) { 
      $(wrapper).css('border', cm.getOption('marttiphpbbBorderStyle'));
    } else {
      $(wrapper).css('border', '');
    }
  });

  CodeMirror.defineOption("marttiphpbbBorderStyle", "1px solid lightgrey");  

  CodeMirror.commands.marttiphpbbToggleBorder = function(cm){
    cm.setOption('marttiphpbbBorderEnabled', !cm.getOption('marttiphpbbBorderEnabled'));
  };
  CodeMirror.commands.marttiphpbbDisableBorder = function(cm){
    cm.setOption('marttiphpbbBorderEnabled', false);
  };
  CodeMirror.commands.marttiphpbbEnableBorder = function(cm){
    cm.setOption('marttiphpbbBorderEnabled', true);
  };

});