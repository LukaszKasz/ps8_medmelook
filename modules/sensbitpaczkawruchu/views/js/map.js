if(!window.fetch) {
  var req = new XMLHttpRequest();
  req.open('GET', 'https://cdnjs.cloudflare.com/polyfill/v3/polyfill.js?version=4.8.0&features=fetch', false);
  req.send();
  eval(req.responseText);
}
if(!window.Promise) {
  var req = new XMLHttpRequest();
  req.open('GET', 'https://cdnjs.cloudflare.com/polyfill/v3/polyfill.js?version=4.8.0&features=Promise', false);
  req.send();
  eval(req.responseText);
}

var RuchWidget = function (el, opts, jquery) {
	this.env = {
		baseLink : 'https://ruch-osm.sysadvisors.pl/',
		isSandbox: 0,
		isCodFilter: 0,
		isPointTypeFilter: 0,
		isDeliveryFilter: 0,
        initialDelivery: 0,
		initialType: 'ALLTYPE',
	};

	if (opts.loadCb) this.loadCb = opts.loadCb;
	if (opts.selectCb) this.selectCb = opts.selectCb;
	if (opts.initialAddress) this.initialAddress = opts.initialAddress;
	if (opts.sandbox) this.sandbox = opts.sandbox;
	if (opts.baseLink) this.base = opts.baseLink;
	if (opts.showCodFilter) this.showCodFilter = opts.showCodFilter;
	if (opts.showPointTypeFilter) this.showPointTypeFilter = opts.showPointTypeFilter;
	if (opts.showDeliveryFilter) this.showDeliveryFilter = opts.showDeliveryFilter;
	if (opts.initialDelivery ) this.selectedDelivery = opts.initialDelivery;
	if (opts.initialType) this.selectedType = opts.initialType;
	if (opts.readyCb) this.readyCb = opts.readyCb;
	if (typeof opts.animation !== 'undefined') this.animation = opts.animation;

	this.base = (this.base) ? this.base : this.env['baseLink'];
	this.url = this.base + 'api.php';
	this.el = null;
	this.allpts = [];
	this.pts = null;
	this.pts_ind = null;
	this.apkt = -1;
	this.tune = false;
	this.map = null;
	this.provider = null;
	this.searchMark = null;
	this.listMax = 500;
	this.listLen = 30;
	this.laDegKm = 111.2;
	this.loDegKm = 68.5;
	this.maxDist = 12;
	this.maxTested = 20;
	this.testedShow = 5;
	this.icons = {};
	this.icons_b = {};
	this.type_i = ['R', 'P', 'U', 'A'];
	this.type_png = {'R': 'marker-icon-orlen.png', 'P': 'marker-icon-orlen.png', 'U': 'marker-icon-orlen.png', 'A': 'marker-icon-orlen.png'};
	this.type_txt = {'R': 'Kiosk/Salonik', 'P': 'Punkt partnerski', 'U': 'Punkt partnerski', 'A': 'Orlen Paczka'};
	this.type_filter = {'PSD': 'Punkt Sprzedaży Detalicznej', 'PSP': 'Punkt Sprzedaży Prasy', 'PPP': 'Punkt pozaprasowy', 'APM': 'APM'};
	this.methods = null;
	this.prices = {};
	this.markerClusters = null;
	this.mks = [];
	this.mks_i = {};
	this.tested = [];
	this.selected = null;
	this.type = ['R', 'P', 'U', 'A'];
	this.type_st = ['R', 'P', 'U', 'A'];
	this.type_api = [];
    this.ptmap = {};
	this.map_y = 500;
	this.state = 0;
	this.notfound = false;
	this.filtering = false;
	this.txt_error = 'Wystąpił błąd wczytywania listy punktów. Odśwież stronę aby spróbować ponownie.';
	this.txt_zoomin = 'Przybliż mapę, aby wyświetlić listę punktów';
	this.txt_select = 'Wybierz ten punkt';
	this.txt_search = 'Wpisz miasto lub kod';
	this.pr = 'id-' + Math.floor(new Date().valueOf() * Math.random()) + '-';
	this.showCodFilter = (this.showCodFilter) ? this.showCodFilter : this.env['isCodFilter'];
	this.showPointTypeFilter = (this.showPointTypeFilter) ? this.showPointTypeFilter : this.env['isPointTypeFilter'];
	this.showDeliveryFilter = (this.showDeliveryFilter) ? this.showDeliveryFilter : this.env['isDeliveryFilter'];

	this.cod = 0;
	this.selectedType = (this.selectedType) ? this.selectedType : this.env['initialType'];
	this.selectedDelivery = (this.selectedDelivery) ? this.selectedDelivery : this.env['initialDelivery'];
	this.loadCb = (this.loadCb) ? this.loadCb : null;
	this.readyCb = (this.readyCb) ? this.readyCb : null;
	this.selectCb = (this.selectCb) ? this.selectCb : null;
	this.initialAddress = (this.initialAddress) ? this.initialAddress : '';
	this.animation = (this.animation) ? this.animation : 400;
	this.sandbox = (this.sandbox) ? this.sandbox : this.env['isSandbox'];

    if (typeof(jquery) !== 'undefined') this.jq = jquery;
	else 
	   if (typeof($) !== 'undefined') this.jq = $;
	if (typeof(this.jq) === 'undefined') {
	   alert('Niedostępna biblioteka JQuery');
	   return;
	}
	if (this.el != null) return false;
	this.el = el;
};

RuchWidget.prototype.constructor = RuchWidget;

