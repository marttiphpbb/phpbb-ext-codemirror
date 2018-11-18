(function(mod) {
    mod(CodeMirror, $);
})(function(CodeMirror, $) {
  "use strict";

  CodeMirror.commands.marttiphpbbSubmit = function(cm){
    $(cm.getInputField()).closest('form').find('input[type="submit"]').click();
  };
});
