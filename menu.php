<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        color: #333;
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: red;
        color: white;
        text-align: center;
        padding: 20px;
    }

    header h1 {
        margin: 0;
        font-size: 36px;
    }

    .menu-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .menu-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px;
        border-bottom: 1px solid #eee;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .menu-item:last-child {
        border-bottom: none;
    }

    .menu-item:hover {
        transform: scale(1.02);
        background-color: #f9f9f9;
    }

    .item-name {
        font-size: 22px;
        color: #333;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .item-description {
        font-size: 16px;
        color: #666;
        margin-bottom: 10px;
    }

    .item-price {
        font-size: 20px;
        color: red;
        font-weight: bold;
    }

    
    .footer {
        background-color: red;
        color: white;
        text-align: center;
        padding: 15px;
        margin-top: 40px;
    }

    .footer a {
        color: white;
        text-decoration: none;
        margin: 0 10px;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    </style>
</head>
<body>

    <!-- Header Section -->
    <header>
        <h1>Our Menu</h1>
    </header>

    <!-- Menu Content Section -->
    <div class="menu-container">
    <div class="menu-container">
        <!-- Appetizers -->
        <div class="menu-section">
            <h2>Appetizers</h2>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Garlic Bread</p>
                    <p class="item-description">Crispy garlic bread topped with melted butter and fresh herbs.</p>
                </div>
                <p class="item-price">$4.99</p>
            </div>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Chicken Wings</p>
                    <p class="item-description">Choice of spicy or BBQ sauce, served with a side of ranch.</p>
                </div>
                <p class="item-price">$8.99</p>
            </div>
        </div>

        <!-- Soups and Salads -->
        <div class="menu-section">
            <h2>Soups and Salads</h2>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Tomato Basil Soup</p>
                    <p class="item-description">Creamy and rich soup made with fresh tomatoes and basil.</p>
                </div>
                <p class="item-price">$5.49</p>
            </div>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Greek Salad</p>
                    <p class="item-description">Fresh greens topped with feta cheese, olives, and vinaigrette.</p>
                </div>
                <p class="item-price">$8.99</p>
            </div>
        </div>

        <!-- Desserts -->
        <div class="menu-section">
            <h2>Desserts</h2>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Chocolate Lava Cake</p>
                    <p class="item-description">Molten chocolate cake with a gooey center, served with ice cream.</p>
                </div>
                <p class="item-price">$6.99</p>
            </div>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Cheesecake</p>
                    <p class="item-description">Classic cheesecake with a graham cracker crust.</p>
                </div>
                <p class="item-price">$5.99</p>
            </div>
        </div>

        <!-- Beverages -->
        <div class="menu-section">
            <h2>Beverages</h2>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Hot Coffee</p>
                    <p class="item-description">Freshly brewed coffee with your choice of milk and sugar.</p>
                </div>
                <p class="item-price">$2.99</p>
            </div>
            <div class="menu-item">
                <div class="item-details">
                    <p class="item-name">Milkshake</p>
                    <p class="item-description">Available in chocolate, vanilla, or strawberry flavors.</p>
                </div>
                <p class="item-price">$4.99</p>
            </div>
        </div>
        <div class="menu-item">
            <div>
                <p class="item-name">Kenyan Nyama Choma</p>
                <p class="item-description">A Kenyan barbecue dish consisting of grilled marinated beef, goat, or chicken served with a side of ugali or fries.</p>
            </div>
            <p class="item-price">$13.49</p>
        </div>

        <div class="menu-item">
            <div>
                <p class="item-name">Ugali with Sukuma</p>
                <p class="item-description">A staple East African dish consisting of maize flour (ugali) served with sautéed kale (sukuma) and your choice of meat or fish.</p>
            </div>
            <p class="item-price">$9.49</p>
        </div>

        <!-- Menu Item 1 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Cheese Burger</p>
                <p class="item-description">A delicious beef patty topped with cheddar cheese, lettuce, tomato, and our special sauce.</p>
            </div>
            <p class="item-price">$8.99</p>
        </div>

        <!-- Menu Item 2 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Grilled Chicken Salad</p>
                <p class="item-description">Fresh greens topped with grilled chicken, cherry tomatoes, cucumbers, and balsamic vinaigrette.</p>
            </div>
            <p class="item-price">$7.49</p>
        </div>

        <!-- Menu Item 3 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Margherita Pizza</p>
                <p class="item-description">Classic pizza with fresh mozzarella, tomatoes, basil, and a drizzle of olive oil.</p>
            </div>
            <p class="item-price">$12.99</p>
        </div>

        <!-- Menu Item 4 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Spaghetti Bolognese</p>
                <p class="item-description">Traditional Italian pasta served with a rich and savory beef and tomato sauce.</p>
            </div>
            <p class="item-price">$10.99</p>
        </div>

        <!-- Menu Item 5 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Vegetable Stir-fry</p>
                <p class="item-description">A mix of stir-fried vegetables served with a soy-based sauce and jasmine rice.</p>
            </div>
            <p class="item-price">$9.49</p>
        </div>

        <!-- Menu Item 6 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Chicken Alfredo</p>
                <p class="item-description">Creamy Alfredo sauce served over fettuccine pasta with grilled chicken and a sprinkle of parmesan cheese.</p>
            </div>
            <p class="item-price">$13.49</p>
        </div>

        <!-- Menu Item 7 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Fish Tacos</p>
                <p class="item-description">Grilled fish topped with tangy slaw, avocado, and a lime dressing, served in soft corn tortillas.</p>
            </div>
            <p class="item-price">$11.99</p>
        </div>

        <!-- Menu Item 8 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Vegetarian Burger</p>
                <p class="item-description">A delicious plant-based patty topped with lettuce, tomato, onions, and a creamy aioli.</p>
            </div>
            <p class="item-price">$9.99</p>
        </div>

        <!-- Menu Item 9 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Classic Caesar Salad</p>
                <p class="item-description">Crisp romaine lettuce, croutons, parmesan cheese, and Caesar dressing.</p>
            </div>
            <p class="item-price">$6.99</p>
        </div>

        <!-- Menu Item 10 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Penne Arrabbiata</p>
                <p class="item-description">Penne pasta tossed in a spicy tomato sauce with garlic, chili, and fresh basil.</p>
            </div>
            <p class="item-price">$9.49</p>
        </div>

        <!-- Menu Item 11 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Beef Tacos</p>
                <p class="item-description">Seasoned ground beef with lettuce, tomato, cheddar cheese, and sour cream in soft tortillas.</p>
            </div>
            <p class="item-price">$7.99</p>
        </div>

        <!-- Menu Item 12 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Lasagna</p>
                <p class="item-description">Layers of pasta, ricotta cheese, marinara sauce, and ground beef, baked to perfection.</p>
            </div>
            <p class="item-price">$14.99</p>
        </div>

        <!-- Menu Item 13 -->
        <div class="menu-item">
            <div>
                <p class="item-name">Mushroom Risotto</p>
                <p class="item-description">Creamy Arborio rice with wild mushrooms, garlic, and parmesan cheese.</p>
            </div>
            <p class="item-price">$12.49</p>
        </div>

        <!-- New Menu Items -->
        <div class="menu-item">
            <div>
                <p class="item-name">BBQ Ribs</p>
                <p class="item-description">Tender pork ribs glazed with smoky BBQ sauce, served with a side of mashed potatoes and coleslaw.</p>
            </div>
            <p class="item-price">$16.99</p>
        </div>

        <div class="menu-item">
            <div>
                <p class="item-name">Lamb Chops</p>
                <p class="item-description">Grilled lamb chops served with rosemary garlic potatoes and a side of sautéed vegetables.</p>
            </div>
            <p class="item-price">$18.49</p>
        </div>

        
<!-- Menu Item 16 -->
<div class="menu-item">
    <div>
        <p class="item-name">Marinated Grilled Chicken</p>
        <p class="item-description">Tender chicken breast marinated in a blend of herbs and spices, grilled and served with roasted vegetables.</p>
    </div>
    <p class="item-price">$12.99</p>
</div>

<!-- Menu Item 17 -->
<div class="menu-item">
    <div>
        <p class="item-name">Cheese and Garlic Bread</p>
        <p class="item-description">Toasted bread topped with melted cheese and garlic, served with marinara dipping sauce.</p>
    </div>
    <p class="item-price">$5.99</p>
</div>

<!-- Menu Item 18 -->
<div class="menu-item">
    <div>
        <p class="item-name">Tomato Basil Soup</p>
        <p class="item-description">A creamy soup made with fresh tomatoes and basil, perfect as a starter or light meal.</p>
    </div>
    <p class="item-price">$4.99</p>
</div>

<!-- Menu Item 19 -->
<div class="menu-item">
    <div>
        <p class="item-name">Chocolate Lava Cake</p>
        <p class="item-description">A warm, molten chocolate cake with a rich, gooey center, served with vanilla ice cream.</p>
    </div>
    <p class="item-price">$6.99</p>
</div>

<!-- Menu Item 20 -->
<div class="menu-item">
    <div>
        <p class="item-name">Vanilla Bean Ice Cream</p>
        <p class="item-description">A scoop of rich vanilla ice cream, perfect for a sweet treat.</p>
    </div>
    <p class="item-price">$3.99</p>
</div>
</div>
</div> 
                <!-- Footer Section -->
    <div class="footer">
        <p>&copy; 2024 Rhys Restaurant</p>
        <a href="home.php">Home</a>
        <a href="contact.php">Contact Us</a>
    </div>

</body>
</html>