RuchWidget.prototype.init = function () {
	this.jq('#' + this.el).hide();
	var r1 = '<div id="' + this.pr + 'ruch_container"><div class="ruch_widget" id="' + this.pr + 'ruch_widget"></div><input type="hidden" name="' + this.pr + 'ruch_selpkt" id="' + this.pr + 'ruch_selpkt" /><br/><div class="ruch_title" id="' + this.pr + 'ruch_selpkt_desc" style=""></div></div>';
	this.jq(r1).appendTo(this.jq('#' + this.el));
	var r2 = '<div class="searchBar" id="' + this.pr + 'searchBar"><div class="ruch_widget_inp_wrapper"><input placeholder="' + this.txt_search + '" type="text" id="' + this.pr + 'ruch_widget_inp" class="ruch_widget_inp"/><div class="suggestion_wrapper" id="' + '#' + this.pr + 'suggestion_wrapper"></div></div><button type="submit" id="' + this.pr + 'ruch_widget_but_s" class="ruch_widget_but_s">Pokaż</button><div class="ruch_search_status" id="' + this.pr + 'ruch_search_status"></div><div class="searchBar__location" id="' + this.pr + 'ruch_widget_lok_link">Lokalizuj mnie</div></div><button type="submit" id="' + this.pr + 'ruch_widget_but_all" class="ruch_widget_but_all" style="display: none;">Pokaż wszystkie</button>';
	var r3 = '<div class="ruch_widget_filter"><div id="codFilter" class="ruch_widget_filter_wrapper"><div class="ruch_widget_filter_desc">Pokaż punkty:</div><select id="' + this.pr + 'ruch_widget_filter_f1" name="' + this.pr + 'ruch_widget_filter_f1" class="ruch_widget_filter_f1"><option value="0">Wszystkie</option><option value="1">Tylko obsługujące COD</option></select></div><div id="pointTypeFilter" class="ruch_widget_filter_wrapper"><div class="ruch_widget_filter_desc">Rodzaj punktu:</div><select id="' + this.pr + 'ruch_widget_filter_f2" name="' + this.pr + 'ruch_widget_filter_f2" class="ruch_widget_filter_f1"></select></div><div id="deliveryFilter" class="ruch_widget_filter_wrapper"><div class="ruch_widget_filter_desc">Rodzaj dostawy:</div><select id="' + this.pr + 'ruch_widget_filter_f3" name="' + this.pr + 'ruch_widget_filter_f3" class="ruch_widget_filter_f1"><option value="0">Wszystkie</option><option value="1">Prasa</option><option value="2">Kurier</option></select></div></div><div class="ruch_sidebar" id="' + this.pr + 'ruch_widget_panel_div"></div><div class="map" id="' + this.pr + 'ruch_widget_map" class="ruch_widget_map"></div>';
	this.jq('#' + this.pr + 'ruch_widget').html('<img id="' + this.pr + 'loading-smmap" src="' + this.base + 'img/busy.gif" class="loading-smmap"/>' + r2 + r3 + '');
	this.jq('#' + this.pr + 'ruch_widget').on('click', function (event) {event.stopPropagation();});
	this.jq('#' + this.pr + 'ruch_widget_map').height(this.map_y);
	this.showButtons();
	if(typeof LRW !== 'undefined') this.init2();
	else { 
		var _this = this;
		this.jq.ajax({
			success: function() {
				_this.init2();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log('status=' + textStatus + ", error=" + errorThrown);
				alert('Błąd wczytywania bibliotek');
			},
			url: this.base + 'leaflet/bundle.js',
			dataType: "script",
			showLoader: true,
			cache: true
		});
	}
	this.hideElement();
};

RuchWidget.prototype.init2 = function () {
	this.map = LRW.map(this.pr + 'ruch_widget_map', {
		center: [52.229823, 21.011721],
		zoom: 13,
		gestureHandling: true
	});
	LRW.tileLayer('https://ruch-osm.sysadvisors.pl/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		maxZoom: 17
	}).addTo(this.map);

	var _this = this;
	this.jq('#' + this.pr + 'ruch_widget_lok_link').click(function () {
		_this.getLocation(_this);
	});
	this.jq('#' + this.pr + 'ruch_widget_all_link').click(function () {
		_this.buttonAll();
	});
	this.markerClusters = LRW.markerClusterGroup({
		showCoverageOnHover: false,
		chunkedLoading: true
	});
	if (this.loadCb) this.loadCb();
	this.loadPts();

	var OpenStreetMapProvider = window.GeoSearch.OpenStreetMapProvider;
	this.provider = new OpenStreetMapProvider({
		params: {
			'accept-language': 'pl',
			'countrycodes': 'pl'
		}
	});

	this.jq('#' + this.pr + 'ruch_widget_but_s').click(function (event) {
		event.preventDefault();
		_this.search();
	});
	this.jq('#' + this.pr + 'ruch_widget_inp').keypress(function (event) {
		if(event.which == 13) {
			var selectedAddressId = _this.jq('.suggestion_wrapper > .selected').attr('id');
			if (selectedAddressId)
			{
				selectedAddressIndex = selectedAddressId.slice(-1);
				_this.rewriteSuggestionsToSearchInput(selectedAddressIndex);
			} else {
				_this.search();
				return false;
			}

		}
	});
	this.suggestionVisibility();
	this.jq('#' + this.pr + 'ruch_widget_but_all').click(function (event) {
		event.preventDefault();
		if(!_this.filtering) return false;
		_this.reload();
		return false;
	});
	this.jq('#' + this.pr + 'ruch_widget_filter_f1').change(function (event) {
		event.preventDefault();
		_this.showWidget(parseInt(_this.jq('#' + _this.pr + 'ruch_widget_filter_f1').val()), _this.prices, _this.methods, 0);
		return false;
	});

	this.jq('#' + this.pr + 'ruch_widget_filter_f2').change(function (event) {
		event.preventDefault();
		_this.setPointType(_this.jq('#' + _this.pr + 'ruch_widget_filter_f2').val(), 1);
		return false;
	});

	this.jq('#' + this.pr + 'ruch_widget_filter_f3').change(function (event) {
		event.preventDefault();
		_this.setDelivery(_this.jq('#' + _this.pr + 'ruch_widget_filter_f3').val(), 1);
		return false;
	});
};

