function placeOrder() {
    const name = document.getElementById("name").value.trim();
    const food = document.getElementById("food");
    const quantity = document.getElementById("quantity").value;
    const payment = document.getElementById("payment").value;
    const selectedFood = food.options[food.selectedIndex];
    const price = selectedFood.getAttribute("data-price");

    if (name === "") {
        alert("Please enter your name.");
        return;
    }
    if (food.value === "") {
        alert("Please select a food.");
        return;
    }
    if (quantity < 1) {
        alert("Quantity must be at least 1.");
        return;
    }
    if (payment === "") {
        alert("Please select a payment method.");
        return;
    }

    const total = Number(price) * Number(quantity);
    const output = document.getElementById("output");
    const outputContent = document.getElementById("outputContent");

    outputContent.innerHTML = `
        <p class="success">✅ Order successfully placed!</p>
        <p><strong>Customer Name:</strong> ${name}</p>
        <p><strong>Food:</strong> ${food.value}</p>
        <p><strong>Quantity:</strong> ${quantity}</p>
        <p><strong>Price:</strong> ₱${price}</p>
        <p><strong>Payment Method:</strong> ${payment}</p>
        <p><strong>Total:</strong> ₱${total.toFixed(2)}</p>
    `;

    output.style.display = "block";
    output.scrollIntoView({ behavior: "smooth" });
}

function submitFeedback() {
    const name = document.getElementById("feedback-name").value.trim();
    const email = document.getElementById("email").value.trim();
    const rating = document.getElementById("rating").value;
    const date = document.getElementById("date").value;
    const comments = document.getElementById("comments").value.trim();

    if (name === "") {
        alert("Please enter your name.");
        return;
    }
    if (email === "") {
        alert("Please enter your email.");
        return;
    }
    if (rating === "") {
        alert("Please select a rating.");
        return;
    }
    if (date === "") {
        alert("Please select the date of your visit.");
        return;
    }
    if (comments === "") {
        alert("Please enter your feedback.");
        return;
    }

    const output = document.getElementById("output");
    const outputContent = document.getElementById("outputContent");

    outputContent.innerHTML = `
        <p class="success">✅ Thank you for your feedback!</p>
        <p><strong>Name:</strong> ${name}</p>
        <p><strong>Email:</strong> ${email}</p>
        <p><strong>Rating:</strong> ${rating}</p>
        <p><strong>Date of Visit:</strong> ${date}</p>
        <p><strong>Comments:</strong> ${comments}</p>
    `;

    output.style.display = "block";
    output.scrollIntoView({ behavior: "smooth" });
}