
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Product Listing</h1>
    </header>
    <div class="container">
        <aside class="sidebar">
            <!-- Filters form -->
            <form id="filtersForm">
                <div class="filter-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category">
                        <option value="">All</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Books">Books</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price_min">Min Price:</label>
                    <input type="number" name="price_min" id="price_min" step="0.01">
                </div>
                <div class="filter-group">
                    <label for="price_max">Max Price:</label>
                    <input type="number" name="price_max" id="price_max" step="0.01">
                </div>
                <div class="filter-group">
                    <label for="sale_status">Sale Status:</label>
                    <select name="sale_status" id="sale_status">
                        <option value="">All</option>
                        <option value="1">On Sale</option>
                        <option value="0">Not on Sale</option>
                    </select>
                </div>
                <button type="submit">Apply Filters</button>
            </form>
        </aside>
        <main class="products-section">
            <div id="products" class="products"></div>
            <div id="pagination"></div>
        </main>
    </div>

    <script>
        const applyFilters = (page = 1) => {
            const form = document.getElementById('filtersForm');
            const formData = new FormData(form);
            formData.append('page', page);

            fetch('get_products.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const productDiv = document.getElementById('products');
                productDiv.innerHTML = '';

                if (data.products.length === 0) {
                    productDiv.innerHTML = '<p>No items found.</p>';
                    return;
                }

                data.products.forEach(product => {
                    const productElement = document.createElement('div');
                    productElement.className = 'product';
                    productElement.innerHTML = `
                        <div class="product-image">
                            <img src="${product.image_url}" alt="${product.name}">
                        </div>
                        <div class="product-details">
                            <h3 class="product-name">${product.name}</h3>
                            <p class="product-category">Category: ${product.category}</p>
                            <p class="product-price">Price: <span class="price-bold">$${product.price}</span></p>
                            <p class="product-status">Status: ${product.sale_status ? 'On Sale' : 'Not on Sale'}</p>
                        </div>
                    `;
                    productDiv.appendChild(productElement);
                });

                const paginationDiv = document.getElementById('pagination');
                paginationDiv.innerHTML = '';
                if (data.total === 0) {
                    paginationDiv.innerHTML = '<p>No items found.</p>';
                } else {
                    const totalPages = Math.ceil(data.total / 12); // 12 products per page
                    for (let i = 1; i <= totalPages; i++) {
                        const pageElement = document.createElement('button');
                        pageElement.textContent = i;
                        pageElement.onclick = () => applyFilters(i);
                        paginationDiv.appendChild(pageElement);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        };

        // Submit form on initial load
        document.addEventListener('DOMContentLoaded', () => {
            applyFilters();
            const form = document.getElementById('filtersForm');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                applyFilters();
            });
        });
    </script>
</body>
</html>
