document.addEventListener("DOMContentLoaded", () => {
    const nav = 
    `
        <nav class="nav">
            <div>
                <a href="">Home</a>
                <a href="">About</a>
                <a href="">Contact</a>
            </div>
        </nav>
    `;

    document.body.insertAdjacentHTML('afterbegin', nav);
})