RuchWidget.prototype.suggestionVisibility = function () {
	var _this = this;
	//close suggestion start
	_this.jq('.suggestion_wrapper').hide();
	this.jq('#' + this.pr + 'ruch_widget').click(function (event) {
		if (_this.jq(event.target).closest('#' + _this.pr + 'ruch_widget_inp ,#'+ _this.pr + 'suggestion_wrapper').length == 0) {
			_this.jq('.suggestion_wrapper').hide();
		}
	});

	this.jq(document).click(function(evt) {
		_this.jq('.suggestion_wrapper').hide();
	})
	//close suggestion end

	this.jq('#' + this.pr + 'ruch_widget_inp').on('input', function() {
		if(event.which != 13) {
		    if(_this.notfound) {
		        _this.jq('#' + _this.pr + 'ruch_search_status').html('');
		        _this.notfound = false;
		    }
			var userInput = _this.jq('#' + _this.pr + 'ruch_widget_inp').val();
			fittingPktResoult = [];
			if (userInput.length > 2)
			{
				fittingPktResoult = _this.checkAddressInPkt(userInput);
				_this.createSugetionContainer(fittingPktResoult, userInput);
			}

			if (fittingPktResoult.length > 0)
			{
				_this.jq('.suggestion_wrapper').show();
			} else {
				_this.jq('.suggestion_wrapper').hide();
			}
		}
	});
}

RuchWidget.prototype.suggestionEvent = function () {
	suggestionCount = this.jq('.suggestion_wrapper').find('div').length;

	var _this = this;
	for (let i = 0; i < suggestionCount; i++) {
		this.jq('#' + this.pr + 'suggestion_container_' + i).click(function() {
			event.preventDefault();
			return function () {
				_this.rewriteSuggestionsToSearchInput(i);
			}
		}(i))
	};
}

RuchWidget.prototype.rewriteSuggestionsToSearchInput = function (suggestionIndex) {
	var suggestionAddress = this.jq('#' + this.pr + 'suggestion_container_' + suggestionIndex + ' p').html();
	suggestionAddress = suggestionAddress.replaceAll('<span class="highlight">','');
	suggestionAddress = suggestionAddress.replaceAll('</span>','');
	this.jq('#' + this.pr + 'ruch_widget_inp').val(suggestionAddress);
	this.search();
};

RuchWidget.prototype.checkAddressInPkt = function (userInput) {
	var preparedUserInput = this.prepareStringToSearch(userInput);
	var fittingPkt = [];


	if (preparedUserInput == '')
	{
		return fittingPkt;
	}
	var wordArray = preparedUserInput.split(' ');

	this.pts.forEach((pkt) => {
		var fit = true;
		for (let word of wordArray) {
			if (pkt.a_al.toLowerCase().includes(word))
			{
				continue;
			}
			if (pkt.a_c.toLowerCase().includes(word))
			{
				continue;
			}
			if (pkt.a_pc.toLowerCase().includes(word))
			{
				continue;
			}
			fit = false;
			break;
		}

		if (fit)
		{
			fittingPkt.push(pkt);
		}
	});

	return fittingPkt;
}

RuchWidget.prototype.createSugetionContainer = function (fittingPkt, userInput) {
	var suggestion_html = '';
	for (let i = 0; i < fittingPkt.length; i++) {
		var addressString = fittingPkt[i].a_c + ' ' + fittingPkt[i].a_al + ' ' + fittingPkt[i].a_pc;
		highlightAddress = this.sugetionHighLight(addressString, userInput);
		suggestion_html = suggestion_html + '<div class="suggestion" id="'+ this.pr + 'suggestion_container_' + i + '"><p>' + highlightAddress + '</p></div>';
	}

	this.jq('.suggestion_wrapper').html(suggestion_html);
	this.suggestionEvent();
}

RuchWidget.prototype.sugetionHighLight = function (stringToHighLight, userInput) {
	var preparedUserInput = this.prepareStringToSearch(userInput);
	var wordArray = preparedUserInput.split(' ');
	var indexLenghtArray = [];
	for (let word of wordArray) {
		if (word && word != '') {
			indexArray = this.getAllIndexesOfSubstring(stringToHighLight ,word);
			indexLenghtArray.push( {indexArray: indexArray, wordLength: word.length})
		}

	}
	highlightText = userInput;
	if (indexLenghtArray.length) {
		charIndexArray = this.suggestionLetterIndexToHighlight(stringToHighLight, indexLenghtArray);
		highlightText = this.suggestionLetterHighlight(stringToHighLight, charIndexArray);
	}
	return highlightText;
}

RuchWidget.prototype.getAllIndexesOfSubstring = function (searchString, subString) {
	lowerCaseSearchString = searchString.toLowerCase();
	var indexArray = [];
	i = 0
	do {
		substringIndex = lowerCaseSearchString.indexOf(subString, i);

		if (substringIndex != -1)
		{
			indexArray.push(substringIndex);
		}

		i = substringIndex + 1;
	} while (substringIndex != -1);
	return indexArray;
}

