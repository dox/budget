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
});
