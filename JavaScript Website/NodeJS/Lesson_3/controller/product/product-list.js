const getProduct = document.getElementById('product-list');

products.forEach(product => {
    const div = document.createElement('div');
    div.className = 'card';

    div.innerHTML = 
        `
            <p>${product.id}</p>
            <p>${product.name}</p>
            <p>${product.category}</p>
            <p>${product.price}</p>
        `;
    getProduct.appendChild(div);
})