function showOutput(message) {
const output = document.getElementById("output");
const outputContent = document.getElementById("outputContent");

outputContent.innerHTML = message;
output.style.display = "block";
}

function placeOrder() {
const name = document.getElementById("name").value.trim();
const food = document.getElementById("food");
const quantity = Number(document.getElementById("quantity").value);
const payment = document.getElementById("payment").value;
const selectedFood = food.options[food.selectedIndex];

if (!name || !food.value || quantity < 1 || !payment) {
showOutput("Please complete all order fields.");
return;
}

const price = Number(selectedFood.dataset.price);
const total = price * quantity;

showOutput(`
<strong>Thank you, ${name}!</strong><br>
Food: ${food.value}<br>
Quantity: ${quantity}<br>
Total: ₱${total}<br>
Payment: ${payment}
`);
}

function submitFeedback() {
const name = document.getElementById("feedback-name").value.trim();
const email = document.getElementById("email").value.trim();
const rating = document.getElementById("rating").value;
const date = document.getElementById("date").value;
const comments = document.getElementById("comments").value.trim();

if (!name || !email || !rating || !date || !comments) {
showOutput("Please complete all feedback fields.");
return;
}

showOutput(`
<strong>Thank you for your feedback, ${name}!</strong><br>
Email: ${email}<br>
Rating: ${rating}<br>
Date of Visit: ${date}<br>
Comments: ${comments}
`);
}