RuchWidget.prototype.suggestionLetterIndexToHighlight = function (stringToHighLight, indexLenghtArray) {
	charArray = Array(stringToHighLight.length);
	for (let i = 0; i < charArray.length; i++) {
		charArray[i] = 0;
	};

	indexLenghtArray.forEach((indexLenght) => {
		for (let i = 0; i < indexLenght.indexArray.length; i++) {
			for (let y = 0; y < indexLenght.wordLength; y++) {
				charArray[indexLenght.indexArray[i] + y] = 1;
			}
		}
	});
	return charArray;
}

RuchWidget.prototype.suggestionLetterHighlight = function (stringToHighLight, charIndexArray) {
	highlightText = '';

	for (let i = 0; i < charIndexArray.length; i++) {
		if (charIndexArray[i] == 1) {
			highlightText = highlightText + '<span class="highlight">' + stringToHighLight[i] +'</span>';
		} else {
			highlightText = highlightText + stringToHighLight[i];
		}
	}

	return highlightText;
}

RuchWidget.prototype.prepareStringToSearch = function (value) {
	var str = value.trimStart();
	str = str.trimEnd();
    str = str.replaceAll('.', ' ');
	str = str.replace(/\s\s+/g, ' ');
	str = str.replaceAll(',', '');
	str = str.toLowerCase();
	return str;
}

RuchWidget.prototype.init3 = function (resp) {
	if (resp.error) {
		this.state = 9;
	}
	else {
		this.state = 2;
		this.allpts = resp.pts;
		this.ptmap = resp.pt;
		this.map.addLayer(this.markerClusters);
		LRW.Icon.Default.prototype.options.iconUrl = 'marker-icon.png';
		LRW.Icon.Default.prototype.options.shadowSize = [0,0];
		LRW.Icon.Default.prototype.options.iconRetinaUrl = 'marker-icon.png';
		LRW.Icon.Default.prototype.options.shadowUrl = 'marker-icon.png';
		for (var i = 0; i < this.type_i.length; i++) {
			var t = this.type_i[i];
			this.icons[t] = LRW.icon({
				iconUrl: this.base + 'img/' + this.type_png[t],
				className: 'ruch_widget_icon',
				iconSize: [64, 64],
				iconAnchor: [32, 62],
				popupAnchor: [0, -28]
			});
			this.icons_b[t] = LRW.icon({
				iconUrl: this.base + 'img/' + this.type_png[t],
				className: 'ruch_widget_icon_b',
				iconSize: [64, 64],
				iconAnchor: [32, 62],
				popupAnchor: [0, -28]
			});
		}
		if (this.readyCb) this.readyCb();
	}
};

RuchWidget.prototype.hideElement = function () {

	if (this.showCodFilter == 0) {
        var el = document.getElementById("codFilter"); 
        if(el != null) el.classList.add('hide_element');
	}

	if (this.showPointTypeFilter == 0) {
        var el = document.getElementById("pointTypeFilter"); 
		if(el != null) el.classList.add('hide_element');
	}

	if (this.showDeliveryFilter == 0) {
        var el = document.getElementById("deliveryFilter"); 
        if(el != null) el.classList.add('hide_element');
		
	}
}

RuchWidget.prototype.filterPts = function (setStart) {
	this.pts = [];

    var ptmap_keys = Object.keys(this.ptmap);
    this.jq('#' + this.pr + 'ruch_widget_filter_f2').empty();
    this.jq('#' + this.pr + 'ruch_widget_filter_f2').append('<option value="ALLTYPE">Wszystkie</option>');
    for(var i = 0; i < ptmap_keys.length; i++) {
        var key = ptmap_keys[i];
        if(this.type_st.indexOf(key) == -1) continue;
        for(var j = 0; j < this.ptmap[key].length; j++) {
            this.type_api.push(this.ptmap[key][j]);
            this.jq('#' + this.pr + 'ruch_widget_filter_f2').append('<option value="' + this.ptmap[key][j] + '">' + (this.type_filter[this.ptmap[key][j]] ? this.type_filter[this.ptmap[key][j]] : this.ptmap[key][j]) + '</option>')
        }
    }
    this.jq('#' + this.pr + 'ruch_widget_filter_f2 option[value="' + this.selectedType + '"]').attr('selected', 'selected');
    this.jq('#' + this.pr + 'ruch_widget_filter_f3 option[value="' + this.selectedDelivery + '"]').attr('selected', 'selected');
    this.jq('#' + this.pr + 'ruch_widget_filter_f2').val(this.selectedType);

	var ptsAux = this.allpts;
	ptsAux = this.filterType(ptsAux);
	ptsAux = this.filterDelivery(ptsAux);

    j = 0;
	for (var i = 0; i < ptsAux.length; i++) {
		var p = ptsAux[i];
		if((this.cod == 0) || (p.hasOwnProperty('c') && p.c == 1)) {
			p.j = j;
			this.pts[j] = p;
			j++;
		}
	}

	var _this = this;
	this.showPts();
	this.map.on('popupopen', function(e) {_this.popopen(e.popup);});
	this.map.on('popupclose', function() {_this.popclose();});
	this.map.on('resize', function() {_this.loadList();});
	this.map.on('zoomend', function() {_this.loadList();});
	this.map.on('moveend', function() {_this.loadList();});
	//		this.map.on('resize', function() {_this.loadList();});
	if ((this.initialAddress != '') && setStart) {
		if ((this.pts_ind != null) && (this.pts_ind[this.initialAddress.toUpperCase()] != null)) {
			var po = this.pts_ind[this.initialAddress.toUpperCase()];
			this.map.setView([po.la, po.lo], 15);
		} else {
			this.provider.search({
				query: this.initialAddress
			}).then(function(results) {
				if (results.length != 0) {
					var r = results[0];
					_this.tune = true;
					_this.markerClusters.clearLayers();
					_this.map.setView([r.y, r.x], 15);
					_this.markerClusters.addLayers(_this.mks);
					_this.loadList();
				}
			});
		}
	} else if(!setStart && this.cod) this.tune = true;
	this.loadList();
};

