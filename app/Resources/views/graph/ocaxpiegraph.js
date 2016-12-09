/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2014 OCAX Contributors. See AUTHORS.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/*
DIVs
there is just one #pie_display. it contains one or many groups
a group contains many graph_container.
a graph_container contains just 1 'jqplot graph' and 1 'yii budget detail'
a graph is a jqplot graph

IDs
a graph_container has id = group.id+"_"+budget_id;
a graph has id = budget_id+"_graph";

Classes
a group has class ".ocaxjqplot"
a graph_container has class ".graph_container"
a graph has class ".graph_pie"
*/

var OCAXPieScripts = [
	"$baseURL/scripts/jqplot/jquery.jqplot.min.js",
	"$baseURL/scripts/jqplot/plugins/jqplot.pieRenderer.min.js",
	"$baseURL/scripts/jqplot/plugins/jqplot.highlighter.min.js",
	"$baseURL/scripts/jqplot.pieProperties.js"
	];
	
var OCAXPieScriptIndex = 0;

function loadOCAXPieScripts(index){
    if(index >= OCAXPieScripts.length)
        return;

	$.ajax({
		url: OCAXPieScripts[index],
		dataType: "script",
		cache: true,
		async: false,
		success: function(){ loadOCAXPieScripts(++OCAXPieScriptIndex) },
	});
}

(function($) {
	loadOCAXPieScripts(0)
	jQuery.support.cors = true;
	
			$("<link/>", {
				rel: "stylesheet",
				type: "text/css",
				href: "$baseURL/css/pdetailview.css"
			}).appendTo("head");
			
			$("<link/>", {
				rel: "stylesheet",
				type: "text/css",
				href: "$baseURL/css/piegraph.css"
			}).appendTo("head");
})(jQuery);

(function($) {
$.widget( "ocax.ocaxpiegraph", {
		// Default options.
		options: {
			source: "",
			rootBudget: 0,
			rootBudgetData: "",
			graphTitle: "",
			loadedRemotely: 0,
		},
		_create: function() {
			//if(!this.options.graphTitle)
			//	this.options.graphTitle = 'A budget';
			this.element.addClass('ocaxjqplot');

			header=$('<div class="ocax_piegraph_header"></div>');
			header.append('<div>'+this.options.graphTitle+'</div>');
			loader=$('<div class="loader_gif"></div>');
			loader.append('<img src="$baseURL/images/preloader.gif"/></div>');
			header.append(loader);
			//header.append('<div style="clear:both"></div>');
			this.element.append(header);

			if(!this.options.rootBudgetData){
				this.options.loadedRemotely = 1;
				getPie(this.options.rootBudget, this.element);

			}else
				createGraph(this.element, this.options.rootBudget, this.options.rootBudgetData);
			
			this.element.find('.graph_container').show();
		},
		// Create a public method.
		source: function() {
				return "$baseURL";
				//return this.options.source;
		},
		rootBudget: function() {
				return this.options.rootBudget;
		},
		loadedRemotely: function() {
				return this.options.loadedRemotely;
		},
	});
})(jQuery);


function getGraphGroup(el){
	return $(el).closest('.ocaxjqplot');
}
function getSource(el){
	return ".";
	group = getGraphGroup(el);
	return $(group).ocaxpiegraph("source");
}
function getRootBudget(el){
	group = getGraphGroup(el);
	return $(group).ocaxpiegraph("rootBudget");
}
function isRemoteGraph(el){
	group = getGraphGroup(el);
	return $(group).ocaxpiegraph("loadedRemotely");
}
function getGraphContainerID(el, budget_id){
	return getGraphGroup(el).attr('id')+'_'+budget_id+'_container';
}
function getGraphID(el, budget_id){
	return getGraphGroup(el).attr('id')+'_'+budget_id+'_graph';
}

$(function() {
	$('.ocaxpiegraph').delegate('.legend_item','click', function() {	
		budget_id = $(this).attr('budget_id');
		getPie(budget_id, this, 1);
		return false;
	});
	$('.ocaxpiegraph').on('mouseleave', '.jqplot-target', function() {
		$('.jqplot-highlighter-tooltip').fadeOut('fast');
	});
});

function getPie(budget_id, clicked_el, slideIn ){
	if($('#'+getGraphContainerID(clicked_el, budget_id)).length > 0 ){
		if(slideIn)
			slideInChild(clicked_el, budget_id);
		return;
	}
	//source = getSource(clicked_el);
	loading_gif = $(clicked_el).parents('.ocaxjqplot').find('.loader_gif');
	$('.loader_gif').hide(); $(loading_gif).show();
	root_budget = getRootBudget(clicked_el);
	
	$.jqplot.config.enablePlugins = true;
	ocaxAPIGetPieData(	budget_id, root_budget, clicked_el,
						function(data){
							createGraph(clicked_el, budget_id, data);
							if(slideIn)
								slideInChild(clicked_el, budget_id);
						}
					);
	return;
}

function ocaxAPIGetPieData(budget_id, root_budget, clicked_el, cfunction){
	$.ajax({
		url: '$baseURL/budget/getPieData/',
		type: 'GET',
		dataType: 'jsonp',
		jsonp: 'callback',
		data: { 'id': budget_id, 'rootBudget_id': root_budget },
		async: false,
		beforeSend: function(){ /* $('.loader_gif').hide(); $(loading_gif).show();*/ },
		complete: function() { /*$('.loader_gif').hide();*/ },
		success: function(data){
			cfunction(data);
		},
		error: function() {
			alert("Error on get Pie Data");
			return;
		}
	});
}

