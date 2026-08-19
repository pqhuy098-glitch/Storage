import { products } from '/data/product.js';

const showProduct = document.getElementById('product-detail');
let content = '';

const queryString = window.location.search;
const param = new URLSearchParams(queryString);
const productID = param.get('id');

products.forEach(product => {
    if (product.id == productID) {
        content = 
            `
                <p>id: ${product.id}</p>
                <p>id: ${product.name}</p>
            `;
    }
});

showProduct.innerHTML = content;
