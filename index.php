<?php
require_once "db.php";

$message = "";
$messageType = "success";

function clean($value) {
    return trim((string)($value ?? ""));
}

$prices = [
    "Cheeseburger" => 99,
    "Chicken Meal" => 129,
    "Pizza" => 149
];

/* CUSTOMER REGISTRATION */
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["form_type"] ?? "") === "register") {
    $name = clean($_POST["name"] ?? "");
    $email = clean($_POST["email"] ?? "");

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid name and email.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO simpletb (Name, Email) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            $message = "Customer registered successfully!";
        } else {
            $message = "Unable to save customer registration.";
            $messageType = "error";
        }
        $stmt->close();
    }
}

/* ORDER FORM */
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["form_type"] ?? "") === "order") {
    $cart = json_decode($_POST["cart_data"] ?? "", true);
    $payment = clean($_POST["payment_method"] ?? "");
    $allowedPayments = ["Cash", "GCash", "Card"];

    if (!is_array($cart) || empty($cart) ||
        !in_array($payment, $allowedPayments, true)) {
        $message = "Please add an item and select a payment method.";
        $messageType = "error";
    } else {
        $items = [];
        $quantity = 0;
        $total = 0;

        foreach ($cart as $item) {
            $itemName = clean($item["name"] ?? "");
            $qty = filter_var($item["qty"] ?? 0, FILTER_VALIDATE_INT);

            if (isset($prices[$itemName]) &&
                $qty !== false && $qty > 0 && $qty <= 50) {
                $items[] = $itemName . " x " . $qty;
                $quantity += $qty;
                $total += $prices[$itemName] * $qty;
            }
        }

        if (empty($items)) {
            $message = "Your order is empty.";
            $messageType = "error";
        } else {
            $itemSummary = implode(", ", $items);
            $stmt = $conn->prepare(
                "INSERT INTO orders (items, quantity, total, payment_method)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("sids", $itemSummary, $quantity, $total, $payment);

            if ($stmt->execute()) {
                $message = "Order saved successfully! Total: ₱" .
                    number_format($total, 2);
            } else {
                $message = "Unable to save your order.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }
}

/* FEEDBACK FORM */
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["form_type"] ?? "") === "feedback") {
    $feedbackName = clean($_POST["feedback_name"] ?? "");
    $feedbackEmail = clean($_POST["feedback_email"] ?? "");
    $rating = filter_var($_POST["rating"] ?? 0, FILTER_VALIDATE_INT);
    $visitDate = clean($_POST["visit_date"] ?? "");
    $comments = clean($_POST["comments"] ?? "");

    if ($feedbackName === "" ||
        !filter_var($feedbackEmail, FILTER_VALIDATE_EMAIL) ||
        $rating === false || $rating < 1 || $rating > 5 ||
        $visitDate === "" || $comments === "") {
        $message = "Please complete all feedback fields correctly.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO feedback (name, email, rating, visit_date, comments)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssiss",
            $feedbackName,
            $feedbackEmail,
            $rating,
            $visitDate,
            $comments
        );

        if ($stmt->execute()) {
            $message = "Thank you! Your feedback has been saved.";
        } else {
            $message = "Unable to save feedback.";
            $messageType = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Udongs BurgerKing</title>

<style>
:root {
    --orange: #e87524;
    --dark: #292522;
    --cream: #fffaf3;
    --line: #eadfd2;
    --muted: #716a63;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: var(--cream);
    color: var(--dark);
}

header {
    background: var(--dark);
    color: white;
    padding: 20px 6%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.logo {
    font-size: 25px;
    font-weight: bold;
}

.logo span { color: #ffad62; }

nav {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
}

nav a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
}

nav a:hover { color: #ffad62; }

.hero {
    background: #ffebd5;
    padding: 45px 6%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

.hero h1 {
    font-size: clamp(32px, 5vw, 48px);
    margin: 0 0 12px;
}

.hero p {
    color: #66574b;
    line-height: 1.7;
    max-width: 550px;
}

.hero-icon {
    font-size: 85px;
    text-align: center;
    min-width: 180px;
}

main {
    width: 92%;
    max-width: 1100px;
    margin: 30px auto 55px;
}

.section-title { margin: 35px 0 18px; }

.section-title h2 {
    font-size: 27px;
    margin: 0 0 7px;
}

.section-title p {
    color: var(--muted);
    margin: 0;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.card, .panel {
    background: white;
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 5px 16px #4b2c100c;
}

.food-icon {
    background: #fff0df;
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    font-size: 48px;
    margin-bottom: 14px;
}

.card h3 { margin: 0 0 8px; }

.card p {
    color: var(--muted);
    font-size: 14px;
    min-height: 35px;
}

.menu-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.price {
    font-weight: bold;
    color: #bd5015;
    font-size: 19px;
}

button {
    border: none;
    border-radius: 8px;
    padding: 11px 15px;
    background: var(--orange);
    color: white;
    font-weight: bold;
    cursor: pointer;
}

button:hover { background: #bd5015; }

.layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}

.panel h2 { margin-top: 0; }

.field { margin-bottom: 15px; }

label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 7px;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #d9cec1;
    border-radius: 8px;
    font: inherit;
    background: white;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

.full-button { width: 100%; }

.cart-empty {
    padding: 18px;
    text-align: center;
    color: var(--muted);
    background: #fff8ef;
    border-radius: 8px;
}

.cart-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--line);
}

.qty-controls {
    display: flex;
    align-items: center;
    gap: 7px;
}

.qty-controls button { padding: 6px 10px; }

.remove-btn {
    background: #eee5dc;
    color: var(--dark);
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 18px 0;
    font-size: 20px;
    font-weight: bold;
}

.notice {
    padding: 14px;
    border-radius: 8px;
    margin: 20px 0;
    background: #e7f6e9;
    color: #205d2d;
}

.notice.error {
    background: #fff0ed;
    color: #922d20;
}

footer {
    background: var(--dark);
    color: white;
    text-align: center;
    padding: 22px;
}

@media (max-width: 760px) {
    .hero { flex-direction: column; align-items: flex-start; }
    .hero-icon { align-self: center; }
    .menu-grid, .layout { grid-template-columns: 1fr; }
    header { padding: 18px 5%; }
}
</style>
</head>

<body>
<header>
    <div class="logo">Udongs <span>BurgerKing</span></div>
    <nav>
        <a href="#menu">Menu</a>
        <a href="#order">Order</a>
        <a href="#registration">Registration</a>
        <a href="#feedback">Feedback</a>
    </nav>
</header>

<section class="hero">
    <div>
        <h1>Good Food.<br>Good Mood.</h1>
        <p>Welcome to Udongs BurgerKing! Choose your favorite food, place your order, and share your experience with us.</p>
        <a href="#menu" style="display:inline-block;background:#e87524;color:white;text-decoration:none;padding:12px 16px;border-radius:8px;font-weight:bold;">View Our Menu</a>
    </div>
    <div class="hero-icon">🍔 🍟</div>
</section>

<main>
<?php if ($message !== ""): ?>
    <div class="notice <?php echo $messageType === "error" ? "error" : ""; ?>">
        <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
    </div>
<?php endif; ?>

<section id="menu">
    <div class="section-title">
        <h2>Our Menu</h2>
        <p>Choose your favorite meal.</p>
    </div>

    <div class="menu-grid">
        <article class="card">
            <div class="food-icon">🍔</div>
            <h3>Cheeseburger</h3>
            <p>Juicy burger with cheese and fresh toppings.</p>
            <div class="menu-bottom">
                <span class="price">₱99</span>
                <button type="button" onclick="addItem('Cheeseburger')">Add to Order</button>
            </div>
        </article>

        <article class="card">
            <div class="food-icon">🍗</div>
            <h3>Chicken Meal</h3>
            <p>Crispy chicken for a satisfying meal.</p>
            <div class="menu-bottom">
                <span class="price">₱129</span>
                <button type="button" onclick="addItem('Chicken Meal')">Add to Order</button>
            </div>
        </article>

        <article class="card">
            <div class="food-icon">🍕</div>
            <h3>Pizza</h3>
            <p>Cheesy pizza made for sharing.</p>
            <div class="menu-bottom">
                <span class="price">₱149</span>
                <button type="button" onclick="addItem('Pizza')">Add to Order</button>
            </div>
        </article>
    </div>
</section>

<section id="order">
    <div class="section-title">
        <h2>Order Form</h2>
        <p>Add food, adjust quantities, and choose how to pay.</p>
    </div>

    <div class="panel">
        <div id="cart-items">
            <div class="cart-empty">Your cart is empty. Add an item from the menu.</div>
        </div>
        <div class="total-row">
            <span>Total</span>
            <span id="cart-total">₱0.00</span>
        </div>

        <form method="post" id="order-form">
            <input type="hidden" name="form_type" value="order">
            <input type="hidden" name="cart_data" id="cart-data" value="">

            <div class="field">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select payment method</option>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="Card">Card</option>
                </select>
            </div>

            <button class="full-button" type="submit">Place Order</button>
        </form>
    </div>
</section>

<section id="registration">
    <div class="section-title">
        <h2>Customer Registration</h2>
        <p>Register your details below.</p>
    </div>

    <div class="panel">
        <form method="post">
            <input type="hidden" name="form_type" value="register">

            <div class="field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <button class="full-button" type="submit">Register Customer</button>
        </form>
    </div>
</section>

<section id="feedback">
    <div class="section-title">
        <h2>Customer Feedback</h2>
        <p>Tell us about your experience.</p>
    </div>

    <div class="panel">
        <form method="post">
            <input type="hidden" name="form_type" value="feedback">

            <div class="layout">
                <div>
                    <div class="field">
                        <label for="feedback_name">Name</label>
                        <input type="text" id="feedback_name" name="feedback_name" placeholder="Your name" required>
                    </div>

                    <div class="field">
                        <label for="feedback_email">Email Address</label>
                        <input type="email" id="feedback_email" name="feedback_email" placeholder="Your email" required>
                    </div>

                    <div class="field">
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" required>
                            <option value="">Choose a rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Needs Improvement</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="field">
                        <label for="visit_date">Date of Visit</label>
                        <input type="date" id="visit_date" name="visit_date" required>
                    </div>

                    <div class="field">
                        <label for="comments">Comments</label>
                        <textarea id="comments" name="comments" placeholder="Write your feedback here..." required></textarea>
                    </div>
                </div>
            </div>

            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</section>
</main>

<footer>Udongs BurgerKing · Thank you for visiting!</footer>

<script>
const prices = {
    "Cheeseburger": 99,
    "Chicken Meal": 129,
    "Pizza": 149
};

const cart = {};

function addItem(name) {
    cart[name] = (cart[name] || 0) + 1;
    renderCart();
    document.getElementById("order").scrollIntoView({behavior: "smooth"});
}

function changeQty(name, amount) {
    cart[name] = (cart[name] || 0) + amount;
    if (cart[name] <= 0) delete cart[name];
    renderCart();
}

function removeItem(name) {
    delete cart[name];
    renderCart();
}

function renderCart() {
    const container = document.getElementById("cart-items");
    const names = Object.keys(cart);
    let total = 0;

    if (names.length === 0) {
        container.innerHTML = '<div class="cart-empty">Your cart is empty. Add an item from the menu.</div>';
    } else {
        container.innerHTML = "";

        names.forEach(name => {
            const qty = cart[name];
            const subtotal = prices[name] * qty;
            total += subtotal;

            const row = document.createElement("div");
            row.className = "cart-row";

            const details = document.createElement("div");
            details.innerHTML =
                '<strong>' + name + '</strong><div style="color:#716a63;font-size:13px;margin-top:4px;">₱' +
                prices[name] + ' each · Subtotal ₱' + subtotal.toFixed(2) + '</div>';

            const controls = document.createElement("div");
            controls.className = "qty-controls";

            const minus = document.createElement("button");
            minus.type = "button";
            minus.textContent = "−";
            minus.onclick = () => changeQty(name, -1);

            const count = document.createElement("strong");
            count.textContent = qty;

            const plus = document.createElement("button");
            plus.type = "button";
            plus.textContent = "+";
            plus.onclick = () => changeQty(name, 1);

            const remove = document.createElement("button");
            remove.type = "button";
            remove.textContent = "×";
            remove.className = "remove-btn";
            remove.onclick = () => removeItem(name);

            controls.append(minus, count, plus, remove);
            row.append(details, controls);
            container.appendChild(row);
        });
    }

    document.getElementById("cart-total").textContent = "₱" + total.toFixed(2);
    document.getElementById("cart-data").value = JSON.stringify(
        names.map(name => ({name: name, qty: cart[name]}))
    );
}

document.getElementById("order-form").addEventListener("submit", function(event) {
    if (Object.keys(cart).length === 0) {
        event.preventDefault();
        alert("Please add at least one item before placing your order.");
    }
});
</script>
</body>
</html>
