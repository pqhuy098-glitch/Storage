import {products} from '/data/product.js';

export let categories = [...new Set(products.map(p => p.category))];
