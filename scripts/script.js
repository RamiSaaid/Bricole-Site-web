document.addEventListener("DOMContentLoaded", function() {
    // Dropdown Menu functionality
    let connexionLink = document.querySelector(".connexion");
    let dropdownMenu = document.querySelector(".dropdown-cnx");

    connexionLink.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        dropdownMenu.classList.toggle("show-dropdown"); // Toggle visibility of the dropdown
    });

    // Clicking outside the dropdown closes it
    document.addEventListener("click", function(event) {
        if (!dropdownMenu.contains(event.target) && !connexionLink.contains(event.target)) {
            dropdownMenu.classList.remove("show-dropdown");
        }
    });

    // Accordion functionality
    const accBtns = document.querySelectorAll('.acc-btn');
    accBtns.forEach(accBtn => {
        accBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
});


$(".hover").mouseleave(
    function() {
      $(this).removeClass("hover");
    }
  );




  function openForm(formId) {
    document.getElementById(formId).style.display = 'block';
}

function closeForm(formId) {
    document.getElementById(formId).style.display = 'none';
}

