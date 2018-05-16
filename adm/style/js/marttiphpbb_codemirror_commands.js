/**
 * 
 */
(function(mod) {
  if (typeof exports == "object" && typeof module == "object")
    mod(require("../../lib/codemirror"), require("./searchcursor"), require("../dialog/dialog"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror", "./searchcursor", "../dialog/dialog"], mod);
  else 
    mod(CodeMirror);
})(function(CodeMirror) {
  "use strict";

  CodeMirror.commands.marttiphpbbToggleFullScreen = function(cm){
    cm.setOption('fullScreen', !cm.getOption('fullScreen'));
  };

  CodeMirror.commands.marttiphpbbExitFullScreen = function(cm){
    cm.setOption('fullScreen', false);
  };
});