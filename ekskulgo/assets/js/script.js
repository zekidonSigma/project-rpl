const dropdownBtn = document.getElementById("userDropdownBtn");
      const dropdownMenu = document.getElementById("userDropdown");

      dropdownBtn.addEventListener("click", () => {
        dropdownMenu.classList.toggle("active");
      });

      window.addEventListener("click", (e) => {
        if (
          !dropdownBtn.contains(e.target) &&
          !dropdownMenu.contains(e.target)
        ) {
          dropdownMenu.classList.remove("active");
        }
    });
