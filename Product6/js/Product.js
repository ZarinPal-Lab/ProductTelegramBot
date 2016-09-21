
$(document).ready(function() {
	
	$("#group").change(function(event){
	  var group = $("select#group option:selected").val();
	  if(group > 0){
          $.get( 
             "temp.php?action=group",
             { name: group },
             function(data) {
				var datas = data.split(",,,");
				var i = 0;
				var $el = $("#product");
				$el.empty(); // remove old options
				$el.append($("<option></option>")
					 .attr("value", 0).text('- None -'));
				
				while(datas[i].length > 0)
				{
					$el.append($("<option></option>")
					 .attr("value", datas[i]).text(datas[i+1]));
					 i++;
					 i++;
				}
				
             }

          );
		}

      });
	  
});
