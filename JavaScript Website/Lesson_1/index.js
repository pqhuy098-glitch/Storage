const http = require('http');
const path = require('path');
const fs = require('fs');

const server = http.createServer((req, res) => {
    let filePath;
    let contentType;

    if (req.url === '/' || req.url === '/page_1.html') {
        filePath = path.join(__dirname, 'view', 'page_1.html');
        contentType = 'text/html; charset=utf-8';
    }
    else if (req.url === '/page_2.html') {
        filePath = path.join(__dirname, 'view', 'page_2.html');
        contentType = 'text/html; charset=utf-8';
    }
    else if (req.url === '/style.css') {
        filePath = path.join(__dirname, 'asset', 'style.css');
        contentType = 'text/css; charset=utf-8';
    }
    else {
        return res.end("Page not found");
    }

    fs.readFile(filePath, 'utf-8', (err, data) => {
        if (err) {
            res.end("Cannot file the website");
            return;
        }

        res.end(data);
    });
});

server.listen(3000, () => {
    console.log("SERVER IS RUNNING");
});