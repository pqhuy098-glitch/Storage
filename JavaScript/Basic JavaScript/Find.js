const array = [1, 4, 5, 7];

const findNumber = array.find(arr => {
    return arr == 4;
});

console.log(findNumber);

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

const result = user.find(u => {
    return u.name === 'basic phone';
});

console.log(result);