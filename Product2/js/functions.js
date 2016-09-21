$(function () {
	$("#UserLogin").submit(function() {
		$("#UserLogin #authMessage").remove();
		$("#UserLogin").prepend('<center><span id="loading"></span></center>');
		$.post($("#UserLogin").attr('action'), $("#UserLogin").serialize(), function(data){
			var $data = data.split('#');
			if($data[0]=='success')
			 $("#UserLogin").html($data[1]);
			else{
			 $("#UserLogin").prepend($data[1]);
			 $("#UserLogin #loading").remove();
			}
		});
		return false;
	});
	$('#user_search').bind('keyup', function(e) {
			var keyCode = (e.keyCode ? e.keyCode : e.which);
			if ( keyCode == 13 ) {
				$('#loading').remove();
				$(this).after('<span id="loading"></span>');
				$.post('/managers/user_search',{query : $(this).val()} ,function (data){
						$("#usersContainer").html(data);
						$('span#loading').fadeOut('fast');
						$('.paginate').remove();
					});
			}
		});
	$('#service_product_property').change(function(){
		$(this).after('<span id="loading"></span>');
		$.post('/managers/productproperty',{service_id : $(this).val()}, function (data) {
			$('#productproperty').html(data);
			$('span#loading').fadeOut('fast');
		}
		);
	});
	$('#OrderMonthly').change(function(){
		if($(this).find('option:selected').val() != "0"){
			$.get('/users/getprice/'+$('#OrderProductId').val()+'/'+$(this).find('option:selected').val(), function (data) {
			$('#price_label').html('<label><b>  قیمت : </b></label>'+data+' تومان'); 
		});
		}
		else
		$('#price_label').html(' ');
	}
	);
	$('#add_property').css('cursor', 'pointer');
	$('#add_property').click(function () {
		var count = $("#property input:last").attr('count');
		if(!count)
			count=0;
		count++;
		$('#property').append('<span><label>مورد ' + count + ':</label> <input name="data[Service][property][' + count + ']" type="text" value="" count="' + count + '" /><br /><br /></span>');
	});
	$("#delete_property").click( function () {
		$("#property span:last").remove();
	});
	
	$('#services_list').change(function () {
		$('#price_label').html(' ');
		$('#service_products').after('<span id="loading"></span>');
		$.post('/users/serviceproducts/', {service_id: $(this).val()}, function (data) {
			$('#service_products').html(data);
			$('#service_products').next('span#loading').fadeOut('fast');
		});
		if ( $(this).find('option:selected').attr('monthly') == 1 )
		{
			$('#period').show();
		}
		else
		{
			$('#period').hide();
		}
		if ( $(this).find('option:selected').attr('domain') == 1 )
		{
			$('#domain').show();
		}
		else
		{
			$('#domain').hide();
		}
		return false;
	});
	$('#service_products').change(function(){
	if($(this).find('option:selected').attr('cost') != "0")
		$('#price_label').html('<b>  قیمت : </b>'+$(this).find('option:selected').attr('cost')+' تومان'); 
	else
		$('#price_label').html(' ');
	});
	
	$('a.newPage').click(function () {
		window.open($(this).attr('href'), "NewPage", "resizable=0,status=0,scrollbars=1,toolbar=0,width=720,height:730");
		return false;
	});

    $(document).pngFix();	
	$('#customers').Customers(3000, 300);

});

//Featured Customers
(function ($) {
	$.fn.Customers = function (interval, speed)
	{	
		var loop = 0,
			list = $(this),
			height = parseInt($(this).find('li:first').css('height')),
			total = $(this).find('li').size();
		
		return this.each(function () {
			function repeat () {
				list.find('li:first').animate({marginTop: "-" + (loop*height) + "px"}, speed);
				if ( loop < total - 1 ) 
					loop++;
				else
					loop = 0;
			}
			setInterval(repeat, interval);
		});
	}
})(jQuery);