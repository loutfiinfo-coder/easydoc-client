// Strict Mode
"use strict";

var timer;
var filterClicked = 'no';
var chartdates;
var chartnbdocuments;
var chartvalue;

// Window Load Event
$(window).on("load", function() {
	var html = $(".contactus").clone();
	$(".lx-header .contactus").remove();
	$(".lx-wrapper").append(html);
    return false;
});

// Document Ready event
$(document).on("ready", function() {
	if($(window).width() < 768){
		$(".tox.tox-tinymce").width("width",($(window).width() - 30) + 'px');
		tinymce.editors.forEach(function(editor) {
			$(editor.getContainer()).css('width', ($(window).width() - 30) + 'px'); // set any width you want
		});
	}

	$("select[name='transform']").html($(".lx-select-facture").html());

	var html = $(".contactus").clone();
	$(".lx-header .contactus").remove();
	$(".lx-wrapper").append(html);
	html = $(".editnotifications").clone();
	$(".lx-header .editnotifications").remove();
	$(".lx-wrapper").append(html);
	html = $(".signature").clone();
	$(".lx-header .signature").remove();
	$(".lx-wrapper").append(html);
	loadNotification();
	window.setInterval(function(){
		loadNotification();
	},50000);
	
	var isMouseDown = false;
    var lastMouseX = 0;

    $(".lx-table").on("mousedown", function(event) {
        isMouseDown = true;
        lastMouseX = event.clientX;
    });

    $(document).on("mouseup", function() {
        isMouseDown = false;
    });

    $(document).on("mousemove", function(event) {
        if (isMouseDown) {
            var deltaX = event.clientX - lastMouseX;
            lastMouseX = event.clientX;
            $(".lx-table")[0].scrollLeft -= deltaX;
        }
    });
	
	document.addEventListener('DOMContentLoaded', function() {
		const infobuls = document.querySelectorAll('.infobul');
			infobuls.forEach(function(infobul) {
			infobul.removeAttribute('data-title');
		});
	});
	
	checkLC();
	
	return false;
});

function loadNotification(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'loadnotification'
		},
		success : function(response){
			$(".lx-notifications-list div").html(response);
			$(".lx-show-notifications ins").text($(".lx-notifications-list div .lx-notifications-nb").text());
			if($(".lx-show-notifications ins").text() === "" || $(".lx-show-notifications ins").text() === "0"){
				$(".lx-show-notifications ins").css("display","none");
			}
			else{
				$(".lx-show-notifications ins").css("display","block");
			}
		}
	});	
}

$(".lx-show-notifications").on("click",function(){
	$(".lx-header-admin > ul > li > img + .lx-notifications-list").hide();
	$(this).parent().prev(".lx-notifications-list").fadeToggle();
});

$(".lx-accordion > li > a").on("click",function(){
	var a = $(this).next("div");
	a.slideToggle();
	if($(this).find("i").attr("class") === "fa fa-plus"){
		$(this).find("i").attr("class","fa fa-minus");
	}
	else{
		$(this).find("i").attr("class","fa fa-plus");
	}
	window.setTimeout(function(){
		a.find("iframe").height(a.find("iframe").width() * 0.56);
	},100);
});

$(".lx-header-admin > ul > li > .lx-show-params").on("click",function(){
	$(".lx-show-notifications").parent().next(".lx-account-settings").hide();
	$(this).next(".lx-account-settings").fadeToggle();
});

$(".lx-mobile-menu").on("click",function(){
	$(".lx-main-leftside").css("left","0px");
});

$(".lx-mobile-menu-hide").on("click",function(){
	$(".lx-main-leftside").css("left","-268px");
});

$(".lx-stats-bloc ul li a").on("click",function(){
	$(this).parents("ul").find("a").removeClass("active");
	$(this).addClass("active");
	$(this).parents("div.lx-stats-bloc").find("table[data-tab],div[data-tab]").hide();
	$(this).parents("div.lx-stats-bloc").find("table[data-tab='"+$(this).attr("data-tab")+"'],div[data-tab='"+$(this).attr("data-tab")+"']").show();
});

$(".lx-login .lx-submit a").on("click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	$(".lx-login form").submit();
});

$(document).on("keyup",function(e) {
    if(e.keyCode == 13) {	
		if($(".lx-login .lx-submit a").length){
			$(".lx-login .lx-submit a").trigger("click");
		}
		if($(".lx-search-keyword").length){
			$(".lx-search-keyword").trigger("click");
		}
		if($(".lx-search-keyword2").length){
			$(".lx-search-keyword2").trigger("click");
		}
    }
});

$(".lx-login .lx-textfield label i").on("click",function(){
	if($(this).attr("class") === "fa fa-eye-slash"){
		$(this).attr("class","fa fa-eye").css("color","#39add1");
		$(this).prev("input").attr("type","text");
	}
	else{
		$(this).attr("class","fa fa-eye-slash").css("color","#CCCCCC");
		$(this).prev("input").attr("type","password");
	}
});

function _(el){
	return document.getElementById(el);
}

$("#medias").on("change",function(){
	if($(this).parent().find("a").find(".fa-circle-notch").length === 0){
		$(this).parent().find("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	uploadsImages2(_("medias").files[0]);
});

function uploadsImages2(picture){
	var file = picture;
	var formdata = new FormData();
	formdata.append("file0", file);
	var ajax = new XMLHttpRequest();
	ajax.addEventListener("load", completeHandler1, false);
	ajax.open("POST", "file_upload_parser.php");
	ajax.send(formdata);
	function completeHandler1(event){
		if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
			if(ajax.responseText !== ""){
				$("#medias").parent().find("a").find("i").remove();
				var html = '<img src="uploads/'+ajax.responseText+'" />';
				$("input[name='picture']").val(ajax.responseText);
				$(".lx-medias-item div").html(html);
			}
		}
	}
}

$("#medias1").on("change",function(){
	if($(this).parent().find("a").find(".fa-circle-notch").length === 0){
		$(this).parent().find("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	uploadsImages(_("medias1").files[0]);
});

function uploadsImages(file){
	var file = file;
	var formdata = new FormData();
	formdata.append("file10", file);
	var ajax = new XMLHttpRequest();
	ajax.addEventListener("load", completeHandler1, false);
	ajax.open("POST", "file_upload_parser.php");
	ajax.send(formdata);
	function completeHandler1(event){
		if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
			if(ajax.responseText !== ""){
				$("#medias1").parent().find("a").find("i.fa-circle-notch").remove();
				$("input[name='medias1']").val("");
				$("input[name='attachments']").val(ajax.responseText);
				$(".fa-check").css("display","inline-block");
				$(".lx-clear-attachment").css("display","inline-block");
			}
		}
	}
}

$("#mediascover").on("change",function(){
	if($(this).parent().find("a").find(".fa-circle-notch").length === 0){
		$(this).parent().find("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	uploadsCover(_("mediascover").files[0]);
});

function uploadsCover(picture){
	var file = picture;
	var formdata = new FormData();
	formdata.append("file0", file);
	var ajax = new XMLHttpRequest();
	ajax.addEventListener("load", completeHandler1, false);
	ajax.open("POST", "file_upload_parser.php");
	ajax.send(formdata);
	function completeHandler1(event){
		if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
			if(ajax.responseText !== ""){
				$("#mediascover").parent().find("a").find("i").remove();
				var html = '<img src="uploads/'+ajax.responseText+'" />';
				$("input[name='cover']").val(ajax.responseText);
				$(".lx-medias-item-cover div").html(html);
			}
		}
	}
}

$("body").delegate(".uploadreceipt","change",function(){
	if($(this).next("a").find("i").length === 0){
		$(this).next("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	uploadReceipt(_($(this).attr("id")).files[0],$(this).attr("data-id"));
});

$("body").delegate(".lx-open-zoombox","click",function(){
	$(".lx-zoombox-content").html('<img src="'+$(this).attr("src").replace(/cropped_/,"large_")+'">').attr("data-pos",$(this).index());
});

$(".lx-zoombox-nav i.fa-angle-right").on("click",function(){
	if($(".lx-zoombox-content").attr("data-pos") < parseFloat($(".lx-open-zoombox").length) - 1){
		$(".lx-zoombox-content").attr("data-pos",parseFloat($(".lx-zoombox-content").attr("data-pos")) + 1);
	}
	else{
		$(".lx-zoombox-content").attr("data-pos",0);
	}
	$(".lx-zoombox-content").html('<img src="'+$(".lx-open-zoombox:eq("+$(".lx-zoombox-content").attr("data-pos")+")").attr("src").replace(/cropped_/,"large_")+'">');
});

$(".lx-zoombox-nav i.fa-angle-left").on("click",function(){
	if($(".lx-zoombox-content").attr("data-pos") > 0){
		$(".lx-zoombox-content").attr("data-pos",parseFloat($(".lx-zoombox-content").attr("data-pos")) - 1);
	}
	else{
		$(".lx-zoombox-content").attr("data-pos",parseFloat($(".lx-open-zoombox").length) - 1);
	}
	$(".lx-zoombox-content").html('<img src="'+$(".lx-open-zoombox:eq("+$(".lx-zoombox-content").attr("data-pos")+")").attr("src").replace(/cropped_/,"large_")+'">');
});


$("body").delegate(".lx-floating-response","click",function(){
	$(".lx-floating-response").fadeOut();
});

$("body").delegate(".lx-main-content .lx-table input[type='checkbox']:not(input[name='selectall'])","click",function(){
	if($(this).prop("checked") === true){
		$(this).parent().parent().parent().css("background","#c6e2ff");
	}
	else{
		$(this).parent().parent().parent().removeAttr("style");
		$(this).parent().parent().parent().attr("style",$(this).parent().parent().parent().attr("data-umpaid"));
	}

	let id = $(this).attr('value');
	let code = $(this).attr('data-code');
	let price = $(this).attr('data-price');
	let client = $(this).attr('data-client');
	let prospect = $(this).attr('data-prospect');

	let idsField = $("#mergeform input[name='ids']");
	let codesField = $("#mergeform input[name='codes']");
	let pricesField = $("#mergeform input[name='prices']");
	let clientsField = $("#mergeform input[name='clients']");
	let prospectsField = $("#mergeform input[name='prospects']");

	let ids = idsField.val().split(',').filter(v => v.trim() !== '');
	let codes = codesField.val().split(',').filter(v => v.trim() !== '');
	let prices = pricesField.val().split(',').filter(v => v.trim() !== '');
	let clients = clientsField.val().split(',').filter(v => v.trim() !== '');
	let prospects = prospectsField.val().split(',').filter(v => v.trim() !== '');

	if ($(this).is(':checked')) {
		let index = ids.indexOf(id);
		
		if (index === -1) {
			// Add new id with its data
			ids.push(id);
			codes.push(code);
			prices.push(price);
			clients.push(client);
			prospects.push(prospect);
		} else {
			// Update existing id with new data
			codes[index] = code;
			prices[index] = price;
			clients[index] = client;
			prospects[index] = prospect;
		}
	} else {
		// Remove the id and its corresponding data by index
		let index = ids.indexOf(id);
		if (index !== -1) {
			ids.splice(index, 1);
			codes.splice(index, 1);
			prices.splice(index, 1);
			clients.splice(index, 1);
			prospects.splice(index, 1);
		}
	}

	// Update the inputs
	idsField.val(ids.join(','));
	codesField.val(codes.join(','));
	pricesField.val(prices.join(','));
	clientsField.val(clients.join(','));
	$("#mergeform input[name='clientid']").val(simplify_values(clients.join(',')));
	prospectsField.val(prospects.join(','));
	$("#mergeform input[name='prospectid']").val(simplify_values(prospects.join(',')));

	if(ids.length === 0){
		$(".lx-transform-bulk").text("Transformation groupée");
	}
	else{
		$(".lx-transform-bulk").text("Transformation groupée ("+ids.length+")");
	}
});

$("body").delegate(".lx-first-tr input[name='selectall']","click",function(){
	var checked = $(this).prop("checked");
	$(".lx-main .lx-table input[type='checkbox']:not(input[name='selectall'])").each(function(){
		$(this).prop("checked",checked);

		let id = $(this).attr('value');
		let code = $(this).attr('data-code');
		let price = $(this).attr('data-price');
		let client = $(this).attr('data-client');
		let prospect = $(this).attr('data-prospect');

		let idsField = $("#mergeform input[name='ids']");
		let codesField = $("#mergeform input[name='codes']");
		let pricesField = $("#mergeform input[name='prices']");
		let clientsField = $("#mergeform input[name='clients']");
		let prospectsField = $("#mergeform input[name='prospects']");

		let ids = idsField.val().split(',').filter(v => v.trim() !== '');
		let codes = codesField.val().split(',').filter(v => v.trim() !== '');
		let prices = pricesField.val().split(',').filter(v => v.trim() !== '');
		let clients = clientsField.val().split(',').filter(v => v.trim() !== '');
		let prospects = prospectsField.val().split(',').filter(v => v.trim() !== '');

		if ($(this).is(':checked')) {
			let index = ids.indexOf(id);
			
			if (index === -1) {
				// Add new id with its data
				ids.push(id);
				codes.push(code);
				prices.push(price);
				clients.push(client);
				prospects.push(prospect);
			} else {
				// Update existing id with new data
				codes[index] = code;
				prices[index] = price;
				clients[index] = client;
				prospects[index] = prospect;
			}
		} else {
			// Remove the id and its corresponding data by index
			let index = ids.indexOf(id);
			if (index !== -1) {
				ids.splice(index, 1);
				codes.splice(index, 1);
				prices.splice(index, 1);
				clients.splice(index, 1);
				prospects.splice(index, 1);
			}
		}

		// Update the inputs
		idsField.val(ids.join(','));
		codesField.val(codes.join(','));
		pricesField.val(prices.join(','));
		clientsField.val(clients.join(','));
		$("#mergeform input[name='clientid']").val(simplify_values(clients.join(',')));
		prospectsField.val(prospects.join(','));
		$("#mergeform input[name='prospectid']").val(simplify_values(prospects.join(',')));
		
		if(ids.length === 0){
			$(".lx-transform-bulk").text("Transformation groupée");
		}
		else{
			$(".lx-transform-bulk").text("Transformation groupée ("+ids.length+")");
		}		
	});
	var ids = "0";
	$("input[type='checkbox'][name='command']").each(function(){
		if($(this).prop("checked") === true){
			ids += ","+$(this).val();
		}
	});
});

function simplify_values(input) {
  // Split by comma and remove empty or space-only elements
  const values = input.split(',').map(v => v.trim()).filter(v => v !== '');

  // Get unique values
  const unique = [...new Set(values)];

  // If all values are the same, return that value; otherwise, return 0
  return unique.length === 1 ? unique[0] : 0;
}

$(".lx-popup-content").delegate(".lx-first-tr input[name='selectall']","click",function(){
	var checked = $(this).prop("checked");
	$(".lx-popup-content .lx-table input[type='checkbox']").each(function(){
		$(this).prop("checked",checked)
	});
	var ids = "0";
	$("input[type='checkbox'][name='command']").each(function(){
		if($(this).prop("checked") === true){
			ids += ","+$(this).val();
		}
	});
	$("#idticket").val(ids);
});

$("body").delegate(".lx-first-tr input[name='selectall']","click",function(){
	var checked = $(this).prop("checked");
	$(".lx-table input[type='checkbox']").each(function(){
		$(this).prop("checked",checked)
	});
	var ids = "0";
	$("input[type='checkbox'][name='command']").each(function(){
		if($(this).prop("checked") === true){
			ids += ","+$(this).val();
		}
	});
	$(".lx-main-content .lx-table input[type='checkbox']:not(input[name='selectall'])").each(function(){
		if($(this).prop("checked") === true){
			$(this).parent().parent().parent().css("background","#c6e2ff");
		}
		else{
			$(this).parent().parent().parent().removeAttr("style");
			$(this).parent().parent().parent().css("background",$(this).parent().parent().parent().attr("data-umpaid"));
		}
	});
	$("#idticket").val(ids);
});

$(".lx-popup-content").delegate(".lx-first-tr input[name='selectall']","click",function(){
	var checked = $(this).prop("checked");
	$(".lx-popup .lx-table input[type='checkbox']").each(function(){
		$(this).prop("checked",checked)
	});
});

$(".lx-popup-content").delegate(".lx-selectall-all-roles","click",function(){
	var checked = $(this).find("input").prop("checked");
	if(checked === true){
		$(".lx-table-roles input[type='checkbox']").not("input[data-uncheck='1']").each(function(){
			$(this).prop("checked",checked);
		});
	}
	else{
		$(".lx-table-roles input[type='checkbox']").each(function(){
			$(this).prop("checked",checked);
		});		
	}

	$("#usersform input[name='rolestext']").val("");
	$("#usersform input[name='roles']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='rolestext']").val($("#usersform input[name='rolestext']").val() + "," + $(this).val());
		}
	});

	$("#usersform input[name='companiestext']").val("");
	$("#usersform input[name='companies']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='companiestext']").val($("#usersform input[name='companiestext']").val() + "," + $(this).val());
		}
	});
});

$("#importproduct").on("change",function(){
	$(".lx-importer").css({"border-color":"green","background":"#d1e8cc"});
	$(".lx-importer span").text($(this).val().replace(/C:\\fakepath\\/i, '')).css({"font-weight":"bold","color":"green"});
});

$(".lx-import-product").on("click",function(){
	uploadsXLSProducts(_("importproduct"),$("#montantformat").val());
});

function uploadsXLSProducts(file,montantformat){
	if($(".lx-import-product").attr("class") !== "lx-import-product lx-disabled"){
		if(file.value !== "" && montantformat !== ""){
			$(".lx-import-product").attr("class","lx-import-product lx-disabled");
			if($(".lx-import-product").find("i").length === 0){
				$(".lx-import-product").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var formdata = new FormData();
			formdata.append("file6", file.files[0]);
			formdata.append("montantformat", montantformat);
			var ajax = new XMLHttpRequest();
			ajax.addEventListener("load", completeHandler, false);
			ajax.open("POST", "file_upload_parser.php");
			ajax.send(formdata);
			function completeHandler(event){
				if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
					$("#importproduct").val("");
					$(".lx-importer").removeAttr("style");
					$(".lx-importer span").text("Choisissez un fichier (excel)").removeAttr("style");

					$(".lx-import-product").attr("class","lx-import-product");
					$(".lx-import-product i").remove();
					$(".lx-list-products table").append(ajax.responseText);

					$("#commandsform input[name='product']").val("");
					$("#commandsform select[name='product']").val("");
					$("#commandsform input[name='unit']").val("");
					$("#commandsform select[name='unit']").val("");
					$("#commandsform input[name='qty']").val("1");
					$("#commandsform select[name='pricebase']").val("HT");
					$("#commandsform select[name='utva']").val("");
					$("#commandsform input[name='uprice']").val("");
					
					if($("#commandsform select[name='typediscount']").val() === ""){
						$(".lx-discount").css("display","none");
						$(".maindiscount").css("display","none");
					}
					else{
						$(".lx-discount").css("display","table-cell");
						$(".maindiscount").css("display","block");
						$(".discounttype").text($("#commandsform select[name='typediscount']").val());
					}
							
					$(".lx-list-products table tr td input[name='discount']").trigger("keyup");
					
					$("#commandsform input[name='maindiscount']").trigger("keyup");
					
					$(".lx-list-products table tr.lx-secondrow").css("display","none");
					
					$(".lx-error-products").html("");
					reloadProducts();
					calculateProfit();
				}
			}	
		}			
		else{
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez choisir un fichier excel et un format de montant !!<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);		
		}
	}
}

$("#importclient").on("change",function(){
	$(".lx-importer").css({"border-color":"green","background":"#d1e8cc"});
	$(".lx-importer span").text($(this).val().replace(/C:\\fakepath\\/i, '')).css({"font-weight":"bold","color":"green"});
});

$("#importclientsform .lx-submit a").on("click",function(){
	uploadsXLSClients(_("importclient"));
});

function uploadsXLSClients(file){
	if(file.value !== ""){
		if($("#importclientsform .lx-submit a").attr("class") !== "lx-disabled"){
			$("#importclientsform .lx-submit a").attr("class","lx-disabled");
			if($("#importclientsform .lx-submit a").find("i").length === 0){
				$("#importclientsform .lx-submit a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			$("*[data-required]").removeAttr("style");
			var formdata = new FormData();
			formdata.append("file3", file.files[0]);
			var ajax = new XMLHttpRequest();
			ajax.addEventListener("load", completeHandler, false);
			ajax.open("POST", "file_upload_parser.php");
			ajax.send(formdata);
			function completeHandler(event){
				if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
					console.log(ajax.responseText);
					$("#importclientsform .lx-submit a").attr("class","");
					$("#importclientsform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadClients($(".lx-pagination ul").attr("data-state"));
					$("#importclient").val("");
					$(".lx-importer").removeAttr("style");
					$(".lx-importer span").text("Choisissez un fichier (excel)").removeAttr("style");
				}
			}	
		}			
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez choisir un fichier excel<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
}

$("#importsupplier").on("change",function(){
	$(".lx-importer").css({"border-color":"green","background":"#d1e8cc"});
	$(".lx-importer span").text($(this).val().replace(/C:\\fakepath\\/i, '')).css({"font-weight":"bold","color":"green"});
});

$("#importsuppliersform .lx-submit a").on("click",function(){
	uploadsXLSSuppliers(_("importsupplier"));
});

function uploadsXLSSuppliers(file){
	if(file.value !== ""){
		if($("#importsuppliersform .lx-submit a").attr("class") !== "lx-disabled"){
			$("#importsuppliersform .lx-submit a").attr("class","lx-disabled");
			if($("#importsuppliersform .lx-submit a").find("i").length === 0){
				$("#importsuppliersform .lx-submit a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			$("*[data-required]").removeAttr("style");
			var formdata = new FormData();
			formdata.append("file4", file.files[0]);
			var ajax = new XMLHttpRequest();
			ajax.addEventListener("load", completeHandler, false);
			ajax.open("POST", "file_upload_parser.php");
			ajax.send(formdata);
			function completeHandler(event){
				if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
					console.log(ajax.responseText);
					$("#importsuppliersform .lx-submit a").attr("class","");
					$("#importsuppliersform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadSuppliers($(".lx-pagination ul").attr("data-state"));
					$("#importsupplier").val("");
					$(".lx-importer").removeAttr("style");
					$(".lx-importer span").text("Choisissez un fichier (excel)").removeAttr("style");
				}
			}	
		}			
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez choisir un fichier excel<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
}

$("#importprospect").on("change",function(){
	$(".lx-importer").css({"border-color":"green","background":"#d1e8cc"});
	$(".lx-importer span").text($(this).val().replace(/C:\\fakepath\\/i, '')).css({"font-weight":"bold","color":"green"});
});

$("#importprospectsform .lx-submit a").on("click",function(){
	uploadsXLSProspects(_("importprospect"));
});

function uploadsXLSProspects(file){
	if(file.value !== ""){
		if($("#importprospectsform .lx-submit a").attr("class") !== "lx-disabled"){
			$("#importprospectsform .lx-submit a").attr("class","lx-disabled");
			if($("#importprospectsform .lx-submit a").find("i").length === 0){
				$("#importprospectsform .lx-submit a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			$("*[data-required]").removeAttr("style");
			var formdata = new FormData();
			formdata.append("file5", file.files[0]);
			var ajax = new XMLHttpRequest();
			ajax.addEventListener("load", completeHandler, false);
			ajax.open("POST", "file_upload_parser.php");
			ajax.send(formdata);
			function completeHandler(event){
				if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
					$("#importprospectsform .lx-submit a").attr("class","");
					$("#importprospectsform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadProspects($(".lx-pagination ul").attr("data-state"));
					$("#importprospect").val("");
					$(".lx-importer").removeAttr("style");
					$(".lx-importer span").text("Choisissez un fichier (excel)").removeAttr("style");
				}
			}	
		}			
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez choisir un fichier excel<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
}

$("#exportform .lx-submit a").on("click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	if($("#exportform input[name='columns']:checked").length){
		$("#exportform .lx-submit a").attr("class","");
		$("#exportform .lx-submit a i").remove();
		var typedoc = $("#exportform input[name='type']").val();
		var table = $("#exportform input[name='table']").val();
		var req = encodeURIComponent($("#req").val());
		var columntitles = "";
		var columns = "";
		$("#exportform input[name='columns']:checked").each(function(){
			columntitles += ","+$(this).attr("data-title");
			columns += ","+$(this).val();
		});
		window.location.href = "export.php?table="+table+"&columntitles="+columntitles+"&columns="+columns+"&typedoc="+typedoc+"&req="+req;
	}
	else{
		$("#exportform .lx-submit a").attr("class","");
		$("#exportform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez choisir des colonnes à exporter<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("#settingsform .lx-submit a").on("click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	var rowcolor = 0;
	if($("#settingsform input[name='rowcolor']").prop("checked") === true){
		rowcolor = 1;
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#settingsform input[name='id']").val(),
			logo : $("#settingsform input[name='picture']").val(),
			cover : $("#settingsform input[name='cover']").val(),
			rowcolor : rowcolor,
			nbrows : $("#settingsform select[name='nbrows']").val(),
			store : $("#settingsform input[name='store']").val(),
			defaultstate : $("#settingsform input[name='defaultstate']:checked").val(),
			currency : $("#settingsform input[name='currency']").val(),
			action : 'editsettings'
		},
		success : function(response){
			$("#settingsform .lx-submit a i").remove();
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="fa fa-times"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
			window.location.href = "settings.php";
		}
	});
});

$("body").delegate("#editnotificationsform input[type='checkbox']","click",function(){
	if($(this).prop("checked") === true){
		$(this).parent().parent().prev("td").find("input").prop("readonly",false);
	}
	else{
		$(this).parent().parent().prev("td").find("input").val("").prop("readonly",true);
		$(this).parent().parent().prev("td").find("ins").remove();
		$(this).parent().parent().prev("td").find("input").removeAttr("style");
	}
});

$("body").delegate("#editnotificationsform .lx-submit a","click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}

	var onfbefore = ($("#editnotificationsform input[name='onfbefore']").prop("checked") === true?"on":"off");
	var onfafter = ($("#editnotificationsform input[name='onfafter']").prop("checked") === true?"on":"off");
	var onfsbefore = ($("#editnotificationsform input[name='onfsbefore']").prop("checked") === true?"on":"off");
	var onfsafter = ($("#editnotificationsform input[name='onfsafter']").prop("checked") === true?"on":"off");
	var onavoir = ($("#editnotificationsform input[name='onavoir']").prop("checked") === true?"on":"off");	
	var onunpaid = ($("#editnotificationsform input[name='onunpaid']").prop("checked") === true?"on":"off");
	var oncaissein = ($("#editnotificationsform input[name='oncaissein']").prop("checked") === true?"on":"off");
	var oncaisseout = ($("#editnotificationsform input[name='oncaisseout']").prop("checked") === true?"on":"off");
	var onremis = ($("#editnotificationsform input[name='onremis']").prop("checked") === true?"on":"off");
	var roncaissein = ($("#editnotificationsform input[name='roncaissein']").prop("checked") === true?"on":"off");
	var roncaisseout = ($("#editnotificationsform input[name='roncaisseout']").prop("checked") === true?"on":"off");
	var onrasclientbefore = ($("#editnotificationsform input[name='onrasclientbefore']").prop("checked") === true?"on":"off");
	var onrasclientafter = ($("#editnotificationsform input[name='onrasclientafter']").prop("checked") === true?"on":"off");
	var onrassupplierbefore = ($("#editnotificationsform input[name='onrassupplierbefore']").prop("checked") === true?"on":"off");
	var onrassupplierafter = ($("#editnotificationsform input[name='onrassupplierafter']").prop("checked") === true?"on":"off");
	var onrecnoenc = ($("#editnotificationsform input[name='onrecnoenc']").prop("checked") === true?"on":"off");
	var onemisnodec = ($("#editnotificationsform input[name='onemisnodec']").prop("checked") === true?"on":"off");
	var onunremis = ($("#editnotificationsform input[name='onunremis']").prop("checked") === true?"on":"off");
	
	var k = 0;
	if(onfbefore === "on"){
		isNumber($("#editnotificationsform input[name='fbefore']"));
		if(!isNumber($("#editnotificationsform input[name='fbefore']"))){
			k++;
		}
	}
	if(onfsbefore === "on"){
		isNumber($("#editnotificationsform input[name='fsbefore']"));
		if(!isNumber($("#editnotificationsform input[name='fsbefore']"))){
			k++;
		}
	}
	if(onavoir === "on"){
		isNumber($("#editnotificationsform input[name='avoir']"));
		if(!isNumber($("#editnotificationsform input[name='avoir']"))){
			k++;
		}
	}
	if(oncaissein === "on"){
		isNumber($("#editnotificationsform input[name='caissein']"));
		if(!isNumber($("#editnotificationsform input[name='caissein']"))){
			k++;
		}
	}
	if(oncaisseout === "on"){
		isNumber($("#editnotificationsform input[name='caisseout']"));
		if(!isNumber($("#editnotificationsform input[name='caisseout']"))){
			k++;
		}
	}
	if(roncaissein === "on"){
		isNumber($("#editnotificationsform input[name='rcaissein']"));
		if(!isNumber($("#editnotificationsform input[name='rcaissein']"))){
			k++;
		}
	}
	if(roncaisseout === "on"){
		isNumber($("#editnotificationsform input[name='rcaisseout']"));
		if(!isNumber($("#editnotificationsform input[name='rcaisseout']"))){
			k++;
		}
	}
	if(onrasclientbefore === "on"){
		isNumber($("#editnotificationsform input[name='rasclientbefore']"));
		if(!isNumber($("#editnotificationsform input[name='rasclientbefore']"))){
			k++;
		}
	}
	if(onrassupplierbefore === "on"){
		isNumber($("#editnotificationsform input[name='rassupplierbefore']"));
		if(!isNumber($("#editnotificationsform input[name='rassupplierbefore']"))){
			k++;
		}
	}
	
	if(k === 0){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				fbefore : onfbefore+"-"+$("#editnotificationsform input[name='fbefore']").val(),
				fafter : onfafter+"-0",
				fsbefore : onfsbefore+"-"+$("#editnotificationsform input[name='fsbefore']").val(),
				fsafter : onfsafter+"-0",
				avoir : onavoir+"-"+$("#editnotificationsform input[name='avoir']").val(),
				remis : onremis+"-"+$("#editnotificationsform input[name='remis']").val(),
				caissein : oncaissein+"-"+$("#editnotificationsform input[name='caissein']").val(),
				caisseout : oncaisseout+"-"+$("#editnotificationsform input[name='caisseout']").val(),
				rcaissein : roncaisseout+"-"+$("#editnotificationsform input[name='rcaissein']").val(),
				rcaisseout : roncaisseout+"-"+$("#editnotificationsform input[name='rcaisseout']").val(),
				unpaid : onunpaid+"-0",
				recnoenc : onrecnoenc+"-0",
				emisnodec : onunremis+"-0",
				unremis : onunremis+"-0",
				inwaiting : "off-0",
				outwaiting : "off-0",
				incach : "off-0",
				outcach : "off-0",
				rremis : "off-0",
				rasclientbefore : onrasclientbefore+"-"+$("#editnotificationsform input[name='rasclientbefore']").val(),
				rasclientafter : onrasclientafter+"-0",
				rassupplierbefore : onrassupplierbefore+"-"+$("#editnotificationsform input[name='rassupplierbefore']").val(),
				rassupplierafter : onrassupplierafter+"-0",

				action : 'editnotifications'
			},
			success : function(response){
				$("#editnotificationsform .lx-submit a").attr("class","");
				$("#editnotificationsform .lx-submit a i").remove();
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="fa fa-times"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);
				window.location.href = window.location.href;
			}
		});
	}
	else{
		$("#editnotificationsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$(".lx-reset-photo").on("click",function(){
	$(".lx-reset-pictures").attr("data-input",$(this).attr("data-input"));
	$(".lx-reset-pictures").attr("data-default",$(this).attr("data-default"));
	$(".lx-reset-pictures").attr("data-file",$(this).attr("data-file"));
	$("ins.lx-title").text($(this).attr("data-img"));
});

$(".lx-reset-pictures").on("click",function(){
	$("input[name='"+$(this).attr("data-input")+"']").val($(this).attr("data-default"));
	$("a[data-input='"+$(this).attr("data-input")+"']").prev("div").find("img").attr("src","images/"+$(this).attr("data-default"));
	$(".lx-popup-content > a > .material-icons").trigger("click");
	$("input[name='"+$(this).attr("data-file")+"']").val("");
});

$("#installform .lx-submit a").on("click",function(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			apikey : $("#installform input[name='apikey']").val(),
			action : 'editinstall'
		},
		success : function(response){
			$("#installform .lx-submit a i").remove();
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="fa fa-times"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
			window.location.href = window.location.href;
		}
	});
});

function checkLC(){
	$(".lx-lc-add").css("display","none");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'lc'
		},
		success : function(response){
			$(".lx-limit-camp").html(response);
			if($(".lx-lc").length === 0){
				$(".lx-lc-add").css("display","block");
			}
		}
	});
}

$("#usersform input[name='roles']").click(function(){
	$("#usersform input[name='rolestext']").val("");
	$("#usersform input[name='roles']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='rolestext']").val($("#usersform input[name='rolestext']").val() + "," + $(this).val());
		}
	});
});

$("#usersform input[name='defaultstates']").click(function(){
	$("#usersform input[name='defaultstatestext']").val("");
	$("#usersform input[name='defaultstates']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='defaultstatestext']").val($("#usersform input[name='defaultstatestext']").val() + "," + $(this).val());
		}
	});
});

$("#usersform input[name='depots']").click(function(){
	$("#usersform input[name='depotstext']").val("");
	$("#usersform input[name='depots']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='depotstext']").val($("#usersform input[name='depotstext']").val() + "," + $(this).val());
		}
	});
});

$("#usersform input[name='companies']").click(function(){
	$("#usersform input[name='companiestext']").val("");
	$("#usersform input[name='companies']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='companiestext']").val($("#usersform input[name='companiestext']").val() + "," + $(this).val());
		}
	});
});

$("#usersform input[name='projects']").click(function(){
	$("#usersform input[name='projectstext']").val("");
	$("#usersform input[name='projects']").each(function(){
		if($(this).prop("checked") === true){
			$("#usersform input[name='projectstext']").val($("#usersform input[name='projectstext']").val() + "," + $(this).val());
		}
	});
});

$(".lx-new-user").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#usersform input[name='fullname']").val("");
	$("#usersform input[name='email']").val("").prop("readonly",false).css("cursor","initial");
	$("#usersform input[name='email']").parent().parent().addClass("lx-g2").removeClass("lx-g1");
	$("#usersform input[name='password']").val("");
	$("#usersform input[name='password']").parent().parent().css("display","block");
	$("#usersform input[name='phone']").val("");
	$("#usersform input[name='job']").val("");
	$("#usersform input[name='roles']").each(function(){
		$(this).prop("checked",false);
	});
	$("#usersform input[name='rolestext']").val("");
	$("#usersform input[name='companies']").each(function(){
		$(this).prop("checked",false);
	});
	$("#usersform input[name='companiestext']").val("");
	$("#usersform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-user","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#usersform input[name='fullname']").val($(this).attr("data-fullname"));
	$("#usersform input[name='email']").val($(this).attr("data-email")).prop("readonly",true).css("cursor","not-allowed");
	$("#usersform input[name='email']").parent().parent().addClass("lx-g1").removeClass("lx-g2");
	$("#usersform input[name='password']").val($(this).attr("data-password"));
	$("#usersform input[name='password']").parent().parent().css("display","none");
	$("#usersform input[name='phone']").val($(this).attr("data-phone"));
	$("#usersform input[name='job']").val($(this).attr("data-job"));
	$("#usersform input[name='rolestext']").val($(this).attr("data-roles"));
	$("#usersform input[name='roles']").each(function(){
		console.log($(this).val());
		var val = $(this).val();
		if($(this).val().indexOf('Devis')){
			if($(this).val() === "Télécharger Backup"){
				val = $(this).val();
			}
			else{
				val = $(this).val()+",";
			}
		}
		val = $("#usersform input[name='rolestext']").val().indexOf(val);
		console.log(val);
		if(val !== -1){
			$(this).prop("checked",true);
		}
		else{
			$(this).prop("checked",false);
		}
	});
	$("#usersform input[name='companiestext']").val($(this).attr("data-companies"));
	$("#usersform input[name='companies']").each(function(){
		var val = $("#usersform input[name='companiestext']").val().indexOf($(this).val());
		if(val !== -1){
			$(this).prop("checked",true);
		}
		else{
			$(this).prop("checked",false);
		}
	});
	$("#usersform input[name='id']").val($(this).attr("data-id"));
});

$("#usersform .lx-submit a").on("click",function(){
	isNotEmpty($("#usersform input[name='fullname']"));
	isNotEmpty($("#usersform input[name='email']"));
	isNotEmpty($("#usersform input[name='password']"));
	isNotEmpty($("#usersform input[name='job']"));
	isNotEmpty($("#usersform input[name='companiestext']"));
	if(isNotEmpty($("#usersform input[name='fullname']"))
	&& isNotEmpty($("#usersform input[name='email']"))
	&& isNotEmpty($("#usersform input[name='password']"))
	&& isNotEmpty($("#usersform input[name='job']"))
	&& isNotEmpty($("#usersform input[name='companiestext']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#usersform input[name='id']").val(),
					fullname : $("#usersform input[name='fullname']").val(),
					email : $("#usersform input[name='email']").val(),
					password : $("#usersform input[name='password']").val(),
					job : $("#usersform input[name='job']").val(),
					phone : $("#usersform input[name='phone']").val(),
					roles : $("#usersform input[name='rolestext']").val(),
					companies : $("#usersform input[name='companiestext']").val(),
					action : 'adduser'
				},
				success : function(response){
					$("#usersform .lx-submit a").attr("class","");
					$("#usersform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadUsers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
						window.location.href = "users.php";
					}
				}
			});
		}
	}
	else{
		$("#usersform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-user","click",function(){
	filterClicked = "yes";
	loadUsers("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-user","click",function(){
	filterClicked = "yes";
	loadUsers("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-user","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-reset-user","click",function(){
	$(".lx-reset-pass").attr("data-id",$(this).attr("data-id"));
});

$(".lx-popup-content").delegate(".lx-reset-pass","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'resetpassword'
		},
		success : function(response){
			$(".lx-popup-content > a > .material-icons").trigger("click");
			loadUsers("1");
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien rénitialisé<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	});
});

$("body").delegate(".lx-restore-user","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoreuser'
		},
		success : function(response){
			loadUsers("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-user","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deleteuserpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadUsers("0");
			}
		}
	});
});

function loadUsers(state){
	if($(".lx-table-users .lx-loading").length === 0){
		$(".lx-table-users").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadusers'
		},
		success : function(response){
			$(".lx-table-users .lx-loading").remove();
			$(".lx-table-users").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-show-roles","click",function(){
	if($(this).parent().next("div").css("display") === "block"){
		$(this).parent().next("div").slideToggle();
		$(this).html('<i class="fa fa-plus"></i> Afficher plus');
	}
	else{
		$(this).parent().next("div").slideToggle();
		$(this).html('<i class="fa fa-minus"></i> Afficher moins');
	}
});

$("#accountform .lx-submit a").on("click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	isNotEmpty($("#accountform input[name='fullname']"));
	isPhone($("#accountform input[name='phone']"));
	if(isNotEmpty($("#accountform input[name='fullname']"))
	&& isPhone($("#accountform input[name='phone']"))){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				id : $("#accountform input[name='id']").val(),
				fullname : $("#accountform input[name='fullname']").val(),
				picture : $("#accountform input[name='picture']").val(),
				phone : $("#accountform input[name='phone']").val(),
				action : 'editaccount'
			},
			success : function(response){
				$("#accountform .lx-submit a i").remove();
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);
				window.location.href = "account.php";
			}
		});
	}
	else{
		$("#accountform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("#passwordform .lx-submit a").on("click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	var ajaxurl = "ajax.php";
	isNotEmpty($("#passwordform input[name='oldpassword']"));
	isNotEmpty($("#passwordform input[name='newpassword1']"));
	isNotEmpty($("#passwordform input[name='newpassword2']"));
	if(isNotEmpty($("#passwordform input[name='oldpassword']"))
	&& isNotEmpty($("#passwordform input[name='newpassword1']"))
	&& isNotEmpty($("#passwordform input[name='newpassword2']"))){
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				id : $("#passwordform input[name='id']").val(),
				oldpassword : $("#passwordform input[name='oldpassword']").val(),
				newpassword1 : $("#passwordform input[name='newpassword1']").val(),
				newpassword2 : $("#passwordform input[name='newpassword2']").val(),
				action : 'editpassword'
			},
			success : function(response){
				$("#passwordform .lx-submit a i").remove();
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				if(response === "1"){
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$("#passwordform input[name='oldpassword']").val("");
					$("#passwordform input[name='newpassword1']").val("");
					$("#passwordform input[name='newpassword2']").val("");
				}
				else if(response === "2"){
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir tous les champs<i class="material-icons"></i></p></div>');
				}
				else if(response === "3"){
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Ancien mot de passe incorrect<i class="material-icons"></i></p></div>');
				}
				else if(response === "4"){
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> les nouveaux mots de passes doivent être identiques<i class="material-icons"></i></p></div>');
				}
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);
			}
		});
	}
	else{
		$("#passwordform .lx-submit a i").remove();
	}
});

$("#commandsform select[name='company']").on("change",function(){
	var company = $(this).val();
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : company,
			category : $("#commandsform input[name='category']").val(),
			action : 'loadproductslist'
		},
		success : function(response){
			$("#commandsform select[name='product']").html(response);
			toDropDownTargeted("#commandsform select[name='product']");
			$("#commandsform select[name='product']").trigger("change");
		}
	});
	
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : company,
			category : $("#commandsform input[name='category']").val(),
			action : 'loadunitslist'
		},
		success : function(response){
			$("#commandsform select[name='unit']").html(response);
			toDropDownTargeted("#commandsform select[name='unit']");
			$("#commandsform select[name='unit']").trigger("change");
		}
	});

	if($("#commandsform input[name='type']").val() === "bc" || $("#commandsform input[name='type']").val() === "bre"){
		reloadSupplierList();
	}
	else{
		reloadClientList();
	}
	
	if($("#commandsform input[name='id']").val() === "0" || $("#commandsform input[name='id']").val() === "-1"){
		if($(this).val() !== ""){
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					company : company,
					type : $("#commandsform input[name='type']").val(),
					dateadd : $("#commandsform input[name='dateaddcommand']").val(),
					category : $("#commandsform input[name='categories']").val(),
					action : 'loadcode'
				},
				success : function(response){
				console.log(response);
					$("#commandsform input[name='code']").val(response);
					$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('input[name="dateaddcommand"]').val())+"-");
					$("#commandsform input[name='code']").trigger("blur");
				}
			});
		}
		else{
			$("#commandsform input[name='code']").val("");
			$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('input[name="dateaddcommand"]').val())+"-");
			$("#commandsform input[name='code']").trigger("blur");
		}
	}
	transform = 0;
});

$("#commandsform input[name='code']").on("keyup",function(){
	if($("#commandsform input[name='prefix']").val() === "BRC"){
		$("#commandsform input[name='code']").css("padding-left","77px");
	}
	else{
		$("#commandsform input[name='code']").css("padding-left","67px");
	}
});

$("#commandsform select[name='product']").on("change", function() {
    var $productSelect = $(this);
    var productVal = $productSelect.val();

    if (productVal !== "") {
        var $unitSelect = $("#commandsform select[name='unit']");
        var dataUnit = ($productSelect.find("option:selected").attr("data-unit") || "").trim().toLowerCase();

        // Find all case-insensitive matches in the unit select
        var $matches = $unitSelect.find("option").filter(function() {
            return ($(this).val() || "").trim().toLowerCase() === dataUnit;
        });

        // Select the *last* match (latest in DOM)
        if ($matches.length) {
            $unitSelect.val($matches.last().val());
        }

        // Update dropdown appearance
        toDropDownTargeted("#commandsform select[name='unit']");

        // Update TinyMCE content
        var html = $productSelect.find("option:selected").html();
        tinyMCE.get("textproduct").setContent(html);

        // Reset product select and dropdown
        $productSelect.val("");
        toDropDownTargeted("#commandsform select[name='product']");
    }
});


$(".lx-new-tva").on("click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#tvasform input[name='tva']").val("");
	$("#tvasform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-tva","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#tvasform input[name='tva']").val($(this).attr("data-titl"));
	$("#tvasform input[name='id']").val($(this).attr("data-id"));
});

$("#tvasform .lx-submit a").on("click",function(){
	isNotEmpty($("#tvasform input[name='tva']"));
	if(isNotEmpty($("#tvasform input[name='tva']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#tvasform input[name='id']").val(),
					tva : $("#tvasform input[name='tva']").val(),
					action : 'addtva'
				},
				success : function(response){
					$("#tvasform .lx-submit a").attr("class","");
					$("#tvasform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadTVAs();
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#tvasform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-tva","click",function(){
	filterClicked = "yes";
	loadTVAs();
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-tva","click",function(){
	filterClicked = "yes";
	loadTVAs();
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-tva","click",function(){
	$(".deleterecord1 .lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-tva","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoretva'
		},
		success : function(response){
			loadTVAs();
		}
	});
});

$("body").delegate(".lx-delete-permanently-tva","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletetvapermanently'
		},
		success : function(response){
			loadTVAs();
		}
	});
});

function loadTVAs(){
	if($(".lx-table-tvas .lx-loading").length === 0){
		$(".lx-table-tvas").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'loadtvas'
		},
		success : function(response){
			$(".lx-table-tvas .lx-loading").remove();
			$(".lx-table-tvas").html(response);
		}
	});
}

$(".lx-new-currency").on("click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#currencysform input[name='title']").val("");
	$("#currencysform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-currency","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#currencysform input[name='title']").val($(this).attr("data-titl"));
	$("#currencysform input[name='id']").val($(this).attr("data-id"));
});

$("#currencysform .lx-submit a").on("click",function(){
	isNotEmpty($("#currencysform input[name='title']"));
	if(isNotEmpty($("#currencysform input[name='title']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#currencysform input[name='id']").val(),
					title : $("#currencysform input[name='title']").val(),
					action : 'addcurrency'
				},
				success : function(response){
					$("#currencysform .lx-submit a").attr("class","");
					$("#currencysform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadCurrencys();
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#currencysform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-currency","click",function(){
	filterClicked = "yes";
	loadCurrencys();
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-currency","click",function(){
	filterClicked = "yes";
	loadCurrencys();
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-currency","click",function(){
	$(".deleterecord2 .lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-currency","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorecurrency'
		},
		success : function(response){
			loadCurrencys();
		}
	});
});

$("body").delegate(".lx-delete-permanently-currency","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletecurrencypermanently'
		},
		success : function(response){
			loadCurrencys();
		}
	});
});

function loadCurrencys(){
	if($(".lx-table-currencys .lx-loading").length === 0){
		$(".lx-table-currencys").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'loadcurrencys'
		},
		success : function(response){
			$(".lx-table-currencys .lx-loading").remove();
			$(".lx-table-currencys").html(response);
		}
	});
}

$(".lx-new-company").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#companiesform input[name='rs']").val("");
	$("#companiesform input[name='phone']").val("");
	$("#companiesform input[name='address']").val("");
	$("#companiesform input[name='email']").val("");
	$("#companiesform input[name='website']").val("");
	$("#companiesform input[name='capital']").val("");
	$("#companiesform input[name='rc']").val("");
	$("#companiesform input[name='patente']").val("");
	$("#companiesform input[name='iff']").val("");
	$("#companiesform input[name='cnss']").val("");
	$("#companiesform input[name='ice']").val("");
	$("#companiesform input[name='facture']").val("1").prop("readonly",false);
	$("#companiesform input[name='nh']").val("1").prop("readonly",false);
	$("#companiesform input[name='nf']").val("1").prop("readonly",false);
	$("#companiesform input[name='devis']").val("1").prop("readonly",false);
	$("#companiesform input[name='ph']").val("1").prop("readonly",false);
	$("#companiesform input[name='avoir']").val("1").prop("readonly",false);
	$("#companiesform input[name='br']").val("1").prop("readonly",false);
	$("#companiesform input[name='factureproforma']").val("1").prop("readonly",false);
	$("#companiesform input[name='bl']").val("1").prop("readonly",false);
	$("#companiesform input[name='bs']").val("1").prop("readonly",false);
	$("#companiesform input[name='blcommand']").val("1").prop("readonly",false);
	$("#companiesform input[name='brcommand']").val("1").prop("readonly",false);
	$("#companiesform input[name='headerheight']").val("3.5");
	$("#companiesform input[name='footerheight']").val("2.5");
	$("#companiesform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-company","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#companiesform input[name='rs']").val($(this).attr("data-rs"));
	$("#companiesform input[name='phone']").val($(this).attr("data-phone"));
	$("#companiesform input[name='address']").val($(this).attr("data-address"));
	$("#companiesform input[name='email']").val($(this).attr("data-email"));
	$("#companiesform input[name='website']").val($(this).attr("data-website"));
	$("#companiesform input[name='capital']").val($(this).attr("data-capital"));
	$("#companiesform input[name='rc']").val($(this).attr("data-rc"));
	$("#companiesform input[name='patente']").val($(this).attr("data-patente"));
	$("#companiesform input[name='iff']").val($(this).attr("data-iff"));
	$("#companiesform input[name='cnss']").val($(this).attr("data-cnss"));
	$("#companiesform input[name='ice']").val($(this).attr("data-ice"));
	$("#companiesform input[name='facture']").val($(this).attr("data-facture")).prop("readonly",Boolean.valueOf($(this).attr("data-facturereadonly")));
	$("#companiesform input[name='nh']").val($(this).attr("data-nh")).prop("readonly",Boolean.valueOf($(this).attr("data-nhreadonly")));
	$("#companiesform input[name='nf']").val($(this).attr("data-nf")).prop("readonly",Boolean.valueOf($(this).attr("data-nfreadonly")));
	$("#companiesform input[name='devis']").val($(this).attr("data-devis")).prop("readonly",Boolean.valueOf($(this).attr("data-devisreadonly")));
	$("#companiesform input[name='ph']").val($(this).attr("data-ph")).prop("readonly",Boolean.valueOf($(this).attr("data-phreadonly")));
	$("#companiesform input[name='avoir']").val($(this).attr("data-avoir")).prop("readonly",Boolean.valueOf($(this).attr("data-avoirreadonly")));
	$("#companiesform input[name='br']").val($(this).attr("data-br")).prop("readonly",Boolean.valueOf($(this).attr("data-brreadonly")));
	$("#companiesform input[name='factureproforma']").val($(this).attr("data-factureproforma")).prop("readonly",Boolean.valueOf($(this).attr("data-factureproformareadonly")));
	$("#companiesform input[name='bl']").val($(this).attr("data-bl")).prop("readonly",Boolean.valueOf($(this).attr("data-blreadonly")));
	$("#companiesform input[name='bs']").val($(this).attr("data-bs")).prop("readonly",Boolean.valueOf($(this).attr("data-bsreadonly")));
	$("#companiesform input[name='blcommand']").val($(this).attr("data-blcommand")).prop("readonly",Boolean.valueOf($(this).attr("data-blcommandreadonly")));
	$("#companiesform input[name='brcommand']").val($(this).attr("data-brcommand")).prop("readonly",Boolean.valueOf($(this).attr("data-brcommandreadonly")));
	$("#companiesform input[name='headerheight']").val($(this).attr("data-headerheight"));
	$("#companiesform input[name='footerheight']").val($(this).attr("data-footerheight"));
	$("#companiesform input[name='id']").val($(this).attr("data-id"));
});

$("#companiesform .lx-submit a").on("click",function(){
	isNotEmpty($("#companiesform input[name='rs']"));
	isNumber($("#companiesform input[name='facture']"));
	isNumber($("#companiesform input[name='nh']"));
	isNumber($("#companiesform input[name='nf']"));
	isNumber($("#companiesform input[name='devis']"));
	isNumber($("#companiesform input[name='ph']"));
	isNumber($("#companiesform input[name='avoir']"));
	isNumber($("#companiesform input[name='br']"));
	isNumber($("#companiesform input[name='factureproforma']"));
	isNumber($("#companiesform input[name='bl']"));
	isNumber($("#companiesform input[name='bs']"));
	isNumber($("#companiesform input[name='blcommand']"));
	isNumber($("#companiesform input[name='brcommand']"));
	if(isNotEmpty($("#companiesform input[name='rs']"))
	&& isNumber($("#companiesform input[name='facture']"))
	&& isNumber($("#companiesform input[name='nh']"))
	&& isNumber($("#companiesform input[name='nf']"))
	&& isNumber($("#companiesform input[name='devis']"))
	&& isNumber($("#companiesform input[name='ph']"))
	&& isNumber($("#companiesform input[name='avoir']"))
	&& isNumber($("#companiesform input[name='br']"))
	&& isNumber($("#companiesform input[name='factureproforma']"))
	&& isNumber($("#companiesform input[name='bl']"))
	&& isNumber($("#companiesform input[name='bs']"))
	&& isNumber($("#companiesform input[name='blcommand']"))
	&& isNumber($("#companiesform input[name='brcommand']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#companiesform input[name='id']").val(),
					rs : $("#companiesform input[name='rs']").val(),
					phone : $("#companiesform input[name='phone']").val(),
					address : $("#companiesform input[name='address']").val(),
					email : $("#companiesform input[name='email']").val(),
					website : $("#companiesform input[name='website']").val(),
					capital : $("#companiesform input[name='capital']").val(),
					rc : $("#companiesform input[name='rc']").val(),
					patente : $("#companiesform input[name='patente']").val(),
					iff : $("#companiesform input[name='iff']").val(),
					cnss : $("#companiesform input[name='cnss']").val(),
					ice : $("#companiesform input[name='ice']").val(),
					facture : $("#companiesform input[name='facture']").val(),
					nh : $("#companiesform input[name='nh']").val(),
					nf : $("#companiesform input[name='nf']").val(),
					devis : $("#companiesform input[name='devis']").val(),
					ph : $("#companiesform input[name='ph']").val(),
					avoir : $("#companiesform input[name='avoir']").val(),
					br : $("#companiesform input[name='br']").val(),
					factureproforma : $("#companiesform input[name='factureproforma']").val(),
					bl : $("#companiesform input[name='bl']").val(),
					bs : $("#companiesform input[name='bs']").val(),
					blcommand : $("#companiesform input[name='blcommand']").val(),
					brcommand : $("#companiesform input[name='brcommand']").val(),
					headerheight : $("#companiesform input[name='headerheight']").val(),
					footerheight : $("#companiesform input[name='footerheight']").val(),
					action : 'addcompany'
				},
				success : function(response){
					$("#companiesform .lx-submit a").attr("class","");
					$("#companiesform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadCompanies("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#companiesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-company","click",function(){
	filterClicked = "yes";
	loadCompanies("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-company","click",function(){
	filterClicked = "yes";
	loadCompanies("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-company","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-company","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorecompany'
		},
		success : function(response){
			loadCompanies("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-company","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletecompanypermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadCompanies("0");
			}
		}
	});
});

function loadCompanies(state){
	if($(".lx-table-companies .lx-loading").length === 0){
		$(".lx-table-companies").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			user : $("#user").attr("data-ids"),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadcompanies'
		},
		success : function(response){
			checkLC();
			$(".lx-table-companies .lx-loading").remove();
			$(".lx-table-companies").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-prospect").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#prospectsform select[name='company']").val($("#prospectsform select[name='company'] option:first").val()).prop("disabled",false);
	$("#prospectsform input[name='source']").val("");
	$("#prospectsform input[name='title']").val("");
	$("#prospectsform input[name='phone']").val("");
	$("#prospectsform input[name='whatsapp']").val("");
	$("#prospectsform input[name='email']").val("");
	$("#prospectsform select[name='gender']").val("");
	$("#prospectsform input[name='company1']").val("");
	$("#prospectsform input[name='ice']").val("");
	$("#prospectsform input[name='address']").val("");
	$(".lx-labels a i").remove();
	$("#prospectsform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-prospect","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#prospectsform input[name='title']").val($(this).attr("data-titl"));
	$("#prospectsform input[name='phone']").val($(this).attr("data-phone"));
	$("#prospectsform input[name='whatsapp']").val($(this).attr("data-whatsapp"));
	$("#prospectsform input[name='email']").val($(this).attr("data-email"));
	$("#prospectsform select[name='company']").val($(this).attr("data-company"));
	$("#prospectsform input[name='source']").val($(this).attr("data-source"));
	$("#prospectsform select[name='gender']").val($(this).attr("data-gender"));
	$("#prospectsform input[name='company1']").val($(this).attr("data-company1"));
	$("#prospectsform input[name='ice']").val($(this).attr("data-ice"));
	$("#prospectsform input[name='address']").val($(this).attr("data-address"));
	$(".lx-labels a i").remove();
	var labels = $(this).attr("data-labels").split(",");
	$(".lx-labels a").each(function(){
		for(var i=0;i<labels.length;i++){
			if($(this).attr("data-label") === labels[i]){
				$(this).prepend('<i class="fa fa-check"></i>');
			}
		}
	});
	$("#prospectsform input[name='id']").val($(this).attr("data-id"));
});

$("#prospectsform .lx-submit a").on("click",function(){
	var labels = "";
	$(".lx-labels a").each(function(){
		if($(this).find("i").length){
			labels += ","+$(this).attr("data-label");
		}
	});
	var k = 0;
	if($("#prospectsform input[name='email']").val() !== ""){
		isEmail($("#prospectsform input[name='email']"));
		if(!isEmail($("#prospectsform input[name='email']"))){
			k++;
		}
	}
	isNotEmpty($("#prospectsform select[name='gender']"));
	isNotEmpty($("#prospectsform select[name='company']"));
	isNotEmpty($("#prospectsform input[name='title']"));
	isPhone($("#prospectsform input[name='phone']"));
	if(isNotEmpty($("#prospectsform select[name='gender']"))
	&& isNotEmpty($("#prospectsform select[name='company']"))
	&& isNotEmpty($("#prospectsform input[name='title']"))
	&& isPhone($("#prospectsform input[name='phone']")) && k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#prospectsform input[name='id']").val(),
					company : $("#prospectsform select[name='company']").val(),
					title : $("#prospectsform input[name='title']").val(),
					phone : $("#prospectsform input[name='phone']").val(),
					whatsapp : $("#prospectsform input[name='whatsapp']").val(),
					email : $("#prospectsform input[name='email']").val(),
					company1 : $("#prospectsform input[name='company1']").val(),
					gender : $("#prospectsform select[name='gender']").val(),
					source : $("#prospectsform input[name='source']").val(),
					ice : $("#prospectsform input[name='ice']").val(),
					address : $("#prospectsform input[name='address']").val(),
					labels : labels,
					action : 'addprospect'
				},
				success : function(response){
					$("#prospectsform .lx-submit a").attr("class","");
					$("#prospectsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						// loadProspects("1");
						window.location.href = window.location.href;
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#prospectsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-prospect","click",function(){
	filterClicked = "yes";
	loadProspects("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-prospect","click",function(){
	filterClicked = "yes";
	loadProspects("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-prospect","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-prospect","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoreprospect'
		},
		success : function(response){
			loadProspects("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-prospect","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deleteprospectpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadProspects("0");
			}
		}
	});
});

function loadProspects(state){
	if($(".lx-table-prospects .lx-loading").length === 0){
		$(".lx-table-prospects").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			tags : $("#tags").attr("data-ids"),
			worker : $("#worker").attr("data-ids"),
			start : $(".lx-pagination ul").attr("data-start"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartupdate : $(".lx-keyword #datestartupdate").val(),
			dateendupdate : $(".lx-keyword #dateendupdate").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadprospects'
		},
		success : function(response){
			$(".lx-table-prospects .lx-loading").remove();
			$(".lx-table-prospects").html(response);
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-transform-prospect","click",function(){
	$(".lx-transform-record").attr("data-id",$(this).attr("data-id"));
});

$(".lx-transform-record").on("click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'transformprospect'
		},
		success : function(response){
			$(".lx-popup-content > a > .material-icons").trigger("click");
			loadProspects("1");
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-success"><i class="fa fa-check"></i> Bien transformé<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);	
		}
	});	
});

function reloadProspectsRecalls(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			table : $("#recallform input[name='table']").val(),
			type : ($("#commandsform input[name='type']").length?$("#commandsform input[name='type']").val():''),
			action : 'loadprospectsrecalls'
		},
		success : function(response){
			if(response !== ""){
				if($(".lx-page-header h2 .lx-reported-blink").length){
					$(".lx-page-header h2 .lx-reported-blink").remove();
				}
				$(".lx-page-header h2").append(response);
			}
			else{
				$(".lx-page-header h2 .lx-reported-blink").remove();
			}
		}
	});
}

$("body").delegate(".lx-page-header h2 .lx-reported-blink","click",function(){
	$("#daterecall").val($(this).attr("data-datestartrecall") + " - " + $(this).attr("data-dateendrecall"));
	$("#datestartrecall").val($(this).attr("data-datestartrecall"));
	$("#dateendrecall").val($(this).attr("data-dateendrecall"));
						if($(".lx-pagination ul").attr("data-table") === "users"){
							loadUsers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "suppliers"){
							loadSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "clients"){
							loadClients($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "devis"){
							loadDevis($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
							loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bl"){
							loadBL($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bs"){
							loadBS($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "br"){
							loadBR($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "factures"){
							loadFactures($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "avoirs"){
							loadAvoirs($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bc"){
							loadBC($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
							loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bre"){
							loadBRC($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nh"){
							loadNH($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nf"){
							loadNF($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "ph"){
							loadPH($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "caisse"){
							loadCaisse($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "companies"){
							loadCompanies($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "prospects"){
							loadProspects($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "labels"){
							loadLabels($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "doclabels"){
							loadDocLabels($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "messages"){
							loadMessages($(".lx-pagination ul").attr("data-state"));
						}
});

$("body").delegate(".lx-edit-prospect-note","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#prospectsnoteform textarea[name='note']").val($(this).attr("data-note"));
	$("#prospectsnoteform input[name='id']").val($(this).attr("data-id"));
});

$("#prospectsnoteform .lx-submit a").on("click",function(){
	isNotEmpty($("#prospectsnoteform textarea[name='note']"));
	if(isNotEmpty($("#prospectsnoteform textarea[name='note']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#prospectsnoteform input[name='id']").val(),
					note : $("#prospectsnoteform textarea[name='note']").val(),
					action : 'addprospectnote'
				},
				success : function(response){
					$("#prospectsnoteform .lx-submit a").attr("class","");
					$("#prospectsnoteform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadProspects("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#prospectsnoteform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-edit-recall-note","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#recallform input[name='recalldate']").val($(this).attr("data-recalldate"));
	$("#recallform textarea[name='note']").val($(this).attr("data-note"));
	$("#recallform input[name='id']").val($(this).attr("data-id"));
});

$("#recallform .lx-submit a").on("click",function(){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#recallform input[name='id']").val(),
					recalldate : $("#recallform input[name='recalldate']").val(),
					recallnote : $("#recallform textarea[name='note']").val(),
					table : $("#recallform input[name='table']").val(),
					action : 'addrecallnote'
				},
				success : function(response){
					$("#recallform .lx-submit a").attr("class","");
					$("#recallform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						if($(".lx-pagination ul").attr("data-table") === "users"){
							loadUsers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "suppliers"){
							loadSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "clients"){
							loadClients($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "devis"){
							loadDevis($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
							loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bl"){
							loadBL($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bs"){
							loadBS($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "br"){
							loadBR($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "factures"){
							loadFactures($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "avoirs"){
							loadAvoirs($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bc"){
							loadBC($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
							loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "bre"){
							loadBRC($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nh"){
							loadNH($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nf"){
							loadNF($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "ph"){
							loadPH($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "caisse"){
							loadCaisse($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "companies"){
							loadCompanies($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "prospects"){
							loadProspects($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "labels"){
							loadLabels($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "doclabels"){
							loadDocLabels($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "messages"){
							loadMessages($(".lx-pagination ul").attr("data-state"));
						}
						loadNotification();
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
});

$(".lx-popup-content").delegate("#sendmessagesform input[name='columns']","click",function(){
	var columns = "";
	var i = 1;
	$("#sendmessagesform input[name='columns']").each(function(){
		if($(this).prop("checked") === true){
			columns += "&d"+i+"=1";
		}
		else{
			columns += "&d"+i+"=0";
		}
		i++;
	});
	$("#sendmessagesform input[name='columnstxt']").val(columns);
});

$("body").delegate(".lx-send-message","click",function(){
	var el = $(this);
	var id = $(this).attr("data-id");
	if($(".lx-print-options .lx-loading").length === 0){
		$(".lx-print-options").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'loadprintoptions'
		},
		success : function(response){
			$(".lx-print-options .lx-loading").remove();
			$(".lx-print-options").html(response);
			
			$("#sendmessagesform input[name='button']").val(el.attr("data-button"));
			if(el.attr("data-button") === "Envoyer le document"){
				$(".printdoc").css("display","block");
				$("#sendmessagesform select[name='type']").parent().parent().css("display","none");
				$("#sendmessagesform select[name='type']").val("E-mail").trigger("change");
			}
			else{
				$(".printdoc").css("display","none");
				$("#sendmessagesform select[name='type']").val("").trigger("change");
				$("#sendmessagesform select[name='type']").parent().parent().css("display","block");		
			}
			
			$(".blbs").css("display","none");
			$(".bcbre").css("display","none");
			$(".fc").css("display","none");

			if(el.attr("data-typedoc") === "Bon de livraison" || el.attr("data-typedoc") === "Bon de sortie"){
				$(".blbs").css("display","block");
			}
			if(el.attr("data-typedoc") !== "Bon de commande" && el.attr("data-typedoc") !== "Bon de réception"){
				$(".bcbre").css("display","block");
			}
			if(el.attr("data-typedoc") === "Facture" || el.attr("data-typedoc") === "Note d'honoraire" || el.attr("data-typedoc") === "Note de frais"){
				$(".fc").css("display","block");
			}
		}
	});
	
	$("#sendmessagesform select[name='model']").val("");
	$("#sendmessagesform input[name='subject']").val("");
	$("#sendmessagesform textarea[name='message']").val("");
	$("#sendmessagesform input[name='email1']").val($(this).attr("data-email1"));
	$("#sendmessagesform input[name='sender']").val($(this).attr("data-sender"));
	$("#sendmessagesform input[name='senderphone']").val($(this).attr("data-senderphone"));
	$("#sendmessagesform input[name='fonction']").val($(this).attr("data-function"));
	$("#sendmessagesform input[name='typedoc']").val($(this).attr("data-typedoc"));
	$("#sendmessagesform input[name='whatsapp']").val($(this).attr("data-whatsapp"));
	$("#sendmessagesform input[name='phone']").val($(this).attr("data-phone"));
	$("#sendmessagesform input[name='email']").val($(this).attr("data-email"));
	$("#sendmessagesform input[name='gender']").val($(this).attr("data-gender"));
	$("#sendmessagesform input[name='societe']").val($(this).attr("data-societe"));
	$("#sendmessagesform input[name='company']").val($(this).attr("data-company"));
	$("#sendmessagesform input[name='utilisateur']").val($(this).attr("data-utilisateur"));
	$("#sendmessagesform input[name='fullname']").val($(this).attr("data-client"));
	$("#sendmessagesform input[name='numero_document']").val($(this).attr("data-numero_document"));
	$("#sendmessagesform input[name='ref_commande']").val($(this).attr("data-ref_commande"));	
	$("#sendmessagesform input[name='montant']").val($(this).attr("data-montant"));
	$("#sendmessagesform input[name='delai_paiement']").val($(this).attr("data-delai_paiement"));
	$("#sendmessagesform input[name='echeance_paiement']").val($(this).attr("data-echeance_paiement"));
	$("#sendmessagesform input[name='id']").val($(this).attr("data-id"));

	document.querySelector('select[name="model"]').addEventListener('change', function () {
	  const selectedOption = this.selectedOptions[0];
	  const messageTemplate = selectedOption.dataset.message;
	  const subjectTemplate = selectedOption.dataset.subject;

	  if (!messageTemplate) return;

		  // Get values from hidden inputs
		const fullname            = document.querySelector('input[type="hidden"][name="fullname"]')?.value || '';
		const phone               = document.querySelector('input[type="hidden"][name="phone"]')?.value || '';
		const email               = document.querySelector('input[type="hidden"][name="email"]')?.value || '';
		const gender              = document.querySelector('input[type="hidden"][name="gender"]')?.value || '';
		const sender              = document.querySelector('input[type="hidden"][name="sender"]')?.value || '';
		const senderphone         = document.querySelector('input[type="hidden"][name="senderphone"]')?.value || '';
		const fonction            = document.querySelector('input[type="hidden"][name="fonction"]')?.value || '';
		const typedoc             = document.querySelector('input[type="hidden"][name="typedoc"]')?.value || '';
		const fournisseur         = document.querySelector('input[type="hidden"][name="fournisseur"]')?.value || '';
		const numero_document     = document.querySelector('input[type="hidden"][name="numero_document"]')?.value || '';
		const ref_commande        = document.querySelector('input[type="hidden"][name="ref_commande"]')?.value || '';
		const montant             = document.querySelector('input[type="hidden"][name="montant"]')?.value || '';
		const societe             = document.querySelector('input[type="hidden"][name="societe"]')?.value || '';
		const delai_paiement      = document.querySelector('input[type="hidden"][name="delai_paiement"]')?.value || '';
		const echeance_paiement   = document.querySelector('input[type="hidden"][name="echeance_paiement"]')?.value || '';
		const client         	  = document.querySelector('input[type="hidden"][name="fullname"]')?.value || '';
		const client_ou_fournisseur = document.querySelector('input[type="hidden"][name="fullname"]')?.value || '';
		const utilisateur         = document.querySelector('input[type="hidden"][name="utilisateur"]')?.value || '';
	  // Replace placeholders
	  const finalMessage = messageTemplate
		.replace(/\[fullname\]/gi, fullname)
		.replace(/\[phone\]/gi, phone)
		.replace(/\[email\]/gi, email)
		.replace(/\[email_utilisateur\]/gi, sender)
		.replace(/\[telephone_utilisateur\]/gi, senderphone)
		.replace(/\[fonction_utilisateur\]/gi, fonction)
		.replace(/\[type_document\]/gi, typedoc)
		.replace(/\[gender\]/gi, gender)
		.replace(/\[fournisseur\]/gi, fournisseur)
		.replace(/\[numero_document\]/gi, numero_document)
		.replace(/\[ref_commande\]/gi, ref_commande)
		.replace(/\[montant\]/gi, montant)
		.replace(/\[societe\]/gi, societe)
		.replace(/\[delai_paiement\]/gi, delai_paiement)
		.replace(/\[echeance_paiement\]/gi, echeance_paiement)
		.replace(/\[client\]/gi, client)
		.replace(/\[client_ou_fournisseur\]/gi, client_ou_fournisseur)
		.replace(/\[utilisateur\]/gi, utilisateur);

	  // Put the final message into the textarea
	  document.querySelector('textarea[name="message"]').value = finalMessage;
	  
	  // Replace placeholders
	  const finalSubject = subjectTemplate
		.replace(/\[fullname\]/gi, fullname)
		.replace(/\[phone\]/gi, phone)
		.replace(/\[email\]/gi, email)
		.replace(/\[email_utilisateur\]/gi, sender)
		.replace(/\[telephone_utilisateur\]/gi, senderphone)
		.replace(/\[fonction_utilisateur\]/gi, fonction)
		.replace(/\[type_document\]/gi, typedoc)
		.replace(/\[gender\]/gi, gender)
		.replace(/\[fournisseur\]/gi, fournisseur)
		.replace(/\[numero_document\]/gi, numero_document)
		.replace(/\[ref_commande\]/gi, ref_commande)
		.replace(/\[montant\]/gi, montant)
		.replace(/\[societe\]/gi, societe)
		.replace(/\[delai_paiement\]/gi, delai_paiement)
		.replace(/\[echeance_paiement\]/gi, echeance_paiement)
		.replace(/\[client\]/gi, client)
		.replace(/\[client_ou_fournisseur\]/gi, client_ou_fournisseur)
		.replace(/\[utilisateur\]/gi, utilisateur);

	  // Put the final message into the textarea
	  document.querySelector('input[name="subject"]').value = finalSubject;
	});
});

var htmlmodel = $("#sendmessagesform select[name='model']").clone().html();
$("#sendmessagesform select[name='type']").on("change",function(){
	$("#sendmessagesform select[name='model']").html(htmlmodel);
	var type = $(this).val();
	$("#sendmessagesform select[name='model'] option[data-type]").each(function(){
		if(!$(this).attr("data-type").split(",").includes(type) || $(this).attr("data-button") !== $("#sendmessagesform input[name='button']").val()){
			$(this).remove();
		}
	});
	
	$(".via").css("display","none");
	$(".viaemail").css("display","none");
	if($(this).val() !== ""){
		$(".via").css("display","block");
		if($(this).val() === "WhatsApp"){
			$(".via").removeClass("lx-g2");
			$("#sendmessagesform input[name='contact']").val($("#sendmessagesform input[name='whatsapp']").val()).attr("data-message","Saisissez un whatsapp");
		}
		if($(this).val() === "E-mail"){
			$(".viaemail").css("display","block");
			$(".via").addClass("lx-g2");
			$("#sendmessagesform input[name='contact']").val($("#sendmessagesform input[name='email1']").val()).attr("data-message","Saisissez un E-mail valid");
			$("#sendmessagesform input[name='sender']").val($("#sendmessagesform input[name='sender']").val());
		}
	}
	$("#sendmessagesform input[name='contact']").removeAttr("style");
	$("#sendmessagesform input[name='contact']").parent().find("ins").remove();
});	

$("#sendmessagesform select[name='model']").on("change",function(){
	$("#sendmessagesform textarea[name='message']").val($(this).find("option:selected").attr("data-message"));
	$("#sendmessagesform input[name='subject']").val($(this).find("option:selected").attr("data-subject"));
});

function normalizeMoroccanPhoneNumber(input) {
    // Remove all non-digit characters
    let phone = input.replace(/\D/g, '');

    // If starts with 0 and has 10 digits, convert to 212 + last 9 digits
    if (phone.length === 10 && phone.startsWith('0')) {
        phone = '212' + phone.substring(1);
    }
    // If starts with 212 and has 12 digits, keep it
    else if (phone.length === 12 && phone.startsWith('212')) {
        // Already in correct format
    }
    // If it has only 9 digits, assume missing 212 prefix
    else if (phone.length === 9) {
        phone = '212' + phone;
    }
    else {
        return null; // Invalid length
    }

    // Final validation: must start with 212 and have exactly 12 digits
    return /^212\d{9}$/.test(phone) ? phone : null;
}

$("#sendmessagesform input[name='columns']").on("click",function(){
	var columns = "";
	var i = 1;
	$("#sendmessagesform input[name='columns']").each(function(){
		if($(this).prop("checked") === true){
			columns += "&d"+i+"=1";
		}
		else{
			columns += "&d"+i+"=0";
		}
		i++;
	});
	$("#sendmessagesform input[name='columnstxt']").val(columns);
});

$("#sendmessagesform .lx-submit a").on("click",function(){
	isNotEmpty($("#sendmessagesform select[name='type']"));
	var k = 0;
	isNotEmpty($("#sendmessagesform select[name='signature']"));
	isNotEmpty($("#sendmessagesform select[name='placement']"));
	isNotEmpty($("#sendmessagesform select[name='header']"));
	isNotEmpty($("#sendmessagesform select[name='refdocs']"));
	isNotEmpty($("#sendmessagesform select[name='ice']"));
	var k = 0;
	if($(".blbs").css("display") === "block"){
		if(!isNotEmpty($("#sendmessagesform select[name='valeur']"))){
			k++;
		}
	}
	if($(".blbs").css("display") === "block"){
		isNotEmpty($("#sendmessagesform select[name='valeur']"));
		if(!isNotEmpty($("#sendmessagesform select[name='valeur']"))){
			k++;
		}
	}
	if($(".fc").css("display") === "block"){
		isNotEmpty($("#sendmessagesform select[name='payments']"));
		isNotEmpty($("#sendmessagesform select[name='avoirs']"));
		isNotEmpty($("#sendmessagesform select[name='reste']"));
		if(!isNotEmpty($("#sendmessagesform select[name='payments']"))
		|| !isNotEmpty($("#sendmessagesform select[name='avoirs']"))
		|| !isNotEmpty($("#sendmessagesform select[name='reste']"))){
			k++;
		}
	}
	if(!isNotEmpty($("#sendmessagesform select[name='signature']"))
	|| !isNotEmpty($("#sendmessagesform select[name='placement']"))
	|| !isNotEmpty($("#sendmessagesform select[name='header']"))
	|| !isNotEmpty($("#sendmessagesform select[name='refdocs']"))){
		k++;
	}
	var label = "&label="+$("#sendmessagesform input[name='signaturetext']").val();
	var company = "&company="+$("#sendmessagesform input[name='company']").val();
	var doc = "&document="+$("#sendmessagesform input[name='document']").val();
	var params = $("#sendmessagesform select[name='placement']").val()+$("#sendmessagesform select[name='payments']").val()+$("#sendmessagesform select[name='avoirs']").val()+$("#sendmessagesform select[name='reste']").val()+$("#sendmessagesform select[name='signature']").val()+$("#sendmessagesform select[name='header']").val()+$("#sendmessagesform select[name='ice']").val()+$("#sendmessagesform select[name='valeur']").val()+$("#sendmessagesform input[name='columnstxt']").val()+$("#sendmessagesform select[name='refdocs']").val()+label+doc+company;
	if($("#sendmessagesform select[name='type']").val() === "E-mail"){
		isEmail($("#sendmessagesform input[name='sender']"));
		if(!isEmail($("#sendmessagesform input[name='sender']"))){
			k = 1;
		}
		isEmail($("#sendmessagesform input[name='contact']"));
		if(!isEmail($("#sendmessagesform input[name='contact']"))){
			k = 1;
		}		
		isNotEmpty($("#sendmessagesform input[name='subject']"));
		if(!isNotEmpty($("#sendmessagesform input[name='subject']"))){
			k = 1;
		}
	}
	else{
		isNotEmpty($("#sendmessagesform input[name='contact']"));
		if(!isNotEmpty($("#sendmessagesform input[name='contact']"))){
			k = 1;
		}
	}
	isNotEmpty($("#sendmessagesform select[name='model']"));
	isNotEmpty($("#sendmessagesform textarea[name='message']"));
	if(isNotEmpty($("#sendmessagesform select[name='type']"))
	&& isNotEmpty($("#sendmessagesform select[name='model']"))
	&& isNotEmpty($("#sendmessagesform textarea[name='message']"))
	&& k === 0){
		if($("#sendmessagesform select[name='type']").val() === "WhatsApp"){
			var message = encodeURIComponent($("#sendmessagesform textarea[name='message']").val()).replace(/%0A/g, '%0A');
			window.open("https://wa.me/"+normalizeMoroccanPhoneNumber($("#sendmessagesform input[name='contact']").val())+"?text="+message, "_blank");
			$(".lx-popup-content > a > .material-icons").trigger("click");
		}
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#sendmessagesform input[name='id']").val(),
					message : $("#sendmessagesform textarea[name='message']").val(),
					subject : $("#sendmessagesform input[name='subject']").val(),
					from : $("#sendmessagesform input[name='sender']").val(),
					email : $("#sendmessagesform input[name='contact']").val(),
					company : $("#sendmessagesform input[name='societe']").val(),
					button : $("#sendmessagesform input[name='button']").val(),
					type : $("#sendmessagesform select[name='type']").val(),
					page : $("#sendmessagesform input[name='page']").val(),
					params : params,
					action : 'savemessage'
				},
				success : function(response){
					console.log(response);
					$("#sendmessagesform .lx-submit a").attr("class","");
					$("#sendmessagesform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien envoyé<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
					window.setTimeout(function(){
						$(".lx-message-history[data-id='"+$("#sendmessagesform input[name='id']").val()+"'][data-button='"+$("#sendmessagesform input[name='button']").val()+"']").trigger("click");
					},500);
				}
			});
		}
	}
	else{
		$("#sendmessagesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$(".lx-popup-content").delegate(".lx-yes-delete-message-history","click",function(){
	var el = $(this);
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletemessagehistory'
		},
		success : function(response){
			el.parent().parent().parent().remove();
		}
	});
});

$("body").delegate(".lx-message-history","click",function(){
	if($(".lx-table-messageshistory .lx-loading").length === 0){
		$(".lx-table-messageshistory").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var id = $(this).attr("data-id");
	var button = $(this).attr("data-button");
	var page = $(this).attr("data-page");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			page : page,
			button : button,
			action : 'loadmessageshistory'
		},
		success : function(response){
			$(".lx-table-messageshistory .lx-loading").remove();
			$(".lx-table-messageshistory").html(response);
		}
	});
});

$(".lx-new-label").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	if($("#labelsform input[name='company']").attr("data-one") !== "1"){
		$("#labelsform input[name='company']").attr("data-ids","");
		$("#labelsform input[name='company']").val("");
		$("#labelsform .lx-advanced-select input[type='checkbox']").each(function(){
			$(this).prop("checked",false);
		});
	}
	$("#labelsform input[name='label']").val("");
	$("#labelsform input[name='color']").val("");
	$("#labelsform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-label","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#labelsform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#labelsform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split("||");
	$("#labelsform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	$("#labelsform input[name='label']").val($(this).attr("data-label"));
	$("#labelsform input[name='color']").val($(this).attr("data-color"));
	$("#labelsform input[name='id']").val($(this).attr("data-id"));
});

$("#labelsform .lx-submit a").on("click",function(){	
	isNotEmpty($("#labelsform input[name='company']"));
	isNotEmpty($("#labelsform input[name='label']"));
	isNotEmpty($("#labelsform input[name='color']"));
	if(isNotEmpty($("#labelsform input[name='company']"))
	&& isNotEmpty($("#labelsform input[name='label']"))
	&& isNotEmpty($("#labelsform input[name='color']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#labelsform input[name='id']").val(),
					company : $("#labelsform input[name='company']").attr("data-ids"),
					label : $("#labelsform input[name='label']").val(),
					color : $("#labelsform input[name='color']").val(),
					action : 'addlabel'
				},
				success : function(response){
					$("#labelsform .lx-submit a").attr("class","");
					$("#labelsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadLabels("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#labelsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-label","click",function(){
	filterClicked = "yes";
	loadLabels("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-label","click",function(){
	filterClicked = "yes";
	loadLabels("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-label","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-label","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorelabel'
		},
		success : function(response){
			loadLabels("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-label","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletelabelpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadLabels("0");
			}
		}
	});
});

function loadLabels(state){
	if($(".lx-table-labels .lx-loading").length === 0){
		$(".lx-table-labels").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadlabels'
		},
		success : function(response){
			$(".lx-table-labels .lx-loading").remove();
			$(".lx-table-labels").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-doclabel").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield doclabel > ins").remove();
	$(".lx-add-form .lx-textfield doclabel > input").removeAttr("style");
	$(".lx-add-form .lx-textfield doclabel > select").removeAttr("style");$(".lx-add-form .lx-textfield doclabel > textarea").removeAttr("style");
	if($("#doclabelsform input[name='company']").attr("data-one") !== "1"){
		$("#doclabelsform input[name='company']").attr("data-ids","");
		$("#doclabelsform input[name='company']").val("");
		$("#doclabelsform .lx-advanced-select input[type='checkbox']").each(function(){
			$(this).prop("checked",false);
		});
	}
	$("#doclabelsform input[name='label']").val("");
	$("#doclabelsform input[name='color']").val("");
	$("#doclabelsform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-doclabel","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield doclabel > ins").remove();
	$(".lx-add-form .lx-textfield doclabel > input").removeAttr("style");
	$(".lx-add-form .lx-textfield doclabel > select").removeAttr("style");$(".lx-add-form .lx-textfield doclabel > textarea").removeAttr("style");
	$("#doclabelsform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#doclabelsform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split("||");
	$("#doclabelsform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	$("#doclabelsform input[name='label']").val($(this).attr("data-label"));
	$("#doclabelsform input[name='color']").val($(this).attr("data-color"));
	$("#doclabelsform input[name='id']").val($(this).attr("data-id"));
});

$("#doclabelsform .lx-submit a").on("click",function(){	
	isNotEmpty($("#doclabelsform input[name='company']"));
	isNotEmpty($("#doclabelsform input[name='label']"));
	isNotEmpty($("#doclabelsform input[name='color']"));
	if(isNotEmpty($("#doclabelsform input[name='company']"))
	&& isNotEmpty($("#doclabelsform input[name='label']"))
	&& isNotEmpty($("#doclabelsform input[name='color']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#doclabelsform input[name='id']").val(),
					company : $("#doclabelsform input[name='company']").attr("data-ids"),
					label : $("#doclabelsform input[name='label']").val(),
					color : $("#doclabelsform input[name='color']").val(),
					type : $("#doclabelsform input[name='type']").val(),
					action : 'adddoclabel'
				},
				success : function(response){
					$("#doclabelsform .lx-submit a").attr("class","");
					$("#doclabelsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadDocLabels("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#doclabelsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-doclabel","click",function(){
	filterClicked = "yes";
	loadDocLabels("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-doclabel","click",function(){
	filterClicked = "yes";
	loadDocLabels("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-doclabel","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-doclabel","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoredoclabel'
		},
		success : function(response){
			loadDocLabels("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-doclabel","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletedoclabelpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadDocLabels("0");
			}
		}
	});
});

function loadDocLabels(state){
	if($(".lx-table-doclabels .lx-loading").length === 0){
		$(".lx-table-doclabels").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			type : $("#type").val(),
			company : $("#company").attr("data-ids"),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loaddoclabels'
		},
		success : function(response){
			$(".lx-table-doclabels .lx-loading").remove();
			$(".lx-table-doclabels").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-message").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield message > input").removeAttr("style");
	$(".lx-add-form .lx-textfield message > select").removeAttr("style");$(".lx-add-form .lx-textfield message > textarea").removeAttr("style");
	if($("#messagesform input[name='company']").attr("data-one") !== "1"){
		$("#messagesform input[name='company']").val("");
		$("#messagesform .lx-advanced-select input[type='checkbox']").each(function(){
			$(this).prop("checked",false);
		});
	}
	$("#messagesform input[name='title']").val("");
	$(".type").css("display","block");
	$("#messagesform select[name='type']").val("");
	$("#messagesform select[name='button']").val("");
	$(".subject").css("display","none");
	$("#messagesform input[name='subject']").val("");
	$("#messagesform textarea[name='message']").val("");
	$("#messagesform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-message","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield message > input").removeAttr("style");
	$(".lx-add-form .lx-textfield message > select").removeAttr("style");$(".lx-add-form .lx-textfield message > textarea").removeAttr("style");
	$("#messagesform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#messagesform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split("||");
	$("#messagesform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	$("#messagesform input[name='title']").val($(this).attr("data-titl"));
	$("#messagesform select[name='type']").val($(this).attr("data-type"));
	$("#messagesform select[name='button']").val($(this).attr("data-button"));
	$(".type").css("display","none");
	if($(this).attr("data-button") !== "Envoyer le document"){
		$(".type").css("display","block");
	}
	$(".subject").css("display","none");
	if($(this).attr("data-type") === "E-mail"){
		$(".subject").css("display","block");
	}
	$("#messagesform input[name='subject']").val($(this).attr("data-subject"));
	$("#messagesform textarea[name='message']").val($(this).attr("data-message"));
	$("#messagesform input[name='id']").val($(this).attr("data-id"));
});

$("#messagesform select[name='type']").on("change",function(){
	$(".subject").css("display","none");
	if($(this).val() === "E-mail"){
		$(".subject").css("display","block");
	}	
});

$("#messagesform select[name='button']").on("change",function(){
	$(".type").css("display","none");
	if($(this).val() !== "Envoyer le document"){
		$(".type").css("display","block");
	}
});

$("#messagesform .lx-submit a").on("click",function(){	
	isNotEmpty($("#messagesform input[name='company']"));
	isNotEmpty($("#messagesform input[name='title']"));
	var k = 0;
	if($("#messagesform select[name='button']").val() !== "Envoyer le document"){
		isNotEmpty($("#messagesform select[name='type']"));
		if(!isNotEmpty($("#messagesform select[name='type']"))){
			k++;
		}
	}
	isNotEmpty($("#messagesform select[name='button']"));
	isNotEmpty($("#messagesform textarea[name='message']"));
	if(isNotEmpty($("#messagesform input[name='company']"))
	&& isNotEmpty($("#messagesform input[name='title']"))
	&& isNotEmpty($("#messagesform select[name='button']"))
	&& isNotEmpty($("#messagesform textarea[name='message']")) && k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#messagesform input[name='id']").val(),
					title : $("#messagesform input[name='title']").val(),
					type : $("#messagesform select[name='type']").val(),
					button : $("#messagesform select[name='button']").val(),
					company : $("#messagesform input[name='company']").attr("data-ids"),
					page : $("#messagesform input[name='page']").val(),
					subject : $("#messagesform input[name='subject']").val(),
					message : $("#messagesform textarea[name='message']").val(),
					action : 'addmessage'
				},
				success : function(response){
					$("#messagesform .lx-submit a").attr("class","");
					$("#messagesform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadMessages("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#messagesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-message","click",function(){
	filterClicked = "yes";
	loadMessages("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-message","click",function(){
	filterClicked = "yes";
	loadMessages("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-message","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-message","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoremessage'
		},
		success : function(response){
			loadMessages("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-message","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletemessagepermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadMessages("0");
			}
		}
	});
});

function loadMessages(state){
	if($(".lx-table-messages .lx-loading").length === 0){
		$(".lx-table-messages").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			type : $("#type").val(),
			page : $("#page").val(),
			company : $("#company").attr("data-ids"),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadmessages'
		},
		success : function(response){
			$(".lx-table-messages .lx-loading").remove();
			$(".lx-table-messages").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-new-bankaccount","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#bankaccountsform input[name='rib']").val("");
	$("#bankaccountsform input[name='bank']").val("");
	$("#bankaccountsform input[name='agency']").val("");
	$("#bankaccountsform input[name='city']").val("");
	$("#bankaccountsform input[name='company']").val($(this).attr("data-company"));
	$("#bankaccountsform input[name='id']").val($(this).attr("data-id"));
});

$("body").delegate(".lx-edit-bankaccount","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#bankaccountsform input[name='rib']").val($(this).attr("data-rib"));
	$("#bankaccountsform input[name='bank']").val($(this).attr("data-bank"));
	$("#bankaccountsform input[name='agency']").val($(this).attr("data-agency"));
	$("#bankaccountsform input[name='city']").val($(this).attr("data-city"));
	$("#bankaccountsform input[name='company']").val($(this).attr("data-company"));
	$("#bankaccountsform input[name='id']").val($(this).attr("data-id"));
});

$("#bankaccountsform .lx-submit a").on("click",function(){
	isNotEmpty($("#bankaccountsform input[name='rib']"));
	isNotEmpty($("#bankaccountsform input[name='bank']"));
	isNotEmpty($("#bankaccountsform input[name='agency']"));
	isNotEmpty($("#bankaccountsform input[name='city']"));
	if(isNotEmpty($("#bankaccountsform input[name='rib']"))
	&& isNotEmpty($("#bankaccountsform input[name='bank']"))
	&& isNotEmpty($("#bankaccountsform input[name='agency']"))
	&& isNotEmpty($("#bankaccountsform input[name='city']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#bankaccountsform input[name='id']").val(),
					rib : $("#bankaccountsform input[name='rib']").val(),
					bank : $("#bankaccountsform input[name='bank']").val(),
					agency : $("#bankaccountsform input[name='agency']").val(),
					city : $("#bankaccountsform input[name='city']").val(),
					company : $("#bankaccountsform input[name='company']").val(),
					action : 'addbankaccount'
				},
				success : function(response){
					$("#bankaccountsform .lx-submit a").attr("class","");
					$("#bankaccountsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						if(!$("#bankaccountsform select[name='bank'] option[value='"+$("#bankaccountsform input[name='bank']").val()+"']").length){
							$("#bankaccountsform select[name='bank']").append('<option value="'+$("#bankaccountsform input[name='bank']").val()+'">'+$("#bankaccountsform input[name='bank']").val()+'</option>');
							toDropDown();
						}
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadCompanies("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#bankaccountsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-bankaccount","click",function(){
	filterClicked = "yes";
	loadCompanies("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-bankaccount","click",function(){
	filterClicked = "yes";
	loadCompanies("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-bankaccount","click",function(){
	$(".deleterecord1 .lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-bankaccount","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorebankaccount'
		},
		success : function(response){
			loadCompanies("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-bankaccount","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletebankaccountpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadCompanies("0");
			}
		}
	});
});

$("#suppliersbankaccountsform select[name='type']").on("change",function(){
	$(".lx-in").css("display","none");
	$(".lx-out").css("display","none");
	if($(this).val() === "in"){
		$(".lx-in").css("display","block");
	}
	if($(this).val() === "out"){
		$(".lx-out").css("display","block");
	}
});

$("body").delegate(".lx-new-supplierbankaccount","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#suppliersbankaccountsform select[name='type']").val("").trigger("change");
	$("#suppliersbankaccountsform input[name='rib']").val("");
	$("#suppliersbankaccountsform input[name='iban']").val("");
	$("#suppliersbankaccountsform input[name='swift']").val("");
	$("#suppliersbankaccountsform input[name='address']").val("");
	$("#suppliersbankaccountsform select[name='bank']").val("");
	$("#suppliersbankaccountsform input[name='agency']").val("");
	$("#suppliersbankaccountsform input[name='supplier']").val($(this).attr("data-supplier"));
	$("#suppliersbankaccountsform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("body").delegate(".lx-edit-supplierbankaccount","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#suppliersbankaccountsform select[name='type']").val($(this).attr("data-location")).trigger("change");;
	$("#suppliersbankaccountsform input[name='rib']").val($(this).attr("data-rib"));
	$("#suppliersbankaccountsform input[name='iban']").val($(this).attr("data-iban"));
	$("#suppliersbankaccountsform input[name='swift']").val($(this).attr("data-swift"));
	$("#suppliersbankaccountsform input[name='address']").val($(this).attr("data-address"));
	$("#suppliersbankaccountsform select[name='bank']").val($(this).attr("data-bank"));
	$("#suppliersbankaccountsform input[name='agency']").val($(this).attr("data-agency"));
	$("#suppliersbankaccountsform input[name='supplier']").val($(this).attr("data-supplier"));
	$("#suppliersbankaccountsform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

function removeAllSpaces(str) {
  return str.replace(/\s+/g, '');
}

$("#suppliersbankaccountsform .lx-submit a").on("click",function(){
	var k = 0;
	
	isNotEmpty($("#suppliersbankaccountsform select[name='type']"));
	if(!isNotEmpty($("#suppliersbankaccountsform select[name='type']"))){
		k++;
	}
	if($("#suppliersbankaccountsform select[name='type']").val() === "in"){
		isNotEmpty($("#suppliersbankaccountsform input[name='rib']"));
		isNotEmpty($("#suppliersbankaccountsform input[name='bank']"));	
		if(!isNotEmpty($("#suppliersbankaccountsform input[name='rib']"))
		|| removeAllSpaces($("#suppliersbankaccountsform input[name='rib']").val()).length !== 24
		|| !isNotEmpty($("#suppliersbankaccountsform input[name='bank']"))){
			k++;
		}
	}
	else if($("#suppliersbankaccountsform select[name='type']").val() === "out"){
		isNotEmpty($("#suppliersbankaccountsform input[name='iban']"));
		isNotEmpty($("#suppliersbankaccountsform input[name='swift']"));
		isNotEmpty($("#suppliersbankaccountsform input[name='address']"));
		isNotEmpty($("#suppliersbankaccountsform input[name='bank']"));		
		if(!isNotEmpty($("#suppliersbankaccountsform input[name='iban']"))
		|| !isNotEmpty($("#suppliersbankaccountsform input[name='swift']"))
		|| !isNotEmpty($("#suppliersbankaccountsform input[name='address']"))
		|| !isNotEmpty($("#suppliersbankaccountsform input[name='bank']"))){
			k++;
		}
	}

	if(k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#suppliersbankaccountsform input[name='id']").val(),
					rib : $("#suppliersbankaccountsform input[name='rib']").val(),
					iban : $("#suppliersbankaccountsform input[name='iban']").val(),
					swift : $("#suppliersbankaccountsform input[name='swift']").val(),
					address : $("#suppliersbankaccountsform input[name='address']").val(),
					type : $("#suppliersbankaccountsform select[name='type']").val(),
					bank : $("#suppliersbankaccountsform input[name='bank']").val(),
					agency : $("#suppliersbankaccountsform input[name='agency']").val(),
					supplier : $("#suppliersbankaccountsform input[name='supplier']").val(),
					action : 'addsupplierbankaccount'
				},
				success : function(response){
					$("#suppliersbankaccountsform .lx-submit a").attr("class","");
					$("#suppliersbankaccountsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						var bank = $("#suppliersbankaccountsform input[name='bank']").val();
						if($("#suppliersbankaccountsform select[name='bank'] option[value='"+bank+"']").length === 0){
							$("#suppliersbankaccountsform select[name='bank']").append('<option value="'+bank+'">'+bank+'</option>');
						}
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadSuppliers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		if($("#suppliersbankaccountsform select[name='type']").val() === "in"){
			if(removeAllSpaces($("#suppliersbankaccountsform input[name='rib']").val()).length !== 24){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Le RIB doit contenir 24 chiffres<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);			
			}
		}
		else{
			$("#suppliersbankaccountsform .lx-submit a i").remove();
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);			
		}
	}
});

$("body").delegate(".lx-edit-email-company","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#emailsform input[name='email']").val($(this).attr("data-email"));
	$("#emailsform input[name='password']").val($(this).attr("data-password"));
	$("#emailsform input[name='host']").val($(this).attr("data-host"));
	$("#emailsform select[name='smtpsecure']").val($(this).attr("data-smtpsecure"));
	$("#emailsform input[name='port']").val($(this).attr("data-port"));
	$("#emailsform input[name='companytxt']").val($(this).attr("data-companytxt"));
	$("#emailsform input[name='company']").val($(this).attr("data-company"));
	$("#emailsform input[name='user']").val($(this).attr("data-user"));
	$("#emailsform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#emailsform .lx-submit a").on("click",function(){
	var k = 0;
	
	isEmail($("#emailsform input[name='email']"));
	isNotEmpty($("#emailsform input[name='password']"));
	isNotEmpty($("#emailsform input[name='host']"));
	isNotEmpty($("#emailsform select[name='smtpsecure']"));
	isNotEmpty($("#emailsform input[name='port']"));
	if(!isEmail($("#emailsform input[name='email']"))
	|| !isNotEmpty($("#emailsform input[name='password']"))
	|| !isNotEmpty($("#emailsform input[name='host']"))
	|| !isNotEmpty($("#emailsform select[name='smtpsecure']"))
	|| !isNotEmpty($("#emailsform input[name='port']"))){
		k++;
	}

	if(k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#emailsform input[name='id']").val(),
					user : $("#emailsform input[name='user']").val(),
					email : $("#emailsform input[name='email']").val(),
					password : $("#emailsform input[name='password']").val(),
					host : $("#emailsform input[name='host']").val(),
					smtpsecure : $("#emailsform select[name='smtpsecure']").val(),
					port : $("#emailsform input[name='port']").val(),
					company : $("#emailsform input[name='company']").val(),
					action : 'addemails'
				},
				success : function(response){
					$("#emailsform .lx-submit a").attr("class","");
					$("#emailsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadUsers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#emailsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);			
	}
});

$("body").delegate(".lx-edit-delay","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#delayform input[name='delay']").val($(this).attr("data-delay"));
	$("#delayform input[name='table']").val($(this).attr("data-table"));
	$("#delayform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#delayform .lx-submit a").on("click",function(){
	var k = 0;
	
	isNumber($("#delayform input[name='delay']"));
	if(!isNumber($("#delayform input[name='delay']"))){
		k++;
	}

	if(k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#delayform input[name='id']").val(),
					delay : $("#delayform input[name='delay']").val(),
					table : $("#delayform input[name='table']").val(),
					action : 'adddelay'
				},
				success : function(response){
					$("#delayform .lx-submit a").attr("class","");
					$("#delayform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						var bank = $("#delayform input[name='bank']").val();
						if($("#delayform select[name='bank'] option[value='"+bank+"']").length === 0){
							$("#delayform select[name='bank']").append('<option value="'+bank+'">'+bank+'</option>');
						}
						$(".lx-popup-content > a > .material-icons").trigger("click");
						if($(".lx-pagination ul").attr("data-table") === "clients"){
							loadClients($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "factures"){
							loadFactures($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nh"){
							loadNH($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nf"){
							loadNF($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "suppliers"){
							loadSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
							loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#delayform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);			
	}
});

$("body").delegate(".lx-edit-datepaid","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#datepaidform input[name='datepaid']").val($(this).attr("data-datepaid"));
	$("#datepaidform input[name='table']").val($(this).attr("data-table"));
	$("#datepaidform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#datepaidform .lx-submit a").on("click",function(){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#datepaidform input[name='id']").val(),
					datepaid : $("#datepaidform input[name='datepaid']").val(),
					table : $("#datepaidform input[name='table']").val(),
					action : 'adddatepaid'
				},
				success : function(response){
					$("#datepaidform .lx-submit a").attr("class","");
					$("#datepaidform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						if($(".lx-pagination ul").attr("data-table") === "clients"){
							loadClients($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "factures"){
							loadFactures($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nh"){
							loadNH($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "nf"){
							loadNF($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "suppliers"){
							loadSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
							loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
						}
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
});

$("#retardform select[name='retardpaid']").on("change",function(){
	$(".dateretard").css("display","none");
	if($(this).val() === "Régularisé"){
		$(".dateretard").css("display","block");
	}
});

$("body").delegate(".lx-edit-retard","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#retardform select[name='retardpaid']").val($(this).attr("data-retardpaid")).trigger("change");
	$("#retardform input[name='dateretard']").val($(this).attr("data-dateretard"));
	$("#retardform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#retardform .lx-submit a").on("click",function(){
	isNotEmpty($("#retardform select[name='retardpaid']"));
	if(isNotEmpty($("#retardform select[name='retardpaid']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#retardform input[name='id']").val(),
					retardpaid : $("#retardform select[name='retardpaid']").val(),
					dateretard : $("#retardform input[name='dateretard']").val(),
					action : 'addretard'
				},
				success : function(response){
					$("#retardform .lx-submit a").attr("class","");
					$("#retardform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadFactureSuppliers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#retardform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);			
	}
});

$("body").delegate(".lx-edit-regularity","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#regularityform select[name='status']").val($(this).attr("data-status"));
	$("#regularityform select[name='type']").val($(this).attr("data-type"));
	$("#regularityform select[name='appras']").val($(this).attr("data-appras")).trigger("change");
	$("#regularityform select[name='doc']").val($(this).attr("data-doc")).trigger("change");
	$("#regularityform input[name='attachments']").val($(this).attr("data-attachments"));
	$(".fa-check").css("display","none");
	if($(this).attr("data-attachments") !== ""){
		$(".fa-check").css("display","inline-block");
	}
	$("#regularityform input[name='datedoc']").val($(this).attr("data-datedoc"));
	$("#regularityform input[name='jdoc']").val($(this).attr("data-jdoc"));
	$("#regularityform input[name='percentage']").val($(this).attr("data-percentage"));
	$("#regularityform input[name='supplier']").val($(this).attr("data-supplier"));
	$("#regularityform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#regularityform select[name='appras']").on("change",function(){
	$(".appras").css("display","none");
	$(".avec").css("display","none");
	$(".sans").css("display","none");
	$("#regularityform select[name='doc']").val("");
	if($(this).val() !== ""){
		if($(this).val() === "Oui"){
			$(".appras").css("display","table-row");
		}
	}
});

$("#regularityform select[name='doc']").on("change",function(){
	$(".avec").css("display","none");
	$(".sans").css("display","none");
	if($(this).val() !== ""){
		if($(this).val() === "Avec"){
			$(".avec").css("display","table-cell");
		}
		if($(this).val() === "Sans"){
			$(".sans").css("display","table-cell");
		}
	}
});

$("#regularityform .lx-submit a").on("click",function(){
	var k = 0;
	
	if($("#regularityform select[name='appras']").val() === "Oui"){
		isNotEmpty($("#regularityform select[name='doc']"));
		if(!isNotEmpty($("#regularityform select[name='doc']"))){
			k++;
		}
		else{
			if($("#regularityform select[name='doc']").val() === "Avec"){
				isNotEmpty($("#regularityform input[name='datedoc']"));
				isNumber($("#regularityform input[name='jdoc']"));
				isNumber($("#regularityform input[name='percentage']"));
				if(!isNotEmpty($("#regularityform input[name='datedoc']"))
				|| !isNumber($("#regularityform input[name='jdoc']"))
				|| !isNumber($("#regularityform input[name='percentage']"))){
					k++;
				}
			}
			if($("#regularityform select[name='doc']").val() === "Sans"){
				isNumber($("#regularityform input[name='percentage']"));
				if(!isNumber($("#regularityform input[name='percentage']"))){
					k++;
				}
			}
		}
	}
	
	isNotEmpty($("#regularityform select[name='status']"));
	isNotEmpty($("#regularityform select[name='appras']"));

	if(isNotEmpty($("#regularityform select[name='status']"))
	&& isNotEmpty($("#regularityform select[name='appras']")) && k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#regularityform input[name='id']").val(),
					supplier : $("#regularityform input[name='supplier']").val(),
					status : $("#regularityform select[name='status']").val(),
					type : $("#regularityform input[name='type']").val(),
					appras : $("#regularityform select[name='appras']").val(),
					doc : $("#regularityform select[name='doc']").val(),
					attachments : $("#regularityform input[name='attachments']").val(),
					datedoc : $("#regularityform input[name='datedoc']").val(),
					jdoc : $("#regularityform input[name='jdoc']").val(),
					percentage : $("#regularityform input[name='percentage']").val(),
					action : 'addregularity'
				},
				success : function(response){
					$("#regularityform .lx-submit a").attr("class","");
					$("#regularityform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						var type = $("#regularityform input[name='type']").val();
						if($("#regularityform select[name='type'] option[value='"+type+"']").length === 0){
							$("#regularityform select[name='type']").append('<option value="'+type+'">'+type+'</option>');
						}
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadSuppliers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#regularityform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);			
	}
});

$("body").delegate(".lx-edit-myregularity","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#myregularityform select[name='status']").val($(this).attr("data-status"));
	$("#myregularityform select[name='type']").val($(this).attr("data-type"));
	$("#myregularityform select[name='appras']").val($(this).attr("data-appras")).trigger("change");
	$("#myregularityform select[name='doc']").val($(this).attr("data-doc")).trigger("change");
	$("#myregularityform input[name='attachments']").val($(this).attr("data-attachments"));
	$(".fa-check").css("display","none");
	if($(this).attr("data-attachments") !== ""){
		$(".fa-check").css("display","inline-block");
	}
	$("#myregularityform input[name='datedoc']").val($(this).attr("data-datedoc"));
	$("#myregularityform input[name='jdoc']").val($(this).attr("data-jdoc"));
	$("#myregularityform input[name='percentage']").val($(this).attr("data-percentage"));
	$("#myregularityform input[name='company']").val($(this).attr("data-company"));
	$("#myregularityform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#myregularityform select[name='appras']").on("change",function(){
	$(".appras").css("display","none");
	$(".avec").css("display","none");
	$(".sans").css("display","none");
	$("#myregularityform select[name='doc']").val("");
	if($(this).val() !== ""){
		if($(this).val() === "Oui"){
			$(".appras").css("display","table-row");
		}
	}
});

$("#myregularityform select[name='doc']").on("change",function(){
	$(".avec").css("display","none");
	$(".sans").css("display","none");
	if($(this).val() !== ""){
		if($(this).val() === "Avec"){
			$(".avec").css("display","table-cell");
		}
		if($(this).val() === "Sans"){
			$(".sans").css("display","table-cell");
		}
	}
});

$("#myregularityform .lx-submit a").on("click",function(){
	var k = 0;
	
	if($("#myregularityform select[name='appras']").val() === "Oui"){
		isNotEmpty($("#myregularityform select[name='doc']"));
		if(!isNotEmpty($("#myregularityform select[name='doc']"))){
			k++;
		}
		else{
			if($("#myregularityform select[name='doc']").val() === "Avec"){
				isNotEmpty($("#myregularityform input[name='datedoc']"));
				isNumber($("#myregularityform input[name='jdoc']"));
				isNumber($("#myregularityform input[name='percentage']"));
				if(!isNotEmpty($("#myregularityform input[name='datedoc']"))
				|| !isNumber($("#myregularityform input[name='jdoc']"))
				|| !isNumber($("#myregularityform input[name='percentage']"))){
					k++;
				}
			}
			if($("#myregularityform select[name='doc']").val() === "Sans"){
				isNumber($("#myregularityform input[name='percentage']"));
				if(!isNumber($("#myregularityform input[name='percentage']"))){
					k++;
				}
			}
		}
	}
	
	isNotEmpty($("#myregularityform select[name='status']"));
	isNotEmpty($("#myregularityform select[name='appras']"));

	if(isNotEmpty($("#myregularityform select[name='status']"))
	&& isNotEmpty($("#myregularityform select[name='appras']")) && k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#myregularityform input[name='id']").val(),
					company : $("#myregularityform input[name='company']").val(),
					status : $("#myregularityform select[name='status']").val(),
					type : $("#myregularityform input[name='type']").val(),
					appras : $("#myregularityform select[name='appras']").val(),
					doc : $("#myregularityform select[name='doc']").val(),
					attachments : $("#myregularityform input[name='attachments']").val(),
					datedoc : $("#myregularityform input[name='datedoc']").val(),
					jdoc : $("#myregularityform input[name='jdoc']").val(),
					percentage : $("#myregularityform input[name='percentage']").val(),
					action : 'addmyregularity'
				},
				success : function(response){
					$("#myregularityform .lx-submit a").attr("class","");
					$("#myregularityform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						var type = $("#myregularityform input[name='type']").val();
						if($("#myregularityform select[name='type'] option[value='"+type+"']").length === 0){
							$("#myregularityform select[name='type']").append('<option value="'+type+'">'+type+'</option>');
						}
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadCompanies("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#myregularityform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);			
	}
});

$("body").delegate(".lx-trash-supplierbankaccount","click",function(){
	filterClicked = "yes";
	loadSuppliers("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-supplierbankaccount","click",function(){
	filterClicked = "yes";
	loadSuppliers("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-supplierbankaccount","click",function(){
	$(".deleterecord1 .lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-supplierbankaccount","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoresupplierbankaccount'
		},
		success : function(response){
			loadSuppliers("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-supplierbankaccount","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletesupplierbankaccountpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadSuppliers("0");
			}
		}
	});
});

$("body").delegate(".lx-new-extrainfo","click",function(){
	$(".lx-tabs-docs a").attr("data-company",$(this).attr("data-company"));
	$("#costumizeextrainfoform input[name='company']").val($(this).attr("data-company"));
	$(".lx-tabs-docs a:eq(0)").trigger("click");
});

$(".lx-tabs-docs a").on("click",function(){
	if($(".lx-docs-form .lx-loading").length === 0){
		$(".lx-docs-form").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var el = $(this);
	var company = $(this).attr("data-company");
	var document = $(this).attr("data-document");
	$("#costumizeextrainfoform input[name='document']").val(document);
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : company,
			document : document,
			action : 'loaddocsextrainfo'
		},
		success : function(response){
			$(".lx-tabs-docs a").removeClass("active");
			el.addClass("active");
			$(".lx-docs-form .lx-loading").remove();
			$(".lx-docs-form").html(response);
		}
	});	
});

$("#costumizeextrainfoform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $("#costumizeextrainfoform input[name='company']").val(),
			document : $("#costumizeextrainfoform input[name='document']").val(),
			modepayment : $("#costumizeextrainfoform textarea[name='modepayment']").val(),
			conditions : $("#costumizeextrainfoform textarea[name='conditions']").val(),
			abovetable : $("#costumizeextrainfoform textarea[name='abovetable']").val(),
			action : 'savedocinfo'
		},
		success : function(response){
			$("#extrainfoblform .lx-submit a i").remove();
			loadCompanies("1");
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Note enregistré<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	});
});

$(".lx-start-inventaire").on("click",function(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'startinventaire'
		},
		success : function(response){
			window.location.href = "inventaire.php";
		}
	});	
});

$(".lx-end-inventaire").on("click",function(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'endinventaire'
		},
		success : function(response){
			window.location.href = "inventaire.php";
		}
	});	
});

$("body").delegate(".lx-delete-inventaire","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

function loadInventaire(state){
	if($(".lx-table-inventaire .lx-loading").length === 0){
		$(".lx-table-inventaire").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadinventaire'
		},
		success : function(response){
			$(".lx-table-inventaire .lx-loading").remove();
			$(".lx-table-inventaire").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadInventaireDetails(){
	if($(".lx-table-inventaire-details .lx-loading").length === 0){
		$(".lx-table-inventaire-details").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			keyword : $("#keyword").val(),	
			depot : $("#depot").attr("data-ids"),	
			company : $("#company").attr("data-ids"),	
			supplier : $("#supplier").attr("data-ids"),	
			family : $("#family").attr("data-ids"),	
			brand : $("#brand").val(),	
			qtymin : ($("#qtymin").prop("checked") === true?"1":"0"),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadinventairedetails'
		},
		success : function(response){
			$(".lx-table-inventaire-details .lx-loading").remove();
			$(".lx-table-inventaire-details").html(response);

			onScroll();
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-delete-placement","click",function(){
	$(".deleterecord1 .lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$(".lx-print-inventaire").on("click",function(){
	var keyword = "keyword="+$("#keyword").val();
	var depot = "&depot="+$("#depot").attr("data-ids");
	var family = "&family="+ $("#family").attr("data-ids");
	var brand = "&brand="+ $("#brand").val();
	var qtymin = "&qtymin="+ ($("#qtymin").prop("checked") === true?"1":"0");
	var sortby = "&sortby="+ $(".lx-keyword input[name='sortby']").val();
	var orderby = "&orderby="+ $(".lx-keyword input[name='orderby']").val();
	// window.location.href = "printinventaire.php?"+keyword+depot+family+brand+qtymin+sortby+orderby;
	window.open(
		'printinventaire.php?'+keyword+depot+family+brand+qtymin+sortby+orderby,
		'_blank'
	);
});

$("body").delegate("input[name='qtyphysical']","keyup blur paste change",function(){
	if($(this).val() !== ""){
		var qtysystem = parseFloat($(this).parent().parent().parent().prev("td").find("b").text());
		var qtyphysical = parseFloat($(this).val());
		if(isNumeric(qtyphysical - qtysystem)){
			$(this).parent().parent().parent().next("td").find("span").text(qtyphysical-qtysystem);
			if(qtyphysical - qtysystem > 0){
				$(this).parent().parent().parent().next("td").css("background","#b4eeb4");
			}
			else if(qtyphysical - qtysystem < 0){
				$(this).parent().parent().parent().next("td").css("background","#ff7373");
			}
			else{
				$(this).parent().parent().parent().next("td").css("background","none");
			}
		}
	}
	else{
		$(this).parent().parent().parent().next("td").find("span").text("");
		$(this).parent().parent().parent().next("td").css("background","none");
	}
});

$("#inventaireform .lx-submit a.lx-save-inventaire").on("click",function(){
	var k = 0;
	if(confirm('Cette action est irréversible, voulez vous continue?')){
		k = 0;
	} 
	else{
		k = 1;
	}
	if(k === 0){
		var content = "";
		$("input[name='qtyphysical']").each(function(){
			if($(this).val() !== ""){
				content += "|"+$(this).attr("data-prevqty")+":"+$(this).val();
			}
		});
		if(content !== ""){
			if($(this).attr("class") !== "lx-disabled"){
				$(this).attr("class","lx-disabled");
				if($(this).find("i").length === 0){
					$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
				}
				var ajaxurl = "ajax.php";
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						note : $("#inventaireform textarea[name='note']").val(),
						content : content,
						action : 'addinventaire'
					},
					success : function(response){
						$("#inventaireform .lx-submit a").attr("class","");
						$("#inventaireform .lx-submit a i").remove();
						window.location.href = "inventaire.php";
					}
				});
			}
		}
		else{
			$("#inventaireform .lx-submit a i").remove();
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez Saisir au moins une quanitité physique<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	}
});

function isNumeric(value) {
  return typeof value === 'number' && !isNaN(value);
}

$(".lx-new-supplier").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#suppliersform input[name='codefo']").val("");
	if($("#suppliersform input[name='company']").attr("data-one") !== "1"){
		$("#suppliersform input[name='company']").val("");
		$("#suppliersform .lx-advanced-select input[type='checkbox']").each(function(){
			$(this).prop("checked",false);
		});
	}
	$("#suppliersform input[name='title']").val("");
	$("#suppliersform input[name='respname']").val("");
	$("#suppliersform input[name='respphone']").val("");
	$("#suppliersform input[name='whatsapp']").val("");
	$("#suppliersform input[name='respemail']").val("");
	$("#suppliersform input[name='respfax']").val("");
	$("#suppliersform input[name='ice']").val("");
	$("#suppliersform input[name='iff']").val("");
	$("#suppliersform input[name='rc']").val("");
	$("#suppliersform input[name='address']").val("");
	$("#suppliersform textarea[name='note']").val("");
	$("#suppliersform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-supplier","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#suppliersform input[name='codefo']").val($(this).attr("data-codefo"));
	$("#suppliersform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#suppliersform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split(",");
	$("#suppliersform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	$("#suppliersform input[name='title']").val($(this).attr("data-titl"));
	$("#suppliersform input[name='respname']").val($(this).attr("data-respname"));
	$("#suppliersform input[name='respphone']").val($(this).attr("data-respphone"));
	$("#suppliersform input[name='whatsapp']").val($(this).attr("data-whatsapp"));
	$("#suppliersform input[name='respemail']").val($(this).attr("data-respemail"));
	$("#suppliersform input[name='respfax']").val($(this).attr("data-respfax"));
	$("#suppliersform input[name='ice']").val($(this).attr("data-ice"));
	$("#suppliersform input[name='iff']").val($(this).attr("data-iff"));
	$("#suppliersform input[name='rc']").val($(this).attr("data-rc"));
	$("#suppliersform input[name='address']").val($(this).attr("data-address"));
	$("#suppliersform textarea[name='note']").val($(this).attr("data-note"));
	$("#suppliersform input[name='id']").val($(this).attr("data-id"));
});

$("#suppliersform .lx-submit a").on("click",function(){
	var k = 0;
	if($("#suppliersform input[name='respemail']").val() !== ""){
		isEmail($("#suppliersform input[name='respemail']"));
		if(!isEmail($("#suppliersform input[name='respemail']"))){
			k++;
		}
	}
	isNotEmpty($("#suppliersform input[name='company']"));
	isNotEmpty($("#suppliersform input[name='title']"));
	if(isNotEmpty($("#suppliersform input[name='title']"))
	&& isNotEmpty($("#suppliersform input[name='company']")) && k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#suppliersform input[name='id']").val(),
					codefo : $("#suppliersform input[name='codefo']").val(),
					company : $("#suppliersform input[name='company']").attr("data-ids"),
					title : $("#suppliersform input[name='title']").val(),
					respname : $("#suppliersform input[name='respname']").val(),
					respphone : $("#suppliersform input[name='respphone']").val(),
					whatsapp : $("#suppliersform input[name='whatsapp']").val(),
					respemail : $("#suppliersform input[name='respemail']").val(),
					respfax : $("#suppliersform input[name='respfax']").val(),
					ice : $("#suppliersform input[name='ice']").val(),
					iff : $("#suppliersform input[name='iff']").val(),
					rc : $("#suppliersform input[name='rc']").val(),
					address : $("#suppliersform input[name='address']").val(),
					note : $("#suppliersform textarea[name='note']").val(),
					action : 'addsupplier'
				},
				success : function(response){
					$("#suppliersform .lx-submit a").attr("class","");
					$("#suppliersform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadSuppliers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#suppliersform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-supplier","click",function(){
	filterClicked = "yes";
	loadSuppliers("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-supplier","click",function(){
	filterClicked = "yes";
	loadSuppliers("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-supplier","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-supplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoresupplier'
		},
		success : function(response){
			loadSuppliers("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-supplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletesupplierpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadSuppliers("0");
			}
		}
	});
});

function loadSuppliers(state){
	if($(".lx-table-suppliers .lx-loading").length === 0){
		$(".lx-table-suppliers").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),	
			paid : $("#paid").attr("data-ids"),
			user : $("#user").attr("data-ids"),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadsuppliers'
		},
		success : function(response){
			$(".lx-table-suppliers .lx-loading").remove();
			$(".lx-table-suppliers").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			onScroll();
			reloadProspectsRecalls();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-facturesupplier").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#facturesuppliersform select[name='company']").val($("#facturesuppliersform select[name='company'] option:first").val()).prop("disabled",false);
	$("#facturesuppliersform select[name='type']").val("0");
	$("#facturesuppliersform input[name='code']").val("");
	$("#facturesuppliersform input[name='new'][value='1']").prop("checked",true);
	$("#facturesuppliersform input[name='exist']").val("1");
	$("#facturesuppliersform input[name='client']").val("");
	$("#facturesuppliersform select[name='client']").val("");
	toDropDownTargeted("#facturesuppliersform select[name='client']");
	$("#facturesuppliersform input[name='ice']").val("");
	$("#facturesuppliersform input[name='phone']").val("");
	$("#facturesuppliersform input[name='address']").val("");
	$("#facturesuppliersform input[name='email']").val("");
	$(".lx-client").show();
	$("#facturesuppliersform input[name='refbc']").val("");
	$("#facturesuppliersform input[name='price']").val("");
	$("#facturesuppliersform input[name='tva']").val("");
	$("#facturesuppliersform input[name='price2']").val("");
	$("#facturesuppliersform input[name='tva2']").val("");
	$("#facturesuppliersform select[name='currency']").val("");
	$("#facturesuppliersform textarea[name='note']").val("");
	$("#facturesuppliersform input[name='new'][value='1']").prop("checked",true);
	$(".out").css("display","none");
	tinyMCE.get("note").setContent("");
	$("#facturesuppliersform input[name='id']").val("0");
	toDropDown();
});

$("body").delegate(".lx-edit-facturesupplier","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#facturesuppliersform select[name='company']").val($(this).attr("data-company"));
	$("#facturesuppliersform input[name='dateaddcommand']").val($(this).attr("data-dateaddcommand"));
	$("#facturesuppliersform input[name='code']").val($(this).attr("data-code"));
	$("#facturesuppliersform input[name='new'][value='"+$(this).attr("data-exist")+"']").prop("checked",true).trigger("click");
	$("#facturesuppliersform input[name='exist']").val($(this).attr("data-exist"));
	$("#facturesuppliersform input[name='ice']").val($(this).attr("data-ice"));
	$("#facturesuppliersform input[name='phone']").val($(this).attr("data-phone"));
	$("#facturesuppliersform input[name='address']").val($(this).attr("data-address"));
	$("#facturesuppliersform input[name='email']").val($(this).attr("data-email"));
	if($(this).attr("data-exist") === "0"){
		$(".lx-client").show();
	}
	else if($(this).attr("data-exist") === "1"){
		$(".lx-client").show();
	}
	else{
		$(".lx-client").hide();
	}
	var client = $(this).attr("data-supplier");

	$("#facturesuppliersform fieldset").css("position","relative");

	if($("#facturesuppliersform fieldset .lx-loading").length === 0){
		$("#facturesuppliersform fieldset").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}

	window.setTimeout(function(){
		$("#facturesuppliersform select[name='client']").val(client);
		toDropDownTargeted("#facturesuppliersform select[name='client']");
		addClientToOrderSupplier();	
		$("#facturesuppliersform fieldset .lx-loading").remove();
	},2000);
	$("#facturesuppliersform input[name='refbc']").val($(this).attr("data-refbc"));
	$("#facturesuppliersform input[name='price']").val($(this).attr("data-price"));
	$("#facturesuppliersform input[name='tva']").val($(this).attr("data-tva"));
	$("#facturesuppliersform input[name='price2']").val($(this).attr("data-price2"));
	$("#facturesuppliersform input[name='tva2']").val($(this).attr("data-tva2"));
	$("#facturesuppliersform select[name='currency']").val($(this).attr("data-currency"));
	$("#facturesuppliersform textarea[name='note']").val($(this).attr("data-note"));
	$(".out").css("display","none");
	$("#facturesuppliersform select[name='type']").val("0");
	if($(this).attr("data-currency") !== "" && $(this).attr("data-currency") !== $("#currency").val()){
		$("#facturesuppliersform select[name='type']").val("1");
		$(".out").css("display","block");
	}
	tinyMCE.get("note").setContent($(this).attr("data-note"));
	$("#facturesuppliersform input[name='id']").val($(this).attr("data-id"));
	toDropDown();
});

$("#facturesuppliersform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	
	var k = 0;
	if($("#facturesuppliersform select[name='type']").val() === "1"){
		isNumber($("#facturesuppliersform input[name='price2']"));
		isNumber($("#facturesuppliersform input[name='tva2']"));
		isNotEmpty($("#facturesuppliersform select[name='currency']"));
		if(!isNumber($("#facturesuppliersform input[name='price2']"))
		|| !isNumber($("#facturesuppliersform input[name='tva2']"))
		|| !isNotEmpty($("#facturesuppliersform select[name='currency']"))){
			k++;
		}
	}

	isNotEmpty($("#facturesuppliersform input[name='exist']"));
	var client = "";
	if($("#facturesuppliersform input[name='exist']").val() === "0"){
		$("#facturesuppliersform input[name='client']").attr("data-isnotempty","");
		$("#facturesuppliersform input[name='client']").attr("data-message","Saisissez un fournisseur");
		if(!isNotEmpty($("#facturesuppliersform input[name='client']"))){
			client = "1";
		}
	}
	if($("#facturesuppliersform input[name='exist']").val() === "1"){
		$("#facturesuppliersform input[name='client']").attr("data-isnotempty","");
		$("#facturesuppliersform input[name='client']").attr("data-message","Choisissez un fournisseur");
		if(!isNotEmpty($("#facturesuppliersform select[name='client']"))){
			$("#facturesuppliersform input[name='client']").css("border","1px solid rgb(214, 50, 50)");
			client = "1";
		}
		else{
			$("#facturesuppliersform input[name='client']").removeAttr("style");
		}
	}

	isNotEmpty($("#facturesuppliersform input[name='dateaddcommand']"));
	isNotEmpty($("#facturesuppliersform select[name='company']"));
	isNotEmpty($("#facturesuppliersform select[name='type']"));
	isNotEmpty($("#facturesuppliersform input[name='code']"));
	isNumber($("#facturesuppliersform input[name='price']"));
	isNumber($("#facturesuppliersform input[name='tva']"));
	if(isNotEmpty($("#facturesuppliersform input[name='dateaddcommand']"))
	&& isNotEmpty($("#facturesuppliersform select[name='company']"))
	&& isNotEmpty($("#facturesuppliersform select[name='type']"))
	&& isNotEmpty($("#facturesuppliersform input[name='code']"))
	&& isNumber($("#facturesuppliersform input[name='price']"))
	&& isNumber($("#facturesuppliersform input[name='tva']")) && k === 0 && client === ""){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var fullname = $("#facturesuppliersform input[name='client']").val();
			if(typeof $("#facturesuppliersform select[name='client'] option:selected").attr("data-name") !== typeof undefined && $("#facturesuppliersform select[name='client'] option:selected").attr("data-name") !== false){
				fullname = $("#facturesuppliersform select[name='client'] option:selected").attr("data-name");
			}	
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#facturesuppliersform input[name='id']").val(),
					company : $("#facturesuppliersform select[name='company']").val(),
					dateadd : $("#facturesuppliersform input[name='dateaddcommand']").val(),
					code : $("#facturesuppliersform input[name='code']").val(),
					exist : $("#facturesuppliersform input[name='exist']").val(),
					fullname : fullname,
					client : $("#facturesuppliersform select[name='client']").val(),
					ice : $("#facturesuppliersform input[name='ice']").val(),
					phone : $("#facturesuppliersform input[name='phone']").val(),
					address : $("#facturesuppliersform input[name='address']").val(),
					email : $("#facturesuppliersform input[name='email']").val(),
					refbc : $("#facturesuppliersform input[name='refbc']").val(),
					price : $("#facturesuppliersform input[name='price']").val(),
					tva : $("#facturesuppliersform input[name='tva']").val(),
					price2 : $("#facturesuppliersform input[name='price2']").val(),
					tva2 : $("#facturesuppliersform input[name='tva2']").val(),
					currency : $("#facturesuppliersform select[name='currency']").val(),
					note : $("#facturesuppliersform textarea[name='note']").val(),
					type : $("#facturesuppliersform select[name='type']").val(),
					action : 'addfacturesupplier'
				},
				success : function(response){
					$("#facturesuppliersform .lx-submit a").attr("class","");
					$("#facturesuppliersform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadFactureSuppliers("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
						reloadSupplierList();
					}
				}
			});
		}
	}
	else{
		$("#facturesuppliersform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-facturesupplier","click",function(){
	filterClicked = "yes";
	loadFactureSuppliers("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-facturesupplier","click",function(){
	filterClicked = "yes";
	loadFactureSuppliers("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-facturesupplier","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-facturesupplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorefacturesupplier'
		},
		success : function(response){
			loadFactureSuppliers("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-facturesupplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletefacturesupplierpermanently'
		},
		success : function(response){
			loadFactureSuppliers("0");
		}
	});
});

function loadFactureSuppliers(state){
	if($(".lx-table-facturesuppliers .lx-loading").length === 0){
		$(".lx-table-facturesuppliers").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			type : $("#type").val(),
			statee : $("#state").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			retard : $("#retard").attr("data-ids"),
			stateras : $("#stateras").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),
			datestartlimit : $(".lx-keyword #datestartlimit").val(),
			dateendlimit : $(".lx-keyword #dateendlimit").val(),
			datestartlimitras : $(".lx-keyword #datestartlimitras").val(),
			dateendlimitras : $(".lx-keyword #dateendlimitras").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadfacturesuppliers'
		},
		success : function(response){
			$(".lx-table-facturesuppliers .lx-loading").remove();
			$(".lx-table-facturesuppliers").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('facturesuppliers');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
			loadNotification();	
		}
	});
}

$("#facturesuppliersform select[name='type']").on("change",function(){
	$(".out").css("display","none");
	if($(this).val() === "1"){
		$(".out").css("display","block");
	}
});

$(".lx-add-command").on("click",function(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			ids : $("#commands").val().substring(1),
			action : 'loadrecallproducts'
		},
		success : function(response){
			$(".lx-table-recall-products").html(response);
		}
	});	
});

$(".lx-popup-content").delegate(".lx-new-supplier-command","click",function(){
	var company = $(this).attr("data-company");
	var project = $(this).attr("data-project");
	var supplier = $(this).attr("data-supplier");
	var invoiced = $(this).attr("data-invoiced");
	$(".lx-popup-content > a > .material-icons").trigger("click");
	window.setTimeout(function(){
		$(".lx-new-commandsupplier").trigger("click");
	},500);
	window.setTimeout(function(){
		$("#commandsuppliersform select[name='company']").val(company).prop("disabled",true).trigger("change");
		$("#commandsuppliersform select[name='supplier']").val(supplier).prop("disabled",true);
		$("#commandsuppliersform select[name='invoiced']").val(invoiced).prop("disabled",true);
	},600);
	window.setTimeout(function(){
		$("#commandsuppliersform select[name='project']").val(project);
	},600);	
	$("#commandsuppliersform select[name='invoiced']").trigger("change");
});

$(".lx-popup-content").delegate(".lx-cancel-supplier-command","click",function(){
	$(".lx-popup-content > a > .material-icons").trigger("click");
	window.setTimeout(function(){
		$(".lx-generate-blcommand").trigger("click");
	},500);
});

$(".lx-popup-content").delegate(".lx-generate-bl-command","click",function(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			ids : $("#commands").val().substring(1),
			action : 'generatebl'
		},
		success : function(response){
			if(response === ""){
				$(".lx-popup-content > a > .material-icons").trigger("click");
				$(".lx-table-commandsuppliers .lx-loading").remove();
				loadCommandSuppliers($(".lx-pagination ul").attr("data-state"));
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien généré<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);
				$(".lx-new-supplier-command").attr("data-command","");
				$(".lx-new-supplier-command").attr("data-company","");
				$(".lx-new-supplier-command").attr("data-project","");
				$(".lx-new-supplier-command").attr("data-supplier","");
				$(".lx-new-supplier-command").attr("data-invoiced","");
				$("#commandsuppliersform select[name='company']").val("").prop("disabled",false);
				$("#commandsuppliersform select[name='project']").val("0").prop("disabled",false);
				$("#commandsuppliersform select[name='supplier']").val("").prop("disabled",false);
				$("#commandsuppliersform select[name='invoiced']").val("").prop("disabled",false);
				$("#commands").val("");				
			}
			else{
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
		}
	});
});

$(".lx-popup-content").delegate(".lx-cancel-bl-command","click",function(){
	$(".lx-new-supplier-command").attr("data-command","");
	$(".lx-new-supplier-command").attr("data-company","");
	$(".lx-new-supplier-command").attr("data-project","");
	$(".lx-new-supplier-command").attr("data-supplier","");
	$(".lx-new-supplier-command").attr("data-invoiced","");
	$("#commandsuppliersform select[name='company']").val("").prop("disabled",false);
	$("#commandsuppliersform select[name='project']").val("0").prop("disabled",false);
	$("#commandsuppliersform select[name='supplier']").val("").prop("disabled",false);
	$("#commandsuppliersform select[name='invoiced']").val("").prop("disabled",false);
	$("#commands").val("");
});


$("body").delegate(".lx-duplicate-commandsupplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'duplicatecommandsupplier'
		},
		success : function(response){
			loadCommandSuppliers("1");
		}
	});
});

$("body").delegate(".lx-trash-commandsupplier","click",function(){
	filterClicked = "yes";
	loadCommandSuppliers("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-commandsupplier","click",function(){
	filterClicked = "yes";
	loadCommandSuppliers("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-commandsupplier","click",function(){
	$(".updatecaisse input[name='price']").val($(this).attr("data-price"));
	$(".updatecaisse input[name='command']").val($(this).attr("data-id"));
	$(".updatecaisse input[name='type']").val($(this).attr("data-type"));
	$(".updatecaisse input[name='company']").val($(this).attr("data-company"));
	$(".updatecaisse input[name='project']").val($(this).attr("data-project"));
	$(".updatecaisse input[name='libelle']").val($(this).attr("data-libelle"));
	$(".updatecaisse input[name='nature']").val($(this).attr("data-nature"));
	$(".updatecaisse input[name='invoiced']").val($(this).attr("data-invoiced"));
	$(".lx-delete-record").attr("data-action",$(this).attr("data-action"));
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$(".lx-popup-content").delegate(".lx-delete-commandsupplier-child","click",function(){
	$(".lx-delete-record").attr("data-action",$(this).attr("data-action"));
	$(".lx-delete-record").attr("data-ids",$(this).attr("data-ids"));
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-commandsupplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorecommandsupplier'
		},
		success : function(response){
			loadCommandSuppliers("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-commandsupplier","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletecommandsupplierpermanently'
		},
		success : function(response){
			loadCommandSuppliers("0");
		}
	});
});

function loadCommandSuppliers(state){
	if($(".lx-table-commandsuppliers .lx-loading").length === 0){
		$(".lx-table-commandsuppliers").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),	
			supplier : $("#supplier").attr("data-ids"),	
			company : $("#company").attr("data-ids"),	
			project : $("#project").attr("data-ids"),	
			product : $("#product").attr("data-ids"),	
			service : $("#service").attr("data-ids"),
			invoiced : $("#invoiced").attr("data-ids"),
			paid : $("#paid").val(),
			modepayment : $("#modepayment").val(),	
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadcommandsuppliers'
		},
		success : function(response){
			$(".lx-table-commandsuppliers .lx-loading").remove();
			$(".lx-table-commandsuppliers").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-download-file1","click",function(){
	var el = $(this);
	var href = $(this).attr("data-href");
	var id = $(this).attr("data-id");
	if($(".lx-print-options .lx-loading").length === 0){
		$(".lx-print-options").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'loadprintoptions'
		},
		success : function(response){
			console.log(response);
			$(".lx-print-options .lx-loading").remove();
			$(".lx-print-options").html(response);
			$("#printform input[name='href']").val(href);
			$(".blbs").css("display","none");
			$(".bcbre").css("display","none");
			$(".fc").css("display","none");
			if (el.hasClass('lx-print-bl')) {
				$(".blbs").css("display","block");
			}
			if (el.hasClass('lx-print-f')) {
				$(".bcbre").css("display","block");
			}
			if (el.hasClass('lx-print-fc')) {
				$(".fc").css("display","block");
			}
		}
	});
});

$("body").delegate("#printform input[name='columns']","click",function(){
	var columns = "";
	var i = 1;
	$("#printform input[name='columns']").each(function(){
		if($(this).prop("checked") === true){
			columns += "&d"+i+"=1";
		}
		else{
			columns += "&d"+i+"=0";
		}
		i++;
	});
	$("#printform input[name='columnstxt']").val(columns);
});

$("body").delegate("#printform .lx-submit a","click",function(){
	isNotEmpty($("#printform select[name='signature']"));
	isNotEmpty($("#printform select[name='placement']"));
	isNotEmpty($("#printform select[name='header']"));
	isNotEmpty($("#printform select[name='refdocs']"));
	var k = 0;
	if($(".blbs").css("display") === "block"){
		isNotEmpty($("#printform select[name='valeur']"));
		if(!isNotEmpty($("#printform select[name='valeur']"))){
			k++;
		}
	}
	if($(".bcbre").css("display") === "block"){
		isNotEmpty($("#printform select[name='ice']"));
		if(!isNotEmpty($("#printform select[name='ice']"))){
			k++;
		}
	}
	if($(".fc").css("display") === "block"){
		isNotEmpty($("#printform select[name='payments']"));
		isNotEmpty($("#printform select[name='avoirs']"));
		isNotEmpty($("#printform select[name='reste']"));
		if(!isNotEmpty($("#printform select[name='payments']"))
		|| !isNotEmpty($("#printform select[name='avoirs']"))
		|| !isNotEmpty($("#printform select[name='reste']"))){
			k++;
		}
	}
	if(isNotEmpty($("#printform select[name='signature']"))
	&& isNotEmpty($("#printform select[name='placement']"))
	&& isNotEmpty($("#printform select[name='header']"))
	&& isNotEmpty($("#printform select[name='refdocs']"))
	&& k === 0){
		var label = "&label="+$("#printform input[name='signaturetext']").val();
		var company = "&company="+$("#printform input[name='company']").val();
		var doc = "&document="+$("#printform input[name='document']").val();
		window.location.href = $("#printform input[name='href']").val()+$("#printform select[name='placement']").val()+$("#printform select[name='payments']").val()+$("#printform select[name='avoirs']").val()+$("#printform select[name='reste']").val()+$("#printform select[name='signature']").val()+$("#printform select[name='header']").val()+$("#printform select[name='ice']").val()+$("#printform select[name='valeur']").val()+$("#printform input[name='columnstxt']").val()+$("#printform select[name='refdocs']").val()+label+company+doc;
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Téléchargement en cours<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-download-file","click",function(){
	$(".lx-popup-content > a > .material-icons").trigger("click");
	$(".lx-table").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	window.setTimeout(function(){
		$(".lx-table .lx-loading").remove();
	},4000);
});

$("body").delegate(".lx-edit-reception-state","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#receptionstateform input[name='refdoc']").val($(this).attr("data-refdoc"));
	if($(this).attr("data-datereceived") !== ""){
		$("#receptionstateform input[name='datereception']").val($(this).attr("data-datereceived"));
	}
	$("#receptionstateform input[name='received']").val($(this).attr("data-received"));
	var received = $(this).attr("data-received");
	$(".lx-reception-state").each(function(){
		$(this).find("i").remove();
		$(this).text($(this).text().trim());
		if($(this).text() === received){
			$(this).prepend('<i class="fa fa-check"></i> ');
		}
	});	
	$("#receptionstateform input[name='id']").val($(this).attr("data-id"));
});

$(".lx-reception-state").on("click",function(){
	$("#receptionstateform input[name='received']").val($(this).attr("data-received"));
	$(".lx-reception-state i").remove();
	$(this).prepend('<i class="fa fa-check"></i> ');
	if($("#receptionstateform input[name='received']").val() === "Réceptionné"){
		$("#receptionstateform input[name='datereception']").val(getCurrentDate());
	}
	else{
		$("#receptionstateform input[name='datereception']").val("");
		$("#receptionstateform input[name='refdoc']").val("");
	}
});

$("#receptionstateform .lx-submit a").on("click",function(){
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#receptionstateform input[name='id']").val(),
			received : $("#receptionstateform input[name='received']").val(),
			refdoc : $("#receptionstateform input[name='refdoc']").val(),
			datereceived : $("#receptionstateform input[name='datereception']").val(),
			action : 'addreceptionstate'
		},
		success : function(response){
			if(response !== ""){
				$("#receptionstateform .lx-submit a i").remove();
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				$("#receptionstateform .lx-submit a i").remove();
				$(".lx-popup-content > a > .material-icons").trigger("click");
				loadCommandSuppliers("1");
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);
			}
		}
	});				
});

$("body").delegate(".lx-upload-files input","change",function(){
	if($(this).next("a").find(".fa-circle-notch").length === 0){
		$(this).next("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	for(var i = 0;i < _($(this).attr("id")).files.length;i++){
		uploadFiles(_($(this).attr("id")).files[i],$(this).attr("data-id"),$(this).attr("data-table"));
	}
});

function uploadFiles(file,id,table){
    const allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword',                      // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/vnd.ms-excel',                // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'text/plain'                               // .txt
    ];

    if (!allowedTypes.includes(file.type)) {
		$(".lx-upload-files a i.fa-circle-notch").remove();
        alert("Veuillez importer des documents de type (image, PDF, Word, Excel, ou text file.");
        return;
    }

    var formdata = new FormData();
    formdata.append("file2", file);
    formdata.append("id", id);
    formdata.append("table", table);

    var ajax = new XMLHttpRequest();
    ajax.addEventListener("load", completeHandler, false);
    ajax.open("POST", "file_upload_parser.php");
    ajax.send(formdata);

    function completeHandler(event) {
		if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				loadFactures($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				loadClients($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
		}
	}	
}

$(".lx-new-source").on("click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#sourcesform input[name='title']").val("");
	$("#sourcesform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-source","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#sourcesform input[name='title']").val($(this).attr("data-titl"));
	$("#sourcesform input[name='id']").val($(this).attr("data-id"));
});

$("#sourcesform .lx-submit a").on("click",function(){
	isNotEmpty($("#sourcesform input[name='title']"));
	if(isNotEmpty($("#sourcesform input[name='title']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#sourcesform input[name='id']").val(),
					title : $("#sourcesform input[name='title']").val(),
					action : 'addsource'
				},
				success : function(response){
					$("#sourcesform .lx-submit a").attr("class","");
					$("#sourcesform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadSources("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#sourcesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-source","click",function(){
	filterClicked = "yes";
	loadSources("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-source","click",function(){
	filterClicked = "yes";
	loadSources("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-source","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-source","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoresource'
		},
		success : function(response){
			loadSources("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-source","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletesourcepermanently'
		},
		success : function(response){
			loadSources("0");
		}
	});
});

function loadSources(state){
	if($(".lx-table-sources .lx-loading").length === 0){
		$(".lx-table-sources").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadsources'
		},
		success : function(response){
			$(".lx-table-sources .lx-loading").remove();
			$(".lx-table-sources").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-client").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#clientsform input[name='codecl']").val("");
	if($("#clientsform input[name='company']").attr("data-one") !== "1"){
		$("#clientsform input[name='company']").val("");
		$("#labelsform .lx-advanced-select input[type='checkbox']").each(function(){
			$(this).prop("checked",false);
		});
	}
	$("#clientsform input[name='ice']").val("");
	$("#clientsform input[name='iff']").val("");
	$("#clientsform input[name='rc']").val("");
	$("#clientsform input[name='fullname']").val("");
	$("#clientsform input[name='phone']").val("");
	$("#clientsform input[name='whatsapp']").val("");
	$("#clientsform input[name='address']").val("");
	$("#clientsform input[name='email']").val("");
	$("#clientsform textarea[name='note']").val("");
	$("#clientsform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-client","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#clientsform input[name='codecl']").val($(this).attr("data-codecl"));
	$("#clientsform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#clientsform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split(",");
	$("#clientsform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	$("#clientsform input[name='ice']").val($(this).attr("data-ice"));
	$("#clientsform input[name='iff']").val($(this).attr("data-iff"));
	$("#clientsform input[name='rc']").val($(this).attr("data-rc"));
	$("#clientsform input[name='fullname']").val($(this).attr("data-fullname"));
	$("#clientsform input[name='phone']").val($(this).attr("data-phone"));
	$("#clientsform input[name='whatsapp']").val($(this).attr("data-whatsapp"));
	$("#clientsform input[name='address']").val($(this).attr("data-address"));
	$("#clientsform input[name='email']").val($(this).attr("data-email"));
	$("#clientsform textarea[name='note']").val($(this).attr("data-note"));
	$("#clientsform input[name='id']").val($(this).attr("data-id"));
});

$("#clientsform .lx-submit a").on("click",function(){
	var k = 0;
	if($("#clientsform input[name='email']").val() !== ""){
		isEmail($("#clientsform input[name='email']"));
		if(!isEmail($("#clientsform input[name='email']"))){
			k++;
		}
	}
	
	isNotEmpty($("#clientsform input[name='company']"));
	isNotEmpty($("#clientsform input[name='fullname']"));
	if(isNotEmpty($("#clientsform input[name='fullname']"))
	&& isNotEmpty($("#clientsform input[name='company']")) && k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#clientsform input[name='id']").val(),
					codecl : $("#clientsform input[name='codecl']").val(),
					company : $("#clientsform input[name='company']").attr("data-ids"),
					ice : $("#clientsform input[name='ice']").val(),
					iff : $("#clientsform input[name='iff']").val(),
					rc : $("#clientsform input[name='rc']").val(),
					fullname : $("#clientsform input[name='fullname']").val(),
					phone : $("#clientsform input[name='phone']").val(),
					whatsapp : $("#clientsform input[name='whatsapp']").val(),
					address : $("#clientsform input[name='address']").val(),
					email : $("#clientsform input[name='email']").val(),
					note : $("#clientsform textarea[name='note']").val(),
					action : 'addclient'
				},
				success : function(response){
					$("#clientsform .lx-submit a").attr("class","");
					$("#clientsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadClients("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#clientsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

function reloadClientList(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $(".lx-popup select[name='company']").val(),
			page : $(".lx-pagination ul").attr("data-table"),
			idclient : '',
			idprospect : '',
			action : 'reloadclientslist'
		},
		success : function(response){
			$("select.clientlist").html(response).trigger("change");
			toDropDownTargeted("select.clientlist");
		}
	});
}

function reloadSupplierList(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $(".lx-popup select[name='company']").val(),
			page : $(".lx-pagination ul").attr("data-table"),
			idclient : '',
			idprospect : '',
			action : 'reloadsupplierslist'
		},
		success : function(response){
			$("select.supplierlist").html(response).trigger("change");
			toDropDownTargeted("select.supplierlist");
		}
	});
}

$("body").delegate(".lx-trash-client","click",function(){
	filterClicked = "yes";
	loadClients("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-client","click",function(){
	filterClicked = "yes";
	loadClients("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-client","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-client","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoreclient'
		},
		success : function(response){
			loadClients("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-client","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deleteclientpermanently'
		},
		success : function(response){
			loadClients("0");
		}
	});
});

function loadClients(state){
	if($(".lx-table-clients .lx-loading").length === 0){
		$(".lx-table-clients").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),	
			paid : $("#paid").attr("data-ids"),
			user : $("#user").attr("data-ids"),	
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadclients'
		},
		success : function(response){
			$(".lx-table-clients .lx-loading").remove();
			$(".lx-table-clients").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			onScroll();
			reloadProspectsRecalls();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-edit-caisse","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");

	$("#caisseform input[name='id']").val($(this).attr("data-id"));
	$("#caisseform input[name='code1']").val($(this).attr("data-code"));
	
	$("#caisseform input[name='modepayment']").val($(this).attr("data-modepayment"));
	$(".lx-cheque").css("display","none");
	if($(this).attr("data-modepayment") === "Chèque" || $(this).attr("data-modepayment") === "Effet"){
		$(".lx-cheque").css("display","block");
	}

	$(".lx-remis").css("display","none");
	if($(this).attr("data-type") === "Entrée"){
		$(".lx-remis").css("display","block");
	}	
	$("#caisseform input[name='companytxt']").val($(this).attr("data-companytxt"));
	$("#caisseform select[name='company']").val($(this).attr("data-company")).trigger("change");
	var rib = $(this).attr("data-rib");
	window.setTimeout(function(){
		$("#caisseform select[name='rib']").val(rib);
	},1000);
	
	if($(this).attr("data-type") === "Sortie"){
		$(".lx-caisse-type").text("Dépense depuis facture");
	}
	else{
		$(".lx-caisse-type").text("Encaissement depuis facture");
	}
	$("#caisseform input[name='dateduecommand']").val($(this).attr("data-datedue"));
	$("#caisseform input[name='datepaidcommand']").val($(this).attr("data-datepaid"));
	$("#caisseform input[name='imputation']").val($(this).attr("data-imputation"));
	$("#caisseform input[name='remis']").prop("checked",$(this).attr("data-remis"));
	$("#caisseform input[name='dateremiscommand']").val($(this).attr("data-dateremis"));
	$("#caisseform input[name='nremise']").val($(this).attr("data-nremise"));
	$("#caisseform input[name='paid']").prop("checked",$(this).attr("data-paid"));
	$("#caisseform textarea[name='description']").val($(this).attr("data-description"));
	$("#caisseform select[name='supplierbankaccount']").html($(this).attr("data-supplierbankaccounts"));
	$("#caisseform select[name='supplierbankaccount']").val($(this).attr("data-rib2"));
	$(".lx-supplier-bankaccount").css("display","none");
	if($(this).attr("data-type") === "Sortie" && $(this).attr("data-modepayment") === "Virement"){
		$(".lx-supplier-bankaccount").css("display","block");
	}
	tinyMCE.get("description1").setContent($(this).attr("data-description"));
});

$(".lx-companies-list").on("change",function(){
	var id = $(this).val();
	var ajaxurl = "ajax.php";
	if($("#caisseform select[name='rib']").length){
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				id : id,
				action : 'loadbankaccountslist'
			},
			success : function(response){
				$(".lx-bankaccounts-list").html(response);
			}
		});
	}
	if($("#commandsform select[name='rib']").length){
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				id : id,
				action : 'loadbankaccountslist'
			},
			success : function(response){
				$(".lx-bankaccounts-list").html(response);
			}
		});
	}
});

$("#caisseform input[name='remis']").on("click",function(){
	if($(this).prop("checked") === true){
		$("#caisseform input[name='dateremiscommand']").val(getCurrentDate());	
	}
	else{
		$("#caisseform input[name='dateremiscommand']").val("");	
	}
});

function getCurrentDate() {
    const currentDate = new Date();

    const day = String(currentDate.getDate()).padStart(2, '0');
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const year = currentDate.getFullYear();

    const formattedDate = `${day}/${month}/${year}`;

    return formattedDate;
}

$("#caisseform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var k = 0;
	var remis = 0;
	if($("#caisseform input[name='remis']").prop("checked") === true){
		remis = 1;
		if(!isNotEmpty($("#caisseform input[name='dateremiscommand']"))){
			k = 1;
		}
	}	
	if(k === 0){
		if($(this).attr("class") !== "lx-disabled"){
			var paid = 0;
			if($("#caisseform input[name='paid']").prop("checked") === true){
				paid = 1;
			}
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#caisseform input[name='id']").val(),
					datedue : $("#caisseform input[name='dateduecommand']").val(),
					datepaid : $("#caisseform input[name='datepaidcommand']").val(),
					rib : $("#caisseform select[name='rib']").val(),
					rib2 : $("#caisseform select[name='supplierbankaccount']").val(),
					imputation : $("#caisseform input[name='imputation']").val(),
					note : $("#caisseform textarea[name='description']").val(),
					remis : remis,
					dateremis : $("#caisseform input[name='dateremiscommand']").val(),
					nremise : $("#caisseform input[name='nremise']").val(),
					paid : paid,
					page : $("#caisseform input[name='payments']").val(),
					action : 'addcaisse'
				},
				success : function(response){
					$("#caisseform .lx-submit a").attr("class","");
					$("#caisseform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadCaisse("1");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
					loadNotification();	
					loadPriceRange($("#caisseform input[name='page']").val());
				}
			});
		}
	}
	else{
		$("#caisseform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-caisse","click",function(){
	filterClicked = "yes";
	loadCaisse("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-caisse","click",function(){
	filterClicked = "yes";
	loadCaisse("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-caisse","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-caisse","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorecaisse'
		},
		success : function(response){
			loadCaisse("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-caisse","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletecaissepermanently'
		},
		success : function(response){
			loadCaisse("0");
		}
	});
});

$("#paid,#remis").on("click",function(){
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
});

function loadCaisse(state){
	if($(".lx-table-caisse .lx-loading").length === 0){
		$(".lx-table-caisse").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			worker : $("#worker").attr("data-ids"),
			type : $("#type").attr("data-ids"),
			status : $("#status").attr("data-ids"),
			invoiced : $("#invoiced").attr("data-ids"),
			modepayment : $("#modepayment").attr("data-ids"),
			imputation : $("#imputation").attr("data-ids"),
			rib : $("#rib").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			dateduestart : $(".lx-keyword #dateduestart").val(),
			datedueend : $(".lx-keyword #datedueend").val(),	
			datepaidstart : $(".lx-keyword #datepaidstart").val(),
			datepaidend : $(".lx-keyword #datepaidend").val(),	
			dateremisstart : $(".lx-keyword #dateremisstart").val(),
			dateremisend : $(".lx-keyword #dateremisend").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadcaisse'
		},
		success : function(response){
			$(".lx-table-caisse .lx-loading").remove();
			$(".lx-table-caisse").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-3").html($(".lx-caisse-total3").html());
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			onScroll();
			document.addEventListener('DOMContentLoaded', function() {
				const infobuls = document.querySelectorAll('.infobul');
					infobuls.forEach(function(infobul) {
					infobul.removeAttribute('data-title');
				});
			});
			
			loadFamilleCaisse();
			
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadFamilleCaisse(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'loadfamillecaisse'
		},
		success : function(response){
			$("#caisseform select[name='nature']").html(response);
			toDropDownTargeted("#caisseform select[name='nature']");
		}
	});		
}

$(".lx-bulk-caisse").on("click",function(){
	var realcompany = 0;
	var company = 0;
	var prevcompany = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			if($(this).attr("data-company") !== prevcompany && prevcompany !== ""){
				company += 1;
			}
			prevcompany = $(this).attr("data-company");		
			realcompany = $(this).attr("data-company");
		}
	});
	
	var ids = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			ids += "," + $(this).val();
		}
	});
	ids = ids.substring(1);
	
	if(ids !== ""){
		if(company === 0){
			$(".lx-bulk-caisse-real").trigger("click");
			$("#caissebulkform select[name='company']").val(realcompany).trigger("change");
		}
		else{
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Vous ne pouvez pas modifier en masse des réglements pour déffirents sociétés en même temps<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);			
		}
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez selectionner des réglements<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}	
});

$(".lx-bulk-caisse-real").on("click",function(){
	$("#caissebulkform input[name='datepaidcommand']").val("");
	$("#caissebulkform select[name='imputation']").val("");
	$("#caissebulkform input[name='imputation']").val("");
	$("#caissebulkform select[name='rib']").val("");
});

$("#caissebulkform .lx-submit a").on("click",function(){
	var ids = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			ids += "," + $(this).val();
		}
	});
	ids = ids.substring(1);
	
	isNotEmpty($("#caissebulkform input[name='datepaidcommand']"));
	if(isNotEmpty($("#caissebulkform input[name='datepaidcommand']"))){
		if($(this).attr("class") !== "lx-disabled"){
			var k = 0;
			if(confirm('Vous êtes sur le point de modifier plusieurs règlements en masse. Voulez-vous vraiment continuer ?')){
				k = 0;
			} 
			else{
				k = 1;
			}
			if(k === 0){
				$(this).attr("class","lx-disabled");
				if($(this).find("i").length === 0){
					$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
				}
				var ajaxurl = "ajax.php";
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						ids : ids,
						datepaid : $("#caissebulkform input[name='datepaidcommand']").val(),
						rib : $("#caissebulkform select[name='rib']").val(),
						action : 'editcaissebulk'
					},
					success : function(response){
						$("#caissebulkform .lx-submit a").attr("class","");
						$("#caissebulkform .lx-submit a i").remove();
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadCaisse("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
						loadNotification();
					}
				});
			}
		}
	}
	else{
		$("#caisseform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$(".lx-bulk-remise").on("click",function(){
	var type = 0;
	var mode = 0;
	var prevtype = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			if(($(this).attr("data-type") !== prevtype && prevtype !== "") || $(this).attr("data-type") !== "Entrée"){
				type += 1;
			}
			prevtype = $(this).attr("data-type");		
			if($(this).attr("data-mode") !== "Chèques"){
				mode += 1;
			}
		}
	});
	
	var ids = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			ids += "," + $(this).val();
		}
	});
	ids = ids.substring(1);
	
	if(ids !== ""){
		if(mode === 0 && type === 0){
			$(".lx-bulk-remise-real").trigger("click");
		}
		else{
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez sélectionner que des Chèques, Effets, Espèce reçus pour la remise en banque<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);			
		}
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez selectionner des réglements<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}	
});

$(".lx-bulk-remise-real").on("click",function(){
	$("#remisebulkform input[name='dateremiscommand']").val("");
	$("#remisebulkform input[name='nremise']").val("");
});

$("#remisebulkform .lx-submit a").on("click",function(){
	var ids = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			ids += "," + $(this).val();
		}
	});
	ids = ids.substring(1);
	
	isNotEmpty($("#remisebulkform input[name='dateremiscommand']"));
	if(isNotEmpty($("#remisebulkform input[name='dateremiscommand']"))){
		if($(this).attr("class") !== "lx-disabled"){
			var k = 0;
			if(confirm('Vous êtes sur le point de modifier plusieurs règlements en masse. Voulez-vous vraiment continuer ?')){
				k = 0;
			} 
			else{
				k = 1;
			}
			if(k === 0){
				$(this).attr("class","lx-disabled");
				if($(this).find("i").length === 0){
					$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
				}
				var ajaxurl = "ajax.php";
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						ids : ids,
						dateremis : $("#remisebulkform input[name='dateremiscommand']").val(),
						nremise : $("#remisebulkform input[name='nremise']").val(),
						action : 'editremisebulk'
					},
					success : function(response){
						$("#remisebulkform .lx-submit a").attr("class","");
						$("#remisebulkform .lx-submit a i").remove();
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadCaisse("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
						loadNotification();
					}
				});
			}
		}
	}
	else{
		$("#remisebulkform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

function loadPriceRange(page){
	if($(".lx-price-range .lx-loading").length === 0){
		$(".lx-price-range").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			page : page,
			action : 'loadpricerange'
		},
		success : function(response){
			$(".lx-price-range.lx-loading").remove();
			$(".lx-price-range").html(response);
			var ids = $("#mergeform input[name='ids']").val().split(",");
			$(".lx-table input[type='checkbox']").each(function(){
				for(var i = 0;i < ids.length;i++){
					if(ids[i] === $(this).val()){
						$(this).prop("checked",false);
						$(this).trigger("click");
					}
				}
			});
		}
	});
}

$("#expenseform select[name='type']").on("change",function(){
	$(".lx-client-expense").hide();
	$(".lx-supplier-expense").hide();
	if($(this).val() === "Entrée"){
		$(".lx-client-expense").show();
		$("#expenseform select[name='supplier']").val("");
		$("#expenseform input[name='supplier']").val("");
	}
	if($(this).val() === "Sortie"){
		$(".lx-supplier-expense").show();
		$("#expenseform select[name='client']").val("");
		$("#expenseform input[name='client']").val("");
	}
	$("#expenseform select[name='modepayment']").trigger("change");
});

$("input[name='countinprofit']").on("click",function(){
	if($(this).prop("checked") === true){
		$(".countinprofit").css("display","block");
	}
	else{
		$(".countinprofit").css("display","none");
	}
});

$("#expenseform select[name='invoiced']").on("change",function(){
	if($(this).val() === "Oui"){
		$(".countinprofit").css("display","block");
		$(".lx-invoiced-yes").css("display","block");	
		$("#expenseform input[name='tva']").val("");
		$(".lx-price-in").addClass("lx-g2").css("transition","none");
	}
	else{
		$(".countinprofit").css("display","none");
		$(".lx-invoiced-yes").css("display","none");	
		$("#expenseform input[name='tva']").val("0");
		$(".lx-price-in").removeClass("lx-g2").css("transition","none");
	}		
});

var htmlclientform = $("#expenseform select[name='client']").clone().html();
var htmlsupplierform = $("#expenseform select[name='supplier']").clone().html();
var htmlcategoryorm = $("#expenseform select[name='nature']").clone().html();
$("#expenseform select[name='company']").on("change",function(){
	if($("#expenseform input[name='newclient']:checked").val() === "1"){
		$("#expenseform select[name='client']").html(htmlclientform).trigger("change");
		var company = $(this).val();
		$("#expenseform select[name='client'] option[data-company]").each(function(){
			if(!$(this).attr("data-company").split(",").includes(company)){
				$(this).remove();
			}
		});
		toDropDownTargeted("#expenseform select[name='client']");
	}
	if($("#expenseform input[name='newsupplier']:checked").val() === "1"){
		$("#expenseform select[name='supplier']").html(htmlsupplierform).trigger("change");
		$("#expenseform select[name='supplier'] option[data-company]").each(function(){
			if(!$(this).attr("data-company").split(",").includes(company)){
				$(this).remove();
			}
		});
		toDropDownTargeted("#expenseform select[name='supplier']");
	}
	/*$("#expenseform select[name='nature']").html(htmlcategoryorm);
	$("#expenseform select[name='nature'] option[data-company]").each(function(){
		if(!$(this).attr("data-company").split(",").includes(company)){
			$(this).remove();
		}
	});
	toDropDownTargeted("#expenseform select[name='nature']");*/
});	

var htmlsupplierbankaccount = $("#expenseform select[name='supplierbankaccount']").clone().html();
$("#expenseform select[name='supplier']").on("change",function(){
	$("#expenseform select[name='supplierbankaccount']").html(htmlsupplierbankaccount);
	var supplier = $(this).val();
	$("#expenseform select[name='supplierbankaccount'] option[data-supplier]").each(function(){
		if(!$(this).attr("data-supplier").split(",").includes(supplier)){
			$(this).remove();
		}
	});
});			

$(".lx-new-expense").on("click",function(){
	tinyMCE.get("description").setContent('');
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#expenseform input[name='newclient'][value='1']").trigger("click");
	$("#expenseform input[name='newsupplier'][value='1']").trigger("click");
	$("#expenseform input[name='id']").val("0");
	$('input[name="dateaddcommand"]').daterangepicker({
		locale: {
			format: 'DD/MM/YYYY'
		},
		singleDatePicker: true,
		showDropdowns: true
	});
	$("#expenseform input[name='dateaddcommand']").val(getCurrentDate()).prop("readonly",false);
	$("#expenseform input[name='dateduecommand']").val(getCurrentDate()).prop("readonly",false);
	$("#expenseform input[name='datepaidcommand']").val("").prop("readonly",false);
	$("#expenseform select[name='company']").val($("#expenseform select[name='company'] option:first").val()).prop("disabled",false).trigger("change");
	$("#expenseform select[name='rib']").val("0").prop("disabled",false);
	$("#expenseform input[name='imputation']").val("").prop("readonly",false);
	$("#expenseform select[name='client']").val("").prop("disabled",false).trigger("change");
	$("#expenseform input[name='client']").val("").prop("readonly",false);
	$("#expenseform select[name='supplier']").val("").prop("disabled",false).trigger("change");
	$("#expenseform input[name='supplier']").val("").prop("readonly",false);
	$("#expenseform input[name='title']").val("").prop("readonly",false);
	$("#expenseform select[name='supplierbankaccount']").val("0").prop("disabled",false).trigger("change");
	$("#expenseform input[name='price']").val("").prop("readonly",false);
	$("#expenseform input[name='price2']").val("").prop("readonly",false);
	$("#expenseform input[name='currency']").val("").prop("readonly",false);
	$("#expenseform select[name='modepayment']").val("").prop("disabled",false).trigger("change");			
	$("#expenseform select[name='invoiced']").val("").prop("disabled",false).trigger("change");
	$("#expenseform input[name='remis']").prop("checked",false);
	$("#expenseform input[name='dateremiscommand']").val("").prop("readonly",false);
	$("#expenseform input[name='nremise']").val("").prop("readonly",false);
	$("#expenseform input[name='paid']").prop("checked",false);
	$(".countinprofit").css("display","none");
	$("#expenseform textarea[name='description']").val("").prop("readonly",false);
	$("#expenseform select[name='tva']").val("").prop("disabled",false);
	$("#expenseform input[name='tva']").val("").prop("readonly",false);
	$("#expenseform select[name='expense']").val("").prop("disabled",false);
	$("#expenseform input[name='nature']").val("").prop("readonly",false);
	$("#expenseform select[name='type']").val("").prop("disabled",false).trigger("change");
});

$("body").delegate(".lx-edit-expense","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	var readonly = false;
	var disabled = false;
	$('input[name="dateaddcommand"]').daterangepicker({
		locale: {
			format: 'DD/MM/YYYY'
		},
		singleDatePicker: true,
		showDropdowns: true
	});
	$("#expenseform input[name='newclient'][value='1']").trigger("click");
	$("#expenseform input[name='newsupplier'][value='1']").trigger("click");
	$("#expenseform input[name='id']").val($(this).attr("data-id"));
	$("#expenseform input[name='dateaddcommand']").val($(this).attr("data-dateadd")).prop("readonly",readonly);
	$("#expenseform input[name='dateduecommand']").val($(this).attr("data-datedue"));
	$("#expenseform input[name='datepaidcommand']").val($(this).attr("data-datepaid")).attr("data-datepaid",$(this).attr("data-datepaid"));
	$("#expenseform select[name='company']").val($(this).attr("data-company")).prop("disabled",disabled);
	$("#expenseform select[name='company']").trigger("change");
	var rib = $(this).attr("data-rib");
	window.setTimeout(function(){
		$("#expenseform select[name='rib']").val(rib);
	},1000);
	$("#expenseform input[name='imputation']").val($(this).attr("data-imputation"));
	$("#expenseform input[name='title']").val($(this).attr("data-titl")).prop("readonly",readonly);
	$("#expenseform input[name='price']").val($(this).attr("data-price")).prop("readonly",readonly);
	$("#expenseform input[name='price2']").val($(this).attr("data-price2")).prop("readonly",readonly);
	$("#expenseform select[name='currency']").val($(this).attr("data-currency")).prop("readonly",readonly);
	var type = $(this).attr("data-type");
	window.setTimeout(function(){
		$("#expenseform select[name='type']").val(type).trigger("change");
	},500);
	if($(this).attr("data-type") === "Entrée"){
		$(".lx-invoiced").show();
	}
	else{
		$(".lx-invoiced").hide();
	}
	var client = $(this).attr("data-client");
	var supplier = $(this).attr("data-supplier");
	var nature = $(this).attr("data-nature");
	var rib2 = $(this).attr("data-rib2");

	$(".lx-clientsupplier").css("position","relative");
	if($(".lx-clientsupplier .lx-loading").length === 0){
		$(".lx-clientsupplier").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	
	window.setTimeout(function(){
		$("#expenseform select[name='client']").val(client).prop("disabled",disabled).trigger("change");
		toDropDownTargeted("#expenseform select[name='client']");
		
		$("#expenseform select[name='supplier']").val(supplier).prop("disabled",disabled).trigger("change");
		toDropDownTargeted("#expenseform select[name='supplier']");
		
		$("#expenseform select[name='nature']").val(nature).prop("disabled",disabled);
		toDropDownTargeted("#expenseform select[name='nature']");

		$(".lx-clientsupplier .lx-loading").remove();
		
		window.setTimeout(function(){
			$("#expenseform select[name='supplierbankaccount']").val(rib2).trigger("change");
		},500);
	},1000);
	
	$("#expenseform select[name='invoiced']").val($(this).attr("data-invoiced")).prop("disabled",disabled).trigger("change");
	$("#expenseform select[name='modepayment']").val($(this).attr("data-modepayment")).prop("disabled",disabled).trigger("change");
	$("#expenseform input[name='remis']").prop("checked",$(this).attr("data-remis"));
	$("#expenseform input[name='dateremiscommand']").val($(this).attr("data-dateremis"));
	$("#expenseform input[name='nremise']").val($(this).attr("data-nremise"));
	$("#expenseform input[name='paid']").prop("checked",$(this).attr("data-paid"));
	$("#expenseform select[name='tva']").val($(this).attr("data-tva")+"%").prop("disabled",disabled);
	if($(this).attr("data-invoiced") === "Oui"){
		$(".countinprofit").css("display","block");
	}
	$("#expenseform textarea[name='description']").val($(this).attr("data-description"));
	tinyMCE.get("description").setContent($(this).attr("data-description"));
});

$("#expenseform select[name='modepayment']").on("change",function(){
	$(".lx-umpaid").css("display","none");
	$(".lx-remis").css("display","none");
	if($(this).val() === "Chèque" || $(this).val() === "Effet"){
		$(".lx-umpaid").css("display","block");
	}
	if($(this).val() === "Virement" || $(this).val() === "TPE"){
		$("#expenseform input[name='datepaidcommand']").val(getCurrentDate());
	}
	else{
		$("#expenseform input[name='datepaidcommand']").val($("#expenseform input[name='datepaidcommand']").attr("data-datepaid"));
	}
	if(($(this).val() === "Chèque" || $(this).val() === "Effet" || $(this).val() === "Espèce") && $("#expenseform select[name='type']").val() === "Entrée"){
		$(".lx-remis").css("display","block");
	}
	else{
		$("#expenseform input[name='paid']").prop("checked",false);
		$("#expenseform input[name='remis']").prop("checked",false);
		$("#expenseform input[name='dateremiscommand']").val("");
		$("#expenseform input[name='nremise']").val("");		
	}
	if($(this).val() === "Virement" && $("#expenseform select[name='type']").val() === "Sortie"){
		$(".lx-supplier-bankaccount").css("display","block");
		$(".lx-price-out").css("display","block");
		$("#expenseform select[name='supplierbankaccount']").trigger("change");
		$(".lx-company-account-ob").css("display","inline-block");
	}
	else{
		$(".lx-supplier-bankaccount").css("display","none");	
		$(".lx-price-out").css("display","none");		
		$(".lx-company-account-ob").css("display","none");
	}
});

$("#expenseform select[name='supplierbankaccount']").on("change",function(){
	if($(this).find("option:selected").attr("data-location") === "out"){
		$(".lx-price-out").css("display","block");
	}
	else{	
		$(".lx-price-out").css("display","none");		
	}
});

$("#expenseform input[name='remis']").on("click",function(){
	if($(this).prop("checked") === true){
		$("#expenseform input[name='dateremiscommand']").val(getCurrentDate());	
	}
	else{
		$("#expenseform input[name='dateremiscommand']").val("");	
	}
});

function getCurrentDate() {
    const currentDate = new Date();

    const day = String(currentDate.getDate()).padStart(2, '0');
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const year = currentDate.getFullYear();

    const formattedDate = `${day}/${month}/${year}`;

    return formattedDate;
}

$(".lx-popup-content").delegate("#expenseform input[name='newclient']","click",function(){
	$("#expenseform input[name='existclient']").val($(this).val()).trigger("keyup");
	$("#expenseform select[name='company']").trigger("change");
	$("#expenseform input[name='client']").val("").removeAttr("style");
	$("#expenseform select[name='client']").val("").next("ins").remove();
	$("#expenseform select[name='client']").trigger("change");
	if($(this).val() === "0"){
		$("#expenseform input[name='client']").attr("placeholder","Saisissez un client").attr("data-message","Saisissez un client");
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").show();
	}
	else if($(this).val() === "1"){
		$(".lx-client").show();
		toDropDownTargeted($("#expenseform select[name='client']"));
	}
});

$(".lx-popup-content").delegate("#expenseform input[name='newsupplier']","click",function(){
	$("#expenseform input[name='existsupplier']").val($(this).val()).trigger("keyup");
	$("#expenseform select[name='company']").trigger("change");
	$("#expenseform input[name='supplier']").val("").removeAttr("style");
	$("#expenseform select[name='supplier']").val("").next("ins").remove();
	$("#expenseform select[name='supplier']").trigger("change");
	if($(this).val() === "0"){
		$("#expenseform input[name='supplier']").attr("placeholder","Saisissez un fournisseur").attr("data-message","Saisissez un fournisseur");
		$(".lx-autocomplete-advanced").remove();
		$(".lx-supplier").show();
	}
	else if($(this).val() === "1"){
		$(".lx-supplier").show();
		toDropDownTargeted($("#expenseform select[name='supplier']"));
	}
});

$("#expenseform select[name='client']").on("change",function(){
	$("#expenseform input[name='phoneclient']").val($(this).find("option:selected").attr("data-phone"));	
	$("#expenseform input[name='emailclient']").val($(this).find("option:selected").attr("data-email"));	
});

$("#expenseform select[name='supplier']").on("change",function(){
	$("#expenseform input[name='phonesupplier']").val($(this).find("option:selected").attr("data-phone"));	
	$("#expenseform input[name='emailsupplier']").val($(this).find("option:selected").attr("data-email"));	
});

$("#expenseform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	isNotEmpty($("#expenseform select[name='company']"));
	isNotEmpty($("#expenseform input[name='title']"));
	isNumber($("#expenseform input[name='price']"));
	isNotEmpty($("#expenseform select[name='type']"));
	var expenserequired = 0;
	if($("#expenseform input[name='page']").val() === "expense"){
		if(!isNotEmpty($("#expenseform select[name='expense']"))){
			expenserequired = 1;
		}
	}
	isNotEmpty($("#expenseform select[name='modepayment']"));
	isNotEmpty($("#expenseform select[name='invoiced']"));
	var k = 0;
	var remis = 0;
	if($("#expenseform input[name='remis']").prop("checked") === true){
		remis = 1;
		if(!isNotEmpty($("#expenseform input[name='dateremiscommand']"))){
			k = 1;
		}
	}
	if($("#expenseform select[name='invoiced']").val() === "Oui"){
		isNotEmpty($("#expenseform select[name='tva']"));
	}
	var clientok = 0;
	if($("#expenseform select[name='type']").val() === "Entrée"){
		if(!isNotEmpty($("#expenseform input[name='client']"))){
			clientok = 1;
		}
	}
	var supplierok = 0;
	if($("#expenseform select[name='type']").val() === "Sortie"){
		if(!isNotEmpty($("#expenseform input[name='supplier']"))){
			supplierok = 1;
		}
	}
	if($("#expenseform select[name='type']").val() === "Sortie" && $("#expenseform select[name='modepayment']").val() == "Virement"){
		isNotEmpty($("#expenseform select[name='supplierbankaccount']"));
		isNumber($("#expenseform input[name='price2']"));
		isNotEmpty($("#expenseform select[name='currency']"));
		if(!isNotEmpty($("#expenseform select[name='supplierbankaccount']"))){
			supplierok = 1;
		}
		else{
			if($("#expenseform select[name='supplierbankaccount'] option:selected").attr("data-location") === "out"){
				if(!isNumber($("#expenseform input[name='price2']"))
				|| !isNotEmpty($("#expenseform select[name='currency']"))){
					supplierok = 1;
				}
			}
		}
	}
	var bankaccount = 0;
	if($("#expenseform select[name='type']").val() === "Sortie" && $("#expenseform select[name='modepayment']").val() == "Virement"){
		isNotZero($("#expenseform select[name='rib']"));
		if(!isNotZero($("#expenseform select[name='rib']"))){
			bankaccount = 1;
		}
	}
	var f = 0;
	if($("#expenseform input[name='emailclient']").val() !== ""){
		isEmail($("#expenseform input[name='emailclient']"));
		if(!isEmail($("#expenseform input[name='emailclient']"))){
			f++;
		}
	}
	if($("#expenseform input[name='emailsupplier']").val() !== ""){
		isEmail($("#expenseform input[name='emailsupplier']"));
		if(!isEmail($("#expenseform input[name='emailsupplier']"))){
			f++;
		}
	}
	if(isNotEmpty($("#expenseform input[name='title']"))
	&& isNotEmpty($("#expenseform select[name='company']"))
	&& isNumber($("#expenseform input[name='price']"))
	&& isNotEmpty($("#expenseform select[name='type']"))
	&& expenserequired === 0
	&& clientok === 0
	&& supplierok === 0
	&& bankaccount === 0
	&& isNotEmpty($("#expenseform select[name='modepayment']"))
	&& isNotEmpty($("#expenseform select[name='invoiced']")) 
	&& (($("#expenseform select[name='invoiced']").val() === "Oui" && isNotEmpty($("#expenseform select[name='tva']"))) || $("#expenseform select[name='invoiced']").val() === "Non") && k === 0 && f === 0){
		if($(this).attr("class") !== "lx-disabled"){
			var k = 0;
			if($("#expenseform select[name='type']").val() === "Sortie" && $("#expenseform input[name='page']").val() === "expense"){
				if(parseFloat($(".lx-solde-now").attr("data-value")) - parseFloat($("#expenseform input[name='price']").val()) < 0){
					if(confirm('Votre solde expense est insuffisant, voulez vous vraiment continue?')){
						k = 0;
					} 
					else{
						k = 1;
					}
				}
			}
			if(k === 0){
				var paid = 0;
				if($("#expenseform input[name='paid']").prop("checked") === true){
					paid = 1;
				}
				var countinprofit = 0;
				if($("#expenseform input[name='countinprofit']").prop("checked") === true){
					countinprofit = 1;
				}
				$(this).attr("class","lx-disabled");
				if($(this).find("i").length === 0){
					$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
				}
				var clientid = $("#expenseform select[name='client']").val();
				var supplierid = $("#expenseform select[name='supplier']").val();
				var ajaxurl = "ajax.php";
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						id : $("#expenseform input[name='id']").val(),
						dateadd : $("#expenseform input[name='dateaddcommand']").val(),
						datedue : $("#expenseform input[name='dateduecommand']").val(),
						datepaid : $("#expenseform input[name='datepaidcommand']").val(),
						company : $("#expenseform select[name='company']").val(),
						project : $("#expenseform select[name='project']").val(),
						rib : $("#expenseform select[name='rib']").val(),
						rib2 : $("#expenseform select[name='supplierbankaccount']").val(),
						location : $("#expenseform select[name='supplierbankaccount'] option:selected").attr("data-location"),
						imputation : $("#expenseform input[name='imputation']").val(),
						existclient: $("#expenseform input[name='newclient']:checked").val(),
						clientid: $("#expenseform select[name='client']").val(),
						client: $("#expenseform input[name='client']").val(),
						phoneclient: $("#expenseform input[name='phoneclient']").val(),
						emailclient: $("#expenseform input[name='emailclient']").val(),
						existsupplier : $("#expenseform input[name='newsupplier']:checked").val(),
						supplierid : $("#expenseform select[name='supplier']").val(),
						supplier : $("#expenseform input[name='supplier']").val(),
						phonesupplier : $("#expenseform input[name='phonesupplier']").val(),
						emailsupplier : $("#expenseform input[name='emailsupplier']").val(),
						title : $("#expenseform input[name='title']").val(),
						price : $("#expenseform input[name='price']").val(),
						price2 : $("#expenseform input[name='price2']").val(),
						currency : $("#expenseform select[name='currency']").val(),
						modepayment : $("#expenseform select[name='modepayment']").val(),
						description : $("#expenseform textarea[name='description']").val(),
						nature : $("#expenseform input[name='nature']").val(),
						depot : $("#expenseform select[name='expense']").val(),
						type : $("#expenseform select[name='type']").val(),
						invoiced : $("#expenseform select[name='invoiced']").val(),
						remis : remis,
						dateremis : $("#expenseform input[name='dateremiscommand']").val(),
						nremise : $("#expenseform input[name='nremise']").val(),
						paid : paid,
						tva : $("#expenseform select[name='tva']").val(),
						page : $("#expenseform input[name='page']").val(),
						action : 'addexpense'
					},
					success : function(response){
						console.log(response);
						$("#expenseform .lx-submit a").attr("class","");
						$("#expenseform .lx-submit a i").remove();
						if(response !== ""){
							$(".lx-floating-response").remove();
							window.clearTimeout(timer);
							$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
							$(".lx-floating-response").fadeIn();
							window.setTimeout(function(){
								$(".lx-floating-response").fadeOut();
							},5000);							
						}
						else{
							if($("#expenseform input[name='id']").val() !== "0"){
								var htmlclientselect = $("<select>" + htmlclientform + "</select>");
								htmlclientselect.find("option[value='"+clientid+"']").attr("data-phone", $("#expenseform input[name='phoneclient']").val()).attr("data-email",$("#expenseform input[name='emailclient']").val());
								htmlclientform = htmlclientselect.html();

								var htmlsupplierselect = $("<select>" + htmlsupplierform + "</select>");
								htmlsupplierselect.find("option[value='"+supplierid+"']").attr("data-phone", $("#expenseform input[name='phonesupplier']").val()).attr("data-email",$("#expenseform input[name='emailsupplier']").val());
								htmlsupplierform = htmlsupplierselect.html();

								$("#expenseform .lx-submit a").attr("class","");
								$("#expenseform .lx-submit a i").remove();
								$(".lx-popup-content > a > .material-icons").trigger("click");
								loadCaisse("1");
								loadNotification();	
								loadPriceRange($("#expenseform input[name='page']").val());
								$(".lx-floating-response").remove();
								window.clearTimeout(timer);
								$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
								$(".lx-floating-response").fadeIn();
								window.setTimeout(function(){
									$(".lx-floating-response").fadeOut();
								},5000);
							}
							else{
								window.location.href = window.location.href;
							}
						}
					}
				});
			}
		}
	}
	else{
		$("#expenseform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-order-virement","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	
	if($(this).attr("data-currency") === ""){
		$(".lx-order-text").text("Note supplimentaire: ");
	}
	else{
		$(".lx-order-text").text("Note supplimentaire (N° engagement, Frais ...): ");
	}
	$("#orderform textarea[name='note']").val("");
	tinyMCE.get("note2").setContent('');
	$("#orderform select[name='type']").val("");
	$("#orderform input[name='id']").val($(this).attr("data-id"));
});

$("#orderform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var id = $("#orderform input[name='id']").val();
	var note = $("#orderform textarea[name='note']").val();
	var type = $("#orderform select[name='type']").val();
	var signaturetext = "&label="+$("#orderform input[name='signaturetext']").val();
	var signature = $("#orderform select[name='signature']").val();
	var placement = $("#orderform select[name='placement']").val();
	var header = $("#orderform select[name='header']").val();
	isNotEmpty($("#orderform select[name='type']"));
	isNotEmpty($("#orderform select[name='signature']"));
	isNotEmpty($("#orderform select[name='placement']"));
	isNotEmpty($("#orderform select[name='header']"));
	if(isNotEmpty($("#orderform select[name='type']"))
	&& isNotEmpty($("#orderform select[name='signature']"))
	&& isNotEmpty($("#orderform select[name='placement']"))
	&& isNotEmpty($("#orderform select[name='header']"))){
		window.location.href = "printorder.php?id=" + id +
			"&type=" + encodeURIComponent(type) +
			"&note=" + encodeURIComponent(note) + signature + header + signaturetext + placement;
		loadCaisse("1");
	}
});

$("body").delegate(".lx-disposition","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#dispositionform select[name='rib']").html($(this).attr("data-bankaccounts"));
	$(".lx-company-mise").text($(this).attr("data-companytxt"));
	$("#dispositionform input[name='fullname']").val("");
	$("#dispositionform input[name='cin']").val("");
	$("#dispositionform textarea[name='note']").val("");
	$("#dispositionform input[name='id']").val($(this).attr("data-id"));
});

$("#dispositionform .lx-submit a").on("click",function(){
	var id = $("#dispositionform input[name='id']").val();
	var bankaccount = $("#dispositionform select[name='rib']").val();
	var fullname = $("#dispositionform input[name='fullname']").val();
	var cin = $("#dispositionform input[name='cin']").val();
	var note = $("#dispositionform textarea[name='note']").val();
	var signaturetext = "&label="+$("#dispositionform input[name='signaturetext']").val();
	var signature = $("#dispositionform select[name='signature']").val();
	var placement = $("#dispositionform select[name='placement']").val();
	var header = $("#dispositionform select[name='header']").val();
	isNotEmpty($("#dispositionform select[name='rib']"));
	isNotEmpty($("#dispositionform input[name='fullname']"));
	isNotEmpty($("#dispositionform input[name='cin']"));
	isNotEmpty($("#dispositionform select[name='signature']"));
	isNotEmpty($("#dispositionform select[name='placement']"));
	isNotEmpty($("#dispositionform select[name='header']"));
	if(isNotEmpty($("#dispositionform select[name='rib']"))
	&& isNotEmpty($("#dispositionform input[name='fullname']"))
	&& isNotEmpty($("#dispositionform input[name='cin']"))
	&& isNotEmpty($("#dispositionform select[name='signature']"))
	&& isNotEmpty($("#dispositionform select[name='placement']"))
	&& isNotEmpty($("#dispositionform select[name='header']"))){
		window.location.href = "printdisposition.php?id="+id+"&bankaccount="+bankaccount+"&fullname="+fullname+"&cin="+cin+"&note="+note+signature+header+signaturetext+placement;
		loadCaisse("1");
	}
});

$(".lx-new-product").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#productsform select[name='company']").val("");
	$("#productsform input[name='code']").val("");
	$("#productsform select[name='brand']").val("");
	$("#productsform input[name='brand']").val("");
	$("#productsform input[name='title']").val("");
	$("#productsform input[name='ref']").val("");
	$("#productsform input[name='family']").val("");
	$("#productsform select[name='family']").val("");
	toDropDownTargeted("#productsform select[name='family']");
	$("#productsform select[name='unit']").val("");
	$("#productsform input[name='bprice']").val("").prop("readonly",false);
	$("#productsform input[name='price']").val("");
	$("#productsform select[name='pricebase']").val("");
	$("#productsform select[name='tva']").val("");
	$("#productsform input[name='tva']").val("");
	$("#productsform select[name='bpricebase']").val("").prop("disabled",false);
	$("#productsform select[name='btva']").val("").prop("disabled",false);
	$("#productsform input[name='btva']").val("").attr("data-disabled","no").prop("readonly",false);
	$(".lx-reload-depots").html($(".lx-fresh-depots").html());
	$("#productsform input[name='placement']").val("").prop("readonly",false);
	$("#productsform select[name='invoiced']").val("").prop("disabled",false);
	$("#productsform input[name='qtymin']").val("");
	$("#productsform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-product","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#productsform select[name='company']").val($(this).attr("data-company"));
	$("#productsform input[name='code']").val($(this).attr("data-code"));
	$("#productsform select[name='brand']").val($(this).attr("data-brand"));
	$("#productsform input[name='brand']").val($(this).attr("data-brand"));
	$("#productsform input[name='title']").val($(this).attr("data-titl"));
	$("#productsform input[name='ref']").val($(this).attr("data-ref"));
	$("#productsform select[name='family']").val($(this).attr("data-family"));
	toDropDownTargeted("#productsform select[name='family']");
	$("#productsform select[name='unit']").val($(this).attr("data-unit"));
	$("#productsform input[name='bprice']").val($(this).attr("data-bprice")).prop("readonly",true);
	$("#productsform input[name='price']").val($(this).attr("data-price"));
	$("#productsform select[name='pricebase']").val($(this).attr("data-pricebase"));
	$("#productsform select[name='tva']").val($(this).attr("data-tva")+"%");
	$("#productsform input[name='tva']").val($(this).attr("data-tva"));
	$("#productsform select[name='bpricebase']").val($(this).attr("data-bpricebase")).prop("disabled",true);
	$("#productsform select[name='btva']").val($(this).attr("data-btva")+"%").prop("disabled",true);
	$("#productsform input[name='btva']").val($(this).attr("data-btva")).attr("data-disabled","yes").prop("readonly",true);
	$(".lx-reload-depots").html($(".lx-fresh-depots").html());
	$(".lx-depots input[name='placement']").val("");
	var depots = $(this).attr("data-depots").split(",");
	var placements = $(this).attr("data-placements").split(",");
	for(var i=0;i<depots.length;i++){
		$(".lx-depots input[name='placement'][data-id='"+depots[i]+"']").val(placements[i]);
	}
	$("#productsform select[name='invoiced']").val($(this).attr("data-invoiced")).prop("disabled",true);
	$("#productsform input[name='qtymin']").val($(this).attr("data-qtymin"));
	$("#productsform input[name='id']").val($(this).attr("data-id"));
});

$(".lx-move-product").on("click",function(){
	$("#moveproductsform select[name='depot']").val($("#moveproductsform select[name='depot'] option:first").val()).trigger("change");
	$("#moveproductsform select[name='depot2']").val($("#moveproductsform select[name='depot2'] option:first").val()).trigger("change");
	loadSimpleProductsList($("#moveproductsform select[name='depot']").val());
	$("#moveproductsform select[name='invoiced']").val($("#moveproductsform select[name='invoiced'] option:first").val()).prop("disabled",false);
	$("#moveproductsform input[name='qty']").val("");
});

$("#moveproductsform select[name='depot']").on("change",function(){
	loadSimpleProductsList($(this).val());
	$(".lx-restqty-move").text("");
});

function loadSimpleProductsList(depot){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			depot : depot,
			action : 'loadsimpleproductslist'
		},
		success : function(response){
			$("#moveproductsform select[name='product']").html(response);
			toDropDownTargeted("#moveproductsform select[name='product']");
			$("#moveproductsform select[name='product']").trigger("change");
		}
	});		
}

$("#moveproductsform select[name='product'],#moveproductsform select[name='invoiced']").on("change",function(){
	loadPricesProductsList($("#moveproductsform select[name='depot']").val(),$("#moveproductsform select[name='product']").val(),$("#moveproductsform select[name='invoiced']").val());
	$(".lx-restqty-move").text("");
});

$("#moveproductsform select[name='product']").on("change",function(){
	$("#moveproductsform input[name='qty']").val("");
	$("#moveproductsform select[name='depot2']").val("");
	$("#moveproductsform input[name='places']").val("").prop("readonly",false);
});

function loadPricesProductsList(depot,product,invoiced){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			depot : depot,
			product : product,
			invoiced : invoiced,
			action : 'loadpricesproductslist'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-restqty-move").text("("+response+")");
			}
			else{
				$(".lx-restqty-move").text("");
			}
		}
	});		
}

$("#moveproductsform select[name='depot2']").on("change",function(){
	loadPlacementsDepot($(this).val(),$("#moveproductsform select[name='product']").val());
});

function loadPlacementsDepot(depot,product){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			depot : depot,
			product : product,
			action : 'loadplacementsdepot'
		},
		success : function(response){
			if(response !== ""){
				$("#moveproductsform input[name='places']").val(response).prop("readonly",true);
			}
			else{
				$("#moveproductsform input[name='places']").val("").prop("readonly",false);
			}
		}
	});		
}

$("#moveproductsform .lx-submit a").on("click",function(){
	isNotEmpty($("#moveproductsform select[name='depot']"));
	isNotEmpty($("#moveproductsform select[name='product']"));
	isNotEmpty($("#moveproductsform select[name='invoiced']"));
	isNotEmpty($("#moveproductsform select[name='prices']"));
	isNumber($("#moveproductsform input[name='qty']"));
	isNotEmpty($("#moveproductsform select[name='depot2']"));
	isNotEmpty($("#moveproductsform input[name='places']"));
	if(isNotEmpty($("#moveproductsform select[name='depot']"))
	&& isNotEmpty($("#moveproductsform select[name='product']"))
	&& isNotEmpty($("#moveproductsform select[name='invoiced']"))
	&& isNotEmpty($("#moveproductsform select[name='prices']"))
	&& isNumber($("#moveproductsform input[name='qty']"))
	&& isNotEmpty($("#moveproductsform select[name='depot2']"))
	&& isNotEmpty($("#moveproductsform input[name='places']"))){
		var k = 0;
		var same = 0;
		if(typeof $("#moveproductsform select[name='places'] option:selected").attr("data-same") !== typeof undefined && $("#moveproductsform select[name='places'] option:selected").attr("data-same") !== false){
			var same = $("#moveproductsform select[name='places'] option:selected").attr("data-same");
			if($("#moveproductsform select[name='places'] option:selected").attr("data-same") === "0"){
				if(confirm('Cette emplacement est déja affecter à un autre produit / prix d\'achat, voulez vous l\'écraser?')){
					k = 0;
				} 
				else{
					k = 1;
				}
			}
		}
		
		if(k === 0){
			if($(this).attr("class") !== "lx-disabled"){
				$(this).attr("class","lx-disabled");
				if($(this).find("i").length === 0){
					$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
				}
				var ajaxurl = "ajax.php";
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						id : $("#moveproductsform select[name='places'] option:selected").attr("data-id"),
						depot : $("#moveproductsform select[name='depot']").val(),
						product : $("#moveproductsform select[name='product']").val(),
						invoiced : $("#moveproductsform select[name='invoiced']").val(),
						prices : $("#moveproductsform select[name='prices'] option:selected").attr("data-price"),
						qty : $("#moveproductsform input[name='qty']").val(),
						depot2 : $("#moveproductsform select[name='depot2']").val(),
						place : $("#moveproductsform input[name='places']").val(),
						same : same,
						action : 'moveproduct'
					},
					success : function(response){
						$("#moveproductsform .lx-submit a").attr("class","");
						$("#moveproductsform .lx-submit a i").remove();
						if(response !== ""){
							$(".lx-floating-response").remove();
							window.clearTimeout(timer);
							$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
							$(".lx-floating-response").fadeIn();
							window.setTimeout(function(){
								$(".lx-floating-response").fadeOut();
							},5000);
						}
						else{
							$(".lx-popup-content > a > .material-icons").trigger("click");
							loadProducts("1");
							$(".lx-floating-response").remove();
							window.clearTimeout(timer);
							$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Produit déplacé avec succés<i class="material-icons"></i></p></div>');
							$(".lx-floating-response").fadeIn();
							window.setTimeout(function(){
								$(".lx-floating-response").fadeOut();
							},5000);
						}
					}
				});
			}
		}
	}
	else{
		$("#moveproductsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("#productsform select[name='invoiced']").on("change",function(){
	if($(this).val() === "Non"){
		$(".invoicedyes").css("display","none");
		$("#productsform select[name='pricebase']").val("TTC");
		$("#productsform select[name='tva']").val("0%");
		$("#productsform input[name='tva']").val("0%");
		$("#productsform select[name='bpricebase']").val("TTC");
		$("#productsform select[name='btva']").val("0%");
		$("#productsform input[name='btva']").val("0%");
	}
	else{
		$(".invoicedyes").css("display","block");
	}
});

$("#productsform .lx-submit a").on("click",function(){
	var depots = "";
	var placements = "";
	$("#productsform input[name='depot']").each(function(){
		depots += ","+$(this).attr("data-id");
	});
	$("#productsform input[name='placement']").each(function(){
		placements += ","+$(this).val();
	});
	isNotEmpty($("#productsform select[name='company']"));
	isNotEmpty($("#productsform input[name='title']"));
	isNotEmpty($("#productsform input[name='ref']"));
	isNotEmpty($("#productsform select[name='family']"));
	isNotEmpty($("#productsform select[name='unit']"));
	// isNumber($("#productsform input[name='bprice']"));
	// isNotEmpty($("#productsform select[name='bpricebase']"));
	// isNotEmpty($("#productsform input[name='btva']"));
	isNumber($("#productsform input[name='price']"));
	isNotEmpty($("#productsform select[name='pricebase']"));
	isNotEmpty($("#productsform input[name='tva']"));
	isNumber($("#productsform input[name='qtymin']"));
	// isNotEmpty($("#productsform select[name='depot']"));
	// isNotEmpty($("#productsform select[name='invoiced']"));
	if(isNotEmpty($("#productsform input[name='title']"))
	&& isNotEmpty($("#productsform select[name='company']"))
	&& isNotEmpty($("#productsform input[name='ref']"))
	&& isNotEmpty($("#productsform select[name='family']"))
	&& isNotEmpty($("#productsform select[name='unit']"))
	// && isNumber($("#productsform input[name='bprice']"))
	// && isNotEmpty($("#productsform select[name='bpricebase']"))
	// && isNotEmpty($("#productsform input[name='btva']"))
	&& isNumber($("#productsform input[name='price']"))
	&& isNotEmpty($("#productsform select[name='pricebase']"))
	&& isNotEmpty($("#productsform input[name='tva']"))
	&& isNumber($("#productsform input[name='qtymin']"))
	// && isNotEmpty($("#productsform select[name='depot']"))
	// && isNotEmpty($("#productsform select[name='invoiced']"))
	){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#productsform input[name='id']").val(),
					company : $("#productsform select[name='company']").val(),
					code : $("#productsform input[name='code']").val(),
					brand : $("#productsform input[name='brand']").val(),
					title : $("#productsform input[name='title']").val(),
					ref : $("#productsform input[name='ref']").val(),
					family : $("#productsform select[name='family']").val(),
					unit : $("#productsform select[name='unit']").val(),
					bprice : $("#productsform input[name='bprice']").val(),
					price : $("#productsform input[name='price']").val(),
					pricebase : $("#productsform select[name='pricebase']").val(),
					tva : $("#productsform select[name='tva']").val(),
					bpricebase : $("#productsform select[name='bpricebase']").val(),
					btva : $("#productsform select[name='btva']").val(),
					depots : depots,
					placements : placements,
					invoiced : $("#productsform select[name='invoiced']").val(),
					qtymin : $("#productsform input[name='qtymin']").val(),
					depot : $("#productsform select[name='depot']").val(),
					action : 'addproduct'
				},
				success : function(response){
					$("#productsform .lx-submit a").attr("class","");
					$("#productsform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadProducts("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
						var existe = 0;
						$("#productsform select[name='brand'] option").each(function(){
							if($(this).text() === $("#productsform input[name='brand']").val()){
								exist = 1;
							}
						});
						if(exist === 0 && $("#productsform input[name='brand']").val() !== ""){
							$("#productsform select[name='brand']").append('<option value="'+$("#productsform input[name='brand']").val()+'">'+$("#productsform input[name='brand']").val()+'</option>');
						}
						toDropDown();
					}
				}
			});
		}
	}
	else{
		$("#productsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-duplicate-product","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'duplicateproduct'
		},
		success : function(response){
			loadProducts("1");
		}
	});
});

$("body").delegate(".lx-trash-product","click",function(){
	filterClicked = "yes";
	loadProducts("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-product","click",function(){
	filterClicked = "yes";
	loadProducts("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-product","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-product","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoreproduct'
		},
		success : function(response){
			loadProducts("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-product","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deleteproductpermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadProducts("0");
			}
		}
	});
});

$("#qtymin").on("click",function(){
	if($(".lx-pagination ul").attr("data-table") === "products"){
		loadProducts($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "inventairedetails"){
		loadInventaireDetails($(".lx-pagination ul").attr("data-state"));
	}
});

function loadProducts(state){
	if($(".lx-table-products .lx-loading").length === 0){
		$(".lx-table-products").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),	
			company : $("#company").attr("data-ids"),	
			depot : $("#depot").attr("data-ids"),	
			supplier : $("#supplier").attr("data-ids"),	
			family : $("#family").attr("data-ids"),	
			brand : $("#brand").val(),	
			qtymin : ($("#qtymin").prop("checked") === true?"1":"0"),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadproducts'
		},
		success : function(response){
			$(".lx-table-products .lx-loading").remove();
			$(".lx-table-products").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-edit-price","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#priceouiform .lx-price-title").text($(this).attr("data-product"));
	$("#priceouiform .lx-price-qty").text($(this).attr("data-qty"));
	$("#priceouiform .lx-price-invoiced").text($(this).attr("data-invoiced"));
	$("#priceouiform .lx-price-placement").text($(this).attr("data-placement"));
	$("#priceouiform input[name='bprice']").val($(this).attr("data-price"));
	$("#priceouiform select[name='bpricebase']").val($(this).attr("data-bpricebase"));
	$("#priceouiform select[name='btva']").val($(this).attr("data-btva")+"%");
	$("#priceouiform input[name='btva']").val($(this).attr("data-btva"));
	$("#priceouiform input[name='id']").val($(this).attr("data-id"));
	$("#priceouiform input[name='idproduct']").val($(this).attr("data-idproduct"));
});

$("#priceouiform .lx-submit a").on("click",function(){
	isNumber($("#priceouiform input[name='bprice']"));
	isNotEmpty($("#priceouiform select[name='bpricebase']"));
	isNotEmpty($("#priceouiform input[name='btva']"));
	if(isNumber($("#priceouiform input[name='bprice']"))
	&& isNotEmpty($("#priceouiform select[name='bpricebase']"))
	&& isNotEmpty($("#priceouiform input[name='btva']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#priceouiform input[name='id']").val(),
					idproduct : $("#priceouiform input[name='idproduct']").val(),
					bprice : $("#priceouiform input[name='bprice']").val(),
					bpricebase : $("#priceouiform select[name='bpricebase']").val(),
					btva : $("#priceouiform select[name='btva']").val(),
					action : 'addprice'
				},
				success : function(response){
					$("#priceouiform .lx-submit a").attr("class","");
					$("#priceouiform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadProducts("1");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
				}
			});
		}
	}
	else{
		$("#priceouiform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-edit-price","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#pricenonform .lx-price-title").text($(this).attr("data-product"));
	$("#pricenonform .lx-price-qty").text($(this).attr("data-qty"));
	$("#pricenonform .lx-price-invoiced").text($(this).attr("data-invoiced"));
	$("#pricenonform .lx-price-placement").text($(this).attr("data-placement"));
	$("#pricenonform input[name='bprice']").val($(this).attr("data-price"));
	$("#pricenonform input[name='id']").val($(this).attr("data-id"));
	$("#pricenonform input[name='idproduct']").val($(this).attr("data-idproduct"));
});

$("#pricenonform .lx-submit a").on("click",function(){
	isNumber($("#pricenonform input[name='bprice']"));
	if(isNumber($("#pricenonform input[name='bprice']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#pricenonform input[name='id']").val(),
					idproduct : $("#pricenonform input[name='idproduct']").val(),
					bprice : $("#pricenonform input[name='bprice']").val(),
					action : 'addpricenon'
				},
				success : function(response){
					$("#pricenonform .lx-submit a").attr("class","");
					$("#pricenonform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadProducts("1");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
				}
			});
		}
	}
	else{
		$("#pricenonform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-edit-placement","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#placementform input[name='place']").val($(this).attr("data-placement"));
	$("#placementform select[name='place']").val($(this).attr("data-placement"));
	$("#placementform input[name='id']").val($(this).attr("data-id"));
});

$("#placementform .lx-submit a").on("click",function(){
	isNotEmpty($("#placementform input[name='place']"));
	if(isNotEmpty($("#placementform input[name='place']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#placementform input[name='id']").val(),
					placement : $("#placementform input[name='place']").val(),
					action : 'editplacement'
				},
				success : function(response){
					$("#placementform .lx-submit a").attr("class","");
					$("#placementform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadProducts("1");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
				}
			});
		}
	}
	else{
		$("#placementform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-upload-photos input","change",function(){
	if($(this).next("a").find(".fa-circle-notch").length === 0){
		$(this).next("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	for(var i = 0;i < _($(this).attr("id")).files.length;i++){
		uploadPhotos(_($(this).attr("id")).files[i],$(this).attr("data-id"),$(this).attr("data-table"),$(this).attr("data-column"));
	}
});

$("body").delegate("#uploadphotos0","change",function(){
	if($(this).next("a").find(".fa-circle-notch").length === 0){
		$(this).next("a").prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	uploadPhotos(_($(this).attr("id")).files[0],$("#uploadphotos0").attr("data-id"),$("#uploadphotos0").attr("data-table"),$("#uploadphotos0").attr("data-column"));
});

function uploadPhotos(picture,id,table,column){
	var formdata = new FormData();
	formdata.append("file1", picture);
	formdata.append("id", id);
	formdata.append("table", table);
	formdata.append("column", column);
	var ajax = new XMLHttpRequest();
	ajax.addEventListener("load", completeHandler, false);
	ajax.open("POST", "file_upload_parser.php");
	ajax.send(formdata);
	function completeHandler(event){
		if (ajax.readyState === 4 && (ajax.status === 200 || ajax.status === 0)) {
			if($(".lx-pagination ul").attr("data-table") === "users"){
				loadUsers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				loadClients($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				loadFactures($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "companies"){
				loadCompanies($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "prospects"){
				loadProspects($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "labels"){
				loadLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "doclabels"){
				loadDocLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "messages"){
				loadMessages($(".lx-pagination ul").attr("data-state"));
			}
		}
	}	
}

$("body").delegate(".lx-show-pictures","click",function(){
	$(this).parent().find(".lx-photos-preview").find("img").attr("src","uploads/"+$(this).attr("data-pic"));
	$(this).parent().find(".lx-photos-preview").find("a").attr("data-pic",$(this).attr("data-pic"));
});

$("body").delegate(".lx-delete-images","click",function(){
	$(this).next(".lx-delete-image-choice").toggle();
});

$("body").delegate(".lx-no-delete-image","click",function(){
	$(this).parent().toggle();
});

$("body").delegate(".lx-yes-delete-image","click",function(){
	var allpics = $(this).attr("data-allpics");
	var pic = $(this).attr("data-pic");
	var id = $(this).attr("data-id");
	var table = $(this).attr("data-table");
	var column = $(this).attr("data-column");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			allpics : allpics,
			pic : pic,
			id : id,
			table : table,
			column : column,
			action : 'deleteproductpicture'
		},
		success : function(response){
			if($(".lx-pagination ul").attr("data-table") === "users"){
				loadUsers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				loadClients($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				loadFactures($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "companies"){
				loadCompanies($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "prospects"){
				loadProspects($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "labels"){
				loadLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "doclabels"){
				loadDocLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "messages"){
				loadMessages($(".lx-pagination ul").attr("data-state"));
			}
		}		
	});	
});

$(".lx-new-service").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#servicesform input[name='company']").val("");
	$("#servicesform .lx-advanced-select input[type='checkbox']").each(function(){
		$(this).prop("checked",false);
	});
	$("#servicesform input[name='title']").val("");
	$("#servicesform input[name='ref']").val("");
	$("#servicesform input[name='family']").val("");
	$("#servicesform select[name='family']").val("");
	toDropDownTargeted("#servicesform select[name='family']");
	$("#servicesform select[name='unit']").val("");
	$("#servicesform input[name='bprice']").val("").prop("readonly",false);
	$("#servicesform input[name='bprice']").parent().parent().parent().parent().css("display","block");
	$("#servicesform input[name='price']").val("");
	$("#servicesform select[name='pricebase']").val("");
	$("#servicesform select[name='tva']").val("");
	$("#servicesform input[name='tva']").val("");
	$("#servicesform input[name='btva']").val("").attr("data-disabled","no").prop("readonly",false);
	$("#servicesform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-service","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#servicesform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#servicesform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split(",");
	$("#servicesform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	$("#servicesform input[name='title']").val($(this).attr("data-titl"));
	$("#servicesform input[name='ref']").val($(this).attr("data-ref"));
	$("#servicesform select[name='family']").val($(this).attr("data-family"));
	toDropDownTargeted("#servicesform select[name='family']");
	$("#servicesform select[name='unit']").val($(this).attr("data-unit"));
	$("#servicesform input[name='bprice']").val($(this).attr("data-bprice")).prop("readonly",$(this).attr("data-readonly"));
	$("#servicesform input[name='bprice']").parent().parent().parent().parent().attr("style",$("#servicesform input[name='bprice']").parent().parent().parent().parent().attr("data-display"));
	$("#servicesform input[name='price']").val($(this).attr("data-price"));
	$("#servicesform select[name='pricebase']").val($(this).attr("data-pricebase"));
	$("#servicesform select[name='tva']").val($(this).attr("data-tva")+"%");
	$("#servicesform input[name='tva']").val($(this).attr("data-tva"));
	$("#servicesform input[name='btva']").val($(this).attr("data-btva")).attr("data-disabled","yes").prop("readonly",true);
	$("#servicesform input[name='id']").val($(this).attr("data-id"));
});

$("#servicesform .lx-submit a").on("click",function(){
	isNotEmpty($("#servicesform input[name='company']"));
	isNotEmpty($("#servicesform input[name='title']"));
	isNotEmpty($("#servicesform input[name='ref']"));
	isNotEmpty($("#servicesform select[name='family']"));
	isNotEmpty($("#servicesform select[name='unit']"));
	isNumber($("#servicesform input[name='bprice']"));
	isNumber($("#servicesform input[name='price']"));
	isNotEmpty($("#servicesform select[name='pricebase']"));
	isNotEmpty($("#servicesform select[name='tva']"));
	if(isNotEmpty($("#servicesform input[name='title']"))
	&& isNotEmpty($("#servicesform input[name='ref']"))
	&& isNotEmpty($("#servicesform input[name='company']"))
	&& isNotEmpty($("#servicesform select[name='family']"))
	&& isNotEmpty($("#servicesform select[name='unit']"))
	&& isNumber($("#servicesform input[name='bprice']"))
	&& isNumber($("#servicesform input[name='price']"))
	&& isNotEmpty($("#servicesform select[name='pricebase']"))
	&& isNotEmpty($("#servicesform select[name='tva']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#servicesform input[name='id']").val(),
					company : $("#servicesform input[name='company']").attr("data-ids"),
					title : $("#servicesform input[name='title']").val(),
					ref : $("#servicesform input[name='ref']").val(),
					family : $("#servicesform select[name='family']").val(),
					unit : $("#servicesform select[name='unit']").val(),
					qtymin : '0',
					bprice : $("#servicesform input[name='bprice']").val(),
					price : $("#servicesform input[name='price']").val(),
					pricebase : $("#servicesform select[name='pricebase']").val(),
					tva : $("#servicesform select[name='tva']").val(),
					bpricebase : 'HT',
					btva : $("#servicesform select[name='tva']").val(),
					action : 'addservice'
				},
				success : function(response){
					$("#servicesform .lx-submit a").attr("class","");
					$("#servicesform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadServices("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
						var existe = 0;
						$("#servicesform select[name='brand'] option").each(function(){
							if($(this).text() === $("#servicesform input[name='brand']").val()){
								exist = 1;
							}
						});
						if(exist === 0 && $("#servicesform input[name='brand']").val() !== ""){
							$("#servicesform select[name='brand']").append('<option value="'+$("#servicesform input[name='brand']").val()+'">'+$("#servicesform input[name='brand']").val()+'</option>');
						}
						toDropDown();
						reloadServiceList();
					}
				}
			});
		}
	}
	else{
		$("#servicesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

function reloadServiceList(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'reloadservicelist'
		},
		success : function(response){
			$("#servicesform select[name='title']").html(response);
			toDropDownTargeted("#servicesform select[name='title']");
		}
	});
}

$("body").delegate(".lx-duplicate-service","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'duplicateservice'
		},
		success : function(response){
			loadServices("1");
		}
	});
});

$("body").delegate(".lx-trash-service","click",function(){
	filterClicked = "yes";
	loadServices("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-service","click",function(){
	filterClicked = "yes";
	loadServices("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-service","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-service","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoreservice'
		},
		success : function(response){
			loadServices("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-service","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deleteservicepermanently'
		},
		success : function(response){
			if(response !== ""){
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
			else{
				loadServices("0");
			}
		}
	});
});

$("#qtymin").on("click",function(){
	loadServices($(".lx-pagination ul").attr("data-state"));
});

function loadServices(state){
	if($(".lx-table-services .lx-loading").length === 0){
		$(".lx-table-services").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),	
			company : $("#company").attr("data-ids"),	
			family : $("#family").attr("data-ids"),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadservices'
		},
		success : function(response){
			$(".lx-table-services .lx-loading").remove();
			$(".lx-table-services").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-unite").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#unitesform input[name='title']").val("");
	$("#unitesform input[name='id']").val("0");
});

$("body").delegate(".lx-edit-unite","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#unitesform input[name='title']").val($(this).attr("data-titl"));
	$("#unitesform input[name='id']").val($(this).attr("data-id"));
});

$("#unitesform .lx-submit a").on("click",function(){
	isNotEmpty($("#unitesform input[name='title']"));
	if(isNotEmpty($("#unitesform input[name='title']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#unitesform input[name='id']").val(),
					title : $("#unitesform input[name='title']").val(),
					type : $("#unitesform input[name='type']").val(),
					action : 'addunite'
				},
				success : function(response){
					$("#unitesform .lx-submit a").attr("class","");
					$("#unitesform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadUnites("1");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#unitesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-unite","click",function(){
	filterClicked = "yes";
	loadUnites("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-unite","click",function(){
	filterClicked = "yes";
	loadUnites("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-unite","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-unite","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restoreunite'
		},
		success : function(response){
			loadUnites("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-unite","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deleteunitepermanently'
		},
		success : function(response){
			loadUnites("0");
		}
	});
});

function loadUnites(state){
	if($(".lx-table-unites .lx-loading").length === 0){
		$(".lx-table-unites").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			type : $("#type").val(),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadunites'
		},
		success : function(response){
			$(".lx-table-unites .lx-loading").remove();
			$(".lx-table-unites").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$(".lx-new-familie").on("click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#familiesform select[name='company']").val("");
	$("#familiesform input[name='company']").val("");
	$("#familiesform .lx-advanced-select input[type='checkbox']").each(function(){
		$(this).prop("checked",false);
	});
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	reloadFamilies();
	$("#familiesform input[name='parent']").val("");
	$("#familiesform select[name='parent']").val("").parent().parent().css("display","block");
	$("#familiesform input[name='title']").val("");
	$("#familiesform input[name='id']").val("0");
});

function reloadFamilies(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			type : $("#type").val(),
			action : 'reloadfamilies'
		},
		success : function(response){
			$("#familiesform select[name='parent']").html(response);
			toDropDown();
		}
	});	
}

$("body").delegate(".lx-edit-familie","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#familiesform input[name='company']").attr("data-ids",$(this).attr("data-company"));
	$("#familiesform input[name='company']").val($(this).attr("data-companytxt"));
	var companies = $(this).attr("data-company").split(",");
	$("#familiesform .lx-advanced-select input[type='checkbox']").each(function(){
		for(var i=0;i<companies.length;i++){
			if($(this).attr("data-ids") === companies[i]){
				$(this).prop("checked",true);
				break;
			}
			else{
				$(this).prop("checked",false);
			}
		}
	});
	if($(this).attr("data-parent") === "0"){
		$("#familiesform select[name='parent']").val($(this).attr("data-parent")).parent().parent().css("display","none");
	}
	else{
		$("#familiesform select[name='parent']").val($(this).attr("data-parent")).parent().parent().css("display","block");
	}
	toDropDownTargeted("#familiesform select[name='parent']");
	$("#familiesform input[name='title']").val($(this).attr("data-titl"));
	$("#familiesform input[name='id']").val($(this).attr("data-id"));
});

$("#familiesform .lx-submit a").on("click",function(){
	isNotEmpty($("#familiesform input[name='company']"));
	isNotEmpty($("#familiesform input[name='title']"));
	if(isNotEmpty($("#familiesform input[name='title']"))
	&& isNotEmpty($("#familiesform input[name='company']"))){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#familiesform input[name='id']").val(),
					parent : $("#familiesform select[name='parent']").val(),
					title : $("#familiesform input[name='title']").val(),
					company : $("#familiesform input[name='company']").attr("data-ids"),
					type : $("#familiesform input[name='type']").val(),
					action : 'addfamilie'
				},
				success : function(response){
					$("#familiesform .lx-submit a").attr("class","");
					$("#familiesform .lx-submit a i").remove();
					if(response !== ""){
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
					else{
						$(".lx-popup-content > a > .material-icons").trigger("click");
						loadFamilies("1");
						reloadFamilies();
						toDropDownTargeted("#familiesform select[name='parent']");
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				}
			});
		}
	}
	else{
		$("#familiesform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

$("body").delegate(".lx-trash-familie","click",function(){
	filterClicked = "yes";
	loadFamilies("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-familie","click",function(){
	filterClicked = "yes";
	loadFamilies("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-familie","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-familie","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorefamilie'
		},
		success : function(response){
			loadFamilies("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-familie","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletefamiliepermanently'
		},
		success : function(response){
			loadFamilies("0");
			reloadFamilies();
		}
	});
});

function loadFamilies(state){
	if($(".lx-table-families .lx-loading").length === 0){
		$(".lx-table-families").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			type : $("#type").val(),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadfamilies'
		},
		success : function(response){
			$(".lx-table-families .lx-loading").remove();
			$(".lx-table-families").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

var htmtproductcommandform = $("#commandsform select[name='product']").clone().html();
$("#commandsform select[name='depot']").on("change",function(){
	$("#commandsform select[name='product']").html(htmtproductcommandform);
	var depot = $(this).val();
	$("#commandsform select[name='product'] option[data-depot]").each(function(){
		if(!$(this).attr("data-depot").split(",").includes(depot)){
			$(this).remove();
		}
	});
	loadProductsList($("#commandsform input[name='id']").val(),$("#commandsform input[name='allproducts']").val(),$("#commandsform select[name='depot']").val(),$("#commandsform select[name='company']").val());
	$("#commandsform label del").text("");
});

$("#commandsform select[name='modepayment']").on("change",function(){
	if($(this).val() === "Espèce"){
		$(".lx-command-caisse").css("display","block");
	}
	else{
		$(".lx-command-caisse").css("display","none");
		$("#commandsform select[name='caisse']").val("");
	}
});

var transform = 0;
$(".lx-new-command").on("click",function(){
	transform = 0;
	loadProductsList($("#commandsform input[name='id']").val(),$("#commandsform input[name='allproducts']").val(),$("#commandsform select[name='depot']").val(),$("#commandsform select[name='company']").val());
	$("#commandsform input[name='table']").val($(this).attr("data-table"));
	$("#commandsform select,#commandsform input[type='text']").removeAttr("style");
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#commandsform select[name='transform']").html($(".lx-select-facture").html());
	$(".lx-create-avoir").css("display","block");
	$(".lx-uncreate-avoir").css("display","none");
	$("input[name='qtyback']").val("");
	$(".lx-qty-back").css("display","none");
	$(".lx-transform").css("display","none");
	$("#commandsform input[name='id']").val("0");
	$("#commandsform select[name='company']").val($("#commandsform select[name='company'] option:first").val()).prop("disabled",false);
	$("#commandsform input[name='new'][value='1']").prop("checked",true);
	$("#commandsform input[name='exist']").val("1");
	$("#commandsform input[name='client']").val("");
	$("#commandsform select[name='client']").val("");
	toDropDownTargeted("#commandsform select[name='client']");
	$("#commandsform input[name='ice']").val("");
	$("#commandsform input[name='phone']").val("");
	$("#commandsform input[name='address']").val("");
	$("#commandsform input[name='email']").val("");
	$("#commandsform input[name='product']").val("");
	$("#commandsform select[name='product']").val("0");
	$("#commandsform input[name='qty']").val("1");
	$("#commandsform input[name='uprice']").val("");
	$("#commandsform select[name='pricebase']").val("HT");
	$("#commandsform select[name='utva']").val("");
	$(".lx-list-products table tr.lx-otherrow").remove();
	$(".lx-list-products table tr.lx-secondrow").css("display","table-row");
	$(".lx-error-products").html("");
	$(".lx-error-command").html("");
	$(".lx-cash").css("display","none");
	var type = $("#commandsform input[name='type']").val();
	if(type === "facture" || type === "nh" || type === "avoir"){
		$(".lx-cash").css("display","block");
	}
	$(".lx-client").show();
	$("#commandsform select[name='typediscount']").val("").prop("disabled",false).trigger("change");
	$("#commandsform select[name='typediscount']").parent().parent().css("display","block");
	$("#commandsform input[name='maindiscount']").val("").prop("readonly",false).attr("data-click","false").trigger("click");
	$("#commandsform input[name='maindiscount']").parent().parent().css("display","block");
	$("#commandsform input[name='price']").val("0");
	$("#commandsform input[name='tva']").val("0");
	$("#commandsform input[name='ttcprice']").val("0");
	$("#commandsform select[name='modepayment']").val("").trigger("change");
	$("#commandsform input[name='modepayment']").val("");
	$("#commandsform input[name='cash']").val("0");
	$("#commandsform input[name='rest']").val("0");
	$("#commandsform select[name='imputation']").val("");
	$("#commandsform input[name='imputation']").val("");
	$("#commandsform input[name='allproducts']").val("");
	$("#commandsform input[name='allqtys']").val("");
	$("#commandsform input[name='allprices']").val("");
	$("#commandsform input[name='dateaddcommand']").val(getCurrentDate());
	$("#commandsform textarea[name='note']").val("");
	if($("#correctdoc").length){
		$("#correctdoc").val("");
	}
	tinyMCE.get("note").setContent('');
	$(".lx-qty-back").css("display","none");
	$(".lx-delete-cell").css("display","table-cell");
	$(".lx-edit-cell").css("display","table-cell");
	$("#commandsform select[name='transform']").val($("#commandsform select[name='transform'] option:first").val()).trigger("change");		
	$("#commandsform input[name='discount']").val("").prop("readonly",false);
	$("#commandsform select[name='company']").trigger("change");
	$("#commandsform input[name='new'][value='1']").trigger("click");
	$(".lx-client-avoir").css("display","block");
	$(".lx-avoir-client-message").html('');
});

$("body").delegate(".lx-edit-command","click",function(){
	transform = 1;
	//loadProductsList($(this).attr("data-id"),$(this).attr("data-allproducts"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#commandsform input[name='id']").val($(this).attr("data-id"));
	if($(this).attr("data-id") === "-1"){
		$("#commandsform select[name='transform']").html($(".lx-select-avoir").html());
		$("#commandsform select[name='transform']").prev("span").text("Type de document:");
		$(".lx-create-avoir").css("display","none");
		$(".lx-uncreate-avoir").css("display","block");
	}
	else{
		$("#commandsform select[name='transform']").html($(".lx-select-facture").html());
		$("#commandsform select[name='transform']").prev("span").text("Duppliquer ou transformer vers:");
		$(".lx-create-avoir").css("display","block");
		$(".lx-uncreate-avoir").css("display","none");
		$("input[name='qtyback']").val("");
	}
	if($(this).attr("data-id") === "0" || $(this).attr("data-id") === "-1"){
		$("#commandsform input[name='dateaddcommand']").val(getCurrentDate());
		$(".lx-transform").css("display","block");
	}
	else{
		$("#commandsform input[name='dateaddcommand']").val($(this).attr("data-dateadd"));
		$(".lx-transform").css("display","none");
	}
	$("#commandsform input[name='table']").val($(this).attr("data-table"));
	$("#commandsform select,#commandsform input[type='text']").removeAttr("style");
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$("#commandsform select[name='company']").val($(this).attr("data-company"));
	$("#commandsform select[name='company']").trigger("change");
	var rib = $(this).attr("data-rib");
	window.setTimeout(function(){
		$("#commandsform select[name='rib']").val(rib);
	},1000);
	$("#commandsform input[name='new'][value='"+$(this).attr("data-exist")+"']").prop("checked",true);
	$("#commandsform input[name='exist']").val($(this).attr("data-exist"));
	$("#commandsform input[name='ice']").val($(this).attr("data-ice"));
	$("#commandsform input[name='phone']").val($(this).attr("data-phone"));
	$("#commandsform input[name='address']").val($(this).attr("data-address"));
	$("#commandsform input[name='email']").val($(this).attr("data-email"));
	$("#commandsform input[name='product']").val("");
	$("#commandsform select[name='product']").val("0");
	$("#commandsform input[name='qty']").val("1");
	$("#commandsform input[name='uprice']").val("");
	$(".lx-list-products").html($(this).attr("data-products").replace(/&#39;/g, "'"));
	$(".lx-list-products tr.lx-otherrow").each(function(){
		$(this).attr("data-title",$(this).find("td:eq(0) span:eq(1)").text());
	});
	$(".lx-error-products").html("");
	$(".lx-error-command").html("");
	$(".lx-cash").hide();
	if($(this).attr("data-exist") === "0"){
		$(".lx-client").show();
	}
	else if($(this).attr("data-exist") === "1"){
		$(".lx-client").show();
	}
	else{
		$(".lx-client").hide();
	}
	$("#commandsform select[name='typediscount']").val($(this).attr("data-discounttype"));
	if($(this).attr("data-discounttype") !== ""){
		$(".lx-discount").css("display","table-cell");
		$(".maindiscount").css("display","block");
		if($("#commandsform select[name='typediscount']").val() === $("#currency").val()){
			var discount = 0;
			$(".lx-list-products table tr").each(function(){
				if($(this).find("input[name='discount']").val() !== ""){
					discount += parseFloat($(this).find("input[name='discount']").val());
				}
			});
			$("#commandsform input[name='maindiscount']").val(discount.toFixed(2));	
		}
		else{
			var discount = 0;
			$(".lx-list-products table tr").each(function(){
				if($(this).find("input[name='discount']").val() !== ""){
					discount = parseFloat($(this).find("input[name='discount']").val());
				}
			});
			$("#commandsform input[name='maindiscount']").val(discount.toFixed(2));				
		}
	}
	$(".lx-list-products table tr td input[name='discount']").trigger("click");
	$("#commandsform select[name='modepayment']").val($(this).attr("data-modepayment")).trigger("change");
	$("#commandsform input[name='modepayment']").val($(this).attr("data-modepayment"));
	$("#commandsform input[name='cash']").val("0");
	$("#commandsform input[name='rest']").val("0");
	$("#commandsform input[name='imputation']").val($(this).attr("data-imputation"));
	$("#commandsform select[name='imputation']").val($(this).attr("data-imputation"));
	$("#commandsform textarea[name='note']").val($(this).attr("data-note"));
	tinyMCE.get("note").setContent($(this).attr("data-note"));
	$("#commandsform input[name='code']").val($(this).attr("data-code"));
	$("#correctdoc").val($(this).attr("data-correctdoc"));
	
	if($(this).attr("data-avoirnative") === "1"){
		var title = "facture";
		if($("#commandsform input[name='correctdoc']").val().includes("NH")){
			title = "note d'honoraire";			
		}
		$(".lx-client-avoir").css("display","none");
		$(".lx-avoir-client-message").html('<p style="padding:15px;font-weight:600;font-size:14px;background:#fcefb4;color:#d9a029;border:1px solid #d9a029;border-radius:6px;"><i class="fa fa-info-circle" style="font-size:20px;"></i> &nbsp;La modification du client n’est pas autorisée pour un avoir associé à une '+title+'.</p>');
	}
	else{
		$(".lx-client-avoir").css("display","block");
		$(".lx-avoir-client-message").html('');
	}
	
	var client = $(this).attr("data-client");
	var prospect = $(this).attr("data-prospect");
	$("#commandsform fieldset").css("position","relative");

	if($("#commandsform fieldset .lx-loading").length === 0){
		$("#commandsform fieldset").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	$("#commandsform .lx-submit a").html('<i class="fa fa-circle-notch fa-spin"></i> Chargement');
	$("#commandsform .lx-submit a").addClass("lx-disabled");
	var type = "";
	if(parseFloat($(this).attr("data-client")) > 0){
		type = "";
	}
	else if(parseFloat($(this).attr("data-prospect")) > 0){
		type = "prospect";
	}
	window.setTimeout(function(){
		if(type === "prospect"){
			$("#commandsform select[name='client'] option[data-type='"+type+"'][value='"+prospect+"']").prop("selected",true);
		}
		else{
			$("#commandsform select[name='client']").val(client);
		}
		toDropDownTargeted("#commandsform select[name='client']");
		addClientToOrder();	
		$("#commandsform fieldset .lx-loading").remove();
		$("#commandsform .lx-submit a").html("Enregistrer");
		$("#commandsform .lx-submit a").removeClass("lx-disabled");
	},4000);
	
	if($(this).attr("data-id") === "-1"){
		$("#commandsform select[name='company']").prop("disabled",true);
		$(".lx-qty-back").css("display","table-cell");
		$(".lx-edit-cell").css("display","none");
		$("#commandsform select[name='typediscount']").prop("disabled",true);
		$("#commandsform input[name='discount']").prop("readonly",true);
		$("#commandsform input[name='maindiscount']").prop("readonly",true);
		if($(this).attr("data-discounttype") === ""){
			$("#commandsform select[name='typediscount']").parent().parent().css("display","none");
			$("#commandsform input[name='maindiscount']").parent().parent().css("display","none");
		}
		else{
			$("#commandsform select[name='typediscount']").parent().parent().css("display","block");
			$("#commandsform input[name='maindiscount']").parent().parent().css("display","block");			
		}
		$(".lx-delete-cell").css("display","none");
		$("input[name='qtyback']").each(function(){
			let unit = $(this).parent().parent().parent().parent().attr("data-unit");
			let qty = parseFloat($(this).parent().parent().parent().parent().attr("data-qty"));
			let qtyavoir = parseFloat($(this).parent().parent().parent().parent().attr("data-qtyavoir"));
			$(this).attr("max",(qty - qtyavoir).toFixed(2));
			$(this).parent().parent().parent().parent().find("td:eq(1)").html('<span>' + (qty - qtyavoir).toFixed(2) + ' ' + unit + '</span>');
			if((qty - qtyavoir) <= 0){
				$(this).parent().parent().parent().parent().css("display","none");
			}
		});
	}
	else{
		$("#commandsform select[name='company']").prop("disabled",false);
		$("input[name='qtyback']").val("");
		$(".lx-qty-back").css("display","none");
		$("#commandsform select[name='typediscount']").prop("disabled",false);
		$("#commandsform input[name='discount']").prop("readonly",false);
		$("#commandsform input[name='maindiscount']").prop("readonly",false);
		$("#commandsform select[name='typediscount']").parent().parent().css("display","block");
		$("#commandsform input[name='maindiscount']").parent().parent().css("display","block");
		$(".lx-delete-cell").css("display","table-cell");
		$(".lx-edit-cell").css("display","table-cell");
		$("input[name='qtyback']").each(function(){
			$(this).attr("max",$(this).parent().parent().parent().parent().attr("data-qty"));
		});
		if($("#commandsform input[name='id']").val() === "0"){
			$("#commandsform input[name='correctdoc']").val("");
		}
	}
	$("#commandsform select[name='transform']").val($("#commandsform select[name='transform'] option:first").val()).trigger("change");	
	$(".discounttype").text($(this).attr("data-discounttype"));
	calculateProfit();
	$("#commandsform input[name='modepayment']").val($(this).attr("data-modepayment"));
	$("#commandsform input[name='conditions']").val($(this).attr("data-conditions"));
	$("#commandsform input[name='abovetable']").val($(this).attr("data-abovetable"));
});

$(".lx-popup-content").delegate("input[name='qtyback']","keyup", function() {
	var row = $(this).parent().parent().parent().parent();
	var qty = $(this).val();
	//row.find("input[name='discount']").trigger("keyup");
	var tqty = parseFloat(row.find("td:eq(1)").text()) + parseFloat(row.attr("data-qtyavoir"));
	var tprice = parseFloat(row.attr("data-uprice")) * parseFloat(qty);
	var remise = (parseFloat(row.attr("data-oremise")) / tqty) * parseFloat(qty);
	var ttprice = tprice - remise;
	row.attr("data-remise",remise.toFixed(2));
	row.attr("data-tprice",tprice.toFixed(2));
	row.attr("data-ttprice",ttprice.toFixed(2));
	var maxValue = parseFloat($(this).attr('max'));
	var enteredValue = parseFloat($(this).val());

	if (enteredValue > maxValue) {
		$(this).val(maxValue); // Set the value to the maximum allowed
	}
});

$('#commandsform input[name="dateaddcommand"]').on("keyup",function(){
	if($("#commandsform select[name='company']").val() !== ""){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				company : $("#commandsform select[name='company']").val(),
				type : $("#commandsform input[name='type']").val(),
				dateadd : $("#commandsform input[name='dateaddcommand']").val(),
				category : $("#commandsform input[name='categories']").val(),
				action : 'loadcode'
			},
			success : function(response){
				console.log(response);
				$("#commandsform input[name='code']").val(response);
				$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('#commandsform input[name="dateaddcommand"]').val())+"-");
				$("#commandsform input[name='code']").trigger("blur");
			}
		});
	}
});

$('#mergeform input[name="dateaddcommand"]').on("keyup",function(){
	if($("#mergeform select[name='company']").val() !== ""){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				company : $("#mergeform select[name='company']").val(),
				type : $("#mergeform input[name='type']").val(),
				dateadd : $("#mergeform input[name='dateaddcommand']").val(),
				category : $("#mergeform input[name='categories']").val(),
				action : 'loadcode'
			},
			success : function(response){
				console.log(response);
				$("#mergeform input[name='code']").val(response);
				$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
				$("#mergeform input[name='code']").trigger("blur");
			}
		});
	}
});

function convertDateFormat(inputDate) {
  if (!inputDate || typeof inputDate !== "string") return ""; // handle empty or invalid input
	  const parts = inputDate.split('/');
	  // Make sure we have day, month, and year
  if (parts.length < 3) return "";
	  const [day, month, year] = parts.map(p => p.trim());
	  // Ensure the year exists and has at least 2 digits
  if (!year || year.length < 2) return "";
	  return year.slice(-2) + month;
}

$("#commandsform select[name='transform']").on("change",function(){
	transform = 1;
	$("#commandsform input[name='type']").val($(this).val());
	$("#commandsform input[name='prefix']").val($(this).find("option:selected").attr("data-prefix"));
	if($("#commandsform input[name='id']").val() === "0" || $("#commandsform input[name='id']").val() === "-1"){
		if($("#commandsform select[name='company']").val() !== ""){
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					company : $("#commandsform select[name='company']").val(),
					type : $("#commandsform input[name='type']").val(),
					dateadd : $("#commandsform input[name='dateaddcommand']").val(),
					category : $("#commandsform input[name='categories']").val(),
					action : 'loadcode'
				},
				success : function(response){
				console.log(response);
					$("#commandsform input[name='code']").val(response);
					$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('input[name="dateaddcommand"]').val())+"-");
					$("#commandsform input[name='code']").trigger("blur");
				}
			});
		}
		else{
			$("#commandsform input[name='code']").val("");
			$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('input[name="dateaddcommand"]').val())+"-");
			$("#commandsform input[name='code']").trigger("blur");
		}
	}

	if($(this).find("option:selected").attr("data-prefix") === "BRC"){
		$("#commandsform input[name='code']").css("padding-left","82px");
	}
	else{
		$("#commandsform input[name='code']").css("padding-left","72px");
	}
	
	if($(this).parent().parent().css("display") === "none"){
		$(".lx-code-label").css("top","30px");
	}
	else{
		$(".lx-code-label").css("top","30px");
	}
});

function loadProductsList(commandid,products,depot,company){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			commandid : commandid,
			products : products,
			depot : depot,
			company : company,
			action : 'loadproductslist'
		},
		success : function(response){
			$("#commandsform select[name='product']").html(response);
			htmtproductcommandform = $("#commandsform select[name='product']").clone().html();
			toDropDownTargeted("#commandsform select[name='product']");
			adjustQtys();
			// $("#commandsform select[name='depot']").trigger("change");
		}
	});		
}

function loadClientsList(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			action : 'loadclientslist'
		},
		success : function(response){
			$("#commandsform select[name='client']").html(response);
		}
	});		
}

$("body").delegate(".lx-open-edit-menu","click",function(){
	$(".lx-edit-menu").hide();
	$(this).next(".lx-edit-menu").show();
});

$("body").delegate(".lx-edit-menu a","click",function(){
	$(".lx-edit-menu").hide();
});

$(".lx-popup-content").delegate(".lx-open-edit-menu","click",function(){
	$(".lx-edit-menu").hide();
	$(this).next(".lx-edit-menu").show();
});

$(".lx-popup-content").delegate(".lx-edit-menu a","click",function(){
	$(".lx-edit-menu").hide();
});

$("body").delegate(".lx-edit-payment","click",function(){
	$(".lx-payment-state[data-paid='Partiellement payée']").css("display","inline-block");
	if($(this).attr("data-paid") === "Payée"){
		$(".lx-payment-state[data-paid='Partiellement payée']").css("display","none");
	}
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	tinyMCE.get("note1").setContent('');
	$("#paymentform select[name='modepayment']").val("").trigger("change");
	
	$("#paymentform input[name='currency']").val($(this).attr("data-currency"));

	$(".devis").css("display","none");
	if($(this).attr("data-currency") !== "DH" && $(this).attr("data-currency") !== "MAD" && $(this).attr("data-currency") !== ""){
		$(".devis").css("display","block");
	}

	$(".lx-devis").text($(this).attr("data-currency"));

	$("#paymentform input[name='paid']").val($(this).attr("data-paid"));
	$(".lx-payment-state").each(function(){
		$(this).find("i").remove();
		$(this).text($(this).text().trim());
	});	
	$(".lx-payment-company").text($(this).attr("data-company"));
	$("#paymentform input[name='company']").val($(this).attr("data-companyid"));
	$("#paymentform input[name='imputation']").val("");
	$("#paymentform select[name='imputation']").val("");
	$("#paymentform select[name='rib']").html($(this).attr("data-bankaccounts"));
	toDropDown();
	$("#paymentform input[name='id']").val($(this).attr("data-id"));
	$("#paymentform select[name='rib2']").html($(this).attr("data-supplierbankaccount"));
	$(".lx-payment-supplier").text($(this).attr("data-supplier"));
	$("#paymentform input[name='supplier']").val($(this).attr("data-supplierid"));
	$(".lx-accordion-content").html("").css("display","none");
	$(".lx-payment-state[data-paid='"+$(this).attr("data-paid")+"']").trigger("click");
	$("#paymentform input[name='rastva']").val($(this).attr("data-rastva"));

	if($("#paymentform .lx-table .lx-loading").length === 0){
		$("#paymentform .lx-table").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#paymentform input[name='id']").val(),
			type : $("#paymentform input[name='type']").val(),
			action : 'loadpayments'
		},
		success : function(response){
			$("#paymentform .lx-table .lx-loading").remove();
			$("#paymentform .lx-table").html(response);
		}
	});
	
	$("#paymentform input[name='cash']").val("").removeAttr("style").prop("readonly",false);
	$("#paymentform input[name='cash']").next("ins").remove();
	$("#paymentform input[name='resthidden']").val($(this).attr("data-rest"));
	$("#paymentform input[name='price']").val($(this).attr("data-price"));	
	$("#paymentform input[name='rest']").val(parseFloat($(this).attr("data-rest")).toFixed(2)+" "+$("#currency").val());

	$("#paymentform input[name='cash2']").val("").removeAttr("style").prop("readonly",false);
	$("#paymentform input[name='cash2']").next("ins").remove();
	$("#paymentform input[name='resthidden2']").val($(this).attr("data-rest2"));
	$("#paymentform input[name='price2']").val($(this).attr("data-price2"));
	$("#paymentform input[name='rest2']").val(parseFloat($(this).attr("data-rest2")).toFixed(2)+" "+$(this).attr("data-currency"));
});

$(".lx-popup-content").delegate("#paymentform input[name='cash']","keyup blur paste change",function(){
	$("#paymentform input[name='rest']").val(parseFloat($("#paymentform input[name='resthidden']").val() - $("#paymentform input[name='cash']").val()).toFixed(2) + " " + $("#currency").val());
});

$(".lx-popup-content").delegate("#paymentform input[name='cash2']","keyup blur paste change",function(){
	$("#paymentform input[name='rest2']").val(parseFloat($("#paymentform input[name='resthidden2']").val() - $("#paymentform input[name='cash2']").val()).toFixed(2) + " " + $("#paymentform input[name='currency']").val());
});

$(".lx-payment-state").on("click",function(){
	$("#paymentform input[name='paid']").val($(this).attr("data-paid"));
	$(".lx-payment-state i").remove();
	$(this).prepend('<i class="fa fa-check"></i> ');
	$(".lx-ras").html("");
	if($(this).attr("data-paid") === "Payée" || $(this).attr("data-paid") === "Remboursée"){
		$("#paymentform input[name='cash']").val($("#paymentform input[name='resthidden']").val());
		$("#paymentform input[name='rest']").val("0.00 "+$("#currency").val());

		$("#paymentform input[name='cash2']").val($("#paymentform input[name='resthidden2']").val());
		$("#paymentform input[name='rest2']").val("0.00 "+$("#paymentform input[name='currency']").val());
		if($(".lx-apply").length){
			if($(".lx-apply").css("display") === "none"){
				var rest = parseFloat($("#paymentform input[name='resthidden']").val());
				var rest2 = parseFloat($("#paymentform input[name='resthidden2']").val());
				var ras = parseFloat($("#ras").val());
				$("#paymentform input[name='cash']").val((rest-ras).toFixed(2));
				$(".lx-ras").html("("+$("#percentage").val()+"% de RAS: <b>-"+ras.toFixed(2)+" "+$("#currency").val()+"</b>)").css({"color":"#FF0000"});
				$(".lx-apply").css("display","none");
				$(".lx-cancel").css("display","inline-block");		
			}
			if($(".lx-cancel").css("display") === "none"){
				var rest = parseFloat($("#paymentform input[name='resthidden']").val());
				var rest2 = parseFloat($("#paymentform input[name='resthidden2']").val());
				$("#paymentform input[name='cash']").val((rest).toFixed(2));
				$("#paymentform input[name='cash2']").val((rest).toFixed(2));
				$(".lx-ras").html("");
				$(".lx-cancel").css("display","none");
				$(".lx-apply").css("display","inline-block");		
			}
		}
	}
	else if($(this).attr("data-paid") === "Non payée" || $(this).attr("data-paid") === "Non remboursée"){
		$("#paymentform input[name='cash']").val("0");
		$("#paymentform input[name='rest']").val(parseFloat($("#paymentform input[name='price']").val()).toFixed(2) + " " + $("#currency").val());

		$("#paymentform input[name='cash2']").val("0");
		$("#paymentform input[name='rest2']").val(parseFloat($("#paymentform input[name='price2']").val()).toFixed(2) + " " + $("#paymentform input[name='currency']").val());
	}
	else{
		$("#paymentform input[name='cash']").val("0").prop("readonly",false);
		$("#paymentform input[name='rest']").val(parseFloat($("#paymentform input[name='resthidden']").val()).toFixed(2) + " " + $("#currency").val());
		$("#paymentform input[name='cash']").trigger("keyup");

		$("#paymentform input[name='cash2']").val("0").prop("readonly",false);
		$("#paymentform input[name='rest2']").val(parseFloat($("#paymentform input[name='resthidden2']").val()).toFixed(2) + " " + $("#paymentform input[name='currency']").val());
		$("#paymentform input[name='cash2']").trigger("keyup");
	}
});

function parseFloatSafe(value) {
  return value && !isNaN(parseFloat(value)) ? parseFloat(value) : 0;
}

$("#paymentform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var k = 0;
	
	if($("#paymentform input[name='currency']").val() !== "DH" && $("#paymentform input[name='currency']").val() !== "MAD" && $("#paymentform input[name='currency']").val() !== ""){
		if($("#paymentform input[name='type']").val() === "facturesupplier"){
			if($("#paymentform select[name='rib2'] option:selected").attr("data-location") === "out"){
				isNotZero($("#paymentform input[name='cash2']"));
				if(!isNotZero($("#paymentform input[name='cash2']"))){
					k++;
				}				
			}
		}
	}
	
	if(k === 0){
		var rastva = $("#paymentform input[name='rastva']").val();
		isNotEmpty($("#paymentform select[name='modepayment']"));
		var x = 0
		if(parseFloat($("#paymentform input[name='rest']").val()) > 0){
			isNotZero($("#paymentform input[name='cash']"));
			if(!isNotZero($("#paymentform input[name='cash']"))){
				x++;
			}
		}
		if(parseFloat($("#paymentform input[name='rest2']").val()) > 0){
			isNotZero($("#paymentform input[name='cash2']"));
			if(!isNotZero($("#paymentform input[name='cash2']"))){
				x++;
			}
		}
		var h = 0;
		if($("#paymentform input[name='currency']").val() !== "DH" && $("#paymentform input[name='currency']").val() !== "MAD" && $("#paymentform input[name='currency']").val() !== ""){
			if(parseFloat($("#paymentform input[name='rest2']").val()) < 0 || parseFloatSafe($("#paymentform input[name='cash2']").val()) < 0){
				h++;	
			}
		}

		if(isNotEmpty($("#paymentform select[name='modepayment']")) && x === 0){	
			if(parseFloat($("#paymentform input[name='rest']").val()) >= 0 && parseFloatSafe($("#paymentform input[name='cash']").val()) >= 0 && h === 0){
				if($(this).attr("class") !== "lx-disabled"){
					$(this).attr("class","lx-disabled");
					if(parseFloat($("#paymentform input[name='rest']").val()) === 0){
						$("#paymentform input[name='paid']").val("Payée");
					}
					if($(this).find("i").length === 0){
						$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
					}
					var ajaxurl = "ajax.php";
					$.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							id : $("#paymentform input[name='id']").val(),
							paid : $("#paymentform input[name='paid']").val(),
							cash : $("#paymentform input[name='cash']").val(),
							price2 : $("#paymentform input[name='cash2']").length?$("#paymentform input[name='cash2']").val():0,
							currency : $("#paymentform input[name='currency']").length?$("#paymentform input[name='currency']").val():$("#currency").val(),
							rest : $("#paymentform input[name='rest']").val(),
							rest2 : $("#paymentform input[name='rest2']").val(),
							rastva : rastva,
							modepayment : $("#paymentform select[name='modepayment']").val(),
							datedue : $("#paymentform input[name='dateduecommand']").val(),
							datepaid : $("#paymentform input[name='datepaidcommand']").val(),
							imputation : $("#paymentform input[name='imputation']").val(),
							rib : $("#paymentform select[name='rib']").val(),
							rib2 : $("#paymentform select[name='rib2']").length?$("#paymentform select[name='rib2']").val():0,
							note : $("#paymentform textarea[name='note1']").val(),
							type : $("#paymentform input[name='type']").val(),
							category : $("#paymentform input[name='category']").val(),
							action : 'addpayment'
						},
						success : function(response){
							console.log(response);
							$("#paymentform input[name='cash']").val("").removeAttr("style");
							$("#paymentform input[name='cash']").next("ins").remove();
							$("#paymentform .lx-submit a").attr("class","");
							$("#paymentform .lx-submit a i").remove();
							$(".lx-popup-content > a > .material-icons").trigger("click");
							if($(".lx-pagination ul").attr("data-table") === "users"){
								loadUsers($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "suppliers"){
								loadSuppliers($(".lx-pagination ul").attr("data-state"));
							}	
							if($(".lx-pagination ul").attr("data-table") === "clients"){
								loadClients($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "devis"){
								loadDevis($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
								loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "bl"){
								loadBL($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "bs"){
								loadBS($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "br"){
								loadBR($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "factures"){
								loadFactures($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "avoirs"){
								loadAvoirs($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "bc"){
								loadBC($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
								loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "bre"){
								loadBRC($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "nh"){
								loadNH($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "nf"){
								loadNF($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "ph"){
								loadPH($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "caisse"){
								loadCaisse($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "companies"){
								loadCompanies($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "prospects"){
								loadProspects($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "labels"){
								loadLabels($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "doclabels"){
								loadDocLabels($(".lx-pagination ul").attr("data-state"));
							}
							if($(".lx-pagination ul").attr("data-table") === "messages"){
								loadMessages($(".lx-pagination ul").attr("data-state"));
							}
							$(".lx-floating-response").remove();
							window.clearTimeout(timer);
							$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
							$(".lx-floating-response").fadeIn();
							window.setTimeout(function(){
								$(".lx-floating-response").fadeOut();
							},5000);
							loadNotification();
						}
					});	
				}					
			}
			else{
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Le montant à payer ou le reste ne peut pas être négatif<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);		
			}
		}
		else{
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);		
		}
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$("#rastvaform .lx-submit a").on("click",function(){
	isNotEmpty($("#rastvaform select[name='stateras']"));
	if(isNotEmpty($("#rastvaform select[name='stateras']"))){		
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#rastvaform input[name='id']").val(),
					page : $("#rastvaform input[name='page']").val(),
					stateras : $("#rastvaform select[name='stateras']").val(),
					datepaidras : $("#rastvaform input[name='datepaidras']").val(),
					attachmentras : $("#rastvaform input[name='attachments']").val(),
					action : 'addpaymentras'
				},
				success : function(response){
					$("#rastvaform .lx-submit a").attr("class","");
					$("#rastvaform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					if($(".lx-pagination ul").attr("data-table") === "factures"){
						loadFactures($(".lx-pagination ul").attr("data-state"));
					}
					if($(".lx-pagination ul").attr("data-table") === "nh"){
						loadNH($(".lx-pagination ul").attr("data-state"));
					}
					if($(".lx-pagination ul").attr("data-table") === "nf"){
						loadNF($(".lx-pagination ul").attr("data-state"));
					}
					if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
						loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
					}
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
					loadNotification();
				}
			});	
		}					
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$("body").delegate(".lx-edit-payment-ras","click",function(){
	$(".lx-payment-state[data-paid='Partiellement payée']").css("display","inline-block");
	if($(this).attr("data-paid") === "Payée"){
		$(".lx-payment-state[data-paid='Partiellement payée']").css("display","none");
	}
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$("#rastvaform select[name='stateras']").val($(this).attr("data-stateras"));
	if($(this).attr("data-datepaidras") !== ""){
		$("#rastvaform input[name='datepaidras']").val($(this).attr("data-datepaidras"));
	}
	$("#rastvaform input[name='attachments']").val($(this).attr("data-attachmentras"));
	$(".fa-check").css("display","none");
	$(".lx-clear-attachment").css("display","none");
	if($(this).attr("data-attachmentras") !== ""){
		$(".fa-check").css("display","inline-block");
		$(".lx-clear-attachment").css("display","inline-block");
	}
	$("#rastvaform input[name='id']").val($(this).attr("data-id"));
});

$("#paymentform select[name='modepayment']").on("change",function(){
	tinyMCE.get("note1").setContent('');
	$(".lx-cheque").css("display","none");
	if($(this).val() == "Chèque" || $(this).val() == "Effet"){
		$(".lx-cheque").css("display","block");
		$("#paymentform input[name='dateduecommand']").val("");
		$("#paymentform input[name='datepaidcommand']").val("");
		if($(this).val() == "Chèque"){
			$("#paymentform input[name='dateduecommand']").val(getCurrentDate());
		}
	}
	if($(this).val() == "Virement"){
		$(".lx-supplier-bankaccount").css("display","block");
		toDropDown();
		$(".lx-company-account-ob").css("display","inline-block");
	}
	else{
		$(".lx-supplier-bankaccount").css("display","none");
		$(".lx-company-account-ob").css("display","none");
	}
});

$("#paymentform select[name='rib2']").on("change",function(){
	if($(this).find("option:selected").attr("data-location") === "out"){
		$(".lx-price-out").css("display","block");
	}
	else{
		$(".lx-price-out").css("display","none");
	}
});

$(".lx-popup-content").delegate(".lx-delete-payment","click",function(){
	$(this).hide();
	$(this).next(".lx-delete-payment-choice").toggle();
});

$(".lx-popup-content").delegate(".lx-no-delete-payment","click",function(){
	$(this).parent().prev("a").show();
	$(this).parent().toggle();
});

$(".lx-popup-content").delegate(".lx-yes-delete-payment","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletepaymenthistory'
		},
		success : function(response){
			$(".lx-popup-content > a > .material-icons").trigger("click");
			if($(".lx-pagination ul").attr("data-table") === "users"){
				loadUsers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
			}	
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				loadClients($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				loadFactures($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "companies"){
				loadCompanies($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "prospects"){
				loadProspects($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "labels"){
				loadLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "doclabels"){
				loadDocLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "messages"){
				loadMessages($(".lx-pagination ul").attr("data-state"));
			}
			loadNotification();
		}
	});
});

$(".lx-popup-content").delegate("#commandsform input[name='new']","click",function(){
	$("#commandsform input[name='exist']").val($(this).val()).trigger("keyup");
	$("#commandsform input[name='client']").val("").removeAttr("style");
	$("#commandsform select[name='client']").val("").next("ins").remove();
	$("#commandsform input[name='ice']").val("").removeAttr("style").next("ins").remove();
	$("#commandsform input[name='phone']").val("");
	$("#commandsform input[name='address']").val("").removeAttr("style").next("ins").remove();
	$("#commandsform input[name='email']").val("");
	isNotEmpty($("#commandsform input[name='exist']"));
	if($(this).val() === "0"){
		if($("#commandsform input[name='type']").val() === "bc" || $("#commandsform input[name='type']").val() === "bre"){
			$("#commandsform input[name='client']").attr("placeholder","Saisissez un fournisseur");
			$("#commandsform input[name='client']").parent().find("> span").html("Fournisseur<sup>*</sup>: ");
		}
		else{
			$("#commandsform input[name='client']").attr("placeholder","Saisissez un client");
			$("#commandsform input[name='client']").parent().find("> span").html("Client<sup>*</sup>: ");
		}
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").show();
	}
	else if($(this).val() === "1"){
		$("#commandsform select[name='company']").trigger("change");
		if($("#commandsform input[name='type']").val() === "bc" || $("#commandsform input[name='type']").val() === "bre"){
			$("#commandsform input[name='client']").parent().find("> span").html("Fournisseur<sup>*</sup>: ");
		}
		else{
			$("#commandsform input[name='client']").parent().find("> span").html("Client<sup>*</sup>: ");
		}
		$(".lx-client").show();
		toDropDownTargeted($("#commandsform select[name='client']"));
	}
	else if($(this).val() === "2"){
		$("#commandsform input[name='client']").attr("placeholder","Saisissez un prospect");
		$("#commandsform input[name='client']").parent().find("> span").html("Prospect<sup>*</sup>: ");
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").show();
	}
	else{
		if($("#commandsform input[name='type']").val() === "bc" || $("#commandsform input[name='type']").val() === "bre"){
			$("#commandsform input[name='client']").parent().find("> span").html("Fournisseur<sup>*</sup>: ");
		}
		else{
			$("#commandsform input[name='client']").parent().find("> span").html("Client<sup>*</sup>: ");
		}
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").hide();
	}
});

$(".lx-refresh-codes").on("click",function(){
	if($("#commandsform select[name='company']").val() !== ""){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				company : $("#commandsform select[name='company']").val(),
				type : $("#commandsform input[name='type']").val(),
				dateadd : $("#commandsform input[name='dateaddcommand']").val(),
				category : $("#commandsform input[name='categories']").val(),
				action : 'loadcode'
			},
			success : function(response){
				console.log(response);
				$("#commandsform input[name='code']").val(response);
			}
		});		
	}
	else{
		$("#commandsform input[name='code']").val("");
	}
});

$("#commandsform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var k = 0;
	$(".lx-save-this").trigger("click");
	if(k === 0){
		var products = "";
		var abovenotes = "";
		var belownotes = "";
		var units = "";
		var qtys = "";
		var uprices = "";
		var tprices = "";
		var discounts = "";
		var discounttypes = "";
		var tvas = "";
		var qtyback = "";
		var h = 0;
		$(".lx-list-products table td").each(function(){
			var bg = $(this).css('background-color');

			if (bg === 'rgb(255, 230, 230)') {
				h++;
			}
		});			
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			products += "||"+$(this).find(".lx-product-title").html();
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			abovenotes += "||"+$(this).attr("data-abovenote");
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			belownotes += "||"+$(this).attr("data-belownote");
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			units += "||"+$(this).attr("data-unit");
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			qtys += "||"+$(this).attr("data-qty");
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			uprices += "||"+$(this).attr("data-uprice");
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			tprices += "||"+$(this).attr("data-ttprice");
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			if($("#commandsform input[name='id']").val() === "-1" && $("#commandsform select[name='typediscount']").val() !== "%" && $("#commandsform select[name='typediscount']").val() !== ""){
				discounts += "||"+$(this).attr("data-remise");
			}
			else{
				discounts += "||"+$(this).find("input[name='discount']").val();
			}
			discounttypes += "||"+$("#commandsform select[name='typediscount']").val();
		});
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			tvas += "||"+$(this).attr("data-tva");
		});
		var qtyfine = 0;
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			qtyback += "||"+$(this).find("input[name='qtyback']").val();
			if(parseFloat($(this).find("input[name='qtyback']").val()) > 0){
				qtyfine++;
			}
		});
		
		if($("#commandsform input[name='cash']").val() !== "" && $("#commandsform input[name='cash']").val() !== "0"){
			isNotEmpty($("#commandsform select[name='caisse']"));
		}
		isNotEmpty($("#commandsform input[name='exist']"));
		var client = "";
		if($("#commandsform input[name='exist']").val() === "0"){
			$("#commandsform input[name='client']").attr("data-isnotempty","");
			$("#commandsform input[name='client']").attr("data-message","Saisissez un client");
			if(!isNotEmpty($("#commandsform input[name='client']"))){
				client = "1";
			}
		}
		if($("#commandsform input[name='exist']").val() === "1"){
			$("#commandsform input[name='client']").attr("data-isnotempty","");
			$("#commandsform input[name='client']").attr("data-message","Choisissez un client");
			if(!isNotEmpty($("#commandsform select[name='client']"))){
				$("#commandsform input[name='client']").css("border","1px solid rgb(214, 50, 50)");
				client = "1";
			}
			else{
				$("#commandsform input[name='client']").removeAttr("style");
			}
		}

		var v = 0;
		if($("#commandsform input[name='cash']").val() !== "" && $("#commandsform input[name='cash']").val() !== "0"){
			if(!isNotEmpty($("#commandsform select[name='modepayment']"))){
				v++;
			}
		}
		else{
			$("#commandsform select[name='modepayment']").removeAttr("style");
			$("#commandsform select[name='modepayment']").parent().find("ins").remove();
		}
		
		isNotEmpty($("#commandsform select[name='company']"));
		isNumber($("#commandsform input[name='code']"));
		if(isNotEmpty($("#commandsform select[name='company']"))
			&& isNumber($("#commandsform input[name='code']"))
			&& isNotEmpty($("#commandsform input[name='exist']"))
			&& v === 0
			&& products !== "" 
			&& client === ""){
			if(parseFloat($("#commandsform input[name='price']").val()) >= 0){
				if(h === 0){
					if($(this).attr("class") !== "lx-disabled"){
						var z = 0;
						if($("#commandsform input[name='type']").val() === "facture" && $("#commandsform input[name='ice']").val() === ""){
							if(confirm('Voulez vous vraiment générer une facture sans ICE?')){
								z = 0;
							} 
							else{
								z = 1;
							}
						}
						if(z === 0){
							$(this).attr("class","lx-disabled");
							if($(this).find("i").length === 0){
								$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
							}
							var fullname = $("#commandsform input[name='client']").val();
							if(typeof $("#commandsform select[name='client'] option:selected").attr("data-name") !== typeof undefined && $("#commandsform select[name='client'] option:selected").attr("data-name") !== false){
								fullname = $("#commandsform select[name='client'] option:selected").attr("data-name");
							}		
							var ajaxurl = "ajax.php";
							$.ajax({
								url : ajaxurl,
								type : 'post',
								data : {
									id : $("#commandsform input[name='id']").val(),
									type : $("#commandsform input[name='type']").val(),
									category : $("#commandsform input[name='category']").val(),
									prefix : $("#commandsform input[name='prefix']").val(),
									code : $("#commandsform input[name='code']").val(),
									dateadd : $("#commandsform input[name='dateaddcommand']").val(),
									company : $("#commandsform select[name='company']").val(),
									correctdoc : ($("#correctdoc").length?$("#correctdoc").val():""),
									exist : $("#commandsform input[name='exist']").val(),
									fullname : fullname,
									client : $("#commandsform select[name='client']").val(),
									typeclient : $("#commandsform select[name='client'] option:selected").attr("data-type"),
									ice : $("#commandsform input[name='ice']").val(),
									phone : $("#commandsform input[name='phone']").val(),
									address : $("#commandsform input[name='address']").val(),
									email : $("#commandsform input[name='email']").val(),
									products : products,
									abovenotes : abovenotes,
									belownotes : belownotes,
									units : units,
									qtys : qtys,
									uprices : uprices,
									tprices : tprices,
									tvas : tvas,
									discounts : discounts,
									qtyback : qtyback,
									discounttypes : discounttypes,
									maindiscount : $("#commandsform input[name='maindiscount']").val(),
									modepayment : $("#commandsform select[name='modepayment']").val(),
									cash : $("#commandsform input[name='cash']").val(),
									rest : $("#commandsform input[name='rest']").val(),
									rib : $("#commandsform select[name='rib']").val(),
									imputation : $("#commandsform input[name='imputation']").val(),
									note : $("#commandsform textarea[name='note']").val(),
									modepayment1 : $("#commandsform select[name='modepayment']").val(),
									modepayment : $("#commandsform input[type='hidden'][name='modepayment']").val(),
									conditions : $("#commandsform input[name='conditions']").val(),
									abovetable : $("#commandsform input[name='abovetable']").val(),
									transform : $("#commandsform input[name='typeaction']").val(),
									action : 'addcommand'
								},
								success : function(response){
									if(response !== ""){
										$("#commandsform .lx-submit a").attr("class","");
										$("#commandsform .lx-submit a i").remove();
										$(".lx-floating-response").remove();
										window.clearTimeout(timer);
										$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
										$(".lx-floating-response").fadeIn();
										window.setTimeout(function(){
											$(".lx-floating-response").fadeOut();
										},5000);							
									}
									else{
										$("#commandsform .lx-submit a").attr("class","");
										$("#commandsform .lx-submit a i").remove();
										$(".lx-popup-content > a > .material-icons").trigger("click");
										if($("#commandsform input[name='id']").val() === "0" || $("#commandsform input[name='id']").val() === "-1"){
											if($("#commandsform input[name='type']").val() === "facture"){
												window.location.href = "factures.php";
											}
											else if($("#commandsform input[name='type']").val() === "nh"){
												window.location.href = "nh.php";
											}
											else if($("#commandsform input[name='type']").val() === "nf"){
												window.location.href = "nf.php";
											}
											else if($("#commandsform input[name='type']").val() === "nf"){
												window.location.href = "nf.php";
											}
											else if($("#commandsform input[name='type']").val() === "devis"){
												window.location.href = "devis.php";
											}
											else if($("#commandsform input[name='type']").val() === "ph"){
												window.location.href = "ph.php";
											}
											else if($("#commandsform input[name='type']").val() === "factureproforma"){
												window.location.href = "facturesproforma.php";
											}
											else if($("#commandsform input[name='type']").val() === "bl"){
												window.location.href = "bl.php";
											}
											else if($("#commandsform input[name='type']").val() === "bs"){
												window.location.href = "bs.php";
											}
											else if($("#commandsform input[name='type']").val() === "bc"){
												window.location.href = "bc.php";
											}
											else if($("#commandsform input[name='type']").val() === "bre"){
												window.location.href = "bre.php";
											}
											if($("#commandsform input[name='type']").val() === "avoir"){
												window.location.href = "avoirs.php";
											}
											if($("#commandsform input[name='type']").val() === "br"){
												window.location.href = "br.php";
											}
										}
										else{
											if($(".lx-pagination ul").attr("data-table") === "devis"){
												loadDevis($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
												loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "bl"){
												loadBL($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "bs"){
												loadBS($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "br"){
												loadBR($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "factures"){
												loadFactures($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "avoirs"){
												loadAvoirs($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "bc"){
												loadBC($(".lx-pagination ul").attr("data-state"));
												reloadSupplierList();
											}
											if($(".lx-pagination ul").attr("data-table") === "bre"){
												loadBRC($(".lx-pagination ul").attr("data-state"));
												reloadSupplierList();
											}
											if($(".lx-pagination ul").attr("data-table") === "nh"){
												loadNH($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "nf"){
												loadNF($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}
											if($(".lx-pagination ul").attr("data-table") === "ph"){
												loadPH($(".lx-pagination ul").attr("data-state"));
												reloadClientList();
											}	
										}
										$(".lx-floating-response").remove();
										window.clearTimeout(timer);
										$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
										$(".lx-floating-response").fadeIn();
										window.setTimeout(function(){
											$(".lx-floating-response").fadeOut();
										},5000);
									}
								}
							});
						}
					}
				}
				else{
					$("#commandsform .lx-submit a i").remove();
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez corriger les cellules en rouge<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
				}
			}
			else{
				$("#commandsform .lx-submit a i").remove();
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Le montant total doit être supérieur ou égale à 0<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);				
			}
		}
		else if(products === ""){
			$(".lx-error-products").html("<p style='padding:10px;background:#FDAFBF;font-style:italic;text-align:center;border:1px solid #FF0000;'>Veuillez ajouter au moins un produit / service</p>");
		}
		else{
			$("#commandsform .lx-submit a i").remove();
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	}
});

$("body").delegate(".lx-add-tags","click",function(){
	$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	$(".lx-labels a i").remove();
	var labels = $(this).attr("data-labels").split(",");
	$(".lx-labels a").each(function(){
		for(var i=0;i<labels.length;i++){
			if($(this).attr("data-label") === labels[i]){
				$(this).prepend('<i class="fa fa-check"></i>');
			}
		}
	});
	$("#addtagsform input[name='id']").val($(this).attr("data-id"));
});

$("#addtagsform .lx-submit a").on("click",function(){
	var labels = "";
	$("#addtagsform .lx-labels a").each(function(){
		if($(this).find("i").length){
			labels += ","+$(this).attr("data-label");
		}
	});
	if($(this).attr("class") !== "lx-disabled"){
		$(this).attr("class","lx-disabled");
		if($(this).find("i").length === 0){
			$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
		}
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				id : $("#addtagsform input[name='id']").val(),
				table : $("#addtagsform input[name='table']").val(),
				labels : labels,
				action : 'addtags'
			},
			success : function(response){
				console.log(response);
				$("#addtagsform .lx-submit a").attr("class","");
				$("#addtagsform .lx-submit a i").remove();
				$(".lx-popup-content > a > .material-icons").trigger("click");
				if($(".lx-pagination ul").attr("data-table") === "devis"){
					loadDevis($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
					loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
					loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bl"){
					loadBL($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bs"){
					loadBS($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "br"){
					loadBR($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "factures"){
					loadFactures($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "avoirs"){
					loadAvoirs($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bc"){
					loadBC($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bre"){
					loadBRC($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "nh"){
					loadNH($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "nf"){
					loadNF($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "ph"){
					loadPH($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "prospects"){
					loadProspects($(".lx-pagination ul").attr("data-state"));
				}
				$(".lx-floating-response").remove();
				window.clearTimeout(timer);
				$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
				$(".lx-floating-response").fadeIn();
				window.setTimeout(function(){
					$(".lx-floating-response").fadeOut();
				},5000);
			}
		});
	}
});

$("#commandsform select[name='client']").on("change",function(){
	addClientToOrder();	
});

$("body").delegate(".lx-check-delivery-state","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'checkdeliverystate'
		},
		success : function(response){
			if(response === "1"){
				$(".lx-return-command[data-id='"+id+"']").addClass("lx-open-popup");
				$(".lx-return-command[data-id='"+id+"']").attr("data-state","Livrée");
				$(".lx-return-command[data-id='"+id+"']").attr("data-title","returncommand");
				$(".lx-return-command[data-id='"+id+"']").trigger("click");
			}
			else{
				$(".lx-return-command[data-id='"+id+"']").removeClass("lx-open-popup");
				$(".lx-return-command[data-id='"+id+"']").attr("data-state","Nouvelle");
				$(".lx-return-command[data-id='"+id+"']").attr("data-title","");
				$(".lx-return-command[data-id='"+id+"']").trigger("click");
			}
		}
	});
});

$("body").delegate(".lx-return-command","click",function(){
	if($(this).attr("data-state") === "Livrée"){
		$("."+$(this).attr("data-title")+" .lx-form-title h3 span").text($(this).attr("data-header"));
		$(".lx-list-products-return").html($(this).attr("data-products"));
		var i = 0;
		$(".lx-list-products-return .lx-table table").each(function(){
			if($(this).find("tr").length === 1){
				$(this).prev("h2").remove();
				$(this).remove();
			}
			else{
				i = 1;
			}
		});
		if(i === 1){
			$("#returncommandsform .lx-submit a").css("display","inline-block")
		}
		else{
			$(".lx-list-products-return").html('<p><center><i>Facture retournée en totalité</i></center></p>');
			$("#returncommandsform .lx-submit a").css("display","none")		
		}
		$("#returncommandsform input[name='id']").val($(this).attr("data-id"));
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		if($(".lx-pagination ul").attr("data-table") === "commands"){
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Vous ne pouvez pas retourné une commande non livrée<i class="material-icons"></i></p></div>');
		}
		if($(".lx-pagination ul").attr("data-table") === "factures"){
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Vous ne pouvez pas faire un bon de retour sur une facture qui contient des commandes non livrées<i class="material-icons"></i></p></div>');
		}
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$("#returncommandsform .lx-submit a").on("click",function(){
	var allcommands = "";
	var allproducts = "";
	var alldepots = "";
	var allqtys = "";
	var alldiscounts = "";
	var allbprices = "";
	var allprices = "";
	var errorqty = 0;
	$(".lx-list-products-return table tr[data-product]").each(function(){
		if($(this).find("input[name='discount']").val() !== "" && $(this).find("input[name='discount']").val() !== "0"){
			if(parseFloat($(this).find("input[name='discount']").val()) <= parseFloat($(this).attr("data-qty"))){
				$(this).find("input[name='discount']").removeAttr("style");
				allcommands += ","+$(this).attr("data-command");
				allproducts += ","+$(this).attr("data-product");
				alldepots += ","+$(this).attr("data-depot");
				allqtys += ",-"+$(this).find("input[name='discount']").val();
				alldiscounts += ","+(parseFloat($(this).attr("data-discount"))*$(this).find("input[name='discount']").val());
				allbprices += ","+$(this).attr("data-bprice");
				allprices += ","+$(this).attr("data-price");
			}
			else{
				$(this).find("input[name='discount']").css("border","1px solid #FF0000");
				errorqty = 1;
			}
		}
	});
			
	$("#returncommandsform input[name='allcommands']").val(allcommands);
	$("#returncommandsform input[name='allproducts']").val(allproducts);
	$("#returncommandsform input[name='alldepots']").val(alldepots);
	$("#returncommandsform input[name='allqtys']").val(allqtys);
	$("#returncommandsform input[name='alldiscounts']").val(alldiscounts);
	$("#returncommandsform input[name='allbprices']").val(allbprices);
	$("#returncommandsform input[name='allprices']").val(allprices);

	if(errorqty === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#returncommandsform input[name='id']").val(),
					allcommands : $("#returncommandsform input[name='allcommands']").val(),
					allproducts : $("#returncommandsform input[name='allproducts']").val(),
					alldepots : $("#returncommandsform input[name='alldepots']").val(),
					allqtys : $("#returncommandsform input[name='allqtys']").val(),
					alldiscounts : $("#returncommandsform input[name='alldiscounts']").val(),
					allbprices : $("#returncommandsform input[name='allbprices']").val(),
					allprices : $("#returncommandsform input[name='allprices']").val(),
					action : 'returncommand'
				},
				success : function(response){
					$("#returncommandsform .lx-submit a").attr("class","");
					$("#returncommandsform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadFactures("1");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
					loadNotification();
				}
			});
		}
	}
	else{
		$("#returncommandsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez verifier les quanitité<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$("#returnnoncommandsform .lx-submit a").on("click",function(){
	var allcommands = "";
	var allproducts = "";
	var alldepots = "";
	var allqtys = "";
	var alldiscounts = "";
	var allbprices = "";
	var allprices = "";
	var errorqty = 0;
	$(".lx-list-products-return table tr[data-product]").each(function(){
		if($(this).find("input[name='discount']").val() !== "" && $(this).find("input[name='discount']").val() !== "0"){
			if(parseFloat($(this).find("input[name='discount']").val()) <= parseFloat($(this).attr("data-qty"))){
				$(this).find("input[name='discount']").removeAttr("style");
				allcommands += ","+$(this).attr("data-command");
				allproducts += ","+$(this).attr("data-product");
				alldepots += ","+$(this).attr("data-depot");
				allqtys += ",-"+$(this).find("input[name='discount']").val();
				alldiscounts += ","+(parseFloat($(this).attr("data-discount"))*$(this).find("input[name='discount']").val());
				allbprices += ","+$(this).attr("data-bprice");
				allprices += ","+$(this).attr("data-price");
			}
			else{
				$(this).find("input[name='discount']").css("border","1px solid #FF0000");
				errorqty = 1;
			}
		}
	});
			
	$("#returnnoncommandsform input[name='allcommands']").val(allcommands);
	$("#returnnoncommandsform input[name='allproducts']").val(allproducts);
	$("#returnnoncommandsform input[name='alldepots']").val(alldepots);
	$("#returnnoncommandsform input[name='allqtys']").val(allqtys);
	$("#returnnoncommandsform input[name='alldiscounts']").val(alldiscounts);
	$("#returnnoncommandsform input[name='allbprices']").val(allbprices);
	$("#returnnoncommandsform input[name='allprices']").val(allprices);

	if(errorqty === 0){
		if($(this).attr("class") !== "lx-disabled"){
			$(this).attr("class","lx-disabled");
			if($(this).find("i").length === 0){
				$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
			}
			var ajaxurl = "ajax.php";
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : {
					id : $("#returnnoncommandsform input[name='id']").val(),
					allcommands : $("#returnnoncommandsform input[name='allcommands']").val(),
					allproducts : $("#returnnoncommandsform input[name='allproducts']").val(),
					alldepots : $("#returnnoncommandsform input[name='alldepots']").val(),
					allqtys : $("#returnnoncommandsform input[name='allqtys']").val(),
					alldiscounts : $("#returnnoncommandsform input[name='alldiscounts']").val(),
					allbprices : $("#returnnoncommandsform input[name='allbprices']").val(),
					allprices : $("#returnnoncommandsform input[name='allprices']").val(),
					action : 'returnnoncommand'
				},
				success : function(response){
					$("#returnnoncommandsform .lx-submit a").attr("class","");
					$("#returnnoncommandsform .lx-submit a i").remove();
					$(".lx-popup-content > a > .material-icons").trigger("click");
					loadCommands("1");
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);
					loadNotification();
				}
			});
		}
	}
	else{
		$("#returnnoncommandsform .lx-submit a i").remove();
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez verifier les quanitité<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$(".lx-coli-state-delivarymen").on("click",function(){
	$("#editstateform input[name='state']").val($(this).attr("data-state"));
	$(".lx-coli-state-delivarymen i").remove();
	$(this).prepend('<i class="fa fa-check"></i> ');
});

$("body").delegate(".lx-edit-state","click",function(){
	$("#editstateform input[name='state']").val($(this).attr("data-state"));
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	var state = $(this).attr("data-state");
	$(".lx-coli-state-delivarymen").each(function(){
		$(this).find("i").remove();
		$(this).text($(this).text().trim());
		if($(this).text() === state){
			$(this).prepend('<i class="fa fa-check"></i> ');
		}
	});
	$("#editstateform textarea[name='note']").val($(this).attr("data-note"));
	tinyMCE.get("note2").setContent($(this).attr("data-note"));
	$("#editstateform input[name='invoiced']").val($(this).attr("data-invoiced"));
	$("#editstateform input[name='maybe']").val($(this).attr("data-maybe"));
	$("#editstateform input[name='id']").val($(this).attr("data-id"));
});

$("#editstateform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	if($("#editstateform input[name='invoiced']").val() === "Peut-être" && $("#editstateform input[name='state']").val() === "Retourné"){
		alert("Ces quantités vont être ajoutés aux stocks dont l'état de facturation est: "+$("#editstateform input[name='maybe']").val());	
	}
	if($(this).find("i").length === 0){
		$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#editstateform input[name='id']").val(),
			state : $("#editstateform input[name='state']").val(),
			note : $("#editstateform textarea[name='note']").val(),
			action : 'editstate'
		},
		success : function(response){
			$("#editstateform .lx-submit a i").remove();
			$(".lx-popup-content > a > .material-icons").trigger("click");
			loadCommands("1");
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	});
});

$("body").delegate(".lx-trash-command","click",function(){
	filterClicked = "yes";
	loadCommands("0");
	$(".lx-pagination ul").attr("data-state","0");
});

$("body").delegate(".lx-published-command","click",function(){
	filterClicked = "yes";
	loadCommands("1");
	$(".lx-pagination ul").attr("data-state","1");
});

$("body").delegate(".lx-delete-command","click",function(){
	$(".updatecaisse input[name='price']").val($(this).attr("data-price"));
	$(".updatecaisse input[name='command']").val($(this).attr("data-id"));
	$(".updatecaisse input[name='type']").val($(this).attr("data-type"));
	$(".updatecaisse input[name='company']").val($(this).attr("data-company"));
	$(".updatecaisse input[name='project']").val($(this).attr("data-project"));
	$(".updatecaisse input[name='libelle']").val($(this).attr("data-libelle"));
	$(".updatecaisse input[name='nature']").val($(this).attr("data-nature"));
	$(".updatecaisse input[name='invoiced']").val($(this).attr("data-invoiced"));
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-delete-devis","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-delete-facturesproforma","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("body").delegate(".lx-restore-command","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'restorecommand'
		},
		success : function(response){
			loadCommands("0");
		}
	});
});

$("body").delegate(".lx-delete-permanently-command","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'deletecommandpermanently'
		},
		success : function(response){
			loadCommands("0");
		}
	});
});

$("input[name='searchadvanced']").on("keyup",function(){
	var el = $(this);
	if(el.attr("data-table") !== "clients" && el.attr("data-table") !== "suppliers" && el.attr("data-table") !== "products" && el.attr("data-table") !== "prospects"){
		var valeur = new RegExp($(this).val(),'i');
		$(this).next("ul").find("li").each(function(){
			if($(this).find("label.lx-selectall").length === 0){
				if(valeur.test($(this).text())){
					$(this).css("display","block");
				}
				else{
					$(this).css("display","none");
				}
			}
		});
	}
	else{
		if(el.next("ul").find(".lx-loading").length === 0){
			el.next("ul").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
		}
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				table : el.attr("data-table"),
				value : el.attr("data-value"),
				ids : el.attr("data-ids"),
				text : el.attr("data-text"),
				keyword : el.val(),
				action : 'advancedsearch'
			},
			success : function(response){
				el.next("ul").find(".lx-loading").remove();
				el.next("ul").html(response);
				var ids = el.parent().prev("input").attr("data-ids").split("||");
				for(var i=0;i<ids.length;i++){
					el.next("ul").find("input[data-ids='"+ids[i]+"']").prop("checked",true);
				}
			}
		});
	}
});

$(".lx-popup-content").delegate("input[name='searchadvanced']","keyup",function(){
	var val = $(this).val();
	var valeur = new RegExp(val,'i');
	$(this).next("ul").find("li").each(function(){
		if($(this).find("label.lx-selectall").length === 0){
			if(valeur.test($(this).text())){
				$(this).css("display","block");
			}
			else{
				$(this).css("display","none");
			}
		}
	});
});

$(".lx-advanced-select > input").on("click",function(){
	$(".lx-advanced-select div").not($(this).next("div")).css({"display":"none"});
	$(this).next("div").toggle();
});

$(".lx-advanced-select > div").on("click",function(){
	var el = $(this);
	window.setTimeout(function(){
		el.css("display","block");
	},1);
});

function removeItem(value, str, sep) {
  return str
    .split(sep)                     // split by separator
    .filter(item => item !== value) // remove the target value
    .join(sep);                     // join back with separator
}

function customTrim(str, chars) {
  if (!chars) return str.trim(); // fallback to normal trim
  
  let pattern = new RegExp(
    `^(${chars})+|(${chars})+$`,
    'g'
  );
  return str.replace(pattern, '');
}

$("body").delegate(".lx-advanced-select > div label","click",function(){
    var $parent = $(this).parents(".lx-advanced-select");
    var $input  = $parent.find("> input[type='text']");
    var val = $input.val() || "";
    var ids = $input.attr("data-ids") || "";

    var thisVal = $(this).find("input").val();
    var thisId  = $(this).find("input").attr("data-ids");

    if ($(this).find("input").prop("checked") === true) {
        // ✅ Add only if not empty
        val = val ? val + "," + thisVal : thisVal;
        ids = ids ? ids + "||" + thisId : thisId;
    } else {
        // ✅ Remove item
        val = removeItem(thisVal, val, ',');
        ids = removeItem(thisId, ids, '||');
    }

    // ✅ Trim separators safely
    val = customTrim(val, ',');
    ids = customTrim(ids, '||');

    $input.val(val);
    $input.attr("title",val);
    $input.attr("data-ids", ids);
});

$(".lx-popup-content").delegate(".lx-advanced-select > div label","click",function(){
    var $parent = $(this).parents(".lx-advanced-select");
    var $input  = $parent.find("> input[type='text']");
    var val = $input.val() || "";
    var ids = $input.attr("data-ids") || "";

    var thisVal = $(this).find("input").val();
    var thisId  = $(this).find("input").attr("data-ids");

    if ($(this).find("input").prop("checked") === true) {
        // ✅ Add only if not empty
        val = val ? val + "," + thisVal : thisVal;
        ids = ids ? ids + "||" + thisId : thisId;
    } else {
        // ✅ Remove item
        val = removeItem(thisVal, val, ',');
        ids = removeItem(thisId, ids, '||');
    }

    // ✅ Trim separators safely
    val = customTrim(val, ',');
    ids = customTrim(ids, '||');

    $input.val(val);
    $input.attr("title",val);
    $input.attr("data-ids", ids);
});

/*$(".lx-popup-content").delegate(".lx-single-select div ul li","click",function(){
	$(this).parents(".lx-advanced-select").find("> input[type='text']").val($(this).text()).trigger("keyup");
	$(this).parent().parent().removeAttr("style");
});*/

$(".lx-advanced-select > div label.lx-selectall").on("click",function(){
	var val = "";
	var ids = "";
	var el = $(this).find("input");
	$(this).parents(".lx-advanced-select").find("input[type='checkbox']").not("input[name='selectall']").each(function(){
		if($(this).parent().parent().css("display") !== "none"){
			$(this).prop("checked",el.prop("checked"));
			if($(this).prop("checked") === true){
				val += "," + $(this).val();
				ids += "||" + $(this).attr("data-ids");
			}
		}
	});
	$(this).parents(".lx-advanced-select").find("> input[type='text']").val(val.substring(1));
	$(this).parents(".lx-advanced-select").find("> input[type='text']").attr("data-ids",ids.substring(2));
});

$(".lx-state-filter").on("click",function(){
	if($("#kpi").length){
		loadKPI();
				var val = $("#typedoc option:selected").attr("data-type");
				$(".lx-tags-index").each(function(){
					if(val === $(this).attr("data-type")){
						$(this).css("display","block");
					}
					else{
						$(this).css("display","none");
					}
				});
	}
	if($("#documents").length){
		loadKPI1();
	}
	if($("#ca").length){
		loadCA();
	}
	if($("#topca").length){
		loadTop();
	}
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}	
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
});

$(".lx-state-empty").on("click",function(){
	$(this).parents(".lx-advanced-select").find("input[type='checkbox']").each(function(){
		$(this).prop("checked",false);
	});
	$(this).parents(".lx-advanced-select").find("> input[type='text']").val("");
	$(this).parents(".lx-advanced-select").find("> input[type='text']").attr("data-ids","");
	$(this).parent().find("> input[type='text']").val("").trigger("keyup");
	if($("#kpi").length){
		loadKPI();
				var val = $("#typedoc option:selected").attr("data-type");
				$(".lx-tags-index").each(function(){
					if(val === $(this).attr("data-type")){
						$(this).css("display","block");
					}
					else{
						$(this).css("display","none");
					}
				});
	}
	if($("#documents").length){
		loadKPI1();
	}
	if($("#ca").length){
		loadCA();
	}
	if($("#topca").length){
		loadTop();
	}
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}	
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
});

function loadCommands(state,neworder){
	if($(".lx-table-commands .lx-loading").length === 0){
		$(".lx-table-commands").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			project : $("#project").attr("data-ids"),
			worker : $("#worker").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			family : $("#family").attr("data-ids"),
			source : $("#source").val(),
			statee : $("#states").val(),
			invoiced : $("#invoiced").attr("data-ids"),
			paid : $("#paid").val(),
			modepayment : $("#modepayment").val(),
			avoir : $("#avoir").val(),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadcommands'
		},
		success : function(response){
			$(".lx-table-commands .lx-loading").remove();
			$(".lx-table-commands").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");				
			if(neworder === "1"){
				$(".lx-table-commands table tr:eq(1) td input[type='checkbox']").prop("checked",true);
				window.setTimeout(function(){
					if($(".lx-table-commands table tr:eq(1) td input[type='checkbox']").prop("checked") === true){
						$(".lx-generate-invoice").trigger("click");
					}					
				},500);
			}
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-edit-extrainfo","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	tinyMCE.get("modepayment").setContent($(this).attr("data-modepayment"));
	tinyMCE.get("conditions").setContent($(this).attr("data-conditions"));
	tinyMCE.get("abovetable").setContent($(this).attr("data-abovetable"));
	$("#extrainfoform input[name='id']").val($(this).attr("data-id"));
});

$("#extrainfoform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#extrainfoform input[name='id']").val(),
			modepayment : $("#extrainfoform textarea[name='modepayment']").val(),
			conditions : $("#extrainfoform textarea[name='conditions']").val(),
			abovetable : $("#extrainfoform textarea[name='abovetable']").val(),
			action : 'editextrainfo'
		},
		success : function(response){
			$("#extrainfoform .lx-submit a i").remove();
			$(".lx-popup-content > a > .material-icons").trigger("click");
			if($(".lx-pagination ul").attr("data-table") === "users"){
				loadUsers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
			}	
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				loadClients($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				loadFactures($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "companies"){
				loadCompanies($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "prospects"){
				loadProspects($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "labels"){
				loadLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "doclabels"){
				loadDocLabels($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "messages"){
				loadMessages($(".lx-pagination ul").attr("data-state"));
			}
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Note enregistré<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	});
});

$("body").delegate(".lx-edit-extrainfo","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	tinyMCE.get("modepayment").setContent($(this).attr("data-modepayment"));
	tinyMCE.get("conditions").setContent($(this).attr("data-conditions"));
	tinyMCE.get("abovetable").setContent($(this).attr("data-abovetable"));
	$("#extrainfofpform input[name='id']").val($(this).attr("data-id"));
});

$("body").delegate(".lx-edit-facture","click",function(){
	$(".lx-textfield label > ins").remove();
	$(".lx-add-form .lx-textfield label > input").removeAttr("style");
	$(".lx-add-form .lx-textfield label > select").removeAttr("style");$(".lx-add-form .lx-textfield label > textarea").removeAttr("style");
	tinyMCE.get("modepayment").setContent($(this).attr("data-modepayment"));
	tinyMCE.get("conditions").setContent($(this).attr("data-conditions"));
	tinyMCE.get("abovetable").setContent($(this).attr("data-abovetable"));
	$("#factureform input[name='id']").val($(this).attr("data-id"));
});

$("#factureform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#factureform input[name='id']").val(),
			modepayment : $("#factureform textarea[name='modepayment']").val(),
			conditions : $("#factureform textarea[name='conditions']").val(),
			abovetable : $("#factureform textarea[name='abovetable']").val(),
			action : 'editfacture'
		},
		success : function(response){
			$("#factureform .lx-submit a i").remove();
			$(".lx-popup-content > a > .material-icons").trigger("click");
			loadFactures("1");
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Note enregistré<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	});
});

$("body").delegate(".lx-delete-facture","click",function(){
	$(".lx-delete-record").attr("data-id",$(this).attr("data-id"));
});

$("#avoir").on("click",function(){
	loadFactures($(".lx-pagination ul").attr("data-state"));
});

$("body").delegate('.lx-open-edit-menu','click',function() {
    var container = $('.lx-table');
    container.animate({
        scrollTop: container.prop("scrollHeight")
    }, 1000);  // Adjust the speed (in milliseconds) if needed
});

function loadFactures(state){
	var avoir = 0;
	if($("#avoir").prop("checked") === true){
		var avoir = 1;
	}
	if($(".lx-table-factures .lx-loading").length === 0){
		$(".lx-table-factures").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			tags : $("#tags").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			statee : $("#state").attr("data-ids"),
			stateras : $("#stateras").attr("data-ids"),
			avoir : avoir,
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartlimit : $(".lx-keyword #datestartlimit").val(),
			dateendlimit : $(".lx-keyword #dateendlimit").val(),
			datestartlimitras : $(".lx-keyword #datestartlimitras").val(),
			dateendlimitras : $(".lx-keyword #dateendlimitras").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadfactures'
		},
		success : function(response){
			$(".lx-table-factures .lx-loading").remove();
			$(".lx-table-factures").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('facture');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
			loadNotification();	
		}
	});
}

function loadNH(state){
	var avoir = 0;
	if($("#avoir").prop("checked") === true){
		var avoir = 1;
	}
	if($(".lx-table-nh .lx-loading").length === 0){
		$(".lx-table-nh").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			tags : $("#tags").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			statee : $("#state").attr("data-ids"),
			stateras : $("#stateras").attr("data-ids"),
			avoir : avoir,
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartlimit : $(".lx-keyword #datestartlimit").val(),
			dateendlimit : $(".lx-keyword #dateendlimit").val(),
			datestartlimitras : $(".lx-keyword #datestartlimitras").val(),
			dateendlimitras : $(".lx-keyword #dateendlimitras").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadnh'
		},
		success : function(response){
			$(".lx-table-nh .lx-loading").remove();
			$(".lx-table-nh").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('nh');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadNF(state){
	var avoir = 0;
	if($("#avoir").prop("checked") === true){
		var avoir = 1;
	}
	if($(".lx-table-nf .lx-loading").length === 0){
		$(".lx-table-nf").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			statee : $("#state").attr("data-ids"),
			stateras : $("#stateras").attr("data-ids"),
			avoir : avoir,
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartlimit : $(".lx-keyword #datestartlimit").val(),
			dateendlimit : $(".lx-keyword #dateendlimit").val(),
			datestartlimitras : $(".lx-keyword #datestartlimitras").val(),
			dateendlimitras : $(".lx-keyword #dateendlimitras").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadnf'
		},
		success : function(response){
			$(".lx-table-nf .lx-loading").remove();
			$(".lx-table-nf").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('nf');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadPH(state){
	if($(".lx-table-ph .lx-loading").length === 0){
		$(".lx-table-ph").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			prospect : $("#prospect").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			statee : $("#state").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadph'
		},
		success : function(response){
			$(".lx-table-ph .lx-loading").remove();
			$(".lx-table-ph").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('ph');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadAvoirs(state){
	if($(".lx-table-avoirs .lx-loading").length === 0){
		$(".lx-table-avoirs").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			statee : $("#state").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadavoirs'
		},
		success : function(response){
			$(".lx-table-avoirs .lx-loading").remove();
			$(".lx-table-avoirs").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('avoir');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadBR(state){
	if($(".lx-table-br .lx-loading").length === 0){
		$(".lx-table-br").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadbr'
		},
		success : function(response){
			$(".lx-table-br .lx-loading").remove();
			$(".lx-table-br").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('br');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadDevis(state){
	if($(".lx-table-devis .lx-loading").length === 0){
		$(".lx-table-devis").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			prospect : $("#prospect").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loaddevis'
		},
		success : function(response){
			$(".lx-table-devis .lx-loading").remove();
			$(".lx-table-devis").html(response);
			loadPriceRange('devis');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadFacturesProforma(state){
	if($(".lx-table-facturesproforma .lx-loading").length === 0){
		$(".lx-table-facturesproforma").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadfacturesproforma'
		},
		success : function(response){
			$(".lx-table-facturesproforma .lx-loading").remove();
			$(".lx-table-facturesproforma").html(response);
			loadPriceRange('factureproforma');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadBL(state){
	if($(".lx-table-bl .lx-loading").length === 0){
		$(".lx-table-bl").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadbl'
		},
		success : function(response){
			$(".lx-table-bl .lx-loading").remove();
			$(".lx-table-bl").html(response);
			loadPriceRange('bl');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadBS(state){
	if($(".lx-table-bs .lx-loading").length === 0){
		$(".lx-table-bs").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadbs'
		},
		success : function(response){
			$(".lx-table-bs .lx-loading").remove();
			$(".lx-table-bs").html(response);
			loadPriceRange('bs');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadBC(state){
	if($(".lx-table-bc .lx-loading").length === 0){
		$(".lx-table-bc").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadbc'
		},
		success : function(response){
			$(".lx-table-bc .lx-loading").remove();
			$(".lx-table-bc").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('bc');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadBRC(state){
	if($(".lx-table-bre .lx-loading").length === 0){
		$(".lx-table-bre").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			state : state,
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			user : $("#user").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			product : $("#product").attr("data-ids"),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			tags : $("#tags").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			datestartrecall : $(".lx-keyword #datestartrecall").val(),
			dateendrecall : $(".lx-keyword #dateendrecall").val(),	
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadbre'
		},
		success : function(response){
			$(".lx-table-bre .lx-loading").remove();
			$(".lx-table-bre").html(response);
			$(".lx-caisse-total-1").html($(".lx-caisse-total1").html()).attr("style",$(".lx-caisse-total1").attr("style"));
			$(".lx-caisse-total-2").html($(".lx-caisse-total2").html()).attr("style",$(".lx-caisse-total2").attr("style"));
			$(".lx-caisse-total-4").html($(".lx-caisse-total3").html()).attr("style",$(".lx-caisse-total3").attr("style"));
			$(".lx-caisse-total1").html("");
			$(".lx-caisse-total2").html("");
			$(".lx-caisse-total3").html("");
			loadPriceRange('bre');
			reloadProspectsRecalls();
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

$("body").delegate(".lx-delete-file","click",function(){
	$(this).next(".lx-delete-file-choice").toggle();
});

$("body").delegate(".lx-no-delete-file","click",function(){
	$(this).parent().toggle();
});

$("body").delegate(".lx-yes-delete-file","click",function(){
	var table = $(this).attr("data-table");
	var files = $(this).attr("data-files");
	var file = $(this).attr("data-file");
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			table : table,
			files : files,
			file : file,
			id : id,
			action : 'deletefiles'
		},
		success : function(response){
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				loadFactures($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				loadClients($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
		}		
	});	
});

$("body").delegate(".lx-detail-command","click",function(){
	if($(".details .lx-add-form .lx-loading").length === 0){
		$(".details .lx-add-form").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'detailscommand'
		},
		success : function(response){
			$(".details .lx-add-form .lx-loading").remove();
			$(".details .lx-add-form").html(response);
		}
	});
});

$(".lx-generate-invoice").on("click",function(){
	var ice = 0;
	var previce = "";
	var company = 0;
	var prevcompany = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			if($(this).attr("data-ice") !== previce && previce !== ""){
				ice += 1;
			}
			previce = $(this).attr("data-ice");	
			if($(this).attr("data-company") !== prevcompany && prevcompany !== ""){
				company += 1;
			}
			prevcompany = $(this).attr("data-company");				
		}
	});
	var ids = "";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			ids += "," + $(this).val();
		}
	});
	ids = ids.substring(1);
	if(ids !== ""){
		if(ice === 0 && company === 0){
			var k = 0;
			if(confirm('Voulez vous générer une facture?')){
				k = 0;
			} 
			else{
				k = 1;
			}
			if(k===0){
				if($(".lx-table-commands .lx-loading").length === 0){
					$(".lx-table-commands").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
				}
				var ajaxurl = "ajax.php";
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						ids : ids,
						action : 'generateinvoice'
					},
					success : function(response){
						$(".lx-table-commands .lx-loading").remove();
						loadCommands($(".lx-pagination ul").attr("data-state"));
						$(".lx-floating-response").remove();
						window.clearTimeout(timer);
						$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien généré<i class="material-icons"></i></p></div>');
						$(".lx-floating-response").fadeIn();
						window.setTimeout(function(){
							$(".lx-floating-response").fadeOut();
						},5000);
					}
				});
			}
		}
		else{
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Vous ne pouvez pas créer une seule facture de plusieurs sociétés ou pour plusieurs clients<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);			
		}
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez selectionner des commandes<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$("body").delegate(".lx-show-history","click",function(){
	var id = $(this).attr("data-id");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : 'showcommandhistory'
		},
		success : function(response){
			$(".commandhistory .lx-add-form").html(response);
			
		}
	});	
});

function loadJCaisse(){
	if($(".lx-table-jcaisse .lx-loading").length === 0){
		$(".lx-table-jcaisse").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			keyword : $("#keyword").val(),
			company : $("#company").attr("data-ids"),
			project : $("#project").attr("data-ids"),
			worker : $("#worker").attr("data-ids"),
			depot : $("#depot").attr("data-ids"),
			nature : $("#nature").val(),
			type : $("#type").val(),
			invoiced : $("#invoiced").val(),
			modepayment : $("#modepayment").val(),
			pricemin : $("#pricemin").val(),
			pricemax : $("#pricemax").val(),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			dateduestart : $(".lx-keyword #dateduestart").val(),
			datedueend : $(".lx-keyword #datedueend").val(),	
			datepaidstart : $(".lx-keyword #datepaidstart").val(),
			datepaidend : $(".lx-keyword #datepaidend").val(),	
			paid : ($("#paid").prop("checked") === true?"1":"0"),
			start : $(".lx-pagination ul").attr("data-start"),
			nbpage : $(".lx-pagination ul").attr("data-nbpage"),
			sortby : $(".lx-keyword input[name='sortby']").val(),
			orderby : $(".lx-keyword input[name='orderby']").val(),
			action : 'loadjcaisse'
		},
		success : function(response){
			$(".lx-table-jcaisse .lx-loading").remove();
			$(".lx-table-jcaisse").html(response);
			onScroll();
			if(filterClicked === 'yes'){
				initPagination();
			}
			if($("input[name='orderby']").val() === "DESC"){
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-down");
			}
			else{
				$(".lx-first-tr i[data-sort='"+$("input[name='sortby']").val()+"']").attr("class","fa fa-sort-up");
			}
		}
	});
}

function loadKPI(){
	if($("#kpi .lx-loading").length === 0){
		$("#kpi").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $("#company").attr("data-ids"),	
			client : $("#client").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			invoiced : $("#invoiced").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			action : 'loadkpi'
		},
		success : function(response){
			$("#kpi .lx-loading").remove();
			$("#kpi").html(response);
		}
	});
}

function loadKPI1(){
	if($("#documents .lx-loading").length === 0){
		$("#documents").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $("#company").attr("data-ids"),	
			client : $("#client").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			invoiced : $("#invoiced").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			action : 'loaddocuments'
		},
		success : function(response){
			$("#documents .lx-loading").remove();
			$("#documents").html(response);
		}
	});
}

$(".lx-table-ca ul li a").on("click",function(){
	$(".lx-table-ca ul li a").removeClass("active");
	$(this).addClass("active");
	$("#period").val($(this).attr("data-period"));
	loadCA();
});

$("#typedoc").on("change",function(){
	var val = $(this).find("option:selected").attr("data-type");
	$(".lx-tags-index input").prop("checked",false);
	$("#tags").val("").attr("data-ids","");
	$(".lx-tags-index").each(function(){
		if(val === $(this).attr("data-type")){
			$(this).css("display","block");
		}
		else{
			$(this).css("display","none");
		}
	});
});

function loadCA(){
	if($("#ca .lx-loading").length === 0){
		$("#ca").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $("#company").attr("data-ids"),	
			typedoc : $("#typedoc").val(),
			tags : $("#tags").attr("data-ids"),
			client : $("#client").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			product : $("#product").attr("data-ids"),	
			period : $("#period").val(),	
			invoiced : $("#invoiced").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			action : 'loadca'
		},
		success : function(response){
			var data = response.split("|");
			chartdates = data[0].split(",");
			chartnbdocuments = data[1].split(",").map(Number);
			chartvalue = data[2].split(",").map(Number);
			loadCAChart();
			$("#ca .lx-loading").remove();
		}
	});
}

function loadCAChart(){
	Highcharts.setOptions({
		colors: ['#39add1','#7EC855'],
	});
	Highcharts.chart('ca', {
		chart: {
			type: 'spline',
			height: 300,
			backgroundColor: null
		},
		title: {
			text: ''
		},
		yAxis: {
			title: {
				text: ''
			},
			alternateGridColor: '#FBFBFB'
		},
		xAxis: {
			categories: chartdates,
		},
		series: [{
			showInLegend: true,
			name: 'Nb. doc.',
			data: chartnbdocuments,
            tooltip: {
                valueSuffix: ''
            }
		},{
			showInLegend: true,
			name: 'Valeur',
			data: chartvalue,
            tooltip: {
                valueSuffix: ' '+$("#currency").val()
            }
		}],
		credits: {
			 enabled: false
		},
		responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'bottom'
					}
				}
			}]
		}
	});	
}

var typedoc2 = $("#typedoc2").clone().html();
$("#topwho").on("change",function(){
	$("#typedoc2").html(typedoc2);
	var who = $(this).val();
	$("#typedoc2 option[data-type]").each(function(){
		if(!$(this).attr("data-type").split(",").includes(who)){
			$(this).remove();
		}
	});
});

var topname;
var topvalue;
var topvalue2;

function loadTop(){
	if($("#topca .lx-loading").length === 0){
		$("#topca").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $("#company").attr("data-ids"),	
			typedoc : $("#typedoc2").val(),
			client : $("#client").attr("data-ids"),
			supplier : $("#supplier").attr("data-ids"),
			product : $("#product2").attr("data-ids"),	
			topwho : $("#topwho").val(),	
			invoiced : $("#invoiced").attr("data-ids"),
			datestart : $(".lx-keyword #datestart").val(),
			dateend : $(".lx-keyword #dateend").val(),	
			action : 'loadtop'
		},
		success : function(response){
			var data = response.split("|");
			topname = data[0].split(",");
			topvalue = data[1].split(",").map(Number);
			topvalue2 = data[2].split(",").map(Number);
			loadTop10();
			$("#topca .lx-loading").remove();
		}
	});
}

function loadTop10(){
	Highcharts.setOptions({
		colors: ['#39add1'],
	});
	Highcharts.chart('topca', {
		chart: {
			type: 'column',
			height: 300,
			backgroundColor: null
		},
		title: {
			text: ''
		},
		yAxis: {
			title: {
				text: ''
			},
			alternateGridColor: '#FBFBFB'
		},
		xAxis: {
			categories: topname,
		},
		series: [{
				showInLegend: true,
				name: "Valeurs",
				data: topvalue2,
				tooltip: {
					valueSuffix: ' '+$("#currency").val()
				}
			}
		],
		credits: {
			 enabled: false
		},
		responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'bottom'
					}
				}
			}]
		}
	});	
}

var clientpaid;
var clientencours;
var supplierpaid;
var supplierencours;

function loadPaid(){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			invoiced : $("#client").val(),	
			datestart : $(".lx-keyword #datestart2").val(),
			dateend : $(".lx-keyword #dateend2").val(),	
			action : 'loadpaid'
		},
		success : function(response){
			var data = response.split("|").map(Number);
			clientpaid = parseFloat(data[0]);
			clientencours = parseFloat(data[1]);
			supplierpaid = parseFloat(data[2]);
			supplierencours = parseFloat(data[3]);
			loadPaidPie();
		}
	});
}

function loadPaidPie(){
	Highcharts.chart('paidclient', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: ''
		},
		tooltip: {
			pointFormat: '<b>{point.y} '+$("#currency").val()+'</b>'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.y} '+$("#currency").val(),
				}
			}
		},
		credits: {
			 enabled: false
		},
		series: [{
			name: 'Valeur',
			colorByPoint: true,
			data: [{
				name: 'Payée',
				y: clientpaid
			},  {
				name: 'En cours',
				y: clientencours
			}]
		}]
	});	
	
	Highcharts.chart('paidsupplier', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: ''
		},
		tooltip: {
			pointFormat: '<b>{point.y} '+$("#currency").val()+'</b>'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.y} '+$("#currency").val(),
				}
			}
		},
		credits: {
			 enabled: false
		},
		series: [{
			name: 'Valeur',
			colorByPoint: true,
			data: [{
				name: 'Payée',
				y: supplierpaid
			},  {
				name: 'En cours',
				y: supplierencours
			}]
		}]
	});	
}

$("body").delegate(".lx-on-off","click",function(){
	if($(this).attr("data-state") !== "off"){
		$(this).removeClass("lx-on-off-blue");
		$(this).attr("data-state","off");
		changeState($(this).attr("data-table"),$(this).attr("data-column"),$(this).attr("data-id"),"off");
	}
	else{
		$(this).addClass("lx-on-off-blue");
		$(this).attr("data-state","on");
		changeState($(this).attr("data-table"),$(this).attr("data-column"),$(this).attr("data-id"),"on");
		if($(".lx-pagination ul").attr("data-table") === "projects"){
			$(".lx-edit-dateend").trigger("click");
			$("#endprojectform input[name='id']").val($(this).attr("data-id"));
		}
	}
});

function changeState(table,column,id,state){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			table : table,
			column : column,
			id : id,
			state : state,
			action : 'changestate'
		},
		success : function(response){
			if($(".lx-pagination ul").attr("data-table") === "projects"){
				loadProjects($(".lx-pagination ul").attr("data-state"));
			}
		}
	});
}

$(".lx-cancel-delete").on("click",function(){
	$(".lx-popup-content > a > .material-icons").trigger("click");
});

$(".lx-delete-record").on("click",function(){
	var id = $(this).attr("data-id");
	var ids = $(this).attr("data-ids");
	var action = $(this).attr("data-action");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			action : action
		},
		success : function(response){
			console.log(response);
			$(".lx-popup-content > a > .material-icons").trigger("click");
			if($(".lx-pagination ul").attr("data-table") === "users"){
				loadUsers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "suppliers"){
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}
				else{
					loadSuppliers($(".lx-pagination ul").attr("data-state"));
				}
			}		
			if($(".lx-pagination ul").attr("data-table") === "clients"){
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}
				else{
					loadClients($(".lx-pagination ul").attr("data-state"));
				}
			}
			if($(".lx-pagination ul").attr("data-table") === "devis"){
				loadDevis($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "tvas" || $(".lx-pagination ul").attr("data-table") === "currencys"){
				window.location.href = window.location.href;
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
				loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bl"){
				loadBL($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bs"){
				loadBS($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "br"){
				loadBR($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "factures"){
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}
				else{
					loadFactures($(".lx-pagination ul").attr("data-state"));
				}
			}
			if($(".lx-pagination ul").attr("data-table") === "avoirs"){
				loadAvoirs($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bc"){
				loadBC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
				loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "bre"){
				loadBRC($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nh"){
				loadNH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "nf"){
				loadNF($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "ph"){
				loadPH($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "caisse"){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
			}
			if($(".lx-pagination ul").attr("data-table") === "companies"){
				loadCompanies($(".lx-pagination ul").attr("data-state"));
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}	
			}
			if($(".lx-pagination ul").attr("data-table") === "prospects"){
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}
				else{
					loadProspects($(".lx-pagination ul").attr("data-state"));
				}
			}	
			if($(".lx-pagination ul").attr("data-table") === "labels"){
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}
				else{
					loadLabels($(".lx-pagination ul").attr("data-state"));
				}
			}
			if($(".lx-pagination ul").attr("data-table") === "doclabels"){
				if(response !== ""){
					$(".lx-floating-response").remove();
					window.clearTimeout(timer);
					$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
					$(".lx-floating-response").fadeIn();
					window.setTimeout(function(){
						$(".lx-floating-response").fadeOut();
					},5000);				
				}
				else{
					loadDocLabels($(".lx-pagination ul").attr("data-state"));
				}
			}
			if($(".lx-pagination ul").attr("data-table") === "messages"){
				loadMessages($(".lx-pagination ul").attr("data-state"));
			}
		}
	});
});

$("body").delegate(".lx-first-tr i","click",function(){
	filterClicked = "yes";
	$(".lx-keyword input[name='sortby']").val($(this).attr("data-sort"));
	if($(".lx-keyword input[name='orderby']").val() === "ASC"){
		$(".lx-keyword input[name='orderby']").val("DESC");
	}
	else{
		$(".lx-keyword input[name='orderby']").val("ASC");
	}
	initPagination();
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}	
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
});

$(".lx-search-keyword").on("click",function(){
	filterClicked = "yes";
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}	
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
});

$("#topwhat,#topwho").on("change",function(){
	loadTop();
});

$("#worker,#paid,#modepayment,#type,#invoiced,#typedoc,#typedoc1,#typedoc2,#company,#supplier,#product,#product1,#client,#imputation,#rib").on("change",function(){
	filterClicked = "yes";
	if($("#ca").length){
		loadCA();
	}
	if($("#topca").length){
		loadTop();
	}
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}	
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
});

function initPagination(){
	filterClicked = 'no';
	if(parseFloat($("#posts").val()) > parseFloat($(".lx-pagination ul").attr("data-nbpage"))){
		$(".lx-pagination").css("display","block");
		$(".lx-pagination ul").attr("data-start",0);
		$(".lx-pagination ul").attr("data-posts",$("#posts").val());
		$(".lx-pagination ul li ins").text("1");
		var nbpage = Math.ceil(parseFloat($("#posts").val()) / parseFloat($(".lx-pagination ul").attr("data-nbpage")))
		$(".lx-pagination ul li abbr").text(nbpage);
		$(".lx-pagination ul li .next").removeClass("disabled");
		$(".lx-pagination ul li .previous").removeClass("disabled");
		$(".lx-pagination ul li .previous").addClass("disabled");
		$("#pgnumber option").remove();
		for(var i=0;i<nbpage;i++){
			$("#pgnumber").append('<option value="'+i+'">'+(i+1)+'</option>');
		}
	}
	else{
		$(".lx-pagination").css("display","none");
	}
}

$(".lx-pagination ul li .next").on("click",function(){
	filterClicked = 'no';
	var start = parseFloat($(".lx-pagination ul").attr("data-start"));
	var nbpage = parseFloat($(".lx-pagination ul").attr("data-nbpage"));
	var posts = parseFloat($(".lx-pagination ul").attr("data-posts"));
	if((start + nbpage) < posts){
		if((start + (nbpage * 2)) >= posts){
			$(this).addClass("disabled");
		}
		$(".lx-pagination ul li .previous").removeClass("disabled");
		$(".lx-pagination ul").attr("data-start",(start+nbpage));
		$(".lx-pagination ul li span ins").text((start+(nbpage*2))/nbpage);
		$(".lx-pagination ul li #pgnumber").val(parseFloat($(".lx-pagination ul li span ins").text()) - 1);
		if($(".lx-pagination ul").attr("data-table") === "users"){
			loadUsers($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "suppliers"){
			loadSuppliers($(".lx-pagination ul").attr("data-state"));
		}	
		if($(".lx-pagination ul").attr("data-table") === "clients"){
			loadClients($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "devis"){
			loadDevis($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
			loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bl"){
			loadBL($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bs"){
			loadBS($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "br"){
			loadBR($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "factures"){
			loadFactures($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "avoirs"){
			loadAvoirs($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bc"){
			loadBC($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
			loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bre"){
			loadBRC($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "nh"){
			loadNH($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "nf"){
			loadNF($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "ph"){
			loadPH($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "caisse"){
			loadCaisse($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "companies"){
			loadCompanies($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "prospects"){
			loadProspects($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "labels"){
			loadLabels($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "doclabels"){
			loadDocLabels($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "messages"){
			loadMessages($(".lx-pagination ul").attr("data-state"));
		}
		$('html, body').animate({
			scrollTop: $(".lx-main-content").offset().top
		}, 1000);
	}
});

$(".lx-pagination ul li .previous").on("click",function(){
	filterClicked = 'no';
	var start = parseFloat($(".lx-pagination ul").attr("data-start"));
	var nbpage = parseFloat($(".lx-pagination ul").attr("data-nbpage"));
	var posts = parseFloat($(".lx-pagination ul").attr("data-posts"));
	if(start > 0){
		if((start - nbpage) === 0){
			$(this).addClass("disabled");
		}
		$(".lx-pagination ul li .next").removeClass("disabled");
		$(".lx-pagination ul").attr("data-start",(start-nbpage));
		$(".lx-pagination ul li span ins").text(start/nbpage);
		$(".lx-pagination ul li #pgnumber").val(parseFloat($(".lx-pagination ul li span ins").text()) - 1);
		if($(".lx-pagination ul").attr("data-table") === "users"){
			loadUsers($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "suppliers"){
			loadSuppliers($(".lx-pagination ul").attr("data-state"));
		}	
		if($(".lx-pagination ul").attr("data-table") === "clients"){
			loadClients($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "devis"){
			loadDevis($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
			loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bl"){
			loadBL($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bs"){
			loadBS($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "br"){
			loadBR($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "factures"){
			loadFactures($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "avoirs"){
			loadAvoirs($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bc"){
			loadBC($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
			loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bre"){
			loadBRC($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "nh"){
			loadNH($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "nf"){
			loadNF($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "ph"){
			loadPH($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "caisse"){
			loadCaisse($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "companies"){
			loadCompanies($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "prospects"){
			loadProspects($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "labels"){
			loadLabels($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "doclabels"){
			loadDocLabels($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "messages"){
			loadMessages($(".lx-pagination ul").attr("data-state"));
		}
		$('html, body').animate({
			scrollTop: $(".lx-main-content").offset().top
		}, 1000);
	}
});

$("#pgnumber").on("change",function(){
	filterClicked = 'no';
	$(".lx-pagination ul").attr("data-start",parseFloat($(".lx-pagination ul").attr("data-nbpage")) * $(this).val())
	$(".lx-pagination ul li span ins").text(parseFloat($(this).val()) + 1);
	if((parseFloat($(this).val()) + 1) >= (parseFloat($(".lx-pagination ul").attr("data-posts")) / parseFloat($(".lx-pagination ul").attr("data-nbpage")))){
		$(".lx-pagination ul li .previous").removeClass("disabled");
		$(".lx-pagination ul li .next").addClass("disabled");
	}
	else if(parseFloat($(this).val() + 1) === 1 ){
		$(".lx-pagination ul li .previous").addClass("disabled");
		$(".lx-pagination ul li .next").removeClass("disabled");
	}
	else{
		$(".lx-pagination ul li .previous").removeClass("disabled");
		$(".lx-pagination ul li .next").removeClass("disabled");
	}
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}	
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
	$('html, body').animate({
		scrollTop: $(".lx-main-content").offset().top
	}, 1000);
});

$(".lx-action-bulk a").on("click",function(){
	var ids = "0";
	$(".lx-main .lx-table input[type='checkbox']:checked").each(function(){
		if($(this).val() !== "selectall"){
			ids += "," + $(this).val();
		}
	});
	var table = $(".lx-pagination ul").attr("data-table");
	if($(".lx-pagination ul").attr("data-table") === "worker" || $(".lx-pagination ul").attr("data-table") === "dlm" || $(".lx-pagination ul").attr("data-table") === "subdlm"){
		table = "users";
	}
	if($(".lx-pagination ul").attr("data-table") === "confirmation"){
		table = "commands";
	}
	var column = "id";
	if(ids !== "0" && $(".lx-action-bulk select[name='statebulk']").val() !== ""){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				ids : ids,
				table : table,
				column : column,
				state : $(".lx-action-bulk select[name='statebulk']").val(),
				action : 'updatebulk'
			},
			success : function(response){
				if($(".lx-pagination ul").attr("data-table") === "users"){
					loadUsers($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "suppliers"){
					loadSuppliers($(".lx-pagination ul").attr("data-state"));
				}	
				if($(".lx-pagination ul").attr("data-table") === "clients"){
					loadClients($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "devis"){
					loadDevis($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
					loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bl"){
					loadBL($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bs"){
					loadBS($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "br"){
					loadBR($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "factures"){
					loadFactures($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "avoirs"){
					loadAvoirs($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bc"){
					loadBC($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
					loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "bre"){
					loadBRC($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "nh"){
					loadNH($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "nf"){
					loadNF($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "ph"){
					loadPH($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "caisse"){
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "companies"){
					loadCompanies($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "prospects"){
					loadProspects($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "labels"){
					loadLabels($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "doclabels"){
					loadDocLabels($(".lx-pagination ul").attr("data-state"));
				}
				if($(".lx-pagination ul").attr("data-table") === "messages"){
					loadMessages($(".lx-pagination ul").attr("data-state"));
				}
			}
		});
	}
});

$(".lx-action-bulk select[name='nbrows']").on("change",function(){
	filterClicked = 'yes';
	$(".lx-pagination ul").attr("data-nbpage",$(this).val());
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			nbrows : $(".lx-pagination ul").attr("data-nbpage"),
			action : 'editnbrows'
		},
		success : function(response){}
	});
	if($(".lx-pagination ul").attr("data-table") === "users"){
		loadUsers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "suppliers"){
		loadSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "clients"){
		loadClients($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "devis"){
		loadDevis($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
		loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bl"){
		loadBL($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bs"){
		loadBS($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "br"){
		loadBR($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "factures"){
		loadFactures($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "avoirs"){
		loadAvoirs($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bc"){
		loadBC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
		loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "bre"){
		loadBRC($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nh"){
		loadNH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "nf"){
		loadNF($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "ph"){
		loadPH($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "caisse"){
		loadCaisse($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "companies"){
		loadCompanies($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "prospects"){
		loadProspects($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "labels"){
		loadLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "doclabels"){
		loadDocLabels($(".lx-pagination ul").attr("data-state"));
	}
	if($(".lx-pagination ul").attr("data-table") === "messages"){
		loadMessages($(".lx-pagination ul").attr("data-state"));
	}
});

var mover = "no";
$(document).mousemove(function(){
	if($(".lx-autocomplete:hover").length != 0){
		mover = "yes";
	} 
	else{
		mover = "no";
	}
	if($(".lx-autocomplete-advanced:hover").length != 0){
		mover = "yes";
	} 
	else{
		mover = "no";
	}
});

$("body").mouseup(function (e){
	if($(".lx-autocomplete").length){
		var container = $(".lx-autocomplete");
		var inside = $(".lx-autocomplete *");
		if (!inside.is(e.target) && mover === "no"){
			container.hide();
		}
	}
    $(".lx-autocomplete-advanced").each(function () {
        var container = $(this);
        var input = container.prev("input"); // assuming input is always just before
        var inside = container.find("*");

        // if click is outside both input and container, hide this container
        if (!inside.is(e.target) && !container.is(e.target) && !input.is(e.target) && mover === "no") {
            container.hide();
        }
    });
	if($(".lx-advanced-select").length){
		var elToHide = $(".lx-advanced-select > div");
		var elPreventHide = $(".lx-advanced-select *");
		if (!elPreventHide.is(e.target)){
			elToHide.hide();
		}
	}
	if($(".lx-edit-menu").length){
		var elToHide = $(".lx-edit-menu");
		var elPreventHide = $(".lx-edit-menu *");
		if (!elPreventHide.is(e.target)){
			elToHide.hide();
		}
	}
	if($(".lx-notifications-list").length){
		var elToHide = $(".lx-notifications-list");
		var elPreventHide = $(".lx-notifications-list *");
		if (!elPreventHide.is(e.target)){
			elToHide.hide();
		}
	}	
	if($(".lx-account-settings").length){
		var elToHide = $(".lx-account-settings");
		var elPreventHide = $(".lx-account-settings *");
		if (!elPreventHide.is(e.target)){
			elToHide.hide();
		}
	}	
});

function stripTagsAndTrim(value) {
    if (value == null) return '';          // handle null/undefined
    value = String(value);                 // ensure it's a string

    // Remove HTML tags
    let stripped = value.replace(/<[^>]*>/g, '');

    // Replace HTML entities like &nbsp; with space
    stripped = stripped.replace(/&nbsp;/gi, '');

    // Remove all non-visible characters (newlines, tabs, multiple spaces)
    stripped = stripped.replace(/\s+/g, '');

    // Trim leading/trailing spaces
    return stripped.trim();
}

function isNotEmpty(el){
	var val = stripTagsAndTrim(el.val());

	if(typeof el.attr("data-id") !== typeof undefined && el.attr("data-id") !== false){
		val = el.attr("data-id");
	}
	if(val === "" || val === null){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
		}
		return false;
	}
	else{
		el.parent().find("> ins").remove();
		el.removeAttr("style");
		return true;
	}
}

$("*[data-isnotempty]").on("keyup blur paste change",function(){
	var val = stripTagsAndTrim($(this).val());
	if(val !== "" && val !== null && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

$(".lx-popup-content").delegate("*[data-isnotempty]","keyup blur paste change",function(){
	if($(this).val() !== "" && $(this).val() !== null && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

$("body").delegate("*[data-isnotempty]","keyup blur paste change",function(){
	if($(this).val() !== "" && $(this).val() !== null && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

function isNotZero(el){
	var val = el.val();
	if(typeof el.attr("data-id") !== typeof undefined && el.attr("data-id") !== false){
		val = el.attr("data-id");
	}
	if(val === "" || val === "0" || val === null){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
		}
		return false;
	}
	else{
		el.removeAttr("style");
		return true;
	}
}

$("*[data-isnotzero]").on("keyup blur paste change",function(){
	if($(this).val() !== "" && $(this).val() !== "0" && $(this).val() !== null && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

function isNumber(el){
	var regex = /^[0-9,\.]+$/;
	var k = 0;
	if(typeof el.attr("data-notzero") !== typeof undefined && el.attr("data-notzero") !== false){
		k = 1;
	}
	if(k === 1 && el.val() === "0"){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
			el.css("display","block");
		}
		return false;
	}
	else if(!regex.test(el.val())){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
			el.css("display","block");
		}
		return false;
	}
	else{
		el.removeAttr("style");
		return true;
	}
}

$("*[data-isnumber]").on("keyup blur paste change",function(){
	var regex = /^[0-9,\.]+$/;
	if(regex.test($(this).val()) && $(this).val() !== "0" && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});
$(".lx-popup-content").delegate("*[data-isnumber]","keyup blur paste change",function(){
	var regex = /^[0-9,\.]+$/;
	if(regex.test($(this).val()) && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});
$("body").delegate("*[data-isnumber]","keyup blur paste change",function(){
	var regex = /^[0-9,\.]+$/;
	if(regex.test($(this).val()) && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

$("body, .lx-popup-content").delegate("*[data-isnumber]", "keypress keyup blur", function(event) {
    var regex = /\./;
    var charCode = event.which ? event.which : event.keyCode;

    // Allow only digits and a single dot
    if (charCode !== 8 && charCode !== 46 && (charCode < 48 || charCode > 57)) {
        event.preventDefault();
    }

    if (charCode === 46 && regex.test($(this).val())) {
        event.preventDefault(); // prevent second dot
    }
});

// Handle paste separately
$("body, .lx-popup-content").delegate("*[data-isnumber]", "paste", function(e) {
    var pastedData = e.originalEvent.clipboardData.getData('text');
    if (!/^\d*\.?\d*$/.test(pastedData)) {
        e.preventDefault(); // block invalid pasted value
    }
});

function isPhone(el){
	var regex = /^([0-9 \(\)\-\+\.]){1,22}$/;
	if(!regex.test(el.val())){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
		}
		return false;
	}
	else{
		el.removeAttr("style");
		return true;
	}
}

$("*[data-isphone]").on("keyup blur paste change",function(){
	var regex = /^([0-9 \(\)\-\+\.]){1,22}$/;
	if(regex.test($(this).val()) && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

function isEmail(el){
	var regex = /^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})(;[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})*$/;
	if(!regex.test(el.val())){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
		}
		return false;
	}
	else{
		el.removeAttr("style");
		return true;
	}
}

$("*[data-isemail]").on("keyup blur paste change",function(){
	var regex = /^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})(;[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})*$/;
	if(regex.test($(this).val()) && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

function isPassword(el){
	if(el.val().length < 6){
		if(el.parent().find("> ins").length === 0){
			el.parent().append("<ins>"+el.attr("data-message")+"</ins>");
			el.css("border-color","#d63232");
		}
		return false;
	}
	else{
		el.removeAttr("style");
		return true;
	}
}

$("*[data-ispassword]").on("keyup blur paste change",function(){
	if($(this).val().length >= 6 && $(this).parent().find("> ins").length){
		$(this).parent().find("> ins").remove();
		$(this).removeAttr("style");
	}
});

$(".lx-popup-content").delegate(".todropdowninput","keyup",function(){
	$(this).parent().find("select").val($(this).parent().find("select option:eq(0)").attr("value"));
	$(this).parent().find("del").text("");
});

function htmlspecialchars(str) {
    if (typeof str !== "string") return str;

    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function toDropDown(){
	$(".todropdown").each(function(){
		$(this).parent().find(".todropdowninput").css("color","#AEAEAE");
		$(this).parent().find(".lx-autocomplete-advanced").remove();
		if($(this).prop("disabled") === false){
			$(this).parent().find(".todropdowninput").remove();
			var data_error = "";
			if(typeof $(this).attr("data-isnotempty") !== typeof undefined && $(this).attr("data-isnotempty") !== false){
				data_error = "data-isnotempty='' data-message='"+$(this).attr("data-message")+"'";
			}
			if($(this).find("option:selected").val() === ""){
				var html = '<i class="fa fa-angle-down"></i><input type="text" autocomplete="off" name="'+$(this).attr("name")+'" placeholder="'+$(this).find("option:selected").html()+'" class="todropdowninput" '+data_error+' />';
			}
			else{
				var html = '<i class="fa fa-angle-down"></i><input type="text" autocomplete="off" name="'+$(this).attr("name")+'" value="'+$(this).find("option:selected").html()+'" class="todropdowninput" '+data_error+' />';
			}
			html += '<div class="lx-autocomplete-advanced lx-autocomplete-'+$(this).attr("name")+'">';
			$(this).find("option").each(function(){				
				html += '<span data-id="'+htmlspecialchars($(this).attr("value"))+'">'+$(this).html()+'</span>';
			});
			html += '</div>';
			$(this).before(html);
			$(this).parent().find(".todropdowninput").css("color","#242424");
		}
	});		
}

function toDropDownTargeted(el){
	$(el).each(function(){
		$(this).parent().find(".todropdowninput").css("color","#AEAEAE");
		$(this).parent().find(".lx-autocomplete-advanced").remove();
		if($(this).prop("disabled") === false){
			$(this).parent().find(".todropdowninput").remove();
			var data_error = "";
			if(typeof $(this).attr("data-isnotempty") !== typeof undefined && $(this).attr("data-isnotempty") !== false){
				data_error = "data-isnotempty='' data-message='"+$(this).attr("data-message")+"'";
			}
			if($(this).find("option:selected").val() === ""){
				var html = '<i class="fa fa-angle-down"></i><input type="text" autocomplete="off" name="'+$(this).attr("name")+'" placeholder="'+$(this).find("option:selected").text()+'" class="todropdowninput" '+data_error+' />';
			}
			else{
				var html = '<i class="fa fa-angle-down"></i><input type="text" autocomplete="off" name="'+$(this).attr("name")+'" value="'+$(this).find("option:selected").text()+'" class="todropdowninput" '+data_error+' />';
			}
			html += '<div class="lx-autocomplete-advanced lx-autocomplete-'+$(this).attr("name")+'">';
			$(this).find("option").each(function(){				
				html += '<span data-id="'+htmlspecialchars($(this).attr("value"))+'">'+$(this).html()+'</span>';
			});
			html += '</div>';
			$(this).before(html);
			$(this).parent().find(".todropdowninput").css("color","#242424");
		}
	});		
}

function loadRecall(id,depot,bprice){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			bprice : bprice,
			depot : $("#commandsform select[name='depot']").val(),
			commandid : $("#commandsform input[name='id']").val(),
			invoiced : $("#commandsform select[name='invoiced']").val(),
			action : 'loadrecall'
		},
		success : function(response){
			$("#commandsform .lx-textfield del").html(response);
			
			/*var qtyoui = 0;
			var qtynon = 0;
			
			var allproducts = $("#commandsform input[name='allproducts']").val().split(",");
			var alldepots = $("#commandsform input[name='alldepots']").val().split(",");
			var allqtys = $("#commandsform input[name='allqtys']").val().split(",");
			var allbprices = $("#commandsform input[name='allbprices']").val().split(",");
			var allprices = $("#commandsform input[name='allprices']").val().split(",");			
			
			$("#commandsform select[name='product'] option[value='"+id+"']").each(function(){
				for(var i=1;i<allproducts.length;i++){
					if($(this).val() === allproducts[i] && $(this).attr("data-depot") === alldepots[i] && $(this).attr("data-bprice") === allbprices[i]){
						if($("#commandsform select[name='invoiced']").val() === "Oui"){
							qtyoui += parseFloat(allqtys[i]);
						}
						if($("#commandsform select[name='invoiced']").val() === "Non"){
							qtynon += parseFloat(allqtys[i]);
						}
					}
				}
			});*/

			var tqtyoui = 0;
			var tqtynon = 0;
			$("#commandsform select[name='product'] option[value='"+id+"']").each(function(){
				tqtyoui += parseFloat($(this).attr("data-qtyoui"));
				tqtynon += parseFloat($(this).attr("data-qtynon"));
			});
			$("#commandsform .lx-textfield del ins.lx-qtyoui").text(tqtyoui);
			$("#commandsform .lx-textfield del ins.lx-qtynon").text(tqtynon);
			/*$("#commandsform .lx-textfield del ins.lx-qtyoui").text(decimalOperation(tqtyoui,qtyoui,"minus"));
			$("#commandsform .lx-textfield del ins.lx-qtynon").text(decimalOperation(tqtynon,qtynon,"minus"));*/
		}
	});	
}

function adjustQtys(){
	var allproducts = $("#commandsform input[name='allproducts']").val().split(",");
	var alldepots = $("#commandsform input[name='alldepots']").val().split(",");
	var allqtys = $("#commandsform input[name='allqtys']").val().split(",");
	var allbprices = $("#commandsform input[name='allbprices']").val().split(",");
	var allprices = $("#commandsform input[name='allprices']").val().split(",");			
	
	$("#commandsform select[name='product'] option").each(function(){
		if(typeof $(this).attr("data-bprice") !== typeof undefined && $(this).attr("data-bprice") !== false){
			var prevqtyoui = $(this).attr("data-oqtyoui");
			var prevqtynon = $(this).attr("data-oqtynon");
			for(var i=1;i<allproducts.length;i++){
				if($(this).val() === allproducts[i] && $(this).attr("data-depot") === alldepots[i] && $(this).attr("data-bprice") === allbprices[i]){
					if($("#commandsform select[name='invoiced']").val() === "Oui"){
						var qtyoui = decimalOperation(parseFloat(prevqtyoui),parseFloat(allqtys[i]),"minus");
						$(this).attr("data-qtyoui",qtyoui);
						prevqtyoui = qtyoui;
						
					}
					if($("#commandsform select[name='invoiced']").val() === "Non"){
						var qtynon = decimalOperation(parseFloat(prevqtynon),parseFloat(allqtys[i]),"minus");
						$(this).attr("data-qtynon",qtynon);
						prevqtynon = qtynon;
					}
				}
			}
		}
	});	
}

$(".body").delegate(".todropdowninput","mousedown",function(){
	if(typeof $(this).attr("readonly") !== typeof undefined && $(this).attr("readonly") !== false){

	}
	else{
		if($(this).val() !== "" || $(this).attr("name") === "rib" || $(this).attr("name") === "rib2" || $(this).attr("name") === "company"){
			$(this).parent().find(".lx-autocomplete-advanced").toggle();
			$(this).trigger("keyup");
		}
	}
});

$(".lx-popup-content").delegate(".todropdowninput","mousedown",function(){
	if(typeof $(this).attr("readonly") !== typeof undefined && $(this).attr("readonly") !== false){

	}
	else{
		if($(this).val() !== "" || $(this).attr("name") === "rib" || $(this).attr("name") === "rib2" || $(this).attr("name") === "company"){
			$(this).parent().find(".lx-autocomplete-advanced").toggle();
			$(this).trigger("keyup");
		}
	}
});

$("body").on("keyup", ".todropdowninput", function () {
    var $this = $(this);
    var val = $this.val().trim();
    var $list = $this.next(".lx-autocomplete-advanced");

    if (val === "") {
        $list.hide();
        return;
    }

    var regex = new RegExp(val, "i");
    var seen = new Set();

    // Loop backwards to keep the latest duplicate (last in DOM)
    $($list.find("> span").get().reverse()).each(function () {
        var $span = $(this);
        var text = $span.text().trim();

        if (regex.test(text)) {
            var lowerText = text.toLowerCase();
            if (seen.has(lowerText)) {
                // Hide older duplicates
                $span.hide();
            } else {
				console.log(lowerText);
                // Keep only the latest and show it
                seen.add(lowerText);
                $span.not("[data-display]").show();
            }
        } else {
            $span.hide();
        }
    });

    $list.show();
});


$("body").delegate(".lx-autocomplete-advanced > span","click",function(){
	if($(this).attr("data-disabled") !== "yes"){
		if($(this).attr("data-id") !== ""){
			$(this).parent().prev("input").val($(this).text());
		}
		else{
			$(this).parent().prev("input").val("");
		}
		$(this).parent().next("select").find("option:eq("+$(this).index()+")").prop("selected",true);
		$(this).parent().hide();
		$(this).parent().next("select").trigger("keyup");
		if($(".lx-pagination ul").attr("data-table") === "users"){
			loadUsers($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "suppliers"){
			loadSuppliers($(".lx-pagination ul").attr("data-state"));
		}	
		if($(".lx-pagination ul").attr("data-table") === "clients"){
			loadClients($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "devis"){
			loadDevis($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "facturesproforma"){
			loadFacturesProforma($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bl"){
			loadBL($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bs"){
			loadBS($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "br"){
			loadBR($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "factures"){
			loadFactures($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "avoirs"){
			loadAvoirs($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bc"){
			loadBC($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "facturesuppliers"){
			loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "bre"){
			loadBRC($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "nh"){
			loadNH($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "nf"){
			loadNF($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "ph"){
			loadPH($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "caisse"){
			loadCaisse($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "companies"){
			loadCompanies($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "prospects"){
			loadProspects($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "labels"){
			loadLabels($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "doclabels"){
			loadDocLabels($(".lx-pagination ul").attr("data-state"));
		}
		if($(".lx-pagination ul").attr("data-table") === "messages"){
			loadMessages($(".lx-pagination ul").attr("data-state"));
		}
	}
});

$(".lx-popup-content").delegate(".lx-autocomplete-advanced > span","click",function(){
	if($(this).attr("data-id") !== ""){
		$(this).parent().prev("input").val($(this).text());
	}
	else{
		$(this).parent().prev("input").val("");
	}
	$(this).parent().next("select").find("option:eq("+$(this).index()+")").prop("selected",true);
	$(this).parent().hide();
	$(this).parent().prev("input").removeAttr("style");
	$(this).parent().next("select").trigger("change");
});

function loadPlacements(product){
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			product : product,
			action : 'loadplacements'
		},
		success : function(response){
			$("select[name='placement']").html(response);
			toDropDownTargeted("select[name='placement']");
		}		
	});		
}

function decodeHtmlEntities(str) {
    if (!str || typeof str !== "string") return str;

    const textarea = document.createElement("textarea");
    textarea.innerHTML = str;
    return textarea.value;
}

$(".lx-popup-content").delegate(".lx-add-product-to-order","click",function(){
	tinyMCE.triggerSave();
	isNotEmpty($("#commandsform textarea[name='textproduct']"));
	isNotZero($("#commandsform input[name='qty']"));
	isNotEmpty($("#commandsform select[name='utva']"));
	isNumber($("#commandsform input[name='uprice']"));
	if(isNotEmpty($("#commandsform textarea[name='textproduct']"))
	&& isNotZero($("#commandsform input[name='qty']"))
	&& isNotEmpty($("#commandsform select[name='utva']"))
	&& isNumber($("#commandsform input[name='uprice']"))){
		var title = decodeHtmlEntities($("#commandsform textarea[name='textproduct']").val());
		var unit = $("#commandsform input[name='unit']").val();
		var qty = parseFloat($("#commandsform input[name='qty']").val());
		var pricebase = $("#commandsform select[name='pricebase']").val();
		var tva = parseFloat($("#commandsform select[name='utva']").val());
		var uprice = parseFloat($("#commandsform input[name='uprice']").val());
		if(pricebase === "TTC"){
			uprice = uprice / (1 + (tva / 100));
		}
		var index = 0;
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			console.log(stripTagsAndTrim(decodeHtmlEntities($(this).find("td:eq(0)").html()))+" === "+stripTagsAndTrim("<span>"+title+"</span>"));
			if(stripTagsAndTrim(decodeHtmlEntities($(this).find("td:eq(0)").html())) === stripTagsAndTrim("<span>"+title+"</span>")){
				index = $(this).index();
			}
		});

		if(index > 0){
			var el = $(".lx-list-products table tr.lx-otherrow:eq("+(index-2)+")");
			el.attr("data-unit",unit);
			el.attr("data-qty",parseFloat(el.attr("data-qty")) + qty);
			el.attr("data-uprice",uprice);
			el.attr("data-tva",tva);
			el.attr("data-tprice",(parseFloat(el.attr("data-qty")) * uprice).toFixed(2));
			el.attr("data-ttprice",(parseFloat(el.attr("data-qty")) * uprice).toFixed(2));
			el.find("td:eq(1)").html('<span>'+(parseFloat(el.attr("data-qty")))+' '+ unit + '</span>');
			el.find("td:eq(2)").html('<span>'+numberFormat(uprice, 2, ',', ' ') + '</span>');
			el.find("td:eq(4)").html('<span>'+tva+'%</span>');
			el.find("td:eq(5)").html('<span>'+numberFormat((parseFloat(el.attr("data-qty")) * uprice), 2, ',', ' ') + '</span>');
		}
		else{
			var html = '<tr class="lx-otherrow" data-title="" data-abovenote="" data-belownote="" data-qty="'+qty+'" data-unit="'+unit+'" data-uprice="'+uprice+'" data-remise="" data-tva="'+tva+'" data-tprice="'+(parseFloat(qty * uprice)).toFixed(2)+'" data-ttprice="'+(parseFloat(qty * uprice)).toFixed(2)+'">';
			html += '<td class="lx-product-title"><span>'+title+'</span></td>';
			html += '<td><span>'+qty+' '+unit+'</span></td>';
			html += '<td><span>'+numberFormat(uprice, 2, ',', ' ')+'</span></td>';
			html += '<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;"></u></label></div></td>';
			html += '<td><span>'+tva+'%</span></td>';
			html += '<td><span>'+numberFormat((qty * uprice), 2, ',', ' ')+'</span></td>';
			html += '<td class="lx-qty-back"><div class="lx-textfield"><label><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>';
			html += '<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>';
			html += '<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>';
			html += '<td class="lx-delete-cell">';
			html += '<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>';
			html += '<div class="lx-delete-payment-choice">';
			html += '<a href="javascript:;" class="lx-delete-this">Oui</a>';
			html += '<a href="javascript:;" class="lx-no-delete-payment">Non</a>';
			html += '</div>';
			html += '</td>';
			html += '</tr>';
			$(".lx-list-products table").append(html);	
		}
		
		$("#commandsform > div > label > input[name='product']").val("");
		$("#commandsform > div > label >  select[name='product']").val("");
		$("#commandsform > div > label >  textarea[name='textproduct']").val("");
		tinyMCE.get("textproduct").setContent("");
		$("#commandsform div > label > input[name='unit']").not(".lx-table input[name='unit']").val("");
		$("#commandsform div > label > select[name='unit']").not(".lx-table select[name='unit']").val("");
		$("#commandsform div > label > input[name='qty']").not(".lx-table input[name='qty']").val("1");
		$("#commandsform div > label > select[name='pricebase']").not(".lx-table select[name='select']").val("HT");
		$("#commandsform div > label > select[name='utva']").not(".lx-table select[name='utva']").val("");
		$("#commandsform div > label > input[name='uprice']").not(".lx-table input[name='uprice']").val("");
		
		if($("#commandsform select[name='typediscount']").val() === ""){
			$(".lx-discount").css("display","none");
			$(".maindiscount").css("display","none");
		}
		else{
			$(".lx-discount").css("display","table-cell");
			$(".maindiscount").css("display","block");
			$(".discounttype").text($("#commandsform select[name='typediscount']").val());
		}
				
		$(".lx-list-products table tr td input[name='discount']").trigger("keyup");
		
		$("#commandsform input[name='maindiscount']").trigger("keyup");
		
		$(".lx-list-products table tr.lx-secondrow").css("display","none");
		
		$(".lx-error-products").html("");
		reloadProducts();
		calculateProfit();
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);
	}
});

function numberFormat(number, decimals = 2, decPoint = '.', thousandsSep = ' ') {
    number = Number(number).toFixed(decimals);
    let parts = number.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
    return parts.join(decPoint);
}

function addSlashes(str) {
    // First replace double quotes with single quotes
    str = str.replace(/"/g, "'");
    // Then escape single quotes and backslashes
    return str.replace(/['\\]/g, '\\$&');
}

$(".lx-popup-content").delegate(".lx-edit-this","click",function(){
	$(this).find("i").attr("class","fa fa-save").css("color","#7EC855");
    $(this).attr("class","lx-save-this");
    var row = $(this).parent().parent();
    var rowindex = $(this).parent().parent().index();
    var html = row.find(".lx-product-title").html();
	var htmltva = $("select[name='utva']").html()

    row.find("td:eq(0)").html('<div class="lx-textfield"><label><textarea name="title" id="title'+rowindex+'" data-isnotempty="" data-message="Saisissez un produit">'+html+'</textarea></label></div>');
    row.find("td:eq(1)").html('<div class="lx-textfield lx-g2-f"><label><span>Qté</span><input type="text" name="qty" value="'+row.attr("data-qty")+'" data-isnumber="" data-message="Saisissez une qté" style="width:100px;" /></label></div><div class="lx-textfield lx-g2-f"><label><span>Unité de mesure</span><input type="text" name="unit" value="'+row.attr("data-unit")+'" style="width:100px;" /></label></div>').css("min-width","240px");
    row.find("td:eq(2)").html('<div class="lx-textfield"><label><span>P.U. HT</span><input type="text" name="uprice" value="'+row.attr("data-uprice")+'" data-isnumber="" data-message="Saisissez un prix" style="width:100px;" /></label></div>');
    row.find("td:eq(4)").html('<div class="lx-textfield"><label><span>TVA</span><select name="tva" data-isnotempty="" data-message="Saisissez une tva" style="width:80px;">'+htmltva+'</select></label></div>');
	row.find("td:eq(4)").find("select[name='tva']").val(row.attr("data-tva")+"%");
	$(".remiseht").css("display","block");
    // 💡 Initialize TinyMCE only on the new textarea
    var newTextareaId = 'title' + rowindex;
    
    // Destroy any existing editor with the same ID (if re-editing)
    if (tinymce.get(newTextareaId)) {
        tinymce.get(newTextareaId).remove();
    }

    // Initialize TinyMCE on the new textarea
    tinymce.init({
        selector: '#' + newTextareaId + ':not(#whatsappMessage):not(#recallnote)',
        height: 200,
		width: 600,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | ' +
                 'bold italic underline | forecolor | alignleft aligncenter ' +
                 'alignright alignjustify | bullist numlist | ' +
                 'removeformat',
			  paste_data_images: false, // disable pasting base64 images

			  valid_elements: 'br,p,strong,em,u,span[style],ul,ol,li,a[href],h1,h2,h3,h4,h5,h6', // allow only text and basic formatting

			  setup: function (editor) {

				// Prevent pasting disallowed content (images, tables, videos, etc.)
				editor.on('paste', function (e) {
				  const clipboard = (e.clipboardData || e.originalEvent.clipboardData);
				  if (!clipboard) return;

				  // Block any pasted files (like images)
				  for (let i = 0; i < clipboard.items.length; i++) {
					const item = clipboard.items[i];
					if (item.kind === 'file' || item.type.indexOf('image') !== -1) {
					  e.preventDefault();
					  alert('Pasting images or files is not allowed.');
					  return false;
					}
				  }

				  // Clean pasted HTML to keep only allowed tags
				  let html = clipboard.getData('text/html');
				  if (html) {
					// Remove disallowed tags (table, video, iframe, img, etc.)
					html = html.replace(/<(table|thead|tbody|tfoot|tr|td|th|video|iframe|img|object|embed|style|script)[^>]*>.*?<\/\1>/gis, '');
					html = html.replace(/<(table|thead|tbody|tfoot|tr|td|th|video|iframe|img|object|embed|style|script)[^>]*\/?>/gis, '');
					e.preventDefault();
					editor.insertContent(html);
				  }
				});

				// Prevent dropping files into the editor
				editor.on('drop', function (e) {
				  if (e.dataTransfer && e.dataTransfer.files.length) {
					e.preventDefault();
					alert('Dropping files or media is not allowed.');
					return false;
				  }
				});
			  },
			  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    });
	if($(window).width() < 768){
		tinymce.editors.forEach(function(editor) {
			$(editor.getContainer()).css('width', ($(window).width() - 30) + 'px'); // set any width you want
		});
	}
});


$(".lx-popup-content").delegate(".lx-save-this","click",function(){
	tinyMCE.triggerSave();
	var row = $(this).parent().parent();
	isNotEmpty(row.find("textarea[name='title']"));
	isNotZero(row.find("input[name='qty']"));
	isNumber(row.find("input[name='uprice']"));
	isNotEmpty(row.find("select[name='tva']"));
	if(isNotEmpty(row.find("textarea[name='title']"))
	&& isNotZero(row.find("input[name='qty']"))
	&& isNumber(row.find("input[name='uprice']"))
	&& isNotEmpty(row.find("select[name='tva']"))){
		var title = row.find("textarea[name='title']").val();
		var abovenote = row.attr("data-abovenote");
		var belownote = row.attr("data-belownote");
		var qty = parseFloat(row.find("input[name='qty']").val());
		var unit = row.find("input[name='unit']").val();
		var uprice = parseFloat(row.find("input[name='uprice']").val());
		var tva = parseFloat(row.find("select[name='tva']").val());
		row.attr("data-title","");
		row.attr("data-unit",unit);
		row.attr("data-qty",qty);
		row.attr("data-uprice",uprice);
		row.attr("data-tva",tva);
		row.attr("data-tprice",(qty*uprice).toFixed(2));
		row.attr("data-ttprice",(qty*uprice).toFixed(2));
		row.find("td:eq(0)").html('<span>'+title+'</span>');
		row.find("td:eq(1)").html('<span>'+qty+' '+row.attr("data-unit")+'</span>').css("min-width","initial");
		row.find("td:eq(2)").html('<span>'+numberFormat(uprice, 2, ',', ' ')+'</span>');
		row.find("td:eq(4)").html('<span>'+tva+'%</span>');
		row.find("td:eq(5)").html('<span>'+numberFormat((qty*uprice), 2, ',', ' ')+'</span>');
		row.find("td").css("background","");
		reloadProducts();
		calculateProfit();
		$(this).find("i").attr("class","fa fa-edit").css("color","#39add1");
		$(this).attr("class","lx-edit-this");
		$("input[name='discount']").trigger("keyup");
		$(".remiseht").css("display","none");
	}		
});

function reloadProducts(){
	$(".lx-list-products table tr.lx-otherrow").each(function(){
		var title = $(this).find("td:eq(0)").html().trim();
		if($(this).find("td:eq(0)").find("textarea").length){
			title = $(this).find("td:eq(0)").find("textarea").val();
		}
		var unit = $(this).attr("data-unit");
		var $productSelect = $("#commandsform select[name='product']");

		// Check if an option exists where the HTML/text equals 'title'
		var productExists = false;
		$productSelect.find("option").each(function(){
			if (htmlspecialchars(stripTagsAndTrim($(this).html().trim())) === htmlspecialchars(stripTagsAndTrim(title))) {
				productExists = true;
				return false; // break loop
			}
		});

		if (!productExists) {
			$productSelect.append('<option value="'+htmlspecialchars(cleanHTML(title))+'" data-unit="'+unit+'">'+title+'</option>');
			$(".lx-autocomplete-product").append('<span data-id="'+htmlspecialchars(cleanHTML(title))+'">'+title+'</span>');
		}
		else{
			$("#commandsform select[name='product'] option[value='"+htmlspecialchars(cleanHTML(title))+"']").attr("data-unit",unit);
		}

		if(unit !== ""){
			$("#commandsform select[name='unit']").append('<option value="'+unit+'">'+unit+'</option>');
			$(".lx-autocomplete-unit").append('<span data-id="'+unit+'">'+unit+'</span>');
		}

		// Ensure all autocomplete spans are visible
		$(".lx-autocomplete-product > span").css("display","block");
		$(".lx-autocomplete-unit > span").css("display","block");
	});
}

function cleanHTML(str) {
    // Remove all HTML tags
    str = str.replace(/<[^>]*>/g, '');

    // Decode HTML entities safely
    const txt = document.createElement('textarea');
    txt.innerHTML = str;
    str = txt.value;

    // Remove any remaining encoded entities like &nbsp; or &#160;
    str = str.replace(/&[a-zA-Z0-9#]+;/g, '');

    // Remove all whitespace (spaces, tabs, newlines, etc.)
    str = str.replace(/\s+/g, '');

    // Trim final result
    return str.trim();
}

		
function calculateProfit(){
	var price = 0;
	var tva = 0;
	
	$(".lx-list-products table tr.lx-otherrow").each(function(){
		price += parseFloat($(this).attr("data-ttprice"));
		tva += parseFloat($(this).attr("data-ttprice")) * (parseFloat($(this).attr("data-tva")) / 100);
	});	
	
	$("#commandsform input[name='price']").val(price.toFixed(2));
	$("#commandsform input[name='tva'][readonly]").val(tva.toFixed(2));
	$("#commandsform input[name='ttcprice']").val((price + tva).toFixed(2));
	$("#commandsform input[name='rest']").val((price + tva).toFixed(2));
}

$(".lx-popup-content").delegate(".lx-delete-this","click",function(){
	$(this).parent().parent().parent().remove();
	if($(".lx-list-products table tr.lx-otherrow").length === 0){
		$(".lx-list-products table tr.lx-secondrow").css("display","table-row");
		$("#commandsform input[name='maindiscount']").val("");
	}
	
	$(".lx-list-products table tr td input[name='discount']").trigger("keyup");
	$(".lx-list-products table tr td input[name='discount']").trigger("click");
	
	calculateProfit();
});

$(".lx-popup-content").delegate("#commandsform select[name='typediscount']","change",function(){
	if($(this).val() === ""){
		$(".lx-discount").css("display","none");
		$(".maindiscount").css("display","none");
	}
	else{
		$(".lx-discount").css("display","table-cell");
		$(".maindiscount").css("display","block");
		$(".discounttype").text($(this).val());
	}
	$("#commandsform input[name='maindiscount']").val("");
	$(".lx-list-products table tr td input[name='discount']").val("");
	$(".lx-list-products table tr td input[name='discount']").trigger("keyup");
	$(".lx-list-products table tr td input[name='discount']").trigger("click");
});

function floorTwoDecimals(str){
	/*var str = parseFloat(str);*/
	str = str.toFixed(2);
	return str;
}

$(".lx-popup-content").delegate(".lx-list-products table tr td input[name='discount']","keyup blur paste change",function(event){
		var el = $(this).parent().parent().parent().parent();
		var discount = 0;
		if($(this).val() !== ""){
			discount = parseFloat($(this).val());
		}
		var price = parseFloat(el.attr("data-tprice"));
		if($("#commandsform select[name='typediscount']").val() === "%"){
			price = price - (price * parseFloat(discount) / 100);
		}
		else if($("#commandsform select[name='typediscount']").val() === $("#currency").val()){
			price = price - parseFloat(discount);
		}
		else{
			price = price;
		}
		price = (parseFloat(price)).toFixed(2);
		el.find("td:eq(5)").html('<span>' + numberFormat(price,2,',',' ') + '</span>');
		el.attr("data-ttprice",price);
		
		price = 0;

		$(".lx-list-products table tr.lx-otherrow").each(function(){
			var discount = 0;
			if($(this).find("input[name='discount']").val() !== ""){
				discount = parseFloat($(this).find("input[name='discount']").val());
			}
			if($("#commandsform select[name='typediscount']").val() === "%"){
				discount = ((parseFloat($(this).attr("data-tprice"))).toFixed(2) * parseFloat(discount) / 100);
			}
			else if($("#commandsform select[name='typediscount']").val() === $("#currency").val()){
				discount = parseFloat(discount);
			}
			var discountttc = (discount);
			price += parseFloat($(this).attr("data-tprice"));
			var tvaprice = ((parseFloat($(this).attr("data-tprice")) - discountttc) - ((parseFloat($(this).attr("data-tprice"))) - discount)).toFixed(2);
		});
		
		if(event.which){
			if($("#commandsform select[name='typediscount']").val() === $("#currency").val()){
				var discount = 0;
				$(".lx-list-products table tr.lx-otherrow").each(function(){
					if($(this).find("input[name='discount']").val() !== ""){
						discount += parseFloat($(this).find("input[name='discount']").val());
					}
				});
				$("#commandsform input[name='maindiscount']").val(discount.toFixed(2));	
			}
			else{
				var discount = 0;
				$(".lx-list-products table tr.lx-otherrow").each(function(){
					if($(this).find("input[name='discount']").val() !== ""){
						discount = parseFloat($(this).find("input[name='discount']").val());
					}
				});
				$("#commandsform input[name='maindiscount']").val(discount.toFixed(2));					
			}
		}
						
		calculateProfit();
});

$(".lx-popup-content").delegate(".lx-list-products table tr td input[name='discount']","click",function(event){
	if($("#commandsform select[name='typediscount']").val() === $("#currency").val()){
		var discount = 0;
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			if($(this).find("input[name='discount']").val() !== ""){
				discount += parseFloat($(this).find("input[name='discount']").val());
			}
		});
		$("#commandsform input[name='maindiscount']").val(discount.toFixed(2));	
	}
	else{
		var discount = 0;
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			if($(this).find("input[name='discount']").val() !== ""){
				discount = parseFloat($(this).find("input[name='discount']").val());
			}
		});
		$("#commandsform input[name='maindiscount']").val(discount.toFixed(2));	
	}
});

$("#commandsform input[name='maindiscount']").on("keyup click",function(event){
	var price = 0;
	
	$(".lx-list-products table tr.lx-otherrow").each(function(){
		price += parseFloat($(this).attr("data-tprice"));
	});
	if($("#commandsform select[name='typediscount']").val() === "%"){
		$(".lx-list-products table tr td input[name='discount']").val($(this).val());
	}
	else if($("#commandsform select[name='typediscount']").val() === $("#currency").val()){
		var discount = ($(this).val() * 100) / price.toFixed(2);
		$(".lx-list-products table tr.lx-otherrow").each(function(){
			price = parseFloat($(this).attr("data-tprice"));
			$(this).find("input[name='discount']").val((price * (discount / 100)).toFixed(2));
		});
	}

	$(".lx-list-products table tr td input[name='discount']").trigger("keyup");
});


$(".lx-popup-content").delegate(".lx-add-service-to-order","click",function(){
	isNotEmpty($("#commandsform select[name='service']"));
	isNumber($("#commandsform input[name='qtyservice']"));
	isNumber($("#commandsform input[name='upriceservice']"));
	if(isNotEmpty($("#commandsform select[name='service']"))
	&& isNumber($("#commandsform input[name='qtyservice']"))
	&& isNumber($("#commandsform input[name='upriceservice']"))){
		if($("#commandsform select[name='invoiced']").val() !== ""){
			addServiceToOrder();
		}
		else{
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Choisissez un état de facturation<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);			
		}
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

function getProduct(code){
	$("#commandsform select[name='product'] option[data-code='"+code+"']").prop("selected",true);
	$("#commandsform input[name='product']").val($("#commandsform select[name='product'] option[data-code='"+code+"']").text());
}

$("#commandsform input[name='cash']").on("keyup blur paste change",function(){
	var regex = /^[+-]?\d+(\.\d+)?$/;
	if(regex.test($("#commandsform input[name='cash']").val())){
		$("#commandsform input[name='rest']").val((parseFloat($("#commandsform input[name='ttcprice']").val()) - parseFloat($("#commandsform input[name='cash']").val())).toFixed(2));
	}
	else{
		$("#commandsform input[name='rest']").val((parseFloat($("#commandsform input[name='ttcprice']").val()) - 0).toFixed(2));
	}
});

$("body").delegate(".lx-product-depot > span > i","click",function(){
	$(this).next("div").slideToggle();
	if($(this).attr("class") === "fa fa-plus-square"){ 
		$(this).attr("class","fa fa-minus-square");
	}
	else{
		$(this).attr("class","fa fa-plus-square");
	}
});

$(".lx-table").on('touchmove', onScroll);
$(".lx-table").on('scroll', onScroll); 

function onScroll(){
	$(".lx-table-shadow").css({"height":$(".lx-table:eq(0) table").height()+"px","top":$(".lx-table:eq(0) table").position().top+"px"});
	if(Math.round($(".lx-table").width()) === Math.round($(".lx-table table").width())){
		$(".lx-table-shadow").hide();
	}
	else if($(".lx-table").scrollLeft() === 0){
		$(".lx-table-shadow-left").hide();
	}
	else if(Math.ceil($(".lx-table").scrollLeft()+$(".lx-table").width()) >= $(".lx-table table").width()){
		$(".lx-table-shadow-right").hide();
	}
	else{
		$(".lx-table-shadow").show();
	}
}

$(".lx-add-service").on("click",function(){
	$(".serviceyes").toggle();
	if($(".serviceyes").css("display") === "block"){
		$(this).find("i").attr("class","fa fa-minus");
	}
	else{
		$(this).find("i").attr("class","fa fa-plus");
	}
});


function decimalOperation(a,b,operation){
	if(operation === "plus"){
		return parseFloat((a+b).toFixed(5));
	}
	else if(operation === "minus"){
		return parseFloat((a-b).toFixed(5));
	}
	else if(operation === "multiply"){
		return parseFloat((a*b).toFixed(5));
	}
	else if(operation === "div"){
		return parseFloat((a/b).toFixed(5));
	}
}

$("body").delegate(".lx-transform-avoir","click",function(){
	var id = $(this).attr("data-id");
	var code = $(this).attr("data-code");
	var dateadd = $(this).attr("data-dateadd");
	$(".lx-transform-to-avoir").attr("data-id",id);
	$(".lx-transform-to-avoir").attr("data-code",code);
	$(".lx-transform-to-avoir").attr("data-dateadd",dateadd);
});

$(".lx-transform-to-avoir").on("click",function(){
	var id = $(this).attr("data-id");
	var code = $(this).attr("data-code");
	var dateadd = $(this).attr("data-dateadd");
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : id,
			code : code,
			dateadd : dateadd,
			action : 'transformtoavoir'
		},
		success : function(response){
			$(".lx-popup-content > a > .material-icons").trigger("click");
			loadAvoirs("1");
		}
	});
});

			// Open the modal
			function openPopup() {
			  document.getElementById("bookingModal").style.display = "flex";
			}

			// Close the modal
			function closePopup() {
			  document.getElementById("bookingModal").style.display = "none";
			}

			// Close the modal when clicking outside of it
			window.onclick = function(event) {
			  const modal = document.getElementById("bookingModal");
			  if (event.target === modal) {
				closePopup();
			  }
			};
			
			var idnote = 0;
			$(".lx-popup-content").delegate(".lx-note-this","click",function(){
				var row = $(this).parent().parent();
				idnote = row.attr("data-title");
				$(".lx-product-label").html(row.attr("data-title"));
				if(row.attr("data-abovenote") !== ""){
					$("textarea[name='abovenote']").val(row.attr("data-abovenote"));
					tinyMCE.get("abovenote").setContent(row.attr("data-abovenote"));
				}
				else{
					$("textarea[name='abovenote']").val("");
					tinyMCE.get("abovenote").setContent('');
				}
				if(row.attr("data-belownote")){
					$("textarea[name='belownote']").val(row.attr("data-belownote"));
					tinyMCE.get("belownote").setContent(row.attr("data-belownote"));
				}
				else{
					$("textarea[name='belownote']").val("");
					tinyMCE.get("belownote").setContent('');
				}
			});
	
			$(".submit-btn").on("click",function(){
				var row = $(".lx-otherrow[data-title='"+idnote+"']");
				tinyMCE.triggerSave();
				if($("textarea[name='abovenote']") !== ""){
					row.attr('data-abovenote',$("textarea[name='abovenote']").val());
				}
				if($("textarea[name='belownote']") !== ""){
					row.attr('data-belownote',$("textarea[name='belownote']").val());
				}
				$("textarea[name='abovenote']").val("");
				$("textarea[name='belownote']").val("");	
				closePopup();
				
				$(".lx-otherrow[data-title='"+idnote+"']").find("td:eq(0)").html('<span>'+row.attr('data-abovenote')+'</span><span>'+row.attr('data-title')+'</span><span>'+row.attr('data-belownote')+'</span>');
			});
			
$(".lx-labels a").on("click",function(){
	if($(this).find("i").length){
		$(this).find("i").remove();
	}
	else{
		$(this).prepend('<i class="fa fa-check"></i>');
	}
});

$(".lx-accordion a").on("click",function(){
	$(this).next("div").slideToggle();
});

$(".lx-accordion > a").on("click",function(){
	if($(".lx-accordion-content .lx-loading").length === 0){
		$(".lx-accordion-content").prepend('<div class="lx-loading" style="padding:10px;text-align:center;"><p>Please wait ...<br /><i class="fa fa-circle-notch fa-spin"></i></p></div>');
	}
	var ajaxurl = "ajax.php";
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			id : $("#paymentform input[name='id']").val(),
			page : $("#paymentform input[name='page']").val(),
			supplier : $("#paymentform input[name='supplier']").val(),
			company : $("#paymentform input[name='company']").val(),
			action : 'loadsupplierras'
		},
		success : function(response){
			$(".lx-table-companies .lx-loading").remove();
			$(".lx-accordion-content").html(response);
			if($(".lx-accordion-content > div > a").length){
				$(".lx-accordion-content").css("background","#FFFFFF");
				if($(".lx-payment-state[data-paid='Partiellement payée']").css("display") === "none" && $(".lx-apply").css("display") === "block" && parseFloat($("#paymentform input[name='rastva']").val()) > 0){
					$(".lx-apply").trigger("click");
				}
			}
			else{
				$(".lx-accordion-content").css("background","#C6E2FF");
			}
		}
	});	
});

$(".lx-popup-content").delegate(".lx-accordion-content > div > a.lx-apply","click",function(){
	$(".lx-payment-state[data-paid='Payée']").trigger("click");
	var rest = parseFloat($("#paymentform input[name='resthidden']").val());
	var ras = parseFloat($("#ras").val());
	$("#paymentform input[name='cash']").val((rest-ras).toFixed(2));
	$(".lx-ras").html("("+$("#percentage").val()+"% de RAS: <b>-"+ras.toFixed(2)+" "+$("#currency").val()+"</b>)").css({"color":"#FF0000"});
	$(this).css("display","none");
	$(".lx-cancel").css("display","inline-block");
	$(".lx-payment-state[data-paid='Partiellement payée']").css("display","none");
	$("#paymentform input[name='rastva']").val($("#ras").val());
});

$(".lx-popup-content").delegate(".lx-accordion-content > div > a.lx-cancel","click",function(){
	$(".lx-payment-state[data-paid='Payée']").trigger("click");
	var rest = parseFloat($("#paymentform input[name='resthidden']").val());
	var ras = parseFloat($("#ras").val());
	$(".lx-ras").html("");
	$(this).css("display","none");
	$(".lx-apply").css("display","inline-block");
	$(".lx-payment-state[data-paid='Partiellement payée']").css("display","inline-block").trigger("click");
	$("#paymentform input[name='cash']").val("0.00");
	$("#paymentform input[name='rastva']").val("0");
	$("#paymentform select[name='modepayment']").val("Espèce");
	$("#paymentform .lx-submit a").trigger("click");
});

$(".lx-clear-attachment").on("click",function(){
	$("#rastvaform input[name='attachments']").val("");
	$(".fa-check").css("display","none");
	$(this).css("display","none");
});

document.addEventListener("DOMContentLoaded", function() {
  const tooltip = document.createElement("div");
  tooltip.className = "tooltip";
  document.body.appendChild(tooltip);

  const select = document.querySelector(".lx-companies-list");

  document.querySelectorAll("label[data-message]").forEach(label => {
    label.addEventListener("mouseenter", e => {
      if (select && select.value === "") { // condition here
        tooltip.textContent = label.getAttribute("data-message");
        tooltip.style.opacity = "1";
      }
    });

    label.addEventListener("mousemove", e => {
      if (tooltip.style.opacity === "1") {
        tooltip.style.left = (e.pageX + 10) + "px";
        tooltip.style.top = (e.pageY + 10) + "px";
      }
    });

    label.addEventListener("mouseleave", () => {
      tooltip.style.opacity = "0";
    });
  });
});

$(".lx-transform-bulk").on("click",function(){
	if($("#mergeform input[name='ids']").val() !== ""){
	$(".merge .lx-form-title h3").text("Transformation groupée");	
		var k = 0;
		var vals = "";
		$(".lx-page-content .lx-table table input[type='checkbox']:not(.lx-page-content .lx-table table input[name='selectall'])").each(function(){
			if($(this).prop("checked") === true){
				vals += ","+$(this).attr("data-discounttype");
			}
		});
		if(!checkAllValuesMatch(vals.substring(1))){
			if(confirm('Les documents sélectionnés comportent différents types de remises. Ils seront uniformisés en '+$("#currency").val()+'. Souhaitez-vous poursuivre cette opération ?')){
				k = 0;
			} 
			else{
				k = 1;
			}
		}
		
		if(k === 0){
			vals = "";
			$(".lx-page-content .lx-table table input[type='checkbox']:not(.lx-page-content .lx-table table input[name='selectall'])").each(function(){
				if($(this).prop("checked") === true){
					vals += ","+$(this).attr("data-company");
				}
			});
			$("#mergeform select[name='company']").val("");
			if(checkAllValuesMatch(vals.substring(1))){
				vals = vals.split(",");
				$("#mergeform select[name='company']").val(vals[1]);
			}

			$(".lx-create-avoir").css("display","block");
			$(".lx-uncreate-avoir").css("display","none");

			$(".lx-transform-bulk-real").trigger("click");
			
			$("#mergeform select[name='transform']").val($("#mergeform select[name='transform'] option:first").attr("value"));
			
			$("#mergeform select[name='transform']").trigger("change");

			$("#mergeform input[name='dateaddcommand']").val(getCurrentDate());

			$("#mergeform input[name='exist']").val("1");
			$("#mergeform input[name='new'][value='1']").trigger("click");
					
			var ids = $("#mergeform input[name='ids']").val().split(",");
			var codes = $("#mergeform input[name='codes']").val().split(",");
			var prices = $("#mergeform input[name='prices']").val().split(",");
		
			var html = '<table cellpadding="0" cellspacing="0">';
			html += '<tr class="lx-first-tr">';
			html += '<td>Réf</td>';
			html += '<td>Montant TTC</td>';
			html += '</tr>';
			
			for(var i = 0;i < ids.length;i++){
				html += '</tr>';
				html += '<td>'+codes[i]+'</td>';
				html += '<td>'+prices[i]+'</td>';
				html += '</tr>';		
			}
			
			html += '</table>';
			
			$(".lx-list-docs").html(html);
		}
	}
	else{
		$(".lx-floating-response").remove();
		window.clearTimeout(timer);
		$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez choisir au moins un document<i class="material-icons"></i></p></div>');
		$(".lx-floating-response").fadeIn();
		window.setTimeout(function(){
			$(".lx-floating-response").fadeOut();
		},5000);		
	}
});

$("#mergeform select[name='transform']").on("change",function(){
	$("#mergeform input[name='type']").val($(this).val());
	$("#mergeform input[name='prefix']").val($(this).find("option:selected").attr("data-prefix"));
	if($("#mergeform select[name='company']").val() !== ""){
		var ajaxurl = "ajax.php";
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				company : $("#mergeform select[name='company']").val(),
				type : $("#mergeform input[name='type']").val(),
				dateadd : $("#mergeform input[name='dateaddcommand']").val(),
				category : $("#mergeform input[name='categories']").val(),
				action : 'loadcode'
			},
			success : function(response){
				console.log(response);
				$("#mergeform input[name='code']").val(response);
				$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
				$("#mergeform input[name='code']").trigger("blur");
			}
		});
	}
	else{
		$("#mergeform input[name='code']").val("");
		$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
		$("#mergeform input[name='code']").trigger("blur");
	}

	if($(this).find("option:selected").attr("data-prefix") === "BRC"){
		$("#mergeform input[name='code']").css("padding-left","82px");
	}
	else{
		$("#mergeform input[name='code']").css("padding-left","72px");
	}
	
	if($(this).parent().parent().css("display") === "none"){
		$(".lx-code-label").css("top","30px");
	}
	else{
		$(".lx-code-label").css("top","30px");
	}
});

$("#mergeform select[name='company']").on("change",function(){
	var company = $(this).val();
	var ajaxurl = "ajax.php";
	
	var action = 'reloadclientslist';
	if($("#mergeform input[name='type']").val() === "bc" || $("#mergeform input[name='type']").val() === "bre"){
		action = 'reloadsupplierslist';
	}
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : {
			company : $("#mergeform input[name='exist']").val()=="1"?company:0,
			category : $("#mergeform input[name='category']").val(),
			page : $("#mergeform input[name='type']").val(),
			idclient : $("#mergeform input[name='clientid']").val(),
			idprospect : $("#mergeform input[name='prospectid']").val(),
			action : action
		},
		success : function(response){
			$("#mergeform select[name='client']").html(response);
			toDropDownTargeted("#mergeform select[name='client']");
			$("#mergeform select[name='client']").trigger("change");
			$("#mergeform select[name='client']").prev("div").find("span[data-id='"+$("#mergeform input[name='clientid']").val()+"']").trigger("click");
		}
	});

	if($(this).val() !== ""){
		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				company : company,
				type : $("#mergeform input[name='type']").val(),
				dateadd : $("#mergeform input[name='dateaddcommand']").val(),
				category : $("#mergeform input[name='categories']").val(),
				action : 'loadcode'
			},
			success : function(response){
				console.log(response);
				$("#mergeform input[name='code']").val(response);
				$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
				$("#mergeform input[name='code']").trigger("blur");
			}
		});
	}
	else{
		$("#mergeform input[name='code']").val("");
		$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
		$("#mergeform input[name='code']").trigger("blur");
	}
});

$("#mergeform input[name='code']").on("keyup",function(){
	if($("#mergeform input[name='prefix']").val() === "BRC"){
		$("#mergeform input[name='code']").css("padding-left","77px");
	}
	else{
		$("#mergeform input[name='code']").css("padding-left","67px");
	}
});

$("#mergeform .lx-submit a").on("click",function(){
	tinyMCE.triggerSave();
	var k = 0;
	if(k === 0){
		isNotEmpty($("#mergeform input[name='exist']"));
		var client = "";
		if($("#mergeform input[name='exist']").val() === "0"){
			$("#mergeform input[name='client']").attr("data-isnotempty","");
			$("#mergeform input[name='client']").attr("data-message","Saisissez un client");
			if(!isNotEmpty($("#mergeform input[name='client']"))){
				client = "1";
			}
		}
		if($("#mergeform input[name='exist']").val() === "1"){
			$("#mergeform input[name='client']").attr("data-isnotempty","");
			$("#mergeform input[name='client']").attr("data-message","Choisissez un client");
			if(!isNotEmpty($("#mergeform select[name='client']"))){
				$("#mergeform input[name='client']").css("border","1px solid rgb(214, 50, 50)");
				client = "1";
			}
			else{
				$("#mergeform input[name='client']").removeAttr("style");
			}
		}
		
		isNotEmpty($("#mergeform select[name='company']"));
		isNumber($("#mergeform input[name='code']"));
		if(isNotEmpty($("#mergeform select[name='company']"))
			&& isNumber($("#mergeform input[name='code']"))
			&& isNotEmpty($("#mergeform input[name='exist']"))
			&& client === ""){
				if($(this).attr("class") !== "lx-disabled"){
					var z = 0;
					if($("#mergeform input[name='type']").val() === "facture" && $("#mergeform input[name='ice']").val() === ""){
						if(confirm('Voulez vous vraiment générer une facture sans ICE?')){
							z = 0;
						} 
						else{
							z = 1;
						}
					}
					if(z === 0){
						$(this).attr("class","lx-disabled");
						if($(this).find("i").length === 0){
							$(this).prepend('<i class="fa fa-circle-notch fa-spin"></i> ');
						}
						var fullname = $("#mergeform input[name='client']").val();
						if(typeof $("#mergeform select[name='client'] option:selected").attr("data-name") !== typeof undefined && $("#mergeform select[name='client'] option:selected").attr("data-name") !== false){
							fullname = $("#mergeform select[name='client'] option:selected").attr("data-name");
						}					
						var ajaxurl = "ajax.php";
						$.ajax({
							url : ajaxurl,
							type : 'post',
							data : {
								id : $("#mergeform input[name='id']").val(),
								ids : $("#mergeform input[name='ids']").val(),
								type : $("#mergeform input[name='type']").val(),
								prefix : $("#mergeform input[name='prefix']").val(),
								category : $("#mergeform input[name='category']").val(),
								code : $("#mergeform input[name='code']").val(),
								dateadd : $("#mergeform input[name='dateaddcommand']").val(),
								company : $("#mergeform select[name='company']").val(),
								exist : $("#mergeform input[name='exist']").val(),
								fullname : fullname,
								client : $("#mergeform select[name='client']").val(),
								typeclient : $("#mergeform select[name='client'] option:selected").attr("data-type"),
								ice : $("#mergeform input[name='ice']").val(),
								phone : $("#mergeform input[name='phone']").val(),
								address : $("#mergeform input[name='address']").val(),
								email : $("#mergeform input[name='email']").val(),
								note : $("#mergeform textarea[name='notemerge']").val(),
								action : 'mergedocs'
							},
							success : function(response){
								console.log(response);
								if(response !== ""){
									$("#mergeform .lx-submit a").attr("class","");
									$("#mergeform .lx-submit a i").remove();
									$(".lx-floating-response").remove();
									window.clearTimeout(timer);
									$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> '+response+'<i class="material-icons"></i></p></div>');
									$(".lx-floating-response").fadeIn();
									window.setTimeout(function(){
										$(".lx-floating-response").fadeOut();
									},5000);							
								}
								else{
									$("#mergeform .lx-submit a").attr("class","");
									$("#mergeform .lx-submit a i").remove();
									$(".lx-popup-content > a > .material-icons").trigger("click");
									if($("#mergeform input[name='type']").val() === "facture"){
										window.location.href = "factures.php";
									}
									else if($("#mergeform input[name='type']").val() === "nh"){
										window.location.href = "nh.php";
									}
									else if($("#mergeform input[name='type']").val() === "nf"){
										window.location.href = "nf.php";
									}
									else if($("#mergeform input[name='type']").val() === "devis"){
										window.location.href = "devis.php";
									}
									else if($("#mergeform input[name='type']").val() === "ph"){
										window.location.href = "ph.php";
									}
									else if($("#mergeform input[name='type']").val() === "avoir"){
										window.location.href = "avoirs.php";
									}
									else if($("#mergeform input[name='type']").val() === "br"){
										window.location.href = "br.php";
									}
									else if($("#mergeform input[name='type']").val() === "factureproforma"){
										window.location.href = "facturesproforma.php";
									}
									else if($("#mergeform input[name='type']").val() === "bl"){
										window.location.href = "bl.php";
									}
									else if($("#mergeform input[name='type']").val() === "bs"){
										window.location.href = "bs.php";
									}
									else if($("#mergeform input[name='type']").val() === "bc"){
										window.location.href = "bc.php";
									}
									else if($("#mergeform input[name='type']").val() === "bre"){
										window.location.href = "bre.php";
									}
									$(".lx-floating-response").remove();
									window.clearTimeout(timer);
									$("body").append('<div class="lx-floating-response"><p class="lx-succes"><i class="fa fa-check"></i> Bien enregistré<i class="material-icons"></i></p></div>');
									$(".lx-floating-response").fadeIn();
									window.setTimeout(function(){
										$(".lx-floating-response").fadeOut();
									},5000);
								}
							}
						});
					}
				}
		}
		else{
			$("#mergeform .lx-submit a i").remove();
			$(".lx-floating-response").remove();
			window.clearTimeout(timer);
			$("body").append('<div class="lx-floating-response"><p class="lx-error"><i class="fa fa-exclamation-triangle"></i> Veuillez remplir les champs en rouge<i class="material-icons"></i></p></div>');
			$(".lx-floating-response").fadeIn();
			window.setTimeout(function(){
				$(".lx-floating-response").fadeOut();
			},5000);
		}
	}
});

$(".lx-popup-content").delegate("#mergeform input[name='new']","click",function(){
	$("#mergeform input[name='exist']").val($(this).val()).trigger("keyup");
	$("#mergeform input[name='client']").val("").removeAttr("style");
	$("#mergeform select[name='client']").val("").next("ins").remove();
	$("#mergeform input[name='ice']").val("").removeAttr("style").next("ins").remove();
	$("#mergeform input[name='phone']").val("");
	$("#mergeform input[name='address']").val("").removeAttr("style").next("ins").remove();
	$("#mergeform input[name='email']").val("");
	isNotEmpty($("#mergeform input[name='exist']"));
	if($(this).val() === "0"){
		if($("#mergeform input[name='type']").val() === "bc" || $("#mergeform input[name='type']").val() === "bre"){
			$("#mergeform input[name='client']").attr("placeholder","Saisissez un fournisseur");
			$("#mergeform input[name='client']").parent().find("> span").html("Fournisseur<sup>*</sup>: ");
		}
		else{
			$("#mergeform input[name='client']").attr("placeholder","Saisissez un client");
			$("#mergeform input[name='client']").parent().find("> span").html("Client<sup>*</sup>: ");
		}
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").show();
	}
	else if($(this).val() === "1"){
		$("#mergeform select[name='company']").trigger("change");
		if($("#mergeform input[name='type']").val() === "bc" || $("#mergeform input[name='type']").val() === "bre"){
			$("#mergeform input[name='client']").parent().find("> span").html("Fournisseur<sup>*</sup>: ");
		}
		else{
			$("#mergeform input[name='client']").parent().find("> span").html("Client<sup>*</sup>: ");
		}
		$(".lx-client").show();
		toDropDownTargeted($("#mergeform select[name='client']"));
	}
	else if($(this).val() === "2"){
		$("#mergeform input[name='client']").attr("placeholder","Saisissez un prospect");
		$("#mergeform input[name='client']").parent().find("> span").html("Prospect<sup>*</sup>: ");
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").show();
	}
	else{
		if($("#mergeform input[name='type']").val() === "bc" || $("#mergeform input[name='type']").val() === "bre"){
			$("#mergeform input[name='client']").parent().find("> span").html("Fournisseur<sup>*</sup>: ");
		}
		else{
			$("#mergeform input[name='client']").parent().find("> span").html("Client<sup>*</sup>: ");
		}
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").hide();
	}
});

$("#mergeform select[name='client']").on("change",function(){
	addClientToOrderMerge();	
});

function addClientToOrderMerge(){
	$("#mergeform input[name='client']").val($("#mergeform select[name='client'] option:selected").attr("data-name"));
	$("#mergeform input[name='ice']").val($("#mergeform select[name='client'] option:selected").attr("data-ice"));
	$("#mergeform input[name='phone']").val($("#mergeform select[name='client'] option:selected").attr("data-phone"));
	$("#mergeform input[name='address']").val($("#mergeform select[name='client'] option:selected").attr("data-address"));
	$("#mergeform input[name='email']").val($("#mergeform select[name='client'] option:selected").attr("data-email"));
}

function checkAllValuesMatch(inputStr) {
    // Split by commas to separate different value groups
    const groups = inputStr.split(',').map(g => g.trim()).filter(g => g !== "");

    // Helper to get first non-empty main value from a group
    function getMainValue(group) {
        const parts = group.split('||').map(v => v.trim());
        for (let p of parts) {
            if (p !== "") return p;
        }
        return ""; // return empty if no main value
    }

    let mainValues = [];

    for (let g of groups) {
        const parts = g.split('||').map(v => v.trim());
        const hasEmpty = parts.some(v => v === "");
        const main = getMainValue(g);

        // If group has an empty value, automatically matches
        if (hasEmpty) return true;

        // Store only non-empty main values
        if (main !== "") mainValues.push(main);
    }

    // If all are empty → match
    if (mainValues.length === 0) return true;

    // Check if all main values are the same
    const first = mainValues[0];
    const allSame = mainValues.every(v => v === first);

    return allSame;
}

$(".lx-popup-content").delegate("#facturesuppliersform input[name='new']","click",function(){
	$("#facturesuppliersform input[name='exist']").val($(this).val()).trigger("keyup");
	$("#facturesuppliersform input[name='client']").val("").removeAttr("style");
	$("#facturesuppliersform select[name='client']").val("").next("ins").remove();
	$("#facturesuppliersform input[name='ice']").val("").removeAttr("style").next("ins").remove();
	$("#facturesuppliersform input[name='phone']").val("");
	$("#facturesuppliersform input[name='address']").val("").removeAttr("style").next("ins").remove();
	$("#facturesuppliersform input[name='email']").val("");
	isNotEmpty($("#facturesuppliersform input[name='exist']"));
	if($(this).val() === "0"){
		$("#facturesuppliersform input[name='client']").attr("placeholder","Saisissez un fournisseur");
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").show();
	}
	else if($(this).val() === "1"){
		$("#facturesuppliersform select[name='company']").trigger("change");
		$(".lx-client").show();
		toDropDownTargeted($("#facturesuppliersform select[name='client']"));
	}
	else{
		$(".lx-autocomplete-advanced").remove();
		$(".lx-client").hide();
	}
});

$("#facturesuppliersform select[name='client']").on("change",function(){
	addClientToOrderSupplier();
});

function addClientToOrderSupplier(){
	$("#facturesuppliersform input[name='client']").val($("#facturesuppliersform select[name='client'] option:selected").attr("data-name"));
	$("#facturesuppliersform input[name='ice']").val($("#facturesuppliersform select[name='client'] option:selected").attr("data-ice"));
	$("#facturesuppliersform input[name='phone']").val($("#facturesuppliersform select[name='client'] option:selected").attr("data-phone"));
	$("#facturesuppliersform input[name='address']").val($("#facturesuppliersform select[name='client'] option:selected").attr("data-address"));
	$("#facturesuppliersform input[name='email']").val($("#facturesuppliersform select[name='client'] option:selected").attr("data-email"));
}

function addClientToOrder(){
	$("#commandsform input[name='client']").val($("#commandsform select[name='client'] option:selected").attr("data-name"));
	$("#commandsform input[name='ice']").val($("#commandsform select[name='client'] option:selected").attr("data-ice"));
	$("#commandsform input[name='phone']").val($("#commandsform select[name='client'] option:selected").attr("data-phone"));
	$("#commandsform input[name='address']").val($("#commandsform select[name='client'] option:selected").attr("data-address"));
	$("#commandsform input[name='email']").val($("#commandsform select[name='client'] option:selected").attr("data-email"));
}

const tabs = document.querySelectorAll('.tab');
const contents = document.querySelectorAll('.tab-content');
tabs.forEach(tab => {
	tab.addEventListener('click', () => {
		tabs.forEach(t => t.classList.remove('active'));
		contents.forEach(c => c.classList.remove('active'));
		tab.classList.add('active');
		document.getElementById(tab.dataset.tab).classList.add('active');
	});
});