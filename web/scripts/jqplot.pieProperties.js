var pie_properties = {
	grid:{
			drawGridlines:false,
			background:'transparent',
			drawBorder:false,
			shadow:false,
	},
	legend:{
		show:true,
		placement:"outside",
		location:"se",
		rowSpacing:'0.1em',
		marginBottom:'0px',
		border:'none',
		/*
		rendererOptions:{
			numberColumns:2,
		}
		*/
	},
	seriesColors: [ "#62b5cf", "#a1a150", "#fed675", "#e85c54", "#ea6da2", "#007281",
					"#6b8912", "#b98f00", "#a30132", "#b83187", ],

	//axesDefaults:[],
	seriesDefaults:{
		renderer:$.jqplot.PieRenderer,
		rendererOptions:{
			shadow:false,
			padding:0,
			showDataLabels:true,
			fill: true,
			sliceMargin: 0,
			lineWidth: 0,
			startAngle: -180,
			//dataLabelThreshold:3,
			dataLabelCenterOn:true,
			//"dataLabelPositionFactor":0.6,
			//"dataLabelNudge":0,
			//dataLabels:["Longer","B","C","Longer","None"],
		},
    	highlighter: {
    	    show: true,
			formatString: '%s<br /><span style="display:none">%s%s</span>%s',
    	    tooltipLocation:'ne', 
    	    useAxesFormatters:false,
    	},
	}
}
