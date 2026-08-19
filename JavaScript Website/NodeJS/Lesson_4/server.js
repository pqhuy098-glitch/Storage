const http = require('http');
const path = require('path');
const fs = require('fs');

const port = 3000;
const host = '127.0.0.1';
const mine_types = {
    '.html' : 'text/html; charset=utf-8',
    '.css' : 'text/css; charset=utf-8',
    '.js' : 'application/javascript; charset=utf-8',
}

const server = http.createServer((req, res) => {
    let urlPath;
    let filePath;
    let extPath;
    let content_type;
    let pathname;
    pathname = new URL(req.url, `http://${req.headers.host}`);
    
    if (pathname.pathname === '/') {
        urlPath = '/public/product/index.html';
    }
    else {
        urlPath = pathname.pathname;
    }

    extPath = path.extname(urlPath);
    content_type = mine_types[extPath];

    filePath = path.join(__dirname, urlPath);

    fs.readFile(filePath, 'utf-8', (err, data) => {
        if (err) {
            res.writeHead(404, {'Content-Type' : 'text/plain; charset=utf-8'});
            res.end('404 Not Found');
            return;
        }

        res.writeHead(200, {'Content-Type' : content_type});
        res.end(data);
    });
});

server.listen(port, host, () => {
    console.log("Server is running");
});