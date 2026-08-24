import {products} from '/data/product.js';

const productDetail = document.getElementById('product-detail');
const param = new URLSearchParams(window.location.search);
const productID = param.get('id');

let content = '';

products.forEach(product => {
    if (product.id == productID) {
        content =
        `
            <p>ID: ${product.id}</p>
            <p>ID: ${product.name}</p>
        `
    }
});

productDetail.innerHTML = content;