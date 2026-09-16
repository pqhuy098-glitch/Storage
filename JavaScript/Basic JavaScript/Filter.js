const array = [1, 4, 5, 7];
const filtered = array.filter(arr => {
    return arr > 4;
});

console.log(filtered);

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

const result = user.filter(u => {
   return u.category === 'laptop'; 
});

console.log(result);
