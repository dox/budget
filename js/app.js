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
