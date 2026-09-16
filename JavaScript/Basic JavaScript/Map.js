const array = [1, 4, 5, 7];

const doubled = array.map(arr => {
    return arr * 2;
});

console.log(doubled);

const user = [
    {
        name: 'basic laptop',
        category: 'laptop'
    },
    {
        name: 'pro laptop',
        category: 'laptop'
    },
    {
        name: 'basic phone',
        category: 'phone'
    },
];

const resutl = user.map(u => {
    return u.name;
});

console.log(resutl);