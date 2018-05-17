/**
 * 
 */
(function(mod) {
  if (typeof exports == "object" && typeof module == "object")
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd)
    define(["../../lib/codemirror"], mod);
  else 
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