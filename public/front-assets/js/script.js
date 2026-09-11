document.addEventListener("click", function(e) {
  if (e.target.classList.contains("plus")) {
    let input = e.target.parentElement.querySelector(".qty");
    input.value = parseInt(input.value) + 1;
  }

  if (e.target.classList.contains("minus")) {
    let input = e.target.parentElement.querySelector(".qty");
    input.value = Math.max(0, parseInt(input.value) - 1);
  }

  if (e.target.classList.contains("remove")) {
    let input = e.target.parentElement.querySelector(".qty");
    input.value = 0;
  }
});
