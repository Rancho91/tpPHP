function filterProduct(url,select, urlImnage) {
    var selectedOptionValue = $(select).val();
    console.log(url+"/"+selectedOptionValue);

    $.ajax({
        url: url+"/"+selectedOptionValue,
        method: "GET",
        success: function (response) {
          console.log(response);
          $("#containerCard").empty();
          response.state.forEach(function(product) {
            var cardHtml = `<div class="col-md-4 mb-4">
                                    <div class="card bg-dark" style="width: 18rem">
                                        <div style="background-image: url('${urlImnage}/${product.image}');
                                                    background-size: cover;
                                                    background-position: center;
                                                    width: 100%;
                                                    height: 300px;
                                                    border: 1px solid #ccc;"></div>
                                        <div class="card-body">
                                            <h1 class="card-title text-white" style="font-size: 1.5rem;">${product.producto}</h1>
                                        </div>
                                    </div>
                                </div>`;
             $("#containerCard").append(cardHtml);
          });
        },
        error: function (xhr, status, error) {
          console.error(error);
        },
      });


}



$(document).ready(function () {
  $("#category-select").change(function () {
    var categoryId = $(this).val();
    console.log(categoryId)
    
    $.ajax({
      url: "ruta/del/servicio",
      method: "GET",
      data: { categoryId: categoryId },
      success: function (response) {
        console.log(response);
      },
      error: function (xhr, status, error) {
        console.error(error);
      },
    });
  });
});
