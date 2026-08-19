import { products } from '/data/product.js';

const productCard = document.getElementById('products-list');
let content = '';

products.forEach(product => {
    content += 
    `   <div class="card">
            <p>id: ${product.id}</p>
            <p>name: ${product.name}</p>
            <a href="/public/product/detail.html?id=${product.id}" class="btn">More detail</a>
        </div>
    `;
});

productCard.innerHTML = content;

