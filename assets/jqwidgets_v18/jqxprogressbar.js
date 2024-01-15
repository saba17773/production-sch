/*
jQWidgets v8.1.2 (2019-June)
Copyright (c) 2011-2019 jQWidgets.
License: https://jqwidgets.com/license/
*/
/* eslint-disable */

(function(a){a.jqx.jqxWidget("jqxProgressBar","",{});a.extend(a.jqx._jqxProgressBar.prototype,{defineInstance:function(){var b={colorRanges:[],value:0,oldValue:null,max:100,min:0,orientation:"horizontal",layout:"normal",width:null,height:null,showText:false,animationDuration:300,disabled:false,rtl:false,renderText:null,template:"",aria:{"aria-valuenow":{name:"value",type:"number"},"aria-disabled":{name:"disabled",type:"boolean"}},events:["valueChanged","invalidValue","complete","change"]};if(this===a.jqx._jqxProgressBar.prototype){return b}a.extend(true,this,b);return b},createInstance:function(c){var b=this;this.host.addClass(this.toThemeProperty("jqx-progressbar"));this.host.addClass(this.toThemeProperty("jqx-widget"));this.host.addClass(this.toThemeProperty("jqx-widget-content"));this.host.addClass(this.toThemeProperty("jqx-rc-all"));a.jqx.aria(this);if(this.width!=null&&this.width.toString().indexOf("px")!=-1){this.host.width(this.width)}else{if(this.width!=undefined&&!isNaN(this.width)){this.host.width(this.width)}else{this.host.width(this.width)}}if(this.height!=null&&this.height.toString().indexOf("px")!=-1){this.host.height(this.height)}else{if(this.height!=undefined&&!isNaN(this.height)){this.host.height(this.height)}else{this.host.height(this.height)}}this.valueDiv=a("<div></div>").appendTo(this.element);this._addRanges();this.valueDiv.addClass(this.toThemeProperty("jqx-fill-state-pressed"));if(this.template){this.valueDiv.addClass(this.toThemeProperty("jqx-"+this.template))}this.feedbackElementHost=a("<div style='left: 0px; top: 0px; width: 100%; height: 100%; position: absolute;'></div>").appendTo(this.host);this.feedbackElement=a("<span class='text'></span>").appendTo(this.feedbackElementHost);this.feedbackElement.addClass(this.toThemeProperty("jqx-progressbar-text"));this.oldValue=this._value();this.refresh();a.jqx.utilities.resize(this.host,function(){b.refresh()})},_addRanges:function(){if(this.colorRanges.length!=0){var d=this.orientation=="vertical";var b=this.colorRanges;var g=b.length;for(var f=0;f<g;f++){var e=b[f].stop;var c=b[f].color;this._createColorElements(e,c,d,g-f,f)}}},_refreshColorElements:function(){var j=this.host.outerWidth();var g=this.host.outerHeight();var h=this.orientation=="vertical";for(var e=0;e<this.colorRanges.length;e++){var f=this.colorRanges[e].element;if(!f){this.host.find(".jqx-progressbar-range").remove();this._addRanges();return}var d=this.colorRanges[e].stop;if(d>Math.min(this.max,this.value)){d=Math.min(this.max,this.value)}var b=100*(d-this.min)/(this.max-this.min);var c;if(!h){c=j*b/100}else{c=g*b/100}c+="px";if(h){f.css("height",c);if(this.layout=="reverse"){f.css("bottom",0)}else{f.css("top",0)}}else{f.css("width",c);if(this.rtl||this.layout=="reverse"){f.css("right","0px")}}}},_createColorElements:function(h,d,i,c,f){var l;if(h>Math.min(this.max,this.value)){h=Math.min(this.max,this.value)}var k=100*h/this.max;var j=this.host.width();var b=this.host.height();if(!i){l=this.host.outerWidth()*k/100}else{l=this.host.outerHeight()*k/100}l+="px";var e=a(this.valueDiv).parent()[0];e.style.position="relative";i=i||false;if(i){var g=a("<div/>");g.attr("class","jqx-progressbar-range");g.css("width","100%");g.css("height",l);g.css("background-color",d);g.css("position","absolute");g.css("z-index",c);if(this.layout=="reverse"){g.css("bottom",0)}else{g.css("top",0)}g.appendTo(e)}else{var g=a("<div/>");g.attr("class","jqx-progressbar-range");g.css("width",l);g.css("height","100%");g.css("background-color",d);g.css("position","absolute");g.css("z-index",c);g.css("top","0px");if(this.rtl){g.css("right","0px")}g.appendTo(e)}this.colorRanges[f].element=g},resize:function(c,b){this.width=c;this.height=b;this.refresh()},destroy:function(){this.host.removeClass();this.valueDiv.removeClass();this.valueDiv.remove();this.feedbackElement.remove()},_raiseevent:function(g,d,f){if(this.isInitialized!=undefined&&this.isInitialized==true){var c=this.events[g];var e=new a.Event(c);e.previousValue=d;e.currentValue=f;e.owner=this;var b=this.host.trigger(e);return b}},actualValue:function(b){if(b===undefined){return this._value()}a.jqx.aria(this,"aria-valuenow",b);a.jqx.setvalueraiseevent(this,"value",b);return this._value()},val:function(b){if(arguments.length==0||typeof(b)=="object"){return this.actualValue()}return this.actualValue(b)},propertiesChangedHandler:function(d,b,c){if(c&&c.width&&c.height&&Object.keys(c).length==2){d.host.width(c.width);d.host.height(c.height);d.refresh()}},propertyChangedHandler:function(c,d,b,f){if(!this.isInitialized){return}if(b==f){return}if(c.batchUpdate&&c.batchUpdate.width&&c.batchUpdate.height&&Object.keys(c.batchUpdate).length==2){return}var e=this;if(d=="colorRanges"){c.host.find(".jqx-progressbar-range").remove();c._addRanges()}if(d=="min"&&c.value<f){c.value=f}else{if(d=="max"&&c.value>f){c.value=f}}if(d==="value"&&e.value!=undefined){e.value=f;e.oldValue=b;a.jqx.aria(c,"aria-valuenow",f);if(f<e.min||f>e.max){e._raiseevent(1,b,f)}e.refresh()}if(d=="theme"){a.jqx.utilities.setTheme(b,f,c.host)}if(d=="renderText"||d=="orientation"||d=="layout"||d=="showText"||d=="min"||d=="max"){e.refresh()}else{if(d=="width"&&e.width!=undefined){if(e.width!=undefined&&!isNaN(e.width)){e.host.width(e.width);e.refresh()}}else{if(d=="height"&&e.height!=undefined){if(e.height!=undefined&&!isNaN(e.height)){e.host.height(e.height);e.refresh()}}}}if(d=="disabled"){e.refresh()}},_value:function(){var c=this.value;if(typeof c!=="number"){var b=parseInt(c);if(isNaN(b)){c=0}else{c=b}}return Math.min(this.max,Math.max(this.min,c))},_percentage:function(){return 100*(this._value()-this.min)/(this.max-this.min)},_textwidth:function(d){var c=a("<span>"+d+"</span>");a(this.host).append(c);var b=c.width();c.remove();return b},_textheight:function(d){var c=a("<span>"+d+"</span>");a(this.host).append(c);var b=c.height();c.remove();return b},_initialRender:true,refresh:function(c){if(c===true){return}var m=this.actualValue();var q=this._percentage();if(this.disabled){this.host.addClass(this.toThemeProperty("jqx-progressbar-disabled"));this.host.addClass(this.toThemeProperty("jqx-fill-state-disabled"));return}else{this.host.removeClass(this.toThemeProperty("jqx-progressbar-disabled"));this.host.removeClass(this.toThemeProperty("jqx-fill-state-disabled"));a(this.element.children[0]).show()}if(isNaN(m)){return}if(isNaN(q)){return}if(this.oldValue!==m){this._raiseevent(0,this.oldValue,m);this._raiseevent(3,this.oldValue,m);this.oldValue=m}var b=this.oldValue;var o=this.host.outerHeight();var d=this.host.outerWidth();if(this.width!=null){d=parseInt(this.width)}if(this.height!=null){o=parseInt(this.height)}this._refreshColorElements();var g=parseInt(this.host.outerWidth())/2;var j=parseInt(this.host.outerHeight())/2;if(isNaN(q)){q=0}this.valueDiv.removeClass(this.toThemeProperty("jqx-progressbar-value-vertical jqx-progressbar-value"));if(this.orientation=="horizontal"){this.valueDiv.width(0);this.valueDiv[0].style.height="100%";this.valueDiv.addClass(this.toThemeProperty("jqx-progressbar-value"))}else{this.valueDiv[0].style.width="100%";this.valueDiv.height(0);this.valueDiv.addClass(this.toThemeProperty("jqx-progressbar-value-vertical"))}var k=this;try{var n=this.element.children[0];a(n)[0].style.position="relative";if(this.orientation=="horizontal"){a(n).toggle(m>=this.min);var d=this.host.outerWidth()*q/100;var f=0;if(this.layout=="reverse"||this.rtl){if(this._initialRender){a(n)[0].style.left=this.host.width()+"px";a(n)[0].style.width=0}f=this.host.outerWidth()-d}a(n).stop();a(n).animate({width:d,left:f+"px"},this.animationDuration,function(){if(k._value()===k.max){k._raiseevent(2,b,k.max)}})}else{a(n).toggle(m>=this.min);var o=this.host.height()*q/100;var e=0;if(this.layout=="reverse"){if(this._initialRender){a(n)[0].style.top=this.host.height()+"px";a(n)[0].style.height=0}e=this.host.height()-o}a(n).stop();a(n).animate({height:o,top:e+"px"},this.animationDuration,function(){var r=k._percentage();if(isNaN(r)){r=0}if(r.toFixed(0)==k.min){a(n).hide();if(k._value()===k.max){k._raiseevent(2,b,k.max)}}})}}catch(i){}this._initialRender=false;this.feedbackElement.html(q.toFixed(0)+"%").toggle(this.showText==true);if(this.renderText){this.feedbackElement.html(this.renderText(q.toFixed(0)+"%",q))}this.feedbackElement.css("position","absolute");this.feedbackElement.css("top","50%");this.feedbackElement.css("left","0");if(this.colorRanges.length>0){this.feedbackElement.css("z-index",this.colorRanges.length+1)}var l=this.feedbackElement.height();var h=this.feedbackElement.width();var p=Math.floor(g-(parseInt(h)/2));this.feedbackElement.css({left:(p),"margin-top":-parseInt(l)/2+"px"})}})})(jqxBaseFramework);

