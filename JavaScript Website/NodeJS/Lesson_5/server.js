const http = require('http');
const path = require('path');
const fs = require('fs');
const port = 3000;
const host = '127.0.0.1';

const type = {
    '.html': 'text/html; charset=utf-8',
    '.css': 'text/css; charset=utf-8',
    '.js': 'application/javascript; charset=utf-8'
}

const server = http.createServer((req, res) => {
    let url;
    let filePath;
    let extUrl;
    let contentType;
    let parseUrl = new URL(req.url, `http://${req.headers.host}`);

    if (parseUrl.pathname === '/') {
        url = '/public/product/src/index.html';
    }
    else {
        url = parseUrl.pathname;
    }

    extUrl = path.extname(url);
    contentType = type[extUrl];

    filePath = path.join(__dirname, url);
    fs.readFile(filePath, 'utf-8', (err, data) => {
        if (err) {
            res.writeHead(404, {'Content-Type': 'text/plain; charset=utf-8'});
            res.end('404 Not Found');
            return
        }
        res.writeHead(200, {'Content-Type': contentType});
        res.end(data);
    });
});

server.listen(port, host, () => {
    console.log('Server is running');
});