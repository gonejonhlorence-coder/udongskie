const form = document.getElementById("feedbackForm");

form.addEventListener("submit", function(event) {
    event.preventDefault();

    // Get input values
    const name = document.getElementById("nameInput").value.trim();
    const email = document.getElementById("emailInput").value.trim();
    const donut = document.getElementById("donutSelect").value;
    const rating = document.getElementById("ratingSelect").value;
    const feedback = document.getElementById("feedbackTextarea").value.trim();

    const errorMsg = document.getElementById("errorMsg");
    const output = document.getElementById("outputContainer");

    // Check if all fields are filled
    if (name === "" || email === "" || donut === "" || rating === "" || feedback === "") {
        errorMsg.textContent = "Please fill in all fields.";
        output.classList.add("hidden");
        return;
    }

    // Show submitted information
    document.getElementById("outName").textContent = name;
    document.getElementById("outEmail").textContent = email;
    document.getElementById("outDonut").textContent = donut;
    document.getElementById("outRating").textContent = rating + " Stars";
    document.getElementById("outFeedback").textContent = feedback;

    // Hide error message
    errorMsg.textContent = "";

    // Show feedback result
    output.classList.remove("hidden");

    // Clear the form
    form.reset();
});