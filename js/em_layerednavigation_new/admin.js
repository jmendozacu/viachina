// depend: jquery.fineuploader.js
(function($) {
	ImagePanel = function(options) {
		this.uploadUrl = options.uploadUrl;
		this.panel = $(options.panel);
		this.grid = $(options.grid);
		this.timer = null;
		this.hoverCell = null;
		this.uploadCell = null;
		
		this.setupPanel();
		this.setupGrid();
	}

	ImagePanel.prototype = {
		setupPanel: function () {
			var self = this;
			this.panel
				.mouseenter(function () { clearTimeout(self.timer); })
				.mouseleave(function () { self.timer = setTimeout(function () { self.panel.hide(); }, 200); })
				.hide();
				
			// add image click
			var uploader = new qq.FineUploaderBasic({
				button: this.panel.find('.btn.add')[0],
				request: {
					endpoint: this.uploadUrl,
					params: { type: 'image', form_key: FORM_KEY }
				},
				validation: {
					allowedExtensions: ['jpeg', 'jpg', 'gif', 'png'],
					sizeLimit: 102400 // 100 kB = 100 * 1024 bytes
				},
				callbacks: { 
					onSubmit: function(id, fileName) {
						self.uploadCell = self.hoverCell;
						self.panel.hide();
						$('#loading-mask').show();
					},
					onComplete: function (id, fileName, responseJSON) {
						$('#loading-mask').hide();
						var cell = self.uploadCell;
						var id = self._findCellObjectId(cell);
						
						// append hidden input for uploaded image & show the uploaded image in cell
						var img = $('<img id="img-'+id+'" src="'+responseJSON.url+'" class="sample"/>');
						var input = $('<input type="hidden" name="options[]" value="'+id+'_'+responseJSON.fileName+'" />');
						cell.html('');
						cell.append(img);
						cell.append(input);
					},
				}
			});

			// remove image click
			this.panel.find('.btn.remove').click(function () {
				var cell = self.hoverCell;
				var id = self._findCellObjectId(cell);
				var input = $('<input type="hidden" name="options[]" value="'+id+'_" />');
				cell.html('');
				cell.append(input);
			});
		},
		
		setupGrid: function () {
			var self = this;
			this.grid.find('>tbody tr').each(function () {
				var cell = $(this).children('td:nth(1)')[0];
				self.setupCell(cell);
			})
		},
		
		setupCell: function (el) {
			var self = this;
			$(el)
				.mouseenter(function () {
					clearTimeout(self.timer);
					self.hoverCell = $(this);

					var position = $(this).offset();
					position.left += 50;
					self.panel.css(position);
					
					// if cell has image: hide add button, show remove button
					if ($(this).find('img').size()>0) {
						self.panel.find('.btn.add').hide();
						self.panel.find('.btn.remove').show();
					} else {
						self.panel.find('.btn.add').show();
						self.panel.find('.btn.remove').hide();
					};
					self.panel.show();
				})
				.mouseleave(function () { self.timer = setTimeout(function () { self.panel.hide(); }, 200); });
		},
		
		_findCellObjectId: function (cell) {
			var el = cell.parent();
			var classList = el[0].className.split(/\s+/);
			for (var i = 0; i < classList.length; i++) {
				if (classList[i].indexOf("r-") !=-1)
					return classList[i].replace('r-', '');
			}
			return '';
		}
	}

})(jQuery);

