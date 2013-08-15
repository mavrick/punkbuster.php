//<![CDATA[
/* LOOK, LEARN, BUT DON'T STEAL :) © 2010 - Daniel Lang, mavrick.id.au */
var msgID = null;
pageLinksFollow=false;
pageLinksFX=null;
function adjustPageLinks(){
	if( pageLinksFollow ){
		if( !$('colorcodes').style.top ){ $('colorcodes').style.top = "0px"; }; 
		if( pageLinksFX ){ pageLinksFX.stop(); };
		pageLinksFX=new Fx.Style('colorcodes','top',{duration:1000,transition:Fx.Transitions.Quad.easeOut}).start(parseInt($('colorcodes').style.top),(window.getScrollTop()+((window.getScrollTop().toInt()>=$('fb_top').getTop().toInt()) ? 0 : ($('fb_top').getTop().toInt()-(10)))));
	};
};
var msgs = [];
window.addEvent('domready', function() {
	
	
	var i = 1; //new Drag.Move('colorcodes',{onStart: function() { $('colorcodes').setOpacity(0.4); }, onComplete: function() { $('colorcodes').setOpacity(1); }});
	$('colorcodes').setStyles({'top':'0px','left':'665px'});
	$('fb_overlayMsg').setStyles({
		'left' : ((window.getWidth() / 2) - 200) + 'px',
		'top' : ((window.getHeight() / 2) + window.getScrollTop().toInt()) + 'px'
	});
	$('fb_overlay').setStyles({
		'height' : window.getScrollHeight().toInt() + 'px',
		'width' : (window.getWidth().toInt()) + 'px',
		'cursor' : 'hand',
		'cursor' : 'pointer'
	}).addEvent('click',function() { $('fb_overlayMsg').setStyle('display','none'); $('fb_overlay').setStyle('display','none'); });
	$('msg_add').addEvent('click',function() {
		i++; var msg = $('msg').clone().injectInside($('container'));
		var child = msg.getChildren(); child.getLast().remove();
		msg.getElements('div[class=msg_title]').setText('Message ' + i + ':');
		msg.getElements('input[id=msg_1]').setProperties({'id':'msg_' + i,'value':''});
	});
	var saveMessage = new Ajax( '/cod4.php', { 
		method: 'post',
		data: Object.toQueryString({'save':'1','msg':msgs,'email':$('fb_emailAddyMsg').getProperty('value'),'delay':$('msg_delay').getProperty('value'),'start':$('msg_start').getProperty('value'),'from':$('msg_from').getProperty('value'),'msgID':(msgID ? msgID : ($('fb_msgID').getProperty('value')&&$('fb_emailAddy').getProperty('value') ? $('fb_msgID').getProperty('value') : ''))}),
		evalResponse: true,
		onRequest: function() { if($('fb_saveMsg')) { $('fb_saveMsg').setProperty('disabled','disabled').setProperty('value','Saving...'); } $('overlayTitle').setHTML('Saving...'); },
		onComplete: function() { (function() { $('fb_overlay').fireEvent('click'); $('overlayTitle').setHTML('Save you message rotation'); }).delay(5000); $('overlayTitle').setHTML('Message Saved, Email Sent...'); $$('input[name^=message]').each(function(el) { var row = el.getProperty('value').split(':comma:').join(','); el.setProperty('value',row).removeProperty('disabled'); }); if($('fb_saveMsg')) { $('fb_saveMsg').removeProperty('disabled').setProperty('value','Save Message'); } }
	});
	var saveMessage1 = new Ajax( '/cod4.php?save', { 
		method: 'post',
		data: $('fb_postForm').toQueryString(),
		evalResponse: true,
		onRequest: function() { if($('fb_saveMsg')) { $('fb_saveMsg').setProperty('disabled','disabled').setProperty('value','Saving...'); } $('overlayTitle').setHTML('Saving...'); },
		onComplete: function() { (function() { $('fb_overlay').fireEvent('click'); $('overlayTitle').setHTML('Save you message rotation'); }).delay(5000); $('overlayTitle').setHTML('Message Saved, Email Sent...'); $$('input[name^=message]').each(function(el) { var row = el.getProperty('value').split(':comma:').join(','); el.setProperty('value',row).removeProperty('readonly'); }); if($('fb_saveMsg')) { $('fb_saveMsg').removeProperty('disabled').setProperty('value','Save Message'); } }
	});
	$('fb_saveMsg').addEvent('click',function() {
		$$('input[name^=message]').each(function(el) { var row = el.getProperty('value').split(',').join(':comma:'); el.setProperty('value',row); });
		new Ajax( '/cod4.php?save', { 
			method: 'post',
			data: Object.toQueryString({'save':'1','msg':msgs,'email':$('fb_emailAddyMsg').getProperty('value'),'delay':$('msg_delay').getProperty('value'),'start':$('msg_start').getProperty('value'),'from':$('msg_from').getProperty('value'),'msgID':(msgID ? msgID : ($('fb_msgID').getProperty('value')&&$('fb_emailAddy').getProperty('value') ? $('fb_msgID').getProperty('value') : ''))}),
			evalResponse: true,
			onRequest: function() { if($('fb_saveMsg')) { $('fb_saveMsg').setProperty('disabled','disabled').setProperty('value','Saving...'); } $('overlayTitle').setHTML('Saving...'); },
			onComplete: function() { (function() { $('fb_overlay').fireEvent('click'); $('overlayTitle').setHTML('Save you message rotation'); }).delay(5000); $('overlayTitle').setHTML('Message Saved, Email Sent...'); $$('input[name^=message]').each(function(el) { var row = el.getProperty('value').split(':comma:').join(','); el.setProperty('value',row).removeProperty('readonly'); }); if($('fb_saveMsg')) { $('fb_saveMsg').removeProperty('disabled').setProperty('value','Save Message'); } }
		}).request();
	});
	$('generate').addEvent('click',function() {
		$$('input[name^=message]').each(function(el) { var row = el.getProperty('value').split(',').join(':comma:'); el.setProperty('value',row); });
		new Ajax( '/cod4.php', { 
			method: 'post',
			data: Object.toQueryString({'delay':$('msg_delay').getProperty('value'),'start':$('msg_start').getProperty('value'),'from':$('msg_from').getProperty('value'),'msg':$$('input[name^=message]').getProperty('value')}),
			onRequest: function() { $('output').toggleClass('fb_loading').setOpacity(0.6); $('fb_download').setProperty('disabled','disabled');  $('fb_select').setProperty('disabled','disabled'); $('fb_save').setProperty('disabled','disabled'); },
			onComplete: function() { $('output').toggleClass('fb_loading').setOpacity(1); $$('input[name^=message]').each(function(el) { var row = el.getProperty('value').split(':comma:').join(','); el.setProperty('value',row).removeProperty('disabled'); }); $('fb_download').removeProperty('disabled'); $('fb_select').removeProperty('disabled'); $('fb_save').removeProperty('disabled'); },
			update: $('output')
		}).request();
	});
	$('fb_select').addEvent('click',function() {
		document.getElementById("output").select();
	});
	$('fb_save').addEvent('click',function() {
		$$('input[name^=message]').each(function(el) {
			msgs.include(el.getProperty('value').split(':comma:').join(','));
		});
		if(msgID) {
			saveMessage.request();
			$('fb_overlay').setOpacity(0.7).setStyle('display','block');
			$('fb_overlayMsg').setStyle('display','block');
		} else {
			//alert('Back Soon, I Promise - Just Adding Some Fixes');
			if(confirm('This will save your message rotation to a global list where anyone can view it!\n\nYou will be asked for email address so your message ID number can be sent to you, your email address will not be available to the public.\n\nAre you sure you want to continue?')) {
				$('fb_overlay').setOpacity(0.7).setStyle('display','block');
				$('fb_overlayMsg').setStyle('display','block');
			}
		}
	});
	if($('fb_overlayClose')) { $('fb_overlayClose').addEvent('click',function() {
		$('fb_overlay').setStyle('display','none');
		$('fb_overlayMsg').setStyle('display','none');
	}); }
	$('fb_download').addEvent('click',function() {
		try {
			$('fb_form').fireEvent('submit');
			$('fb_form').submit();
		} catch(err) {}
	});
});
function fb_overlayMsg_adjust() {
	var myValues = $('fb_overlayMsg').getCoordinates();
	$('fb_overlayMsg').setStyles({
		'left' : ((window.getWidth() / 2) - 200) + 'px',
		'top' : ((window.getHeight() / 2) - (myValues.height.toInt() / 2) + window.getScrollTop().toInt()) + 'px'
	});
	$('fb_overlay').setStyles({
		'height' : window.getScrollHeight().toInt() + 'px',
		'width' : (window.getWidth().toInt()) + 'px'
	});
}
window.addEvent('resize',function() {
	fb_overlayMsg_adjust();
	$('fb_overlay').setStyles({
		'height' : window.getScrollHeight().toInt() + 'px',
		'width' : (window.getWidth().toInt()) + 'px'
	});
});
window.onscroll = function() {
	fb_overlayMsg_adjust();
	adjustPageLinks();
};
window.addEvent('domready',function(){
	pageLinksFollow=true;
	adjustPageLinks();
});
//]]>