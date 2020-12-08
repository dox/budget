//alert('Test');

$(document).ready(function(){
$('.toast').toast('show');

$(".emailParcelButton1").click(function() {
	//$(this).parent().parent().fadeOut();

	var departmentUID = $(this).attr('id');
	//alert(departmend_id);

	var url = 'actions/user_change_department.php';

	// perform the post to the action (take the info and submit to database)
	$.post(url,{
		departmentUID: departmentUID
	}, function(data){
		//$("#andrew").append(data);
		alert('You have changed departments - please remember to change back!');
	},'html');

	return false;
});

$(".orderCreateButton").click(function() {
	$(this).prop("disabled", true);

	var submit = true;

	var date = $("input#date").val();
	var cost_centre = $("select#cost_centre").val();
	var po = $("input#po").val();
	var order_num = $("input#order_num").val();
	var name = $("input#name").val();
	var value = $("input#value").val();
	var supplier = $("input#supplier").val();
	var description = $("textarea#description").val();

	var url = 'actions/order_create.php';

	if (date.length == 0) {
		$("input#date").addClass("is-invalid");
		submit = false;
	} else {
		$("input#date").removeClass("is-invalid");
	}

	if (po.length == 0) {
		$("input#po").addClass("is-invalid");
		submit = false;
	} else {
		$("input#po").removeClass("is-invalid");
	}

	if (cost_centre.length == 0) {
		$("select#cost_centre").addClass("is-invalid");
		submit = false;
	} else {
		$("select#cost_centre").removeClass("is-invalid");
	}

	if (!$.isNumeric($("input#value").val())) {
		$("input#value").addClass("is-invalid");
		submit = false;
	} else {
		$("input#value").removeClass("is-invalid");
	}

	if (submit == true) {
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			date: date,
			cost_centre: cost_centre,
			po: po,
			order_num: order_num,
			name: name,
			value: value,
			supplier: supplier,
			description: description
		}, function(data){
			$("#andrew").append(data);
			window.location.href = "./index.php?n=orders_all";
		},'html');
	} else {
		alert("Errors found on order submission.  Please check and try again!")
		$(this).prop("disabled", false);
	}

	return false;
});

$(".deleteUpload").click(function() {
	var thisObject = $(this);
	var uploadUID = $(this).attr('id');

	var url = 'actions/file_delete.php';

	var r=confirm("Are you sure you want to delete this file?  This cannot be undone!");

	if (r==true) {
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			uploadUID: uploadUID
		}, function(data){
			$(thisObject).parent().fadeOut();
		},'html');
	}

	return false;
});

});
