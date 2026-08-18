const getProduct = document.getElementById('product-list');

products.forEach(product => {
    const div = document.createElement('div');
    div.className = 'card';

    div.innerHTML = 
        `
            <p>#${product.id}</p>
            <h3><strong>${product.name}</strong></h3>
            <p><strong>Category:</strong> ${product.category}</p>
            <p><strong>Price:</strong> ${product.price}</p>
            <button class="btn">More detail</button>
            <button class="btn">Order</button>
        `;
    getProduct.appendChild(div);
})