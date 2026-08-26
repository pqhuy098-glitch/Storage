let point = 4;

if (point < 5) {
    console.log('Failed');
}
else {
    console.log('Passed');

    if (point < 7) {
        console.log('Good');
    }
    else if (point >= 7 && point < 9) {
        console.log('Excellent');
    }
    else {
        console.log('Brilliant');
    }
}