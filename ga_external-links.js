/****** GA urchin.trackLinks 0.2
Docs: http://www.terenzani.it/54/urchintrack-utility-tracciare-link-esterni-e-download-con-google-analytics
Author: Francesco Terenzani http://www.terenzani.it/ ******/

function urchin(){
        this.trackDownload = '';
        this.trackLinks = function(){
                var a = document.getElementsByTagName('a');
                var domain = /^(http|https):\/\/([a-z-.0-9]+)[\/]{0,1}/i.exec(window.location);
                var internalLink = new RegExp("^(http|https):\/\/"+domain[2], "i");
                var isDownload = new RegExp("("+this.trackDownload+")$", "i");
                for(var i = 0; i < a.length; i++){
                        if(internalLink.test(a[i].href)){
                                if(this.trackDownload && isDownload.test(a[i].href))
										addHandler(a[i], 'click', function(){ urchinTracker('/download/'+this.href.replace(/^(http|https):\/\/([a-z-.0-9]+)\//i, '').split('/').join('--')); }, false);
                        }
                        else {			
							addHandler(a[i], 'click', function() { urchinTracker('/outgoing/'+this.href.replace(/^http:\/\/|https:\/\//i, '').split('/').join('--')); }, false);
                        }
                };
        };
};

function onContent(f){//(C)webreflection.blogspot.com
var a,b=navigator.userAgent,d=document,w=window,
c="__onContent__",e="addEventListener",o="opera",r="readyState",
s="<scr".concat("ipt defer src='//:' on",r,"change='if(this.",r,"==\"complete\"){this.parentNode.removeChild(this);",c,"()}'></scr","ipt>");
w[c]=(function(o){return function(){w[c]=function(){};for(a=arguments.callee;!a.done;a.done=1)f(o?o():o)}})(w[c]);
if(d[e])d[e]("DOMContentLoaded",w[c],false);
if(/WebKit|Khtml/i.test(b)||(w[o]&&parseInt(w[o].version())<9))
(function(){/loaded|complete/.test(d[r])?w[c]():setTimeout(arguments.callee,1)})();
else if(/MSIE/i.test(b))d.write(s);
};

function addHandler(obj, evt, newhandler, captures)
{
	if (obj.attachEvent)
		obj.attachEvent('on' + evt, newhandler);
	else if (obj.addEventListener)
		obj.addEventListener(evt, newhandler, captures);
	else
	{
		var oldhandler;
		if (oldhandler = obj['on' + evt])
			obj['on' + evt] = function() { oldhandler(); newhandler(); }
		else obj['on' + evt] = newhandler;
	}
}

