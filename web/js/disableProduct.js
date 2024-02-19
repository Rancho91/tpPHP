function toggleProductState(path, el) {
    $.ajax({
      url: path,
      contentType: "application/json",
      success: function (ret) {
        if (ret.success) {
          if (ret.state == "enabled") {
            $(el).removeClass("btn-success");
            $(el).addClass("btn-danger");
            $(el).html("Disable");
          } else {
            $(el).removeClass("btn-danger");
            $(el).addClass("btn-success");
            $(el).html("Enable");
          }
        }
      },
    });
  }