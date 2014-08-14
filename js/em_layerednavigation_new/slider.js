/*
 * EM Slider
 *
 * class for range filter
 * Depends on library: EM Layer, Scriptaculous
 *
 */
var EM_Slider = Class.create();
EM_Slider.prototype = {
	initialize: function(options) {
		var config = Object.extend({
			min: 0,
			max: 0,
			from: 0,
			to: 0,
			query: '',
			valTpl: '',
			
			fromHandle: 'from',
			toHandle: 'to',
			track: 'track',
			spans: ['range']
		}, options || { });
		this.config = config;

		this.control = new Control.Slider([config.fromHandle, config.toHandle], config.track, {
			range:$R(config.min, config.max),
			sliderValue:[config.from, config.to],
			restricted:true,
			spans: config.spans,
			onSlide: this.onSlide.bind(this),
			onChange: this.onChange.bind(this)
		});
		
		this.onSlide([config.from, config.to]);
	},
	
	onSlide: function(value) {
		var from, to;
		from = this.formatValue(Math.round(value[0]));
		to = this.formatValue(Math.round(value[1]));
		$('from-val').update(from);
		$('to-val').update(to);
	},
	
	onChange: function(value) {
		var from, to, params;
		from = Math.round(value[0]);
		to = Math.round(value[1]);
		if (from == this.config.from && to == this.config.to)
			return;
		var params = this.getQueryString(from, to);
		em_layer.update(params);	// global layer object
	},
	
	formatValue: function(value) {
		return this.config.valTpl.replace('_value_', value);
	},
	
	getQueryString: function(from, to) {
		if (from == this.config.min && to == this.config.max)
			return this.config.removeUrl;	// get reset query
		return this.config.query.replace('_from_', from).replace('_to_', to);
	}
}