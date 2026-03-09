jQuery(document).ready(function($){
	
	$("#footer h5").click(function() {
		var cur = $(this);
		if(cur.hasClass("active")) {
			cur.removeClass("active");
			cur.next('ul').removeClass("active");
		} else {
			cur.addClass("active");
			cur.next('ul').addClass("active");
		}
	});
	
	$(window).scroll(function(){
		if ($(this).scrollTop() > 60) {
			$("#nav-link").addClass('fixed');
			$('#to-top').fadeIn();
		} else {
			$("#nav-link").removeClass('fixed');
			$('#to-top').fadeOut();
		}
	});
	
	$('#to-top').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});
	
	
	$("#nav-link").click(function() {
		if($("#nav-link").hasClass("active")) {
			$("#nav-inside").fadeOut(0);
			$("#nav-link").removeClass("active");
			$("#nav").removeClass("active");
		} else {
			setTimeout(function() {
				$("#nav-inside").fadeIn(300);
				$("#nav-link").addClass("active");
				$("#nav").addClass("active");
			}, 300);
		}
	});
	
	$(".sidebar-title").click(function() {
		var cur = $(this);
		if($(window).width() < 1000) {
			cur.toggleClass('active');
			cur.closest(".sidebar").find(".sidebar-menu").slideToggle(300);
		}
	});
	
	$("table").each(function() {
		$(this).wrap('<div class="table-wrapper"></div>');
	});
	
	$("#footer-mobile-to-desktop").click(function() {

		$.post(ajaxUrl, { 'xxicms_ajax_action': 'force_desktop', 'desktop': 'yes' }, function(data, textStatus) {
			console.log(data);
		}, "json");
		
	});
	
	
	$("body").on("click", ".collapsible-header", function() {
		var cur = $(this);
		if(cur.closest(".collapsible").find(".collapsible-content").css("display") == "block") {
			cur.find('.collapsible-control').html("<span>Показать</span> ↓");
			cur.closest(".collapsible").find(".collapsible-content").slideUp(300);
		} else {
			cur.find('.collapsible-control').html("<span>Свернуть</span> ↑");
			cur.closest(".collapsible").find(".collapsible-content").slideDown(300);
		}
	});
	
	$("li.current").each(function(){
		$(this).closest('ul').closest('li').addClass('ancestor');
		$(this).closest('ul').closest('li').closest('ul').closest('li').addClass('ancestor');
	});


	$("#nav a").each(function() {
		$(this).html('<span>' + $(this).html() + '</span>');
	});
	
	$("#gallery-chapter-by-object a").each(function() {
		$(this).html('<span>' + $(this).html() + '</span>');
	});

	
	$(".fancybox").fancybox({
		helpers: {
			overlay: { locked: false },
			title : { type : 'inside' }
		}
	});
	
	$('a.fancybox').each( function() {
		var title = $(this).find('img').prop('title');
		var alt = $(this).find('img').prop('alt');
		if ( typeof title !== 'undefined' && 0 != title.length) {
			$(this).attr('title',title);
		}
		else if (typeof alt !== 'undefined' &&  0 != alt.length) {
			$(this).attr('title',alt);
		}
	});

	
	$(".popup-close").click(function(){
		var overlay = $(this).closest(".popup-overlay");
		$(this).closest(".popup-general").fadeOut(300,function(){
			overlay.fadeOut(300);
		});
	});
	
	$(".callback-link-inline, .callback-link, .callback-link a").click(function(e){
		e.preventDefault();
		$(".popup-overlay").fadeIn(300,function(){
			$("#popup-callback input").removeClass('with-error');
			$("#popup-callback input[type=text]").val('');
			$("#popup-callback input[type=tel]").val('');
			$("#popup-callback input[type=email]").val('');
			$("#popup-callback textarea").val('');
			$("#popup-callback .form-error").hide();
			$("#popup-callback .form-ok").hide();
			$("#popup-callback .form-content").show();
			$("#popup-callback").fadeIn(300);
		});
	});
	
	$(".tender-link, .tender-link-inline, .tender-link a").click(function(e){
		e.preventDefault();
		$(".popup-overlay").fadeIn(300,function(){
			$("#popup-tender input").removeClass('with-error');
			$("#popup-tender input[type=text]").val('');
			$("#popup-tender input[type=tel]").val('');
			$("#popup-tender input[type=email]").val('');
			$("#popup-tender textarea").val('');
			$("#popup-tender .form-error").hide();
			$("#popup-tender .form-ok").hide();
			$("#popup-tender .form-content").show();
			$("#popup-tender").fadeIn(300);
		});
	});
	
	$(".popup-overlay").click(function(){
		$(".popup-close").click();
	});
	
	$(".popup-general").click(function(e){
		e.stopPropagation();
	});
	
	if($("#aboutindigits").length > 0) {
		var arrCounters = [];
		var i = 0;
		$("#aboutindigits strong").each(function() {
			var curCounter = $(this);
				curCounter.counter({
				autoStart: false,
				duration: 3000,
				countFrom: 0,
				countTo: parseInt($(this).text()),
				runOnce: false,
				placeholder: "?",
				easing: "easeOutCubic"
			});
			arrCounters.push(curCounter);
			i++;
		});

		$(window).scroll(function(){
			$.doTimeout( 'scroll', 250, function(){
				startCounters(arrCounters);
			});
		});
		
		function startCounters(arrCounters) {
			$.each(arrCounters,function(){
				$(this).counter('start');
			});
		}
	}
	
	
	
	
	/* Calc Vent */
    $("body").on("keydown", ".numberonly", function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 191]) !== -1 ||
            (e.keyCode == 65 && e.ctrlKey === true) ||
            (e.keyCode == 67 && e.ctrlKey === true) ||
            (e.keyCode == 88 && e.ctrlKey === true) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 return;
        }

        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        $("#total-value").html("");
    });
	
    $("body").on("keydown", ".integeronly", function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
            (e.keyCode == 65 && e.ctrlKey === true) ||
            (e.keyCode == 67 && e.ctrlKey === true) ||
            (e.keyCode == 88 && e.ctrlKey === true) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        $("#total-value").html("");
    });
	

	
	$("#container").append('<div id="callout"></div>');
	$(".calc-system-type").each(function(){
		$(this).append('<svg viewBox="0 0 20 20"><path d="M 10.082444,1.2654184 A 8.9175444,7.7603059 0 0 0 1.1644749,9.0251838 8.9175444,7.7603059 0 0 0 5.7504124,15.806434 l -0.025391,3.464843 2.7246094,-2.623046 a 8.9175444,7.7603059 0 0 0 1.6328122,0.138672 8.9175444,7.7603059 0 0 0 8.917969,-7.7617192 8.9175444,7.7603059 0 0 0 -8.917969,-7.7597654 z m -1.5996097,2.7578125 3.1992187,0 0,2.9257813 -3.1992187,0 0,-2.9257813 z m 0,4.4238279 3.1992187,0 0,5.5820312 -3.1992187,0 0,-5.5820312 z" /></svg>');
	});
	$(".calc-system-type").on("mouseenter","svg",function(){
		if($(window).width() > 1000) {
			if($(window).width() < 1200) {
				calloutWidth = '150px';
			} else {
				calloutWidth = '200px';
			}
			var curText = $(this).closest(".calc-system-type").data("clarification");
			$("#callout").html(curText).fadeIn(150).css("top",$(this).offset().top + "px").css("right","auto").css("width",calloutWidth).css("left",$(this).offset().left + 35 +"px");
		}
	});
	
	$(".calc-system-type").on("click","svg",function(e){
		e.stopPropagation();
		if($(window).width() < 1000) {
			var curText = $(this).closest(".calc-system-type").data("clarification");
			$("#callout").html(curText).fadeIn(150).css("top",$(this).offset().top + "px").css("right","20px").css("width","auto").css("left","20px");
		}
	});
	
	$("#container").click(function() {
		$("#callout").html('').fadeOut(150);
	});
	$(window).resize(function() {
		$("#callout").html('').fadeOut(150);
	});
	
	$(".calc-system-type").on("mouseleave","svg",function(){
		if($(window).width() > 1000) {
			$("#callout").fadeOut(150).css("top","-100px").css("right","50%").html("");
		}
	});
	
	$("body").on("blur",".numberonly",function(){
		var minValue = parseFloat($(this).data("min"));
		var maxValue = parseFloat($(this).data("max"));
		var curVal = parseFloat($(this).val());
        if((minValue != "NaN")&&(curVal < minValue)) {
			$(this).val(minValue);
		}
        if((maxValue != "NaN")&&(curVal > maxValue)) {
			$(this).val(maxValue);
		}
		$("#total-value").html("");
	});
	$(".calc-building-type").click(function(){
		$(".calc-building-type").removeClass("active");
		$(this).addClass("active");
		$("#total-value").html("");
		if($(this).data("coefficient") == 50) {
			$("#calc-title-area").html("Укажите площадь зеркала воды");
		} else {
			$("#calc-title-area").html("Укажите площадь помещения");
		}
	});
	$(".calc-system-type").click(function(){
		$(this).toggleClass("active");
		$("#total-value").html("");
	});
	$("#calc-vent-do").click(function(e){
		e.preventDefault();
		var resultArea = 0;
		var sum = 0;
		var discount = 1;
		if($(".calc-system-type.active").length > 0) {
			$(".calc-system-type.active").each(function(){
				if($(this).data("argument")=="volume") {
					sum += parseFloat($(this).data("coefficient"));
				} else if($(this).data("argument")=="area") {
					resultArea = parseFloat(parseFloat($("#calc-vent-area").val()) * parseFloat($(this).data("coefficient")));
				}
			});
		}
		var ventArea = parseFloat($("#calc-vent-area").val());
		if($(".calc-building-type.active").data("argument")=="volume") {
			var result = parseFloat($(".calc-building-type.active").data("coefficient")) * parseFloat($("#calc-vent-area").val()) * 3 * sum;
			if( ( ventArea > 199 ) && ( ventArea < 401 ) ) {
				discount = 0.9;
			}
			if( ( ventArea > 400 ) && ( ventArea < 601 ) ) {
				discount = 0.8;
			}
			if( ( ventArea > 600 ) && ( ventArea < 1001 ) ) {
				discount = 0.7;
			}
			if( ventArea > 1000 ) {
				discount = 0.6;
			}
		} else if($(".calc-building-type.active").data("argument")=="area") {
			var result = parseFloat($(".calc-building-type.active").data("coefficient")) * parseFloat($("#calc-vent-area").val()) * sum;
			if( ventArea < 21 ) {
				discount = 1.0;
			}
			if( ( ventArea > 20 ) && ( ventArea < 41 ) ) {
				discount = 0.7;
			}
			if( ( ventArea > 40 ) && ( ventArea < 81 ) ) {
				discount = 0.6;
			}
			if( ventArea > 80 ) {
				discount = 0.5;
			}
		}
		
		console.log('discount = ' + discount);
		result = result + resultArea;
		
		result = discount*result;
		
		if((result != "NaN") && (result > 0)) {
			var cvrResult = Math.ceil(result);
		
				var cvrContent = '<table><tr><th>№</th><th>Наименование</th><th>Ед. изм</th><th>Стоимость, руб.</th></tr>';

 				cvrContent += '<tr><td>1.</td><td>Основное оборудование: вентиляционная установка, вентиляторы, теплообменники и т.п.</td><td>комп.</td><td>' + addSpaces(""+Math.ceil(0.35*cvrResult)) + '</td></tr>';
 				cvrContent += '<tr><td>2.</td><td>Воздуховоды, решетки, изоляция, крепежный и расходный материал </td><td>комп.</td><td>' + addSpaces(""+Math.ceil(0.30*cvrResult)) + '</td></tr>';
 				cvrContent += '<tr><td>3.</td><td>Автоматика: щиты управления, датчики, кабельная продукция и т.п.</td><td>комп.</td><td>' + addSpaces(""+Math.ceil(0.10*cvrResult)) + '</td></tr>';
 				cvrContent += '<tr><td>4.</td><td>Монтажные и пусконаладочные работы</td><td>работа</td><td>' + addSpaces(""+Math.ceil(0.25*cvrResult)) + '</td></tr>';
 				cvrContent += '<tr><td colspan="3">ИТОГО:</td><td><span id="total-value">' + addSpaces(""+cvrResult) + '</span></td></tr></table>';
				$("#calc-vent-result").html('РАСЧЕТ СИСТЕМЫ ВЕНТИЛЯЦИИ ПО ВЫБРАННЫМ ПАРАМЕТРАМ:<div class="h-25"></div>' + cvrContent);
			
			
			$("#calc-send-order").slideDown(300);
			target = $("#calc-do-bookmark");
			if (target.length) {
				$('html,body').animate({
				scrollTop: target.offset().top
				}, 500);
				return false;
			}
		} else {
			$("#total").html('ДАННЫЕ ВВЕДЕНЫ НЕВЕРНО!');
			$("#calc-send-order").slideUp(300);
		}
	});
	
	$(".room-count-dec").click(function(){
		var curVal = parseInt($(".room-count").val());
		if(curVal < 2) {
			$(".room-count").val(1);
		} else {
			$(".room-count").val(curVal-1);
		}
		changeRoomCount(curVal);
	});
	$(".room-count-inc").click(function(){
		var curVal = parseInt($(".room-count").val());
		var curMax = parseInt($(".room-count").data("max"));
		if(curVal > curMax-1) {
			$(".room-count").val(curMax);
		} else {
			$(".room-count").val(curVal+1);
		}
		changeRoomCount(curVal);
	});
	
	$(".room-count").change(function(){
		changeRoomCount(curVal);
	});
	
	function changeRoomCount(curVal) {
		var html = [];
		var htmlItem = '<div class="calc-item"><div class="calc-item-error">Заполните все поля!</div>';
		htmlItem += '<div class="row">';
		htmlItem += '<div class="col half"><div class="calc-title">Площадь помещения №[itemNum], м²</div></div>';
		htmlItem += '<div class="col half"><input class="calc-vent-area numberonly" type="text" data-max="100" placeholder="max 100 м²"></div>';
		htmlItem += '</div><div class="h-25"></div>';
		htmlItem += '<div class="row">';
		htmlItem += '<div class="col half"><div class="calc-title">Тип внутреннего блока</div><div class="calc-notes">&nbsp;</div>';
		htmlItem += '<div class="calc-block-type calc-checkbox" data-type="1">Настенный</div>';
		htmlItem += '<div class="calc-block-type calc-checkbox" data-type="2">Канальный</div>';
		htmlItem += '<div class="calc-block-type calc-checkbox" data-type="3">Кассетный</div>';
		htmlItem += '</div><div class="col half"><div class="calc-title">Инсоляция</div><div class="calc-notes">cтепень освещенности помещения солнцем</div>';
		htmlItem += '<div class="calc-insolation-type calc-checkbox" data-type="1">Низкая</div>';
		htmlItem += '<div class="calc-insolation-type calc-checkbox" data-type="2">Средняя</div>';
		htmlItem += '<div class="calc-insolation-type calc-checkbox" data-type="3">Высокая</div>';
		htmlItem += '</div></div><div class="calc-delimiter"></div></div>';
		for(i=0;i<parseInt($(".room-count").val());i++) {
			html.push(htmlItem.replace("[itemNum]",(i+1)));
		}
		$("#calc-items-wrapper").html(html.join(''));
		$("#calc-cond-result").html('');
		return true;
	}
	
	$("#calc-cond-wrapper").on("click",".calc-block-type",function(){
		$(this).closest(".calc-item").find(".calc-block-type").removeClass("active");
		$(this).addClass("active");
	});
	$("#calc-cond-wrapper").on("click",".calc-insolation-type",function(){
		$(this).closest(".calc-item").find(".calc-insolation-type").removeClass("active");
		$(this).addClass("active");
	});
	$("#calc-cond-wrapper").on("click",".calc-brand-type",function(){
		$(".calc-brand-type").removeClass("active");
		$(this).addClass("active");
	});
	
	$("#calc-cond-do").click(function(e){
		e.preventDefault();
		$(".calc-item").each(function(){
			var valCount = 0;
			valCount += $(this).find(".calc-block-type.active").length;
			valCount += $(this).find(".calc-insolation-type.active").length;
			if((valCount < 2)||("" == $(this).find(".calc-vent-area").val().trim())) { 
				$(this).addClass("with-error");
			} else {
				$(this).removeClass("with-error");
			}
			if($("#calc-brand").find(".active").length == 0){
				$("#calc-brand").addClass("with-error");
			} else {
				$("#calc-brand").removeClass("with-error");
			}
		});
		if($("#calc-cond-wrapper").find(".with-error").length > 0) {
			$("#calc-cond-error").show();
			$("#calc-cond-result").hide();
			$("#calc-send-order").slideUp(300);
		} else {
			
			/* Calculating Result BEGIN */
			$.ajaxSetup({ cache: false });
			$.getJSON( homeUrl + "data/json/calc-cond.json", function( data ) {
				if($(".calc-brand-type.active").data("type") == "mitsubishi") {
					arrBrandFilter = data.mitsubishi.contents;
				} else {
					arrBrandFilter = data.toshiba.contents;
				}
				var arrResult = [];
				var rowCount = $(".calc-item").length;
				var curConsumables = 0;
				var curMounting = 0
				var curTotal = 0;
				for(i=0;i<rowCount;i++){
					var curArea = $(".calc-item").eq(i).find(".calc-vent-area").val();
					var curBlockType = parseInt($(".calc-item").eq(i).find(".calc-block-type.active").data("type"));
					var curInsolation = parseInt($(".calc-item").eq(i).find(".calc-insolation-type.active").data("type"));
					if(1 == parseFloat($(".calc-item").eq(i).find(".calc-block-type.active").data("type"))) {
						arrTypeFilter = arrBrandFilter.wall;
					}
					if(2 == parseFloat($(".calc-item").eq(i).find(".calc-block-type.active").data("type"))) {
						arrTypeFilter = arrBrandFilter.ducted;
					}
					if(3 == parseFloat($(".calc-item").eq(i).find(".calc-block-type.active").data("type"))) {
						arrTypeFilter = arrBrandFilter.cassette;
					}
					var nums = [];
					$.each(arrTypeFilter,function(){
						var cur = $(this);
						nums.push(parseFloat(cur[0].index));
					});
					if(curInsolation > 2) {
						var closestNum = getNextNum(parseFloat(curArea),nums);
					} else {
						var closestNum = getClosestNum(parseFloat(curArea),nums);
					}
					var curArrIndex = nums.indexOf(closestNum);
 					curID = arrTypeFilter[curArrIndex].id;
 					curTitle = arrTypeFilter[curArrIndex].model;
 					curUnit = arrTypeFilter[curArrIndex].unit;
					if(typeof(arrResult[curID]) != 'undefined') {
						curCount = parseFloat(arrResult[curID][2]) + 1;
					} else {
						curCount = 1;
					}
 					curCost = parseFloat(arrTypeFilter[curArrIndex].cost)*curCount;
					curConsumables += parseFloat(arrTypeFilter[curArrIndex].consumables);
					curMounting += parseFloat(arrTypeFilter[curArrIndex].mounting);
					curTotal += parseFloat(arrTypeFilter[curArrIndex].cost);
					arrResult[curID] = [curTitle,curUnit,curCount,curCost];
					console.log('curTotal='+curTotal);
				}
				curResTotal = curTotal + curConsumables + curMounting;
				curResTotal = curResTotal.toFixed(2);
				var ccrContent = '<table><tr><th>№</th><th>Наименование</th><th>Ед. изм</th><th>Кол-во</th><th>Сумма, $</th></tr>';
				var i=0;
				for(var key in arrResult) {
					i++;
					rowResult = arrResult[key];
  					ccrContent += '<tr><td>' + i + '</td><td style="text-align:left;font-size:14px;">' + rowResult[0] + '</td><td>' + rowResult[1] + '</td><td>' + rowResult[2] + '</td><td>' + rowResult[3] + '</td></tr>';
				}
				ccrContent += '<tr><td>' + (i+1) +'</td><td style="text-align:left;font-size:14px;">Расходные материалы</td><td></td><td></td><td>' + curConsumables + '</td></tr>';
				ccrContent += '<tr><td>' + (i+2) +'</td><td style="text-align:left;font-size:14px;">Монтажные работы</td><td></td><td></td><td>' + curMounting + '</td></tr>';
				ccrContent += '<tr><td colspan="4">ИТОГО:</td><td><span id="calc-total">' + curResTotal + '</span></td></tr></table>';
				$("#calc-cond-result").html('РАСЧЕТ СИСТЕМЫ КОНДИЦИОНИРОВАНИЯ ПО ВЫБРАННЫМ ПАРАМЕТРАМ:<div class="h-25"></div>' + ccrContent);
				$("#calc-cond-error").hide();
				$("#calc-cond-result").show();
				$("#calc-send-order").slideDown(300,function() {
					if ($("#calc-do-bookmark").length > 0) {
						$('html,body').animate({
							scrollTop: $("#calc-do-bookmark").offset().top + $(window).scrollTop()
						}, 500);
						return false;
					}
				});
				
			});
	
			/* Calculating Result END */
			
		}
	});
	
	/* Cals Bassein */
	$(".calc-bassein-type").click(function(){
		$(".calc-bassein-type").removeClass("active");
		$(this).addClass("active");
	});
	$(".calc-bassein-vent-type").click(function(){
		$(".calc-bassein-vent-type").removeClass("active");
		$(this).addClass("active");
	});
	$(".calc-office-insolation").click(function(){
		$(".calc-office-insolation").removeClass("active");
		$(this).addClass("active");
	});
	
	/*
		Рассчетные данные (не показываем пользователю)		
		intensityOfMoistureEmissions = Интенсивность влаговыделений	м/ч	21.0
		waterVaporPressureWaterTemperature = Давление водяных паров насышенного воздуха, равной заданной температуре воды	Па	3782.0
		waterVaporPressureIndoor = Давление водяных паров воздуха в помещении	Па	4246.0
		waterVaporPartialPressure = Парциальное давление водяных паров при заданных темп и отн влажности воздуха 	Па	2335.3
		RelativeHumiditySummer = Относительная влажность ЛЕТО	%	55.0
		RelativeHumidityWinter = Относительная влажность ЗИМА	%	45.0
		gasСonstant = Газовая постоянная	кДж/(кг*К)	461.5
		averageTOfWaterAndAirK = Среднеарифмитическая температура воды и воздуха	К	302.2
		partialWaterVaporPressureOutdoorSummer = Парциальное давление водяного пара в наружном воздухе летом	Па	14.7
		moistureContentIndoorSummer = Влагосодержание в помещении ЛЕТО	г/кг	14.1
		moistureContentOutdoorSummer = Влагосодержание на улице ЛЕТО	г/кг	9.2
		moistureContentIndoorWinter = Влагосодержание в помещении ЗИМА	г/кг	10.5
		moistureContentOutdoorWinter = Влагосодержание на улице ЗИМА	г/кг	0.5
		dehumidifierElectricPower = Электрическая мощность осушителя	кВт/л/ч	0.50
		airDensity28 = Плотность воздуха при +28		1.2
		airDryingMinExchange = Осушение воздуха при минимальном воздухообмене	кг/час	2.4
		outdoorTemperatureWinter = Наружная температура ЗИМА		-28.0
		moistureEmissionInOperatingMode = Влаговыделения в рабочем режиме	кг/ч	5.4
		? = Проверка		4.6
		usingRate = Коэф занятости: 0,3 - небольшой частный басс, 0,4 - небол обществен, 0,5 - большой обществ		0.3
		airAmountPerVisitor = Кол-во свежего воздуха для одного посетителя	м3/час	80.0

	 */
	
	var intensityOfMoistureEmissions;
	var waterVaporPressureWaterTemperature;
	var waterVaporPressureIndoor;
	var waterVaporPartialPressure;
	var relativeHumiditySummer = 55;
	var relativeHumidityWinter = 45;
	var gasСonstant = 461.52;
	var averageTOfWaterAndAirK;
	var partialWaterVaporPressureOutdoorSummer;
	var moistureContentIndoorSummer = 14.1;
	var moistureContentOutdoorSummer;
	var moistureContentIndoorWinter = 10.5;
	var moistureContentOutdoorWinter = 0.5;
	var dehumidifierElectricPower = 0.5;
	var airDensity28 = 1.2;
	var airDryingMinExchange;
	var outdoorTemperatureWinter;
	var moistureEmissionInOperatingMode;
	var usingRate = 0.3;
	var airAmountPerVisitor = 80;
	
	var arrRegions = {
		1:["Республика Адыгея",17.4,-19],
		2:["Республика Башкортостан",15.4,-37],
		3:["Республика Бурятия",14,-37],
		4:["Республика Алтай ",12.4,-39],
		5:["Республика Дагестан",19.8,-19],
		6:["Республика Ингушетия",18.7,-18],
		7:["Кабардино-Балкарская Республика",17.3,-18],
		8:["Республика Калмыкия",13.7,-23],
		9:["Республика Карачаево-Черкессия",16.2,-18],
		10:["Республика Карелия",13.5,-29],
		11:["Республика Коми",13.4,-36],
		12:["Республика Марий Эл",15,-34],
		13:["Республика Мордовия",15.1,-30],
		14:["Республика Саха (Якутия)",13.5,-54],
		15:["Республика Северная Осетия-Алания",17.3,-18],
		16:["Республика Татарстан",15,-34],
		17:["Республика Тыва",12.6,-47],
		18:["Удмуртская Республика",14.5,-34],
		19:["Республика Хакасия",15,-40],
		21:["Чувашская Республика",15.3,-32],
		22:["Алтайский край",15.9,-39],
		23:["г. Сочи и Краснодарский край",17.9,-19],
		24:["Красноярский край",15.1,-40],
		25:["Приморский край",19,-24],
		26:["Ставропольский край",16.1,-19],
		27:["Хабаровский край",18.2,-31],
		28:["Амурская область",19.2,-34],
		29:["Архангельская область",14.1,-31],
		30:["Астраханская область",17.1,-23],
		31:["Белгородская область",14.9,-23],
		32:["Брянская область",15,-26],
		33:["Владимирская область",15,-28],
		34:["Волгоградская область",15.1,-26],
		35:["Вологодская область",14.2,-32],
		36:["Воронежская область",15,-26],
		37:["Ивановская область",14.8,-31],
		38:["Иркутская область",14.9,-36],
		39:["Калининградская область",15,-19],
		40:["Калужская область",14.9,-27],
		41:["Камчатский край",13,-27],
		42:["Кемеровская область",15.3,-39],
		43:["Кировская область",14.1,-33],
		44:["Костромская область",14.9,-31],
		45:["Курганская область",14.9,-37],
		46:["Курская область",15,-26],
		47:["г. Санкт-Петербург и Ленинградская область",14.8,-29],
		48:["Липецкая область",15,-27],
		49:["Магаданская область",11.9,-29],
		50:["г. Москва и Московская область",14.7,-28],
		51:["Мурманская область",11.8,-27],
		52:["Нижегородская область",14.8,-32],
		53:["Новгородская область",15.1,-27],
		54:["Новосибирская область",15.6,-39],
		55:["Омская область",15.4,-37],
		56:["Оренбургская область",14.2,-31],
		57:["Орловская область",14.9,-26],
		58:["Пензенская область",15,-29],
		59:["Пермский край",14.6,-35],
		60:["Псковская область",14.9,-27],
		61:["Ростовская область",17.6,-22],
		62:["Рязанская область",14.9,-27],
		63:["Самарская область",14.7,-30],
		64:["Саратовская область",14.6,-27],
		65:["Сахалинская область",15.7,-24],
		66:["Свердловская область",14.7,-35],
		67:["Смоленская область",14.9,-27],
		68:["Тамбовская область",15,-28],
		69:["Тверская область",15,-29],
		70:["Томская область",15.4,-40],
		71:["Тульская область",14.9,-27],
		72:["Тюменская область",13.5,-45],
		73:["Ульяновская область",15,-31],
		74:["Челябинская область",14.7,-34],
		75:["Забайкальский край",15.4,-45],
		76:["Ярославская область",14.8,-31],
		79:["Еврейская автономная область",20.1,-40],
		80:["Ненецкий автономный округ",13,-46],
		81:["Ханты-Мансийский автономный округ - Югра",13.5,-40],
		82:["Чукотский автономный округ",11,-45],
		83:["Ямало-Ненецкий автономный округ",12.5,-46],
		84:["Чеченская республика",18.7,-18]
	};

	var arrTempPress = {
		15:1705,
		16:1817,
		17:1937,
		18:2064,
		19:2197,
		20:2338,
		21:2488,
		22:2644,
		23:2809,
		24:2984,
		25:3168,
		26:3363,
		27:3567,
		28:3782,
		29:4005,
		30:4246,
		31:4492,
		32:4755,
		33:5030,
		34:5319,
		35:5623,
		36:5941,
		37:6275,
		38:6626,
		39:6993,
		40:7377
	};
	
	
	var dropdown = $('select#select-region');
    for(var key in arrRegions) {
        var entry = $('<option>').attr('value', key).html(arrRegions[key][0]);
        dropdown.append(entry);
	}
	dropdown.val(50);
	
	
	$("#calc-bassein-do").click(function(e){
		e.preventDefault();
		
		/* Init */
		var waterArea = 0;
		var roomArea = 0;
		var ceilingHeight = 0;
		var visitorsAmount = 0;
		var waterTemp = 0;
		var airTemp = 0;
		var intensityOfMoistureEmissions = 0;
		var waterVaporPressureWaterTemperature = 0;
		var regionId = 0;
		var waterVaporPressureIndoor = 0;
		var ventPerformanceSummer;
		var ventPerformanceWinter;
		var heaterPower;
		var dryerCapacity;
		var dryerElectricConsumption;
		
		/* Populate by data */
		waterArea = $("#calc-bassein-wrapper .calc-water-area").val();
		roomArea = $("#calc-bassein-wrapper .calc-total-area").val();
		ceilingHeight = $("#calc-bassein-wrapper .calc-ceil-height").val();
		visitorsAmount = $("#calc-bassein-wrapper .calc-visitors-num").val();
		waterTemp = $("#calc-bassein-wrapper #calc-water-temp").val();
		airTemp = $("#calc-bassein-wrapper #calc-air-temp").val();
		intensityOfMoistureEmissions = $("#calc-bassein-wrapper .calc-bassein-type.active").data("coefficient");
		regionId = $("#select-region").val();
		systemType = $("#calc-bassein-wrapper .calc-bassein-vent-type.active").data("type");
		
		/* Calculate */
		waterVaporPressureWaterTemperature = arrTempPress[waterTemp];
		waterVaporPressureIndoor = arrTempPress[airTemp];
		waterVaporPartialPressure = (relativeHumiditySummer * waterVaporPressureIndoor) / 100;
		averageTOfWaterAndAirK = (parseFloat(waterTemp) + 273.15 + parseFloat(airTemp) + 273.15) / 2;
		partialWaterVaporPressureOutdoorSummer = arrRegions[$("#select-region").val()][1];
		moistureContentOutdoorSummerPrecise = 0.622*(partialWaterVaporPressureOutdoorSummer*100/(101080-partialWaterVaporPressureOutdoorSummer*100)*1000);
		moistureContentOutdoorSummer = Math.round(10 * 0.622*(partialWaterVaporPressureOutdoorSummer*100/(101080-partialWaterVaporPressureOutdoorSummer*100)*1000))/10;
		ventilationRateSanitaryStandards = visitorsAmount*airAmountPerVisitor;
		airDryingMinExchangePrecise = ventilationRateSanitaryStandards*airDensity28*(moistureContentIndoorSummer-moistureContentOutdoorSummer)/1000;
		airDryingMinExchange = Math.round(10 * ventilationRateSanitaryStandards*airDensity28*(moistureContentIndoorSummer-moistureContentOutdoorSummer)/1000)/10;
		outdoorTemperatureWinter = arrRegions[$("#select-region").val()][2];
		moistureEmissionInOperatingModePrecise = intensityOfMoistureEmissions/(gasСonstant*averageTOfWaterAndAirK)*(waterVaporPressureWaterTemperature-waterVaporPartialPressure)*waterArea;
		moistureEmissionInOperatingMode = Math.round(10 * intensityOfMoistureEmissions/(gasСonstant*averageTOfWaterAndAirK)*(waterVaporPressureWaterTemperature-waterVaporPartialPressure)*waterArea) / 10;
		ventPerformanceSummer = Math.round(10 * moistureEmissionInOperatingModePrecise/(moistureContentIndoorSummer-moistureContentOutdoorSummerPrecise)*1000/airDensity28)/10;
		ventPerformanceWinter = Math.round(10 * moistureEmissionInOperatingModePrecise/(moistureContentIndoorWinter-moistureContentOutdoorWinter)*1000/airDensity28) / 10;
		heaterPowerVent = Math.round(10 * ventPerformanceWinter/3600*1.2*1.005*(airTemp-outdoorTemperatureWinter)) / 10;
		heaterPowerVentDry = Math.round(10 * ventilationRateSanitaryStandards/3600*1.005*1.2*(airTemp-outdoorTemperatureWinter)) / 10;
		dryerCapacityPrecise = moistureEmissionInOperatingModePrecise - airDryingMinExchange;
		dryerCapacity = Math.round(10 * (moistureEmissionInOperatingModePrecise - airDryingMinExchangePrecise)) / 10;
		dryerElectricConsumption = Math.round(10 * (dryerCapacityPrecise * dehumidifierElectricPower)) / 10;
		
		var calcBasseinResult = '';
		
		var errorCount = 0;
		
		if(isNaN(parseFloat(waterArea))) errorCount++;
		if(isNaN(parseInt(visitorsAmount))) errorCount++;
		if(isNaN(parseInt(waterTemp))) errorCount++;
		if(isNaN(parseInt(airTemp))) errorCount++;
		if(isNaN(parseInt(intensityOfMoistureEmissions))) errorCount++;
		if(isNaN(parseInt(regionId))) errorCount++;
		if(isNaN(parseInt(systemType))) errorCount++;

		function elementInViewport(el) {
			var top = el.offsetTop;
			var left = el.offsetLeft;
			var width = el.offsetWidth;
			var height = el.offsetHeight;

			while(el.offsetParent) {
				el = el.offsetParent;
				top += el.offsetTop;
				left += el.offsetLeft;
			}

			return (
				top >= window.pageYOffset &&
				left >= window.pageXOffset &&
				(top + height) <= (window.pageYOffset + window.innerHeight) &&
				(left + width) <= (window.pageXOffset + window.innerWidth)
			);
		}
		
		if(errorCount == 0) {
			$("#calc-error").slideUp(0);
			calcBasseinResult += '<div>РЕЗУЛЬТАТЫ РАСЧЁТА ВЛАЖНОСТИ В БАССЕЙНАХ</div>';
			calcBasseinResult += '<div class="h-25"></div>';
			calcBasseinResult += '<table>';
			calcBasseinResult += '<tr><th>Наименование</th><th>Ед. изм.</th><th>Значение</th></tr>';
			calcBasseinResult += '<tr><td>Количество выделяющейся влаги</td><td>кг/час</td><td>' + moistureEmissionInOperatingMode + '</td></tr>';
			if( $("#calc-bassein-wrapper .calc-bassein-vent-type.active").data('type') == 1 ) {
				calcBasseinResult += '<tr><td>Производительность вентиляции в режиме Лето</td><td>м³/час</td><td>' + ventPerformanceSummer + '</td></tr>';
				calcBasseinResult += '<tr><td>Производительность вентиляции в режиме Зима</td><td>м³/час</td><td>' + ventPerformanceWinter + '</td></tr>';
				calcBasseinResult += '<tr><td>Мощность нагревателя*</td><td>кВт</td><td>' + heaterPowerVent + '</td></tr>';
			}
			if( $("#calc-bassein-wrapper .calc-bassein-vent-type.active").data('type') == 2 ) {
				calcBasseinResult += '<tr><td>Производительность вентиляции по санитарной норме</td><td>м³/час</td><td>' + ventilationRateSanitaryStandards + '</td></tr>';
				calcBasseinResult += '<tr><td>Мощность нагевателя*</td><td>кВт</td><td>' + heaterPowerVentDry + '</td></tr>';
				calcBasseinResult += '<tr><td>Производительность осушителя</td><td>кг/час</td><td>' + dryerCapacity + '</td></tr>';
				calcBasseinResult += '<tr><td>Электро-потребление осушителя</td><td>кВт</td><td>' + dryerElectricConsumption + '</td></tr>';
			}

			calcBasseinResult += '</table>';
			calcBasseinResult += '<div class="h-10"></div>';
			calcBasseinResult += '<div>*Мощность нагревателя рассчитана без учета <a href="http://climate-technology.ru/recuperatory" target="_blank">рекуперации</a></div>';
			calcBasseinResult += '<div class="h-10"></div>';
			calcBasseinResult += '<div><em>Расчет выполнен на основе Р НП "АВОК" 7.5-2012 «Обеспечение микроклимата и энергосбережение в крытых плавательных бассейнах». Нормы проектирования.</em></div>';

			
			$("#calc-bassein-result").html(calcBasseinResult);
			$("#calc-bassein-error").hide();
			$("#calc-bassein-result").show();
			$("#calc-send-order").slideDown(300,function() {
				if ($("#calc-do-bookmark").length > 0) {
					$('html,body').animate({
						scrollTop: $("#calc-do-bookmark").offset().top
					}, 500);
					return false;
				}
			});
			
		} else {
			$("#calc-error").slideDown(300);
			if( $(window).scrollTop() > $("#calc-do-bookmark").offset().top ) {
				$('html,body').animate({
					scrollTop: $("#calc-error").offset().top
				}, 500);
			}
		}
		
	});
	
	/* Cals Office */
	$("#calc-office-do").click(function(e){
		e.preventDefault();

		
		/* Vars Init */
		var visitorsCount;
		var officeArea;
		var ceilingHeight;
		var tempOutdoorWinter;
		var tempIndoor;
		var airAmountPerVisitor;
		var airExchangeMultiplicity;
		var performancePerVisitors;
		var performancePerMultiplicity;
		var fansPower;
		var ventPerformance;
		var heaterPower;
		var fanPower;
		var glazingArea;
		var heatDissipationPerEmployee;
		var sunHeatInput;
		var airConditioningSystemCapacity;
		var airConditioningSystemPower;
		
		/* Vars Population */
		
		visitorsCount = $("#calc-office-wrapper .visitors-count").val();
		officeArea = $("#calc-office-wrapper .office-area").val();
		ceilingHeight = $("#calc-office-wrapper .ceiling-height").val();
		if(ceilingHeight > 5) ceilingHeight = 5;
		tempOutdoorWinter = -28;
		tempIndoor = 20;
		airAmountPerVisitor = 60;
		airExchangeMultiplicity = 2.5;
		performancePerVisitors = visitorsCount * airAmountPerVisitor;
		performancePerMultiplicity = (officeArea * ceilingHeight) * airExchangeMultiplicity;
		fansPower = 0.0011;
		glazingArea = officeArea/6;
		heatDissipationPerEmployee = 450;
		sunHeatInput = $(".calc-office-insolation.active").data("coefficient");
		
		if(performancePerMultiplicity > performancePerVisitors) {
			ventPerformance = performancePerMultiplicity;
		} else {
			ventPerformance = performancePerVisitors;
		}
		heaterPower = Math.round(10 * ventPerformance/3600*1.2*1.005*(tempIndoor-tempOutdoorWinter))/10;
		fanPower = Math.round(100 * ventPerformance * fansPower)/100;
		airConditioningSystemCapacity = ((ventPerformance/3600*1.2*10*1000)+(visitorsCount*heatDissipationPerEmployee)+(glazingArea*sunHeatInput))/1000;
		airConditioningSystemPower = airConditioningSystemCapacity * 0.3;
		
		airConditioningSystemCapacity = Math.round(10 * airConditioningSystemCapacity) / 10;
		airConditioningSystemPower = Math.round(10 * airConditioningSystemPower) / 10;
		
		
		var calcOfficeResult = '';
		
		var errorCount = 0;
		
		if(isNaN(parseFloat(officeArea))) errorCount++;
		if(isNaN(parseFloat(ceilingHeight))) errorCount++;
		if(isNaN(parseInt(visitorsCount))) errorCount++;
		if(isNaN(parseInt(sunHeatInput))) errorCount++;

		function elementInViewport(el) {
			var top = el.offsetTop;
			var left = el.offsetLeft;
			var width = el.offsetWidth;
			var height = el.offsetHeight;

			while(el.offsetParent) {
				el = el.offsetParent;
				top += el.offsetTop;
				left += el.offsetLeft;
			}

			return (
				top >= window.pageYOffset &&
				left >= window.pageXOffset &&
				(top + height) <= (window.pageYOffset + window.innerHeight) &&
				(left + width) <= (window.pageXOffset + window.innerWidth)
			);
		}
		
		if(errorCount == 0) {
			$("#calc-error").slideUp(0);
			
			calcOfficeResult += '<div>Результаты расчета</div>';
			calcOfficeResult += '<div class="h-25"></div>';
			calcOfficeResult += '<div><strong>Вентиляция</strong></div>';
			calcOfficeResult += '<div class="h-10"></div>';
			calcOfficeResult += '<table>';
			calcOfficeResult += '<tr><th>Наименование</th><th>Ед. изм.</th><th>Значение</th></tr>';
			calcOfficeResult += '<tr><td>Производительность приточно-вытяжной вентиляции</td><td>м³/час</td><td>' + ventPerformance + '</td></tr>';
			calcOfficeResult += '<tr><td>Мощность нагревателя*</td><td>кВт</td><td>' + heaterPower + '</td></tr>';
			calcOfficeResult += '<tr><td>Эл.мощность вентиляторов</td><td>кВт</td><td>' + fanPower + '</td></tr>';
			calcOfficeResult += '</table>';
			calcOfficeResult += '<div class="h-10"></div>';
			calcOfficeResult += '<div>*Мощность нагревателя рассчитана без учета <a href="http://climate-technology.ru/recuperatory" target="_blank">рекуперации</a></div>';
			calcOfficeResult += '<div class="h-25"></div>';
			calcOfficeResult += '<div><strong>Кондиционирование</strong></div>';
			calcOfficeResult += '<div class="h-10"></div>';
			calcOfficeResult += '<table>';
			calcOfficeResult += '<tr><th>Наименование</th><th>Ед. изм.</th><th>Значение</th></tr>';
			calcOfficeResult += '<tr><td>Производительность системы кондиционирования*</td><td>кВт</td><td>' + airConditioningSystemCapacity + '</td></tr>';
			calcOfficeResult += '<tr><td>Электрическая мощность системы кондиционирования**</td><td>кВт</td><td>' + airConditioningSystemPower + '</td></tr>';
			calcOfficeResult += '</table>';
			calcOfficeResult += '<div class="h-10"></div>';
			calcOfficeResult += '<div>* Производительность системы рассчитана по средним показателям. Для получения более точных данных обратитесь к специалистам</div>';
			calcOfficeResult += '<div>** Эл. мощность системы приведена с учетом компрессорно-конденсаторных блоков или чиллера</div>';

			
			
			$("#calc-office-result").html(calcOfficeResult);
			$("#calc-office-error").hide();
			$("#calc-office-result").show();
			$("#calc-send-order").slideDown(300,function() {
				if ($("#calc-do-bookmark").length > 0) {
					$('html,body').animate({
						scrollTop: $("#calc-do-bookmark").offset().top
					}, 500);
					return false;
				}
			});
			
		} else {
			$("#calc-error").slideDown(300);
			if( $(window).scrollTop() > $("#calc-do-bookmark").offset().top ) {
				$('html,body').animate({
					scrollTop: $("#calc-error").offset().top
				}, 500);
			}
		}
		
			
	});
	
	/* Send Order */
	$(".submit-calc").on('click', function(e){
		e.preventDefault();
		var calcContentText = "";
		calcContentText += '<strong style="display:block;font-size:14px;margin-bottom:10px;">' + $("h1:first").html() + '</strong>';
		if($("#calc-vent-wrapper").length > 0) {
			var ccType = $(".calc-building-type.active").text();
			var ccArea = $("#calc-vent-area").val();
			var ccHeight = $("#calc-vent-height").val();
			var arrCcConfig = [];
			$(".calc-system-type.active").each(function(){
				arrCcConfig.push($(this).text()); 
			});
			var ccConfig = arrCcConfig.join(", ");
			var ccResult = $("#total-value").text() + " р.";
			calcContentText += '<table style="border-collapse:collapse">';
			calcContentText += '<tr><th style="text-align:right;padding:5px 10px;border:#ccc solid 1px">Тип помещения</th><td style="padding:5px 10px;border:#ccc solid 1px">' + ccType + '</td></tr>';
			calcContentText += '<tr><th style="text-align:right;padding:5px 10px;border:#ccc solid 1px">Площадь</th><td style="padding:5px 10px;border:#ccc solid 1px">' + ccArea + '</td></tr>';
			calcContentText += '<tr><th style="text-align:right;padding:5px 10px;border:#ccc solid 1px">Высота</th><td style="padding:5px 10px;border:#ccc solid 1px">' + ccHeight + '</td></tr>';
			calcContentText += '<tr><th style="text-align:right;padding:5px 10px;border:#ccc solid 1px">Состав системы</th><td style="padding:5px 10px;border:#ccc solid 1px">' + ccConfig + '</td></tr>';
			calcContentText += '<tr><th style="text-align:right;padding:5px 10px;border:#ccc solid 1px">Расчет системы</th><td style="padding:5px 10px;border:#ccc solid 1px">' + ccResult + '</td></tr>';
			calcContentText += '</table>';
		}
		if($("#calc-cond-wrapper").length > 0) {
			var calcCondTable = '<table style="border-collapse:collapse">' + $("#calc-cond-result table").html() + '</table>';
			var calcCondResult = '<div style="margin-bottom:20px;">' + calcCondTable + '</div>';
			calcContentText += calcCondResult;
		}
		if($("#calc-bassein-wrapper").length > 0) {
			var calcBasseinTable = '<table style="border-collapse:collapse">' + $("#calc-bassein-result table").html() + '</table>';
			var calcBasseinResult = '<div style="margin-bottom:20px;">' + calcBasseinTable + '</div>';
			calcContentText += calcBasseinResult;
		}
		if($("#calc-office-wrapper").length > 0) {
			var calcOfficeTable = '<table style="border-collapse:collapse">' + $("#calc-office-result table").html() + '</table>';
			var calcOfficeResult = '<div style="margin-bottom:20px;">' + calcOfficeTable + '</div>';
			calcContentText += calcOfficeResult;
		}
		$("#calc-content").val(calcContentText);
	});
	
	function getClosestNum(num, ar) {
		var i = 0, closest, closestDiff, currentDiff;
		if(ar.length)
		{
			closest = ar[0];
			for(i;i<ar.length;i++)
			{           
				closestDiff = Math.abs(num - closest);
				currentDiff = Math.abs(num - ar[i]);
				if(currentDiff < closestDiff)
				{
					closest = ar[i];
				}
				closestDiff = null;
				currentDiff = null;
			}
			return closest;
		}
		return false;
	}
	
	function getNextNum(num, ar) {
		var i = 0, closest;
		if(ar.length) {
			res = ar[0];
			for(i;i<ar.length;i++) {
				if(ar[i] >= num) {
					if(i<ar.length-1){
						res = ar[i+1];
					} else {
						res = ar[i];
					}
					break;
				}
			}
			return res;
		}
		return false;
	}

	function addSpaces(nStr) {
		var remainder = nStr.length % 3;
		return (nStr.substr(0, remainder) + nStr.substr(remainder).replace(/(\d{3})/g, ' $1')).trim();
	}
	
	/* Forms */
	
	function validateInput(input) {
		
		if(input.prop('required')) {
			if(input.val() == '') {
				input.addClass('with-error');
				return false;
			}
		}
		
		if(input.prop('type') == 'tel') {
			var pattTel = /^[\s()+-]*([0-9][\s()+-]*){6,20}$/;
			if(
				(!pattTel.test(input.val()))
				&&
				(input.val() != '')
			) {
				input.addClass('with-error');
				return false;
			}
		}

		if(input.prop('type') == 'email') {
			var pattEmail = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
			if(
				(!pattEmail.test(input.val()))
				&&
				(input.val() != '')
			) {
				input.addClass('with-error');
				return false;
			}
		}
		
		input.removeClass('with-error');
		return true;
	}
	
	$(".submit").click(function(e) {
		e.preventDefault();
		var formErrors = 0;
		
		var curForm = $(this).closest('form');
		
		var formData = new FormData(curForm[0]);
		
		curForm.find('input,textarea,select').each(function() {
			if( !validateInput( $(this) ) ) {
				formErrors++;
			}
		});
		
		if(formErrors == 0) {
		
			$.ajax({
				url: ajaxUrl,
				type: 'post',
				data: formData,
				dataType: 'text',
				contentType: false,
				cache: false,
				processData:false
			}).done(function(res) {
				console.log('-'+res+'-');
				if(res == 'ok') {
					curForm.find('.form-error').slideUp(300);
					curForm.find('.form-content').slideUp(300);
					curForm.find('.form-ok').slideDown(300);
				} else {
					curForm.find('.form-error').html('При отправке произошла ошибка!').slideDown(300);
				}
			});
			
		} else {
			curForm.find('.form-error').slideDown(300);
		}
	});
	
	
});