RuchWidget.prototype.filterType = function (pts) {
	let filteredPts;

	if (this.type_api.includes(this.selectedType)) filteredPts = pts.filter((p) => { return p.r === this.selectedType })
	else filteredPts = pts;

	return filteredPts;
}

RuchWidget.prototype.filterDelivery = function (pts) {
	let regex;
	let filteredPts;

	switch (this.selectedDelivery.toString()) {
		case '1':
			regex = new RegExp('-+.*-+[0-9][0-9]-+', 'i');
			filteredPts = pts.filter((d) => { return regex.test(d.id) })
			break;
		case '2':
			// regex = new RegExp('-+.*-+[a-z][a-z]-+', 'i');
			regex = new RegExp('-+.*-+[0-9][0-9]-+', 'i');
			filteredPts = pts.filter((d) => { return !regex.test(d.id)})
			break;
		default:
			filteredPts = pts;
	}

	return filteredPts;
}

RuchWidget.prototype.showWidget = function (cod, prices, methods, setStart) {
    cod = parseInt(cod);
    var chcod = false;
    if (typeof(setStart) === 'undefined') setStart = 1;
    if(this.cod != cod) {
	    this.cod = cod;
	    chcod = true;
	}
	this.prices = prices;
	this.methods = methods;
	this.type_st = Object.keys(prices);
	this.jq('#' + this.pr + 'ruch_widget_filter_f1 option[value="' + (1 - this.cod) + '"]').removeAttr('selected');
	this.jq('#' + this.pr + 'ruch_widget_filter_f1 option[value="' + this.cod + '"]').attr('selected', 'selected');
	this.jq('#' + this.pr + 'ruch_widget_filter_f1').val(this.cod);

	this.reload(setStart);
};

RuchWidget.prototype.setPointType = function (type, needReload) {
	this.jq('#' + this.pr + 'ruch_widget_filter_f2 option[value="' + this.selectedType + '"]').removeAttr('selected');
	this.selectedType = type.toUpperCase();
	if(needReload === 1) this.reload(1);
};

RuchWidget.prototype.setDelivery = function (delivery, needReload) {
	this.jq('#' + this.pr + 'ruch_widget_filter_f3 option[value="' + this.selectedDelivery + '"]').removeAttr('selected');
	this.selectedDelivery = delivery;
	this.jq('#' + this.pr + 'ruch_widget_filter_f3 option[value="' + this.selectedDelivery + '"]').attr('selected', 'selected');
    if(needReload === 1) this.reload(1);
};

RuchWidget.prototype.hideWidget = function () {
	if (this.animation == 0) this.jq('#' + this.el).hide();
	else this.jq('#' + this.el).slideUp(this.animation);
};

RuchWidget.prototype.isVisible = function () {
    return this.jq('#' + this.el).is(":visible");
};

RuchWidget.prototype.show = function () {
	if (this.animation == 0) this.jq('#' + this.el).show();
	else this.jq('#' + this.el).slideDown(this.animation);
	this.map.invalidateSize();
};

RuchWidget.prototype.reload = function (setStart ) {
    if (typeof(setStart) === 'undefined') setStart = 1;
	if(this.state < 2) return;

	this.filtering = false;
	this.jq('#' + this.pr + 'searchBar').show();
	this.jq('.ruch_widget_filter').show();
	this.jq('#' + this.pr + 'ruch_widget_but_all').hide();

	this.pts_ind = null;
	this.mks = [];
	this.mks_i = [];
	this.select(null);
	this.apkt = -1;
	this.markerClusters.clearLayers();
	this.show();
	this.filterPts(setStart);	
};

