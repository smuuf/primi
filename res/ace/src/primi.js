define(function(require, exports, module) {

    "use strict";

    var oop = require("../lib/oop");
    var TextMode = require("./text").Mode;
    var PrimiHighlightRules = require("./primi_highlight_rules").PrimiHighlightRules;

    var Mode = function() {
        this.HighlightRules = PrimiHighlightRules;
        this.$behaviour = this.$defaultBehaviour;
    };
    oop.inherits(Mode, TextMode);

    (function() {
        this.$id = "ace/mode/primi";
    }).call(Mode.prototype);

    exports.Mode = Mode;

});
