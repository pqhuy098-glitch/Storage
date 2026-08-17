const http = require('http');
const path = require('path');
const fs = require('fs');

const server = http.createServer((req, res) => {
    let filePath;

    if (req.url === '/' || req.url === '/list.html'){
        filePath = path.join(__dirname, 'view', 'list.html');
    }
    else if (req.url === '/data/products.js') {
        filePath = path.join(__dirname, 'data', 'products.js');
    }
    else if (req.url === '/script.js') {
        filePath = path.join(__dirname, 'script.js');
    }
    
    fs.readFile(filePath, 'utf-8', (err, data) => {
        if (err) {
            res.end("CANNOT FOUND THE WEBSITE");
            return;
        };
        res.end(data);
    });
});

server.listen(3000, () => {
    console.log("SERVER IS RUNNING");
})