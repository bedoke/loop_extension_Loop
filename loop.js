$(document).ready(function() {

var wgStylePath  = mw.config.get( 'wgStylePath' );
var jstreeSkinPath = wgStylePath + '/loop/jstree/themes/';
$.jstree._themes = jstreeSkinPath;

$("#sidebartoc").jstree({
    "core" : {
    	"load_open" : true,
    	"open_parents" : true,
    	"animation" : 200
    },
    "themes" : {
                "theme" : "default",
                "dots" : true,
                "icons" : true
            },
    "plugins" : [ "themes", "html_data", "cookies"]
});



$("#speciallooptoc").bind("loaded.jstree", function (e, data) { 
    data.inst.open_all(-1); // -1 opens all nodes in the container 
}).jstree({
    "core" : {
    	"load_open" : true,
    	"open_parents" : true,
    	"animation" : 200
    },
    "themes" : {
                "theme" : "default",
                "dots" : true,
                "icons" : true
            },
    "plugins" : [ "themes", "html_data", "cookies"]
});







} );



