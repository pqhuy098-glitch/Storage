const http = require('http');
const path = require('path');
const fs = require('fs');

const server = http.createServer((req, res) => {
    let filePath;

    res.end("Welcome to lesson 3");
});

server.listen(3000, () => {
    console.log("Server is running");
});