RuchWidget.prototype.loadList = function () {
	if(this.state == 9) {
		this.jq('#' + this.pr + 'ruch_widget_panel_div').html(this.txt_error);
		return;
	}
	if(this.tune && !this.tuneZoom()) return;
	this.tune = false;
	var l = [];
	var c = this.map.getCenter();
	var bo = this.map.getBounds();
	var n = 0;
    var p1 = null;
    if(this.searchMark) p1 = this.searchMark.getLatLng();
	for (var i = 0; i < this.mks.length; i++) {
		var mk = this.mks[i];
		if (bo.contains(mk.getLatLng())) {
			n++;
			if (n > this.listMax) {
				this.jq('#' + this.pr + 'ruch_widget_panel_div').html(this.txt_zoomin);
				return;
			}
			var html = mk._popup.getContent();
            var dist = '';
			var id = this.jq(html).find('button').attr('id');
			var t = id.split("_");
			var p = this.pts[t[1]];
			var p2 = LRW.latLng(p.la, p.lo);
			var b = null;
			if(p1 && p2) dist = '<br/>Od szukanego punktu: ' + (Math.ceil(p1.distanceTo(p2).toFixed(0)/100) / 10) + ' km';
			else dist = '';
			if (this.apkt != t[1]) b = '<button id="' + this.pr + 'l_' + t[1] + '" class="mapPoint__confirm ruch_widget_l" type="button">' + this.txt_select + '</button>';
			else b = '<div id="' + this.pr + 'ruch_widget_tu" class="ruch_widget_tu">Odbieram tutaj</div>';
			var txt = '<div id="' + this.pr + 'ldiv_' + t[1] + '" class="mapPoint ruch_widget_ldiv"><div class="mapPoint__title">' + this.type_txt[p.t] +
				'</div><div class="mapPoint__street">' + p.a +
				(p.h ? '<br/>' + p.h : '') +
				'</div><div class="mapPoint__time">Otwarte: ' + p.o + '<span class="ruch_widget_distance">' + dist + '</span></p>' +
				'</div><div class="mapPoint__price">Koszt dostawy do punktu: ' + this.prices[p.t] +
				' zł</div>' + b + '</div>';
			//var x = c.lat - p.la;
			//var y = c.lng - p.lo;
			var d = c.distanceTo(p2)
			//var d = x * x + y * y;
			l.push({
				d: d,
				txt: txt
			});
		}
	}
	l.sort(function (a, b) {
		return a.d - b.d;
	});
	var l1 = [];
	var m = l.length;
	if (m > this.listLen) m = this.listLen;
	for (var i = 0; i < m; i++) l1.push(l[i].txt);
	var s = l1.join('');
	this.jq('#' + this.pr + 'ruch_widget_panel_div').html(s);
	var _this = this;
	this.jq('.ruch_widget_l').click(function () {
		var id = _this.jq(this).attr('id');
		var pkt = _this.id2pkt(id);
		if(_this.testSelected(pkt)) {
			_this.map.closePopup();
			var t = id.split("_");
			var oldApkt = _this.apkt;
			_this.apkt = t[1];
			_this.genPopup(t[1]);
			if (oldApkt > -1)
			{
				_this.genPopup(oldApkt);
			}
			_this.loadList();
			_this.select(pkt);
			if(_this.filtering) _this.reload();
		}
		// _this.map.setView([pkt.la, pkt.lo], 18);
		// _this.mks[id.split("_")[1]].openPopup();
	});
	this.jq('.ruch_widget_ldiv').click(function () {
		var p = _this.id2pkt(_this.jq(this).attr('id'));
		_this.map.setView([p.la, p.lo], 15);
	});
	this.jq('.ruch_widget_ldiv').mouseenter(function () {
		var p = _this.id2pkt(_this.jq(this).attr('id'));
		var t = _this.jq(this).attr('id').split("_");
		_this.mks_i[t[1]].setIcon(_this.icons_b[p.t]);
	});
	this.jq('.ruch_widget_ldiv').mouseleave(function () {
		var p = _this.id2pkt(_this.jq(this).attr('id'));
		var t = _this.jq(this).attr('id').split("_");
		_this.mks_i[t[1]].setIcon(_this.icons[p.t]);
	});
};

RuchWidget.prototype.showPts = function () {
	if(this.state == 9) {
		this.jq('#' + this.pr + 'ruch_widget_panel_div').html(this.txt_error);
		return;
	}

	this.mks = [];
	this.mks_i = {};
	this.pts_ind = new Object();
	this.jq('#' + this.pr + 'loading-smmap').show();
	for (var i = 0; i < this.pts.length; i++) this.genPopup(i);
	this.jq('#' + this.pr + 'loading-smmap').hide();
	this.markerClusters.addLayers(this.mks);
};

RuchWidget.prototype.genPopup = function (i) {
	var p = this.pts[i];
	if ((this.type.indexOf(p.t) == -1) || (this.type_st.indexOf(p.t) == -1)) return;
	this.pts_ind[p.id.toUpperCase()] = p;
	var b;
	if (this.apkt != i) {
		b = '<button id="' + this.pr + 'p_' + i + '" class="ruch_widget_p" type="button">Wybierz ten punkt</button>';
		if (this.prices[p.t] != null) b += '<button id="' + this.pr + 'p_' + i + '_b" class="ruch_widget_k1" style="display: none;" type="button">Koszt dostawy do punktu: ' + this.prices[p.t] + ' zł</button>';
	}
	else {
		b = '<button id="' + this.pr + 'p_' + i + '_b" class="ruch_widget_k1" type="button">Koszt dostawy do punktu: ' + this.prices[p.t] + ' zł</button>';			
	}
	var popup = '<div class="mapPoint_popup" id="' + this.pr + 'pdiv_' + i + '"><b>' + this.type_txt[p.t] +
		'</b>' + '<p class="mapPoint_addres">' + p.a + 
		(p.h ? '<br/>' + p.h : '') + '</p>' +
		'<p class="mapPoint_time unset_margin_bottom">Otwarte: ' + p.o + '</p><p class="ruch_widget_distance unset_margin_top">&zwnj;</p><p class="mapPoint_psd">' + p.r + ' ' + p.p + '</p>' + b + '</div>';
	var m;
	if(this.mks_i[i]) m = this.mks_i[i];
	else {
		m = LRW.marker([p.la, p.lo], {
			icon: this.icons[p.t]
		});		
		this.mks.push(m);
		this.mks_i[i] = m;
	}
	m.bindPopup(popup);
};

RuchWidget.prototype.popopen = function (popup) {
	this.loadList();
	var p1 = popup.getLatLng();
	if(p1 && this.searchMark) this.jq('.ruch_widget_distance').html('Od szukanego punktu: ' + (Math.ceil(this.searchMark.getLatLng().distanceTo(p1).toFixed(0)/100) / 10) + ' km');
	var _this = this;
	this.jq('.ruch_widget_p').click(function () {
		var id = _this.jq(this).attr('id');
		var pkt = _this.id2pkt(id);
		if(_this.testSelected(pkt)) {
			_this.jq(this).hide();
			_this.jq('#' + _this.pr + id + '_b').show();
			var t = id.split("_");
			var oldApkt = _this.apkt;
			_this.apkt = t[1];
			_this.genPopup(t[1]);
			if (oldApkt > -1)
			{
				_this.genPopup(oldApkt);
			}
			_this.loadList();
			_this.select(pkt);
			if(_this.filtering) _this.reload();
		}
	});
};

RuchWidget.prototype.popclose = function (e) {
	this.loadList();
};

RuchWidget.prototype.select = function (pkt) {
	this.selected = pkt;
	if(this.selectCb != null) {
		if(pkt != null) pkt.m = this.methods[pkt.t];
		this.selectCb(pkt);
	}
};

