const validType = ["image/jpeg", "image/png", "image/jpg"];

function onChange(event) {
  const file = event.target.files[0];
  if (validType.some((f) => f === file.type)) {
    const imageMin = document.getElementById("image");
    imageMin.src = window.URL.createObjectURL(file);
  }
}
