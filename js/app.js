function departmentChange(this_id) {
  event.preventDefault()
  var formData = new FormData();

  formData.append("departmentUID", this_id);

  //https://javascript.info/xmlhttprequest GREAT documentation!
  var request = new XMLHttpRequest();

  request.open("POST", "../actions/user_change_department.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result
			alert('You have changed departments - please remember to change back!');
			//alert(this.responseText);
    }
  };

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
}

function uploadDelete(this_id) {
  event.preventDefault()

  var uploadLine = document.getElementById("uploadLine_" + this_id);

  var formData = new FormData();

  formData.append("uploadUID", this_id);

  //https://javascript.info/xmlhttprequest GREAT documentation!
  var request = new XMLHttpRequest();

  request.open("POST", "../actions/file_delete.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result

			alert('File deleted!');
      uploadLine.classList.add("visually-hidden");
    }
  };

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
}




$(document).ready(function(){
$('.toast').toast('show');
















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
