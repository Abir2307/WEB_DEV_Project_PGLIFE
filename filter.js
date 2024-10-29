document.addEventListener("DOMContentLoaded", () => {
    const filterButtons = document.querySelectorAll(".modal-body .btn-outline-dark");
    const highestRentButton = document.querySelector(".filter-bar .col-auto:nth-child(2)");
    const lowestRentButton = document.querySelector(".filter-bar .col-auto:nth-child(3)");
    const propertyCards = document.querySelectorAll(".property-card");

    let selectedFilter = "No Filter";

    // Filter by gender
    filterButtons.forEach(button => {
        button.addEventListener("click", () => {
            filterButtons.forEach(btn => btn.classList.remove("btn-active"));
            button.classList.add("btn-active");
            selectedFilter = button.textContent.trim();
            applyFilters();
        });
    });

    // Sort by highest rent
    highestRentButton.addEventListener("click", () => {
        sortProperties("desc");
    });

    // Sort by lowest rent
    lowestRentButton.addEventListener("click", () => {
        sortProperties("asc");
    });

    // Toggle interested (heart icon)
    propertyCards.forEach(card => {
        const heartIcon = card.querySelector(".interested-container i");
        heartIcon.addEventListener("click", () => {
            toggleInterested(heartIcon, card.dataset.propertyId);
        });
    });

    // Function to apply filters
    function applyFilters() {
        propertyCards.forEach(card => {
            const gender = card.querySelector(".property-gender img").alt.toLowerCase();
            if (selectedFilter === "No Filter" || selectedFilter.toLowerCase() === gender) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Function to sort properties by rent
    function sortProperties(order) {
        const container = document.querySelector(".page-container");
        const sortedCards = Array.from(propertyCards).sort((a, b) => {
            const rentA = parseInt(a.querySelector(".rent-container .rent").textContent.replace(/[₹,/-]/g, ""));
            const rentB = parseInt(b.querySelector(".rent-container .rent").textContent.replace(/[₹,/-]/g, ""));
            return order === "asc" ? rentA - rentB : rentB - rentA;
        });

        sortedCards.forEach(card => container.appendChild(card));
    }

    // Function to toggle interested state
    function toggleInterested(heartIcon) {
        const propertyId = heartIcon.getAttribute("data-property-id"); // Now fetching property ID from data attribute
        const isInterested = heartIcon.classList.contains("fas");
    
        // Optimistically toggle heart icon style
        heartIcon.classList.toggle("fas", !isInterested);
        heartIcon.classList.toggle("far", isInterested);
    
        // Make the AJAX request
        $.post("toggle_interest.php", { property_id: propertyId }, (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === "success") {
                    document.querySelector(".interested-user-count").innerText = data.interested_count;
                } else {
                    // Revert heart icon if failed
                    heartIcon.classList.toggle("fas", isInterested);
                    heartIcon.classList.toggle("far", !isInterested);
                    alert(data.message || "Failed to update interest.");
                }
            } catch (e) {
                // Revert heart icon if JSON parse fails
                heartIcon.classList.toggle("fas", isInterested);
                heartIcon.classList.toggle("far", !isInterested);
                alert("Unexpected error: " + e.message);
            }
        }).fail(() => {
            // Revert heart icon if AJAX fails
            heartIcon.classList.toggle("fas", isInterested);
            heartIcon.classList.toggle("far", !isInterested);
            alert("Failed to update interest.");
        });
    }        
});
