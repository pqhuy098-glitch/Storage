import {products} from '/data/product.js';
import {categories} from '/data/category.js';

const productList = document.getElementById('product-list');
let content = '';

categories.forEach(category => {
    content +=
    `
    <div class="product-each-category">
        <h1>${category}</h1>
        <div class="show-card">
    `;

    products.forEach(product => {
        if (product.category === category) {
            content +=
            `
            <div class="card">
                <p>#${product.id}</p>
                <h3><strong>${product.name}</strong></h3>
                <p><strong>quantity:</strong> ${product.quantity}</p>
                <p><strong>price:</strong> ${product.price}</p>
                <a href="/public/product/src/detail.html?id=${product.id}" class="btn">More detail</a>
            </div>
            
            `
        }
    });

    content +=
    `
        </div>
    </div>
    `
});

productList.innerHTML = content;