RuchWidget.prototype.testSelected = function (pkt) {
	this.testPts([pkt.id]);
	if(this.tested.length > 0) return true;

	var np = [];
	var j = 0;
	var max = this.maxDist * this.maxDist;
	for (var i = 0; i < this.pts.length; i++) {
		var p = this.pts[i];
		if(p.id == pkt.id) continue;
		var loKm = (p.lo - pkt.lo) * this.loDegKm;
		var laKm = (p.la - pkt.la) * this.laDegKm;
		var dS = loKm * loKm + laKm * laKm;
		if((dS <= max) && ((this.cod == 0) || (p.hasOwnProperty('c') && p.c == 1))) {
			np[j] = [dS, p];
			j++;
		}
	}
	np = np.sort(this.cmp);
	var l = np.length;
	if(l > this.maxTested) l = this.maxTested;
	var tp = [];
	j = 0;
	for(var i = 0; i < l; i++) {
		var p = np[i][1];
		tp[j] = p.id;
		j++;
	}
	this.testPts(tp);
	
	this.map.closePopup();
	
	var b1;
	var b2;
	var html;
	if(this.tested.length > 0) {
		b1 = '<button id="' + this.pr + 'f_' + pkt.j + '" class="ruch_widget_f" type="button">Wybierz inny</button>';
		b2 = '<button id="' + this.pr + 'p_' + pkt.j + '" class="ruch_widget_p" type="button">Wybierz ten</button>';		
		html = '<div class="mapPoint_popup" id="' + this.pr + 'popup_full"><b>Uwaga</b><br/><br/>Punkt zapełniony, wybierz inny bo paczka może iść dłużej lub zostać przekierowana<br/><br/>' + b1 + '<br/><br/>' + b2 + '</div>';
	}
	else {
		b2 = '<button id="' + this.pr + 'p_' + pkt.j + '" class="ruch_widget_p" type="button">Wybierz</button>';				
		html = '<div class="mapPoint_popup" id="' + this.pr + 'popup_full"><b>Uwaga</b><br/><br/>Punkt zapełniony, paczka może iść dłużej lub zostać przekierowana<br/><br/>' + b2 + '</div>';
	}
	LRW.popup().setLatLng([pkt.la, pkt.lo]).setContent(html).openOn(this.map);

	var _this = this;
	this.jq('.ruch_widget_p').click(function () {
		var id = _this.jq(this).attr('id');
		var pkt = _this.id2pkt(id);
		_this.map.closePopup();
		var t = id.split("_");
		_this.apkt = t[1];
		_this.genPopup(t[1]);
		_this.loadList();
		_this.select(pkt);
	});

	this.jq('.ruch_widget_f').click(function () {
		_this.map.closePopup();
		_this.pts = [];
		var j = 0;
		var l = _this.tested.length;
		if(l > _this.testedShow) l = _this.testedShow;
		var la = 0;
		var lo = 0;
		for (var i = 0; i < l; i++) {
			var p = _this.pts_ind[_this.tested[i].toUpperCase()];
			_this.pts[j] = p;
			la += 1 * p.la;
			lo += 1 * p.lo;
			j++;
		}
		la = la / l;
		lo = lo / l;
		_this.pts_ind = null;
		_this.mks = [];
		_this.mks_i = [];
		_this.markerClusters.clearLayers();
		_this.filtering = true;
		_this.showPts();
		
		_this.map.setView([la, lo], 15);
		var minLo = 999;
		var maxLo = 0;
		var minLa = 999;
		var maxLa = 0;
		for (var i = 0; i < _this.pts.length; i++) {
			var p = _this.pts[i];
			if(p.lo < minLo) minLo = p.lo;
			if(p.lo > maxLo) maxLo = p.lo;
			if(p.la < minLa) minLa = p.la;
			if(p.la > maxLa) maxLa = p.la;
		}
		_this.map.fitBounds([
			[minLa, minLo],
			[maxLa, maxLo]
		]);

		_this.jq('#' + _this.pr + 'searchBar').hide();
		_this.jq('.ruch_widget_filter').hide();
		_this.jq('#' + _this.pr + 'ruch_widget_but_all').show();
	});

	return false;
};

RuchWidget.prototype.cmp = function (a, b) {
	if (a[0] > b[0]) return 1;
	if (b[0] > a[0]) return -1;

	return 0;
};

RuchWidget.prototype.testPts = function (ids) {
	this.jq('#' + this.pr + 'loading-smmap').show();
	var d = {a: 't', 's': this.sandbox, 'ids': ids};
	var _this = this;
	this.jq.ajax({
		success: function (data) {
			if(data.error == 0) _this.tested = data.tested;
			else _this.tested = ids;
		},
		async: false,
		type: 'POST',
		url: this.url,
		dataType: "json",
		data: JSON.stringify(d),
		showLoader: true,
		complete: function () {
			_this.jq('#' + _this.pr + 'loading-smmap').hide();
		}
	}).fail(function() {
		this.tested = ids;
	});
};

RuchWidget.prototype.id2pkt = function (pid) {
	var t = pid.split("_");
	return this.pts[t[1]];
};

RuchWidget.prototype.loadPts = function () {
	var d = {a: 'f', 's': this.sandbox};
	this.state = 1;
	var _this = this;
	this.jq.ajax({
		success: function (resp) {
			_this.init3(resp);
		},
		type: 'POST',
		url: this.url,
		dataType: "json",
		data: JSON.stringify(d),
		showLoader: true,
		complete: function () {
			_this.jq('#' + _this.pr + 'loading-smmap').hide();
		}
	}).fail(function() {
		this.state = 9;
	});
};

