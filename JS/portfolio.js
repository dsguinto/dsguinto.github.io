window.onload = function () {
  //Toggles hamburger nav menu
  var hamIcon = document.getElementById("hamIcon");

  function hamFunction() {
    var x = document.getElementById("myLinks");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

  hamIcon.onclick = function () {
    hamFunction();
  };

  //Toggles Back to Top Btn
  const toTop = document.querySelector(".to-top");

  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 250) {
      toTop.classList.add("active");
    } else {
      toTop.classList.remove("active");
    }
  });
};
