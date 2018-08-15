(function(mod) {
    mod(CodeMirror);
})(function(CodeMirror) {
  "use strict";

  CodeMirror.commands.marttiphpbbToggleFullScreen = function(cm){
    cm.setOption('fullScreen', !cm.getOption('fullScreen'));
  };
  CodeMirror.commands.marttiphpbbEnableFullScreen = function(cm){
    cm.setOption('fullScreen', true);
  };
  CodeMirror.commands.marttiphpbbDisableFullScreen = function(cm){
    cm.setOption('fullScreen', false);
  };
});