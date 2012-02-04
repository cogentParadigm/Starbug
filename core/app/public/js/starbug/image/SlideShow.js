define(["dojo", "dojox", "dojox/image/SlideShow"], function (dojo, dojox) {
	return dojo.declare('starbug.image.SlideShow', dojox.image.SlideShow, {
		showImage: function(idx, /* Function? */callback){
			// summary: Shows the image at index 'idx'.
			// idx: Number
			//      The position of the image in the data store to display
			// callback: Function
			//      Optional callback function to call when the image has finished displaying.
			
			if(!callback && this._slideId){ this.toggleSlideShow(); }
			var _this = this;
			var current = this.largeNode.getElementsByTagName("div");
			this.imageIndex = idx;

			var showOrLoadIt = function() {
				//If the image is already loaded, then show it.
				if (_this.images[idx]) {
					_this.images[idx].style.opacity = 0;
					_this.largeNode.appendChild(_this.images[idx]);
					_this._currentImage = _this.images[idx]._img;
					_this._fitSize();

					var handler = function(a,b,c) {
						if (current.length > 2) _this.hiddenNode.appendChild(current[0]);
						var img = _this.images[idx].firstChild;
						if (img.tagName.toLowerCase() != "img") {img = img.firstChild;}
						title = img.getAttribute("title");

						if(_this._navShowing){
							_this._showNav(true);
						}
						dojo.publish(_this.getShowTopicName(), [{
							index: idx,
							title: title,
							url: img.getAttribute("src")
						}]);
						if(callback) { callback(a,b,c); }
						_this._setTitle(title);
					};
					dojo.fadeIn({
						node: _this.images[idx],
						duration: 1000,
						onEnd: handler
					}).play();
				} else {
					//If the image is not loaded yet, load it first, then show it.
					_this._loadImage(idx, function(){
						dojo.publish(_this.getLoadTopicName(), [idx]);
						_this.showImage(idx, callback);
					});
				}
			};

			//If an image is currently showing, fade it out, then show
			//the new image. Otherwise, just show the new image.   
			if (current && current.length > 0) {
				showOrLoadIt();
			} else {
				showOrLoadIt();
			}
		},
		_setTitle: function(title){
				// summary: Sets the title of the image to be displayed
				// title: String
				//      The String title of the image
				this.titleNode.innerHTML = title;
		}
	});
});