function createGraph(clicked_el, budget_id, data){
	group = getGraphGroup(clicked_el);
	source = getSource(clicked_el);
	
	container_id = getGraphContainerID(clicked_el, budget_id);
	graph_id = getGraphID(clicked_el, budget_id);
	
	graph_container=$('<div id="'+container_id+'" class="graph_container"></div>');
	graph_container.attr('parent_id',data.params.parent_id);
	graph_container.attr('is_parent',data.params.is_parent);
	
	if(data.params.is_parent){
		title=	'<a href="$baseURL/budget/view/'+budget_id+
				'" onclick="js:showBudget('+budget_id+', this);return false;">'+data.params.title+'</a>';
	}else{
		title=	'<a href="$baseURL/budget/view/'+data.params.parent_id+
				'" onclick="js:showBudget('+data.params.parent_id+', this);return false;">'+data.params.title+'</a>';			
	}
	graph_container.append('<div class="graph_title">'+title+'</div>');
	graph_container.append(data.params.budget_details);
			
	graph=$('<div id="'+graph_id+'" class="graph_pie"></div>');
	graph_container.append(graph);
	group.append(graph_container);

	$.jqplot.config.enablePlugins = true;
	createPie(graph_id, data);

	if(data.params.go_back_id){
		
		go_back_to = getGraphContainerID(clicked_el, data.params.go_back_id);
		generic_button_image='';
		if(isRemoteGraph(clicked_el) == 1){
			generic_button_image='style="background-image:url($baseURL/images/prev_budget.png);"';
			back_button='<div class="prev_budget_arrow" '+generic_button_image+' onclick="javascript:goBack(\''+go_back_to+'\');return false;"></div>';
		}else
			back_button='<div class="prev_budget_arrow" onclick="javascript:goBack(\''+go_back_to+'\');return false;"><i class="icon-left-circled"></i></div>';

		$('#'+graph_id).append(back_button);
	}

	legend = $(clicked_el).closest('.jqplot-table-legend-container');
	if($(legend).length > 0){
		scroll = $(legend).scrollTop();
		brothers = group.find('div[parent_id="'+data.params.parent_id+'"]'); // graphs with the same parent as this.
		$.each( brothers, function( key, value ) {
			$(value).find('.jqplot-table-legend-container').scrollTop(scroll);
		});
	}
	return;
}

function createPie(target_div_id, data){
	$.jqplot.config.enablePlugins = true;
	chart= $.jqplot(target_div_id, [data.data], pie_properties);
	target_div = $('#'+target_div_id);

	// fix for IExplorer Does not respect table height property. we wrap it with a <div>
	legend = target_div.find('table');
	legendContainer=$('<div class="jqplot-table-legend-container"></div>');
	legendContainer.attr('style',legend.attr('style'));
	legend.attr('style','');
	legend.before( legendContainer );
	legendContainer.append( legend );

	if(data.params.actual_provision==0){
		target_div.append('<div style="position:absolute;top:20px;left:30px;font-size:20em;color:grey;">0€</div>');
	}else{
		//http://www.kathyw.org/jQPlot/LinkTest.html
		target_div.bind('jqplotDataClick', 
			function (ev, seriesIndex, pointIndex, data) {
				getPie(data[2], this, 1);
				return false;
			}
		);
		target_div.bind('jqplotDataHighlight', function(ev, seriesIndex, pointIndex, data) {$(this).css('cursor','pointer');}); 
		target_div.bind('jqplotDataUnhighlight', function(ev, seriesIndex, pointIndex, data) {$(this).css('cursor','default');});
	}
}

function slideInChild(clicked_el, child_id){
	child_budget_graph_container=$('#'+getGraphContainerID(clicked_el, child_id));
	child_budget_graph_container.hide();
	
	group=getGraphGroup(clicked_el);

	if(child_budget_graph_container.attr('is_parent') == 0){	
		budget_details=child_budget_graph_container.children('.budget_details');
		budget_details.hide();
		group.children('.graph_container').hide();
		child_budget_graph_container.show();
		budget_details.fadeIn(200, function(){ $('.loader_gif').hide(); } );
		child_budget_graph_container.find('.legend_item[budget_id='+child_id+']').css('font-weight','bold');
		addColorKeyToBudgetDetails(child_budget_graph_container, child_id);	
		return;
	}
	group.children('.graph_container:visible').hide("slide",
													{ direction: "left" },
													600,
													function(){
														child_budget_graph_container.fadeIn(200);
														$('.loader_gif').hide();
													;}
					);
}

function addColorKeyToBudgetDetails(graph_container, budget_id){
	item = graph_container.find('span[budget_id='+budget_id+']');
	swatch = $(item).parent().prev('td');
	swatch = swatch.find('.jqplot-table-legend-swatch');
	concept = graph_container.find('.budget_details').find('th:first');
	concept.css('border-left', '5px solid '+swatch.css('background-color'));
}

function goBack(parent_container_id){
	parent_graph_container=$('#'+parent_container_id);
	parent_graph_container.show("slide",{ direction: "left" },	500);
	group=parent_graph_container.closest('.ocaxjqplot');
	group.children(".graph_container").hide();
}

