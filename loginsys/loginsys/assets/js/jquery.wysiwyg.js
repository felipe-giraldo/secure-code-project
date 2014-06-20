/**
 * WYSIWYG â€“ jQuery plugin 0.98.dev
 *
 * Copyright Â© 2008â€“2009 Juan M Martinez, 2010â€“2013 Akzhan Abdulin and all contributors
 * https://github.com/jwysiwyg/jwysiwyg
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Release: 2013-05-14
 *
 */
/*jslint browser: true, forin: true, white: true */
(function(c){var a=window.console||{log:c.noop,error:function(e){c.error(e)}},b=(c.fn.prop!==undefined)&&(c.fn.removeProp!==undefined);function d(){this.controls={bold:{groupIndex:0,visible:true,tags:["b","strong"],css:{fontWeight:"bold"},tooltip:"Bold",hotkey:{ctrl:1,key:66}},copy:{groupIndex:8,visible:false,tooltip:"Copy"},createLink:{groupIndex:6,visible:true,exec:function(){var e=this;if(c.wysiwyg.controls&&c.wysiwyg.controls.link){c.wysiwyg.controls.link.init(this)}else{if(c.wysiwyg.autoload){c.wysiwyg.autoload.control("wysiwyg.link.js",function(){e.controls.createLink.exec.apply(e)})}else{a.error("$.wysiwyg.controls.link not defined. You need to include wysiwyg.link.js file")}}},tags:["a"],tooltip:"Create link"},unLink:{groupIndex:6,visible:true,exec:function(){this.editorDoc.execCommand("unlink",false,null)},tooltip:"Remove link"},cut:{groupIndex:8,visible:false,tooltip:"Cut"},decreaseFontSize:{groupIndex:9,visible:false,tags:["small"],tooltip:"Decrease font size",exec:function(){this.decreaseFontSize()}},h1:{groupIndex:7,visible:true,className:"h1",command:(c.browser.msie||c.browser.opera)?"FormatBlock":"heading","arguments":(c.browser.msie||c.browser.opera)?"<h1>":"h1",tags:["h1"],tooltip:"Header 1"},h2:{groupIndex:7,visible:true,className:"h2",command:(c.browser.msie||c.browser.opera)?"FormatBlock":"heading","arguments":(c.browser.msie||c.browser.opera)?"<h2>":"h2",tags:["h2"],tooltip:"Header 2"},h3:{groupIndex:7,visible:true,className:"h3",command:(c.browser.msie||c.browser.opera)?"FormatBlock":"heading","arguments":(c.browser.msie||c.browser.opera)?"<h3>":"h3",tags:["h3"],tooltip:"Header 3"},highlight:{tooltip:"Highlight",className:"highlight",groupIndex:1,visible:false,css:{backgroundColor:"rgb(255, 255, 102)"},exec:function(){var h,g,f,e;if(c.browser.msie||c.browser.opera){h="backcolor"}else{h="hilitecolor"}if(c.browser.msie){g=this.getInternalRange().parentElement()}else{f=this.getInternalSelection();g=f.extentNode||f.focusNode;while(g.style===undefined){g=g.parentNode;if(g.tagName&&g.tagName.toLowerCase()==="body"){return}}}if(g.style.backgroundColor==="rgb(255, 255, 102)"||g.style.backgroundColor==="#ffff66"){e="#ffffff"}else{e="#ffff66"}this.editorDoc.execCommand(h,false,e)}},html:{groupIndex:10,visible:false,exec:function(e,g){var f;if(this.options.resizeOptions&&c.fn.resizable){f=this.element.height()}if(this.viewHTML){this.setContent((typeof g==="function")?g(this.original.value):this.original.value);c(this.original).hide();this.editor.show();if(this.options.resizeOptions&&c.fn.resizable){if(f===this.element.height()){this.element.height(f+this.editor.height())}this.element.resizable(c.extend(true,{alsoResize:this.editor},this.options.resizeOptions))}this.ui.toolbar.find("li").each(function(){var h=c(this);if(h.hasClass("html")){h.removeClass("active")}else{h.removeClass("disabled")}})}else{this.saveContent(e);c(this.original).css({width:this.editor.width(),height:this.editor.height(),resize:"none"}).show();this.editor.hide();if(this.options.resizeOptions&&c.fn.resizable){if(f===this.element.height()){this.element.height(this.ui.toolbar.height())}this.element.resizable("destroy")}this.ui.toolbar.find("li").each(function(){var h=c(this);if(h.hasClass("html")){h.addClass("active")}else{if(false===h.hasClass("fullscreen")){h.removeClass("active").addClass("disabled")}}})}this.viewHTML=!(this.viewHTML)},tooltip:"View source code"},increaseFontSize:{groupIndex:9,visible:false,tags:["big"],tooltip:"Increase font size",exec:function(){this.increaseFontSize()}},indent:{groupIndex:2,visible:true,tooltip:"Indent"},insertHorizontalRule:{groupIndex:6,visible:true,tags:["hr"],tooltip:"Insert Horizontal Rule"},insertImage:{groupIndex:6,visible:true,exec:function(){var e=this;if(c.wysiwyg.controls&&c.wysiwyg.controls.image){c.wysiwyg.controls.image.init(this)}else{if(c.wysiwyg.autoload){c.wysiwyg.autoload.control("wysiwyg.image.js",function(){e.controls.insertImage.exec.apply(e)})}else{a.error("$.wysiwyg.controls.image not defined. You need to include wysiwyg.image.js file")}}},tags:["img"],tooltip:"Insert image"},insertOrderedList:{groupIndex:5,visible:true,tags:["ol"],tooltip:"Insert Ordered List"},insertTable:{groupIndex:6,visible:true,exec:function(){var e=this;if(c.wysiwyg.controls&&c.wysiwyg.controls.table){c.wysiwyg.controls.table(this)}else{if(c.wysiwyg.autoload){c.wysiwyg.autoload.control("wysiwyg.table.js",function(){e.controls.insertTable.exec.apply(e)})}else{a.error("$.wysiwyg.controls.table not defined. You need to include wysiwyg.table.js file")}}},tags:["table"],tooltip:"Insert table"},insertUnorderedList:{groupIndex:5,visible:true,tags:["ul"],tooltip:"Insert Unordered List"},italic:{groupIndex:0,visible:true,tags:["i","em"],css:{fontStyle:"italic"},tooltip:"Italic",hotkey:{ctrl:1,key:73}},justifyCenter:{groupIndex:1,visible:true,tags:["center"],css:{textAlign:"center"},tooltip:"Justify Center"},justifyFull:{groupIndex:1,visible:true,css:{textAlign:"justify"},tooltip:"Justify Full"},justifyLeft:{visible:true,groupIndex:1,css:{textAlign:"left"},tooltip:"Justify Left"},justifyRight:{groupIndex:1,visible:true,css:{textAlign:"right"},tooltip:"Justify Right"},ltr:{groupIndex:10,visible:false,exec:function(){var e=this.dom.getElement("p");if(!e){return false}c(e).attr("dir","ltr");return true},tooltip:"Left to Right"},outdent:{groupIndex:2,visible:true,tooltip:"Outdent"},paragraph:{groupIndex:7,visible:false,className:"paragraph",command:"FormatBlock","arguments":(c.browser.msie||c.browser.opera)?"<p>":"p",tags:["p"],tooltip:"Paragraph"},paste:{groupIndex:8,visible:false,tooltip:"Paste"},redo:{groupIndex:4,visible:true,tooltip:"Redo"},removeFormat:{groupIndex:10,visible:true,exec:function(){this.removeFormat()},tooltip:"Remove formatting"},rtl:{groupIndex:10,visible:false,exec:function(){var e=this.dom.getElement("p");if(!e){return false}c(e).attr("dir","rtl");return true},tooltip:"Right to Left"},strikeThrough:{groupIndex:0,visible:true,tags:["s","strike"],css:{textDecoration:"line-through"},tooltip:"Strike-through"},subscript:{groupIndex:3,visible:true,tags:["sub"],tooltip:"Subscript"},superscript:{groupIndex:3,visible:true,tags:["sup"],tooltip:"Superscript"},underline:{groupIndex:0,visible:true,tags:["u"],css:{textDecoration:"underline"},tooltip:"Underline",hotkey:{ctrl:1,key:85}},undo:{groupIndex:4,visible:true,tooltip:"Undo"},code:{visible:true,groupIndex:6,tooltip:"Code snippet",exec:function(){var e=this.getInternalRange(),f=c(e.commonAncestorContainer),g=e.commonAncestorContainer.nodeName.toLowerCase();if(f.parent("code").length){f.unwrap()}else{if(g!=="body"){f.wrap("<code/>")}}}},cssWrap:{visible:false,groupIndex:6,tooltip:"CSS Wrapper",exec:function(){c.wysiwyg.controls.cssWrap.init(this)}}};this.defaults={html:'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" style="margin:0"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body style="margin:0">INITIAL_CONTENT</body></html>',debug:false,controls:{},css:{},events:{},autoGrow:false,autoSave:true,brIE:false,formHeight:270,formWidth:440,iFrameClass:null,initialContent:"<p>Initial content</p>",maxHeight:10000,maxLength:0,messages:{nonSelection:"Select the text you wish to link"},toolbarHtml:'<ul role="menu" class="toolbar"></ul>',removeHeadings:false,replaceDivWithP:false,resizeOptions:false,rmUnusedControls:false,rmUnwantedBr:true,tableFiller:"Lorem ipsum",initialMinHeight:null,controlImage:{forceRelativeUrls:false},controlLink:{forceRelativeUrls:false},plugins:{autoload:false,i18n:false,rmFormat:{rmMsWordMarkup:false}},dialog:"default"};this.availableControlProperties=["arguments","callback","callbackArguments","className","command","css","custom","exec","groupIndex","hotkey","icon","separator","tags","tooltip","visible"];this.editor=null;this.editorDoc=null;this.element=null;this.options={};this.original=null;this.savedRange=null;this.timers=[];this.validKeyCodes=[8,9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45,46];this.isDestroyed=false;this.dom={ie:{parent:null},w3c:{parent:null}};this.dom.parent=this;this.dom.ie.parent=this.dom;this.dom.w3c.parent=this.dom;this.ui={};this.ui.self=this;this.ui.toolbar=null;this.ui.initialHeight=null;this.dom.getAncestor=function(e,f){f=f.toLowerCase();while(e&&e.tagName!==undefined&&"body"!==e.tagName.toLowerCase()){if(f===e.tagName.toLowerCase()){return e}e=e.parentNode}if(!e.tagName&&(e.previousSibling||e.nextSibling)){if(e.previousSibling){if(e.previousSibling.tagName.toLowerCase()===f){return e.previousSibling}}if(e.nextSibling){if(e.nextSibling.tagName.toLowerCase()===f){return e.nextSibling}}}return null};this.dom.getElement=function(f){var e=this;f=f.toLowerCase();return window.getSelection?e.w3c.getElement(f):e.ie.getElement(f)};this.dom.ie.getElement=function(i){var h=this.parent,g=h.parent.getInternalSelection(),e=g.createRange(),f;if("Control"===g.type){if(1===e.length){f=e.item(0)}else{return null}}else{f=e.parentElement()}return h.getAncestor(f,i)};this.dom.w3c.getElement=function(h){var g=this.parent,e=g.parent.getInternalRange(),f;if(!e){return null}f=e.commonAncestorContainer;if(3===f.nodeType){f=f.parentNode}if(f===e.startContainer){f=f.childNodes[e.startOffset]}if(!f.tagName&&(f.previousSibling||f.nextSibling)){if(f.previousSibling){if(f.previousSibling.tagName.toLowerCase()===h){return f.previousSibling}}if(f.nextSibling){if(f.nextSibling.tagName.toLowerCase()===h){return f.nextSibling}}}return g.getAncestor(f,h)};this.ui.addHoverClass=function(){c(this).addClass("wysiwyg-button-hover")};this.ui.appendControls=function(){var k=this,n=this.self,m=n.parseControls(),l=true,e=[],f={},g,h,j=function(o,i){if(i.groupIndex&&h!==i.groupIndex){h=i.groupIndex;l=false}if(!i.visible){return}if(!l){k.appendItemSeparator();l=true}if(i.custom){k.appendItemCustom(o,i)}else{k.appendItem(o,i)}};c.each(m,function(o,p){var i="empty";if(undefined!==p.groupIndex){if(""===p.groupIndex){i="empty"}else{i=p.groupIndex}}if(undefined===f[i]){e.push(i);f[i]={}}f[i][o]=p});e.sort(function(o,i){if((typeof o==="number")&&(typeof i==="number")){return(o-i)}o=o.toString();i=i.toString();if(o>i){return 1}if(o===i){return 0}return -1});if(0<e.length){h=e[0]}for(g=0;g<e.length;g+=1){c.each(f[e[g]],j)}};this.ui.appendItem=function(f,i){var e=this.self,g=i.className||i.command||f||"empty",h=i.tooltip||i.command||f||"";return c('<li role="menuitem" unselectable="on">'+g+"</li>").addClass(g).attr("title",h).hover(this.addHoverClass,this.removeHoverClass).click(function(k){if(c(this).hasClass("disabled")){return false}e.triggerControl(f,i);var j=c(k.target);for(var l in e.controls){if(j.hasClass(l)){e.ui.toolbar.find("."+l).toggleClass("active");e.editorDoc.rememberCommand=true;break}}this.blur();e.ui.returnRange();e.ui.focus();return true}).appendTo(e.ui.toolbar)};this.ui.appendItemCustom=function(f,h){var e=this.self,g=h.tooltip||h.command||f||"";if(h.callback){c(window).bind("trigger-"+f+".wysiwyg",h.callback)}return c('<li role="menuitem" unselectable="on" style="background: url(\''+h.icon+"') no-repeat;\"></li>").addClass("custom-command-"+f).addClass("wysiwyg-custom-command").addClass(f).attr("title",g).hover(this.addHoverClass,this.removeHoverClass).click(function(){if(c(this).hasClass("disabled")){return false}e.triggerControl.apply(e,[f,h]);this.blur();e.ui.returnRange();e.ui.focus();e.triggerControlCallback(f);return true}).appendTo(e.ui.toolbar)};this.ui.appendItemSeparator=function(){var e=this.self;return c('<li role="separator" class="separator"></li>').appendTo(e.ui.toolbar)};this.autoSaveFunction=function(){this.saveContent()};this.ui.checkTargets=function(f){var e=this.self;c.each(e.options.controls,function(h,l){var k=l.className||l.command||h||"empty",g,n,i,j,m=function(p){var q=0;var o=0;c.each(p,function(s,r){if("function"===typeof r){if(r.apply(e,[j.css(s).toString().toLowerCase(),e])){q+=1}}else{if(j.css(s).toString().toLowerCase()===r){q+=1}}o+=1});if(o===q){e.ui.toolbar.find("."+k).addClass("active")}};if("fullscreen"!==k){e.ui.toolbar.find("."+k).removeClass("active")}if(l.tags||(l.options&&l.options.tags)){g=l.tags||(l.options&&l.options.tags);n=f;while(n){if(n.nodeType!==1){break}if(c.inArray(n.tagName.toLowerCase(),g)!==-1){e.ui.toolbar.find("."+k).addClass("active")}n=n.parentNode}}if(l.css||(l.options&&l.options.css)){i=l.css||(l.options&&l.options.css);j=c(f);while(j){if(j[0].nodeType!==1){break}m(i);j=j.parent()}}})};this.ui.designMode=function(){var f=3,e=this.self,g;g=function(h){if("on"===e.editorDoc.designMode){if(e.timers.designMode){window.clearTimeout(e.timers.designMode)}if(e.innerDocument()!==e.editorDoc){e.ui.initFrame()}return}try{e.editorDoc.designMode="on"}catch(i){}h-=1;if(h>0){e.timers.designMode=window.setTimeout(function(){g(h)},100)}};g(f)};this.destroy=function(){this.isDestroyed=true;var f,e=this.element.closest("form");for(f=0;f<this.timers.length;f+=1){window.clearTimeout(this.timers[f])}c(this.original).appendTo(c(this.element.parent()));e.unbind(".wysiwyg");this.element.remove();c.removeData(this.original,"wysiwyg");c(this.original).show();return this};this.getRangeText=function(){var e=this.getInternalRange();if(e){if(e.toString){e=e.toString()}else{if(e.text){e=e.text}}}return e};this.execute=function(f,e){if(typeof(e)==="undefined"){e=null}this.editorDoc.execCommand(f,false,e)};this.extendOptions=function(f){var e={};if("object"===typeof f.controls){e=f.controls;delete f.controls}f=c.extend(true,{},this.defaults,f);f.controls=c.extend(true,{},e,this.controls,e);if(f.rmUnusedControls){c.each(f.controls,function(g){if(!e[g]){delete f.controls[g]}})}return f};this.ui.focus=function(){var e=this.self;e.editor.get(0).contentWindow.focus();return e};this.ui.returnRange=function(){var f=this.self,g;if(f.savedRange!==null){if(window.getSelection){g=window.getSelection();if(g.rangeCount>0){g.removeAllRanges()}try{g.addRange(f.savedRange)}catch(h){a.error(h)}}else{if(window.document.createRange){window.getSelection().addRange(f.savedRange)}else{if(window.document.selection){f.savedRange.select()}}}f.savedRange=null}};this.increaseFontSize=function(){if(c.browser.mozilla||c.browser.opera){this.editorDoc.execCommand("increaseFontSize",false,null)}else{if(c.browser.webkit){var h=this.getInternalRange(),g=this.getInternalSelection(),f=this.editorDoc.createElement("big");if(true===h.collapsed&&3===h.commonAncestorContainer.nodeType){var i=h.commonAncestorContainer.nodeValue.toString(),j=i.lastIndexOf(" ",h.startOffset)+1,e=(-1===i.indexOf(" ",h.startOffset))?i:i.indexOf(" ",h.startOffset);h.setStart(h.commonAncestorContainer,j);h.setEnd(h.commonAncestorContainer,e);h.surroundContents(f);g.addRange(h)}else{h.surroundContents(f);g.removeAllRanges();g.addRange(h)}}else{a.error("Internet Explorer?")}}};this.decreaseFontSize=function(){if(c.browser.mozilla||c.browser.opera){this.editorDoc.execCommand("decreaseFontSize",false,null)}else{if(c.browser.webkit){var h=this.getInternalRange(),g=this.getInternalSelection(),f=this.editorDoc.createElement("small");if(true===h.collapsed&&3===h.commonAncestorContainer.nodeType){var i=h.commonAncestorContainer.nodeValue.toString(),j=i.lastIndexOf(" ",h.startOffset)+1,e=(-1===i.indexOf(" ",h.startOffset))?i:i.indexOf(" ",h.startOffset);h.setStart(h.commonAncestorContainer,j);h.setEnd(h.commonAncestorContainer,e);h.surroundContents(f);g.addRange(h)}else{h.surroundContents(f);g.removeAllRanges();g.addRange(h)}}else{a.error("Internet Explorer?")}}};this.getContent=function(){if(this.viewHTML){this.setContent(this.original.value)}return this.events.filter("getContent",this.editorDoc.body.innerHTML)};this.events={_events:{},bind:function(e,f){if(typeof(this._events.eventName)!=="object"){this._events[e]=[]}this._events[e].push(f)},trigger:function(e,f){if(typeof(this._events.eventName)==="object"){var g=this.editor;c.each(this._events[e],function(i,h){if(typeof(h)==="function"){h.apply(g,f)}})}},filter:function(e,h){if(typeof(this._events[e])==="object"){var g=this.editor,f=Array.prototype.slice.call(arguments,1);c.each(this._events[e],function(j,i){if(typeof(i)==="function"){h=i.apply(g,f)}})}return h}};this.getElementByAttributeValue=function(g,e,h){var f,k,j=this.editorDoc.getElementsByTagName(g);for(f=0;f<j.length;f+=1){k=j[f].getAttribute(e);if(c.browser.msie){k=k.substr(k.length-h.length)}if(k===h){return j[f]}}return false};this.getInternalRange=function(){var e=this.getInternalSelection();if(!e){return null}if(e.rangeCount&&e.rangeCount>0){return e.getRangeAt(0)}else{if(e.createRange){return e.createRange()}}return null};this.getInternalSelection=function(){var e=this.editor.get(0).contentWindow;if(e&&e.getSelection){return e.getSelection()}else{if(this.editorDoc.getSelection){return this.editorDoc.getSelection()}else{if(this.editorDoc.selection){return this.editorDoc.selection}}}return null};this.getRange=function(){var e=this.getSelection();if(!e){return null}if(e.rangeCount&&e.rangeCount>0){e.getRangeAt(0)}else{if(e.createRange){return e.createRange()}}return null};this.getSelection=function(){var e=(window.getSelection&&window.getSelection()!==null&&window.getSelection().createRange)?window.getSelection():window.document.selection;return e};this.ui.grow=function(){var f=this.self,i=c(f.editorDoc.body),h=c.browser.msie?i[0].scrollHeight:i.height()+2+20,g=f.ui.initialHeight,e=Math.max(h,g);e=Math.min(e,f.options.maxHeight);f.editor.attr("scrolling",e<f.options.maxHeight?"no":"auto");i.css("overflow",e<f.options.maxHeight?"hidden":"");f.editor.get(0).height=e;return f};this.init=function(i,h){var g=this,f=c(i).closest("form"),k=(i.width||i.clientWidth||0),j=(i.height||i.clientHeight||0);this.options=this.extendOptions(h);this.original=i;this.ui.toolbar=c(this.options.toolbarHtml);if(c.browser.msie&&parseInt(c.browser.version,10)<8){this.options.autoGrow=false}if(k===0&&i.cols){k=(i.cols*8)+21}if(j===0&&i.rows){j=(i.rows*16)+16}this.editor=c(window.location.protocol==="https:"?'<iframe src="javascript:false;"></iframe>':"<iframe></iframe>").attr("frameborder","0");if(this.options.iFrameClass){this.editor.addClass(this.options.iFrameClass)}else{this.editor.css({minHeight:(j-6).toString()+"px",width:(k>50)?k.toString()+"px":""});if(c.browser.msie&&parseInt(c.browser.version,10)<7){this.editor.css("height",j.toString()+"px")}}if(i.id){var e=i.id+"-wysiwyg-iframe";if(!document.getElementById(e)){this.editor.attr("id",e)}}this.editor.attr("tabindex",c(i).attr("tabindex"));this.element=c("<div/>").addClass("wysiwyg");if(!this.options.iFrameClass){this.element.css({width:(k>0)?k.toString()+"px":"100%"})}c(i).hide().before(this.element);this.viewHTML=false;this.initialContent=c(i).val();this.ui.initFrame();if(this.options.resizeOptions&&c.fn.resizable){this.element.resizable(c.extend(true,{alsoResize:this.editor},this.options.resizeOptions))}if(this.options.autoSave){f.bind("submit.wysiwyg",function(){g.autoSaveFunction()})}f.bind("reset.wysiwyg",function(){g.resetFunction()})};this.ui.initFrame=function(){var e=this.self,g,j,f,i;i=c('<div class="toolbar-wrap"><div style="clear: both"><!-- --></div>').prepend(e.ui.toolbar);e.ui.appendControls();e.element.append(i).append(e.editor).append(e.original);e.editorDoc=e.innerDocument();if(e.isDestroyed){return null}e.ui.designMode();e.editorDoc.open();e.editorDoc.write(e.options.html.replace(/INITIAL_CONTENT/,function(){return e.wrapInitialContent()}));e.editorDoc.close();c.wysiwyg.plugin.bind(e);c(e.editorDoc).trigger("initFrame.wysiwyg");c(e.editorDoc).bind("click.wysiwyg",function(k){e.ui.checkTargets(k.target?k.target:k.srcElement)});c(e.original).focus(function(){if(c(this).filter(":visible").length===0||c.browser.opera){return}e.ui.focus()});c(c.wysiwyg.quirk.quirks).each(function(k,l){l.init(e)});c(e.editorDoc).keydown(function(k){var l;if(k.keyCode===8){l=/^<([\w]+)[^>]*>(<br\/?>)?<\/\1>$/;if(l.test(e.getContent())){k.stopPropagation();return false}}e.editorDoc.rememberCommand=false;return true});if(!c.browser.msie){c(e.editorDoc).keydown(function(k){var m;var l;if(k.ctrlKey||k.metaKey){for(m in e.options.controls){l=e.options.controls[m];if(l.hotkey&&l.hotkey.ctrl){if(k.keyCode===l.hotkey.key){e.triggerControl.apply(e,[m,l]);return false}}}}return true})}if(e.options.brIE){c(e.editorDoc).keydown(function(n){if(n.keyCode===13){if(c.browser.msie||c.browser.opera){var k=e.getRange();if(k){k.pasteHTML("<br/>");k.collapse(false);k.select()}else{e.insertHtml("<br/>")}}else{var m=e.editorDoc.getSelection();if(m&&m.getRangeAt&&m.rangeCount){var l=m.getRangeAt(0);if(!l){return true}var o=document.createElement("br");l.deleteContents();l.insertNode(o);l.setStartAfter(o);l.collapse(true);m.removeAllRanges();m.addRange(l)}else{return true}}return false}return true})}if(e.options.plugins.rmFormat.rmMsWordMarkup){c(e.editorDoc).bind("keyup.wysiwyg",function(k){if(k.ctrlKey||k.metaKey){if(86===k.keyCode){if(c.wysiwyg.rmFormat){if("object"===typeof(e.options.plugins.rmFormat.rmMsWordMarkup)){c.wysiwyg.rmFormat.run(e,{rules:{msWordMarkup:e.options.plugins.rmFormat.rmMsWordMarkup}})}else{c.wysiwyg.rmFormat.run(e,{rules:{msWordMarkup:{enabled:true}}})}}}}})}if(e.options.autoSave){c(e.editorDoc).keydown(function(){e.autoSaveFunction()}).keyup(function(){e.autoSaveFunction()}).mousedown(function(){e.autoSaveFunction()}).bind(c.support.noCloneEvent?"input.wysiwyg":"paste.wysiwyg",function(){e.autoSaveFunction()})}if(e.options.autoGrow){if(e.options.initialMinHeight!==null){e.ui.initialHeight=e.options.initialMinHeight}else{e.ui.initialHeight=c(e.editorDoc).height()}c(e.editorDoc.body).css("border","1px solid white");j=function(){e.ui.grow()};c(e.editorDoc).keyup(j);c(e.editorDoc).bind("editorRefresh.wysiwyg",j);e.ui.grow()}if(e.options.css){if(String===e.options.css.constructor){if(c.browser.msie){g=e.editorDoc.createStyleSheet(e.options.css);c(g).attr({media:"all"})}else{g=c("<link/>").attr({href:e.options.css,media:"all",rel:"stylesheet",type:"text/css"});c(e.editorDoc).find("head").append(g)}}else{e.timers.initFrame_Css=window.setTimeout(function(){c(e.editorDoc.body).css(e.options.css)},0)}}if(e.initialContent.length===0){if("function"===typeof(e.options.initialContent)){e.setContent(e.options.initialContent())}else{e.setContent(e.options.initialContent)}}if(e.options.maxLength>0){c(e.editorDoc).keydown(function(k){if(c(e.editorDoc).text().length>=e.options.maxLength&&c.inArray(k.which,e.validKeyCodes)===-1){k.preventDefault()}})}c.each(e.options.events,function(k,l){c(e.editorDoc).bind(k+".wysiwyg",function(m){l.apply(e.editorDoc,[m,e])})});if(c.browser.msie){c(e.editorDoc).bind("beforedeactivate.wysiwyg",function(){e.savedRange=e.getInternalRange()})}else{c(e.editorDoc).bind("blur.wysiwyg",function(){e.savedRange=e.getInternalRange()})}c(e.editorDoc.body).addClass("wysiwyg");if(e.options.events&&e.options.events.save){f=e.options.events.save;c(e.editorDoc).bind("keyup.wysiwyg",f);c(e.editorDoc).bind("change.wysiwyg",f);if(c.support.noCloneEvent){c(e.editorDoc).bind("input.wysiwyg",f)}else{c(e.editorDoc).bind("paste.wysiwyg",f);c(e.editorDoc).bind("cut.wysiwyg",f)}}if(e.options.xhtml5&&e.options.unicode){var h={ne:8800,le:8804,para:182,xi:958,darr:8595,nu:957,oacute:243,Uacute:218,omega:969,prime:8242,pound:163,igrave:236,thorn:254,forall:8704,emsp:8195,lowast:8727,brvbar:166,alefsym:8501,nbsp:160,delta:948,clubs:9827,lArr:8656,Omega:937,Auml:196,cedil:184,and:8743,plusmn:177,ge:8805,raquo:187,uml:168,equiv:8801,laquo:171,rdquo:8221,Epsilon:917,divide:247,fnof:402,chi:967,Dagger:8225,iacute:237,rceil:8969,sigma:963,Oslash:216,acute:180,frac34:190,lrm:8206,upsih:978,Scaron:352,part:8706,exist:8707,nabla:8711,image:8465,prop:8733,zwj:8205,omicron:959,aacute:225,Yuml:376,Yacute:221,weierp:8472,rsquo:8217,otimes:8855,kappa:954,thetasym:977,harr:8596,Ouml:214,Iota:921,ograve:242,sdot:8901,copy:169,oplus:8853,acirc:226,sup:8835,zeta:950,Iacute:205,Oacute:211,crarr:8629,Nu:925,bdquo:8222,lsquo:8216,apos:39,Beta:914,eacute:233,egrave:232,lceil:8968,Kappa:922,piv:982,Ccedil:199,ldquo:8220,Xi:926,cent:162,uarr:8593,hellip:8230,Aacute:193,ensp:8194,sect:167,Ugrave:217,aelig:230,ordf:170,curren:164,sbquo:8218,macr:175,Phi:934,Eta:919,rho:961,Omicron:927,sup2:178,euro:8364,aring:229,Theta:920,mdash:8212,uuml:252,otilde:245,eta:951,uacute:250,rArr:8658,nsub:8836,agrave:224,notin:8713,ndash:8211,Psi:936,Ocirc:212,sube:8838,szlig:223,micro:181,not:172,sup1:185,middot:183,iota:953,ecirc:234,lsaquo:8249,thinsp:8201,sum:8721,ntilde:241,scaron:353,cap:8745,atilde:227,lang:10216,__replacement:65533,isin:8712,gamma:947,Euml:203,ang:8736,upsilon:965,Ntilde:209,hearts:9829,Alpha:913,Tau:932,spades:9824,dagger:8224,THORN:222,"int":8747,lambda:955,Eacute:201,Uuml:220,infin:8734,rlm:8207,Aring:197,ugrave:249,Egrave:200,Acirc:194,rsaquo:8250,ETH:208,oslash:248,alpha:945,Ograve:210,Prime:8243,mu:956,ni:8715,real:8476,bull:8226,beta:946,icirc:238,eth:240,prod:8719,larr:8592,ordm:186,perp:8869,Gamma:915,reg:174,ucirc:251,Pi:928,psi:968,tilde:732,asymp:8776,zwnj:8204,Agrave:192,deg:176,AElig:198,times:215,Delta:916,sim:8764,Otilde:213,Mu:924,uArr:8657,circ:710,theta:952,Rho:929,sup3:179,diams:9830,tau:964,Chi:935,frac14:188,oelig:339,shy:173,or:8744,dArr:8659,phi:966,iuml:239,Lambda:923,rfloor:8971,iexcl:161,cong:8773,ccedil:231,Icirc:206,frac12:189,loz:9674,rarr:8594,cup:8746,radic:8730,frasl:8260,euml:235,OElig:338,hArr:8660,Atilde:195,Upsilon:933,there4:8756,ouml:246,oline:8254,Ecirc:202,yacute:253,auml:228,permil:8240,sigmaf:962,iquest:191,empty:8709,pi:960,Ucirc:219,supe:8839,Igrave:204,yen:165,rang:10217,trade:8482,lfloor:8970,minus:8722,Zeta:918,sub:8834,epsilon:949,yuml:255,Sigma:931,Iuml:207,ocirc:244};e.events.bind("getContent",function(k){return k.replace(/&(?:amp;)?(?!amp|lt|gt|quot)([a-z][a-z0-9]*);/gi,function(n,m){if(!h[m]){m=m.toLowerCase();if(!h[m]){m="__replacement"}}var l=h[m];return String.fromCharCode(l)})})}c(e.original).trigger("ready.jwysiwyg",[e.editorDoc,e])};this.innerDocument=function(){var e=this.editor.get(0);if(e.nodeName.toLowerCase()==="iframe"){if(e.contentDocument){return e.contentDocument}else{if(e.contentWindow){return e.contentWindow.document}}if(this.isDestroyed){return null}a.error("Unexpected error in innerDocument")}return e};this.insertHtml=function(e){var g,f;if(!e||e.length===0){return this}if(c.browser.msie){this.ui.focus();this.editorDoc.execCommand("insertImage",false,"#jwysiwyg#");g=this.getElementByAttributeValue("img","src","#jwysiwyg#");if(g){c(g).replaceWith(e)}}else{if(c.browser.mozilla){if(1===c(e).length){f=this.getInternalRange();f.deleteContents();f.insertNode(c(e).get(0))}else{this.editorDoc.execCommand("insertHTML",false,e)}}else{if(!this.editorDoc.execCommand("insertHTML",false,e)){this.editor.focus();this.editorDoc.execCommand("insertHTML",false,e)}}}this.saveContent();return this};this.parseControls=function(){var e=this;c.each(this.options.controls,function(g,f){c.each(f,function(h){if(-1===c.inArray(h,e.availableControlProperties)){throw g+'["'+h+'"]: property "'+h+'" not exists in Wysiwyg.availableControlProperties'}})});if(this.options.parseControls){return this.options.parseControls.call(this)}return this.options.controls};this.removeFormat=function(){if(c.browser.msie){this.ui.focus()}if(this.options.removeHeadings){this.editorDoc.execCommand("formatBlock",false,"<p>")}this.editorDoc.execCommand("removeFormat",false,null);this.editorDoc.execCommand("unlink",false,null);if(c.wysiwyg.rmFormat&&c.wysiwyg.rmFormat.enabled){if("object"===typeof(this.options.plugins.rmFormat.rmMsWordMarkup)){c.wysiwyg.rmFormat.run(this,{rules:{msWordMarkup:this.options.plugins.rmFormat.rmMsWordMarkup}})}else{c.wysiwyg.rmFormat.run(this,{rules:{msWordMarkup:{enabled:true}}})}}return this};this.ui.removeHoverClass=function(){c(this).removeClass("wysiwyg-button-hover")};this.resetFunction=function(){this.setContent(this.initialContent)};this.saveContent=function(f){if(this.viewHTML){return}if(this.original){var h,e;h=(typeof f==="function")?f(this.getContent()):this.getContent();if(this.options.rmUnwantedBr){h=h.replace(/<br\/?>$/,"")}if(this.options.replaceDivWithP){e=c("<div/>").addClass("temp").append(h);e.children("div").each(function(){var k=c(this),l=k.find("p"),j;if(0===l.length){l=c("<p></p>");if(this.attributes.length>0){for(j=0;j<this.attributes.length;j+=1){l.attr(this.attributes[j].name,k.attr(this.attributes[j].name))}}l.append(k.html());k.replaceWith(l)}});h=e.html()}var g=c.Event("change");g.source=this;c(this.original).val(h).trigger(g);if(this.options.events&&this.options.events.save){this.options.events.save.call(this)}}return this};this.setContent=function(e){this.editorDoc.body.innerHTML=e;this.saveContent();return this};this.triggerControl=function(g,j){var h=j.command||g,f=j["arguments"]||[];if(j.exec){j.exec.apply(this,j.callbackArguments)}else{this.ui.focus();this.ui.withoutCss();try{this.editorDoc.execCommand(h,false,f)}catch(i){a.error(i)}}if(this.options.autoSave){this.autoSaveFunction()}};this.triggerControlCallback=function(e){c(window).trigger("trigger-"+e+".wysiwyg",[this])};this.ui.withoutCss=function(){var f=this.self;if(c.browser.mozilla){try{f.editorDoc.execCommand("styleWithCSS",false,false)}catch(h){try{f.editorDoc.execCommand("useCSS",false,true)}catch(g){}}}return f};this.wrapInitialContent=function(){var e=this.initialContent;return e}}c.wysiwyg={messages:{noObject:"Something goes wrong, check object"},addControl:function(f,e,g){return f.each(function(){var h=c(this).data("wysiwyg"),j={},i;if(!h){return this}j[e]=c.extend(true,{visible:true,custom:true},g);c.extend(true,h.options.controls,j);i=c(h.options.toolbarHtml);h.ui.toolbar.replaceWith(i);h.ui.toolbar=i;h.ui.appendControls()})},clear:function(e){return e.each(function(){var f=c(this).data("wysiwyg");if(!f){return this}f.setContent("")})},console:a,destroy:function(e){return e.each(function(){var f=c(this).data("wysiwyg");if(!f){return this}f.destroy()})},document:function(f){var e=f.data("wysiwyg");if(!e){return undefined}return c(e.editorDoc)},focus:function(f){var e=f.data("wysiwyg");if(!e){return undefined}e.ui.focus();return f},getContent:function(f){var e=f.data("wysiwyg");if(!e){return undefined}return e.getContent()},getSelection:function(f){var e=f.data("wysiwyg");if(!e){return undefined}return e.getRangeText()},init:function(f,e){return f.each(function(){var g=c.extend(true,{},e),h;if(("textarea"!==this.nodeName.toLowerCase())||c(this).data("wysiwyg")){return}h=new d();h.init(this,g);c.data(this,"wysiwyg",h);c(h.editorDoc).trigger("afterInit.wysiwyg")})},insertHtml:function(f,e){return f.each(function(){var g=c(this).data("wysiwyg");if(!g){return this}g.insertHtml(e)})},plugin:{listeners:{},bind:function(g){var f=this;var e=function(){return function(i){var j=i.data.plugin.name;var h=i.data.plugin.method;c.wysiwyg[j][h].apply(c.wysiwyg[j],[g])}};c.each(this.listeners,function(l,h){var j,k;for(j=0;j<h.length;j+=1){k=f.parseName(h[j]);c(g.editorDoc).bind(l+".wysiwyg",{plugin:k},e())}})},exists:function(e){var f;if("string"!==typeof(e)){return false}f=this.parseName(e);if(!c.wysiwyg[f.name]||!c.wysiwyg[f.name][f.method]){return false}return true},listen:function(g,e){var f;f=this.parseName(e);if(!c.wysiwyg[f.name]||!c.wysiwyg[f.name][f.method]){return false}if(!this.listeners[g]){this.listeners[g]=[]}this.listeners[g].push(e);return true},parseName:function(e){var f;if("string"!==typeof(e)){return false}f=e.split(".");if(2>f.length){return false}return{name:f[0],method:f[1]}},register:function(e){if(!e.name){a.error("Plugin name missing")}c.each(c.wysiwyg,function(f){if(f===e.name){a.error("Plugin with name '"+e.name+"' was already registered")}});c.wysiwyg[e.name]=e;return true}},quirk:{quirks:[],assert:function(f,e){if(!f){throw new Error(e)}},register:function(e){this.assert(typeof e.init==="function","quirk.init must be a function");this.quirks.push(e)}},removeFormat:function(e){return e.each(function(){var f=c(this).data("wysiwyg");if(!f){return this}f.removeFormat()})},save:function(e){return e.each(function(){var f=c(this).data("wysiwyg");if(!f){return this}f.saveContent()})},selectAll:function(g){var e=g.data("wysiwyg"),f,i,h;if(!e){return this}f=e.editorDoc.body;if(window.getSelection){h=e.getInternalSelection();h.selectAllChildren(f)}else{i=f.createTextRange();i.moveToElementText(f);i.select()}},setContent:function(f,e){return f.each(function(){var g=c(this).data("wysiwyg");if(!g){return this}g.setContent(e)})},triggerControl:function(e,f){return e.each(function(){var g=c(this).data("wysiwyg");if(!g){return this}if(!g.controls[f]){a.error("Control '"+f+"' not exists")}g.triggerControl.apply(g,[f,g.controls[f]])})},support:{prop:b},utils:{extraSafeEntities:[["<",">","'",'"'," "],[32]],encodeEntities:function(h){var f=this,e,g=[];if(this.extraSafeEntities[1].length===0){c.each(this.extraSafeEntities[0],function(j,k){f.extraSafeEntities[1].push(k.charCodeAt(0))})}e=h.split("");c.each(e,function(j){var k=e[j].charCodeAt(0);if(c.inArray(k,f.extraSafeEntities[1])&&(k<65||k>127||(k>90&&k<97))){g.push("&#"+k+";")}else{g.push(e[j])}});return g.join("")}}};c.wysiwyg.dialog=function(f,h){var j=(f&&f.options&&f.options.dialog)?f.options.dialog:(h.theme?h.theme:"default"),i=new c.wysiwyg.dialog.createDialog(j),g=this,e=c(g);this.options={modal:true,draggable:true,title:"Title",content:"Content",width:"auto",height:"auto",zIndex:2000,open:false,close:false};this.isOpen=false;c.extend(this.options,h);this.object=i;this.open=function(){this.isOpen=true;i.init.apply(g,[]);var k=i.show.apply(g,[]);e.trigger("afterOpen",[k])};this.show=function(){this.isOpen=true;e.trigger("beforeShow");e.trigger("afterShow")};this.hide=function(){this.isOpen=false;e.trigger("beforeHide");var k=i.hide.apply(g,[]);e.trigger("afterHide",[k])};this.close=function(){this.isOpen=false;var k=i.hide.apply(g,[]);e.trigger("beforeClose",[k]);i.destroy.apply(g,[]);e.trigger("afterClose",[k]);f.ui.focus()};if(this.options.open){e.bind("afterOpen",this.options.open)}if(this.options.close){e.bind("afterClose",this.options.close)}return this};c.extend(true,c.wysiwyg.dialog,{_themes:{},_theme:"",register:function(e,f){c.wysiwyg.dialog._themes[e]=f},deregister:function(e){delete c.wysiwyg.dialog._themes[e]},createDialog:function(e){return new c.wysiwyg.dialog._themes[e]()},getDimensions:function(){var f=document.body.scrollWidth,e=document.body.scrollHeight;if(c.browser.opera){e=Math.max(c(document).height(),c(window).height(),document.documentElement.clientHeight)}return[f,e]}});c(function(){if(c.ui){c.wysiwyg.dialog.register("jqueryui",function(){var e=this;this._$dialog=null;this.init=function(){var g=this.options.content;if(typeof g==="object"){if(typeof g.html==="function"){g=g.html()}else{if(typeof g.toString==="function"){g=g.toString()}}}e._$dialog=c("<div></div>").attr("title",this.options.title).html(g);var f=this.options.height==="auto"?300:this.options.height,h=this.options.width==="auto"?450:this.options.width;e._$dialog.dialog({modal:this.options.modal,draggable:this.options.draggable,height:f,width:h});return e._$dialog};this.show=function(){e._$dialog.dialog("open");return e._$dialog};this.hide=function(){e._$dialog.dialog("close");return e._$dialog};this.destroy=function(){e._$dialog.dialog("destroy");return e._$dialog}})}c.wysiwyg.dialog.register("default",function(){var e=this;this._$dialog=null;this.init=function(){var l=this,i=this.options.content;if(typeof i==="object"){if(typeof i.html==="function"){i=i.html()}else{if(typeof i.toString==="function"){i=i.toString()}}}e._$dialog=c('<div class="wysiwyg-dialog"></div>').css({"z-index":this.options.zIndex});var h=c('<div class="wysiwyg-dialog-topbar"><div class="wysiwyg-dialog-close-wrapper"></div><div class="wysiwyg-dialog-title">'+this.options.title+"</div></div>");var f=c('<a href="#" class="wysiwyg-dialog-close-button">X</a>');f.click(function(){l.close()});h.find(".wysiwyg-dialog-close-wrapper").prepend(f);var k=c('<div class="wysiwyg-dialog-content">'+i+"</div>");e._$dialog.append(h).append(k);var g=this.options.height==="auto"?300:this.options.height,j=this.options.width==="auto"?450:this.options.width;e._$dialog.hide().css({width:j,height:g,left:((c(window).width()-j)/2),top:((c(window).height()-g)/3)});c("body").append(e._$dialog);return e._$dialog};this.show=function(){if(this.options.modal){var g=c.wysiwyg.dialog.getDimensions(),h=c('<div class="wysiwyg-dialog-modal-div"></div>').css({width:g[0],height:g[1]});e._$dialog.wrap(h)}if(this.options.draggable){var f=false;e._$dialog.find("div.wysiwyg-dialog-topbar").bind("mousedown",function(l){l.preventDefault();c(this).css({cursor:"move"});var j=c(this),k=c(this).parents(".wysiwyg-dialog"),i=(l.pageX-parseInt(k.css("left"),10)),m=(l.pageY-parseInt(k.css("top"),10));f=true;c(this).css({cursor:"move"});c(document).bind("mousemove",function(n){n.preventDefault();if(f){k.css({top:(n.pageY-m),left:(n.pageX-i)})}}).bind("mouseup",function(n){n.preventDefault();f=false;j.css({cursor:"auto"});c(document).unbind("mousemove").unbind("mouseup")})})}e._$dialog.show();return e._$dialog};this.hide=function(){e._$dialog.hide();return e._$dialog};this.destroy=function(){if(this.options.modal){e._$dialog.unwrap()}if(this.options.draggable){e._$dialog.find("div.wysiwyg-dialog-topbar").unbind("mousedown")}e._$dialog.remove();return e._$dialog}})});if(c.browser===undefined){jQuery.browser=function(){var g=function(i){i=i.toLowerCase();var h=/(chrome)[ \/]([\w.]+)/.exec(i)||/(webkit)[ \/]([\w.]+)/.exec(i)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(i)||/(msie) ([\w.]+)/.exec(i)||i.indexOf("compatible")<0&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(i)||[];return{browser:h[1]||"",version:h[2]||"0"}},e=g(navigator.userAgent),f={};if(e.browser){f[e.browser]=true;f.version=e.version}if(f.chrome){f.webkit=true}else{if(f.webkit){f.safari=true}}return f}}c.fn.wysiwyg=function(g){var e=arguments,f;if("undefined"!==typeof c.wysiwyg[g]){e=Array.prototype.concat.call([e[0]],[this],Array.prototype.slice.call(e,1));return c.wysiwyg[g].apply(c.wysiwyg,Array.prototype.slice.call(e,1))}else{if("object"===typeof g||!g){Array.prototype.unshift.call(e,this);return c.wysiwyg.init.apply(c.wysiwyg,e)}else{if(c.wysiwyg.plugin.exists(g)){f=c.wysiwyg.plugin.parseName(g);e=Array.prototype.concat.call([e[0]],[this],Array.prototype.slice.call(e,1));return c.wysiwyg[f.name][f.method].apply(c.wysiwyg[f.name],Array.prototype.slice.call(e,1))}else{a.error("Method '"+g+"' does not exist on jQuery.wysiwyg.\nTry to include some extra controls or plugins")}}}};c.fn.getWysiwyg=function(){return this.data("wysiwyg")}})(jQuery);
