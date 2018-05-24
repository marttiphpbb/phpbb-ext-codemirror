(function(mod) {
    mod(CodeMirror, $);
})(function(CodeMirror, $) {
  "use strict";

  CodeMirror.commands.marttiphpbbSubmit = function(cm){
    console.log('ofudqsfdsqf');
//    var wrapper = cm.getWrapperElement();
//    $(wrapper).closest('form').submit();
    $('input[type="submit"]').click();
  };
});
