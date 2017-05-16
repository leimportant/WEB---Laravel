
$(document).ready(function(){
	
	var rightpanelmargin = '260px';
	Modernizr.Detectizr.detect();
	if($('html').hasClass('chrome25') && $('html').hasClass('windows')) {
		$('.leftpanel').css({width: '259px', position: 'static'});
		$('.rightpanel, .breadcrumbwidget').css({position: 'static'});
		rightpanelmargin = '259px';
	}
	
	prettyPrint();			//syntax highlighter
	mainwrapperHeight();
	responsive();
	

	// adjust height of mainwrapper when 
	// it's below the document height
	function mainwrapperHeight() {
		var windowHeight = $(window).height();
		var mainWrapperHeight = $('.mainwrapper').height();
		var leftPanelHeight = $('.leftpanel').height();
		if(leftPanelHeight > mainWrapperHeight)
			$('.mainwrapper').css({minHeight: leftPanelHeight});	
		if($('.mainwrapper').height() < windowHeight)
			$('.mainwrapper').css({minHeight: windowHeight});
	}
	
	function responsive() {
		
		var windowWidth = $(window).width();
		
		// hiding and showing left menu
		if(!$('.showmenu').hasClass('clicked')) {
			
			if(windowWidth < 960)
				hideLeftPanel();
			else
				showLeftPanel();
		}
		
		// rearranging widget icons in dashboard
		if(windowWidth < 768) {
			if($('.widgeticons .one_third').length == 0) {
				var count = 0;
				$('.widgeticons li').each(function(){
					$(this).removeClass('one_fifth last').addClass('one_third');
					if(count == 2) {
						$(this).addClass('last');
						count = 0;
					} else { count++; }
				});	
			}
		} else {
			if($('.widgeticons .one_firth').length == 0) {
				var count = 0;
				$('.widgeticons li').each(function(){
					$(this).removeClass('one_third last').addClass('one_fifth');
					if(count == 4) {
						$(this).addClass('last');
						count = 0;
					} else { count++; }
				});	
			}
		}
	}
	
	// when resize window event fired
	$(window).resize(function(){
		mainwrapperHeight();
		responsive();
	});
	
	// dropdown in leftmenu
	$('.leftmenu .dropdown > a').click(function(){
		if(!$(this).next().is(':visible'))
			$(this).next().slideDown('fast');
		else
			$(this).next().slideUp('fast');	
		return false;
	});
	
	// hide left panel
	function hideLeftPanel() {
		$('.leftpanel').css({marginLeft: '-260px'}).addClass('hide');
		$('.rightpanel').css({marginLeft: 0});
		$('.mainwrapper').css({backgroundPosition: '-260px 0'});
		$('.footerleft').hide();
		$('.footerright').css({marginLeft: 0});
	}
	
	// show left panel
	function showLeftPanel() {
		$('.leftpanel').css({marginLeft: '0px'}).removeClass('hide');
		$('.rightpanel').css({marginLeft: rightpanelmargin});
		$('.mainwrapper').css({backgroundPosition: '0 0'});
		$('.footerleft').show();
		$('.footerright').css({marginLeft: rightpanelmargin});
	}
	
	// show and hide left panel
	$('.showmenu').click(function() {
		$(this).addClass('clicked');
		if($('.leftpanel').hasClass('hide'))
			showLeftPanel();
		else
			hideLeftPanel();
		return false;
	});
	
	// transform checkbox and radio box using uniform plugin
	if($().uniform)
		$('input:checkbox, input:radio, select.uniformselect').uniform();
	
	
	// show/hide widget content or widget content's child	
	if($('.showhide').length > 0 ) {
		$('.showhide').click(function(){
			var t = $(this);
			var p = t.parent();
			var target = t.attr('href');
			target = (!target)? p.next() :	p.next().find('.'+target);
			t.text((target.is(':visible'))? 'View Source' : 'Hide Source');
			(target.is(':visible'))? target.hide() : target.show(100);
			return false;
		});
	}
	
	
	// tabbed widget
	$('#tabs, #tabs2').tabs();
	
	// accordion widget
	$('#accordion, #accordion2').accordion({heightStyle: "content"});
	
	
	
	// change layout
	$('.skin-layout').click(function(){
		$('.skin-layout').each(function(){ $(this).parent().removeClass('selected'); });
		if($(this).hasClass('fixed')) {
			$('.mainwrapper').removeClass('fullwrapper');
			if($('.stickyheaderinner').length > 0) $('.stickyheaderinner').removeClass('wideheader');
			$.cookie("skin-layout", 'fixed', { path: '/' });
		} else {
			$('.mainwrapper').addClass('fullwrapper');
			if($('.stickyheaderinner').length > 0) $('.stickyheaderinner').addClass('wideheader');
			$.cookie("skin-layout", 'wide', { path: '/' });
		}
		return false;
	});
	
	// load selected layout from cookie
	if($.cookie('skin-layout')) {
		var layout = $.cookie('skin-layout');
		if(layout == 'fixed') {
			$('.mainwrapper').removeClass('fullwrapper');
			if($('.stickyheaderinner').length > 0) $('.stickyheaderinner').removeClass('wideheader');
		} else {
			$('.mainwrapper').addClass('fullwrapper');
			if($('.stickyheaderinner').length > 0) $('.stickyheaderinner').addClass('wideheader');
		}	
	}
	
	
	// change skin color
	$('.skin-color').click(function(){
		var s = $(this).attr('href');
		if($('#skinstyle').length > 0) {
			if(s!='default') {
				$('#skinstyle').attr('href','css/style.'+s+'.css');	
				$.cookie('skin-color', s, { path: '/' });
			} else {
				$('#skinstyle').remove();
				$.cookie("skin-color", '', { path: '/' });
			}
		} else {
			if(s!='default') {
				$('head').append('<link id="skinstyle" rel="stylesheet" href="css/style.'+s+'.css" type="text/css" />');
				$.cookie("skin-color", s, { path: '/' });
			}
		}
		return false;
	});
	
	
});