RuchWidget.prototype.search = function () {
	var _this = this;
	if (_this.searchMark != null) _this.searchMark.remove(_this.map);
	var v = this.jq('#' + this.pr + 'ruch_widget_inp').val();


	var foundPktWithAddress = _this.searchValueCompatibilityWithPktAddress(v);
	if (!foundPktWithAddress)
	{
		_this.osmSearch(v);
	}
};

RuchWidget.prototype.searchValueCompatibilityWithPktAddress = function (searchString) {
	var _this = this;
	var fittingPktResoult = _this.checkAddressInPkt(searchString);
	var preparedUserInput = _this.prepareStringToSearch(searchString);
	if(fittingPktResoult.length >= 1) {
	   var p = fittingPktResoult[0];
            _this.setPktOnMap(p.la, p.lo);
            _this.jq('.suggestion_wrapper').hide();
            return true;	   
	}
//	for (var i = 0; i < fittingPktResoult.length; i++) {
//		var p = fittingPktResoult[i];
//		var pktAddress = p.a_c + ' ' + p.a_al + ' ' + p.a_pc;
//		var preparedPktAddress  = _this.prepareStringToSearch(pktAddress);
//		if (preparedPktAddress == preparedUserInput)
//		{
//			_this.setPktOnMap(p.la, p.lo);
//			_this.jq('.suggestion_wrapper').hide();
//			return true;
//		}
//	}

	return false;
}

RuchWidget.prototype.osmSearch = function (searchString) {
	if ((this.pts_ind != null) && (this.pts_ind[searchString.toUpperCase()] != null)) {
		var p = this.pts_ind[searchString.toUpperCase()];
		this.map.setView([p.la, p.lo], 15);
		return;
	}
	var _this = this;
	this.provider.search({
		query: searchString
	}).then(function(results) {
		if (results.length == 0) {
		    _this.jq('#' + _this.pr + 'ruch_search_status').html('Nie znaleziono');
		    _this.notfound = true;
		}
		else {
			var r = results[0];
			_this.setPktOnMap(r.y, r.x, r.label);
			_this.jq('.suggestion_wrapper').hide();
		}
	});
}

RuchWidget.prototype.setPktOnMap = function (la, lo, label ='') {
	var _this = this;
	var popup = label;
	var m = LRW.marker([la, lo]);
	if (label != '')
	{
		m.bindPopup(popup);
	}
	m.addTo(_this.map);
	_this.searchMark = m;
	_this.tune = true;
	_this.markerClusters.clearLayers();
	_this.map.setView([la, lo], 15);
	_this.markerClusters.addLayers(_this.mks);
}

RuchWidget.prototype.toggleType = function (t) {
	if (this.type_st.indexOf(t) != -1) this.type_st.splice(this.type_st.indexOf(t), 1);
	else this.type_st.push(t);
};

RuchWidget.prototype.buttonFilter = function (i) {
	this.toggleType(this.type_i[i - 1]);
	this.showButtons();
	this.markerClusters.clearLayers();
	this.showPts();
};

RuchWidget.prototype.genButtons = function () {
	var s = '<table class="ruch_widget_f">';
	var n = 1;
	for (var i = 1; i <= 9; i++) {
		var t = this.type_i[i - 1];
		if (this.type.indexOf(t) != -1) {
			if (n % 3 == 1) s += '<tr>';
			s += '<td><button onclick="this.buttonFilter(' + i + ')" id="' + this.pr + 'ruch_widget_but_f' + i + '" class="ruch_widget_p1" type="button">' + this.type_t[t] + '</button></td>';
			if (n % 3 == 0) s += '</tr>';
			n++;
		}
	}
	if (n % 3 != 0) s += '</tr>';
	s += '</table>';
	return s;
};

RuchWidget.prototype.buttonAll = function (i) {
	for (var i = 1; i <= 9; i++) {
		var t = this.type_i[i - 1];
		if (this.type_st.indexOf(t) == -1) this.toggleType(t);
	}
	this.showButtons();
	this.markerClusters.clearLayers();
	this.showPts();
};

RuchWidget.prototype.showButtons = function () {
	for (var i = 1; i <= 9; i++) {
		var t = this.type_i[i - 1];
		if (this.type_st.indexOf(t) != -1) {
			this.jq('#' + this.pr + 'ruch_widget_but_f' + i).addClass('ruch_widget_p1');
			this.jq('#' + this.pr + 'ruch_widget_but_f' + i).removeClass('ruch_widget_p2');
		} else {
			this.jq('#' + this.pr + 'ruch_widget_but_f' + i).addClass('ruch_widget_p2');
			this.jq('#' + this.pr + 'ruch_widget_but_f' + i).removeClass('ruch_widget_p1');
		}
	}
};

RuchWidget.prototype.getLocation = function (_this) {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function (p) {
            if (_this.searchMark != null) _this.searchMark.remove(_this.map);
            var m = LRW.marker([p.coords.latitude, p.coords.longitude]).addTo(_this.map);
            _this.searchMark = m;
            _this.tune = true;
            _this.map.setView([p.coords.latitude, p.coords.longitude], 15);
		});
	} else {
	   _this.jq('#' + _this.pr + 'ruch_search_status').html('Funkcja nie obsługiwana przez przeglądarkę');
	}
};

RuchWidget.prototype.tuneZoom = function () {
	var l = 0;
	var b = this.map.getBounds();
	for (var i = 0; i < this.mks.length; i++) {
		var mk = this.mks[i];
		if (b.contains(mk.getLatLng())) l++;
	}
	var z = this.map.getZoom();
	if (l > 0) return true;
	if (z < 8) return true;
	this.map.zoomOut(1);
	return false;
};
