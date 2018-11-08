define("ace/mode/primi_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"], function(require, exports, module) {

    "use strict";

    var oop = require("../lib/oop");
    var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;

    var PrimiHighlightRules = function() {

        this.$rules = {
            "start" : [
               {
                    token : "comment",
                    regex : /\/\/.*$/
                }, {
                    token : "constant.language",
                    regex : /\b(true|false|null)\b/
                }, {
                    token : "constant.numeric",
                    regex : /-?\d+(\.\d+)?/
                }, {
                    token : "string",
                    regex : /("(.|\n)*?"|\'(.|\n)*?\')/
                }, {
                    token : "string.regexp",
                    regex : /r("(.|\n)*?"|\'(.|\n)*?\')/
                }, {
                    token : "keyword.control",
                    regex : /\b(return|if|while|for|as|in|continue|break)\b/
                }, {
                    token : "keyword.storage",
                    regex : /\bfunction\b/
                }, {
                    token : "support.other",
                    regex : /{|}|;|\)|\(/
                }, {
                    token : "keyword.operator",
                    regex : /\.|\+|-|\*|\/|=|\+=|-=|\*=|\/=|==|!=|>=|<=|>|<|and|or/
                }
            ]
        };

    };

    oop.inherits(PrimiHighlightRules, TextHighlightRules);
    exports.PrimiHighlightRules = PrimiHighlightRules;

});

define("ace/mode/primi",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/primi_highlight_rules"], function(require, exports, module) {

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
