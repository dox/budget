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

  if (window.confirm("Are you sure you want to delete this file?  This action cannot be undone.")) {
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

        uploadLine.classList.add("visually-hidden");
      }
    };

    request.onerror = function() {
      alert("Request failed");
    };
  }


  return false;
}

function createOrder(this_id) {
  event.preventDefault()

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation');

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms).forEach(function (form) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      } else {
        var date = document.getElementById("date").value;
        var cost_centre = document.getElementById("cost_centre").value;
        var po = document.getElementById("po").value;
        var order_num = document.getElementById("order_num").value;
        var name = document.getElementById("name").value;
        var value = document.getElementById("value").value;
        var supplier = document.getElementById("supplier").value;
        var description = document.getElementById("description").value;

        var formData = new FormData();

        formData.append("date", date);
        formData.append("cost_centre", cost_centre);
        formData.append("po", po);
        formData.append("order_num", order_num);
        formData.append("name", name);
        formData.append("value", value);
        formData.append("supplier", supplier);
        formData.append("description", description);

        //https://javascript.info/xmlhttprequest GREAT documentation!
        var request = new XMLHttpRequest();

        request.open("POST", "../actions/order_create.php", true);
        request.send(formData);

        // 4. This will be called after the response is received
        request.onload = function() {
          if (request.status != 200) { // analyze HTTP status of the response
            alert("Something went wrong.  Please refresh this page and try again.");
            alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
          } else { // show the result
            alert("Order added!");
          }
        };

        request.onerror = function() {
          alert("Request failed");
        };
      }

      form.classList.add('was-validated')

  });





  return false;
}
