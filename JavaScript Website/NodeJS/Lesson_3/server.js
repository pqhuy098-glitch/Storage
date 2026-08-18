const http = require('http');
const path = require('path');
const fs = require('fs');

const server = http.createServer((req, res) => {
    let filePath;

    if (req.url === '/' || req.url === '/list.html'){
        filePath = path.join(__dirname, 'view', 'product', 'list.html');
    }
    else if (req.url === '/style.css') {
        filePath = path.join(__dirname, 'asset', 'style.css');
    }
    else if (req.url === '/data/products.js') {
        filePath = path.join(__dirname, 'data', 'products.js');
    }
    else if (req.url === '/controller/product/product-list.js') {
        filePath = path.join(__dirname, 'controller', 'product', 'product-list.js');
    }

    fs.readFile(filePath, 'utf-8', (err, data) => {
        if (err){
            res.end("The website not found");
        }
        res.end(data);
    });
});

server.listen(3000, () => {
    console.log("Server is running");
});